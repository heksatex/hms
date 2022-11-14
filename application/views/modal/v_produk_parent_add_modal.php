<form class="form-horizontal" id="create_parent" name="create_parent">
    <div class="col-md-6">
    <div class="form-group">
      <div class="col-md-12 col-xs-12">
        <div class="col-xs-4"><label>Nama</label></div>
        <div class="col-xs-8">
          <input type="text" name="nama" id="nama" class="form-control input-sm"  >
        </div>  
      </div>
      </div>
    </div>
    </div>
    <div class="form-group">
    </div>
</form>

<script>
    $("#btn-tambah-parent").off("click").on("click",function(e) {
      $('#btn-tambah-parent').button('loading');
      
      var nama   = $('#nama').val();

      please_wait(function(){});
      $.ajax({
         type: "POST",
         dataType: "json",
         url :'<?php echo base_url('warehouse/produkparent/simpan')?>',
         beforeSend: function(e) {
            if(e && e.overrideMimeType) {
                e.overrideMimeType("application/json;charset=UTF-8");
            }
         },
         data: { nama : nama},
         success: function(data){
            if(data.sesi == "habis"){
                //alert jika session habis
                alert_modal_warning(data.message);
                window.location.replace('index');
            }else if(data.status == "failed"){
                //jika ada form belum keiisi
                unblockUI( function() {
                    setTimeout(function() { alert_notify(data.icon,data.message,data.type,function(){}); }, 1000);
                });
              document.getElementById(data.field).focus();
            }else{
                //jika berhasil disimpan/diubah
                unblockUI( function() {                
                    setTimeout(function() { alert_notify(data.icon,data.message,data.type,function(){}); }, 1000);
                });
                $('#tambah_data').modal('hide');
            }
            $('#btn-tambah-parent').button('reset');

          },error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.responseText);
            unblockUI( function(){});
            $('#btn-tambah-parent').button('reset');
          }
      });
    });
    // disable enter
    $("#nama").keydown(function(event){
        if(event.keyCode == 13) {
        event.preventDefault();
        return false;
        }
    });



</script>