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
    $(document).ajaxError(function(event, xhr, options) {
        if (xhr.status === 401) {
            loginFunc('<?php echo base_url('login/aksi_login'); ?>');
            //            $('#form-login').submit(false);
        }
        if (xhr.status === 403) {
            alert_modal_warning("Akses Tidak diijinkan.");
        }

    });
</script>
<script type="text/javascript">
    const setTglFormatDef = ((clss) => {
        $(clss).datetimepicker({
            format: 'YYYY-MM-DD'
        }).on('dp.show', function() {
            $(this).closest('.table-responsive').removeClass('table-responsive').addClass('temp');
        }).on('dp.hide', function() {
            $(this).closest('.temp').addClass('table-responsive').removeClass('temp')
        });
    });

    function formatNumber(n) {
        // format number 1000000 to 1,234,567
        return n.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }

    const formatCurrency = ((input, blur) => {
        // appends $ to value, validates decimal side
        // and puts cursor back in right position.

        // get input value
        var input_val = input.val();

        // don't validate empty input
        if (input_val === "") {
            return;
        }

        // original length
        var original_len = input_val.length;

        // initial caret position 
        var caret_pos = input.prop("selectionStart");

        // check for decimal
        if (input_val.indexOf(".") >= 0) {

            // get position of first decimal
            // this prevents multiple decimals from
            // being entered
            var decimal_pos = input_val.indexOf(".");

            // split number by decimal point
            var left_side = input_val.substring(0, decimal_pos);
            var right_side = input_val.substring(decimal_pos);

            // add commas to left side of number
            left_side = formatNumber(left_side);

            // validate right side
            right_side = formatNumber(right_side);

            // On blur make sure 2 numbers after decimal
            if (blur === "blur") {
                right_side += "00";
            }

            // Limit decimal to only 2 digits
            right_side = right_side.substring(0, 2);

            // join number by .
            input_val = left_side + "." + right_side;

        } else {
            // no decimal entered
            // add commas to number
            // remove all non-digits
            input_val = formatNumber(input_val);
            input_val = input_val;

            // final formatting
            if (blur === "blur") {
                input_val += ".00";
            }
        }

        // send updated string to input
        input.val(input_val);

        // put caret back in the right position
        var updated_len = input_val.length;
        caret_pos = updated_len - original_len + caret_pos;
        input[0].setSelectionRange(caret_pos, caret_pos);
    });

    $(function() {

        $(".tgl-def-format").datetimepicker({
            format: 'YYYY-MM-DD'
        }).on('dp.show', function() {
            $(this).closest('.table-responsive').removeClass('table-responsive').addClass('temp');
        }).on('dp.hide', function() {
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

        $(".show_printer").off("click").unbind("click").on("click", function() {
            $("#modal_printer").modal({
                show: true,
                backdrop: 'static'
            });
            $(".view_body").html('<center><h5><img src="<?php echo base_url('dist/img/ajax-loader.gif') ?> "/><br>Please Wait...</h5></center>');
            $.post("<?= base_url('setting/printershare/data') ?>", {}, function(data) {
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

    // simpan timeout global biar bisa dibersihkan
    var pleaseWaitTimeouts = [];

    function please_wait(callback) {
        // Hapus semua timeout lama
        pleaseWaitTimeouts.forEach(clearTimeout);
        pleaseWaitTimeouts = [];

        $.blockUI({
            message: '<h4><img src="<?php echo base_url('dist/img/ajax-loader.gif') ?>"/><br> Please wait...</h4>',
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

        // ubah pesan setelah 5 detik
        pleaseWaitTimeouts.push(setTimeout(function() {
            $(".blockUI h4").html('<img src="<?php echo base_url('dist/img/ajax-loader.gif') ?>"/><br> Proses masih berjalan,<br> mohon tunggu sebentar lagi...');
        }, 5000));

        // ubah pesan lagi setelah 40 detik
        pleaseWaitTimeouts.push(setTimeout(function() {
            $(".blockUI h4").html('<img src="<?php echo base_url('dist/img/ajax-loader.gif') ?>"/><br> Proses masih berjalan,<br> mungkin waktu yang pas untuk membuat kopi ☕');
        }, 40000));

        // jalankan callback, dan sediakan fungsi done()
        callback(function done() {
            pleaseWaitTimeouts.forEach(clearTimeout);
            pleaseWaitTimeouts = [];
            $.unblockUI();
        });
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

    $(".np").on("click", function() {
        var url = $(this).data("url");
        if (url === "") {
            return;
        }
        location.href = url;
    });
</script>

<script>
    $(function() {
        // Override fungsi tree bawaan AdminLTE
        $.AdminLTE.tree = function(menu) {
            var animationSpeed = $.AdminLTE.options.animationSpeed || 300;

            // Hapus event lama
            $(document).off('click', menu + ' li a');

            // Tambah event baru (multi expand)
            $(document).on('click', menu + ' li a', function(e) {
                var $this = $(this);
                var checkElement = $this.next();
                var parentLi = $this.parent('li');

                // Jika submenu sedang terbuka → tutup
                if (checkElement.is('.treeview-menu') && checkElement.is(':visible')) {
                    checkElement.slideUp(animationSpeed, function() {
                        checkElement.removeClass('menu-open');
                    });
                    parentLi.removeClass('menu-open');

                    // Panah kembali ke kiri
                    $this.find('.fa-angle-left').removeClass('rotate-down');
                }

                // Jika submenu tertutup → buka
                else if (checkElement.is('.treeview-menu') && !checkElement.is(':visible')) {
                    checkElement.slideDown(animationSpeed, function() {
                        checkElement.addClass('menu-open');
                    });
                    parentLi.addClass('menu-open');

                    // Panah mengarah ke bawah
                    $this.find('.fa-angle-left').addClass('rotate-down');
                }

                // Cegah aksi link jika itu treeview
                if (checkElement.is('.treeview-menu')) {
                    e.preventDefault();
                }
            });

            // Tambahkan CSS animasi rotasi (sekali saja)
            if (!$('#rotate-style').length) {
                $('<style id="rotate-style">')
                    .prop('type', 'text/css')
                    .html(`
            .fa-angle-left {
            transition: transform 0.3s ease;
            }
            .rotate-down {
            transform: rotate(-90deg);
            }
        `)
                    .appendTo('head');
            }

            // 🔹 Atur arah panah hanya untuk yang sedang terbuka (menu-open)
            $(menu + ' li.menu-open > a .fa-angle-left').addClass('rotate-down');
            $(menu + ' li:not(.menu-open) > a .fa-angle-left').removeClass('rotate-down');
        };

        // Jalankan langsung
        $.AdminLTE.tree('.sidebar');


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