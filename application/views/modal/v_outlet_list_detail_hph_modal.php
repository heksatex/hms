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
              <th>Lbr.Jadi</th>
              <th>Lokasi</th>
              <th>Lokasi Fisik</th>
              <th>Nama User</th>
            </tr>
          </thead>
          <tbody>

          </tbody>
        </table>
        <small><b>*Jika terdapat baris yang berwarna <font color="red">MERAH</font> maka Product/Lot tersebut telah di proses SPLIT !!</b></small>
        <br>
        &nbsp;
    </div>
  </div>
</form>
<style>
  .max-width-5{
    max-width:5px
  }
</style>
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
                "className": "max-width-5"
              },
            ],
             "createdRow": function( row, data, dataIndex){
              if( data[16].includes('SPL') == true ){
                $(row).addClass('text-red');
              }
            }
        });
 
    });

</script>