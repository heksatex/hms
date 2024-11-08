<?php
$no = 1;
foreach ($data as $key => $value) {
    ?>
    <tr>
        <td><?= $no ?></td>
        <td><?= $value->nama_sales_group ?></td>
        <td><?= $value->report_date ?></td>
        <td><a class="detail" href="#" data-sales="<?= $value->nama_sales_group ?>" data-date="<?= $value->report_date ?>" data-corak='<?= $value->corak ?>'><?= $value->corak ?></a></td>
        <td><?= $value->customer_name ?></td>
        <td><?= $value->jml_warna ?></td>
        <td><?= $value->lot ?></td>
        <td><?= $value->qty . " " . $value->uom ?></td>
        <td><?= $value->qty2 . " " . $value->uom2 ?></td>
        <td><?= $value->lebar_jadi ?></td>
    </tr>
    <?php
    $no++;
}
?>
<script>
    $(function () {
        $(".detail").on("click", function () {
            var data = $(this).data();
            $("#view_data").modal({
                show: true,
                backdrop: 'static'
            });
            $(".view_body").html('<center><h5><img src="<?php echo base_url('dist/img/ajax-loader.gif') ?> "/><br>Please Wait...</h5></center>');
            $('.modal-title').text('Details');
            $.post("<?= base_url('report/goodstopush/details/') ?>", data, function (datas) {
                setTimeout(function () {
                    $(".view_body").html(datas.content);
                }, 500);
            });
        });
    });
</script>