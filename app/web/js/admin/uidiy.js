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

    var coveringToggle = function (param) {
        if (param == 'close') {
            $('.covering').fadeOut();
        } else {
            $('.covering').fadeIn();
        }
    }

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
        },
        sync: function (method, model, options) {
        },
        validate: function (attrs, options) {
            // if (attrs.title == '') {
            //     return '请输入1-4个字母、数字或汉字作为名称';
            // }
            return '';
        },
    });

    var NavItemModel = Backbone.Model.extend({
        defaults: uidiyGlobalObj.navItemInitParams,
        validate: function (attrs, options) {
            if (attrs.title == '') {
                return '请输入1-4个字母、数字或汉字作为名称';
            }
            return '';
        },
    });

    var ModuleList = Backbone.Collection.extend({
        model: ModuleModel,
    });

    var NavItemList = Backbone.Collection.extend({
        model: NavItemModel,
    });

    var ComponentList = Backbone.Collection.extend({
        model: ComponentModel,
    });

    var modules = new ModuleList();
    var navItems = new NavItemList();

    var NavItemView = Backbone.View.extend({
        className: 'nav-item',
        template: _.template($('#navitem-template').html()),
        events: {
            'click .navitem-edit-btn': 'dlgEditNavItem',
            'click .navitem-remove-btn': 'dlgRemoveNavItem',
        },
        initialize: function() {
            this.listenTo(this.model, 'change', this.render);
            this.listenTo(this.model, 'destroy', this.remove);

            this.$el.hover(function() {
                $(this).find('.navitem-title').addClass('hidden');
                $(this).find('.nav-edit').removeClass('hidden');
            }, function () {
                $(this).find('.navitem-title').removeClass('hidden');
                $(this).find('.nav-edit').addClass('hidden');
            });
        },
        render: function () {
            this.$el.html(this.template(this.model.attributes));
            return this;
        },
        dlgEditNavItem: function (event) {
            navItemEditDlg.model = this.model;
            navItemEditDlg.render();
            navItemEditDlg.toggle();
            coveringToggle();
        },
        dlgRemoveNavItem: function (event) {
            navItemRemoveDlg.model = this.model;
            navItemRemoveDlg.render();
            navItemRemoveDlg.toggle();
            coveringToggle();
        },
    });

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
            'click .remove-component-btn': 'remove',
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
    
    var NewsComponentEditDlg = Backbone.View.extend({
        el: $("#news-component-edit-dlg-view"),
        template: _.template($('#news-component-edit-dlg-template').html()),
        events: {
            'click .news-component-close-btn': 'closeComponentDlg',
            'submit .news-component-edit-form': 'submitNewsComponent',
        },
        render: function () {
            this.$el.html(this.template(this.model.attributes));
            var view = new ComponentView({model: this.model});
            $('.component-view-container').html(view.render().el);
            return this;
        },
        toggle: function () {
            this.$el.fadeToggle();
        },
        closeComponentDlg: function () {
            this.$el.fadeOut();
            this.$el.html('');
            coveringToggle('close');
        },
        submitNewsComponent: function (event) {
            event.preventDefault();

            var form = $('.news-component-edit-form')[0],
                componentType = $(form['componentType[]'])[0],
                componentTitle = $(form['componentTitle[]'])[0],
                componentDesc = $(form['componentDesc[]'])[0],
                isShowForumIcon = $(form['isShowForumIcon[]'])[0],
                isShowForumTwoCols = $(form['isShowForumTwoCols[]'])[0],
                newsModuleId = $(form['newsModuleId[]'])[0],
                forumId = $(form['forumId[]'])[0],
                fastpostForumId = $(form['fastpostForumId[]'])[0],
                isShowTopicTitle = $(form['isShowTopicTitle[]'])[0],
                isShowTopicSort = $(form['isShowTopicSort[]'])[0],
                componentRedirect = $(form['componentRedirect[]'])[0],
                componentStyle = $(form['componentStyle[]'])[0];

            var extParams = {
                isShowForumIcon: isShowForumIcon.checked ? 1 : 0,
                isShowForumTwoCols: isShowForumTwoCols.checked ? 1 : 0,
                newsModuleId: parseInt(newsModuleId.value),
                forumId: parseInt(forumId.value),
                isShowTopicTitle: isShowTopicTitle.checked ? 1 : 0,
                isShowTopicSort: isShowTopicSort.checked ? 1 : 0,
                redirect: componentRedirect.value,
            };
            this.model.set({
                title: componentTitle.value,
                desc: componentDesc.value,
                type: componentType.value,
                style: componentStyle.value,
                extParams: extParams,
            });

            var error = this.model.validate(this.model.attributes);
            if (error != '') {
                alert(error);
                return;
            }

            this.moduleModel.attributes.componentList.add(this.model, {merge: true, remove: false, add: true});

            this.closeComponentDlg();
        },
    });

    var ModuleEditDlg = Backbone.View.extend({
        el: $("#module-edit-dlg-view"),
        template: _.template($('#module-edit-template').html()),
        events: {
            'change #moduleType': 'onChangeModuleType',
            'submit .module-edit-form': 'moduleSubmit',
            'click .close-module-play' : 'closeModule',
            'click .more-fastpost-btn': 'selectFastpostItem',
            'click .close-fastpost-item-btn': 'closeSelectFastpostItem',
            'click .add-fastpost-item-btn': 'addFastpostItem',
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
            moduleEditMobileView.model = moduleModel;
            moduleEditMobileView.render();

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
                case MODULE_TYPE_FASTPOST:
                    _.each(this.model.attributes.componentList, function (component) {
                        var view = new ComponentView({model: component});
                        $('.fastpost-components-container').append(view.render().el);
                    });
                    break;
                default:
                    break;
            }
        },
        moduleSubmit: function (event) {
            event.preventDefault();

            var form = $('.module-edit-form')[0],
                componentType = $(form['componentType[]']),
                componentTitle = $(form['componentTitle[]']),
                componentDesc = $(form['componentDesc[]']),
                isShowForumIcon = $(form['isShowForumIcon[]']),
                isShowForumTwoCols = $(form['isShowForumTwoCols[]']),
                newsModuleId = $(form['newsModuleId[]']),
                forumId = $(form['forumId[]']),
                fastpostForumId = $(form['fastpostForumId[]']),
                isShowTopicTitle = $(form['isShowTopicTitle[]']),
                isShowTopicSort = $(form['isShowTopicSort[]']),
                componentRedirect = $(form['componentRedirect[]']),
                componentStyle = $(form['componentStyle[]']);
            
            var moduleType = form.moduleType.value;
            this.model.set({
                title: form.moduleTitle.value,
                type: moduleType,
            });

            switch (moduleType) {
                case MODULE_TYPE_FULL:
                case MODULE_TYPE_SUBNAV:
                case MODULE_TYPE_FASTPOST:
                    if (this.model.id != MODULE_ID_DISCOVER) {
                        var componentList = [];
                        for (var i = 0; i < componentType.length; i++) {
                            var extParams = {
                                isShowForumIcon: isShowForumIcon[i].checked ? 1 : 0,
                                isShowForumTwoCols: isShowForumTwoCols[i].checked ? 1 : 0,
                                newsModuleId: parseInt(newsModuleId[i].value),
                                forumId: parseInt(moduleType == MODULE_TYPE_FASTPOST ? fastpostForumId[i].value : forumId[i].value),
                                isShowTopicTitle: isShowTopicTitle[i].checked ? 1 : 0,
                                isShowTopicSort: isShowTopicSort[i].checked ? 1 : 0,
                                redirect: componentRedirect[i].value,
                            };
                            var model = new ComponentModel({
                                title: componentTitle[i].value,
                                desc: componentDesc[i].value,
                                type: componentType[i].value,
                                style: componentStyle[i].value,
                                extParams: extParams,
                            });
                            componentList.push(model);
                        }
                        this.model.attributes.componentList = componentList;
                    }
                    break;
                case MODULE_TYPE_NEWS:
                    this.model.attributes.componentList = this.model.attributes.componentList.models;
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

            this.closeModule();
        },
        closeModule: function () {
            $('.module-play').fadeToggle();
            
            moduleEditMobileView.$el.html('');
        },
        selectFastpostItem: function () {
            event.preventDefault();

            $('.fastpost-item-select-div').removeClass('hidden');
            $('.more-fastpost-btn').addClass('hidden');
        },
        closeSelectFastpostItem: function () {
            $('.fastpost-item-select-div').addClass('hidden');
            $('.more-fastpost-btn').removeClass('hidden');
        },
        addFastpostItem: function () {
            var form = $('.module-edit-form')[0],
                type = $(form['fastpostItemSelect'])[0].value;
            
            var model = new ComponentModel({
                type: type,
            });

            var view = new ComponentView({model: model});
            $('.fastpost-components-container').append(view.render().el);
        }
    });
    
    var ModuleEditDetailView = Backbone.View.extend({
        template: _.template($('#module-edit-detail-template').html()),
        render: function () {
            $('#module-edit-detail-view').html(this.template(this.model.attributes));
            return this;
        },
    });

    var ModuleEditMobileView = Backbone.View.extend({
        el: $('#module-edit-mobile-view'),
        template: _.template($('#module-edit-mobile-template').html()),
        events: {
            'click .select-topbar-btn': 'selectTopbar',
            'click .add-news-component-item-btn': 'dlgAddNewsComponent',
        },
        initialize: function () { 
        },
        render: function () {
            this.$el.html(this.template(this.model.attributes));

            // module type 为news时, 转换 componentList对象为一个collection
            if (this.model.attributes.type == MODULE_TYPE_NEWS) {
                var componentList = this.model.attributes.componentList;
                this.model.attributes.componentList = new ComponentList();
                this.listenTo(this.model.attributes.componentList, 'add', this.addNewsComponentItem);

                this.model.attributes.componentList.set(componentList);
            }
            $('.carousel-example-generic_one').carousel();
            return this;
        },
        selectTopbar: function (event) {
            var index = $(event.currentTarget).index();
            var module = this.model.attributes;
            var componentModel = new ComponentModel();
            switch (index) {
                case 0:
                    if (module.leftTopbars.length > 0) {
                        componentModel = module.leftTopbars[0];
                    }
                    break;
                case 2:
                case 3:
                    if (module.rightTopbars.length > index - 2) {
                        componentModel = module.rightTopbars[index - 2];
                    }
                    break;
                default:
                    break;
            }
            moduleTopbarDlg.model = componentModel;
            moduleTopbarDlg.moduleModel = module;
            moduleTopbarDlg.render();
            moduleTopbarDlg.toggle();
            coveringToggle();
            $('#topbarIndex').val(index);
        },
        dlgAddNewsComponent: function () {
            newsComponentEditDlg.model = new ComponentModel();
            newsComponentEditDlg.moduleModel = this.model;
            newsComponentEditDlg.render();
            newsComponentEditDlg.toggle();
            coveringToggle();
        },
        addNewsComponentItem: function (component) {
            var view = new NewsComponentItemView({model: component});
            view.moduleModel = this.model;
            $('.news-component-item-container').append(view.render().el);
        },
    });
    
    var NewsComponentItemView = Backbone.View.extend({
        template: _.template($('#news-component-item-template').html()),
        events: {
            'click .edit-news-component-item-btn': 'dlgEditNewsComponent',
            'click .remove-news-component-item-btn': 'removeItem',
        },
        initialize: function () {
            this.listenTo(this.model, 'change', this.render);
            this.listenTo(this.model, 'destroy', this.remove);
        },
        render: function () {
            this.$el.html(this.template(this.model.attributes));
            return this;
        },
        dlgEditNewsComponent: function () {
            newsComponentEditDlg.model = this.model;
            newsComponentEditDlg.moduleModel = this.moduleModel;
            newsComponentEditDlg.render();
            newsComponentEditDlg.toggle();
        },
        removeItem: function () {
            this.model.destroy();
        },
    });

    var ModuleTopbarDlg = Backbone.View.extend({
        el: $('#module-topbar-dlg-view'),
        template: _.template($('#module-topbar-dlg-template').html()),
        events: {
            'submit .module-topbar-edit-form': 'submitTopbar',
            'click .close-topbar-btn': 'toggle',
        },
        render: function () {
            this.$el.html(this.template(this.model.attributes));
            return this;
        },
        submitTopbar: function () {
            event.preventDefault();

            var form = $('.module-topbar-edit-form')[0],
                type = form.topbarComponentType.value,
                index = parseInt(form.topbarIndex.value),
                model = new ComponentModel({type: type}),
                module = this.moduleModel;

            switch (index) {
                case 0:
                    if (type == COMPONENT_TYPE_DEFAULT) {
                        module.leftTopbars = [];
                    } else {
                        module.leftTopbars[0] = model;
                    }
                    break;
                case 2:
                    if (type == COMPONENT_TYPE_DEFAULT) {
                        module.rightTopbars.shift();
                    } else {
                        module.rightTopbars[0] = model;
                    }
                    break;
                case 3:
                    if (type == COMPONENT_TYPE_DEFAULT) {
                        module.rightTopbars.length > 1 && module.rightTopbars.pop();
                    } else {
                        module.rightTopbars[module.rightTopbars.length > 0 ? 1 : 0] = model;
                    }
                    break;
                default:
                    break;
            }

            this.toggle();
        },
        toggle: function () {
            coveringToggle('close');
            this.$el.fadeToggle();
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

    var NavItemEditDlg = Backbone.View.extend({
        el: $("#navitem-edit-dlg-view"),
        template: _.template($('#navitem-edit-template').html()),
        events: {
            'submit .navitem-edit-form': 'submitNavItem',
            'click .add-nav-close' : 'toggle',
        },
        render: function () {
            this.$el.html(this.template(this.model.attributes));
            return this;
        },
        submitNavItem: function (event) {
            event.preventDefault();

            var form = $('.navitem-edit-form')[0];
            
            this.model.set({
                title: form.navItemTitle.value,
                moduleId: parseInt(form.navItemModuleId.value),
                // icon: '',
            });

            var error = this.model.validate(this.model.attributes);
            if (error != '') {
                alert(error);
                this.model.destroy();
                return;
            }

            navItems.add(this.model, {merge: true, remove: false, add: true});

            this.toggle();
        },
        toggle: function () {
            coveringToggle('close');
            this.$el.fadeToggle();
        },
    });

    var NavItemRemoveDlg = Backbone.View.extend({
        el: $("#navitem-remove-dlg-view"),
        template: _.template($('#navitem-remove-template').html()),
        events: {
            'submit .navitem-remove-form': 'submitNavItem',
            'click .btn-remove-navitem' : 'toggle',
        },
        render: function () {
            this.$el.html(this.template(this.model.attributes));
            return this;
        },
        submitNavItem: function (event) {
            event.preventDefault();
            this.model.destroy();
            this.toggle();
        },
        toggle: function () {
            this.$el.fadeToggle();
            coveringToggle('close');
        },
    });

    var MainView = Backbone.View.extend({
        el: $("#uidiy-main-view"),
        events: {
            'click .module-add-btn': 'dlgAddModule',
            'click .module-remove-btn': 'dlgRemoveModule',
            'click .navitem-add-btn': 'dlgAddNavItem',
            'click .uidiy-sync-btn': 'uidiySync',
            'click .uidiy-init-btn': 'uidiyInit',
        },
        initialize: function() {
            this.listenTo(modules, 'add', this.addModule);
            this.listenTo(navItems, 'add', this.addNavItem);

            // 转换module, component对象
            _.each(uidiyGlobalObj.moduleInitList, function (module) {
                var tmpComponentList = [],
                    tmpLeftTopbars = [],
                    tmpRightTopbars = [];
                _.each(module.componentList, function (component) {
                    tmpComponentList.push(wrapComponent(component));
                });
                _.each(module.leftTopbars, function (component) {
                    tmpLeftTopbars.push(wrapComponent(component));
                });
                _.each(module.rightTopbars, function (component) {
                    tmpRightTopbars.push(wrapComponent(component));
                });
                module.componentList = tmpComponentList;
                module.leftTopbars = tmpLeftTopbars;
                module.rightTopbars = tmpRightTopbars;
                modules.add(new ModuleModel(module));
            })

            navItems.set(uidiyGlobalObj.navItemInitList);
        },
        render: function () {
            return this;
        },
        addModule: function (module) {
            var view = new ModuleView({model: module});
            $('.last-module').before(view.render().el);
        },
        addNavItem: function (navItem) {
            var view = new NavItemView({model: navItem});
            $('.navitem-add-btn').before(view.render().el);   
        },
        dlgAddNavItem: function () {
            navItemEditDlg.model = new NavItemModel();
            navItemEditDlg.render();
            navItemEditDlg.toggle();
            coveringToggle();
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
                    var navInfo = {
                        type: NAV_TYPE_BOTTOM,
                        navItemList: navItems,
                    };
                    Backbone.ajax({
                        url: uidiyGlobalObj.rootUrl + '/index.php?r=admin/uidiy/savenavinfo',
                        type: 'post',
                        dataType: 'json',
                        data: {
                            navInfo: JSON.stringify(navInfo),
                        },
                        success: function (result,status,xhr) {
                            alert('同步成功');
                        }
                    });
                }
            });
        },
        uidiyInit: function () {
            Backbone.ajax({
                url: uidiyGlobalObj.rootUrl + '/index.php?r=admin/uidiy/init',
                type: 'post',
                success: function (result,status,xhr) {
                    alert('初始化成功');
                    location.href = uidiyGlobalObj.rootUrl + '/index.php?r=admin/uidiy';
                }
            });
        }
    });

    var mainView = new MainView(),
        navItemEditDlg = new NavItemEditDlg(),
        navItemRemoveDlg = new NavItemRemoveDlg(),
        moduleEditDlg = new ModuleEditDlg(),
        moduleTopbarDlg = new ModuleTopbarDlg(),
        newsComponentEditDlg = new NewsComponentEditDlg(),
        moduleEditDetailView = new ModuleEditDetailView(),
        moduleEditMobileView = new ModuleEditMobileView(),
        moduleRemoveDlg = new ModuleRemoveDlg();

    window.Appbyme = {
        uiModules: modules,
    }
});