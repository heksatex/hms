<form class="form-horizontal">
  <div class="form-group">
      <div class="col-md-12 col-xs-12">
        <div class="col-xs-4"><label>Warna </label></div>
          <div class="col-xs-8">
            <input type="text" class="form-control input-sm " name="warna" id="warna"  value="<?php echo $nama_warna;?>" readonly="readonly" >
          </div>  
      </div>
      
      <div class="col-md-12 col-xs-12">
        <div class="col-xs-4"><label>Varian </label></div>
          <div class="col-xs-8">
            <select class="form-control input-sm" name="cmbVarian" id="cmbVarian" >
                    <option value="">-- Pilih Varian --</option>
                    <?php 
                      foreach ($list_varian as $var) {
                          echo "<option value='".$var->id."'>".$var->nama_varian."</option>";
                      }
                    ?>
            </select> 
          </div>  
      </div>   
  </div>   
</form>

<style>
   
</style>

<script>

  $('#cmbVarian').select2({});
    
  //klik button request
  $('#btn_request').click(function(){
    $("#btn_request").unbind( "click" );
    var deptid    = "<?php echo $deptid; ?>"
    var id_warna  = "<?php echo $id_warna;?>"
    var kode      = "<?php echo $kode;?>"
    var origin    = "<?php echo  $origin;?>"
    var varian    = $('#cmbVarian').val();

    var status = $('#status').val();
    if(status == 'done'){
      alert_modal_warning('Maaf, Proses Produksi telah Selesai !');
    }else if(status == 'cancel'){
      alert_modal_warning('Maaf, Proses Produksi telah dibatalkan !');
    }else if(varian == ''){
      alert_modal_warning('Varian Harus diisi !');
    }else{
      bootbox.dialog({
        message: "Apakah Anda yakin ingin Request Resep Obat ?",
        title  : "<i class='fa fa-gear'></i> Request Resep Obat !",
        buttons: {
          danger: {
              label    : "Yes ",
              className: "btn-primary btn-sm",
              callback : function() {
                please_wait(function(){});
                $.ajax({
                  dataType: "JSON",
                  url     : '<?php echo site_url('manufacturing/mO/request_obat') ?>',
                  type    : "POST",
                  data    : {id_warna:id_warna, kode:kode, deptid:deptid, origin:origin, varian:varian },
                  success: function(data){
                    if(data.sesi=='habis'){
                        //alert jika session habis
                        alert_modal_warning(data.message);
                        window.location.replace('../index');
                    }else if(data.status == 'failed'){
                        unblockUI( function() {});
                        alert_modal_warning(data.message);
                        $("#status_bar").load(location.href + " #status_bar");
                        $("#table_aux").load(location.href + " #table_aux");
                        $("#table_dyest").load(location.href + " #table_dyest");
                        $('#btn_request').button('reset');

                    }else{
                         //jika berhasil disimpan/diubah
                        unblockUI( function() {
                            setTimeout(function() { alert_notify(data.icon,data.message,data.type,function(){}); }, 1000);
                        });
                        $("#foot").load(location.href + " #foot");
                        $("#status_bar").load(location.href + " #status_bar");
                        $("#table_aux").load(location.href + " #table_aux");
                        $("#table_dyest").load(location.href + " #table_dyest");
                        //$(".highlight").prop("readonly", false);
                        $("#mo").load(location.href + " #mo");
                        $('#btn_request').button('reset');
	                 	    $('#tambah_data').modal('hide');
                    }

                  },error: function (xhr, ajaxOptions, thrownError){
                    alert(xhr.responseText);
                    setTimeout($.unblockUI, 1000); 
                    unblockUI( function(){});
                    $('#btn_request').button('reset');
                  }
                });
              }
          },
          success: {
                label    : "No",
                className: "btn-default  btn-sm",
                callback : function() {
                  $('#btn_request').button('reset');
                  $('.bootbox').modal('hide');
                }
          }
        }
      });
    }
  });

</script>