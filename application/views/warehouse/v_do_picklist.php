<div class="col-md-12">
    <div class="tab-content over"><br>
        <div class="tab-pane active" id="tab_1">
            <div class="col-md-12 table-responsive over">
                <table class="table table-condesed table-hover rlstable  over" width="100%" id="item_picklist" >
                    <thead>                          
                        <tr>
                            <th class="style" width="10px">No</th>
                            <th class="style" >Picklist</th>
                            <th class="style" >Jenis</th>
                            <th class="style" >Marketing</th>
                            <th class="style" >Customer</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
    $(function () {
        
        const table = $("#item_picklist").DataTable({
            "iDisplayLength": 25,
            "processing": true,
            "serverSide": true,
            "order": [],
            "paging": true,
            "lengthChange": false,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "ajax": {
                "url": "<?= base_url('warehouse/deliveryorder/list_picklist') ?>",
                "type": "POST"
//            "data": function (d) {
//                d.filter = "";
//            },
//            "dataSrc": function (data) {
//                if (data.data.length < 1) {
//                    $(".header-status").hide();
//                }
//                return data.data;
//            }
            },
        "columnDefs": [
            {
                "targets": [0,3,4],
                "orderable": false
            }
        ]
        });
    });

</script>