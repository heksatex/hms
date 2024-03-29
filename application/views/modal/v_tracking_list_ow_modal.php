<style>
    .add-title{
        font-size: 15px !important;
    }
</style>

<form class="form-horizontal">
 	<div class="row">                  
        <div class="col-xs-12 col-md-6">
            <div class="col-xs-6 col-md-5">
                <label>Sales Contract [SC]</label>                            
            </div>            
            <div class="col-xs-6 col-md-7"> 
              <label>:</label>                            
              <?php echo $sales_order; ?>                      
            </div>
            <div class="col-xs-6 col-md-5">                          
                <label>Nama Produk </label>
            </div>
            <div class="col-xs-6 col-md-7"> 
                <label>:</label>                            
                <?php echo $nama_produk; ?>
            </div>
            <div class="col-xs-6 col-md-5">                          
                <label>Qty </label>
            </div>
            <div class="col-xs-6 col-md-7"> 
                <label>:</label>                            
                <?php echo $qty; ?>
            </div>
        </div>
        <div class="col-xs-12 col-md-6">
            <div class="col-xs-6 col-md-4">
                <label>CO</label>                            
            </div>            
            <div class="col-xs-6 col-md-8"> 
              <label>:</label>                            
              <?php echo $kode_co; ?>                      
            </div>
            <div class="col-xs-6 col-md-4">
                <label>OW</label>                            
            </div>            
            <div class="col-xs-6 col-md-8"> 
              <label>:</label>                            
              <?php echo $ow; ?>                      
            </div>
            <div class="col-xs-6 col-md-4">
                <label>Warna</label>                            
            </div>            
            <div class="col-xs-6 col-md-8"> 
              <label>:</label>                            
              <?php echo $nama_warna; ?>                      
            </div>
        </div>
    </div>

    <div class="col-xs-12 col-md-6 add-title">
        <label>List Pemartaian By OW</label>
    </div>
    <br>

    <div class="panel-group" id="accordion">
    <?php 
        $num = 1;
        $last_done ='';
        foreach($list as $cod){
            if($cod->nama_status != 'Draft'){

                if(!empty($cod->last_method)){
                    //explode
                    $ex_mth = explode("|",$cod->last_method);
                    //cek nama departemen
                    $dept= $this->_module->get_nama_dept_by_kode($ex_mth[0])->row_array();
                    if($ex_mth[1]== 'IN'){
                        $last_done = '( Penerimaan '.$dept['nama'].' )';
                    }else if($ex_mth[1] == 'OUT'){
                        $last_done = '( Pengiriman  '.$dept['nama'].' )';
                    }else{
                        $last_done = '( MG '.$dept['nama'].' )';
                    }
                }else{
                    $last_done = '';
                }

                $href     = 'collapse'.$cod->row_order;
                $id_panel = 'panel'.$cod->row_order;
                if($cod->status == 'cancel'){
                    $color = "style='color:red'";
                }else if($cod->status == 'ng'){
                    $color = "style='color:blue'";
                }else{
                    $color = "style='color:#333'";
                }
                $panel_title   = "<a data-toggle='collapse' title='Lihat Route OW' data-parent='#accordion' href='#".$href."'  ".$color.">".$num.". ".$cod->nama_produk .' '.number_format($cod->qty,2).' '.$cod->uom ." ( ".$cod->route_co." ) ".$last_done."</a> <label class='pull-right' ".$color .">".$cod->nama_status."</p>";
            }else{
                $href           = '';
                $id_panel       = '';
                $panel_title    = "<p data-toggle='tooltip' title='Route OW Belum Terbentuk' style='margin:0px'>".$num.". ".$cod->nama_produk .' '.number_format($cod->qty,2).' '.$cod->uom .' - '.$cod->nama_status." ( ".$cod->route_co." )</p>";
            }
            $num++;
    ?>
        <div class="panel panel-default" id="<?php echo $id_panel;?>" row="<?php echo $cod->row_order;?>">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <?php echo $panel_title?>
                </h4>
            </div>
            <div id="<?php echo $href; ?>" class="panel-collapse collapse" > <!--addclass in for view panel-->
                <div class="panel-body">
                </div>
            </div>
        </div>

    <?php
        }
    ?>
    </div>


</form>


<script>

    $('.panel').on('shown.bs.collapse', function (e) {
        console.log('Collapse Alert' + e.currentTarget.id);
        var kode_co         = '<?php echo $kode_co;?>';
        var ow              = '<?php echo $ow;?>';
        var sales_order     = '<?php echo $sales_order;?>';
        var row             = $(this).attr('row');

        $.post('<?php echo site_url()?>report/listOW/view_detail_items_panel',
            {kode_co:kode_co, ow:ow, sales_order:sales_order, row:row},
            function(html){
                setTimeout(function() {$('#' + e.currentTarget.id+' .panel-body').html(html);  });
            }   
        );
        
    })

 
</script>