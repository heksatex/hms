<form class="form-horizontal">
  <div class="form-group">
    <input type="hidden" name="kode_produk" id="kode_produk" value="<?php echo $kode;?>">
    <input type="hidden" name="deptid" id="deptid" value="<?php echo $deptid;?>">
    <input type="hidden" name="nama_produk" id="nama_produk" value="<?php echo htmlentities($nama_produk);?>">
    <input type="hidden" name="move_id" id="move_id" value="<?php echo $move_id;?>">

    <div class="col-xs-12 table-responsive">
        <table id="example2" class="table table-striped table-hover rlstable">
          <thead>
            <tr>
              <th class="no">No</th>
              <th>Corak</th>
              <th>Lot</th>
              <th>Qty</th>
              <th>Berat</th>
              <th width="50px">All <input type="checkbox" id="checkAll"/></th>
              <th>hidden</th>
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
                "url": "<?php echo site_url('warehouse/pengirimanbarang/tambah_data_details_modal')?>",
                "type": "POST",
                "data":{"nama_produk" : "<?php echo $prod;?>", "move_id" : "<?php echo $move_id;?>"}

            },
           
            "columnDefs": [
              {
               'targets':5,
               'data' : 6,
               'searchable':false,
               'orderable':false,
               'className': 'text-center',
               'render': function (data, type, full, meta){
                 return '<input type="checkbox" class="checkitem" value="' + data + '">';
                }
              },
              {
                "visible": false, "targets": 6 
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
  

  //simpan details ketika button simpan di klik
  $("#btn-tambah").unbind( "click" );
  $('#btn-tambah').click(function(){
    
      var message = 'Silahkan pilih data terlebih dahulu !';
      var myCheckboxes = new Array();

        $(".checkitem:checked").each(function() {
           myCheckboxes.push($(this).val());
        });
       //alert(myCheckboxes);
        countchek = myCheckboxes.length;
        
        if(myCheckboxes == ''){
          alert_modal_warning(message);

        }else{
          
          $('#btn-tambah').button('loading');
          $.ajax({
              type: "POST",
              url :'<?php echo base_url('warehouse/pengirimanbarang/save_details_modal')?>',
              dataType: 'JSON',
              data: 'kode='+$("#kode").val()+'&kode_produk='+$("#kode_produk").val()+'&nama_produk='+$("#nama_produk").val()+'&move_id='+$("#move_id").val()+'&checkbox='+myCheckboxes+'&countchek='+countchek+'&deptid='+$("#deptid").val(),
              success: function(data){
                if(data.sesi=='habis'){
                  //alert jika session habis
                  alert_modal_warning(data.message);
                  window.location.replace('../index');
                  $('#btn-tambah').button('reset');

                }else if(data.status == 'failed'){
                  var pesan = "Lot "+data.lot+ " Sudah diinput !"       
                  alert_modal_warning(pesan);
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