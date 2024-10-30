<h2>
    <?php echo $main_tab_title; ?>
</h2>
<div class="row">
    <div class="board">
        <div class="board-inner">
            <ul class="nav nav-tabs" id="myTab">
               <div class="owl_1 owl-carousel owl-theme">
                <?php
                $count = 0;
                foreach ($stores as $_stores) {
                ?>
                     <div class="item">
                  <li class="<?php echo ++$count == 1 ? 'active' : ''; ?>">
                      <a data-toggle="tab" href="#<?php echo $_stores->key; ?>" title="welcome">
                          <span class="round-tabs one"><?php echo $_stores->key; ?></span>
                      </a>
                  </li>
                     </div>
                <?php
                }
                ?>
               </div>
            </ul>
        </div>
        <div class="tab-content">
             <?php
            $count = 0;
            foreach ($stores as $_stores) {
            ?>
            <div class="tab-pane fade <?php echo ++$count == 1 ? 'in active' : ''; ?>" id="<?php echo $_stores->key; ?>">
                 <table class="table table-striped table-bordered" id="tblListing">
                    <thead>
                        <tr>
                                    <th style="width:70%">Date</th>
            <th style="width:40%">Day</th>
            <?php
                if(sizeof($dynamic_column)) {
                    foreach($dynamic_column as $value) {
            ?>
                        <th style="width:50%"><?php echo $value ?></th>
            <?php   }
                }
            ?>
           
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                       if(isset($alldates) && !empty($alldates)){
                             
                           foreach ($alldates as $aRow){
                                $total_Amount = 0;
                               ?>
                        <tr>
                            <td><?php echo $aRow; ?></td>
                            <td><?php  echo date('D',strtotime($aRow)); ?></td>
                            <?php 
                            $cdate = str_replace("-", "", $aRow);
//                            echo "<pre>";
//                            print_r($paidout[350432][14052020]);
//                           echo "</pre>";
//                            if(isset($paidout[$_stores->key][$aRow]) && !empty($paidout[$_stores->key][$aRow])){
                                  if(sizeof($dynamic_column)) {
                                    
                    foreach($dynamic_column as $key => $value) {
                                ?>
                            
                            <td><?php 
                            $amount = isset($cardrecapdata[$_stores->key][$cdate][$key]) ? $cardrecapdata[$_stores->key][$cdate][$key] : 0; 
                            echo number_format($amount, 2);
                            $total_Amount += $amount;
                            ?></td>
                            <?php
                    }
                                  }
                                  
//                            }
                            ?>
                            
                            
                        </tr>
                               <?php
                           }
                       }
                        ?>
                    </tbody>
                 </table>
            </div>
            <?php } 
            ?>
                            
        </div>
    </div>
</div>