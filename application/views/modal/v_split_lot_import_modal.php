<form class="form-horizontal">
  <div class="form-group">
    
    <div class="col-xs-12 table-responsive">
        <table id="example2" class="table table-striped table-hover rlstable">
          <thead>
            <tr>
              <th class="no">No</th>
              <th>Kode Produk</th>
              <th>Nama Produk</th>
              <?php if($departemen == 'GJD'){?>
                <th>Corak Remark</th>
                <th>Warna Remark</th>
               <?php } ?>
              <th>Lot</th>
              <th>Qty</th>
              <th>Qty2</th>
              <?php if($departemen == 'GJD'){?>
                <th>Qty Jual</th>
                <th>Qty2 Jual</th>
                <?php } ?>
              <th>Grade</th>
              <th>Lokasi Fisik</th>
              <th>Reff Notes</th>
              <th>Reserve Move</th>
              <?php if($departemen == 'GJD'){?>
                <th>Marketing</th>
              <?php } ?>
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
  
  var dept = "<?php echo $departemen;?>";
  var kolom = (dept=='GJD')? 15 : 10;
  var id_kolom = (dept=='GJD')? 13 : 9;
  $(document).ready( function () {
        var  table = $('#example2').DataTable({ 
            "processing": true, 
            "serverSide": true, 
            "order": [], 
            "select" : true,
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
             
            "ajax": {
                "url": "<?php echo site_url('warehouse/splitlot/list_import_produk')?>",
                "type": "POST",
                "data":{"departemen" : dept}
            },
            "columnDefs": [
              {
                "targets" : kolom,
                "orderable": false, 
                "createdCell": function (td, cellData, rowData, row, col) {
                   var rowId = rowData[id_kolom];
                  //  if(rowId.includes('SM') == true){  
                   if(rowId.length > 0){  
                      $(td).html('');
                   }

                   if(dept == 'GJD'){
                      var rowId2 = rowData[11];
                      if(rowId.length > 0 || rowId2 == 'XPD'){
                          $(td).html('');
                      }
                   }

                }, 
              },
              { 
                "targets": [0], 
                "orderable": false, 
              }
            ],
            
        });

  });
  
 

</script>