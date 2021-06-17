<form class="form-horizontal">
<div class="form-group">
<div class="col-xs-12 table-responsive">
    <table id="example2" class="table table-striped table-hover">
      <thead>
        <tr>
          <th class="no">No</th>
          <th>Name</th>
          <th>Buyer kode</th>
          <th>Invoice Addres</th>
          <th>Delivery Addres</th>
        </tr>
      </thead>
    </table>
</div>
</div>
</form>

<script type="text/javascript">
    var table;
    $(document).ready(function() {
 
        //datatables
        table = $('#example2').DataTable({ 
            "dom": "<'row'<'col-sm-4'l><'col-sm-5'i><'col-sm-3'f>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-5'><'col-sm-7'p>>",
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
                "url": "<?php echo site_url('sales/salescontract/get_data_customer')?>",
                "type": "POST"
            },
           
            "columnDefs": [
               { 
                "targets": [ 0 ], 
                "orderable": false, 
               },
               {
                    render: function (data, type, full, meta) {
                        return "<div class='text-wrap width-200'>" + data + "</div>";
                    },
                    "targets": [3,4]
                }
            ],

 
        });
 
    });
 
</script>