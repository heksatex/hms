<!DOCTYPE html>
<html>
  <head>
        <?php $this->load->view("produksi/hmi/head") ?>
        <link rel="stylesheet" href="<?=base_url()?>dist/css/hmi/page-hph.css">
        <link rel="stylesheet" href="<?=base_url()?>dist/css/hmi/header2.css">
        <link rel="stylesheet" type="text/css" href="<?php echo base_url('dist/select2/css/select2.min.css') ?>">
  </head>
  <body> 

    <?php $this->load->view('produksi/hmi/header2', $header); ?> 
    <!-- <div class="container-fluid hmi-body hph-page"> -->
        <div class="hph-production">
            <!-- =====================================================
                    HEADER MO
                ====================================================== -->
            <div class="hph-header">
                <!-- MO -->
                <div class="hph-mo-info">
                    <div class="hph-mo-number"> 
                        <?= isset($data_mo['kode']) ? $data_mo['kode'] : '-'; ?> 
                    </div>
                    <div class="hph-product-name"> 
                        <?= isset($data_mo['nama_produk'])? $data_mo['nama_produk'] : '-'; ?> 
                    </div>
                </div>
                <!-- SUMMARY -->
                 
                <div class="summary-box">
                    <label class="info-summary-label">MO Knitting</label>
                    <div class="info-summary-val"> <?= isset($mo_knitting) ? $mo_knitting : '-"'; ?> </div>
                </div>
                <div class="summary-box">
                    <label class="info-summary-label">MC Knitting</label>
                    <div class="info-summary-val"> <?= isset($mc_knitting) ? $mc_knitting: '-'; ?> </div>
                </div>
                <div class="summary-box">
                    <label class="info-summary-label">Jml Beam</label>
                    <div class="info-summary-val"> <?= isset($jml_beam) ? $jml_beam : '-'; ?> </div>
                </div>
                <div class="summary-box">
                    <label class="info-summary-label">Total Target</label>
                    <div class="info-summary-val"> <?= isset($total_target) ? number_format($total_target,2) : '0'; ?> <small>Mtr</small>
                    </div>
                </div>
                <div class="summary-box">
                    <label class="info-summary-label">Sudah Dibuat</label>
                    <div class="info-summary-val" id="sudah-dibuat"> <?= isset($sudah_dibuat) ? number_format($sudah_dibuat,2) : '0'; ?> <small>Mtr</small>
                    </div>
                </div>
                <div class="summary-box">
                    <label class="info-summary-label">Sisa Target</label>
                    <div class="info-summary-val" id="sisa-target"> 
                        <span class="<?= (isset($sisa_target) && $sisa_target < 0) ? 'text-danger' : ''; ?>">
                            <?= isset($sisa_target) ? number_format($sisa_target, 2) : '0'; ?>
                        </span>
                        <small>Mtr</small>
                    </div>
                </div>
            </div>

            <!-- =====================================================
                    BODY
            ====================================================== -->
            <div class="hph-body">
                <!-- =================================================
                        LEFT : LIST LOT
                ================================================== -->
                <aside class="hph-lot-panel">
                    <div class="panel-title">
                        <span class="panel-title-label">
                            <i class="fa fa-list"></i>
                            LIST LOT HPH TERSIMPAN
                        </span>

                        <div class="lot-panel-actions">

                            <span class="lot-count">
                                <?= isset($list_lot) ? count($list_lot) : 0; ?> LOT
                            </span>
                            <div class="lot-check-all">
                                <label>
                                    <input type="checkbox" id="check-all-lot">
                                    <span>Check All</span>
                                </label>
                            </div>
                            <button type="button" id="btn-print-lot" class="btn-print-lot" disabled>
                                <i class="fa fa-print"></i>
                                <span>PRINT</span>
                            </button>

                            <button type="button" id="btn-setting-printer" class="btn-setting-printer" title="Default Printer">
                                <i class="fa fa-cog"></i>
                            </button>

                        </div>
                    </div>
                    <div class="lot-list"> 
                        <div class="lot-empty"> 
                            <i class="fa fa-inbox"></i>
                            <span> Belum ada LOT tersimpan </span>
                        </div>
                        <?php 
                        // endif; 
                        ?> 
                    </div>
                </aside>
                <!-- =================================================
                        CENTER : INPUT PRODUKSI
                ================================================== -->
                <main class="hph-input-panel">
                    <div class="input-row">
                        <div class="row-number">1</div>
                        <div class="input-label"> BEAM - LOT </div>
                        <div class="input-content beam-content">
                        <div class="input-group-small">
                            <label>BEAM</label>
                            <!-- <input type="text" id="beam" name="beam" class="hph-input beam-input" value="L35"> -->
                                <select id="beam" name="beam" class="hph-input beam-input">
                                    <option value="">Pilih</option>
                                </select>
                        </div>
                        <div class="input-group-lot">
                            <label>LOT</label>
                            <input type="text" id="lot" name="lot" class="hph-input lot-input" value="">
                        </div>
                        </div>
                    </div>
                    <!-- GRADE -->
                    <div class="input-row">
                        <div class="row-number">2</div>
                        <div class="input-label"> GRADE </div>
                        <div class="input-content">
                            <select id="grade" name="grade" class="hph-input select-input">
                                <option value="">Pilih</option>
                                <?php 
                                    foreach($list_grade as $row) {
                                        echo '<option value="' . $row->nama_grade . '" ' . ($row->nama_grade === 'A' ? 'selected' : '') . '>' . $row->nama_grade . '</option>';
                                    }
                                ?>
                            </select>
                        </div>
                    </div>
                    <!-- QTY MTR -->
                    <div class="input-row input-qty">
                        <div class="row-number">3</div>
                        <div class="input-label"> QTY (<?= $data_mo['uom']; ?>) </div>
                        <div class="input-content number-content">
                            <span class="unit"> <?= $data_mo['uom']; ?> </span>
                            <strong class="display-value"> <?= number_format($data_mo['qty1_std'],2); ?> </strong>
                            <!-- <input type="number" id="qty" name="qty" class="hph-input number-input  " value="<?= ($data_mo['qty1_std']); ?>"> -->
                            <input type="text" id="qty" name="qty" class="hph-input number-input formatAngka" data-decimal="2" inputmode="decimal" autocomplete="off" placeholder="Qty" value="<?= ($data_mo['qty1_std']); ?>" >
                        </div>
                    </div>
                    <!-- QTY KG -->
                    <div class="input-row input-qty2">
                        <div class="row-number">4</div>
                        <div class="input-label"> QTY2 (<?= $data_mo['uom_2']; ?>) </div>
                        <div class="input-content number-content">
                        <span class="unit"> <?= $data_mo['uom_2']; ?> </span>
                        <strong class="display-value"> <?= number_format($data_mo['qty2_std'],2); ?> </strong>
                        <input type="text" id="qty2" name="qty2" class="hph-input number-input formatAngka" data-decimal="2" inputmode="decimal" autocomplete="off" placeholder="Qty2" value="<?= ($data_mo['qty2_std']); ?>" >
                        </div>
                    </div>
                    <!-- UA -->
                    <div class="input-row">
                        <div class="row-number">5</div>
                        <div class="input-label"> UA (mm) </div>
                        <div class="input-content number-content">
                        <span class="unit"> UA </span>
                        <input type="text" id="ua" name="ua" class="hph-input number-input formatAngka" data-decimal="0" inputmode="decimal" value="">
                        </div>
                    </div>
                    <!-- UI -->
                    <div class="input-row">
                        <div class="row-number">6</div>
                        <div class="input-label"> UI (mm) </div>
                        <div class="input-content number-content">
                        <span class="unit"> UI </span>
                        <input type="text" id="ui" name="ui" class="hph-input number-input formatAngka" data-decimal="0" inputmode="decimal"  value="">
                        </div>
                    </div>
                    <!-- WINDING -->
                    <div class="input-row">
                        <div class="row-number">7</div>
                        <div class="input-label"> WINDING </div>
                        <div class="input-content number-content">
                        <span class="unit"> W </span>
                        <input type="text" id="winding" name="winding" class="hph-input number-input formatAngka" data-decimal="0" inputmode="decimal"  value="" >
                        </div>
                    </div>
                    <!-- SAVE -->
                    <div class="save-wrapper">
                        <button type="button" id="btnChecklistLot" class="btn-hph-checklist"  data-checklist="0"> 
                            <i class="fa fa-square-o"></i>
                             PRINT LOT
                        </button>
                        <button type="button" id="btnSimpan" class="btn-hph-save"  data-loading-text="<i class='fa fa-spinner fa-spin '></i> menyimpan...">
                        <i class="fa fa-save"></i> SIMPAN </button>
                    </div>
                </main>
                <!-- =================================================
                        RIGHT : CACAT
                    ================================================== -->
                <aside class="hph-defect-panel">
                    <div class="defect-title">
                        <span>
                        <i class="fa fa-warning"></i> INPUTAN CACAT </span>
                        <span class="defect-count"> 0 ITEM </span>
                    </div>
                    <div class="defect-form">
                        <!-- MTR CACAT -->
                        <div class="defect-field">
                            <label> MTR CACAT </label>
                            <!-- <input type="number" id="mtrCacat" class="defect-input" value="0.00" step="0.01"> -->
                            <input type="text" id="mtrCacat" name="mtrCacat" class="defect-input formatAngka" data-decimal="0" inputmode="decimal"  value="">
                        </div>
                        <!-- KODE CACAT -->
                        <div class="defect-field">
                            <label> KODE CACAT </label>
                            <select id="kodeCacat" class="defect-input">
                                <?php foreach($list_cacat as $row){ echo '<option value='.$row['kode_cacat'].' data-nama='.$row['kode_cacat'].'>'.$row['kode_nama'].'</option>';}?>
                            </select>
                        </div>
                        <button type="button" id="btnTambahCacat" class="btn-add-defect">
                        <i class="fa fa-plus"></i> TAMBAH CACAT </button>
                    </div>
                    <!-- DEFECT TABLE -->
                    <div class="defect-table-wrapper">
                        <table class="defect-table">
                        <thead>
                            <tr>
                                <th>MTR</th>
                                <th>KODE</th>
                                <th>AKSI</th>
                            </tr>
                        </thead>
                        <tbody id="defectTableBody">
                            <tr class="defect-empty">
                                <td colspan='3' class="text-center" style="color:#9aa7ae;">Belum ada data cacat</td>
                            </tr>
                        </tbody>
                        </table>
                    </div>
                </aside>
            </div>
        </div>

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
                
    <!-- </div>  -->
    
    <?php $this->load->view('produksi/hmi/footer'); ?> 
    <?php $this->load->view("produksi/hmi/js") ?>
    <script type="text/javascript" src="<?php echo site_url('dist/select2/js/select2.min.js') ?>"></script>
    <script src="<?php echo base_url('dist/notify/bootstrap-notify.js') ?>"></script>
    <script src="<?php echo base_url('dist/notify/bootstrap-notify.min.js') ?>"></script>
    <!-- Block UI -->
    <script src="<?php echo base_url('dist/blockui/jqueryblockUI.js') ?>"></script>
    <script src="<?php echo base_url('dist/bootbox/bootbox.min.js') ?>"></script>
    <script src="<?php echo site_url('dist/js/formatAdded.js') ?>"></script>
    <script type="module" src="<?php echo site_url('dist/js/main_module.js') ?>"></script>
    

    <script>
            var LOT_CONFIG = {
                loadUrl: "<?= site_url('manufacturing/produksi/loadLot') ?>",
                kodeMO: "<?= ($data_mo['kode']) ?>",
                idDept: "<?= ($id_dept) ?>",
                loadUrlBeam : "<?= site_url('manufacturing/MO/getBeamSelect2')?>",
                lot_prefix: "<?= ($data_mo['lot_prefix']) ?>",
                loadUrlPrint :"<?= site_url('manufacturing/produksi/print_lot')?>",
            };
    </script>
    <script src="<?=base_url()?>dist/js/hmi/hmi_produksi_hph.js"></script>
    <script>
        
        $(document).ready(function () {

        $(".btn-setting-printer").off("click").unbind("click").on("click", function() {
            $("#modal_printer").modal({
                show: true,
                backdrop: 'static'
            });
            $(".view_body").html('<center><h5><img src="<?php echo base_url('dist/img/ajax-loader.gif') ?> "/><br>Please Wait...</h5></center>');
            $.post("<?= base_url('setting/printershare/data') ?>", {}, function(data) {
                $(".view_body").html(data.data);
            });
        });


        /* =====================================================
        TAMBAH CACAT
        ====================================================== */
        $('#btnTambahCacat').on('click', function () {

            var mtr  = $('#mtrCacat').val();
            var kode = $('#kodeCacat').val();

            if (!mtr || parseFloat(mtr) <= 0) {
                alert('MTR cacat belum diisi');
                $('#mtrCacat').focus();
                return;
            }

            var $option = $('#kodeCacat option:selected');

            // Untuk tampilan
            var kodeText = $option.text();

            // Untuk database
            var namaCacat = $option.data('nama');

            $('#defectTableBody .defect-empty').remove();

            var row = `
                <tr
                    data-mtr="${mtr}"
                    data-kode-cacat="${kode}"
                    data-nama-cacat="${namaCacat}"
                >
                    <td>
                        ${mtr}
                    </td>

                    <td>
                        ${kodeText}
                    </td>

                    <td>
                        <button type="button" class="btn-remove-defect">
                            <i class="fa fa-close"></i>
                        </button>
                    </td>
                </tr>
            `;

            $('#defectTableBody').append(row);

            updateDefectCount();

            $('#mtrCacat').val('');
            $('#mtrCacat').focus();
        });

        var data_cacat = [];

        function getDataCacat() {

            var data_cacat = [];

            $('#defectTableBody tr').not('.defect-empty').each(function () {

                var $row = $(this);

                // var mtr = parseFloat(
                //     $row.find('td:eq(0)').text().trim()
                // ) || 0;

                var mtr = unformatNumber($row.attr('data-mtr'));
                var kode_cacat = $row.attr('data-kode-cacat');
                var nama_cacat = $row.attr('data-nama-cacat');

                if (mtr > 0 && kode_cacat && nama_cacat) {

                    data_cacat.push({
                        mtr: mtr,
                        kode_cacat: kode_cacat,
                        nama_cacat: nama_cacat
                    });
                }
            });

            return data_cacat;
        }


        /* =====================================================
        HAPUS CACAT
        ====================================================== */

        $(document).on('click','.btn-remove-defect',function () {
            $(this).closest('tr').remove();
            updateDefectCount();
        });


        function updateDefectCount() {
            var count =$('#defectTableBody tr').length;
            $('.defect-count').text(count + ' ITEM');
        }



        /* =====================================================
        SIMPAN
        ====================================================== */
        
        const numberFormatter = new Intl.NumberFormat('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });

        function saveProduksi() {

            var deptid      = "<?= $id_dept ?>";
            var kode        = "<?= $kode_mo ?>";
            var origin_mo   = "<?= $data_mo['origin'] ?>";
            var uom         = "<?= $data_mo['uom']; ?>";
            var uom2        = "<?= $data_mo['uom_2']; ?>";
            var kode_produk = `<?= $data_mo['kode_produk']; ?>`;
            var nama_produk = `<?= $data_mo['nama_produk']; ?>`;
            
            var reff_note_concat = 'UA:'+unformatNumber($('#ua').val())+' UI:'+unformatNumber($('#ui').val())+' W:'+unformatNumber($('#winding').val());
            var data = {
                kode_produk : kode_produk,
                nama_produk : nama_produk,
                beam    : $('#beam').val(),
                lot     : $('#lot').val(),
                grade   : $('#grade').val(),
                qty     : unformatNumber($('#qty').val()),
                uom     : uom,
                qty2    : unformatNumber($('#qty2').val()),
                uom2    : uom2,
                reff_note : reff_note_concat
            };

            var printLot = $('#btnChecklistLot').hasClass('active');

            var data_cacat = getDataCacat();

            // console.log('DATA PRODUKSI:', data);
            // console.log("data cacat : ", data_cacat)


            $("#btnSimpan").button("loading");

            $.ajax({
                url: "<?= site_url('manufacturing/produksi/save_hph_hmi') ?>",
                type: "POST",
                dataType: "JSON",
                data: {
                    deptid: deptid,
                    origin_mo: origin_mo,
                    kode: kode,
                    kode_produk: kode_produk,
                    nama_produk : nama_produk,
                    data_fg: JSON.stringify([data]),
                    data_defect: JSON.stringify(data_cacat),
                    printLot:printLot
                },
                success: function(res) {
                    $("#btnSimpan").button("reset");
                    if (res.sesi == "habis") {
                        alert(res.message);
                        window.location.replace("../index");
                        return;
                    }
                    if (res.status == "failed") {
                        alert_notify(res.icon,res.message,res.type,function(){})
                        return;
                    }

                    // =========================
                    // UPDATE SUMMARY
                    // =========================

                    if (res.sisa_target !== undefined) {

                        let sisaTarget = parseFloat(res.sisa_target) || 0;

                        $('#sisa-target').html(
                            numberFormatter.format(sisaTarget) +
                            ' <small>Mtr</small>'
                        );

                        // Merah jika minus
                        if (sisaTarget < 0) {
                            $('#sisa-target').addClass('text-danger');
                        } else {
                            $('#sisa-target').removeClass('text-danger');
                        }
                    }


                    if (res.sudah_dibuat !== undefined) {

                        let sudahDibuat = parseFloat(res.sudah_dibuat) || 0;

                        $('#sudah-dibuat').html(
                            numberFormatter.format(sudahDibuat) +
                            ' <small>Mtr</small>'
                        );
                    }

                    var newLot = data.lot;

                    loadLot(newLot);
                    alert_notify(res.icon,res.message,res.type,function(){})

                    if (res.double == "yes") {
                        // alert("data pernah diinput");
                        alert_notify(res.icon,res.message2,"warning",function(){})
                    }
                    resetFormProduksi();
                    
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    $("#btnSimpan").button("reset");
                    
                    var pesanError = "Error Simpan Produksi";
                    
                    // Cek apakah response berupa JSON dan memiliki properti pesan
                    if (jqXHR.responseJSON && jqXHR.responseJSON.message) {
                        pesanError = jqXHR.responseJSON.message;
                        typeError = jqXHR.responseJSON.type;
                        iconError = jqXHR.responseJSON.icon;
                    } else if (jqXHR.responseText) {
                        pesanError = jqXHR.responseText;
                    }
                    
                    alert_notify(iconError,pesanError,typeError,function(){})

                    loadLot();
                }
            });

        }

        $("#btnSimpan").on("click", function(e){

            e.preventDefault();
            if(!validateSaveProduksi()){
                return;
            }
            saveProduksi();
        });


        function resetFormProduksi() {

            // Clear input
            $('#beam').val(null).trigger('change');
            $('#lot').val('');
            
            // Clear data cacat
            $('#defectTableBody').html(`
                <tr class="defect-empty">
                    <td colspan="3">
                        Belum ada data cacat
                    </td>
                </tr>
            `);

            updateDefectCount();

            // Grade TETAP
            $('#grade').val('A').trigger('change');

            // Qty kembali default
            $('#qty').val('<?= ($data_mo['qty1_std']); ?>');

            // Qty2 kembali default
            $('#qty2').val('<?= ($data_mo['qty2_std']); ?>');

            // Kalau ada input UA / UI / Winding yang ingin clear
            $('#ua').val('');
            $('#ui').val('');
            $('#winding').val('');

            // Fokus kembali ke beam
            $('#beam').focus();
        }
    

        function validateSaveProduksi() {

            var fields = [
                {
                    id: 'beam',
                    name: 'Beam'
                },
                {
                    id: 'lot',
                    name: 'Lot'
                },
                {
                    id: 'grade',
                    name: 'Grade'
                },
                {
                    id: 'qty',
                    name: 'Qty'
                },
                {
                    id: 'qty2',
                    name: 'Qty2'
                },
                {
                    id: 'ua',
                    name: 'UA'
                },
                {
                    id: 'ui',
                    name: 'UI'
                },
                {
                    id: 'winding',
                    name: 'Winding'
                },
                
            ];

            // =========================
            // VALIDASI INPUT FORM
            // =========================
            for (var i = 0; i < fields.length; i++) {

                var value = $('#' + fields[i].id).val();

                if (value === null || value === undefined || $.trim(value) === '') {

                    message = fields[i].name + ' wajib diisi.';
                    alert_notify("fa fa-warning",message,"danger",function(){})

                    $('#' + fields[i].id).focus();
                    return false;
                }
            }


            // =========================
            // VALIDASI QTY
            // =========================
            var qty = unformatNumber($('#qty').val());
            if (isNaN(qty) || parseFloat(qty) <= 0) {
                message = "Qty harus lebih besar dari 0.";
                alert_notify("fa fa-warning",message,"danger",function(){})
                $('#qty').focus();
                return false;
            }

            // =========================
            // VALIDASI QTY 2
            // =========================
            var qty2 = unformatNumber($('#qty2').val());
            if (isNaN(qty2) || parseFloat(qty2) <= 0) {
                message = "Qty 2  harus lebih besar dari 0.";
                alert_notify("fa fa-warning",message,"danger",function(){})
                $('#qty2').focus();

                return false;
            }


            // =========================
            // VALIDASI DATA DARI PHP
            // =========================

            var deptid      = "<?= $id_dept ?>";
            var kode        = "<?= $kode_mo ?>";
            var origin_mo   = "<?= $data_mo['origin'] ?>";
            var uom         = "<?= $data_mo['uom']; ?>";
            var uom2        = "<?= $data_mo['uom_2']; ?>";
            var kode_produk = `<?= $data_mo['kode_produk']; ?>`;
            var nama_produk = `<?= $data_mo['nama_produk']; ?>`;


            if ($.trim(deptid) === '') {
                alert_notify("fa fa-warning","Department tidak ditemukan.","danger",function(){})
                return false;
            }

            if ($.trim(kode) === '') {
                alert_notify("fa fa-warning","Kode MO tidak ditemukan.","danger",function(){})
                return false;
            }

            if ($.trim(origin_mo) === '') {
                alert_notify("fa fa-warning","Origin MO tidak ditemukan.","danger",function(){})
                return false;
            }

            if ($.trim(kode_produk) === '') {
                alert_notify("fa fa-warning","Kode produk tidak ditemukan.","danger",function(){})
                return false;
            }

            if ($.trim(nama_produk) === '') {
                alert_notify("fa fa-warning","Nama produk tidak ditemukan.","danger",function(){})
                return false;
            }

            if ($.trim(uom) === '') {
                alert_notify("fa fa-warning","UOM tidak ditemukan.","danger",function(){})
                return false;
            }

            if ($.trim(uom2) === '') {
                alert_notify("fa fa-warning","UOM2 tidak ditemukan.","danger",function(){})
                return false;
            }


            // =========================
            // SEMUA VALID
            // =========================
            return true;
        }

        
        
        
    });
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
            pleaseWaitTimeouts.push(setTimeout(function () {
                $(".blockUI h4").html('<img src="<?php echo base_url('dist/img/ajax-loader.gif') ?>"/><br> Proses masih berjalan,<br> mohon tunggu sebentar lagi...');
            }, 5000));

            // ubah pesan lagi setelah 40 detik
            pleaseWaitTimeouts.push(setTimeout(function () {
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
    </script>
  </body>
</html>