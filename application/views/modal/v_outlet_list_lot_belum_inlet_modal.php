<form class="form-horizontal">
  <div class="form-group">
    <div class="col-xs-12 table-responsive">
        <table id="example2" class="table table-condesed table-hover rlstable  over" style="border-bottom:0px !important">
          <thead>
            <tr>
              <th class="no">No</th>
              <th>Kode Produk</th>
              <th>Nama Produk</th>
              <th>Lot</th>
              <th>Qty</th>
              <th>Qty2</th>
              <th>Qty Opname</th>
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
            "stateSave": true,
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
                "url": "<?php echo site_url('manufacturing/outlet/get_data_lot_belum_inlet_modal')?>",
                "type": "POST",
            },
           
            "columnDefs": [
              { 
                "targets": [0], 
                "orderable": false, 
              },
            ],
        });
 
    });

</script>