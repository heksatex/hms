<form class="form-horizontal">
    <div class="form-group">
        <div class="col-xs-12 table-responsive">
            <table id="example2" class="table table-striped table-hover">
              <thead>
                <tr>
                  <th class="no">No</th>
                  <th>Production Order No</th>
                  <th>Create Date</th>
                  <th>Sales Order</th>
                  <th>Priority</th>
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
                "url": "<?php echo site_url('ppic/procurementorder/get_list_data_production_order_modal')?>",
                "type": "POST"
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