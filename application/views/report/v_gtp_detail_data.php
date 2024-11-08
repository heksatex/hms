<div class="row">
    <div class="form-group">
        <div class="col-md-6 col-xs-6">
            <div class="col-md-12 col-xs-12">
                <div class="col-xs-4"><label>Corak</label></div>
                <div class="col-xs-8">
                    <span>
                        <?= $corak ?>
                    </span>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-xs-6">
            <div class="col-md-12 col-xs-12">
                <div class="col-xs-4"><label>Sales</label></div>
                <div class="col-xs-8">
                    <span>
                        <?= $sales ?>
                    </span>
                </div>
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="col-md-6 col-xs-6">
            <div class="col-md-12 col-xs-12">
                <div class="col-xs-4"><label>Report Data</label></div>
                <div class="col-xs-8">
                    <span>
                        <?= $date ?>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12 table-responsive over">
        <table id="gtp_detail" class="table table-condesed table-hover rlstable over" width="100%">
            <thead>
                <tr>
                    <th></th>
                    <th>Kode Produk</th>
                    <th>Nama Produk</th>
                    <th>Lot</th>
                    <th>Grade</th>
                    <th>QTY</th>
                    <th>QTY 2</th>
                    <th>Qty Jual</th>
                    <th>Qty Jual 2</th>
                    <th>Lokasi Fisik</th>
                    <th>Lebar Jadi</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
<script>
    $(function () {
        const table = $("#gtp_detail").DataTable({
            "iDisplayLength": 25,
            "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
            "processing": true,
            "serverSide": true,
//            "order": [[8, 'desc']],
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "ajax": {
                "url": "<?= site_url('report/goodstopush/detail_data') ?>",
                "type": "POST",
                "data": function (d) {
                    d.corak = '<?= $corak ?>';
                    d.sales = "<?= $sales ?>";
                    d.lokasi = "<?= $lokasi ?>";
                    d.report_date = "<?= date('Y-m-d', strtotime($date)) ?>";
                }
            },
            "columnDefs": [
                {
                    "targets": [0,5,6,7,8],
                    "orderable": false
                }
            ]
        });
    });
</script>