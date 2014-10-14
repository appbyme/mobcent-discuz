$(function () {

    var ModuleModel = Backbone.Model.extend({
        defaults: uidiyGlobalObj.moduleInitParams,
        sync: function (method, model, options) {
            switch (method) {
                case 'delete':
                    break;
                default:
                    break;
            }
        },
        validate: function (attrs, options) {
            if (attrs.title == '') {
                return '请输入1-4个字母、数字或汉字作为名称';
            }
            return '';
        },
        isNew: function () {
            return !(this.id > 0);
        },
        getLastInsertId: function () {
            return modules.at(modules.length-1).id + 1;
        }
    });

    var ModuleList = Backbone.Collection.extend({
        model: ModuleModel,
    });

    var modules = new ModuleList();

    var ModuleView = Backbone.View.extend({
        template: _.template($('#module-template').html()),
        events: {
            'click .module-edit-btn': 'showModuleEdit',
        },
        initialize: function() {
            this.listenTo(this.model, 'change', this.render);
            this.listenTo(this.model, 'destroy', this.remove);
        },
        render: function () {
            this.$el.html(this.template(this.model.attributes));
            return this;
        },
        showModuleEdit: function (event) {
            var view = new ModuleEditDlg({model: this.model});
            $('#module-edit-dlg-view').html(view.render().el);
        },
    });

    var ModuleEditDlg = Backbone.View.extend({
        template: _.template($('#module-edit-template').html()),
        events: {
            'submit .module-edit-form': 'moduleSubmit',
        },
        render: function () {
            this.$el.html(this.template(this.model.attributes));
            return this;
        },
        moduleSubmit: function (event) {
            event.preventDefault();

            var form = $('.module-edit-form')[0];
            
            this.model.set({
                title: form.moduleTitle.value,
            });

            var error = this.model.validate(this.model.attributes);
            if (error != '') {
                alert(error);
                this.model.destroy();
                return;
            }

            if (this.model.isNew()) {
                this.model.set('id', this.model.getLastInsertId());
            }
            
            modules.set(this.model, {merge: true, remove: false, add: true});

            $('.module-edit-dlg').modal('hide');
        },
    });
    
    var ModuleRemoveDlg = Backbone.View.extend({
        template: _.template($('#module-remove-template').html()),
        events: {
            'submit .module-remove-form': 'moduleSubmit',
        },
        render: function () {
            this.$el.html(this.template(this.model.attributes));
            return this;
        },
        moduleSubmit: function (event) {
            event.preventDefault();

            this.model.destroy();

            $('.module-remove-dlg').modal('hide');
        },
    });

    var MainView = Backbone.View.extend({
        el: $("#uidiy-main-view"),
        events: {
            'click .module-add-btn': 'dlgAddModule',
            'click .module-remove-btn': 'dlgRemoveModule',
            'click .uidiy-sync-btn': 'uidiySync',
        },
        initialize: function() {
            this.listenTo(modules, 'add', this.addModule);

            modules.set(uidiyGlobalObj.moduleInitList);
        },
        render: function () {
            return this;
        },
        addModule: function (module) {
            var view = new ModuleView({model: module});
            $('.last-module').before(view.render().el);
        },
        dlgAddModule: function (event) {
            console.log(modules);
            var module = new ModuleModel();
            var view = new ModuleEditDlg({model: module});
            $('#module-edit-dlg-view').html(view.render().el);
            view = null;
        },
        dlgRemoveModule: function (event) {
            var moduleId = $(event.currentTarget).parents('div.module')[0].id.slice(10);
            var view = new ModuleRemoveDlg({model: modules.get(moduleId)});
            $('#module-edit-dlg-view').html(view.render().el);
            view = null;
        },
        uidiySync: function (event) {
            Backbone.ajax({
                url: uidiyGlobalObj.rootUrl + '/index.php?r=admin/uidiy/savemodules',
                type: 'post',
                dataType: 'json',
                data: {
                    modules: JSON.stringify(modules.toJSON()),
                },
                success: function (result,status,xhr) {
                    console.log(result);
                    alert('同步成功');
                }
            });
        },
    });

    var mainView = new MainView(); 
});