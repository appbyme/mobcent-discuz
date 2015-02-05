

$(function() {

    // 添加公共服务
    $('.add-public-btn').on({
        click:function() {

            $('.save-public').show();
            $('.edit-public').hide();
            initAddPublic();

            var title = $('.public-title').val('');
            var icon = $('.public-img').attr('src', '');
            var keyword = $('.public-keyword').val('');
            switchCovering('add-public');              }
    })
    
    $('.save-public').on({
        click:function() {
            var title = $('.public-title').val();
            var icon = $('.public-img').attr('src');
            var type = $('.public-type').find("option:selected").val();
            var keyword = $('.public-keyword').val();

            if (title == '' || keyword == '') {
                alert('标题或者服务词不能空！');
                return false;
            }

            $.ajax({
                type:'POST',
                url :dzRootUrl + '/mobcent/app/web/index.php?r=admin/wshdiy/addpublic',
                data:{
                    title:title,
                    icon:icon,
                    type:type,
                    keyword:keyword
                },
                dataType:'json',
                cache: false,
                success:function(msg) {
                    console.log(msg);
                    if (msg.errCode) {
                        // alert('添加成功！');
                        location.href = dzRootUrl + '/mobcent/app/web/index.php?r=admin/wshdiy'; 
                    } else {
                        alert('添加失败！');
                        location.href = dzRootUrl + '/mobcent/app/web/index.php?r=admin/wshdiy'; 
                    }
                }
            })
        }
    })    

    // 编辑公共服务
    $('.edit-public-btn').on({
        click:function() {
            var id = $(this).attr('data-id');
            $('.public-alert-title').html('编辑公共服务页');

            $('.save-public').hide();
            $('.edit-public').show();
            initAddPublic();

            $.ajax({
                type:'GET',
                url :dzRootUrl + '/mobcent/app/web/index.php?r=admin/wshdiy/editpublic&id='+id,
                dataType:'json',
                cache: false,
                success:function(msg) {
                    $('.public-id').val(msg.moduleRow.id);
                    $('.public-title').val(msg.moduleRow.title);
                    $('.public-img').attr('src', msg.moduleRow.icon);
                    $('.public-type').val(msg.moduleRow.type);
                    $('.public-keyword').val(msg.moduleRow.keyword);
                }
            })

            switchCovering('add-public');              }
    })
    
    $('.edit-public').on({
        click:function() {
            var id = $('.public-id').val();
            var title = $('.public-title').val();
            var icon = $('.public-img').attr('src');
            var type = $('.public-type').find("option:selected").val();
            var keyword = $('.public-keyword').val();

            if (title == '' || keyword == '') {
                alert('标题或者服务词不能空！');
                return false;
            }

            $.ajax({
                type:'POST',
                url :dzRootUrl + '/mobcent/app/web/index.php?r=admin/wshdiy/editpublic&id='+id,
                data:{
                    title:title,
                    icon:icon,
                    type:type,
                    keyword:keyword
                },
                dataType:'json',
                cache: false,
                success:function(msg) {
                    location.href = dzRootUrl + '/mobcent/app/web/index.php?r=admin/wshdiy'; 
                }
            })    
        }
    })

    $('.wap-public-btn').on({
        click:function() {
            $('.wap-public').css({"top":"100px", "left":"100px"})
            var url = $(this).attr('data-url');
            $('.public-url').html(url);
            switchCovering('wap-public');
        }
    })

    $('.add-public input[name=title]').on({
        keyup:function() {
            var len = $(this).val().length;
            if (len >= 1 && len <= 8) {
                $('.save-public').removeClass('disabled');
                $(this).next('.help-block').css('color', '');
            } else {
                $('.save-public').addClass('disabled');
                $(this).next('.help-block').css('color', 'red');
            }
        }
    })

    // 上传图片
    $('.public-icon').on({
        change:function() {
            var data = new FormData();
            data.append('file', $('.public-icon')[0].files[0]);
            $.ajax({
                type:'POST',
                url:dzRootUrl + '/mobcent/app/web/index.php?r=admin/uidiy/uploadicon&type=wshdiy',
                data:data,
                dataType:'json',
                cache: false,
                processData:false,
                contentType:false,
                success:function(msg) {

                    var oldImg = $('.public-img').attr('src');
                    if (oldImg != '') {
                        $.ajax({
                            type: 'GET',
                            url: dzRootUrl + '/mobcent/app/web/index.php?r=admin/uidiy/delicon&fileName='+oldImg,
                            success: function(msg) {

                            }
                        });
                    }

                    $('.public-img').attr('src', msg.errMsg);

                }
            })
        }
    })
    
    // 公共服务点击左侧显示
    $('.module-list').on({
        click:function() {
            var url = $(this).find('.wap-public-btn').attr('data-url');
            $('.wap-preview').attr('src', url);  
        }
    })

    // 关闭添加公共服务页
    $('.add-public-close').on({
        click:function() {
            switchCovering('add-public');  
        }
    })

    // 关闭wap地址
    $('.wap-public-close').on({
        click:function() {
            switchCovering('wap-public');  
        }
    })

    // 拖动
    $('.add-public').draggable();
    // $('.wap-public').draggable();

    // 弹出框和覆盖层
    function switchCovering(className) {
        $('.' + className).fadeToggle();
        $('.covering').fadeToggle();   
    }


    function initAddPublic() {
        $('.add-public').css({
            "left": "100px",
            "top": "100px"
        })
    }
})