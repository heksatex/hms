<!DOCTYPE html>
<html>
  <head>
        <?php $this->load->view("produksi/hmi/head") ?>
        <link rel="stylesheet" href="<?=base_url()?>dist/css/hmi/page-setting.css">
  </head>
  <body> 

    <?php $this->load->view('produksi/hmi/header', $header); ?> 
    <div class="container-fluid hmi-body">
        <div class="page-header">
            <div class="page-header-top">
                <div class="page-title">
                    <h2><i class="fa fa-sliders"></i>Setting</h2>
                </div>
            </div>
        </div>
        <div class="row"> 
            <div class="form-group">
                <div class="col-md-12">
                    <main class="setting-input-panel">

                        <!-- USER NAME -->
                        <div class="input-row">
                            <div class="row-number">1</div>

                            <div class="input-label">
                                NAMA USER
                            </div>

                            <div class="input-content">
                                <input type="text"
                                    id="nama_user"
                                    name="nama_user"
                                    class="setting-input"
                                    value="<?= $user->nama; ?>" readonly>
                            </div>
                        </div>

                        <!-- LOGIN -->
                        <div class="input-row">
                            <div class="row-number">2</div>

                            <div class="input-label">
                                LOGIN
                            </div>

                            <div class="input-content">
                                <input type="text"
                                    id="login"
                                    name="login"
                                    class="setting-input"
                                    value="<?= $user->username; ?>"
                                    readonly>
                            </div>
                        </div>

                        <!-- PASSWORD LAMA -->
                        <div class="input-row">
                            <div class="row-number">3</div>

                            <div class="input-label">
                                PASSWORD LAMA
                            </div>

                            <div class="input-content password-content">
                                <input type="password"
                                    id="password_lama"
                                    name="password_lama"
                                    class="setting-input"
                                    autocomplete="current-password">

                            </div>
                        </div>

                        <!-- PASSWORD BARU -->
                        <div class="input-row">
                            <div class="row-number">4</div>

                            <div class="input-label">
                                PASSWORD BARU
                            </div>

                            <div class="input-content password-content">
                                <input type="password"
                                    id="password_baru"
                                    name="password_baru"
                                    class="setting-input">

                                <button type="button"
                                        class="btn-show-password"
                                        data-target="password_baru"
                                        tabindex="-1">
                                    <i class="fa fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <!-- ULANGI PASSWORD -->
                        <div class="input-row">
                            <div class="row-number">5</div>

                            <div class="input-label">
                                ULANGI PASSWORD BARU
                            </div>

                            <div class="input-content password-content">
                                <input type="password"
                                    id="password_baru_ulang"
                                    name="password_baru_ulang"
                                    class="setting-input">

                                <button type="button"
                                        class="btn-show-password"
                                        data-target="password_baru_ulang"
                                        tabindex="-1">
                                    <i class="fa fa-eye"></i>
                                </button>
                            </div>
                        </div>


                        <!-- SAVE -->
                        <div class="setting-save-wrapper">
                            <button type="button"
                                    id="btn-simpan"
                                    class="btn-setting-save"
                                    data-loading-text="<i class='fa fa-spinner fa-spin'></i> MENYIMPAN...">

                                <i class="fa fa-save"></i>
                                <span>SIMPAN </span>

                            </button>
                        </div>

                    </main>
                    
                </div>
            </div>
        </div>
    </div> 
    
    <?php $this->load->view('produksi/hmi/footer'); ?> 
    <?php $this->load->view("produksi/hmi/js") ?>
    <script src="<?php echo base_url('dist/notify/bootstrap-notify.js') ?>"></script>
    <script src="<?php echo base_url('dist/notify/bootstrap-notify.min.js') ?>"></script>

    <script>
        $(document).on('click', '.btn-show-password', function () {

            var $button = $(this);
            var target  = $button.data('target');
            var $input  = $('#' + target);
            var $icon   = $button.find('i');

            if ($input.attr('type') === 'password') {

                // Tampilkan password
                $input.attr('type', 'text');

                // Ganti icon menjadi mata dicoret
                $icon
                    .removeClass('fa-eye')
                    .addClass('fa-eye-slash');

            } else {

                // Sembunyikan password
                $input.attr('type', 'password');

                // Kembalikan icon mata
                $icon
                    .removeClass('fa-eye-slash')
                    .addClass('fa-eye');
            }

        });

        
        $('#btn-simpan').click(function() {
            var passwordLama = $('#password_lama').val();
            var passwordBaru = $('#password_baru').val();
            var ulangiPassword = $('#password_baru_ulang').val();
            // Validasi client-side sederhana
            if (passwordLama === '') {
                $('#password_lama').focus();
                alert_notify('warning', 'Password lama harus diisi', 'warning',function(){})
                return;
            }
            if (passwordBaru === '') {
                $('#password_baru').focus();
                alert_notify('warning', 'Password baru harus diisi', 'warning',function(){})
                return;
            }
            if (ulangiPassword === '') {
                $('#password_baru_ulang').focus();
                alert_notify('warning', 'Ulangi password baru', 'warning',function(){})
                return;
            }
            if (passwordBaru !== ulangiPassword) {
                $('#password_baru_ulang').focus();
                alert_notify('warning', 'Password baru dan ulangi password tidak sama', 'warning',function(){})
                return;
            }
            $('#btn-simpan').button('loading');
            $.ajax({
                type: "POST",
                dataType: "json",
                url: '<?= base_url("setting/ganti_pass/simpan") ?>',
                beforeSend: function(e) {
                    if (e && e.overrideMimeType) {
                        e.overrideMimeType("application/json;charset=UTF-8");
                    }
                },
                data: {
                    login: $('#login').val(),
                    passwordlama: passwordLama,
                    passwordbaru: passwordBaru,
                    ulangipasswordbaru: ulangiPassword,
                    status: 'edit'
                },
                success: function(data) {
                    if (data.sesi == "habis") {
                        alert(data.message);
                        window.location.replace('index');
                        return;
                    } else if (data.status == "failed") {
                        $('#btn-simpan').button('reset');
                   
                        alert_notify(data.icon, data.message, data.type,function(){})
                        if (data.field) {
                            $('#' + data.field).focus();
                        }
                    } else {
                        alert_notify(data.icon, data.message, data.type,function(){})
                        $('#password_lama').val('');
                        $('#password_baru').val('');
                        $('#password_baru_ulang').val('');
                    }
                    $('#btn-simpan').button('reset');
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    console.log(xhr.responseText);
                    $('#btn-simpan').button('reset');
                    alert('Terjadi kesalahan saat menyimpan password');
                }
            });
        });
    </script>
  </body>
</html>