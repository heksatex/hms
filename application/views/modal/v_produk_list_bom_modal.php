<form class="form-horizontal">
  <div class="form-group">
    <div class="col-xs-12 table-responsive">
        <table id="example2" class="table table-striped table-hover rlstable">
          <thead>
            <tr>
                <th>No</th>
                <th>Kode BOM</th>
                <th>Nama BOM</th>
                <th>Kode Produk</th>
                <th>Nama Produk</th>
	            <th>qty</th>
	            <th>uom</th>
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
            //"stateSave": true,
            "dom": "<'row'<'col-sm-4'l><'col-sm-5'i><'col-sm-3'f>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-5'><'col-sm-7'p>>",

            "aLengthMenu": [[100, 500, 1000, -1], [100, 500, 1000, "All"]],
            "iDisplayLength": 100,
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
                "url": "<?php echo site_url('warehouse/produk/get_data_list_bom_produk')?>",
                "type": "POST",
                "data":{"kode_produk" : "<?php echo $kode_produk;?>"}
            },
            "columnDefs": [
              { 
                  "targets": [ 0 ], 
                  "orderable": false, 
              },
              {
                "targets" : 2,
                 render: function (data, type, full, meta) {
                        return "<div class='text-wrap width-300'>" + data + "</div>";
                }
              }
            ],
        });
    });

</script>