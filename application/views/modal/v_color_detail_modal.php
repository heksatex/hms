<form class="form-horizontal">
  <div class="form-group">
    <input type="hidden" name="txtso" id="txtso" value="<?php echo $sc;?>">
    <input type="hidden" name="txtco" id="txtco" value="<?php echo $co;?>">
    <div class="col-xs-12 table-responsive">
      <table id="example2" class="table table-striped table-hover rlstable">
        <thead>
          <tr>
            <th class="no">No</th>
            <th>Product</th>
            <th>Color</th>
            <th>Qty</th>
            <th>Uom</th>
            <th>Reff Notes</th>
            <th>All <input type="checkbox" id="checkAll"/></th>
          </tr>
        </thead>
        <tbody>
          
        </tbody>
      </table>
    </div>
  </div>
</form>
<script type="text/javascript">

    var table;
    $(document).ready(function() {
        //datatables
        table = $('#example2').DataTable({ 
            "processing": true, 
            "serverSide": true, 
            "order": [], 
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,             
            "ajax": {
                "url": "<?php echo site_url('ppic/colororder/list_color_detail_modal')?>",
                "type": "POST",
                "data":{"sales_order" : "<?php echo $sc;?>"}
            },
           
            "columnDefs": [
              {
               'targets':6,
               'data' : 7,
               'searchable':false,
               'orderable':false,
               'className': 'text-center',
               'render': function (data, type, full, meta){
                 return '<input type="checkbox" class="checkitem" value="' + data + '">';
                }
              },
              {
                "visible": false, "targets": 7 
              },
              { 
                "targets": [0], 
                "orderable": false, 
              },
            ]
        });
 
    });

  //checked All
  $('#checkAll').change(function(){
    $('.checkitem').prop("checked", $(this).prop("checked"))
  });

  //simpan color details ketika button simpan di klik
  $("#btn-tambah").unbind( "click" );
  $('#btn-tambah').click(function(){
    
      var message = 'Silahkan pilih data terlebih dahulu !';
      var myCheckboxes = new Array();
        $(".checkitem:checked").each(function() {
           myCheckboxes.push($(this).val());
        });
        countchek = myCheckboxes.length;
        
        if(myCheckboxes == ''){
          alert_modal_warning(message);

        }else{
          $('#btn-tambah').button('loading');
          $.ajax({
              type: "POST",
              url :'<?php echo base_url('ppic/colororder/save_color_detail_modal')?>',
              dataType: 'JSON',
              data: 'txtso='+$("#txtso").val()+'&txtco='+$("#txtco").val()+'&checkbox='+myCheckboxes+'&countchek='+countchek,
              success: function(data){
                if(data.sesi=='habis'){
                  //alert jika session habis
                  alert_modal_warning(data.message);
                  window.location.replace('../index');
                }else{
                 $("#color_detail").load(location.href + " #color_detail");
                 $('#tambah_data').modal('hide');
                 $('#btn-tambah').button('reset');
                 $("#foot").load(location.href + " #foot");
                  alert_notify(data.icon,data.message,data.type);
                }

              },error: function (xhr, ajaxOptions, thrownError) {
              alert(xhr.responseText);
             $('#btn-tambah').button('reset');
            }
          });

        }
      return false;
  });

</script>