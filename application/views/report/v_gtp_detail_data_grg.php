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
        <div class="col-md-6 col-xs-6">
            <div class="col-md-12 col-xs-12">
                <div class="col-xs-4"><label>Lokasi</label></div>
                <div class="col-xs-8">
                    <span>
                        <?= $lokasi ?>
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
                    <th>Lot</th>
                    <th>Grade</th>
                    <th>QTY</th>
                    <th>QTY 2</th>
                    <th>Lokasi Fisik</th>
                    <th>SC</th>
                    <th>Customer</th>
                    <th>Kategori</th>
                    <th>Umur</th>
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
            "ordering": false,
            "info": true,
            "autoWidth": false,
            "ajax": {
                "url": "<?= site_url('report/goodstopush/detail_data') ?>",
                "type": "POST",
                "data": function (d) {
                    d.corak = '<?= $corak ?>';
                    d.sales = "<?= $sales ?>";
                    d.lokasi = "<?= $lokasi ?>";
                    d.kategori = "<?= $kategori ?>";
                    d.report_date = "<?= date('Y-m-d', strtotime($date)) ?>";
                }
            },
            "columnDefs": [
                {
                    "targets": [0],
                    "orderable": false
                }
            ]
        });
    });
</script>