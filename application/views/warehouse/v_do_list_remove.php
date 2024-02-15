
<div class="row">
    <div class="col-md-6 col-xs-12">
        <div class="form-group">
            <div class="col-md-12 col-xs-12">
                <div class="col-xs-4">
                    <label class="form-label required">Scan Barcode yang akan dikeluarkan</label>
                </div>
                <div class="col-xs-8">
                    <input type='text' name="r_search" id="r_search" class="form-control input-lg r_search"/>
                    <input type="hidden" name="r_tipe"  value="">
                    <input type="hidden" name="r_value" id="r_value" value="">
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12 table-responsive over">
        <table class="table table-condesed table-hover rlstable  over" width="100%" id="remove-table-item" >
            <thead>
                <tr>
                    <th class="style">Barcode</th>
                    <th class="style">#</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
<script>
    $(function () {
        $("#r_search").focus();
        const rTable = $("#remove-table-item").DataTable({
            "iDisplayLength": 10,
            "order": [],
            "paging": true,
            "lengthChange": false,
            "searching": true,
            "ordering": true,
            "info": true
        });
        $("#remove-table-item").on("click", '.remove-item', function () {
            let dt = $(this).attr("data-value");
            rTable.row($(this).parents("tr"))
                    .remove()
                    .draw(false);
            
            var index = listRemoveItem.indexOf(dt);
            if (index !== -1) {
                listRemoveItem.splice(index, 1);
            }
        });
        
        $("#r_search").on("keyup", async function (e) {
            let code = e.key;
            if (code === "Enter") {
                let checkinRmvItem = false;
                let data = $("#r_search").val();
                await searchArray(listRemoveItem, null, data).then(
                        rsp => {
                            if (rsp.length > 0) {
                                checkinRmvItem = true;
                            }
                        });
                $("#r_search").val("");
                if (data.trim("") === "" || checkinRmvItem) {
                    return;
                }
                listRemoveItem.push(data);
                rTable.row.add([
                    data,
                    "<button type='button' class='btn btn-danger btn-sm remove-item' data-value='" + data + "'><i class='fa fa-trash'></i></button>"
                ]).draw(false);
                $("#remove_item").val(JSON.stringify(listRemoveItem));
            }
        }
        );
        listRemoveItem.forEach((item, index) => {
            rTable.row.add([
                item,
                "<button type='button' class='btn btn-danger btn-sm remove-item' data-value='" + item + "'><i class='fa fa-trash'></i></button>"
            ]).draw(false);
        });
    });
</script>