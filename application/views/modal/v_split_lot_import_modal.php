<form class="form-horizontal">
  <div class="form-group">
    
    <div class="col-xs-12 table-responsive">
        <table id="example2" class="table table-striped table-hover rlstable">
          <thead>
            <tr>
              <th class="no">No</th>
              <th>Kode Produk</th>
              <th>Product</th>
              <th>Lot</th>
              <th>Qty</th>
              <th>Qty2</th>
              <th>Grade</th>
              <th>Reff Notes</th>
              <th>Reserve Move</th>
            </tr>
          </thead>
          <tbody>

          </tbody>
        </table>
    </div>
  </div>
</form>
  
<script type="text/javascript">
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
                "data":{"departemen" : "<?php echo $departemen;?>"}
            },
            "columnDefs": [
              { 
                "targets": [0], 
                "orderable": false, 
              }
            ],
        });

  });
  
 

</script>