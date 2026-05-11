<style>
    /* Dropdown */
    .select2-results__option {
        white-space: nowrap !important;
        /* overflow-x: auto !important; */
        text-overflow: clip !important;
        /* -webkit-overflow-scrolling: touch; */
    }

    /* Selected element */
    .select2-selection__rendered {
        white-space: nowrap !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
    }

    /* Buat dropdown melebar sesuai isi */
    .select2-container--open .select2-dropdown {
        width: auto !important;
        /* biarkan melebar */
        min-width: 200px;
        /* kasih batas minimal (opsional) */
        max-width: none !important;
        /* hilangkan batas maksimum */
    }

    .select2-results {
        max-height: 250px !important;
        overflow-y: auto !important;
    }


    /* Agar tampilan dropdown tidak melebar terlalu jauh di HP */
    @media screen and (max-width: 768px) {
        .select2-container--open .select2-dropdown {
            width: 80% !important;
            max-width: 100% !important;
            /* overflow-x: auto !important; */
        }
    }

    .select2-container {
        width: 100% !important;
    }
</style>

<div class="row">
    <form class="form-horizontal" id="form-add-kas" name="form-add-kas">
        <div class="row">
            <div class="form-group">
                <div class="col-md-6">
                    <div class="col-md-12 col-xs-12">
                        <div class="col-xs-4"><label class="form-label">Kas/Bank</label></div>
                        <input type="hidden" name="no_pelunasan" id="no_pelunasan" value="<?php echo $no_pelunasan; ?>">
                        <div class="col-xs-8 col-md-8">
                            <select class="form-control input-sm" name="pilih_kas" id="pilih_kas">
                                <option value="">Pilih</option>
                                <option value="kas">Kas</option>
                                <option value="bank">Bank</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12 col-xs-12">
                        <div class="col-xs-4"><label class="form-label">No ACC (Kredit)</label></div>
                        <div class="col-xs-8 col-md-8">
                            <select class="form-control input-sm select2 no_acc" name="no_acc" style="width: 100%">
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12 col-xs-12">
                        <div class="col-xs-4"><label class="form-label">Untuk Transaksi</label></div>
                        <div class="col-xs-8 col-md-8">
                            <input type="text" name="transaksi" id="transaksi" class="form-control input-sm" />
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="col-md-12 col-xs-12">
                        <div class="col-xs-4"><label class="form-label">Tanggal</label></div>
                        <div class="col-xs-8 col-md-8">
                            <input type="text" name="tanggal" id="tanggal" class="form-control input-sm" value="<?= date("Y-m-d") ?>" readonly />
                        </div>
                    </div>
                    <div class="col-md-12 col-xs-12" id="div_jenis_transaksi">
                        <div class="col-xs-4"><label class="form-label">Jenis Transaksi</label></div>
                        <div class="col-xs-8 col-md-8">
                            <select class="form-control input-sm" name="jenis_transaksi" style="width: 100%">
                                <option value=""></option>
                                <option value="transfer" selected>Transfer</option>
                                <option value="inkaso">Inkaso</option>
                                <option value="kliring">Kliring</option>
                                <option value="lain-lain">Lain - Lain</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <!-- Custom Tabs -->
                <div class="">
                    <ul class="nav nav-tabs ">
                        <li class="active"><a href="#tab_1" data-toggle="tab">Detail</a></li>
                    </ul>
                    <div class="tab-content over"><br>
                        <div class="tab-pane active" id="tab_1">
                            <div class="col-md-12 table-responsive over">
                                <table class="table table-condesed table-hover rlstable" width="100%" id="kas-bank" style="min-width: 100%">
                                    <thead>
                                        <th class="style bb no">No.</th>
                                        <th class="style bb">Uraian</th>
                                        <th class="style bb">No.Acc(Debet)</th>
                                        <th class="style bb" style="text-align: right;">Kurs</th>
                                        <th class="style bb">Curr</th>
                                        <th class="style bb text-right">Nominal</th>
                                        <th class="style bb text-center">PPh23</th>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td>
                                                <button type="button" class="btn btn-success btn-sm btn-add-item"><i class="fa fa-plus-circle"></i></button>
                                            </td>
                                            <td colspan="4" class="text-right text-bold total-nominal">

                                            </td>
                                            <td class="text-bold">
                                                <input type="text" pattern="^\d{1,3}(,\d{3})*(\.\d+)?$" data-type='currency' name="total_nominal" id="total_nominal" class="form-control input-sm text-right" value="0" readonly />
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <template class="kasbank-tmplt-add">
        <tr class="list-new">
            <td>
                <div class="input-group">
                    <span class="input-group-addon nourut:nourut" style="border: none;"></span>
                    <button type="button" class="btn btn-danger btn-sm btn-rmv-item"><i class="fa fa-close"></i></button>
                </div>
            </td>
            <td>
                <input type="text" name="uraian[]" class="form-control uraian:nourut input-sm" value="" />
            </td>
            <td>
                <select class="form-control input-sm select2 select2-coa coa_:nourut" style="width:100%" name="kode_coa[]" required>
                    <option value=""></option>
                </select>
            </td>
            <td>
                <input type="text" name="kurs[]" value="1.00" class="form-control input-sm kurs:nourut text-right" required />
            </td>
            <td>
                <select class="form-control input-sm select2 select2-curr curr_:nourut" style="width:100%" name="curr[]" required>
                    <?php foreach ($curr as $key => $values) {
                        $selected = ($values->id == 1) ? "selected" : "";
                    ?>
                        <option value="<?= $values->id ?>" <?php echo $selected ?>><?= $values->currency ?></option>
                    <?php }
                    ?>
                </select>
            </td>
            <td>
                <input type="text" name="nominal[]" pattern="^\d{1,3}(,\d{3})*(\.\d+)?$" data-type='currency' class="form-control input-sm nominal text-right nominal:nourut formatAngka" data-decimal="2" value="0" required />
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-warning btn-sm btn-hitung-pph23" title="Hitung PPH23">
                    <i class="fa fa-calculator"></i>
                </button>
            </td>
        </tr>
    </template>
</div>
<script>
    $(document).ready(function() {

        $("#tambah_data .modal-dialog .modal-content .modal-footer").html('<button type="button" id="btn-tambah-kas" class="btn btn-primary btn-sm"> Tambahkan</button> <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Tutup</button>');

        // $(document).on('focus', '.select2', function(e) {
        //     if (e.originalEvent) {
        //         var s2element = $(this).siblings('select');
        //         s2element.select2('open');

        //         // Set focus back to select2 element on closing.
        //         s2element.on('select2:closing', function(e) {
        //             s2element.select2('focus');
        //         });
        //     }
        // });

        // change kas/bank/giro
        $('#pilih_kas').on('change', function() {

            $val = $(this).val();
            if ($val === 'kas') {
                // Sembunyikan secara halus
                $('#div_jenis_transaksi').fadeOut(200);
                // Kosongkan nilai agar tidak terkirim ke database secara tidak sengaja
                $('select[name="jenis_transaksi"]').val('').trigger('change');
            } else {
                // Tampilkan kembali jika pilih Bank
                $('#div_jenis_transaksi').fadeIn(200);
            }
            setCoaItem('no_acc', $val);
        });

        // Ganti kode Anda menjadi seperti ini:
        $(document).off('click', '.btn-add-item').on('click', '.btn-add-item', function(e) {
            e.preventDefault();
            tambahBaris();
        });

        // Hitung total setiap kali nominal diubah
        $(document).on('input', '.nominal', function() {
            hitungTotal();
        });

        $(document).on("keyup change", ".nominal", function() {
            hitungTotal();
        });

        function hitungTotal() {
            let total = 0;
            $('.nominal').each(function() {
                // Hapus koma sebelum konversi ke float (asumsi format ribuan menggunakan koma)
                let val = $(this).val().replace(/,/g, '');
                total += parseFloat(val) || 0;
            });

            // Set ke input total nominal (pastikan format angka kembali jika perlu)
            $('#total_nominal').val(total.toLocaleString('en-US', {
                minimumFractionDigits: 2
            }));
        }

        // 1. Trigger saat modal dibuka
        loadDataInvoice();
        // $('#tambah_data').on('shown.bs.modal', function() {
        // });

        // Tambahan: Bersihkan saat modal ditutup agar tidak "nyangkut"
        $('.kas_bank').on('hidden.bs.modal', function() {
            $('#kas-bank tbody').empty();
            $('#form-add-kas')[0].reset();
            // Jika ada select2 di header, reset juga:
            $('.no_acc').val(null).trigger('change');
        });

        // 2. Fungsi Load Data


        // 3. Fungsi Tambah Baris
        function tambahBaris(data = {}) {
            var template = $('.kasbank-tmplt-add').html();
            var $row = $(template);

            // 1. Isi field jika ada data (untuk load otomatis), jika tidak biarkan default
            $row.find('input[name="uraian[]"]').val(data.uraian || '');
            $row.find('input[name="kurs[]"]').val(data.kurs || '1.00');
            $row.find('select[name="curr[]"]').val(data.currency_id || '1');
            $row.find('input[name="nominal[]"]').val(data.nominal || 0);

            // Jika ada COA dari database (saat load data invoice)
            if (data.coa_hutang_dagang) {
                var newOption = new Option(data.coa_hutang_dagang, data.coa_hutang_dagang, true, true);
                $row.find('select[name="kode_coa[]"]').append(newOption).trigger('change');
            }

            // 2. Masukkan ke dalam tabel
            $('#kas-bank tbody').append($row);

            // 3. Inisialisasi plugin khusus untuk baris baru ini
            // Agar Select2 COA bisa jalan (AJAX)
            setCoaItem();
            // Agar Select2 Currency bisa jalan
            setCurr();
            // Agar input nominal otomatis format ribuan
            if (typeof bindFormatAngka === "function") {
                bindFormatAngka($row[0]);
            }

            // 4. Update nomor urut tabel
            updateNomorUrut();
        }


        function loadDataInvoice() {
            $.ajax({
                url: "<?= base_url('finance/pelunasanhutang/loadDataInvoice') ?>",
                type: 'POST',
                dataType: 'JSON',
                data: {
                    no_pelunasan: `<?php echo $no_pelunasan; ?>`
                },
                beforeSend: function() {
                    $('#kas-bank tbody').empty();
                },
                success: function(response) {

                    if (response.header) {
                        $('#pilih_kas').val(response.header.pilih_kas).trigger('change');
                        $('#transaksi').val(response.header.untuk_transaksi);
                        $('select[name="jenis_transaksi"]').val(response.header.jenis_transaksi);

                        // Khusus Select2 No ACC (Kredit)
                        if (response.header.kode_acc) {
                            var accOption = new Option(response.header.kode_acc + ' - ' + response.header.nama_coa, response.header.kode_acc, true, true);
                            $('.no_acc').append(accOption).trigger('change');
                        }
                    }

                    // Langsung cek key 'record' sesuai respon yang Anda kirim
                    if (response.record && response.record.length > 0) {
                        $('#kas-bank tbody').empty();

                        $.each(response.record, function(i, item) {
                            tambahBaris(item);
                        });
                        hitungTotal();
                    } else {
                        // console.warn("Respon diterima tapi record kosong atau format salah.");
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error load data: ", error);
                }
            });
        }

        // 4. Handle Hapus Baris
        $(document).off('click', '.btn-rmv-item').on('click', '.btn-rmv-item', function(e) {
            e.preventDefault();
            $(this).closest('tr').remove();
            updateNomorUrut();
            hitungTotal(); // Pastikan hitung ulang total jika baris dihapus
        });

        // 5. Fungsi Update Nomor Urut
        function updateNomorUrut() {
            $('#kas-bank tbody tr').each(function(index) {
                // Gunakan .nourut:nourut sesuai class di span template Anda
                $(this).find('.input-group-addon.nourut\\:nourut').text(index + 1);
            });
        }

        $(document).on('click', '.btn-hitung-pph23', function() {

            let persen = 2;

            let $row = $(this).closest('tr');
            let $nominalInput = $row.find('.nominal');

            let nominal = parseFloat(
                $nominalInput.val().replace(/,/g, '')
            ) || 0;

            if (nominal <= 0) {
                alert('Nominal harus lebih dari 0');
                return;
            }

            let hasil;

            if (!$nominalInput.data('original')) {
                // simpan nilai awal
                $nominalInput.data('original', nominal);

                // hitung PPh23
                hasil = nominal * (persen / 100);
            } else {
                // kembalikan ke nilai awal
                hasil = $nominalInput.data('original');

                // hapus flag
                $nominalInput.removeData('original');
            }

            // set ke input
            $nominalInput.val(
                hasil.toLocaleString('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                })
            );

            hitungTotal();
        });


        const setCoaItem = ((klas = "select2-coa", jentrans = "") => {
            $("." + klas).select2({
                placeholder: "Pilih Coa",
                dropdownParent: $('#tambah_data'),
                allowClear: true,
                ajax: {
                    dataType: 'JSON',
                    type: "get",
                    url: "<?php echo base_url(); ?>finance/pelunasanhutang/get_coa",
                    delay: 250,
                    data: function(params) {
                        return {
                            search: params.term,
                            jentrans: jentrans
                        };
                    },
                    processResults: function(data) {
                        var results = [];
                        $.each(data.data, function(index, item) {
                            if (jentrans != "") {
                                results.push({
                                    id: item.kode_coa,
                                    text: item.kode_coa + ' - ' + item.nama,
                                });
                            } else {
                                results.push({
                                    text: item.nama,
                                    children: [{
                                        id: item.kode_coa,
                                        text: item.kode_coa
                                    }]
                                });
                            }
                        });
                        return {
                            results: results
                        };
                    }
                }
            });
        });

        const setCurr = (() => {
            $(".select2-curr").select2({
                placeholder: "Pilih",
                allowClear: true,

            });
        });

        $(document).off('click', '#btn-tambah-kas').on('click', '#btn-tambah-kas', function(e) {
            e.preventDefault();

            // 1. Ambil Nilai Input Header
            let pilihKas = $('#pilih_kas').val();
            let noAcc = $('select[name="no_acc"]').val();
            let jnsTrans = $('select[name="jenis_transaksi"]').val();

            // 2. Validasi Input Header (Kas/Bank, No Acc, Jenis Transaksi)
            if (!pilihKas) {
                alert_notify('fa fa-warning', "Kas/Bank harus dipilih !", 'danger', function() {});
                return false;
            }

            if (!noAcc) {
                alert_notify('fa fa-warning', "No ACC (Kredit) tidak boleh kosong !", 'danger', function() {});
                return false;
            }

            if (!jnsTrans && pilihKas != 'kas') {
                alert_notify('fa fa-warning', "Jenis Transaksi harus dipilih !", 'danger', function() {});
                return false;
            }

            // 3. Validasi Detail: Pastikan ada minimal 1 baris data
            // if ($('#kas-bank tbody tr').length === 0) {
            //     alert_notify('fa fa-warning', "Tambahkan minimal satu baris detail !", 'danger', function() {});
            //     return false;
            // }

            // 4. Proses Simpan via AJAX
            let formData = $('#form-add-kas').serialize();

            $.ajax({
                url: "<?= base_url('finance/pelunasanhutang/save_kas_bank') ?>",
                type: "POST",
                data: formData,
                dataType: "JSON",
                beforeSend: function() {
                    $('#btn-tambah-kas').attr('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Memproses...');
                },
                success: function(response) {
                    if (response.status === 'success') {
                        // Gunakan alert_notify untuk konsistensi UI
                        alert_notify('fa fa-check', response.message, 'success', function() {
                            $('#tambah_data').modal('hide');
                            // Tambahkan fungsi reload table Anda di sini jika ada
                        });
                    } else {
                        alert_notify('fa fa-times', "Gagal: " + response.message, 'danger', function() {});
                    }
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                    alert_notify('fa fa-bug', "Terjadi kesalahan sistem. Cek console.", 'danger', function() {});
                },
                complete: function() {
                    $('#btn-tambah-kas').attr('disabled', false).html('Tambahkan');
                }
            });
        });
    });
</script>