<div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
        <div class="box-footer box-comments">
            <div class="box-comment" id="log_load">
                <img src="<?php echo base_url('dist/img/ajax-loader.gif') ?>" class="user-image" alt="User Image">
            </div>
        </div>
    </div>
</div>
<script src="<?= base_url('plugins/jQuery/jquery-2.2.3.min.js') ?>"></script>
<script>
    j$ = jQuery.noConflict();
    j$(function () {
        async function loadLog() {
            await j$.ajax({
                url: "<?= base_url('service/get_log') ?>",
                type: "POST",
                data: {
                    kode: "<?= $kode ?? "" ?>",
                    mms: "<?= $mms ?? "" ?>",
                    uri_segmen: "<?= $this->uri->segment(4) ?>"
                },
                success: function (data) {
                    j$(".box-comments").append(data.data);
                },
                error: function (req, error) {
                    j$(".box-comments").append("");
                    alert_notify('fa fa-close', "Failed Load Log History", 'danger', function () {});
                },
                complete: function (jqXHR, textStatus) {
                    document.getElementById("log_load").remove();
                }

            });
        }
        loadLog();

    });
</script>
