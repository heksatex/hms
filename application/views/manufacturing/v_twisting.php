
<!DOCTYPE html>
<html lang="en">
<head>
  <?php $this->load->view("admin/_partials/head.php") ?>
</head>

<body class="hold-transition skin-black fixed sidebar-mini">
<!-- Site wrapper -->
<div class="wrapper">

  <!-- main -header -->
  <header class="main-header">
   <?php $this->load->view("admin/_partials/main-menu.php") ?>
   <?php $this->load->view("admin/_partials/topbar.php") ?>
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
          <h3 class="box-title">Ini Halaman Twisting</h3>
          <div class="pull-right">
            <a href="<?php echo site_url('manufacturing/twisting/add') ?>" ><i class="fa fa-plus"></i> Add New</a>

          </div>
        </div>
        <div class="box-body">
          <div id="pesan-sukses" class="alert alert-success" style="margin: 10px 20px;"></div>
          <div id="view" style="margin: 10px 20px;">
              <?php $this->load->view('manufacturing/v_twisting_tabel', array('model'=>$products)); // Load file view_tabel.php dan kirim data product ?>
          </div>

          <!--
          -- Membuat sebuah tag div untuk Modal Dialog untuk Form Tambah dan Ubah
          -- Beri id "form-modal" untuk tag div tersebut
          -->
          <div id="form-modal" class="modal fade">
              <div class="modal-dialog">
                  <div class="modal-content">
                      <div class="modal-header">
                          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                          <h4 class="modal-title">
                              <!-- Beri id "modal-title" untuk tag span pada judul modal -->
                              <span id="modal-title"></span>
                          </h4>
                      </div>
                      <div class="modal-body">
                          <!-- Beri id "pesan-error" untuk menampung pesan error -->
                          <div id="pesan-error" class="alert alert-danger"></div>
                          <form enctype="multipart/form-data">
                              <div class="form-group">
                                  <label>Nama*</label>
                                  <input type="text" class="form-control" id="nama" name="nama" placeholder="Nama">
                              </div>
                              <div class="form-group">
                                  <label>Price*</label>
                                  <input type="text" class="form-control" id="price" name="price" placeholder="Price">
                              </div>
                              <div class="form-group">
                                  <label for="name">Photo</label>
                                  <input class="form-control-file"  type="file" name="image" id="image" />
                              </div>
                              <div class="form-group">
                                <label for="description">Description*</label>
                                <textarea class="form-control" name="description"  id="description" placeholder="Product description..."></textarea>
                              </div>
                          </form>
                      </div>
                      <div class="modal-footer">
                          <!-- Beri id "loading-simpan" untuk loading ketika klik tombol simpan -->
                          <div id="loading-simpan" class="pull-left">
                              <b>Sedang menyimpan...</b>
                          </div>
                          <!-- Beri id "loading-ubah" untuk loading ketika klik tombol ubah -->
                          <div id="loading-ubah" class="pull-left">
                              <b>Sedang mengubah...</b>
                          </div>
                          <!-- Beri id "btn-simpan" untuk tombol simpan nya -->
                          <button type="button" class="btn btn-primary" id="btn-simpan">Simpan</button>
                          <!-- Beri id "btn-ubah" untuk tombol simpan nya -->
                          <button type="button" class="btn btn-primary" id="btn-ubah">Ubah</button>
                          <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
                      </div>
                  </div>
              </div>
          </div>


        </div>
        <!-- /.box-body -->
      </div>
      <!-- /.box -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <footer class="main-footer">
   <?php $this->load->view("admin/_partials/footer.php") ?>
  </footer>
   <?php $this->load->view("admin/_partials/modal.php") ?>
</div>

<?php $this->load->view("admin/_partials/js.php") ?>

</body>
</html>
