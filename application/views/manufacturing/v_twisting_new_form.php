
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
          <h3 class="box-title">Tambah Data</h3>
          <div class="pull-right">
            <a href="<?php echo site_url('manufacturing/twisting/') ?>"><i class="fa fa-arrow-left"></i> Back</a>
          </div>
        </div>
        <div class="box-body">

          <?php if ($this->session->flashdata('success')): ?>
          <div class="alert alert-success" role="alert">
            <?php echo $this->session->flashdata('success'); ?>
          </div>
          <?php endif; ?>

          <form action="<?php base_url('manufacturing/twisting/add')?>" method="post" enctype="multipart/form-data">
            <div class="form-group">
              <label for="name">Name*</label>
              <input type="text" name="name" class="form-control <?php echo form_error('name') ? 'is-invalid':'' ?>" placeholder="Product Name" />
              <div class="invalid-feedback">
                <?php echo form_error('name')?>
              </div>
            </div>

            <div class="form-group">
              <label for="price">Price*</label>
              <input type="number" name="price" class="form-control <?php echo form_error('price') ? 'is-invalid':'' ?>" placeholder="Product Price" min="0" />
              <div class="invalid-feedback">
                <?php echo form_error('price')?>
              </div>
            </div>
            
            <div class="form-group">
                <label for="name">Photo</label>
                <input class="form-control-file <?php echo form_error('price') ? 'is-invalid':'' ?>"
                 type="file" name="image" />
                <div class="invalid-feedback">
                  <?php echo form_error('image') ?>
                </div>
            </div>

            <div class="form-group">
              <label for="description">Description*</label>
              <textarea class="form-control <?php echo form_error('description') ? 'is-invalid':'' ?>"
                 name="description" placeholder="Product description..."></textarea>
              <div class="invalid-feedback">
                <?php echo form_error('description')?>
              </div>
            </div>
            <input class="btn btn-success" type="submit" name="btn" value="Save" />
          </form>

          <div class="card-footer small text-muted">
            * required fields
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

</div>

<?php $this->load->view("admin/_partials/js.php") ?>


</body>
</html>
