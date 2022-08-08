
<form class="form-horizontal">
    <?php if(!empty($sm_obat)){?>
    <div class="col-md-6 col-xs-12">
        <div class="box box-default " style="margin-bottom:0px !important">
            <div class="box-header with-border"><b>PDF<b></div><br>
            <div class="form-group">
                <div class="col-md-12 col-xs-12">
                    <div class="col-xs-12">
                        <?php 
                            $add_num = 1;
                            $caption = '';
                            foreach($sm_obat as $rmo){
                                if($rmo->additional == 'f'){
                                    $caption = 'Obat '.$departemen;
                                }else if($rmo->additional == 't'){ 
                                    $caption = 'Additional '.$add_num;
                                    $add_num++;
                                }
                        ?>            
                            <button type="button" class="btn btn-default btn-sm" data-togle="toottip" title="Print <?php echo $caption;?>" onclick="print('<?php echo $kode?>','<?php echo $rmo->move_id;?>')"> <i class="fa fa-file-pdf-o"></i> <?php echo $caption;?>
                            </button><br><br>
                        <?php
                            }
                        ?>
                    </div>  
                </div>
            </div>
            <br>
        </div>
    </div>
    <div class="col-md-6 col-xs-12">
        <div class="box box-default " style="margin-bottom:0px !important">
            <div class="box-header with-border"><b>TXT<b></div><br>
            <div class="form-group">
                <div class="col-md-12 col-xs-12">
                    <div class="col-xs-12">
                        <?php 
                            $add_num = 1;
                            $caption = '';
                            foreach($sm_obat as $rmo){
                                if($rmo->additional == 'f'){
                                    $caption = 'Obat '.$departemen;
                                }else if($rmo->additional == 't'){ 
                                    $caption = 'Additional '.$add_num;
                                    $add_num++;
                                }
                        ?>            
                            <button type="button" class="btn btn-default btn-sm" data-togle="toottip" title="Print <?php echo $caption;?>" onclick="txt('<?php echo $kode?>','<?php echo $rmo->move_id;?>')"> <i class="fa fa-file-text-o"></i> <?php echo $caption;?>
                            </button><br><br>
                        <?php
                            }
                        ?>
                    </div>  
                </div>
            </div>
            <br>
        </div>
    </div>
    <?php 
    }else{ ?>
        <p>Data yang akan di Print belum tersedia !</p>
    <?php
    }?>
  <div class="form-group">
  </div>
</form>

<script type="text/javascript">

    function print(kode,move_id){
        event.preventDefault();
        var url      = '<?php echo base_url() ?>manufacturing/mO/print_mo';
        window.open(url+'?kode='+ kode+'&move_id='+move_id,'_blank');
        //$('#print_data').modal('hide');
    }

    function txt(kode,move_id){
        event.preventDefault();
        var url      = '<?php echo base_url() ?>manufacturing/mO/export_txt';
        window.open(url+'?kode='+ kode+'&move_id='+move_id,'_blank');
        //$('#print_data').modal('hide');
    }

</script>