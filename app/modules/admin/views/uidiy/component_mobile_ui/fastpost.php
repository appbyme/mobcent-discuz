
 <div class="content-list-ui">
        <div style="height:493px;padding-top: 300px;background-color: grey;">
            <?php 
            $count = count($component['componentList']);
            if($count == 6 || $count == 3){
                     foreach($component['componentList'] as $k => $v){   $icon = $v['icon'];?>
                    <div style="width:33%;height:110px;float: left;padding-top: 15px;">
                        <div>
                            <img style="width:70px;height: 70px;" src="<?php echo $v['icon']?>">
                        </div>
                        <div style="height: 50px;padding-top:5px;"><?php echo mb_substr($v['title'],0,6);?></div>
                    </div>
                  <?php }}else if($count == 4 ||  $count == 2){ ?>
                    <div style="width: 230px;margin: auto;">
                     <?php foreach($component['componentList'] as $k => $v){  $icon = $v['icon']; ?>
                        <div style="width:50%;height:110px;float: left;margin: 0 auto;">
                            <div>
                                <img style="width:70px;height: 70px;" src="<?php echo $icon; ?>">
                            </div>
                            <div style="height: 50px;padding-top:5px;"><?php echo mb_substr($v['title'],0,6);?></div>
                        </div>
                     <?php }?>
                   </div>
                   <?php }else if($count == 1){ 
                         foreach($component['componentList'] as $k => $v){   $icon= $v['icon']; ?>
                         <div style="margin: 0 auto;height:110px;">
                            <div>
                                <img style="width:70px;height: 70px;" src="<?php echo $icon; ?>">
                            </div>
                            <div style="height: 50px;padding-top:5px;"><?php echo mb_substr($v['title'],0,6);?></div>
                        </div>
                        <?php }}else{  foreach($component['componentList'] as $k => $v){
                              $icon = $v['icon'];
                              if($k <=2){?>
                             <div style="width:33%;height:110px;float: left;padding-top: 15px;">
                                <div>
                                    <img style="width:70px;height: 70px;" src="<?php echo $icon; ?>">
                                </div>
                                <div style="height: 50px;padding-top:5px;"><?php echo mb_substr($v['title'],0,6);?></div>
                            </div>
                            <?php }else{?>
                               <div style="width:20%;height:110px;float: left;padding-top:10px;padding-left:75px;padding-right: 35px;">
                                  <div>
                                      <img style="width:70px;height: 70px;" src="<?php echo $icon; ?>">
                                  </div>
                                  <div style="height: 50px;padding-top:5px;"><?php echo mb_substr($v['title'],0,6); ?></div>
                               </div>
                        <?php }}} ?>
        </div>
</div>


