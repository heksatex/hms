<form class="form-horizontal">
  <div class="form-group">
    
    <div class="col-xs-12 table-responsive">
        <table id="example2" class="table table-striped table-hover rlstable">
          <thead>
            <tr>
              <th class="no">No</th>
              <th>Kode Produk</th>
              <th>Nama Produk</th>
              <th>Lot</th>
              <th>Qty</th>
              <th>Qty2</th>
              <th>Lokasi</th>
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
                "url": "<?php echo site_url('ppic/reproses/list_import_produk')?>",
                "type": "POST",
            },
            "columnDefs": [
              {
                "targets" : 9,
                
                'checkboxes': {
                    'selectRow': true
                 },
                'createdCell':  function (td, cellData, rowData, row, col){
                   var rowId = rowData[8];
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
               var rowId = data[9];
                // If row ID is in the list of selected row IDs
                if (rowId.includes('SM') == true){     
                  $(row).find('input[type="checkbox"]').prop('disabled', true);
               }
            }
        });
 
        
        $("#btn-tambah").off("click").on("click",function(e) {
            var rows_selected = table.column(9).checkboxes.selected();
            var rows_selected_arr = new Array();
            var message = 'Silahkan pilih data terlebih dahulu !';
            var kode_reproses = "<?php echo $kode_reproses; ?>";

            // Iterate over all selected checkboxes'
            $.each(rows_selected, function(index, rowId){        
              rows_selected_arr.push(rowId);
            });

            countchek = rows_selected_arr.length;

            if(rows_selected_arr == ''){
              alert_modal_warning(message);
            }else{
              $('#btn-tambah').button('loading');
              $.ajax({
                  type: "POST",
                  url :'<?php echo base_url('ppic/reproses/save_details_import_produk_reproses_modal')?>',
                  dataType: 'JSON',
                  data: {arr_data : rows_selected_arr,
                        kode_reproses : kode_reproses,
                        countchek : countchek,
                        },
                  success: function(data){
                    if(data.sesi=='habis'){
                      //alert jika session habis
                      alert_modal_warning(data.message);
                      window.location.replace('../index');
                      $('#btn-tambah').button('reset');
                    }else if(data.status == 'failed'){
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
                      alert_notify(data.icon,data.message,data.type,function(){});
                    }

                  },error: function (xhr, ajaxOptions, thrownError) {
                      alert("Error Simpan Data");
                      $('#btn-tambah').button('reset');
                  }
              });
            } 

            return false;
        });
</script>