
<form class="form-horizontal" id="form_rm">
	<div class="col-md-6">
		<div class="form-group">
			<div class="col-md-12 col-xs-12">
        <div class="col-xs-4"><label>Kode</label></div>
        <div class="col-xs-8">
         	<input type="text" name="kode1" id="kode1" class="form-control input-sm"  readonly="readonly"/ value="<?php echo $kode?>" >
        </div>  
      </div>
      <div class="col-md-12 col-xs-12">
  		  <div class="col-xs-4"><label>Product</label></div>
  		  <div class="col-xs-8">
  		  	<input type="text" name="txtProduct" id="txtProduct" class="form-control input-sm"  />
  		  </div>  
  		</div>
    </div>
  </div>
  <div class="form-group">
  	<div class="col-md-6">
  	  <div class="col-md-12 col-xs-12">
  		  <div class="col-xs-4"><label>Qty</label></div>
  		  <div class="col-xs-8">
  		    <input type="text" name="txtQty" id="txtQty" class="form-control input-sm" />
  		  </div>  
  		</div>
  		<div class="col-md-12 col-xs-12">
  		  <div class="col-xs-4"><label>uom</label></div>
  		  <div class="col-xs-8">
  		   	<select name="txtUom" id="txtUom" class="form-control input-sm">
  		   		<option>m</option>
  		   		<option>kg</option>
  		   		<option>hours</option>
  		   	</select>
  		  </div>  
  		</div>
  	</div>
  </div>
</form>

<script type="text/javascript">
  $("#btn-tambah").unbind( "click" );
  $('#btn-tambah').click(function(){
      $('#btn-tambah').button('loading');
      $.ajax({
          dataType: "JSON",
          url : '<?php echo site_url('manufacturing/mO/save_rm_modal') ?>',
          type: "POST",
          data: {kode   	   : $('#kode').val(),
                 txtProduct  : $('#txtProduct').val(),
                 txtQty      : $('#txtQty').val(),
                 txtUom      : $('#txtUom').val() },
          success: function(data){
            if(data.status == "failed"){
                //jika ada form belum keiisi
                alert_modal_warning(data.message);
                $('#btn-tambah').button('reset');
             }else{
                $('#btn-tambah').button('reset');
                $("#table_rm").load(location.href + " #table_rm");
                $('#tambah_data').modal('hide');
                alert_notify(data.icon,data.message,data.type);
             }
          },
          error: function (jqXHR, textStatus, errorThrown)
          {
              alert('Error data');
              $('#btn-tambah').button('reset');
          }
      });
  });
 
</script>
