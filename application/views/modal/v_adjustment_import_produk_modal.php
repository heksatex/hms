<form class="form-horizontal">
  <div class="form-group">
    
    <input type="hidden" name="kode_lokasi" id="kode_lokasi" value="<?php echo $kode_lokasi;?>">
    
    <div class="col-xs-12 table-responsive">
        <table id="example2" class="table table-striped table-hover rlstable">
          <thead>
            <tr>
              <th class="no">No</th>
              <th>Product</th>
              <th>Lot</th>
              <th>Qty</th>
              <th>Qty2</th>
              <th>Reff Notes</th>
              <th>Reserve Move</th>
              <th></th>
            </tr>
          </thead>
          <tbody>

          </tbody>
        </table>
    </div>
  </div>
</form>
  
<script type="text/javascript">
       var tes = 'coba tes';
        //datatables
       var  table = $('#example2').DataTable({ 
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
                "url": "<?php echo site_url('warehouse/adjustment/list_import_produk')?>",
                "type": "POST",
                "data":{"kode_lokasi" : "<?php echo $kode_lokasi;?>"}
            },
            "columnDefs": [
              {
                "targets" : 7,
                
                'checkboxes': {
                    'selectRow': true
                 },
                'createdCell':  function (td, cellData, rowData, row, col){
                   var rowId = rowData[7];
                   if(rowId.includes('SM') == true){  
                      this.api().cell(td).checkboxes.disable();
                   }
                }, 
              },
              { 
                "targets": [0], 
                "orderable": false, 
              }
            ],
             "select": {
              'style': 'multi'
            },
            'rowCallback': function(row, data, dataIndex){
               // Get row ID
               var rowId = data[7];
                // If row ID is in the list of selected row IDs
                if (rowId.includes('SM') == true){     
                  $(row).find('input[type="checkbox"]').prop('disabled', true);
               }
            }
        });
 
  //checked All
  /*
  $('#checkAll').change(function(){
    $('.checkitem').prop("checked", $(this).prop("checked"))
  });
  */

  //$("#btn-tambah").unbind( "click" );
  //simpan details ketika button simpan di klik
  
  $("#btn-tambah").off("click").on("click",function(e) {
      var rows_selected = table.column(7).checkboxes.selected();
      var rows_selected_arr = new Array();
      var message = 'Silahkan pilih data terlebih dahulu !';
      var kode_adjustment = "<?php echo $kode_adjustment ?>";

      // Iterate over all selected checkboxes'
      $.each(rows_selected, function(index, rowId){        
        rows_selected_arr.push(rowId);
      });

      countchek =rows_selected_arr.length;

      if(rows_selected_arr == ''){
        alert_modal_warning(message);
      }else{
        $('#btn-tambah').button('loading');
        $.ajax({
            type: "POST",
            url :'<?php echo base_url('warehouse/adjustment/save_details_import_produk_adjustment_modal')?>',
            dataType: 'JSON',
            data: {arr_data : rows_selected_arr,
                   kode_adjustment : kode_adjustment,
                   countchek : countchek,
                  },
            success: function(data){
              if(data.sesi=='habis'){
                //alert jika session habis
                alert_modal_warning(data.message);
                window.location.replace('../index');
                $('#btn-tambah').button('reset');
              }else if(data.status == 'failed'){
                //var pesan = "Lot "+data.lot+ " Sudah diinput !"       
                alert_modal_warning(data.message);
                $('#btn-tambah').button('reset');
              }else{
                $("#tab_1").load(location.href + " #tab_1");
                $("#status_bar").load(location.href + " #status_bar");
                $("#foot").load(location.href + " #foot");
                $('#tambah_data').modal('hide');
                $('#btn-tambah').button('reset');
                if(data.msg2 == 'Yes'){
                  alert_modal_warning(data.message2);
                }
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