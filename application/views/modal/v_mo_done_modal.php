<style>
    .box-title2{
        display:inline-block;
        font-size : 15px;
        margin:0;
        line-height:1;
        font-weight :600;
    }
    .info-box2{
        display: block;
        min-height: 90px;
        background: #fff;
        width: 100%;
        box-shadow: 0 15px 15px rgba(32, 21, 21, 0.18);
        border-radius: 2px;
        margin-bottom: 15px;
    }
</style>
<form class="form-horizontal">
  <div class="form-group">
        <div class="col-md-4 col-xs-12">
          <div class="box ">
            <div class="box-header with-border">
              <h6 class="box-title2">Bahan Baku</h6>
              
            </div>
            <div class="box-body">

              <div class="col-md-12 col-sm-12 col-xs-12">
                  <div class="info-box2">
                      <span class="info-box-icon "><i class="fa fa-cubes"></i></span>
                      <div class="info-box-content">
                          <span class="info-box-text">Consume</span>
                          <span class="info-box-number"><?php echo number_format($rm_done->mtr,2);?>  <small>Mtr</small></span>
                          <span class="info-box-number"><?php echo number_format($rm_done->kg,2);?>  <small>Kg</small></span>
                      </div>
                  </div>
              </div>
              
            </div>
          </div>
        </div>

        <div class="col-md-4 col-xs-12">
          <div class="box ">
              <!-- <div class="info-box">
                <span class="info-box-icon "><i class="fa fa-cubes"></i></span>
              </div> -->
              <?php 
              if($show_btn == true AND ($status == 'ready')){?>
                      <h2><center><b>KG</b> Bahan Baku dan <br><b>KG</b> Barang Jadi <br><b><font color="green">SAMA</font></b> !!!</center></h2>
                      <button type="button" class="btn btn-success btn-sm btn-block" id="btn-done-mo" name="btn-done-mo">DONE !!</button>
              <?php }else if($show_btn == true AND ($status == 'done' or $status == 'cancel')){?>
                      <h2><center><b>KG</b> Bahan Baku dan <br><b>KG</b> Barang Jadi <b><br> <font color="green">SAMA</font></b> !!!</center></h2>
              <?php }else if($show_btn == false AND ($status == 'done' or $status == 'cancel')){?>
                      <h2><center><b>KG</b> Bahan Baku dan <br><b>KG</b> Barang Jadi <br><b><font color="red">TIDAK SAMA</font></b> !!!</center></h2>
              <?php }else if($show_btn == false ){?>
                      <h2><center><b>KG</b> Bahan Baku dan <br><b>KG</b> Barang Jadi <br><b><font color="red">TIDAK SAMA</font></b>!!!</center></h2>
              <?php } 
              ?>
          </div>
        </div>

        <div class="col-md-4 col-xs-12">
          <div class="box ">
            <div class="box-header with-border">
              <h6 class="box-title2">Barang Jadi</h6>
              
            </div>
            <div class="box-body">

              <div class="col-md-12 col-sm-12 col-xs-12">
                  <div class="info-box2">
                      <span class="info-box-icon bg-blue"><i class="fa fa-cube"></i></span>
                      <div class="info-box-content">
                          <span class="info-box-text">Produce</span>
                          <span class="info-box-number"><?php echo number_format($fg_prod->mtr,2);?>  <small>Mtr</small></span>
                          <span class="info-box-number"><?php echo number_format($fg_prod->kg,2);?>  <small>Kg</small></span>
                      </div>
                  </div>
                  <div class="info-box2">
                      <span class="info-box-icon bg-blue"><i class="fa fa-cube"></i></span>
                      <div class="info-box-content">
                          <span class="info-box-text">Waste</span>
                          <span class="info-box-number"><?php echo number_format($fg_waste->mtr,2);?>  <small>Mtr</small></span>
                          <span class="info-box-number"><?php echo number_format($fg_waste->kg,2);?>  <small>Kg</small></span>
                      </div>
                  </div>
                  <div class="info-box2">
                      <span class="info-box-icon bg-red"><i class="fa fa-cube"></i></span>
                      <div class="info-box-content">
                          <span class="info-box-text">Adjustment</span>
                          <span class="info-box-number"><?php echo number_format($fg_adj->mtr,2);?>  <small>Mtr</small></span>
                          <span class="info-box-number"><?php echo number_format($fg_adj->kg,2);?>  <small>Kg</small></span>
                      </div>
                  </div>
              </div>
            </div>
          </div>
        </div>
      
  </div>   
</form>


<script>

  //klik button done
  $("#btn-done-mo").unbind( "click" );
  $('#btn-done-mo').click(function(){

      $('#btn-done-mo').button('loading');
      var kode     = "<?php echo $kode_mo; ?>";
      var deptid   = "<?php echo $deptid; ?>";//parsing data id dept untuk log history    
      please_wait(function(){});

        $.ajax({
            type: "POST",
            dataType: "json",
            url :'<?php echo base_url('manufacturing/mO/mo_done')?>',
            beforeSend: function(e) {
                if(e && e.overrideMimeType) {
                    e.overrideMimeType("application/json;charset=UTF-8");
                }
            },
            data: {kode   : kode,              
                   deptid : deptid,   
            },success: function(data){
              if(data.sesi == "habis"){
                //alert jika session habis
                alert_modal_warning(data.message);
                window.location.replace('../index');
              }else if(data.status == "failed"){
                unblockUI( function() {
                  setTimeout(function() { alert_notify(data.icon,data.message,data.type,function(){}); }, 1000);
                });
                refresh_mo(); 
                $('#btn-done-mo').button('reset')         
              }else{
                unblockUI( function() {
                    setTimeout(function() { alert_notify(data.icon,data.message,data.type,function(){}); }, 1000);
                });
                refresh_mo();
                $('#btn-done-mo').button('reset');  
                $('#view_data').modal('hide');
              }

            },error: function (xhr, ajaxOptions, thrownError) { 
              alert(xhr.responseText);
              setTimeout($.unblockUI, 1000); 
              unblockUI( function(){});
              $('#btn-done-mo').button('reset');
            }
        });
    // }

  });


</script>