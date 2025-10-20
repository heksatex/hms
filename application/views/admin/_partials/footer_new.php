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

    window.loadLog = async function() {
        // console.log("loadLog terpanggil...");

        await j$.ajax({
            url: "<?= base_url('service/get_log') ?>",
            type: "POST",
            dataType: "json",
            data: {
                kode: "<?= $kode ?? '' ?>",
                mms: "<?= $mms ?? '' ?>",
                uri_segmen: "<?= $this->uri->segment(4) ?>",
            },
            success: function(data) {
                // console.log('Response:', data);
                if (data?.data) {
                    j$(".box-comments").html(data.data);
                } else {
                    console.warn('data.data kosong');
                    j$(".box-comments").html("<div class='text-center text-muted'>Tidak ada log ditemukan</div>");
                }
            },
            error: function(xhr, error) {
                console.error(xhr.responseText);
                alert_notify('fa fa-close', "Failed Load Log History", 'danger');
            },
            complete: function() {
                j$("#log_load").remove();
            }
        });
    }

    j$(function() {
        loadLog();
    });
</script>