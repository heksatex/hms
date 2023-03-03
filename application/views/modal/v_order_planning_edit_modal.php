<form class="form-horizontal">
  <div class="form-group">
    <div class="col-md-12">
          <div class="col-md-12 col-xs-12">
            <div class="col-xs-4"><label>Due Date</label></div>
              <div class="col-xs-8">
                <?php if($status == 'waiting_date'){?>
                <div class='input-group date ' id='due' >
                  <input type='text' class="form-control input-sm" name="due_date" id="due_date" readonly="readonly" value="<?php echo $row->due_date?>" />
                  <span class="input-group-addon ">
                    <span class="glyphicon glyphicon-calendar" ></span>
                  </span>
                </div> 
                <?php }else{?>
                  <input type='text' class="form-control input-sm" name="due_date" id="due_date" readonly="readonly" value="<?php echo $row->due_date?>" />
                <?php }?>
              </div>  
          </div>
          <div class="col-md-12 col-xs-12">
            <div class="col-xs-4"><label>Mesin</label></div>
              <div class="col-xs-8">
                <select type="text" class="form-control input-sm" name="mc" id="mc"  style="width:100% !important"> 
                  <option value="">-- Pilih No Mesin --</option>
                  <?php 
                    foreach ($mesin as $val) {
                      if($row->mc_id==$val->mc_id){
                        echo "<option selected value='".$val->mc_id."'>".$val->nama_mesin."</option>";
                      }else{
                        echo "<option value='".$val->mc_id."'>".$val->nama_mesin."</option>";
                      }
                    }
                  ?>
                </select>
              </div>  
          </div>
          &nbsp
          <div class="col-md-12 col-xs-12">
            <div class="col-xs-4"><label>Product</label></div>
              <div class="col-xs-8">
                <input type="text" name="product" id="product" class="form-control input-sm"  value="<?php echo htmlentities($row->nama_produk) ?>" readonly="readonly"/>
                <input type="hidden" name="row_order" id="row_order" value="<?php echo $row->row_order ?>">
                <input type="hidden" name="so" id="so" value="<?php echo $row->sales_order ?>">
              </div>  
          </div>
          <div class="col-md-12 col-xs-12">
            <div class="col-xs-4"><label>Description</label></div>
              <div class="col-xs-8">
                <input type="text" name="desc" id="desc" class="form-control input-sm"  value="<?php echo htmlentities($row->description) ?>" readonly="readonly"/>
              </div>  
          </div>
          <div class="col-md-12 col-xs-12">
            <div class="col-xs-4"><label>qty</label></div>
              <div class="col-xs-6">
                <input type="text" name="qty" id="qty" class="form-control input-sm"  value="<?php echo ($row->qty) ?>" readonly="readonly"/>
              </div> 
              <div class="col-xs-2">
                <input type="text" name="uom" id="uom" class="form-control input-sm"  value="<?php echo ($row->uom) ?>" readonly="readonly"/>
              </div>  
          </div>
        </div>

    </div>
  </div>
</form>

<script type="text/javascript">
  //set tgl due date
  var datenow=new Date();  
  datenow.setMonth(datenow.getMonth());
  $('#due').datetimepicker({
      defaultDate: datenow,
      format : 'YYYY-MM-DD HH:mm:ss',
      ignoreReadonly: true,
      widgetPositioning:{
                          horizontal: 'auto',
                          vertical: 'bottom',
                        }


  });
  $('#mc').select2({});

  $("#btn-ubah").unbind( "click" );
  $('#btn-ubah').click(function(){

    var kode_produk = '<?php echo $row->kode_produk;?>';

    if($('#due_date').val()==""){
      alert_modal_warning('Due Date tidak Boleh Kosong !');
    }else{
      $('#btn-ubah').button('loading');
      $.ajax({
         dataType: "json",
         type: "POST",
         url :'<?php echo base_url('ppic/orderplanning/update_due_date')?>',
         data: {sales_order: $('#so').val(), row_order:$('#row_order').val(), due_date:$('#due_date').val(), kode_produk:kode_produk, nama_produk:$('#product').val(), desc:$('#desc').val(), mc:$('#mc').val()},
         success: function(data){
            if(data.sesi == 'habis'){
              alert_modal_warning(data.message);
              window.location.replace('../index');
            }else{
              $("#tab_1").load(location.href + " #tab_1");
              $("#foot").load(location.href + " #foot");
              $('#edit_data').modal('hide');
              $('#btn-ubah').button('reset');
              alert_notify(data.icon,data.message,data.type,function(){});
            }
            $('#btn-ubah').button('reset');

         },error: function (xhr, ajaxOptions, thrownError) { 
            alert('Error Simpan');
            $('#btn-ubah').button('reset');
          }
      });
    }
  });


</script>