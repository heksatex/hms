<form class="form-horizontal">
  <div class="form-group">
    <div class="col-xs-12 table-responsive">
        <table id="example3" class="table table-condesed table-hover rlstable  over" style="border-bottom:0px !important">
          <thead>
            <tr>
              <th class="no">No</th>
              <th>Tanggal HPH</th>
              <th>Kode Produk</th>
              <th>Nama Produk</th>
              <th>Corak Remark</th>
              <th>Warna Remark</th>
              <th>Lot</th>
              <th>Grade</th>
              <th>Qty</th>
              <th>Qty2</th>
              <th>Qty Jual</th>
              <th>Qty2 Jual</th>
              <th>Lokasi</th>
              <th>Lokasi Fisik</th>
              <th>Nama User</th>
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
        table = $('#example3').DataTable({ 
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
                "url": "<?php echo site_url('manufacturing/outlet/get_data_detail_hph_modal')?>",
                "type": "POST",
                "data": {"id":<?php echo $id_inlet;?>}
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