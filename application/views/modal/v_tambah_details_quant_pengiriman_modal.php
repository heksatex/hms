<form class="form-horizontal">
  <div class="form-group">
    <input type="hidden" name="kode_produk" id="kode_produk" value="<?php echo $kode;?>">
    <input type="hidden" name="deptid" id="deptid" value="<?php echo $deptid;?>">
    <input type="hidden" name="nama_produk" id="nama_produk" value="<?php echo htmlentities($nama_produk);?>">
    <input type="hidden" name="move_id" id="move_id" value="<?php echo $move_id;?>">
    <input type="hidden" name="origin" id="origin" value="<?php echo $origin;?>">
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
              <th>Reff Note</th>
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
                "url": "<?php echo site_url('warehouse/pengirimanbarang/tambah_data_details_quant_modal')?>",
                "type": "POST",
                "data":{"kode" :$('#kode').val() ,"kode_produk" : "<?php echo $kode;?>", "move_id" : "<?php echo $move_id;?>", "origin": "<?php echo $origin;?>", "deptid": "<?php echo $deptid;?>", }
            },
           
            "columnDefs": [
              {
               'targets':6,
               'data' : 6,
               /*
               'searchable':false,
               'orderable':false,
               'className': 'text-center',
               */
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
  $("#btn-tambah").off("click").on("click",function(e) {
      var myCheckboxes = table.column(6).checkboxes.selected();
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
              url :'<?php echo base_url('warehouse/pengirimanbarang/save_details_quant_modal')?>',
              dataType: 'JSON',
              data    : {kode : $('#kode').val(), 
                         kode_produk : $('#kode_produk').val(),
                         nama_produk : $('#nama_produk').val(),
                         move_id     : $('#move_id').val(),
                         checkbox    : myCheckboxes_arr,
                         countchek   : countchek,
                         deptid      : $('#deptid').val(),
                         origin_prod : $('#origin_prod').val(),
                         origin      : $('#origin').val()
                        },
              success: function(data){
                if(data.sesi=='habis'){
                  //alert jika session habis
                  alert_modal_warning(data.message);
                  window.location.replace('../index');
                  $('#btn-tambah').button('reset');

                }else if(data.status == 'kosong'){                     
                  alert_modal_warning(data.message);
                  $('#btn-tambah').button('reset');

                }else{
                  $("#table_prod").load(location.href + " #table_prod");
                  $("#table_items").load(location.href + " #table_items");
                  $("#status_bar").load(location.href + " #status_bar");
                  $("#tab_3").load(location.href + " #tab_3");
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