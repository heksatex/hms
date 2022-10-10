<form class="form-horizontal">
  <div class="form-group">
    <div class="col-xs-12 table-responsive">
        <table id="example2" class="table table-striped table-hover rlstable">
          <thead>
            <tr>
                <th>No</th>
                <th>MG</th>
                <th>Tanggal dibuat</th>
                <th>Origin</th>
                <th>Mesin</th>
                <th>Nama Varian</th>
                <th>Status</th>
            </tr>
          </thead>
          <tbody>

          </tbody>
        </table>
    </div>
  </div>
</form>

<script type="text/javascript">

    var nama_warna = "<?php echo $head->nama_warna; ?>";

    $('.modal-title').text('History DTI List MG ('+nama_warna+')' );

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
                "url": "<?php echo site_url('lab/dti/get_data_history_dti')?>",
                "type": "POST",
                "data":{"id_warna" : "<?php echo $id_warna;?>"}
            },
            "columnDefs": [
              { 
                  "targets": [ 0 ], 
                  "orderable": false, 
              },
            ],
        });
    });

</script>