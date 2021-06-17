<form class="form-horizontal">
  <div class="form-group">
    <input type="hidden" name="kode_produk" id="kode_produk" value="<?php echo $kode_produk;?>">
    <input type="hidden" name="deptid" id="deptid" value="<?php echo $deptid;?>">
    <input type="hidden" name="move_id" id="move_id" value="<?php echo $move_id;?>">
    <input type="hidden" name="origin_prod" id="origin_prod" value="<?php echo $origin_prod;?>">

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
              <th width="50px"></th>
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
                "url": "<?php echo site_url('manufacturing/mO/tambah_data_details_quant_mo_modal')?>",
                "type": "POST",
                "data":{"kode_produk" : "<?php echo $kode_produk;?>", "move_id" : "<?php echo $move_id;?>" }

            },
           
            "columnDefs": [
              {
               'targets':6,
               'data' : 6,
               'checkboxes': {
                  'selectRow': true
                },
                'createdCell':  function (td, cellData, rowData, row, col){
                   var rowId = rowData[6];
                },
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
               var rowId = data[6];
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

  //simpan details ketika button simpan di klik
  //$("#btn-tambah").unbind( "click" );
  $("#btn-tambah").off("click").on("click",function(e) {
      var myCheckboxes = table.column(6).checkboxes.selected();
      var myCheckboxes_arr = new Array();
      var message = 'Silahkan pilih data terlebih dahulu !';

        $.each(myCheckboxes, function(index, rowId){        
          myCheckboxes_arr.push(rowId);
        });
       //alert(myCheckboxes);
        countchek = myCheckboxes.length;
        
        if(myCheckboxes_arr == ''){
          alert_modal_warning(message);

        }else{
          
          $('#btn-tambah').button('loading');
          $.ajax({
              type: "POST",
              url :'<?php echo base_url('manufacturing/mO/save_details_quant_mo_modal')?>',
              dataType: 'JSON',
              data    : {kode : $('#kode').val(), 
                         kode_produk : $('#kode_produk').val(),
                         nama_produk : $('#nama_produk').val(),
                         move_id     : $('#move_id').val(),
                         checkbox    : myCheckboxes_arr,
                         countchek   : countchek,
                         deptid      : $('#deptid').val(),
                         origin_prod : $('#origin_prod').val(),
                        
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
                  $("#tab_2").load(location.href + " #tab_2");
                  $("#status_bar").load(location.href + " #status_bar");
                  $("#foot").load(location.href + " #foot");
                  $('#tambah_data').modal('hide');
                  $('#btn-tambah').button('reset');
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