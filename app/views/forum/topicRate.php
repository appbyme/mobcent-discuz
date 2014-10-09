<?php header("Content-Type: text/html; charset=utf-8");?>
<?php global $_G; ?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="Cache-Control" content="no-transform" />
<link rel="alternate" type="application/vnd.wap.xhtml+xml" media="handheld" href="target"/>
<meta name="viewport" content="width=device-width,user-scalable=no,minimum-scale=1.0,initial-scale=1.0">
<script type="text/javascript" src ="<?php echo $this->dzRootUrl; ?>/static/js/common.js"></script>
<script type="text/javascript" src="<?php echo $this->rootUrl.'/js/'; ?>jquery-2.0.3.min.js"></script>
<style type="text/css">
    *{
        margin: 0;
        padding: 0;
        word-wrap: break-word;
    }
    body, input, button, select, textarea {
        font: 12px/1.5 "Hiragino Sans GB", "Microsoft YaHei", "微软雅黑", "宋体", Arial, Verdana, sans-serif;
        color: #444;
    }
    h1, h2, h3, h4, h5, h6 {
        font-size: 1em;
    }
    a {
        color: #333;
        text-decoration: none;
    }
    li {
        list-style: none;
    }
    #main{
        margin: 0 auto;
        width: 20%;
        height: auto;
        /*background: #e3e3e3;*/
        padding-bottom: 5px;
    }

    table td {
        border-radius: 5px;
    }

    table tr {
    	height: 25px;
    }

    .rate_font{
        font-size: 15px;
        font-weight: 700;
        color: #369;
    }

	select{
	    border-radius: 5px;
	}

    #rate_category {
    	margin:15px 0px 5px 0px;
    }

	#rate_reason {
		width: 100%;
		padding: 3px;
	}

	#foot {
		width: 100px,
		padding:3px;
		text-align: right;
	}

	.pc {
	    vertical-align: middle;
	    margin: 0 8px 1px 0;
	    padding: 0;   		
	}

	.btu {
	    width: 80px;
	    height: 32px;
	    line-height: 32px;
	    background: #6da136;
	    color: #fff;
	    display: inline-block;
	    padding: 0;
	    -webkit-border-radius: 3px;
	    -moz-border-radius: 3px;
	    border-radius: 3px;
	    margin: 5px 0;
	    border: none;
	}	

	.pt {
		width: 100%;
		margin-top: 5px;
		border-radius: 5px;
		padding: 4px;
	}

    @media (max-width: 480px) {
	    #main{
	        width: 90%;
	        margin: 0 auto;
	        padding-left: 5%;
	        padding-right: 5%;
	    }

	    #rate_category, #rate_reason, #foot {
	    	width: 90%;
	    }
	}
</style>
<script type="text/javascript">
    $(function(){
    	$('select').on({
    		change:function(){
    			$(this).prev().val($(this).val());
    		}
    	});
    	
	    $('#reasonSelect').on({'change': function () {
	        var value = $(this).val();
	        if (value != '-1') {
	            if (value == '0') {
	                $('#reason').get(0).focus();
	            } else {
	                $('#reason').val(value);
	            }
	        }
	    }});

        var errorMsg = '<?php echo WebUtils::u($errorMsg); ?>';
        if (errorMsg != '') {
            alert(errorMsg);
        }
    })
</script>
</head>
<body>
    <div id="main">
    <form method="post" action="<?php echo $formUrl; ?>">
        <h4 class="rate_font">评分</h4>
        <div id="rata_interval">
            <table style="margin:0 auto;text-align:center;width:100%" >
                <tr class="rate_top">
                    <td colspan="2"></td>
                    <td width=25%>评分区间</td>
                    <td width=25%>今日剩余</td>
                </tr>
                <?php foreach( $ratelist as $id=>$options): ?>
                <?php
                    $reg = '/[+|-][\w]+/';
                    preg_match_all($reg, $options, $options);
                    $options = $options[0];
                ?>
                <tr class="rate_content">
                    <td ><?php echo WebUtils::u($_G['setting']['extcredits'][$id]['title']);?></td>
                    <td width=35%>
                        <input type="text" style="width:30%;" value="0" name="score<?php echo $id ?>" />
                        <select class="rate_size">
                            <option value="0">0</option>
                            <?php foreach($options as $key=>$value): ?>
                                <option value="<?php echo $value; ?>"><?php echo $value; ?></option>
                            <?php endforeach; ?>
                    	</select>
                    </td>
                    <td><?php echo $_G['group']['raterange'][$id]['min'];?> ~ <?php echo $_G['group']['raterange'][$id]['max'];?></td>
                    <td><?php echo $maxratetoday[$id] ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>

        <div id="rate_category">
        	<span>可选评分理由:</span>
            <select id="reasonSelect">
            <?php foreach($selectreason as $value): ?>
                <option value="<?php echo WebUtils::u($value); ?>"><?php echo WebUtils::u($value); ?></option>
            <?php endforeach; ?>
                <option value="0">自定义</option>
            </select>
            <p><textarea id="reason" name="reason" class="pt" rows="2"></textarea></p>
            <p class="o pns">
                <label for="sendreasonpm">
                    <input type="checkbox" name="sendreasonpm" id="sendreasonpm" class="pc" style="margin-right:5px;">通知作者
                </label>
            </p>
            <p style="text-align:center">
                <button type="submit" name="modsubmit" id="modsubmit" class="btu" value="确定" ><span>确定</span>
                </button>
            </p>
        </div>
    </form>
    </div>
</body>