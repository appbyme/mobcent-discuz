/**
 * uidiy.js 
 * UI Diy 模块
 *
 * @author 谢建平 <jianping_xie@aliyun.com>
 * @copyright 2012-2014 Appbyme
 */

$(function () {

    var LocalStorageWrapper = function () {
        this.available = typeof(Storage) !== 'undefined';
        this.getItem = function (key) {
            return this.available ? localStorage.getItem(key) : undefined;
        };
        this.setItem = function (key, value) {
            this.available && localStorage.setItem(key, value);
        };
    };

    var sortArray = function (arr, fromIndex, toIndex) {
        if (fromIndex == toIndex) {
            return arr;
        }
        var tmp = arr[fromIndex];
        if (toIndex > fromIndex) {
            for (var i = fromIndex; i < toIndex; i++) {
                arr[i] = arr[i+1];
            }
        } else {
            for (var i = fromIndex; i > toIndex; i--) {
                arr[i] = arr[i-1];
            }
        }
        arr[toIndex] = tmp;
        return arr;
    };

    var sortableHelper = function ($el, list, update) {
        var sortStartIndex = 0;
        $el.sortable({
            revert: true,
            opacity: 0.6,
            start: function (event, ui) {
                sortStartIndex = ui.item.index();
            },
            update: function (event, ui) {
                sortArray(list, sortStartIndex, ui.item.index());
                typeof update == 'function' && update(list, sortStartIndex, ui.item.index());
            },
        });
        $el.disableSelection();
    };

    var scrollToBottomHelper = function (element) {
        element.scrollTop = element.scrollHeight;
    };

    var localStorageWrapper = new LocalStorageWrapper();

    var APPBYME_UIDIY_AUTOSAVE = 'appbyme_uidiy_autosave';

    var wrapComponent = function f(component) {
        var tmpComponentList = [];
        _.each(component.componentList, function (value) {
            tmpComponentList.push(f(value));
        });
        component.componentList = tmpComponentList;
        return new ComponentModel(component);
    };

    var getNavIconUrl = function (icon) {
        return uidiyGlobalObj.navItemIconUrlBasePath+'/'+icon+'_n.png'
    };

    var getComponentIconUrl = function (icon) {
        if (icon.indexOf(COMPONENT_ICON_DISCOVER_DEFAULT) != -1) {
            return uidiyGlobalObj.componentDiscoverIconBaseUrlPath+'/'+icon+'.png';
        } else if (icon.indexOf(COMPONENT_ICON_FASTPOST) != -1) {
            return uidiyGlobalObj.componentFastpostIconBaseUrlPath+'/'+icon+'_n.png';
        } else if (icon.indexOf(COMPONENT_ICON_TOPBAR) != -1) {
            return uidiyGlobalObj.componentTopbarIconBaseUrlPath+'/'+icon+'_n.png';
        } else {
            return icon;    
        }
    };
    
    var getAjaxApiUrl = function (route) {
        return uidiyGlobalObj.rootUrl + '/index.php?r=' + route + '&apphash=' + uidiyGlobalObj.apphash;
    } 

    var toggleUICover = function () {
        $('.covering').fadeToggle();
    };

    var submitComponentHelper = function (form) {
        var componentType = $(form['componentType[]']),
            componentTitle = $(form['componentTitle[]']),
            componentDesc = $(form['componentDesc[]']),
            componentIcon = $(form['componentIcon[]']),
            componentIconStyle = $(form['componentIconStyle[]']),
            // isShowForumIcon = $(form['isShowForumIcon[]']),
            // isShowForumTwoCols = $(form['isShowForumTwoCols[]']),
            // pageTitle = $(form['pageTitle[]']),
            newsModuleId = $(form['newsModuleId[]']),
            moduleId = $(form['moduleId[]']),
            topicId = $(form['topicId[]']),
            articleId = $(form['articleId[]']),
            topicForumId = $(form['topicForumId[]']),
            topicSimpleForumId = $(form['topicSimpleForumId[]']),
            fastpostForumIds = $(form['fastpostForumIds[]']),
            isShowTopicTitle = $(form['isShowTopicTitle[]']),
            // isShowTopicSort = $(form['isShowTopicSort[]']),
            isShowMessagelist = $(form['isShowMessagelist[]']),
            topicSimpleOrderby = $(form['topicSimpleOrderby[]']),
            topicSimpleTopOrder = $(form['topicSimpleTopOrder[]']),
            topicSimpleTypeId = $(form['topicSimpleTypeId[]']),
            userlistFilter = $(form['userlistFilter[]']),
            userlistOrderby = $(form['userlistOrderby[]']),
            listTitleLength = $(form['listTitleLength[]']),
            listSummaryLength = $(form['listSummaryLength[]']),
            listImagePosition = $(form['listImagePosition[]']),
            listDetailStyle = $(form['listDetailStyle[]']),
            forumTopiclistStyle = $(form['forumTopiclistStyle[]']),
            forumPostlistStyle = $(form['forumPostlistStyle[]']),
            componentRedirect = $(form['componentRedirect[]']),
            componentStyle = $(form['componentStyle[]']);
        
        var componentList = [];
        for (var i = 0; i < componentType.length; i++) {
            var tempForumIds = [];
            var options = fastpostForumIds[i].selectedOptions;
            for (var j = 0; j < options.length; j++) {
                tempForumIds.push(parseInt(options[j].value));
            }
            var type = componentType[i].value,
                filter = '',
                orderby = '',
                forumId = parseInt(type == COMPONENT_TYPE_TOPICLIST ? topicForumId[i].value : topicSimpleForumId[i].value),
                subListStyle = '',
                subDetailViewStyle = '',
                dataId = 0;

            switch (type) {
                case COMPONENT_TYPE_FORUMLIST:
                    subListStyle = forumTopiclistStyle[i].value;
                    subDetailViewStyle = forumPostlistStyle[i].value;
                    break;
                case COMPONENT_TYPE_NEWSLIST:
                    subDetailViewStyle = listDetailStyle[i].value;
                    break;
                case COMPONENT_TYPE_TOPICLIST: 
                    dataId = parseInt(topicForumId[i].value);
                    subDetailViewStyle = listDetailStyle[i].value;
                    break;
                case COMPONENT_TYPE_TOPICLIST_SIMPLE:
                    dataId = parseInt(topicSimpleForumId[i].value);
                    orderby = topicSimpleOrderby[i].value; 
                    filter = 'typeid';
                    subDetailViewStyle = listDetailStyle[i].value;
                    break;
                case COMPONENT_TYPE_USERLIST: 
                    orderby = userlistOrderby[i].value;
                    filter = userlistFilter[i].value;
                    break;
                default: 
                    break;
            }

            var extParams = {
                dataId: dataId,
                titlePosition: COMPONENT_TITLE_POSITION_LEFT,
                // isShowForumIcon: isShowForumIcon[i].checked ? 1 : 0,
                // isShowForumTwoCols: isShowForumTwoCols[i].checked ? 1 : 0,
                // pageTitle: pageTitle[i].value,
                newsModuleId: parseInt(newsModuleId[i].value),
                forumId: forumId,
                moduleId: parseInt(moduleId[i].value) || 0,
                topicId: parseInt(topicId[i].value) || 0,
                articleId: parseInt(articleId[i].value) || 0,
                fastpostForumIds: tempForumIds,
                isShowTopicTitle: isShowTopicTitle[i].checked ? 1 : 0,
                // isShowTopicSort: isShowTopicSort[i].checked ? 1 : 0,
                isShowMessagelist: isShowMessagelist[i].checked ? 1 : 0,
                filterId: parseInt(topicSimpleTypeId[i].value) || 0,
                filter: filter,
                orderby: orderby,
                order: parseInt(topicSimpleTopOrder[i].value), 
                redirect: componentRedirect[i].value,
                listTitleLength: parseInt(listTitleLength[i].value) || 10, 
                listSummaryLength: parseInt(listSummaryLength[i].value) || 40, 
                listImagePosition: parseInt(listImagePosition[i].value) || IMAGE_POSITION_LEFT,
                subListStyle: subListStyle,
                subDetailViewStyle: subDetailViewStyle,
            };

            var model = new ComponentModel({
                type: componentType[i].value,
                style: componentStyle[i].value,
                icon: componentIcon[i].value,
                iconStyle: componentIconStyle[i].value,
                title: componentTitle[i].value,
                desc: componentDesc[i].value,

                extParams: extParams,
            });
            componentList.push(model);
        }
        return componentList;
    };

    var ModuleModel = Backbone.Model.extend({
        defaults: uidiyGlobalObj.moduleInitParams,
        initialize: function () {
            var tmpComponentList = [],
                tmpLeftTopbars = [],
                tmpRightTopbars = [];
            _.each(this.attributes.componentList, function (component) {
                tmpComponentList.push(wrapComponent(component));
            });
            _.each(this.attributes.leftTopbars, function (component) {
                tmpLeftTopbars.push(wrapComponent(component));
            });
            _.each(this.attributes.rightTopbars, function (component) {
                tmpRightTopbars.push(wrapComponent(component));
            });
            this.attributes.componentList = tmpComponentList;
            this.attributes.leftTopbars = tmpLeftTopbars;
            this.attributes.rightTopbars = tmpRightTopbars;
        },
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
            console.log(method);
        },
        validate: function (attrs, options) {
            // if (attrs.title == '') {
            //     return '请输入1-4个字母、数字或汉字作为名称';
            // }
            return '';
        },
        getDiscoverComponentList: function (style, isInner) {
            var tempMap = {};
            tempMap[COMPONENT_STYLE_DISCOVER_SLIDER] = 0;
            tempMap[COMPONENT_STYLE_DISCOVER_DEFAULT] = 1;
            tempMap[COMPONENT_STYLE_DISCOVER_CUSTOM] = 2;
            var tempComponentList = this.attributes.componentList[tempMap[style]];
            return isInner ? tempComponentList.attributes.componentList : tempComponentList;
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

    var ComponentList = Backbone.Collection.extend({
        model: ComponentModel,
    });

    var NavItemList = Backbone.Collection.extend({
        model: NavItemModel,
    });

    var modules = new ModuleList();
    var navItems = new NavItemList();

    var NavItemView = Backbone.View.extend({
        className: 'nav-item',
        template: _.template($('#navitem-template').html()),
        events: {
            'click .navitem-edit-btn': 'dlgEditNavItem',
            'click .navitem-remove-btn': 'dlgRemoveNavItem',
            'click .nav-column': 'renderMobileUI',
        },
        initialize: function() {
            this.listenTo(this.model, 'change', this.render);
            this.listenTo(this.model, 'destroy', this.remove);

            this.$el.hover(function() {
                var imgUrl = $(this).children('.nav-column').css('background-image'); 
                var newImgUrl = imgUrl.replace(/_n.png/, '_h.png');
                $(this).children('.nav-column').css('background-image', newImgUrl);

                $(this).find('.navitem-title').addClass('hidden');
                $(this).find('.nav-edit').removeClass('hidden');
            }, function () {
                var imgUrl = $(this).children('.nav-column').css('background-image'); 
                var newImgUrl = imgUrl.replace(/_h.png/, '_n.png');
                $(this).children('.nav-column').css('background-image', newImgUrl);
                
                $(this).find('.navitem-title').removeClass('hidden');
                $(this).find('.nav-edit').addClass('hidden');
            });
        },
        render: function () {
            this.$el.html(this.template(this.model.attributes));
            return this;
        },
        dlgEditNavItem: function (event) {
            event.stopPropagation();
            navItemEditDlg.model = this.model;
            navItemEditDlg.render();
            navItemEditDlg.toggle();
        },
        dlgRemoveNavItem: function (event) {
            event.stopPropagation();
            navItemRemoveDlg.model = this.model;
            navItemRemoveDlg.render();
            navItemRemoveDlg.toggle();
        },
        renderMobileUI: function () {
            mainView.renderMobileUI(this.model.attributes.moduleId);
        }
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
            moduleEditDlg.toggle();
        },
    });

    var ComponentView = Backbone.View.extend({
        className: 'component-view',
        template: _.template($('#component-template').html()),
        events: {
            'change .selectComponentType': 'onChangeComponentType',
            'click .remove-component-btn': 'remove',
            'click .upload-component-icon-btn': 'uploadIcon',
            'change .componentIconFile': 'onChangeComponentIcon',
            'change .componentIconStyle': 'onChangeComponentIconStyle',
            'change .topicSimpleForumSelect': 'onChangeTopicSimpleForum',
        },
        initialize: function(options) {
            this.initUIConfig();

            var uiconfig = options.uiconfig || {};
            for (var config in uiconfig) {
                this.uiconfig[config] = uiconfig[config];
            }
        },
        initUIConfig: function () {
            this.uiconfig = {
                isShow_title: 1,
                isShow_delete: 0,
                isShow_icon: 0,
                isShow_iconStyle: 0,
                isShow_desc: 0,
                isShow_style: 1,
                isShow_typeSelect: 1,
                isShow_typeEmpty: 0,
                isShow_typeForumlist: 1,
                isShow_typeNewslist: 1,
                isShow_typeTopiclist: 1,
                isShow_typeTopiclistSimple: 1,
                isShow_typeWebapp: 1,
                isShow_typeSetting: 1,
                isShow_typeAbout: 1,
                isShow_typeModuleRef: 1,
                isShow_typePostlist: 1,
                isShow_typeNewsview: 1,
                isShow_typeMessagelist: 1,
                isShow_typeUserinfo: 1,
                isShow_typeUserlist: 1,
                isShow_typeFasttext: 0,
                isShow_typeFastimage: 0,
                isShow_typeFastcamera: 0,
                isShow_typeFastaudio: 0,
                isShow_typeSign: 0,
                isShow_typeWeather: 0,
                isShow_typeSearch: 0,
                iconRatio: '适当',
                iconRatioCircle: '80*80',
            };
        },
        render: function () {
            this.$el.html(this.template(this.model.attributes));
            this.changeStyleUIByType(this.model.attributes.type);
            this.toggleExtConfigDiv(this.model.attributes.type);
            this.changeTopicSimpleForum();
            return this;
        },
        // 选择可选的页面样式
        changeStyleUIByType: function (type, isInitSelect) {
            var $styleSelectDiv = this.$el.find('.component-style-select-div');
            switch (type) {
                case COMPONENT_TYPE_FORUMLIST:
                case COMPONENT_TYPE_NEWSLIST:
                case COMPONENT_TYPE_TOPICLIST:
                case COMPONENT_TYPE_TOPICLIST_SIMPLE:
                case COMPONENT_TYPE_POSTLIST:
                // case COMPONENT_TYPE_USERINFO:
                    $styleSelectDiv.find('[value='+COMPONENT_STYLE_FLAT+']').show();
                    $styleSelectDiv.find('[value='+COMPONENT_STYLE_CARD+']').show();
                    $styleSelectDiv.find('[value='+COMPONENT_STYLE_1+']').hide();
                    $styleSelectDiv.find('[value='+COMPONENT_STYLE_2+']').hide();
                    $styleSelectDiv.find('[value='+COMPONENT_STYLE_TIEBA+']').hide();
                    $styleSelectDiv.find('[value='+COMPONENT_STYLE_HEADLINES+']').hide();
                    $styleSelectDiv.find('[value='+COMPONENT_STYLE_NETEASE_NEWS+']').hide();
                    
                    $styleSelectDiv.find('[value='+COMPONENT_STYLE_IMAGE+']').hide();
                    $styleSelectDiv.find('[value='+COMPONENT_STYLE_IMAGE_2+']').hide();
                    $styleSelectDiv.find('[value='+COMPONENT_STYLE_IMAGE_BIG+']').hide();
                    $styleSelectDiv.find('[value='+COMPONENT_STYLE_IMAGE_SUDOKU+']').hide();

                    if (type == COMPONENT_TYPE_NEWSLIST) {
                        $styleSelectDiv.find('[value='+COMPONENT_STYLE_IMAGE+']').show();
                        $styleSelectDiv.find('[value='+COMPONENT_STYLE_IMAGE_2+']').show();
                        $styleSelectDiv.find('[value='+COMPONENT_STYLE_IMAGE_BIG+']').show();
                        $styleSelectDiv.find('[value='+COMPONENT_STYLE_IMAGE_SUDOKU+']').show();
                        $styleSelectDiv.find('[value='+COMPONENT_STYLE_TIEBA+']').show();
                        // $styleSelectDiv.find('[value='+COMPONENT_STYLE_HEADLINES+']').show();
                        $styleSelectDiv.find('[value='+COMPONENT_STYLE_NETEASE_NEWS+']').show();
                    }
                    if (type == COMPONENT_TYPE_TOPICLIST || type == COMPONENT_TYPE_TOPICLIST_SIMPLE) {
                        $styleSelectDiv.find('[value='+COMPONENT_STYLE_IMAGE_SUDOKU+']').show();
                        $styleSelectDiv.find('[value='+COMPONENT_STYLE_TIEBA+']').show();
                        // $styleSelectDiv.find('[value='+COMPONENT_STYLE_HEADLINES+']').show();
                        $styleSelectDiv.find('[value='+COMPONENT_STYLE_NETEASE_NEWS+']').show();
                    }

                    if (type == COMPONENT_TYPE_USERINFO) {
                        $styleSelectDiv.find('[value='+COMPONENT_STYLE_FLAT+']').hide();
                        $styleSelectDiv.find('[value='+COMPONENT_STYLE_CARD+']').hide();
                        $styleSelectDiv.find('[value='+COMPONENT_STYLE_1+']').show();
                        $styleSelectDiv.find('[value='+COMPONENT_STYLE_2+']').show();
                    }

                    if (isInitSelect) {
                        $styleSelectDiv.find(':selected').each(function () {
                            this.selected = false;
                        });
                        if (type == COMPONENT_TYPE_USERINFO) {
                            $styleSelectDiv.find('[value='+COMPONENT_STYLE_1+']')[0].selected = true;
                        } else {
                            $styleSelectDiv.find('option')[0].selected = true;
                        }
                    }
                    
                    $styleSelectDiv.show();
                    break;
                default:
                    $styleSelectDiv.hide();
                    break;
            };
        },
        onChangeComponentType: function (event) {
            var id = this.model.id;
            var type = $(event.currentTarget).val();
            
            this.changeStyleUIByType(type, true);
            this.toggleExtConfigDiv(type);

            if (type == COMPONENT_TYPE_FASTTEXT || type == COMPONENT_TYPE_FASTIMAGE || type == COMPONENT_TYPE_FASTCAMERA || type == COMPONENT_TYPE_FASTAUDIO) {
                type = 'fastpost';
            }

            $('#component-view-'+type+'-'+id).removeClass('hidden').siblings('.component-view-item').addClass('hidden');
        },
        onChangeComponentIcon: function (event) {
            if (URL) {
                var objectURL = URL.createObjectURL(event.currentTarget.files[0]);
                this.$el.find('.component-icon-preview').attr('src', objectURL);
                this.$el.find('.component-icon-preview').css('opacity', 0.3);
                this.$el.find('.upload-component-icon-btn').addClass('btn-default').removeClass('btn-primary').html('点击上传图片');
            }
        },
        onChangeComponentIconStyle: function (event) {
            var ratio = event.currentTarget.value == COMPONENT_ICON_STYLE_CIRCLE ? this.uiconfig.iconRatioCircle : this.uiconfig.iconRatio;
            this.$el.find('.componentIconRatio').text(ratio);
        },
        onChangeTopicSimpleForum: function (event) {
            this.changeTopicSimpleForum(true);
        },
        changeTopicSimpleForum: function (isInitSelect) {
            this.$el.find('.divTopicType').hide();
            this.$el.find('.divTopicTypeFid-'+this.$el.find('.topicSimpleForumSelect').val()).show();
            if (isInitSelect) {
                this.$el.find('.topicSimpleTopicTypeSelect option')[0].selected = true;
            }
        },
        toggleExtConfigDiv: function (type) {
            this.$el.find('.list-ext-config-div').hide();
            if (type == COMPONENT_TYPE_NEWSLIST || type == COMPONENT_TYPE_TOPICLIST || type == COMPONENT_TYPE_TOPICLIST_SIMPLE) {
                this.$el.find('.list-ext-config-div').show();
            }
        },
        uploadIcon: function () {
            var data = new FormData();
            data.append('file', this.$el.find(':file')[0].files[0]);
            var _this = this.$el;
            $.ajax({
                timeout: 30000,
                type: 'POST',
                url: getAjaxApiUrl('admin/uidiy/uploadicon'),
                data: data,
                dataType: 'json',
                cache: false,
                processData: false,
                contentType: false,
                success: function(msg) {
                    if (!msg.errCode) {
                        alert(msg.errMsg);
                        return false;
                    }
                    // 获取原来的图片，并且删除
                    var fileName = _this.find('.componentIcon').val();
                    if (fileName != '') {
                        $.ajax({
                            type: 'GET',
                            url: getAjaxApiUrl('admin/uidiy/delicon'),
                            data: {'fileName': fileName},
                            success: function(msg) {
                               // console.log(msg);
                            }
                        });
                    }
                    
                    _this.find('.componentIcon').val(msg.errMsg);
                    _this.find('.component-icon-preview').attr('src', msg.errMsg);
                    _this.find('.component-icon-preview').animate({opacity:'10'}, 3000);
                    _this.find('.upload-component-icon-btn').html('上传成功！');
                    _this.find('.upload-component-icon-btn').addClass('btn-primary').removeClass('btn-default');
                    
                    // alert('上传成功！');
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    // console.log(textStatus);
                }
            });
        },
    });
    
    var createComponentViewFull = function(model) {
        return new ComponentView({model: model, uiconfig: {
            isShow_title: 0,
            isShow_typeModuleRef: 0,
            isShow_typePostlist: 0,
            isShow_typeNewsview: 0,
        }});
    };

    var createComponentViewSubnav = function(model) {
        return new ComponentView({model: model, uiconfig: {
            isShow_typePostlist: 0,
            isShow_typeNewsview: 0,
            isShow_typeMessagelist: 0,
            isShow_typeUserinfo: 0,
        }});
    };

    var createComponentViewFastpost = function(model) {
        return new ComponentView({model: model, uiconfig: {
            isShow_typeSelect: 0,
            isShow_delete: 1,
            isShow_icon: 1,
        }});
    };

    var createComponentViewDiscoverSlider = function(model) {
        return new ComponentView({model: model, uiconfig: {
            isShow_icon: 1,
            isShow_desc: 1,
            isShow_delete: 1,
            iconRatio: '2:1',
        }});
    };

    var createComponentViewSlider = function(model) {
        return new ComponentView({model: model, uiconfig: {
            isShow_icon: 1,
            isShow_desc: 1,
            isShow_delete: 1,
            iconRatio: '2:1',
        }});
    };

    var createComponentViewTopbar = function(model) {
        return new ComponentView({model: model, uiconfig: {
            isShow_title: 0,
            isShow_typeEmpty: 1,
            isShow_typeForumlist: 0,
            isShow_typeNewslist: 0,
            isShow_typeTopiclist: 0,
            isShow_typeTopiclistSimple: 0,
            isShow_typeWebapp: 0,
            isShow_typeSetting: 0,
            isShow_typeAbout: 0,
            isShow_typeModuleRef: 0,
            isShow_typePostlist: 0,
            isShow_typeNewsview: 0,
            isShow_typeMessagelist: 0,
            isShow_typeUserinfo: 1,
            isShow_typeUserlist: 0,
            isShow_typeSign: 1,
            isShow_typeWeather: 1,
            isShow_typeSearch: 1,
            isShow_typeFasttext: 1,
            isShow_typeFastimage: 1,
            isShow_typeFastcamera: 1,
            isShow_typeFastaudio: 1,
        }});
    };

    // 单个组件编辑框
    var ComponentEditDlg = Backbone.View.extend({
        el: $("#component-edit-dlg-view"),
        template: _.template($('#component-edit-dlg-template').html()),
        events: {
            'click .component-close-btn': 'closeComponentDlg',
            'submit .component-edit-form': 'submitComponent',
        },
        render: function () {
            this.$el.html(this.template(this.model.attributes));
            var view = new ComponentView({model: this.model, uiconfig: {
                isShow_icon: 1,
                isShow_desc: 1,
            }});
            $('.component-view-container').html(view.render().el);
            return this;
        },
        toggle: function () {
            this.$el.fadeToggle();
            toggleUICover();
        },
        closeComponentDlg: function () {
            this.toggle();
            this.$el.html('');
        },
        submitComponent: function (event) {
            event.preventDefault();

            var form = $('.component-edit-form')[0];
            var componentList = submitComponentHelper(form);

            var error = this.model.validate(componentList[0].attributes);
            if (error != '') {
                alert(error);
                return;
            }

            this.model.set(componentList[0].attributes);

            typeof this.submitCallback == 'function' && this.submitCallback(this.model);

            this.closeComponentDlg();
        },
    });
        
    // 发现幻灯片编辑框
    var DiscoverSliderComponentEditDlg = Backbone.View.extend({
        el: $("#discover-slider-component-edit-dlg-view"),
        template: _.template($('#discover-slider-component-edit-dlg-template').html()),
        events: {
            'click .component-close-btn': 'closeComponentDlg',
            'click .add-component-item-btn': 'addComponentItem',
            'submit .component-edit-form': 'submitComponent',
        },
        render: function () {
            this.$el.html(this.template(this.model.attributes));

            var componentList = this.model.attributes.componentList;
            for (var i = 0; i < componentList.length; i++) {
                var model = componentList[i];
                var view = createComponentViewDiscoverSlider(model);
                $('.component-view-container').append(view.render().el);
            }
            return this;
        },
        toggle: function () {
            this.$el.fadeToggle();
            toggleUICover();
        },
        closeComponentDlg: function () {
            this.toggle();
            this.$el.html('');
        },
        addComponentItem: function () {   
            event.preventDefault();

            var view = createComponentViewDiscoverSlider(new ComponentModel());
            $('.component-view-container').append(view.render().el);
        },
        submitComponent: function (event) {
            event.preventDefault();

            var form = $('.component-edit-form')[0];

            this.model.attributes.componentList = submitComponentHelper(form);

            var error = this.model.validate(this.model.attributes);
            if (error != '') {
                alert(error);
                return;
            }

            typeof this.submitCallback == 'function' && this.submitCallback(this.model);

            this.closeComponentDlg();
        },
    });

    // 自定义风格编辑框
    var CustomStyleEditDlg = Backbone.View.extend({
        el: $("#custom-style-edit-dlg-view"),
        template: _.template($('#custom-style-edit-dlg-template').html()),
        events: {
            'click .style-close-btn': 'closeComponentDlg',
            'submit .style-edit-form': 'submitStyleComponent',
            'change .isShowStyleHeaderRadio' : 'changeShowHeader',
            'change .isShowStyleHeaderMoreRadio' : 'changeShowHeaderMore',
        },
        render: function () {
            this.$el.html(this.template(this.model.attributes));
            this.changeShowHeader();
            this.changeShowHeaderMore();

            // 添加风格区更多的组件
            var view = new ComponentView({model: new ComponentModel(this.model.attributes.extParams.styleHeader.moreComponent)});
            $('.component-view-container').html(view.render().el);
            return this;
        },
        toggle: function () {
            this.$el.fadeToggle();
            toggleUICover();
        },
        closeComponentDlg: function () {
            this.toggle();
            this.$el.html('');
        },
        // 风格区submit
        submitStyleComponent: function (event) {
            event.preventDefault();

            var form = $('.style-edit-form')[0];
            var componentList = submitComponentHelper(form);
            var extParams = {
                styleHeader: {
                    isShow: parseInt(form.isShowStyleHeader.value),
                    title: form.styleHeaderTitle.value,
                    position: parseInt(form.styleHeaderPosition.value),
                    isShowMore: parseInt(form.isShowStyleHeaderMore.value),
                    moreComponent: componentList[0].attributes,
                },
            };
            var model = {
                type: COMPONENT_TYPE_LAYOUT,
                style: form.layoutStyle.value,
                extParams: extParams,
            }

            var error = this.model.validate(model);
            if (error != '') {
                alert(error);
                return;
            }

            this.model.set(model);
            moduleEditMobileView.model.tempComponentList.add(this.model, {merge: true, remove: false, add: true});
            
            this.closeComponentDlg();
        },
        changeShowHeader: function () {
            $('.isShowStyleHeaderRadio')[0].checked ? $('.style-header-container').removeClass('hidden') : $('.style-header-container').addClass('hidden');
        },
        changeShowHeaderMore: function () {
            $('.isShowStyleHeaderMoreRadio')[0].checked ? $('.component-view-container').removeClass('hidden') : $('.component-view-container').addClass('hidden');
        },
    });
    
    // 自定义风格组件编辑框
    var CustomStyleComponentEditDlg = Backbone.View.extend({
        el: $("#custom-style-component-edit-dlg-view"),
        template: _.template($('#custom-style-component-edit-dlg-template').html()),
        initialize: function () {
        },
        initUIConfig: function () {
            this.uiconfig = {
                isShow_layoutOneColHigh: 0,
                isShow_layoutOneColLow: 0,
                isShow_layoutOneColLowFixed: 0,
                isShow_layoutTwoColText: 0,
                isShow_layoutTwoColHigh: 0,
                isShow_layoutTwoColMid: 0,
                isShow_layoutTwoColLow: 0,
                isShow_layoutThreeColText: 0,
                isShow_layoutThreeColHigh: 0,
                isShow_layoutThreeColMid: 0,
                isShow_layoutThreeColLow: 0,
                isShow_layoutFourCol: 0,
                isShow_layoutOneColOneRow: 0,
                isShow_layoutOneColTwoRow: 0,
                isShow_layoutOneColThreeRow: 0,
                isShow_layoutOneRowOneCol: 0,
                isShow_layoutTwoRowOneCol: 0,
                isShow_layoutThreeRowOneCol: 0,
                isShow_layoutSlider: 0,
                isShow_layoutNewsAuto: 0,
            };
        },
        events: {
            'click .style-component-close-btn': 'closeComponentDlg',
            'click .add-component-item-btn': 'addComponentItem',
            'submit .style-component-edit-form': 'submitStyleComponent',
            'change .layoutStyleSelect': 'onChangeComponentStyle',
        },
        render: function () {
            this.initUIConfig();
            // 过滤可选的组件布局
            var style = this.styleModel.attributes.style;
            switch (style) {
                case COMPONENT_STYLE_LAYOUT_DEFAULT:
                case COMPONENT_STYLE_LAYOUT_LINE:
                    for (var config in this.uiconfig) {
                        this.uiconfig[config] = 1;
                    }
                    if (style == COMPONENT_STYLE_LAYOUT_LINE) {
                        this.uiconfig.isShow_layoutTwoColText = 0;
                        this.uiconfig.isShow_layoutThreeColText = 0;
                        this.uiconfig.isShow_layoutNewsAuto = 0;
                    }
                    break;
                case COMPONENT_STYLE_LAYOUT_IMAGE:
                    this.uiconfig.isShow_layoutOneColHigh = 1;
                    this.uiconfig.isShow_layoutOneColLow = 1;
                    this.uiconfig.isShow_layoutOneColLowFixed = 1;
                    this.uiconfig.isShow_layoutThreeColText = 1;
                    this.uiconfig.isShow_layoutThreeColMid = 1;
                    this.uiconfig.isShow_layoutOneColOneRow = 1;
                    this.uiconfig.isShow_layoutOneColTwoRow = 1;
                    this.uiconfig.isShow_layoutOneRowOneCol = 1;
                    this.uiconfig.isShow_layoutTwoRowOneCol = 1;
                    this.uiconfig.isShow_layoutSlider = 1;
                    this.uiconfig.isShow_layoutNewsAuto = 1;
                    break;
                default:
                    break;
            }

            this.$el.html(this.template(this.model.attributes));
            // 初始化layoutStyleSelect
            if ($('.layoutStyleSelect :selected').hasClass('hidden')) {
                $('.layoutStyleSelect option')[0].selected = true;
            }
            this.onChangeComponentStyle();
            return this;
        },
        toggle: function () {
            this.$el.fadeToggle();
            toggleUICover();
        },
        closeComponentDlg: function () {
            this.toggle();
            this.$el.html('');
        },
        submitStyleComponent: function (event) {
            event.preventDefault();

            var form = $('.style-component-edit-form')[0];
            var model = {
                type: COMPONENT_TYPE_LAYOUT,
                style: form.layoutStyle.value,
                title: (new Date()).getTime().toString(), // 为了让风格区组件重新render
            }
            model.componentList = submitComponentHelper(form);

            var error = this.model.validate(model);
            if (error != '') {
                alert(error);
                return;
            }

            this.model.set(model);
            this.styleModel.tempComponentList.add(this.model, {merge: true, remove: false, add: true});
            this.styleModel.attributes.componentList = this.styleModel.tempComponentList.models;
            
            this.closeComponentDlg();
        },
        addComponentItem: function () {
            event.preventDefault();
            
            var view = createComponentViewSlider(new ComponentModel());
            $('.component-view-container').append(view.render().el);
        },
        onChangeComponentStyle: function () {
            var layoutStyle = $('.layoutStyleSelect').val();
            var layoutModel = this.model.attributes.style == layoutStyle ? this.model : new ComponentModel({type: COMPONENT_TYPE_DEFAULT, style: layoutStyle});
            var style = this.styleModel.attributes.style;

            var componentList = [];
            if (layoutStyle == this.model.attributes.style) {
                componentList = this.model.attributes.componentList;
            } else {
                var size = 0;
                switch (layoutStyle) {
                    case COMPONENT_STYLE_LAYOUT_ONE_COL:
                    case COMPONENT_STYLE_LAYOUT_ONE_COL_HIGH:
                    case COMPONENT_STYLE_LAYOUT_ONE_COL_MID:
                    case COMPONENT_STYLE_LAYOUT_ONE_COL_LOW:
                    case COMPONENT_STYLE_LAYOUT_ONE_COL_LOW_FIXED:
                    case COMPONENT_STYLE_LAYOUT_NEWS_AUTO:
                        size = 1;
                        break;
                    case COMPONENT_STYLE_LAYOUT_TWO_COL:
                    case COMPONENT_STYLE_LAYOUT_TWO_COL_TEXT:
                    case COMPONENT_STYLE_LAYOUT_TWO_COL_HIGH:
                    case COMPONENT_STYLE_LAYOUT_TWO_COL_MID:
                    case COMPONENT_STYLE_LAYOUT_TWO_COL_LOW:
                    case COMPONENT_STYLE_LAYOUT_ONE_COL_ONE_ROW:
                    case COMPONENT_STYLE_LAYOUT_ONE_ROW_ONE_COL:
                        size = 2;
                        break;
                    case COMPONENT_STYLE_LAYOUT_THREE_COL:
                    case COMPONENT_STYLE_LAYOUT_THREE_COL_TEXT:
                    case COMPONENT_STYLE_LAYOUT_THREE_COL_HIGH:
                    case COMPONENT_STYLE_LAYOUT_THREE_COL_MID:
                    case COMPONENT_STYLE_LAYOUT_THREE_COL_LOW:
                    case COMPONENT_STYLE_LAYOUT_ONE_COL_TWO_ROW:
                    case COMPONENT_STYLE_LAYOUT_TWO_ROW_ONE_COL:
                        size = 3;
                        break;
                    case COMPONENT_STYLE_LAYOUT_FOUR_COL:
                    case COMPONENT_STYLE_LAYOUT_FOUR_COL_HIGH:
                    case COMPONENT_STYLE_LAYOUT_FOUR_COL_MID:
                    case COMPONENT_STYLE_LAYOUT_FOUR_COL_LOW:
                    case COMPONENT_STYLE_LAYOUT_ONE_COL_THREE_ROW:
                    case COMPONENT_STYLE_LAYOUT_THREE_ROW_ONE_COL:
                        size = 4;
                        break;
                    default:
                        size = 0;
                        break;
                }
                for (var i = 0; i < size; i++) {
                    componentList.push(new ComponentModel());
                }
            }

            if (layoutStyle == COMPONENT_STYLE_LAYOUT_SLIDER) {
                $('.style-component-edit-form').find('.add-component-item-btn').show();
            } else {
                $('.style-component-edit-form').find('.add-component-item-btn').hide();    
            }
            
            $('.component-view-container').html('');
            var uiconfig = {
                isShow_desc: 1,
                isShow_icon: 1,
                isShow_iconStyle: 1,
                isShow_typeFasttext: 1,
                isShow_typeFastimage: 1,
                isShow_typeFastcamera: 1,
                isShow_typeFastaudio: 1,
                isShow_typeSign: 1,
                isShow_typeSearch: 1,
            };
            for (var i = 0; i < componentList.length; i++) {
                var model = componentList[i];
                // 过滤可选的图标样式
                switch (layoutStyle) {
                    case COMPONENT_STYLE_LAYOUT_ONE_COL_HIGH:
                    case COMPONENT_STYLE_LAYOUT_ONE_COL_LOW:
                    case COMPONENT_STYLE_LAYOUT_ONE_COL_LOW_FIXED:
                        uiconfig.isShow_iconStyleImage = 1;
                        uiconfig.isShow_iconStyleTextImage = 1;
                        uiconfig.isShow_iconStyleTextOverlapDown = 1;
                        uiconfig.isShow_iconStyleTextOverlapUpVideo = 1;
                        uiconfig.isShow_iconStyleTextOverlapDownVideo = 1;
                        if (layoutStyle == COMPONENT_STYLE_LAYOUT_ONE_COL_HIGH) {
                            uiconfig.iconRatio = '640*640';
                        } else if (layoutStyle == COMPONENT_STYLE_LAYOUT_ONE_COL_LOW) {
                            uiconfig.iconRatio = '320*640';
                        } else if (layoutStyle == COMPONENT_STYLE_LAYOUT_ONE_COL_LOW_FIXED) {
                            var iconRatioMap = {};
                            iconRatioMap[COMPONENT_STYLE_LAYOUT_DEFAULT] = '54*320';
                            iconRatioMap[COMPONENT_STYLE_LAYOUT_IMAGE] = '60*320';
                            iconRatioMap[COMPONENT_STYLE_LAYOUT_LINE] = '70*320';
                            uiconfig.iconRatio = iconRatioMap[style];
                        }
                        break;
                    case COMPONENT_STYLE_LAYOUT_TWO_COL_HIGH:
                        uiconfig.isShow_iconStyleImage = 1;
                        uiconfig.isShow_iconStyleTextOverlapDown = 1;
                        uiconfig.isShow_iconStyleTextOverlapUpVideo = 1;
                        uiconfig.isShow_iconStyleTextOverlapDownVideo = 1;

                        var iconRatioMap = {};
                        iconRatioMap[COMPONENT_STYLE_LAYOUT_DEFAULT] = '284*284';
                        iconRatioMap[COMPONENT_STYLE_LAYOUT_IMAGE] = '284*284';
                        iconRatioMap[COMPONENT_STYLE_LAYOUT_LINE] = '320*314';
                        uiconfig.iconRatio = iconRatioMap[style];
                        break;
                    case COMPONENT_STYLE_LAYOUT_TWO_COL_MID:
                        uiconfig.isShow_iconStyleImage = 1;
                        uiconfig.isShow_iconStyleTextOverlapDown = 1;
                        uiconfig.isShow_iconStyleTextOverlapDownVideo = 1;

                        var iconRatioMap = {};
                        iconRatioMap[COMPONENT_STYLE_LAYOUT_DEFAULT] = '284*240';
                        iconRatioMap[COMPONENT_STYLE_LAYOUT_IMAGE] = '284*240';
                        iconRatioMap[COMPONENT_STYLE_LAYOUT_LINE] = '320*272';
                        uiconfig.iconRatio = iconRatioMap[style];
                        break;
                    case COMPONENT_STYLE_LAYOUT_TWO_COL_LOW:
                        uiconfig.isShow_iconStyleImage = 1;
                        uiconfig.isShow_iconStyleTextOverlapDown = 1;
                        uiconfig.isShow_iconStyleTextOverlapDownVideo = 1;
                        uiconfig.isShow_iconStyleCircle = 1;

                        var iconRatioMap = {};
                        iconRatioMap[COMPONENT_STYLE_LAYOUT_DEFAULT] = '284*130';
                        iconRatioMap[COMPONENT_STYLE_LAYOUT_IMAGE] = '284*130';
                        iconRatioMap[COMPONENT_STYLE_LAYOUT_LINE] = '320*160';
                        uiconfig.iconRatio = iconRatioMap[style];
                        break;
                    case COMPONENT_STYLE_LAYOUT_THREE_COL_HIGH:
                        uiconfig.isShow_iconStyleImage = 1;
                        uiconfig.isShow_iconStyleTextOverlapDown = 1;

                        var iconRatioMap = {};
                        iconRatioMap[COMPONENT_STYLE_LAYOUT_DEFAULT] = '184*240';
                        iconRatioMap[COMPONENT_STYLE_LAYOUT_IMAGE] = '184*240';
                        iconRatioMap[COMPONENT_STYLE_LAYOUT_LINE] = '214*272';
                        uiconfig.iconRatio = iconRatioMap[style];
                        break;
                    case COMPONENT_STYLE_LAYOUT_THREE_COL_MID:
                        uiconfig.isShow_iconStyleImage = 1;
                        uiconfig.isShow_iconStyleTextOverlapDown = 1;
                        uiconfig.isShow_iconStyleTextOverlapDownVideo = 1;
                        uiconfig.isShow_iconStyleCircle = 1;

                        var iconRatioMap = {};
                        iconRatioMap[COMPONENT_STYLE_LAYOUT_DEFAULT] = '184*184';
                        iconRatioMap[COMPONENT_STYLE_LAYOUT_IMAGE] = '200*200';
                        iconRatioMap[COMPONENT_STYLE_LAYOUT_LINE] = '214*214';
                        uiconfig.iconRatio = iconRatioMap[style];
                        uiconfig.iconRatioCircle = '110*110';
                        break;
                    case COMPONENT_STYLE_LAYOUT_THREE_COL_LOW:
                        uiconfig.isShow_iconStyleImage = 1;
                        uiconfig.isShow_iconStyleTextOverlapDown = 1;
                        uiconfig.isShow_iconStyleTextOverlapDownVideo = 1;
                        uiconfig.isShow_iconStyleCircle = 1;

                        var iconRatioMap = {};
                        iconRatioMap[COMPONENT_STYLE_LAYOUT_DEFAULT] = '184*138';
                        iconRatioMap[COMPONENT_STYLE_LAYOUT_IMAGE] = '184*138';
                        iconRatioMap[COMPONENT_STYLE_LAYOUT_LINE] = '214*160';
                        uiconfig.iconRatio = iconRatioMap[style];
                        break;
                    case COMPONENT_STYLE_LAYOUT_FOUR_COL:
                        uiconfig.isShow_iconStyleImage = 1;
                        uiconfig.isShow_iconStyleTextOverlapDown = 1;
                        uiconfig.isShow_iconStyleTextOverlapDownVideo = 1;
                        uiconfig.isShow_iconStyleCircle = 1;

                        var iconRatioMap = {};
                        iconRatioMap[COMPONENT_STYLE_LAYOUT_DEFAULT] = '130*130';
                        iconRatioMap[COMPONENT_STYLE_LAYOUT_IMAGE] = '130*130';
                        iconRatioMap[COMPONENT_STYLE_LAYOUT_LINE] = '160*160';
                        uiconfig.iconRatio = iconRatioMap[style];
                        break;
                    case COMPONENT_STYLE_LAYOUT_ONE_COL_ONE_ROW:
                    case COMPONENT_STYLE_LAYOUT_ONE_ROW_ONE_COL:
                        uiconfig.isShow_iconStyleImage = 1;
                        uiconfig.isShow_iconStyleTextOverlapDown = 1;
                        uiconfig.isShow_iconStyleTextOverlapDownVideo = 1;
                        uiconfig.isShow_iconStyleCircle = 1;

                        var isCol = (layoutStyle == COMPONENT_STYLE_LAYOUT_ONE_COL_ONE_ROW && i == 0) || 
                            (layoutStyle == COMPONENT_STYLE_LAYOUT_ONE_ROW_ONE_COL && i == 1);

                        var iconRatioMap = {};
                        iconRatioMap[COMPONENT_STYLE_LAYOUT_DEFAULT] = ['410*220', '220*220'];
                        iconRatioMap[COMPONENT_STYLE_LAYOUT_IMAGE] = ['410*220', '220*220'];
                        iconRatioMap[COMPONENT_STYLE_LAYOUT_LINE] = ['410*220', '220*220'];
                        uiconfig.iconRatio = iconRatioMap[style][isCol ? 0 : 1];
                        break;
                    case COMPONENT_STYLE_LAYOUT_ONE_COL_TWO_ROW:
                    case COMPONENT_STYLE_LAYOUT_TWO_ROW_ONE_COL:
                        uiconfig.isShow_iconStyleImage = 1;
                        uiconfig.isShow_iconStyleTextOverlapDown = 1;
                        uiconfig.isShow_iconStyleTextOverlapDownVideo = 1;
                        
                        var isCol = (layoutStyle == COMPONENT_STYLE_LAYOUT_ONE_COL_TWO_ROW && i == 0) || 
                            (layoutStyle == COMPONENT_STYLE_LAYOUT_TWO_ROW_ONE_COL && i == 2);
                        uiconfig.isShow_iconStyleTextOverlapUpVideo = isCol ? 1 : 0;
                        uiconfig.isShow_iconStyleCircle = isCol ? 0 : 1;

                        var iconRatioMap = {};
                        iconRatioMap[COMPONENT_STYLE_LAYOUT_DEFAULT] = ['284*340', '284*160'];
                        iconRatioMap[COMPONENT_STYLE_LAYOUT_IMAGE] = ['410*410', '200*200'];
                        iconRatioMap[COMPONENT_STYLE_LAYOUT_LINE] = ['320*372', '320*186'];
                        uiconfig.iconRatio = iconRatioMap[style][isCol ? 0 : 1];
                        break;
                    case COMPONENT_STYLE_LAYOUT_ONE_COL_THREE_ROW:
                    case COMPONENT_STYLE_LAYOUT_THREE_ROW_ONE_COL:
                        uiconfig.isShow_iconStyleImage = 1;
                        uiconfig.isShow_iconStyleTextOverlapUp = 1;
                        uiconfig.isShow_iconStyleTextOverlapDown = 1;

                        var isCol = (layoutStyle == COMPONENT_STYLE_LAYOUT_ONE_COL_THREE_ROW && i == 0) || 
                            (layoutStyle == COMPONENT_STYLE_LAYOUT_THREE_ROW_ONE_COL && i == 3);
                        uiconfig.isShow_iconStyleTextImage = isCol ? 1 : 0;
                        uiconfig.isShow_iconStyleTextOverlapDownVideo = isCol ? 1 : 0;
                        uiconfig.isShow_iconStyleTextOverlapUpVideo = isCol ? 1 : 0;
                        
                        var iconRatioMap = {};
                        iconRatioMap[COMPONENT_STYLE_LAYOUT_DEFAULT] = ['430*450', '144*150'];
                        iconRatioMap[COMPONENT_STYLE_LAYOUT_IMAGE] = ['430*450', '144*150'];
                        iconRatioMap[COMPONENT_STYLE_LAYOUT_LINE] = ['460*480', '180*160'];
                        uiconfig.iconRatio = iconRatioMap[style][isCol ? 0 : 1];
                        uiconfig.isShow_iconStyleCircle = isCol ? 0 : 1;
                        break;
                    case COMPONENT_STYLE_LAYOUT_TWO_COL_TEXT:
                    case COMPONENT_STYLE_LAYOUT_THREE_COL_TEXT:
                        uiconfig.isShow_iconStyleText = 1;
                        model.attributes.iconStyle = COMPONENT_ICON_STYLE_TEXT;
                        break;
                    case COMPONENT_STYLE_LAYOUT_SLIDER:
                        uiconfig.isShow_delete = 1;
                        uiconfig.isShow_iconStyle = 0;
                        uiconfig.iconRatio = '2:1';
                        break;
                    case COMPONENT_STYLE_LAYOUT_NEWS_AUTO:
                        uiconfig.isShow_title = 0;
                        uiconfig.isShow_desc = 0;
                        uiconfig.isShow_icon = 0;
                        uiconfig.isShow_iconStyle = 0;
                        uiconfig.isShow_style = 0;
                        uiconfig.isShow_typeSelect = 0;
                        model.attributes.type = COMPONENT_TYPE_NEWSLIST;
                        break;
                    default:
                        console.error('Error Style!!!');
                        break;
                }
                var view = new ComponentView({model: model, uiconfig: uiconfig});
                $('.component-view-container').append(view.render().el);
            }
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

            $('.module-mobile-ui-view').html('').addClass('hidden');
            return this;
        },
        onChangeModuleType: function (event) {
            var moduleType = $('#moduleType').val();
            var moduleModel = this.model.attributes.type == moduleType ? this.model : new ModuleModel({type: moduleType});

            moduleEditDetailView.model = moduleModel;
            moduleEditDetailView.render();
            moduleEditMobileView.model = moduleModel;
            moduleEditMobileView.render();

            if (moduleType == MODULE_TYPE_SUBNAV) {
                this.$el.find('.module-style-select-div').show();
            } else {
                this.$el.find('.module-style-select-div').hide();
            }

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
                        var view = moduleType == MODULE_TYPE_FULL ? createComponentViewFull(model) : createComponentViewSubnav(model);
                        $('.component-view-container').eq(i).html(view.render().el);
                    }
                    break;
                case MODULE_TYPE_FASTPOST:
                    _.each(this.model.attributes.componentList, function (component) {
                        var view = createComponentViewFastpost(component);
                        $('.fastpost-components-container').append(view.render().el);
                    });
                    break;
                case MODULE_TYPE_NEWS:
                case MODULE_TYPE_CUSTOM:
                    var componentList = [];
                    if (this.model.attributes.type == moduleType) {
                        componentList = this.model.attributes.componentList;
                    }
                    moduleModel.tempComponentList = new ComponentList();
                    if (moduleType == MODULE_TYPE_CUSTOM) {
                        this.listenTo(moduleModel.tempComponentList, 'add', moduleEditMobileView.addCustomStyleItem);
                    } else {
                        this.listenTo(moduleModel.tempComponentList, 'add', moduleEditMobileView.addNewsComponentItem);
                    }
                    moduleModel.tempComponentList.set(componentList);

                    if (moduleType == MODULE_TYPE_NEWS) {
                        // 左图右文排序
                        sortableHelper($('.news-component-item-container'), moduleModel.tempComponentList.models);
                    }
                    if (moduleType == MODULE_TYPE_CUSTOM) {
                        // 风格区排序
                        sortableHelper($('.custom-style-item-container'), moduleModel.tempComponentList.models);
                    }
                    break;
                default:
                    break;
            }
        },
        moduleSubmit: function (event) {
            event.preventDefault();

            var form = $('.module-edit-form')[0];
            var moduleType = form.moduleType.value;
            this.model.set({
                title: form.moduleTitle.value,
                type: moduleType,
                style: form.moduleStyle.value,
            });

            switch (moduleType) {
                case MODULE_TYPE_FULL:
                case MODULE_TYPE_SUBNAV:
                case MODULE_TYPE_FASTPOST:
                    if (this.model.id == MODULE_ID_DISCOVER) {
                        var discoverModel = moduleEditMobileView.model.attributes.componentList[0];
                        var discoverModelCustomComponentList = discoverModel.getDiscoverComponentList(COMPONENT_STYLE_DISCOVER_CUSTOM);
                        discoverModelCustomComponentList.attributes.componentList = discoverModelCustomComponentList.tempComponentList.models;
                    } else {
                        this.model.attributes.componentList = submitComponentHelper(form);
                    }
                    break;
                case MODULE_TYPE_NEWS:
                case MODULE_TYPE_CUSTOM:
                    this.model.attributes.componentList = moduleEditMobileView.model.tempComponentList.models;
                    break;
                default:
                    break;
            }

            var error = this.model.validate(this.model.attributes);
            if (error != '') {
                alert(error);
                return;
            }

            if (this.model.isNew()) {
                this.model.set('id', this.model.getLastInsertId());
            }
            
            modules.add(this.model, {merge: true, remove: false, add: true});

            this.closeModule();

            if ($('#autoSaveCheckbox')[0].checked) {
                mainView.saveUIDiy(0);
            }

            showTipsByAppLevel();
        },
        closeModule: function () {
            this.toggle();
            
            moduleEditMobileView.$el.html('');

            $('.module-mobile-ui-view').removeClass('hidden');
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
            
            // 目前快速发帖的icon是固定的
            var icon = '';
            switch (type) {
                case COMPONENT_TYPE_FASTTEXT: icon = COMPONENT_ICON_FASTPOST+27; break;
                case COMPONENT_TYPE_FASTIMAGE: icon = COMPONENT_ICON_FASTPOST+28; break;
                case COMPONENT_TYPE_FASTCAMERA: icon = COMPONENT_ICON_FASTPOST+29; break;
                case COMPONENT_TYPE_FASTAUDIO: icon = COMPONENT_ICON_FASTPOST+45; break;
                case COMPONENT_TYPE_SIGN: icon = COMPONENT_ICON_FASTPOST+30; break;
                default: icon = ''; break;
            }

            var model = new ComponentModel({
                type: type,
                icon: icon,
            });

            var view = createComponentViewFastpost(model);
            $('.fastpost-components-container').append(view.render().el);
        },
        toggle: function () {
            this.$el.fadeToggle();
        },
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
            'click .add-style-btn': 'dlgAddStyleComponent',
            'click .add-discover-custom-component-item-btn': 'dlgAddDiscoverCustomComponent',
            'click .add-discover-slider-component-item-btn': 'dlgAddDiscoverSliderComponent',
        },
        initialize: function () { 
        },
        render: function () {
            this.$el.html(this.template(this.model.attributes));

            // render 发现组件
            if (this.model.id == MODULE_ID_DISCOVER) {
                var discoverModel = this.model.attributes.componentList[0];
                    sliderComponentList = discoverModel.getDiscoverComponentList(COMPONENT_STYLE_DISCOVER_SLIDER),
                    defaultComponentList = discoverModel.getDiscoverComponentList(COMPONENT_STYLE_DISCOVER_DEFAULT, true),
                    customComponentList = discoverModel.getDiscoverComponentList(COMPONENT_STYLE_DISCOVER_CUSTOM);
                // render default
                for (var i = 0; i < defaultComponentList.length; i++) {
                    var view = new DiscoverDefaultComponentItemView({model: defaultComponentList[i]});
                    $('.discover-default-component-container').append(view.render().el);
                }
                $('.discover-item-switch').bootstrapSwitch();
                
                // render custom
                customComponentList.tempComponentList = new ComponentList();
                this.listenTo(customComponentList.tempComponentList, 'add', this.addDiscoverCustomComponentItem);
                customComponentList.tempComponentList.add(discoverModel.getDiscoverComponentList(COMPONENT_STYLE_DISCOVER_CUSTOM, true), {merge: true, remove: false, add: true});
                // 添加排序
                sortableHelper($('.discover-custom-component-container'), customComponentList.tempComponentList.models);

                // render slider
                discoverSliderComponentView.model = sliderComponentList;
                discoverSliderComponentView.render();
            }

            return this;
        },
        selectTopbar: function (event) {
            var index = $(event.currentTarget).index();
            var module = this.model.attributes;
            var componentModel = new ComponentModel({type: COMPONENT_TYPE_EMPTY});

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
            moduleTopbarDlg.render();
            moduleTopbarDlg.toggle();

            $('#topbarIndex').val(index);
        },
        dlgAddNewsComponent: function () {
            componentEditDlg.model = new ComponentModel();
            componentEditDlg.submitCallback = function (model) {
                moduleEditMobileView.model.tempComponentList.add(model, {merge: true, remove: false, add: true});
            };
            componentEditDlg.render();
            componentEditDlg.toggle();
        },
        addNewsComponentItem: function (component) {
            var view = new NewsComponentItemView({model: component});
            $('.news-component-item-container').append(view.render().el);
        },
        // 弹出添加风格区
        dlgAddStyleComponent: function () {
            var model = new ComponentModel(uidiyGlobalObj.layoutInitParams);
            model.attributes.extParams.styleHeader = {
                isShow: 1,
                title: '',
                position: 0,
                isShowMore: 1,
            };
            customStyleEditDlg.model = model;
            customStyleEditDlg.render();
            customStyleEditDlg.toggle();
        },
        // 添加风格区
        addCustomStyleItem: function (style) {
            var view = new CustomStyleItemView({model: style});
            $('.custom-style-item-container').append(view.render().el);
        },
        dlgAddDiscoverCustomComponent: function () {
            componentEditDlg.model = new ComponentModel();
            componentEditDlg.submitCallback = function (component) {
                var discoverModel = moduleEditMobileView.model.attributes.componentList[0];
                var discoverCustomComponentList = discoverModel.getDiscoverComponentList(COMPONENT_STYLE_DISCOVER_CUSTOM);
                discoverCustomComponentList.tempComponentList.add(component, {merge: true, remove: false, add: true});

                scrollToBottomHelper($('.found-content')[0]);
            };
            componentEditDlg.render();
            componentEditDlg.toggle();
        },
        // 弹出添加发现幻灯组件编辑框
        dlgAddDiscoverSliderComponent: function () {
            var discoverModel = moduleEditMobileView.model.attributes.componentList[0];
            discoverSliderComponentEditDlg.model = discoverModel.getDiscoverComponentList(COMPONENT_STYLE_DISCOVER_SLIDER);
            discoverSliderComponentEditDlg.submitCallback = function (model) {
                discoverSliderComponentView.model = model;
                discoverSliderComponentView.render();
            };
            discoverSliderComponentEditDlg.render();
            discoverSliderComponentEditDlg.toggle();
        },
        addDiscoverCustomComponentItem: function (component) {
            var view = new DiscoverCustomComponentItemView({model: component});
            $('.discover-custom-component-container').append(view.render().el);
        },
    });
    
    var NewsComponentItemView = Backbone.View.extend({
        className: 'news-component-item list-group-item',
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
            componentEditDlg.model = this.model;
            componentEditDlg.render();
            componentEditDlg.toggle();
        },
        removeItem: function () {
            this.model.destroy();
        },
    });

    // 发现用户可添加组件view
    var DiscoverSliderComponentView = Backbone.View.extend({
        template: _.template($('#discover-slider-component-template').html()),
        render: function () {
            $('.discover-slider-component-container').html(this.template(this.model.attributes));
            $('.carousel-example-generic_one').carousel();
            return this;
        },
    });

    // 发现默认组件view
    var DiscoverDefaultComponentItemView = Backbone.View.extend({
        className: 'list-group-item',
        template: _.template($('#discover-default-component-item-template').html()),
        events: {
            'switchChange.bootstrapSwitch .discover-item-switch': 'switchItem',
        },
        render: function () {
            this.$el.html(this.template(this.model.attributes));
            return this;
        },
        // 显示/隐藏 组件
        switchItem: function (event, state) {
            this.model.attributes.extParams.isHidden = state ? 0 : 1;
        },
    });

    // 发现用户可添加组件view
    var DiscoverCustomComponentItemView = Backbone.View.extend({
        className: 'list-group-item',
        template: _.template($('#discover-custom-component-item-template').html()),
        events: {
            'click .edit-discover-item-btn': 'dlgEditComponent',
            'click .remove-discover-item-btn': 'removeItem',
        },
        initialize: function () {
            this.listenTo(this.model, 'change', this.render);
            this.listenTo(this.model, 'destroy', this.remove);
        },
        render: function () {
            this.$el.html(this.template(this.model.attributes));
            return this;
        },
        dlgEditComponent: function () {
            componentEditDlg.model = this.model;
            componentEditDlg.render();
            componentEditDlg.toggle();
        },
        removeItem: function () {
            this.model.destroy();
        },
    });

    // 自定义风格项视图
    var CustomStyleItemView = Backbone.View.extend({
        className: 'custom-style-item',
        template: _.template($('#custom-style-item-template').html()),
        events: {
            'click .edit-custom-style-item-btn': 'dlgEditCustomStyle',
            'click .add-style-component-btn': 'dlgAddStyleComponent',
            'click .remove-custom-style-item-btn': 'removeItem',
        },
        initialize: function () {
            this.listenTo(this.model, 'change', this.render);
            this.listenTo(this.model, 'destroy', this.remove);
        },
        render: function () {
            this.$el.html(this.template(this.model.attributes));

            this.model.tempComponentList = new ComponentList();
            this.listenTo(this.model.tempComponentList, 'add', this.addStyleComponentItem);
            this.model.tempComponentList.add(this.model.attributes.componentList, {merge: true, remove: false, add: true});

            // 风格区内组件拖动
            var _this = this;
            sortableHelper(this.$el.find('.custom-style-component-item-container'), _this.model.tempComponentList.models, function () {
                _this.model.attributes.componentList = _this.model.tempComponentList.models;
            });

            return this;
        },
        dlgEditCustomStyle: function () {
            customStyleEditDlg.model = this.model;
            customStyleEditDlg.render();
            customStyleEditDlg.toggle();
        },
        // 弹出添加风格区内组件
        dlgAddStyleComponent: function () {
            customStyleComponentEditDlg.model = new ComponentModel(uidiyGlobalObj.layoutInitParams);
            customStyleComponentEditDlg.styleModel = this.model;
            customStyleComponentEditDlg.render();
            customStyleComponentEditDlg.toggle();
        },
        removeItem: function () {
            if (confirm('确定要删除该风格区吗?')) {
                this.model.destroy();
            }
        },
        addStyleComponentItem: function (component) {
            var view = new CustomStyleComponentItemView({model: component});
            view.styleModel = this.model;
            this.$el.find('.custom-style-component-item-container').append(view.render().el);
        },
    });
    
    // 自定义风格内组件项视图
    var CustomStyleComponentItemView = Backbone.View.extend({
        className: 'custom-style-component-item',
        template: _.template($('#custom-style-component-item-template').html()),
        events: {
            'click .edit-style-component-item-btn': 'dlgEditItem',
            'click .remove-style-component-item-btn': 'removeItem',
            'click .add-style-component-item-btn': '',
        },
        initialize: function () {
            this.listenTo(this.model, 'change', this.render);
            this.listenTo(this.model, 'destroy', this.remove);
        },
        render: function () {
            this.$el.html(this.template(this.model.attributes));
            
            // render 列表自动类型
            if (this.model.attributes.style == COMPONENT_STYLE_LAYOUT_NEWS_AUTO) {
                var componentList = this.model.attributes.componentList;
                var _this = this;
                if (componentList.length > 0 && componentList[0].attributes.type == COMPONENT_TYPE_NEWSLIST) {
                    Backbone.ajax({
                        url: Appbyme.getAjaxApiUrl('portal/newslist')+'&hacker_uid=1',
                        // timeout: 5000,
                        type: 'get',
                        dataType: 'json',
                        data: {
                            moduleId: parseInt(componentList[0].attributes.extParams.newsModuleId),
                        },
                        success: function (result,status,xhr) {
                            var html = '';
                            for (var i = 0; i < result.list.length; i++) {
                                html += _.template(' \
                            <div class="newsauto-component-item"> \
                                <div class="pull-left"> \
                                    <img src="<%= pic_path %>" style="width:50px;height:50px" class="img-rounded"> \
                                </div> \
                                <div class="pull-left text-left page-main"> \
                                    <div class="page-title"><strong><%= title %></strong></div> \
                                    <div class="page-content"><%= summary %></div> \
                                </div> \
                            </div>')(result.list[i]);
                            }
                            _this.$el.find('.newsauto-component-item-container').html(html);
                        },
                        error: function (xhr,status,error) {
                            // console.error(xhr);
                            console.error(status);
                            console.error(error);
                        },
                    });
                }
            }
            return this;
        },
        dlgEditItem: function () {
            customStyleComponentEditDlg.model = this.model;
            customStyleComponentEditDlg.styleModel = this.styleModel;
            customStyleComponentEditDlg.render();
            customStyleComponentEditDlg.toggle();
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

            var view = createComponentViewTopbar(this.model);
            $('.topbar-component-view-container').html(view.render().el);

            return this;
        },
        submitTopbar: function () {
            event.preventDefault();

            var form = $('.module-topbar-edit-form')[0],
                index = parseInt(form.topbarIndex.value);

            var componentList = submitComponentHelper(form),
                model = componentList[0],
                type = model.attributes.type;

            // 目前topbar的icon是固定的
            var icon = '';
            switch (type) {
                case COMPONENT_TYPE_FASTTEXT: icon = COMPONENT_ICON_TOPBAR+21; break;
                case COMPONENT_TYPE_FASTIMAGE: icon = COMPONENT_ICON_TOPBAR+22; break;
                case COMPONENT_TYPE_FASTCAMERA: icon = COMPONENT_ICON_TOPBAR+24; break;
                case COMPONENT_TYPE_FASTAUDIO: icon = COMPONENT_ICON_TOPBAR+23; break;
                case COMPONENT_TYPE_USERINFO: icon = COMPONENT_ICON_TOPBAR+6; break;
                case COMPONENT_TYPE_SEARCH: icon = COMPONENT_ICON_TOPBAR+10; break;
                default: icon = ''; break;
            }
            model.attributes.icon = icon;

            var error = this.model.validate(model.attributes);
            if (error != '') {
                alert(error);
                return;
            }

            module = moduleEditMobileView.model.attributes;
            switch (index) {
                case 0:
                    if (type == COMPONENT_TYPE_EMPTY) {
                        module.leftTopbars = [];
                    } else {
                        module.leftTopbars[0] = model;
                    }
                    break;
                case 2:
                case 3:
                    module.rightTopbars[index-2] = model;
                    break;
                default:
                    break;
            }

            this.toggle();
        },
        toggle: function () {
            this.$el.fadeToggle();
            toggleUICover();
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
            // 弹出选择图标
            $('.select-nav-icon').on({
                click: function() {
                    $('.nav-icon').toggle('drop');
                }
            })
            // 图标选择
            $('.nav-pic').on({
                click: function() {
                    $('.nav-pic-preview').attr('src', $(this).attr('src'));
                    $('#navItemIcon').val($(this).attr('data-nav-icon'));
                    $('.nav-icon').toggle('drop');
                },

                mousemove: function() {
                    $(this).css('background', '#428bca');
                    $('.nav-pic-preview').attr('src', $(this).attr('src'));
                },
                mouseout: function() {
                    $(this).css('background', '');
                }
            });
            // 关闭图标选择
            $('.nav-icon-close').on({
                click: function() {
                    $('.nav-icon').toggle('drop');
                }
            })
            return this;
        },
        submitNavItem: function (event) {
            event.preventDefault();

            var form = $('.navitem-edit-form')[0];
            var model = {
                title: form.navItemTitle.value,
                moduleId: parseInt(form.navItemModuleId.value),
                icon: form.navItemIcon.value,
            };

            var error = this.model.validate(model);
            if (error != '') {
                alert(error);
                return;
            }

            this.model.set(model);
            navItems.add(this.model, {merge: true, remove: false, add: true});

            this.toggle();
        },
        toggle: function () {
            this.$el.fadeToggle();
            toggleUICover();
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
            toggleUICover();
        },
    });

    var MainView = Backbone.View.extend({
        el: $("#uidiy-main-view"),
        events: {
            'click .module-add-btn': 'dlgAddModule',
            'click .module-remove-btn': 'dlgRemoveModule',
            'click .navitem-add-btn': 'dlgAddNavItem',
            'click .uidiy-save-btn': 'uidiySave',
            'click .uidiy-sync-btn': 'uidiySync',
            'click .uidiy-init-btn': 'uidiyInit',
            'click .uidiy-config-import-btn': 'uidiyImportConfig',
            'change #autoSaveCheckbox': 'onChangeAutoSave',
        },
        initialize: function() {
            this.listenTo(modules, 'add', this.addModule);
            this.listenTo(navItems, 'add', this.addNavItem);

            _.each(uidiyGlobalObj.moduleInitList, function (module) {
                modules.add(new ModuleModel(module));
            })

            navItems.set(uidiyGlobalObj.navItemInitList);

            $('#autoSaveCheckbox')[0].checked = localStorageWrapper.getItem(APPBYME_UIDIY_AUTOSAVE) == 1;

            // 底部导航拖动
            sortableHelper($('.nav-item-container'), navItems.models);
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
            $('.nav-item-container').append(view.render().el);   
        },
        dlgAddNavItem: function () {
            navItemEditDlg.model = new NavItemModel();
            navItemEditDlg.render();
            navItemEditDlg.toggle();
        },
        dlgAddModule: function (event) {
            moduleEditDlg.model = new ModuleModel();
            moduleEditDlg.render();
            moduleEditDlg.toggle();
        },
        dlgRemoveModule: function (event) {
            var moduleId = $(event.currentTarget).parents('div.module')[0].id.slice(10);
            moduleRemoveDlg.model = modules.get(moduleId);
            moduleRemoveDlg.render();
        },
        onChangeAutoSave: function (event) {
            localStorageWrapper.setItem(APPBYME_UIDIY_AUTOSAVE, event.currentTarget.checked ? 1 : 0);
        },
        saveUIDiy: function (isSync, success, error) {
            Backbone.ajax({
                url: getAjaxApiUrl('admin/uidiy/savemodules'),
                type: 'post',
                dataType: 'json',
                data: {
                    modules: JSON.stringify(modules),
                    isSync: isSync,
                },
                success: function (result,status,xhr) {
                    var navInfo = {
                        type: NAV_TYPE_BOTTOM,
                        navItemList: navItems,
                    };
                    Backbone.ajax({
                        url: getAjaxApiUrl('admin/uidiy/savenavinfo'),
                        type: 'post',
                        dataType: 'json',
                        data: {
                            navInfo: JSON.stringify(navInfo),
                            isSync: isSync,
                        },
                        success: success,
                        error: error,
                    });
                },
                error: error,
            });
        },
        uidiySave: function () {
            this.saveUIDiy(0, function () {
                alert('保存成功');
            }, function () {
                alert('保存失败');
            });
        },
        uidiySync: function () {
            if (confirm('确定要现在同步吗?')) {
                this.saveUIDiy(1, function () {
                    alert('同步成功，实际的样式请以客户端为准！');
                }, function () {
                    alert('同步失败');
                });
            }
        },
        uidiyInit: function () {
            if (confirm('确定要初始化配置吗?')) {
                Backbone.ajax({
                    url: getAjaxApiUrl('admin/uidiy/init'),
                    type: 'post',
                    success: function (result,status,xhr) {
                        alert('初始化成功');
                        location.href = uidiyGlobalObj.rootUrl + '/index.php?r=admin/uidiy';
                    }
                });
            }
        },
        renderMobileUI: function (moduleId) {
            var module = modules.findWhere({id: moduleId});
            Backbone.ajax({
                url: getAjaxApiUrl('admin/uidiy/modulemobileui'),
                type: 'post',
                dataType: 'html',
                data: {
                    module: JSON.stringify(module),
                },
                success: function (result, status, xhr) {
                    $('.module-mobile-ui-view').html(result).removeClass('hidden');
                }
            });
        },
        uidiyImportConfig: function () {
            var data = new FormData();
            data.append('file', $('.uidiy-config-file')[0].files[0]);
            $.ajax({
                url: getAjaxApiUrl('admin/uidiy/importconfig'),
                type: 'post',
                dataType: 'json',
                data: data,
                processData: false,
                contentType: false,
                success: function (result, status, xhr) {
                    alert(result.errMsg);
                    if (result.errCode == 0) {
                        location.href = uidiyGlobalObj.rootUrl + '/index.php?r=admin/uidiy';
                    }
                },
                error: function (xhr, status, error) {
                    console.error(status);
                    console.error(error);
                },
            });
        },
    });
    
    window.Appbyme = {
        uiModules: modules,
        getNavIconUrl: getNavIconUrl,
        getComponentIconUrl: getComponentIconUrl,
        getAjaxApiUrl: getAjaxApiUrl,
    };

    var mainView = new MainView(),
        navItemEditDlg = new NavItemEditDlg(),
        navItemRemoveDlg = new NavItemRemoveDlg(),
        moduleEditDlg = new ModuleEditDlg(),
        moduleTopbarDlg = new ModuleTopbarDlg(),
        componentEditDlg = new ComponentEditDlg(),
        discoverSliderComponentEditDlg = new DiscoverSliderComponentEditDlg(),
        customStyleEditDlg = new CustomStyleEditDlg(),
        customStyleComponentEditDlg = new CustomStyleComponentEditDlg(),
        moduleEditDetailView = new ModuleEditDetailView(),
        moduleEditMobileView = new ModuleEditMobileView(),
        discoverSliderComponentView = new DiscoverSliderComponentView(),
        moduleRemoveDlg = new ModuleRemoveDlg();

    if (navItems.length) {
        mainView.renderMobileUI(navItems.models[0].attributes.moduleId);
    }

    Appbyme.mainView = mainView;
});
