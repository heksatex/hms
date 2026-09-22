<!DOCTYPE html>
<html lang="en">
<head>
    <?php $this->load->view("admin/_partials/head.php") ?>
    <link rel="stylesheet" href="<?=base_url()?>dist/css/karyawan.css">

</head>

<body class="hold-transition skin-black fixed sidebar-mini">
<!-- Site wrapper -->
<div class="wrapper">
  <!-- main -header -->
  <header class="main-header">
   <?php $this->load->view("admin/_partials/main-menu.php") ?>
   <?php 
     $data['deptid']     = $id_dept;
     $this->load->view("admin/_partials/topbar.php",$data)
   ?>
  </header>

  <!-- Menu Side Bar -->
  <aside class="main-sidebar">
  <?php $this->load->view("admin/_partials/sidebar.php") ?>
  </aside>

  <!-- Content Wrapper-->
  <div class="content-wrapper">
    <!-- Content Header (Status - Bar) -->
    <section class="content-header">
    </section>

    <!-- Main content -->
    <section class="content">
      <!--  box content -->
      <!-- <div class="box"> -->
        <!-- <div class="box-body"> -->
            <section class="content master-karyawan-wrapper">

            <div class="master-karyawan-page">
                <!-- ================================
                        HEADER MASTER KARYAWAN
                    ================================= -->
                <div class="mk-header-card">
                    <div class="mk-title">
                        <h2>Master Karyawan HMS</h2>
                        <p>Direktori & Profil Karyawan (Urut: Departemen & Nama)</p>
                    </div>
                    <div class="mk-summary">
                        <div class="summary-box total">
                            <span>TOTAL</span>
                            <strong id="statTotal">0</strong>
                        </div>
                        <div class="summary-box tersaring">
                            <span>TERSARING</span>
                            <strong id="statFound">0</strong>
                        </div>
                    </div>
                </div>
                 <!-- ================================
                    MAIN CONTENT
                ================================= -->
                <div class="mk-content">
                    <!-- ============================
                            DETAIL KARYAWAN
                        ============================= -->
                    <div class="mk-profile-panel">
                        <div class="panel-title"> DETAIL PROFIL KARYAWAN </div>
                        <div class="profile-content">

                            <div class="profile-photo-wrapper">
                                <img id="detailPhoto" src="" class="profile-photo" alt="Foto Karyawan" onerror="this.onerror=null; this.src='<?= base_url('dist/img/user2-160x160.jpg') ?>';">
                                <div class="photo-caption"> Klik foto untuk memperbesar </div>
                            </div>
                            <h3 id="detailName">-</h3>

                            <div class="profile-position" id="detailRole"> - </div>
                            <!-- DETAIL -->
                            <div class="profile-detail">
                                <div class="detail-row">
                                    <span>NRP</span>
                                    <strong id="detailNrp"> - </strong>
                                </div>
                                <div class="detail-row">
                                    <span id="id_nik">NIK</span>
                                    <strong id="detailNik"> - </strong>
                                </div>
                                <div class="detail-row">
                                    <span>Departemen</span>
                                    <strong id="detailDept"> - </strong>
                                </div>
                                <div class="detail-row">
                                    <span>Golongan</span>
                                    <strong  id="detailGolongan"> - </strong>
                                </div>
                                <div class="detail-row">
                                    <span>Tgl Masuk</span>
                                    <strong id="detailtglMasuk"> - </strong>
                                </div>
                                 <div class="detail-row">
                                    <span>Awal Kontrak</span>
                                    <strong id="detailawalKontrak"> - </strong>
                                </div>
                                <div class="detail-row">
                                    <span>Akhir Kontrak</span>
                                    <strong id="detailakhirKontrak"> - </strong>
                                </div>
                                <div class="detail-row">
                                    <span>Jenis Shift</span>
                                    <strong id="detailjenisShift"> - </strong>
                                </div>
                                <div class="detail-row">
                                    <span>Group Shift</span>
                                    <strong id="detailgroupShift"> - </strong>
                                </div>
                                <div class="detail-row">
                                    <span>Shift</span>
                                    <strong id="detailshift"> - </strong>
                                </div>
                                <div class="detail-row">
                                    <span>Tempat, Tgl Lahir</span>
                                    <strong id="detailtempatTglLahir"> - </strong>
                                </div>
                                <div class="detail-row">
                                    <span>Alamat</span>
                                    <strong id="detailalamat"> - </strong>
                                </div>
                                <div class="detail-row">
                                    <span>Pendidikan</span>
                                    <strong id="detailpendidikan"> - </strong>
                                </div>
                                <div class="detail-row">
                                    <span>Jenis Kelamin</span>
                                    <strong id="detailjenisKelamin"> - </strong>
                                </div>
                                <div class="detail-row">
                                    <span>Agama</span>
                                    <strong id="detailagama"> - </strong>
                                </div>
                                <div class="detail-row">
                                    <span>Status Kawin</span>
                                    <strong id="detailkawin"> - </strong>
                                </div>
                               
                                <div class="detail-row">
                                    <span>Jamsostek</span>
                                    <strong id="detailjamsostek"> - </strong>
                                </div>
                                <div class="detail-row">
                                    <span>SPSI</span>
                                    <strong id="detailspsi"> - </strong>
                                </div>
                                <div class="detail-row">
                                    <span>Kematian</span>
                                    <strong id="detailkematian"> - </strong>
                                </div>
                                <div class="detail-row">
                                    <span>BPJS</span>
                                    <strong id="detailbpjs"> - </strong>
                                </div>
                                <div class="detail-row">
                                    <span>UM</span>
                                    <strong id="detailum"> - </strong>
                                </div>
                                <div class="detail-row">
                                    <span>No Rek</span>
                                    <strong id="detailnoRek"> - </strong>
                                </div>
                                <div class="detail-row">
                                    <span>Finger Id</span>
                                    <strong id="detailfingerId"> - </strong>
                                </div>
                                <div class="detail-row">
                                    <span>Status Gaji</span>
                                    <strong id="detailstatusGaji"> - </strong>
                                </div>
                                <div class="detail-row">
                                    <span>Tunjangan Jabatan</span>
                                    <strong id="detailtunjanganJabatan"> - </strong>
                                </div>
                                <div class="detail-row">
                                    <span>Tunjangan Masa Kerja </span>
                                    <strong id="detailtunjanganMasaKerja"> - </strong>
                                </div>
                                <div class="detail-row">
                                    <span>Tunjangan Fungsional </span>
                                    <strong id="detailtunjanganFungsional"> - </strong>
                                </div>
                                <div class="detail-row">
                                    <span>Status Kepegawaian</span>
                                    <strong id="detailStatus"> - </strong>
                                </div>

                                
                            </div>
                        </div>
                        <button type="button" class="btn btn-full-photo" id="btnFullPhoto"> Lihat Foto Penuh </button>
                    </div>
                    <!-- ============================
                            LIST KARYAWAN
                        ============================= -->
                    <div class="mk-list-panel">
                    <!-- SEARCH -->
                    <div class="mk-search">
                        <i class="fa fa-search"></i>
                         <input type="text" id="searchKaryawan" class="form-control" placeholder="Cari NRP, nama, bagian atau jabatan..." autocomplete="off">
                    </div>
                    <!-- EMPLOYEE GRID -->
                    <div id="employeeGrid" class="employee-grid"></div>
                    <!-- PAGINATION -->
                    <div class="mk-list-footer">
                        <div class="page-info" id="pageIndicator"> Halaman 1 dari 1 </div>
                        <div class="pagination-custom">
                        <button type="button" class="btn btn-prev" id="btnPrev"> ← Prev </button>
                        <button type="button" class="btn btn-next" id="btnNext"> Next → </button>
                        </div>
                    </div>
                    </div>
                </div>
            </div>
            <!-- ================================
                MODAL FOTO
            ================================= -->
            <!-- <div class="modal fade" id="photoModal" tabindex="-1" role="dialog">
                <div class="modal-dialog modal-sm" role="document">
                    <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                        </button>
                        <h4 class="modal-title" id="modalName"> Foto Karyawan </h4>
                    </div>
                    <div class="modal-body text-center">
                        <img id="modalPhoto" src="" class="modal-profile-photo" alt="Foto"  loading="lazy">
                        <div class="modal-photo-info" id="modalNipRole"></div>
                    </div>
                    </div>
                </div>
            </div> -->

            <div class="modal fade" id="photoModal" tabindex="-1" role="dialog">
                <div class="modal-dialog modal-sm" role="document">
                    <div class="modal-content">

                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">
                                <span>&times;</span>
                            </button>

                            <h4 class="modal-title" id="modalName">
                                Foto Karyawan
                            </h4>
                        </div>

                        <div class="modal-body text-center">

                            <div id="photoLoading" style="padding:30px;">
                                <i class="fa fa-spinner fa-spin fa-2x"></i>
                                <div style="margin-top:10px;">
                                    Memuat foto...
                                </div>
                            </div>

                            <img
                                id="modalPhoto"
                                src=""
                                class="modal-profile-photo"
                                alt="Foto"
                                style="display:none;"
                            >

                            <div
                                class="modal-photo-info"
                                id="modalNipRole">
                            </div>

                        </div>

                    </div>
                </div>
            </div>

            </section>


        <!-- </div> -->
        <!-- /.box-body -->
      <!-- </div> -->
      <!-- /.box -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->


  <?php $this->load->view("admin/_partials/modal.php") ?>
</div>

<?php $this->load->view("admin/_partials/js.php") ?>

<script>
    var currentPage = 1;
    var currentKeyword = '';
    var searchTimer = null;
    // =====================================================
    // LOAD DATA
    // =====================================================
    function loadEmployees(page, keyword) {
        currentPage = page;
        currentKeyword = keyword || '';
        $.ajax({
            url: "<?= site_url('hr/karyawan/load_data') ?>",
            type: "POST",
            dataType: "json",
            data: {
                page: page,
                keyword: currentKeyword
            },
            beforeSend: function() {
                $('#employeeGrid').html(`
                        <div class="employee-loading">
                            <i class="fa fa-spinner fa-spin"></i>
                            <span>Memuat data karyawan...</span>
                        </div>
                    `);
            },
            success: function(response) {
                if (!response.status) {
                    return;
                }
                renderEmployees(response.data);
                $('#statTotal').text(response.total);
                $('#statFound').text(response.total_filtered);
                $('#pageIndicator').text("Halaman "+response.page + ' / ' + response.total_pages);
                // Previous
                $('#btnPrev').prop('disabled', response.page <= 1);
                // Next
                $('#btnNext').prop('disabled', response.page >= response.total_pages);
            },
            error: function(xhr) {
                console.log(xhr.responseText);
                $('#employeeGrid').html(`
                        <div class="employee-empty">
                            <i class="fa fa-warning"></i>
                            <h4>Gagal mengambil data</h4>
                            <p>Silakan coba lagi.</p>
                        </div>
                    `);
            }
        });
    }
    // =====================================================
    // RENDER CARD
    // =====================================================
    function renderEmployees(data) {
        $('#employeeGrid').removeClass('fill-10');
        if (!data || data.length === 0) {
            $('#employeeGrid').html(`
                <div class="employee-empty">
                    <i class="fa fa-user-times"></i>
                    <h4>Data tidak ditemukan</h4>
                    <p>Tidak ada karyawan yang sesuai.</p>
                </div>
            `);
            return;
        }
        var html = '';
        $.each(data, function(index, emp) {
            var photoUrl = "<?= site_url('hr/karyawan/foto_thumb') ?>/" + emp.nrp;
            var defaultPhoto = "<?= base_url('dist/img/user2-160x160.jpg') ?>";
            html += `
                <div class="employee-card" data-id="${emp.nrp}">

                    <div class="employee-card-photo">
                        <img
                            src="${photoUrl}"
                            alt="${emp.nama || 'Foto Karyawan'}"
                            onerror="this.onerror=null; this.src='${defaultPhoto}';"
                        >
                    </div>

                    <div class="employee-info">

                        <div class="employee-name">
                            ${emp.nama || '-'}
                        </div>

                        <div class="employee-nrp">
                            ${emp.nrp || '-'}
                        </div>

                        <div class="employee-role">
                            ${emp.jabatan || '-'}
                        </div>

                        <div class="employee-dept">
                            ${emp.bagian || '-'}
                        </div>

                    </div>

                </div>
            `;
        });
        $('#employeeGrid').html(html);
        requestAnimationFrame(function() {
            updateEmployeeGridMode();
        });

        // $('#employeeGrid').removeClass('fill-10 scroll-mode');

        // otomatis pilih karyawan pertama
        // $('#employeeGrid .employee-card').first().trigger('click');
    }

    $(window).on('resize', function() {
        updateEmployeeGridMode();
    });


   function updateEmployeeGridMode()
    {
        var $grid = $('#employeeGrid');
        var grid = document.getElementById('employeeGrid');

        $grid.removeClass('fill-10 scroll-mode');

        var count = $grid.find('.employee-card').length;

        if (count === 0) {
            return;
        }

        // Kurang dari 10 → tetap 5 row, card normal
        if (count < 10) {
            return;
        }

        // 10 card → coba mode penuh
        if (count === 10) {
            var card = grid.querySelector('.employee-card');
            var cardHeight = $(card).outerHeight();
            var requiredHeight = (cardHeight * 5) + (10 * 4);

            if (grid.clientHeight >= requiredHeight) {
                $grid.addClass('fill-10');
            } else {
                $grid.addClass('scroll-mode');
            }
        }
    }
    // =====================================================
    // INITIAL
    // =====================================================
    function getInitial(nama) {
        if (!nama) {
            return '?';
        }
        return nama.trim().charAt(0).toUpperCase();
    }

    // =====================================================
    // SEARCH
    // =====================================================
    $('#searchKaryawan').on('keyup', function() {
        var keyword = $.trim($(this).val());
        clearTimeout(searchTimer);
        searchTimer = setTimeout(function() {
            loadEmployees(1, keyword);
        }, 300);
    });
    // =====================================================
    // PREVIOUS
    // =====================================================
    $('#btnPrev').on('click', function() {
        if ($(this).prop('disabled')) {
            return;
        }
        if (currentPage <= 1) {
            return;
        }
        loadEmployees(currentPage - 1, currentKeyword);
    });
    // =====================================================
    // NEXT
    // =====================================================
    $('#btnNext').on('click', function() {
        if ($(this).prop('disabled')) {
            return;
        }
        loadEmployees(currentPage + 1, currentKeyword);
    });
    // =====================================================
    // CLICK EMPLOYEE
    // =====================================================
    $(document).on('click', '.employee-card', function() {
        var id = $(this).data('id');
        $('.employee-card').removeClass('active');
        $(this).addClass('active');
        loadEmployeeDetail(id);
    });
    // =====================================================
    // DETAIL
    // =====================================================
    function loadEmployeeDetail(id) {
        $.ajax({
            url: "<?= site_url('hr/karyawan/detail') ?>/" + id,
            type: "GET",
            dataType: "json",
            success: function(response) {
                if (!response.status) {
                    return;
                }
                updateEmployeeDetail(response.data);
            },
            error: function(xhr) {
                console.log(xhr.responseText);
            }
        });
    }
    // =====================================================
    // UPDATE PROFILE
    // =====================================================
    function updateEmployeeDetail(emp) {
        var photoUrl = "<?= site_url('hr/karyawan/foto_thumb') ?>/" + emp.nrp;

        $('#detailPhoto').attr('src', photoUrl);
        $('#detailName').text(emp.nama || '-');
        $('#detailNrp').text(emp.nrp || '-');
        $('#id_nik').html('ID ('+emp.jenis_id+')' || 'ID');
        $('#detailNik').text(emp.no_id || '-');
        $('#detailDept').text(emp.bagian || '-');
        $('#detailRole').text(emp.jabatan || '-');
        $('#detailGolongan').text(emp.golongan || '-');
        $('#detailtglMasuk').text(formatTanggalIndonesia(emp.tgl_masuk) || '-');
        $('#detailawalKontrak').text(formatTanggalIndonesia(emp.awal_kontrak) || '-');
        $('#detailakhirKontrak').text(formatTanggalIndonesia(emp.akhir_kontrak) || '-');
        $('#detailjenisShift').text(emp.jenis_shift || '-');
        $('#detailgroupShift').text(emp.group_shift || '-');
        $('#detailshift').text(emp.shift || '-');
        $('#detailtempatTglLahir').text(emp.tmp_lahir+', '+formatTanggalIndonesia(emp.tgl_lahir) || '-');
        $('#detailalamat').text(emp.alamat || '-');
        $('#detailpendidikan').text(emp.pendidikan || '-');
        $('#detailjenisKelamin').text(emp.jk || '-');
        $('#detailagama').text(emp.agama || '-');
        $('#detailkawin').text(emp.kawin+ "("+emp.jml_anak+")" || '-');
        $('#detailjamsostek').text(formatAngka(emp.jamsostek) || '-');
        $('#detailspsi').text(formatAngka(emp.spsi) || '-');
        $('#detailkematian').text(formatAngka(emp.kematian) || '-');
        $('#detailbpjs').text(formatAngka(emp.bpjs) || '-');
        $('#detailum').text(formatAngka(emp.um) || '-');
        $('#detailnoRek').text((emp.norek_bca!=='') ? "BCA - "+emp.norek_bca : '-');
        $('#detailfingerId').text(emp.finger_id || '-');
        $('#detailstatusGaji').text(emp.status_gaji || '-');
        $('#detailtunjanganJabatan').text(formatAngka(emp.t_jabatan) || '-');
        $('#detailtunjanganMasaKerja').text(formatAngka(emp.t_masakerja) || '-');
        $('#detailtunjanganFungsional').text(formatAngka(emp.t_fungsional) || '-');
        $('#detailStatus').text(emp.status || '-');
    }

    function formatTanggalIndonesia(tanggal) {
        if (!tanggal) {
            return '-';
        }

        const date = new Date(tanggal);

        if (isNaN(date.getTime())) {
            return tanggal;
        }

        const options = {
            // weekday: 'long',//hari
            day: '2-digit',
            month: 'long',
            year: 'numeric'
        };

        return date.toLocaleDateString('en-ID', options);
    }

    function formatAngka(angka)
    {
        if (angka === null || angka === undefined || angka === '') {
            return '0';
        }

        return Number(angka).toLocaleString('en-US')
    }
    // =====================================================
    // LOAD AWAL
    // =====================================================
    $(document).ready(function() {
        loadEmployees(1, '');
    });

    // function openPhotoModal()
    // {
    //     // var photoSrc = $('#detailPhoto').attr('src');

    //     var photSrcAsli = "<?= site_url('hr/karyawan/foto') ?>/" +  $('#detailNrp').text();

    //     var nama = $('#detailName').text();
    //     var nrp = $('#detailNrp').text();
    //     var jabatan = $('#detailRole').text();

    //     $('#modalPhoto').attr('src', photSrcAsli);

    //     $('#modalName').text(nama || 'Foto Karyawan');

    //     $('#modalNipRole').text(
    //         (nrp || '-') + ' • ' + (jabatan || '-')
    //     );

    //     $('#photoModal').modal('show');
    // }

    var photoLoadId = 0;

    function openPhotoModal() {
        var nama = $('#detailName').text().trim();
        var nrp = $('#detailNrp').text().trim();
        var jabatan = $('#detailRole').text().trim();
        // ID loading baru
        photoLoadId++;
        var currentLoadId = photoLoadId;
        $('#modalName').text(nama || 'Foto Karyawan');
        $('#modalNipRole').text((nrp || '-') + ' • ' + (jabatan || '-'));
        var $photo = $('#modalPhoto');
        // ==========================================
        // STOP LOAD FOTO SEBELUMNYA
        // ==========================================
        $photo.off('.photo');
        // Ganti src ke blank terlebih dahulu
        $photo.attr('src', 'about:blank');

        // Sembunyikan gambar
        $photo.hide();

        // ==========================================
        // LOADING
        // ==========================================
        $('#photoLoading').show().html('<i class="fa fa-spinner fa-spin fa-2x"></i>' + '<div style="margin-top:10px;">Memuat foto...</div>');

        // ==========================================
        // TAMPILKAN MODAL
        // ==========================================
        $('#photoModal').modal('show');
        // ==========================================
        // URL FOTO
        // ==========================================
        var photoUrl = "<?= site_url('hr/karyawan/foto_thumb900') ?>/" + encodeURIComponent(nrp);

        // ==========================================
        // EVENT LOAD
        // ==========================================
        $photo.off('load.photo error.photo').on('load.photo', function() {
            // Pastikan ini masih request terakhir
            if (currentLoadId !== photoLoadId) {
                return;
            }
            $('#photoLoading').hide();
            $(this).show();
        }).on('error.photo', function() {
            // Pastikan ini masih request terakhir
            if (currentLoadId !== photoLoadId) {
                return;
            }
            $(this).hide();
            $('#photoLoading').html('<i class="fa fa-image fa-2x"></i>' + '<div style="margin-top:10px;">' + 'Foto tidak tersedia' + '</div>');
        });
        // ==========================================
        // MULAI LOAD FOTO
        // ==========================================
        $photo.attr('src', photoUrl);
    }


    $('#photoModal').on('hidden.bs.modal', function () {

        $('#modalPhoto')
            .off('load.photo error.photo')
            .removeAttr('src')
            .hide();

        $('#photoLoading').show();
    });

    $(document).on('click', '#detailPhoto', function() {
        openPhotoModal();
    });

    $(document).on('click', '#btnFullPhoto', function() {
        openPhotoModal();
    });
        
</script>
</body>
</html>
