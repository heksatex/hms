<!-- jQuery 2.2.3 -->
<script src="<?php echo base_url('plugins/jQuery/jquery-2.2.3.min.js') ?>"></script>
<!-- Input Mask-->
<script type="text/javascript" src="<?php echo base_url('dist/inputmask/jquery.inputmask.bundle.js') ?>"></script>
<!-- Bootstrap 3.3.6 -->
<script src="<?php echo base_url('bootstrap/js/bootstrap.min.js') ?>"></script>
<!-- SlimScroll -->
<script src="<?php echo base_url('plugins/slimScroll/jquery.slimscroll.min.js') ?>"></script>
<!-- FastClick -->
<script src="<?php echo base_url('plugins/fastclick/fastclick.js') ?>"></script>
<!-- AdminLTE App -->
<script src="<?php echo base_url('dist/js/app.min.js') ?>"></script>
<!-- Jquery DataTables  1.10.18-->
<script type="text/javascript" src="<?php echo base_url('plugins/DataTables-1.10.18/js/jquery.dataTables.min.js') ?>"></script>
<!-- Bootstrap Javascript -->
<script type="text/javascript" src="<?php echo base_url('plugins/DataTables-1.10.18/js/dataTables.bootstrap4.min.js') ?>"></script>
<!-- DaTables Checkbox -->
<script type="text/javascript" src="<?php echo base_url('plugins/datatables/checkbox/js/dataTables.checkboxes.min.js') ?>"></script>
<!-- DaTables Button -->
<script type="text/javascript" src="<?php echo base_url('plugins/datatables/button/button.dataTables.min.js') ?>"></script>
<!-- Data table row group -->
<!--script type="text/javascript" src="<?php echo base_url('plugins/DataTables-1.10.18/js/dataTables.rowGroup.min.js') ?>"></script-->
<!-- Date Time Picker Javascript -->
<script type="text/javascript" src="<?php echo base_url('plugins/datepicker/date/js/moment.js') ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/datepicker/date/js/bootstrap-datetimepicker.min.js') ?>"></script>
<!-- select2-->
<script type="text/javascript" src="<?php echo site_url('dist/select2/js/select2.min.js') ?>"></script>
<!-- bootbox -->
<script src="<?php echo base_url('dist/bootbox/bootbox.min.js') ?>"></script>
<!-- Block UI -->
<script src="<?php echo base_url('dist/blockui/jqueryblockUI.js') ?>"></script>
<!-- Notify -->
<script src="<?php echo base_url('dist/notify/bootstrap-notify.js') ?>"></script>
<script src="<?php echo base_url('dist/notify/bootstrap-notify.min.js') ?>"></script>
<!-- Tags Input -->
<script src="<?php echo base_url('dist/tags-input/bootstrap-tagsinput.js') ?>"></script>
<!--charJS-->
<script type="text/javascript" src="<?php echo site_url('dist/apexchart/js/apex.js') ?>"></script>

<script type="module" src="<?php echo site_url('dist/js/main_module.js') ?>"></script>

<script>
    $(document).ajaxError(function (event, xhr, options) {
        if (xhr.status === 401) {
            loginFunc('<?php echo base_url('login/aksi_login'); ?>');
//            $('#form-login').submit(false);
        }
        if (xhr.status === 403) {
            alert_modal_warning("Akses Tidak diijinkan.");
        }

    }).ajaxSetup({
        headers: {
            "_request": "json"
        }
    });
</script>
<script type="text/javascript">
    const setTglFormatDef = ((clss) => {
        $(clss).datetimepicker({
            format: 'YYYY-MM-DD'
        }).on('dp.show', function () {
            $(this).closest('.table-responsive').removeClass('table-responsive').addClass('temp');
        }).on('dp.hide', function () {
            $(this).closest('.temp').addClass('table-responsive').removeClass('temp')
        });
    });

    $(function () {

        $(".tgl-def-format").datetimepicker({
            format: 'YYYY-MM-DD'
        }).on('dp.show', function () {
            $(this).closest('.table-responsive').removeClass('table-responsive').addClass('temp');
        }).on('dp.hide', function () {
            $(this).closest('.temp').addClass('table-responsive').removeClass('temp')
        });

        $('#datetimepicker1').datetimepicker({
            format: 'YYYY-MM-DD HH:mm:ss',
            ignoreReadonly: true
        });
        $('#datetimepicker2').datetimepicker({
            format: 'YYYY-MM-DD HH:mm:ss',
            ignoreReadonly: true
        });
        $('#datetimepicker3').datetimepicker({
            format: 'YYYY-MM-DD HH:mm:ss',
            ignoreReadonly: true
        });
        $('#datetimepicker4').datetimepicker({
            format: 'YYYY-MM-DD HH:mm:ss',
            ignoreReadonly: true
        });

        $(".show_printer").off("click").unbind("click").on("click", function () {
            $("#modal_printer").modal({
                show: true,
                backdrop: 'static'
            });
            $(".view_body").html('<center><h5><img src="<?php echo base_url('dist/img/ajax-loader.gif') ?> "/><br>Please Wait...</h5></center>');
            $.post("<?= base_url('setting/printershare/data') ?>", {}, function (data) {
                $(".view_body").html(data.data);
            });
        });

    });
    //untuk alert notify
    function alert_notify(icon, message, type, callback) {
        $.notify({
            icon: icon,
            message: message,
        }, {
            type: type,
            allow_dismiss: true,
            newest_on_top: false,
            showProgressbar: false,
            placement: {
                from: "top",
                align: "right"
            },
            z_index: 2000,
            //delay: 500,
            timer: 500,
        });
        callback();
    }

    //untuk  loading saat proses klik button
    function please_wait(callback) {
        //$('#block-page').block({ 
        $.blockUI({
            message: '<h4><img src="<?php echo base_url('dist/img/ajax-loader.gif') ?> "/><br> Please wait...</h4>',
            //theme: false,
            baseZ: 2000,
            css: {
                border: 'none',
                padding: '0px',
                backgroundColor: '#000',
                '-webkit-border-radius': '10px',
                '-moz-border-radius': '10px',
                opacity: .5,
                color: '#fff',
                clear: "both",
            },
        });
        callback();
    }

    //unblock UI 
    function unblockUI(callback, timeout = 1000) {
        setTimeout($.unblockUI, timeout);
        callback();
    }

    //alert modal Warning
    function alert_modal_warning(message) {
        bootbox.alert({
            title: "<font color='red'><li class='fa fa-warning'></li></font> Warning !",
            message: message,
            // size: 'small',
            buttons: {
                ok: {
                    label: "ok",
                    className: 'btn-sm btn-primary',
                }
            }
        });
        //callback();
        return true;
    }
    //auto sizing input
    function calcHeight(value) {
        let numberOfLineBreaks = (value.match(/\n/g) || []).length;
        // min-height + lines x line-height + padding + border
        let newHeight = 20 + numberOfLineBreaks * 20 + 12 + 2;
        return newHeight;
    }

    let textarea = document.querySelector(".resize-ta");
    if (textarea !== null) {
        textarea.addEventListener("keyup", () => {
            textarea.style.height = calcHeight(textarea.value) + "px";
        });
    }

    $(".np").on("click", function () {
        var url = $(this).data("url");
        if (url === "") {
            return;
        }
        location.href = url;
    });

    $(document).ready(function () {

        // Buka semua treeview di awal
        $('.sidebar-menu .treeview').addClass('menu-open active');
        $('.sidebar-menu .treeview-menu').css('display', 'block');

        // Matikan behavior default AdminLTE yang close menu lainnya
        $('.sidebar-menu .treeview > a').off('click').on('click', function (e) {
            e.preventDefault();
            var parent = $(this).parent();
            var submenu = parent.children('.treeview-menu');

            // Toggle menu yang diklik saja
            if (parent.hasClass('menu-open')) {
                submenu.slideUp(200, function () {
                    parent.removeClass('menu-open active');
                });
            } else {
                submenu.slideDown(200, function () {
                    parent.addClass('menu-open active');
                });
            }
        });
    });

</script>

<div class="modal fade" id="modal_printer" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">
                    Printer Share
                </h4>
            </div>
            <div class="modal-body view_body">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>