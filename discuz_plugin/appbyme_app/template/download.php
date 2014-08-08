<?php exit; ?>
<!--{subtemplate common/header}-->
<link href="{$assetsBaseUrlPath}/css/download.css" type="text/css" rel="stylesheet">
<div id="appbyme_wrap">
    <div class="appbyme_header">
        <div class="appbyme_iphone_img"><img src="{$appImage}">
        </div>
        <div class="appbyme_text">
            <h1><!--{eval echo Appbyme::t('新版 ').$appName.Appbyme::t(' 客户端<br />强势归来！');}--></h1>
            <p>{$appDescribe}</p>
        </div>
        <div class="appbyme_download">
            <a href="{$androidDownloadUrl}">{lang appbyme_app:appbyme_android_install}</a>
            <a href="{$appleDownloadUrl}" class="appbyme_spec">{lang appbyme_app:appbyme_apple_install}</a>
        </div>
        <div class="appbyme_qrcode">
            <div><img src="{$androidQRCode}">{lang appbyme_app:appbyme_qrcode_install}</div>
            <div><img src="{$appleQRCode}">{lang appbyme_app:appbyme_qrcode_install}</div>
        </div>
    </div>
    <div class="appbyme_item">
        <ul>
            <li>
                <a><img src="{$assetsBaseUrlPath}/images/d_2.png">
                    <span>
                        <i><!--{eval echo Appbyme::t('功能丰富');}--></i>
                        <em><!--{eval echo Appbyme::t('支持分类信息/主题分类');}--></em>
                        <em><!--{eval echo Appbyme::t('支持搜索/分享/删选/注册');}--></em>
                    </span>
                </a>
            </li>
            <li>
                <a><img src="{$assetsBaseUrlPath}/images/d_3.png">
                    <span>
                        <i><!--{eval echo Appbyme::t('实时更新');}--></i>
                        <em><!--{eval echo Appbyme::t('社区新帖热帖实时更新');}--></em>
                        <em><!--{eval echo Appbyme::t('所有数据和网站实时同步');}--></em>
                    </span>
                </a>
            </li>
            <li>
                <a><img src="{$assetsBaseUrlPath}/images/d_4.png">
                    <span>
                        <i><!--{eval echo Appbyme::t('周边功能');}--></i>
                        <em><!--{eval echo Appbyme::t('查看周边用户、周边帖子');}--></em>
                        <em><!--{eval echo Appbyme::t('查看网友发帖位置');}--></em>
                    </span>
                </a>
            </li>
            <li>
                <a><img src="{$assetsBaseUrlPath}/images/d_5.png">
                    <span>
                        <i><!--{eval echo Appbyme::t('图片上传');}--></i>
                        <em><!--{eval echo Appbyme::t('随时随地拍照上传');}--></em>
                        <em><!--{eval echo Appbyme::t('可多选5张照片');}--></em>
                    </span>
                </a>
            </li>
            <li>
                <a><img src="{$assetsBaseUrlPath}/images/d_6.png">
                    <span>
                        <i><!--{eval echo Appbyme::t('语音发帖');}--></i>
                        <em><!--{eval echo Appbyme::t('轻松录音上传');}--></em>
                        <em><!--{eval echo Appbyme::t('倾听ta的声音');}--></em>
                    </span>
                </a>
            </li>
            <li>
                <a><img src="{$assetsBaseUrlPath}/images/d_7.png">
                    <span>
                        <i><!--{eval echo Appbyme::t('消息推送');}--></i>
                        <em><!--{eval echo Appbyme::t('回复信息及时通知');}--></em>
                        <em><!--{eval echo Appbyme::t('和好友实时语音交流');}--></em>
                    </span>
                </a>
            </li>
        </ul>
    </div>
</div>
<!--{subtemplate common/footer}-->