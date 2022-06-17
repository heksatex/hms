<?php 
    if($get['type_obat'] == 'DYE'){
        $qty_capt = 'Qty (%)';
    }else{
        $qty_capt = 'Qty (g/L)';
    }
?>

<form class="form-horizontal">
	<div class="col-md-6">
		<div class="form-group">
            <div class="col-md-12 col-xs-12">
                <div class="col-xs-4"><label>Warna</label></div>
                <div class="col-xs-8">
                <input type="text" name="warna" id="warna" class="form-control input-sm"   value="<?php echo htmlentities($nama_warna) ?>" readonly="readonly"/>
                </div>  
            </div>
            <div class="col-md-12 col-xs-12">
                <div class="col-xs-4"><label>Kode Produk</label></div>
                <div class="col-xs-8">
                <input type="text" name="kode_produk" id="kode_produk" class="form-control input-sm"   value="<?php echo $get['kode_produk'] ?>" readonly="readonly"/>
                </div>  
            </div>
            <div class="col-md-12 col-xs-12">
            <div class="col-xs-4"><label>Product</label></div>
                <div class="col-xs-8">
                <input type="text" name="product" id="product" class="form-control input-sm"  value="<?php echo htmlentities($get['nama_produk']) ?>" readonly="readonly"/>
                    <input type="hidden" name="row_order" id="row_order" value="<?php echo $ro ?>">
                    <input type="hidden" name="id_warna" id="id_warna" value="<?php echo $id_warna ?>">
                </div>  
            </div>
            <div class="col-md-12 col-xs-12">
            <div class="col-xs-4"><label><?php echo $qty_capt?> </label></div>
            <div class="col-xs-4">
                <input type="text" name="qty" id="qty" class="form-control input-sm" value="<?php echo $get['qty'] ?>" onkeyup="validAngka(this)" />
            </div>  
            <div class="col-xs-4">
                <select class="form-control input-sm" name="uom" id="uom" >
                    <option value=""></option>
                    <?php foreach ($uom as $row) {
                            if($row->short == $get['uom']){
                                echo "<option selected value='".$row->short."'>".$row->short."</option>";
                            }else{
                                echo "<option value='".$row->short."'>".$row->short."</option>";
                            }
                            }
                    ?>
                </select>
            </div>
            </div>
		</div>
	</div>
	<div class="form-group">
	 <div class="col-md-6">
	    <div class="col-md-12 col-xs-12">
	      <div class="col-xs-4"><label>Reff Notes</label></div>
	        <div class="col-xs-8">
	          <textarea  type="text" class="form-control input-sm"  name="reff" id="reff"  ><?php echo $get['reff_note'] ?></textarea>
	        </div>  
	      </div>
		</div>
	  </div>
	</div>
</form>

<style type="text/css">
  .error{
    border:  1px solid red;
  } 
</style>

<script type="text/javascript">

    
    // validasi qty
    function validAngka(a){
        if(!/^[0-9.]+$/.test(a.value)){
        a.value = a.value.substring(0,a.value.length-1000);
        alert_notify('fa fa-warning','Maaf, Inputan Hanya Berupa Angka !','danger',function(){});
        }
    }

    //simpan data
    $("#btn-ubah").unbind( "click" );
    $('#btn-ubah').click(function(){

        var tipe_obat = '<?php echo $get['type_obat'] ?>';
        var id_warna  = '<?php echo $get['id_warna'] ?>';
        var row_order = '<?php echo $get['row_order'] ?>';
        var reff_note = $('#reff').val();
        var uom       = $('#uom').val();
        var qty       = $('#qty').val();
        var kode_produk  = $('#kode_produk').val();
        var product      = $('#product').val();
        var warna        =  $('#warna').val();

        $('#btn-ubah').button('loading');
        $.ajax({
            dataType: "JSON",
            url : '<?php echo site_url('lab/dti/simpan_dyestuff_aux_modal') ?>',
            type: "POST",
            data: { warna   	: warna,
                    txtKode     : kode_produk,
                    txtProduct  : product,
                    txtQty      : qty,
                    txtUom      : uom,
                    reff_note   : reff_note,
                    tipe_obat   : tipe_obat,
                    id_warna    : id_warna,
                    row_order   : row_order,
                    },
            success: function(data){
                if(data.sesi=='habis'){
                    //alert jika session habis
                    alert_modal_warning(data.message);
                    window.location.replace('../index');
                    $('#btn-ubah').button('reset');

                }else  if(data.status == "failed"){
                    //jika ada form belum keiisi
                    alert_modal_warning(data.message);
                    $('#btn-ubah').button('reset');
                }else{
                    $("#table_dyest").load(location.href + " #table_dyest");
                    $("#table_aux").load(location.href + " #table_aux");
                    $("#foot").load(location.href + " #foot");    
                    $("#status_head").load(location.href + " #status_head");
                    $('#edit_data').modal('hide');
                    alert_notify(data.icon,data.message,data.type,function(){});
                    $('#btn-ubah').button('reset');
                }
            },
            error: function (xhr, ajaxOptions, thrownError)
            {
                alert('Error data');
                alert(xhr.responseText);
                $('#btn-ubah').button('reset');
            }
        });
    });
  

</script>
