                <div class="row">
                    <div class="col-md-12">
                    <!-- The time line -->
                    <ul class="timeline">
                        <!-- timeline time label -->
                        <li class="time-label">
                            <span class="bg-red">
                             Start 
                            </span>
                        </li>
                        <!-- /.timeline-label -->
                        <?php 
                            foreach($route as $routes){
                                $back = 0;

                                $mthd = explode("|",$routes->method);
                                $method = $mthd[1];
                                $dept_id =$mthd[0];
                    
                                if($method == 'OUT'){
                                    $i_icon = 'fa fa-send bg-olive';
                                }else if($method == 'IN'){
                                    $i_icon = 'fa fa-send bg-orange-active';
                                }else if($method == 'PROD'){
                                    $i_icon = 'fa fa-cog bg-red-active';
                                }else{
                                    $i_icon = '';
                                }

                                if($method == 'OUT' OR$method == 'IN' OR $method == 'PROD' ){
                                ?>

                                <!-- Start timeline item -->
                                <!-- timeline item -->
                                <li style="margin-bottom:0px !important;">

                                    <i class="<?php echo $i_icon;?>"></i>
                                    <?php 
                                        $mthd_routes = $this->m_listOW->get_route_by_origin_method($origin,$routes->method);
                                        foreach($mthd_routes as $mr){
                                            if($method == 'OUT'){
                                                $outs   = $this->m_listOW->get_detail_pengiriman($mr->move_id);
                                                if(!empty($outs)){
                                                    $tgl  = tgl_indo(date('d-m-Y H:i:s',strtotime($outs->tanggal_transaksi)));
                                                    $header =  "Pengiriman Barang ".$outs->departemen.' ( '.$outs->kode.' )';
                                                    if($outs->status =='draft'){
                                                        $status =  '<span class="label bg-black">'.$outs->nama_status.'</span>';
                                                    }else if($outs->status =='ready'){
                                                        $status =  '<span class="label bg-blue">'.$outs->nama_status.'</span>';
                                                    }else if($outs->status =='done'){
                                                        $status =  '<span class="label bg-green-active">'.$outs->nama_status.'</span>';
                                                    }else{
                                                        $status =  '<span class="label bg-red">'.$outs->nama_status.'</span>';
                                                    }
                                                }else{
                                                    $tgl = '';
                                                    $header = '';
                                                    $status = '';
                                                }
                                            }else if($method == 'IN'){
                                                $in   = $this->m_listOW->get_detail_penerimaan($mr->move_id);
                                                if(!empty($in)){
                                                    $tgl  = tgl_indo(date('d-m-Y H:i:s',strtotime($in->tanggal_transaksi)));
                                                    $header =  "Penerimaan Barang ".$in->departemen.' ( '.$in->kode.' )';
                                                    if($in->status =='draft'){
                                                        $status =  '<span class="label bg-black">'.$in->nama_status.'</span>';
                                                    }else if($in->status =='ready'){
                                                        $status =  '<span class="label bg-blue">'.$in->nama_status.'</span>';
                                                    }else if($in->status =='done'){
                                                        $status =  '<span class="label bg-green-active">'.$in->nama_status.'</span>';
                                                    }else{
                                                        $status =  '<span class="label bg-red">'.$in->nama_status.'</span>';
                                                    }
                                                }else{
                                                    $tgl = '';
                                                    $header = '';
                                                    $status = '';
                                                }
                                            }else if($method == 'PROD'){
                                                $mrp    = $this->m_listOW->get_detail_items_mo($mr->move_id);
                                                if(!empty($mrp)){
                                                    $tgl  = tgl_indo(date('d-m-Y H:i:s',strtotime($mrp->start_time)));
                                                    $header =  "Manufacturing Order Group ".$mrp->departemen.' ( '.$mrp->kode.' )';
                                                    if($mrp->status =='draft'){
                                                        $status =  '<span class="label bg-black">'.$mrp->nama_status.'</span>';
                                                    }else if($mrp->status =='ready'){
                                                        $status =  '<span class="label bg-blue">'.$mrp->nama_status.'</span>';
                                                    }else if($mrp->status =='done'){
                                                        $status =  '<span class="label bg-green-active">'.$mrp->nama_status.'</span>';
                                                    }else{
                                                        $status =  '<span class="label bg-red">'.$mrp->nama_status.'</span>';
                                                    }
                                                }else{
                                                    $tgl = '';
                                                    $header = '';
                                                    $status = '';
                                                }
                                            }else{
                                                $tgl = '';
                                                $header = '';
                                                $status = '';
                                            }
                                        // untuk back order
                                        if($back >= 1 ){
                                            echo '<li>';
                                        }
                                    ?>
                                        <div class="timeline-item">
                                            <span class="time"><i class="fa fa-clock-o"></i> <?php echo $tgl.' '.$status;?> </span>

                                            <h3 class="timeline-header" style="text-align:left !important"><b><?php echo $header;?></b></h3>

                                            <div class="timeline-body" style="padding: 0px !important;">
                                                <?php 
                                                    if($method == 'OUT' OR $method == 'IN'){

                                                        if($method == 'IN'){
                                                            $pbi    = $this->m_listOW->get_detail_penerimaan_barang_items($in->kode);
                                                        }else {
                                                            $pbi    = $this->m_listOW->get_detail_pengiriman_barang_items($outs->kode);
                                                        }
                                                    
                                                    ?>
                                                        <div class="box-body" style="display: block; padding-top:5px !important;">
                                                            <div class="col-xs-12 table-responsive">
                                                                <table id="example1" class="table">
                                                                <thead>
                                                                    <tr>
                                                                        <th class='style'>Nama Produk</th>
                                                                        <th class='style'>Qty</th>
                                                                        <th class='style'>Tersedia</th>
                                                                        <th class='style'>Lokasi Tujuan</th>
                                                                        <th class='style'>Status</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <?php 
                                                                        if(!empty($pbi)){
                                                                        foreach($pbi as $pbis){
                                                                            if($pbis->qty_target > $pbis->qty_tersedia){
                                                                                $color = 'blue';
                                                                            }elseif($pbis->qty_tersedia > $pbis->qty_target){
                                                                                $color = 'red';
                                                                            }else{
                                                                                $color = '';
                                                                            }
                                                                    ?>      
                                                                        <tr>
                                                                            <td><?php echo $pbis->nama_produk?></td>
                                                                            <td style="text-align: right;"><?php echo number_format($pbis->qty_target,2)?></td>
                                                                            <td style="color:<?php echo $color;?>; text-align: right;" ><?php echo number_format($pbis->qty_tersedia,2)?></td>
                                                                            <td><?php echo $pbis->lokasi_tujuan?></td>
                                                                            <td><?php echo $pbis->nama_status?></td>
                                                                        </tr>
                                                                    <?php
                                                                        }
                                                                        }
                                                                    ?>
                                                                    
                                                                </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                <?php
                                                    }else{
                                                ?>
                                                        <div class="box-body" style="display: block; padding-top:5px !important;">
                                                            <div class="col-xs-12 table-responsive">
                                                                <table id="example1" class="table">
                                                                <thead>
                                                                    <tr>
                                                                        <th class='style'>Departemen</th>
                                                                        <th class='style'>Nama Produk</th>
                                                                        <th class='style'>Qty</th>
                                                                        <th class='style'>Tersedia</th>
                                                                        <th class='style'>Status</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <?php 
                                                                        if(!empty($mrp)){
                                                                        $mo    = $this->m_listOW->get_detail_mrp_fg_target_items($mrp->kode);
                                                                        foreach($mo as $mos){
                                                                            if($mos->qty_target > $mos->qty_tersedia){
                                                                                $color = 'blue';
                                                                            }elseif($mos->qty_tersedia > $mos->qty_target){
                                                                                $color = 'red';
                                                                            }else{
                                                                                $color = '';
                                                                            }
                                                                    ?>      
                                                                        <tr>
                                                                            <td><?php echo $mos->departemen?></td>
                                                                            <td><?php echo $mos->nama_produk?></td>
                                                                            <td style="text-align: right;"><?php echo number_format($mos->qty_target,2)?></td>
                                                                            <td style="color:<?php echo $color;?>; text-align: right;"><?php echo number_format($mos->qty_tersedia,2)?></td>
                                                                            <td><?php echo $mos->nama_status?></td>
                                                                        </tr>
                                                                    <?php
                                                                        }
                                                                        }
                                                                    ?>
                                                                    
                                                                </tbody>
                                                                </table>
                                                            </div>
                                                        </div>

                                                <?php 

                                                    }

                                                ?>

                                            </div>
                                        
                                        </div>
                                    <?php 
                                        if($back >= 1 ){
                                            echo '</li>';
                                        }
                                    ?>

                                </li>
                                <!-- END timeline item -->
                                <?php 
                                    $back++;
                                        }
                                        ?>
                        <?php   
                                }
                            }
                        ?>
                        <!-- timeline time label -->
                        <li class="time-label">
                            <span class="bg-green-active">
                            <i class="fa fa-flag-checkered"></i> Finish 
                            </span>
                        </li>
                    </ul>
                    </div>
                    <!-- /.col -->
                </div>