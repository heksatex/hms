
<form class="form-horizontal">
  <div class="col-md-6">
    <div class="form-group">
      <div class="col-md-12 col-xs-12">
        <div class="col-xs-4"><label>Warna</label></div>
        <div class="col-xs-8">
          <input type="text" name="warna" id="warna" class="form-control input-sm"  readonly="readonly" value="<?php echo $warna?>">
          <input type="hidden" name="tipe" id="tipe" class="form-control input-sm"  readonly="readonly" value="<?php echo $tipe_obat?>">
          <input type="hidden" name="id_warna" id="id_warna" class="form-control input-sm"  readonly="readonly" value="<?php echo $id_warna?>">
        </div>  
      </div>
      <div class="col-md-12 col-xs-12">
        <div class="col-xs-4"><label>Product</label></div>
        <div class="col-xs-8">
          <select type="text" name="txtKode" id="txtKode" class="form-control input-sm select2"  ></select>
          <input type="hidden" name="txtProduct" id="txtProduct">
        </div>  
      </div>
      <div class="col-md-12 col-xs-12">
        <div class="col-xs-4"><label>Reff Notes </label></div>
          <div class="col-xs-8">
            <textarea type="text" class="form-control input-sm" name="reff_note" id="reff_note"  ></textarea>
          </div>                                    
      </div>
    </div>
  </div>
  <div class="form-group">
    <div class="col-md-6">
      <div class="col-md-12 col-xs-12">
        <div class="col-xs-4"><label>Qty (g/L)</label></div>
        <div class="col-xs-8">
          <input type="text" name="txtQty" id="txtQty" class="form-control input-sm" onkeyup="validAngka(this)"/>
        </div>  
      </div>
      <div class="col-md-12 col-xs-12">
        <div class="col-xs-4"><label>uom</label></div>
        <div class="col-xs-8">
          <input name="txtUom" id="txtUom" class="form-control input-sm" readonly="readonly">
        </div>  
      </div>
    </div>
  </div>
</form>

<script type="text/javascript">
  //select 2 product
  $('#txtKode').select2({
    ajax:{
          dataType: 'JSON',
          type : "POST",
          url : "<?php echo base_url();?>lab/dti/get_list_aux",
          //delay : 250,
          data : function(params){
            return{
              prod:params.term
            };
          }, 
          processResults:function(data){
            var results = [];

            $.each(data, function(index,item){
                results.push({
                    id:item.kode_produk,
                    text:'['+item.kode_produk+'] '+item.nama_produk
                });
            });
            return {
              results:results
            };
          },
          error: function (xhr, ajaxOptions, thrownError){
            alert('Error data');
            alert(xhr.responseText);
          }
    }
  });

 $("#txtKode").change(function(){
    $.ajax({
          dataType: "JSON",
          url : '<?php echo site_url('lab/dti/get_data_aux') ?>',
          type: "POST",
          data: {kode_produk: $('#txtKode').val() },
          success: function(data){
            $('#txtUom').val(data.uom);
            $('#txtProduct').val(data.nama_produk);
          },
          error: function (xhr, ajaxOptions, thrownError){
            alert('Error data');
            alert(xhr.responseText);
          }
    });
 });

  //validasi qty
  function validAngka(a)
  {
    if(!/^[0-9.]+$/.test(a.value))
    {
      a.value = a.value.substring(0,a.value.length-1000);
    }
  }

  //simpan data
  $("#btn-tambah").unbind( "click" );
  $('#btn-tambah').click(function(){
      $('#btn-tambah').button('loading');
      $.ajax({
          dataType: "JSON",
          url : '<?php echo site_url('lab/dti/simpan_dyestuff_aux_modal') ?>',
          type: "POST",
          data: {warna       : $('#warna').val(),
                 txtKode     : $('#txtKode').val(),
                 txtProduct  : $('#txtProduct').val(),
                 txtQty      : $('#txtQty').val(),
                 txtUom      : $('#txtUom').val(),
                 reff_note   : $('#reff_note').val(),
                 tipe_obat   : $('#tipe').val(),
                 id_warna    : $('#id_warna').val(),
                  },
          success: function(data){
            if(data.status == "failed"){
                //jika ada form belum keiisi
                alert_modal_warning(data.message);
                $('#btn-tambah').button('reset');
             }else{
                $('#btn-tambah').button('reset');
                $("#table_dyest").load(location.href + " #table_dyest");
                $("#table_aux").load(location.href + " #table_aux");
                $("#foot").load(location.href + " #foot"); 
                $("#status_head").load(location.href + " #status_head");
                $('#tambah_data').modal('hide');
                alert_notify(data.icon,data.message,data.type,function(){});
             }
          },
          error: function (xhr, ajaxOptions, thrownError)
          {
            alert('Error data');
            alert(xhr.responseText);
            $('#btn-tambah').button('reset');
          }
      });
  });
 
</script>
