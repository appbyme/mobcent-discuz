$(function () {

    var ModuleModel = Backbone.Model.extend({
        default: uidiyGlobalObj.moduleInitParams,
    });

    var ModuleList = Backbone.Collection.extend({
        model: ModuleModel,

    });

    var modules = new ModuleList();

    var ModuleView = Backbone.View.extend({
        template: _.template($('#module-template').html()),
        render: function () {
            this.$el.html(this.template(this.model.attributes));
            return this;
        },
    });

    var ModuleEditView = Backbone.View.extend({
        template: _.template($('#module-edit-template').html()),
        render: function () {
            this.$el.html(this.template());
            return this;
        },
    });

    var MainView = Backbone.View.extend({
        el: $("#uidiy-main-view"),
        events: {
            'click ': 'showModuleEdit'
        },
        initialize: function() {
            this.listenTo(modules, 'add', this.addModule);

            modules.set(uidiyGlobalObj.moduleInitList);
        },
        render: function () {

        },
        addModule: function (module) {
            var view = new ModuleView({model: module});
            $('.last-module').before(view.render().el);
        },
        showModuleEdit: function () {
            var view = new ModuleEditView();
            $('#module-edit-view').html(view.render().el);
        }
    });

    var mainView = new MainView(); 
});