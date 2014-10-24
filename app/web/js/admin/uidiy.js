/**
 * uidiy.js 
 * UI Diy 模块
 *
 * @author 谢建平 <jianping_xie@aliyun.com>
 * @copyright 2012-2014 Appbyme
 */

$(function () {

    var wrapComponent = function f(component) {
        var tmpComponentList = [];
        _.each(component.componentList, function (value) {
            tmpComponentList.push(f(value));
        });
        component.componentList = tmpComponentList;
        return new ComponentModel(component);
    };

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

    var ComponentModel = Backbone.Model.extend({
        defaults: uidiyGlobalObj.componentInitParams,
        initialize: function () {
            this.set({id: this.cid});
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
            moduleEditDlg.model = this.model;
            moduleEditDlg.render();
        },
    });

    var ComponentView = Backbone.View.extend({
        template: _.template($('#component-template').html()),
        events: {
            'change .selectComponentType': 'onChangeComponentType',
        },
        initialize: function() {
        },
        render: function () {
            this.$el.html(this.template(this.model.attributes));
            return this;
        },
        onChangeComponentType: function (event) {
            var id = this.model.id;
            var type = $(event.currentTarget).val();
            $('#component-view-'+type+'-'+id).removeClass('hidden').siblings('.component-view-item').addClass('hidden');
        },
    });

    var ModuleEditDlg = Backbone.View.extend({
        el: $("#module-edit-dlg-view"),
        template: _.template($('#module-edit-template').html()),
        events: {
            'change #moduleType': 'onChangeModuleType',
            'submit .module-edit-form': 'moduleSubmit',
            'click .close-module-play' : 'closeModule',
        },
        render: function () {
            this.$el.html(this.template(this.model.attributes));
            this.onChangeModuleType();
            return this;
        },
        onChangeModuleType: function (event) {
            var moduleType = $('#moduleType').val();
            var moduleModel = this.model.attributes.type == moduleType ? this.model : new ModuleModel({type: moduleType});
            moduleEditDetailView.model = moduleModel;
            moduleEditDetailView.render();
            
            switch (moduleType) {
                case MODULE_TYPE_FULL:
                case MODULE_TYPE_SUBNAV:
                    var componentList = [];
                    size = moduleType == MODULE_TYPE_FULL ? 1 : SUBNAV_MAX_COMPONENT_LEN;
                    if (moduleModel.attributes.componentList.length == size) {
                        componentList = this.model.attributes.componentList;
                    } else {
                        for (var i = 0; i < size; i++) {
                            componentList.push(new ComponentModel());
                        }
                    }
                    for (var i = 0; i < componentList.length; i++) {
                        var model = componentList[i];
                        var view = new ComponentView({model: model});
                        $('.component-view-container').eq(i).html(view.render().el);
                    }
                    break;
                default:
                    break;
            }
        },
        moduleSubmit: function (event) {
            event.preventDefault();

            var form = $('.module-edit-form')[0],
                componentTitle = $(form['componentTitle[]']),
                componentType = $(form['componentType[]']),
                isShowForumIcon = $(form['isShowForumIcon[]']),
                isShowForumTwoCols = $(form['isShowForumTwoCols[]']),
                newsModuleId = $(form['newsModuleId[]']),
                componentRedirect = $(form['componentRedirect[]']),
                componentStyle = $(form['componentStyle[]']);
            
            this.model.set({
                title: form.moduleTitle.value,
                type: form.moduleType.value,
            });

            switch (this.model.get('type')) {
                case MODULE_TYPE_FULL:
                case MODULE_TYPE_SUBNAV:
                    if (this.model.id != MODULE_ID_DISCOVER) {
                        var componentList = [];
                        for (var i = 0; i < componentTitle.length; i++) {
                            var extParams = {
                                isShowForumIcon: isShowForumIcon[i].checked ? 1 : 0,
                                isShowForumTwoCols: isShowForumTwoCols[i].checked ? 1 : 0,
                                newsModuleId: parseInt(newsModuleId[i].value),
                                componentRedirect: componentRedirect[i].value,
                            };
                            var model = new ComponentModel({
                                title: componentTitle[i].value,
                                type: componentType[i].value,
                                style: componentStyle[i].value,
                                extParams: extParams,
                            });
                            componentList.push(model);
                        }
                        this.model.attributes.componentList = componentList;
                    }
                    break;
                default:
                    break;
            }

            var error = this.model.validate(this.model.attributes);
            if (error != '') {
                alert(error);
                this.model.destroy();
                return;
            }

            if (this.model.isNew()) {
                this.model.set('id', this.model.getLastInsertId());
            }
            
            modules.add(this.model, {merge: true, remove: false, add: true});

            $('.module-edit-dlg').modal('hide');
            this.$el.html('');
        },
        closeModule: function () {
            $('.module-play').fadeToggle();
        },
    });
    
    var ModuleEditDetailView = Backbone.View.extend({
        template: _.template($('#module-edit-detail-template').html()),
        render: function () {
            $('#module-edit-detail').html(this.template(this.model.attributes));
            return this;
        },
    });

    var ModuleRemoveDlg = Backbone.View.extend({
        el: $('#module-remove-dlg-view'),
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

            // 转换module, component对象
            _.each(uidiyGlobalObj.moduleInitList, function (module) {
                var tmpComponentList = [];
                _.each(module.componentList, function (component) {
                    tmpComponentList.push(wrapComponent(component));
                });
                module.componentList = tmpComponentList;
                modules.add(new ModuleModel(module));
            })
        },
        render: function () {
            return this;
        },
        addModule: function (module) {
            var view = new ModuleView({model: module});
            $('.last-module').before(view.render().el);
        },
        dlgAddModule: function (event) {
            moduleEditDlg.model = new ModuleModel();
            moduleEditDlg.render();
        },
        dlgRemoveModule: function (event) {
            var moduleId = $(event.currentTarget).parents('div.module')[0].id.slice(10);
            moduleRemoveDlg.model = modules.get(moduleId);
            moduleRemoveDlg.render();
        },
        uidiySync: function (event) {
            Backbone.ajax({
                url: uidiyGlobalObj.rootUrl + '/index.php?r=admin/uidiy/savemodules',
                type: 'post',
                dataType: 'json',
                data: {
                    modules: JSON.stringify(modules),
                },
                success: function (result,status,xhr) {
                    Backbone.ajax({
                        url: uidiyGlobalObj.rootUrl + '/index.php?r=admin/uidiy/savenavinfo',
                        type: 'post',
                        dataType: 'json',
                        data: {
                            navinfo: JSON.stringify(modules),
                        },
                        success: function (result,status,xhr) {
                            // alert('同步成功');
                        }
                    });
                    alert('同步成功');
                }
            });
        },
    });

    var mainView = new MainView();
    var moduleEditDlg = new ModuleEditDlg();
    var moduleEditDetailView = new ModuleEditDetailView();
    var moduleRemoveDlg = new ModuleRemoveDlg();
});