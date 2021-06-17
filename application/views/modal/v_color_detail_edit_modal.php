<form class="form-horizontal">
	<div class="col-md-6">
		<div class="form-group">
			<div class="col-md-12 col-xs-12">
          <div class="col-xs-4"><label>Product</label></div>
            <div class="col-xs-8">
              <input type="text" name="product" id="product" class="form-control input-sm"  value="<?php echo htmlentities($get['nama_produk']) ?>" readonly="readonly"/>
               	<input type="hidden" name="row_order" id="row_order" value="<?php echo $ro ?>">
               	<input type="hidden" name="kode_co" id="kode_co" value="<?php echo $co ?>">
              </div>  
          </div>
          <div class="col-md-12 col-xs-12">
            <div class="col-xs-4"><label>Color</label></div>
            <div class="col-xs-8">
             	<input type="text" name="color" id="color" class="form-control input-sm"   value="<?php echo $get['kode_warna'] ?>" readonly="readonly"/>
            </div>  
          </div>
          <div class="col-md-12 col-xs-12">
            <div class="col-xs-4"><label>Qty</label></div>
            <div class="col-xs-6">
             	<input type="text" name="qty" id="qty" class="form-control input-sm" value="<?php echo $get['qty'] ?>" />
            </div>  
            <div class="col-xs-2">
             	<input type="text" name="uom" id="uom" class="form-control input-sm"  value="<?php echo $get['uom'] ?>" readonly="readonly"/>
           	</div>
          </div>
		</div>
	</div>
	<div class="form-group">
		<div class="col-md-6">
			<div class="col-md-12 col-xs-12">
	      <div class="col-xs-4"><label>Reff Notes</label></div>
	        <div class="col-xs-8">
	          <textarea  type="text" class="form-control input-sm"  name="reff" id="reff"  ><?php echo $get['reff_notes'] ?></textarea>
	        </div>  
	      </div>
	      <div class="col-md-12 col-xs-12">
	        <div class="col-xs-4"><label>Status</label></div>
	        <div class="col-xs-8">
	          <input type="text" name="status" id="status" class="form-control input-sm"  value="<?php echo $get['status'] ?>" readonly="readonly"/>
	        </div>  
	      </div>
		</div>
	</div>
  <?php if($status=="done"){?>
    <script type="text/javascript">
      $("#btn-ubah").attr("disabled", true);
    </script>
    <?php $data1 ="";$data2 ="";$data3 ="";$data4 =""; ?>
    <table border="0">
      <tr>
        <th valign="top">Stock Move</th>
        <td>&nbsp</td>
        <td>
          <?php foreach ($sm as $val) {  $data1.=$val->move_id." <i class='fa fa-arrow-right'></i> ";}       
            $data1 = rtrim($data1," <i class='fa fa-arrow-right'></i> ");
            echo $data1;
          ?>
        </td>
      </tr>
      <tr>
        <th valign="top">MO</th>
        <td>&nbsp</td>
        <td>
          <?php foreach ($mo as $val) { $data2 .= $val->kode." <i class='fa fa-arrow-right'></i> ";  }
            $data2 = rtrim($data2," <i class='fa fa-arrow-right'></i> ");
            echo $data2;
          ?>
        </td>
      </tr>
       <tr>
        <th valign="top">Penerimaan</th>
        <td>&nbsp</td>
        <td>
          <?php foreach ($penerimaan as $val) {$data3 .= $val->kode." <i class='fa fa-arrow-right'></i> "; }
            $data3 = rtrim($data3," <i class='fa fa-arrow-right'></i> ");
            echo $data3;
          ?>
        </td>
      </tr>
       <tr>
        <th valign="top">Pengiriman</th>
        <td>&nbsp</td>
        <td>
          <?php foreach ($pengiriman as $val) { $data4 .= $val->kode." <i class='fa fa-arrow-right'></i> "; }
            $data4 = rtrim($data4," <i class='fa fa-arrow-right'></i> ");
            echo $data4;
          ?>
        </td>
      </tr>
    </table>
  <?php }?>
</form>


<script type="text/javascript">

	$('#btn-ubah').click(function(){
	    $('#btn-ubah').button('loading');
      $.ajax({
         //dataType: "json",
         type: "POST",
         url :'<?php echo base_url('ppic/colororder/update_color_detail')?>',
         data: {qty    : $('#qty').val(), reff    : $('#reff').val(), row_order    : $('#row_order').val(), kode_co    : $('#kode_co').val() },
         success: function(data){
            $("#color_detail").load(location.href + " #color_detail");
            $('#edit_data').modal('hide');
            $('#btn-ubah').button('reset');

         },error: function (xhr, ajaxOptions, thrownError) { 
            alert(xhr.responseText);
            $('#btn-ubah').button('reset');
          }
      });
    });

</script>
