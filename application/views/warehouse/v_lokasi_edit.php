<!DOCTYPE html>
<html>

<head>
  <?php $this->load->view("admin/_partials/head.php") ?>
</head>

<body class="hold-transition skin-black fixed sidebar-mini">
  <!-- Site wrapper -->
  <div class="wrapper">

    <!-- main -header -->
    <header class="main-header">
      <?php $this->load->view("admin/_partials/main-menu.php") ?>
      <?php
      $data['deptid']     = $id_dept;
      $this->load->view("admin/_partials/topbar.php", $data)
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
        <?php $this->load->view("admin/_partials/statusbar.php") ?>
      </section>

      <!-- Main content -->
      <section class="content">

        <!--  box content -->
        <div class="box">
          <div class="box-header with-border">
            <h3 class="box-title"><b>Form Edit - <?php echo $lokasi->kode_lokasi ?></b></h3>
          </div>
          <div class="box-body">
            <form class="form-horizontal">

              <div class="form-group">
                <div class="col-md-4 col-xs-4">
                  <center><label>Arah Panah</label>
                    <div class="form-group">
                      <div class="rado">
                        <label class="fa fa-arrow-up" style=" font-size: 32px;"> </label>
                        <input type="radio" name="arah_panah" id="arah_panah1" value="1" <?php echo ($lokasi->panah == '1') ? 'checked' : '' ?>>
                      </div>
                      <div class="rado">
                        <label class="fa fa-arrow-down" style=" font-size: 32px;"> </label>
                        <input type="radio" name="arah_panah" id="arah_panah2" value="0" <?php echo ($lokasi->panah == '0') ? 'checked' : '' ?>>
                      </div>
                    </div>
                  </center>
                </div>
                <div class="col-md-8 col-xs-8">
                  <div class="col-md-3"><label style="font-size: 30px">A </label> <label>Aisle</label>
                    <input type="text" class="form-control" name="aisle" id="aisle" placeholder="A" onkeyup="return myFunction(this,'bay')" maxlength="2" readonly="" value="<?php echo $lokasi->aisle ?>">
                  </div>
                  <div class="col-md-3"><label style="font-size: 30px">B </label> <label>Bay</label>
                    <input type="text" class="form-control" name="bay" id="bay" placeholder="B" onkeyup="return myFunction(this,'slot')" maxlength="2" readonly="" value="<?php echo $lokasi->bay ?>">
                  </div>
                  <div class="col-md-3"><label style="font-size: 30px">S </label> <label>Slot</label>
                    <input type="text" class="form-control" name="slot" id="slot" placeholder="S" onkeyup="return myFunction(this,'kode_lokasi')" maxlength="2" readonly="" value="<?php echo $lokasi->slot ?>">
                  </div>
                </div>
              </div>
              <div class="form-group">
                <div class="col-md-6 ">
                  <div class="col-md-5"><label>Kode Lokasi / Nama Lokasi</label></div>
                  <div class="col-md-7 ">
                    <input type="text" class="form-control" name="kode_lokasi" id="kode_lokasi" readonly="" value="<?php echo $lokasi->kode_lokasi ?>">
                  </div>
                  <div class="col-md-5"><label>Departemen</label></div>
                  <div class="col-md-7 ">
                    <input type="text" class="form-control" name="departemen" id="departemen" readonly="" value="<?php echo $lokasi->nama ?>">

                  </div>
                  <div class="col-md-5"><label>Status Aktif</label></div>
                  <div class="col-md-7 ">
                    <select class="form-control input-sm" name="status" id="status">
                      <?php
                      $st = array('t', 'f');
                      foreach ($st as $val) {
                        if ($val == 't') {
                          $status_aktif = 'Aktif';
                        } else {
                          $status_aktif = 'Tidak Aktif';
                        }

                        if ($lokasi->status_aktif == $val) {
                          echo "<option value='" . $val . "' selected>" . $status_aktif . "</option>";
                        } else {
                          echo "<option value='" . $val . "' >" . $status_aktif . "</option>";
                        }
                        # code...
                      }
                      ?>
                    </select>
                  </div>
                </div>
              </div>
            </form>
          </div>
          <!-- /.box-body -->
        </div>
        <!-- /.box -->
      </section>
      <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

    <footer class="main-footer">
      <?php $this->load->view("admin/_partials/modal.php") ?>
      <div id="foot">
        <?php
        $data['kode'] =  $lokasi->id;
        $data['mms']  =  $mms->kode;
        $this->load->view("admin/_partials/footer.php", $data)
        ?>
        <div>
    </footer>

  </div>

  <?php $this->load->view("admin/_partials/js.php") ?>

  <script type="text/javascript">
    // focus to aisle
    $('#aisle').focus();

    function myFunction(field, nextFieldID) {

      var aisle = $('#aisle').val();
      var bay = $('#bay').val();
      var slot = $('#slot').val();

      if (bay != '') {
        var b = '.' + bay;
      } else {
        b = bay;
      }

      if (slot != '') {
        var s = '.' + slot;
      } else {
        s = slot;
      }
      $('#kode_lokasi').val(aisle + '' + b + '' + s);

      //focus next textbox
      if (field.value.length >= field.maxLength) {
        document.getElementById(nextFieldID).focus();
      }

    }

    $("#btn-simpan").on('click', function() {

      //var kode_rak   = '';
      var last_id = '<?php echo $lokasi->id ?>';
      var departemen = '<?php echo $lokasi->dept_id ?>';
      var aisle = $('#aisle').val();
      var bay = $('#bay').val();
      var slot = $('#slot').val();
      var panah = $("input[name='arah_panah']:checked").val();
      var status = $('#status').val();
      var kode_lokasi = $('#kode_lokasi').val();


      var valid = true;


      if (valid == true) {

        $('#btn-simpan').button('loading');
        please_wait(function() {});

        $.ajax({
          dataType: "JSON",
          url: '<?php echo base_url('warehouse/lokasi/simpan') ?>',
          type: "POST",
          data: {
            kode_lokasi: kode_lokasi,
            departemen: departemen,
            aisle: aisle,
            bay: bay,
            slot: slot,
            panah: panah,
            status: status,
            last_id: last_id,
            aksi: 'edit'
          },
          success: function(data) {
            if (data.sesi == "habis") {
              //alert jika session habis
              alert_modal_warning(data.message);
              window.location.replace('index');
            } else if (data.status == "failed") {
              //jika ada form belum keiisi
              $('#btn-simpan').button('reset');
              unblockUI(function() {
                setTimeout(function() {
                  alert_notify(data.icon, data.message, data.type, function() {});
                }, 1000);
              });
              document.getElementById(data.field).focus();
            } else {
              //jika berhasil disimpan/diubah
              $("#foot").load(location.href + " #foot");
              unblockUI(function() {
                setTimeout(function() {
                  alert_notify(data.icon, data.message, data.type, function() {}, 1000);
                });
              });
            }
            $('#btn-simpan').button('reset');
          },
          error: function(jqXHR, textStatus, errorThrown) {
            alert(jqXHR.responseText);
            $('#btn-simpan').button('reset');
            unblockUI(function() {});
          }
        });

      }
    });

    $('#btn-print').click(function() {
      lokasi_id = `<?php echo $lokasi->id; ?>`;
      if (lokasi_id == '') {
        alert('Lokasi Kosong');

      } else {
        var kode_rak = [];
        kode_rak.push(lokasi_id);
        var arrStr = encodeURIComponent(JSON.stringify(kode_rak));
        var url = '<?php echo base_url() ?>warehouse/lokasi/print_lokasi';
        window.open(url + '?lokasi=' + arrStr, '_blank');


      }
      return false;
    });
  </script>


</body>

</html>