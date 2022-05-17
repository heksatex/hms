<form class="form-horizontal">
  <div class="form-group">
    <input type="hidden" name="txtso" id="txtso" value="<?php echo $sc;?>">
    <input type="hidden" name="txtco" id="txtco" value="<?php echo $co;?>">
    <div class="col-xs-12 table-responsive">
      <table id="example2" class="table table-striped table-hover rlstable">
        <thead>
          <tr>
            <th class="no">No</th>
            <th>OW</th>
            <th>Tgl.OW</th>
            <th>Product</th>
            <th>Color</th>
            <th>Qty</th>
            <th>Uom</th>
            <th>Lebar Jadi</th>
            <th>Handling</th>
            <th>Gramasi</th>
            <th>Reff Notes</th>
            <th width="50px"></th>
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
           "dom": "<'row'<'col-sm-4'l><'col-sm-5'i><'col-sm-3'f>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-5'><'col-sm-7'p>>",
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
               'targets':11,
               'data' : 11,
                'checkboxes': {
                  'selectRow': true
                },
                'createdCell':  function (td, cellData, rowData, row, col){
                   var rowId = rowData[11];
                },
              },
              {
                "visible": false,  "orderable": false, 
              },
              { 
                "targets": [0], 
                "orderable": false, 
              },
            ],
             "select": {
              'style': 'multi'
            },
            'rowCallback': function(row, data, dataIndex){
               // Get row ID
               var rowId = data[11];
               // If row ID is in the list of selected row IDs
            }
        });
 
    });

  /*
  //checked All
  $('#checkAll').change(function(){
    $('.checkitem').prop("checked", $(this).prop("checked"))
  });
  */

  //simpan color details ketika button simpan di klik
  $("#btn-tambah").unbind( "click" );
  $('#btn-tambah').click(function(){
    
      var myCheckboxes = table.column(11).checkboxes.selected();
      var myCheckboxes_arr = new Array();
      var message = 'Silahkan pilih data terlebih dahulu !';

        $.each(myCheckboxes, function(index, rowId){        
          myCheckboxes_arr.push(rowId);
        });

        countchek = myCheckboxes_arr.length;
        
        if(myCheckboxes_arr == ''){
          alert_modal_warning(message);

        }else{
          $('#btn-tambah').button('loading');
          $.ajax({
              type: "POST",
              url :'<?php echo base_url('ppic/colororder/save_color_detail_modal')?>',
              dataType: 'JSON',
              data    : { txtso : $("#txtso").val(),
                          txtco : $("#txtco").val(),
                          checkbox: myCheckboxes_arr,
                          countchek:countchek
                        },
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
                 $("#status_bar").load(location.href + " #status_bar");
                  alert_notify(data.icon,data.message,data.type,function(){});
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