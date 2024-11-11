<form class="form-horizontal">
  <div class="form-group">
    
    <input type="hidden" name="kode_lokasi" id="kode_lokasi" >
    
    <div class="col-xs-12 table-responsive">
        <!-- <table id="example2" class="table table-striped table-hover rlstable " style="border-bottom:0px !important"> -->
        <table id="example2" class="table table-striped table-hover rlstable">
          <thead>
            <tr>
              <th class="no">No</th>
              <th>Kode Produk</th>
              <th>Nama Produk</th>
              <th>Corak Remark</th>
              <th>Remark Remark</th>
              <th>Lot</th>
              <th>Qty</th>
              <th>Qty2</th>
              <th>Qty Jual</th>
              <th>Qty2 Jual</th>
              <th>Grade</th>
              <th>Lbr.Jadi</th>
              <th>MKT</th>
              <th>Lokasi Fisik</th>
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
                "url": "<?php echo site_url('warehouse/joinlot/list_import_produk')?>",
                "type": "POST",
                // "data":{"kode_lokasi" : "GJD/Stock"}
            },
            "columnDefs": [
              {
                "targets" : 15,
                
                'checkboxes': {
                    'selectRow': true
                 },
                'createdCell':  function (td, cellData, rowData, row, col){
                   var rowId = rowData[14];
                   if(rowId.includes('SM') == true || rowId != ''){  
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
               var rowId = data[15];
                if (rowId.includes('SM') == true){     
                  $(row).find('input[type="checkbox"]').prop('disabled', true);
               }
            }
        });

        $("#btn-tambah").off("click").on("click",function(e) {
              var rows_selected = table.column(15).checkboxes.selected();
              var rows_selected_arr = new Array();
              var message     = 'Silahkan pilih data terlebih dahulu !';
              var kode_join   = "<?php echo $kode_join; ?>";
              var dept_id     = "<?php echo $dept_id; ?>";

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
                    url :'<?php echo base_url('warehouse/joinlot/save_details_import_produk_joinlot_modal')?>',
                    dataType: 'JSON',
                    data: {arr_data:rows_selected_arr, kode_join:kode_join, countchek:countchek, dept_id:dept_id},
                    success: function(data){
                      if(data.status == 'failed'){
                        alert_notify(data.icon,data.message,data.type,function(){});
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
                      $('#btn-tambah').button('reset');
                      if(xhr.status == 401){
                          var err = JSON.parse(xhr.responseText);
                          alert(err.message);
                      }else{
                          alert("Error Simpan Data!")
                      } 
                  }
                });
              } 

              return false;
          });
 

</script>