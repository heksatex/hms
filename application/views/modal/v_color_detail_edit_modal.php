
<form class="form-horizontal">
  <div class="form-group">
    <div class="col-md-6">
       <div class="col-md-12 col-xs-12">
            <div class="col-xs-4"><label>OW</label></div>
            <div class="col-xs-8">
              <input type="text" name="ow" id="ow" class="form-control input-sm"   value="<?php echo $get['ow'] ?>" readonly="readonly"/>
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
               	<input type="hidden" name="kode_co" id="kode_co" value="<?php echo $co ?>">
            </div>  
        </div>
        <div class="col-md-12 col-xs-12">
          <div class="col-xs-4"><label>Product Parent</label></div>
            <div class="col-xs-8">
              <input type="text" name="product_parent" id="product_parent" class="form-control input-sm"  value="<?php echo htmlentities($get['nama_parent']) ?>" readonly="readonly"/>
            </div>  
        </div>
        <div class="col-md-12 col-xs-12">
          <div class="col-xs-4"><label>Jenis Kain</label></div>
            <div class="col-xs-8">
              <input type="text" name="jenis_kain" id="jenis_kain" class="form-control input-sm"  value="<?php echo htmlentities($get['nama_jenis_kain']) ?>" readonly="readonly"/>
            </div>  
        </div>
        <div class="col-md-12 col-xs-12">
          <div class="col-xs-4"><label>Color</label></div>
          <div class="col-xs-8">
             	<input type="text" name="color" id="color" class="form-control input-sm"   value="<?php echo $get['nama_warna'] ?>" readonly="readonly"/>
          </div>  
        </div>
        <div class="col-md-12 col-xs-12">
          <div class="col-xs-4"><label>Qty</label></div>
          <div class="col-xs-6">
             	<input type="text" name="qty" id="qty" class="form-control input-sm" value="<?php echo $get['qty'] ?>" onkeyup="validAngka(this)" />
          </div>  
          <div class="col-xs-2">
            	<input type="text" name="uom" id="uom" class="form-control input-sm"  value="<?php echo $get['uom'] ?>" readonly="readonly"/>
          </div>
        </div>
        <div class="col-md-12 col-xs-12">
          <div class="col-xs-4"><label>Finishing</label></div>
          <div class="col-xs-8">
            <select class="form-control input-sm" name="handling" id="handling" >
              <option value="">Pilih Handling</option>
              <?php 
                foreach ($handling as $row) {
                  if($get['id_handling'] == $row->id){?>
                    <option value="<?php echo $row->id;?>" selected><?php echo $row->nama_handling;?></option>
              <?php 
                  }else{?>
                    <option value="<?php echo $row->id;?>"><?php echo $row->nama_handling;?></option>
              <?php
                  }
                }?>
            </select>
          </div>  
        </div>
        <div class="col-md-12 col-xs-12">
            <div class="col-xs-4"><label>Gramasi</label></div>
            <div class="col-xs-8">
                <input type="text" name="gramasi" id="gramasi" class="form-control input-sm"   value="<?php echo $get['gramasi'] ?>" >
            </div>  
        </div>
      
		</div>

    <div class="col-md-6">
     
      <div class="col-md-12 col-xs-12">
          <div class="col-xs-4"><label>Lebar Jadi</label></div>
          <div class="col-xs-5">
              <input type="text" name="lebar_jadi" id="lebar_jadi" class="form-control input-sm" value="<?php echo $get['lebar_jadi'] ?>" />
          </div>  
          <div class="col-xs-3">
              <select class="form-control input-sm" name="uom_lebar_jadi" id="uom_lebar_jadi" >
                  <option value=""></option>
                  <?php foreach ($uom as $row) {
                          if($row->short == $get['uom_lebar_jadi']){
                              echo "<option selected value='".$row->short."'>".$row->short."</option>";
                          }else{
                              echo "<option value='".$row->short."'>".$row->short."</option>";
                          }
                        }
                  ?>
              </select>
          </div>  
      </div>
      <div class="col-md-12 col-xs-12">
          <div class="col-xs-4"><label>Route</label></div>
          <div class="col-xs-8">
            <select class="form-control input-sm" name="route_co" id="route_co" >
              <option value="">Pilih Route</option>
              <?php 
                foreach ($route as $row) {
                  if($get['route_co'] == $row->kode){?>
                    <option value="<?php echo $row->kode;?>" selected><?php echo $row->nama;?></option>
              <?php 
                  }else{?>
                    <option value="<?php echo $row->kode;?>"><?php echo $row->nama;?></option>
              <?php
                  }
                }?>
            </select>
          </div>  
      </div>
      <div class="col-md-12 col-xs-12">
        <div class="col-xs-4"><label>Reff Notes PPIC</label></div>
            <div class="col-xs-8">
              <textarea  type="text" class="form-control input-sm ta set_textarea"  name="reff" id="reff"  ><?php echo $get['reff_notes'] ?></textarea>
            </div>  
      </div>
      <div class="col-md-12 col-xs-12">
          <div class="col-xs-4"><label>Reff Notes MKT</label></div>
          <div class="col-xs-8">
            <textarea  type="text" class="form-control input-sm ta set_textarea"  name="reff_mkt" id="reff_mkt" readonly="readonly"  ><?php echo $get['reff_notes_mkt'] ?></textarea>

          </div>  
      </div>
      <div class="col-md-12 col-xs-12">
          <div class="col-xs-4"><label>Status</label></div>
          <div class="col-xs-8">
            <input type="text" name="status" id="status" class="form-control input-sm"  value="<?php echo $get['status'] ?>" readonly="readonly"/>
          </div>  
      </div>

    </div><!-- col-md-6 -->
      
	</div><!-- form group-->
	
</form>

<style type="text/css">
  .error{
    border:  1px solid red;
  } 
  .set_textarea{
      resize: vertical;
  }
</style>

<script type="text/javascript">

  $(document).on('select2:open', () => {
      document.querySelector('.select2-search__field').focus();
  });

  <?php if($status=="generated" || $status=='cancel'){?>
      $("#btn-ubah").attr("disabled", true);
  <?php }else if($status =='draft'){?>
      $("#btn-ubah").attr("disabled", false);
  <?php }?>

  $('#route_co').select2({});
    
  // validasi qty
  function validAngka(a){
    if(!/^[0-9.]+$/.test(a.value)){
      a.value = a.value.substring(0,a.value.length-1000);
      alert_notify('fa fa-warning','Maaf, Inputan Hanya Berupa Angka !','danger',function(){});
    }
  }

  //untuk mengatur lebar textarea sesuai value yang ada
  $('.ta').on( 'change keyup keydown paste cut', 'textarea', function (){
    $(this).height(0).height(this.scrollHeight);
  }).find( 'textarea' ).change();

  /*
  // validasi lebar jadi
  function validationNumber(a){
    var valid = /^[0-9]+$/.test(a.value);
        val = a.value;
    if(!valid){
      alert_notify('fa fa-warning','Inputan hanya Berupa Bngka / Bilangan Bulat !','danger', function(){});
      a.value = val.substring(0, val.length - 1000);
    }
    return;
  }
  */
  
  $("#btn-ubah").off("click").on("click",function(e) {

      var qty      =  $('#qty').val();
      var reff     =  $('#reff').val();
      var route_co = $('#route_co').val();
      var handling = $('#handling').val();
      var gramasi  = $('#gramasi').val();
      var lebar_jadi     = $('#lebar_jadi').val();
      var uom_lebar_jadi = $('#uom_lebar_jadi').val();

      $('#qty').removeClass('error'); 
      $('#reff').removeClass('error'); 
      $('#route_co').removeClass('error'); 
      $('#handling').removeClass('error'); 
      $('#lebar_jadi').removeClass('error'); 
      $('#uom_lebar_jadi').removeClass('error'); 
      $('#gramasi').removeClass('error'); 

      if(qty == 0){
          alert('Qty tidak boleh kurang atau sama dengan 0 !');
          $('#qty').addClass('error'); 
      }else if(qty == '' ){
          alert('Qty tidak boleh kosong !');
          $('#qty').addClass('error'); 
      }else if(reff == '' ){
          alert('Reff Notes tidak boleh kosong !');
          $('#reff').addClass('error'); 
      }else if(route_co == ''){
          alert('Route Harus diisi !');
          $('#route_co').addClass('error'); 
      }else if(handling == ''){
          alert('Finishing Harus diisi !');
          $('#handling').addClass('error'); 
      }else if(gramasi == ''){
          alert('Gramasi Harus diisi !');
          $('#gramasi').addClass('error'); 
      }else if(lebar_jadi == ''){
          alert('Lebar Jadi Harus diisi !');
          $('#lebar_jadi').addClass('error'); 
      }else if(uom_lebar_jadi == ''){
          alert('Uom Lebar Jadi Harus diisi !');
          $('#uom_lebar_jadi').addClass('error'); 
      }else if(lebar_jadi == 0 || lebar_jadi <= 0){
          alert('Lebar Jadi tidak boleh kurang atau sama dengan 0 !');
          $('#lebar_jadi').addClass('error'); 
      }else{

    	    $('#btn-ubah').button('loading');
          $.ajax({
             dataType: "json",
             type: "POST",
             url : '<?php echo base_url('ppic/colororder/update_color_detail')?>',
             data: {qty        : qty, 
                    reff       : reff, 
                    row_order  : $('#row_order').val(),
                    route_co   : route_co,
                    handling   : handling,
                    gramasi    : gramasi,
                    lebar_jadi : lebar_jadi,
                    uom_lebar_jadi : uom_lebar_jadi,
                    kode_co    : $('#kode_co').val() },
             success: function(data){
              if(data.status == 'failed'){
                alert_modal_warning(data.message);
                $('#btn-ubah').button('reset');

              }else{
                $('#btn-ubah').button('reset');
                $('#edit_data').modal('hide');
                $("#status_bar").load(location.href + " #status_bar");
                $("#foot").load(location.href + " #foot");
                $("#tab_1").load(location.href + " #tab_1");    
                alert_notify(data.icon,data.message,data.type, function(){});
              }

             },error: function (xhr, ajaxOptions, thrownError) { 
                //alert(xhr.responseText);
                alert('Error Update data');
                $('#btn-ubah').button('reset');
              }
          });
      }

    });

</script>
