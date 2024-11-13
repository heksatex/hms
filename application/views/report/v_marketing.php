
<!DOCTYPE html>
<html lang="en">
<head>
  <?php $this->load->view("admin/_partials/head.php") ?>
  <link rel="stylesheet" type="text/css" href="<?php echo base_url('dist/css/tableScroll.css') ?>">
  <style type="text/css">
    
    h3{
      display: block !important;
      text-align: center !important;
    }

    @media (max-width: 767px) {
      .top-bar{
        display:none !important;
      }

      .content-wrapper{
        padding-top : 50px !important;
        margin-top  : 10px !important;
      }
    }

  </style>
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
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row col-md-6">
            <!--  box content -->
            <div class="box ">
                <div class="box-header with-border">
                <h3 class="box-title"><b>Gudang Jadi</b></h3>
                </div>
                <div class="box-body ">
                    <div class="col-md-12" style="margin-bottom:10px" >
                        <button type='button' class='btn btn-default btn-block' onclick="location.href='<?php echo base_url();?>report/marketing/stockbyproduct'"><i class='fa fa-cubes'></i> View Stock By Product (GJD)</button>
                        <button type='button' class='btn btn-default btn-block' onclick="location.href='<?php echo base_url();?>report/marketing/stockbylokasi'"><i class='fa fa-map-marker'></i> View Stock By Location (GJD)</button>
                        <button type='button' class='btn btn-default btn-block' onclick="location.href='<?php echo base_url();?>report/marketing/gradeexpiredgjd'"><i class='fa fa-file-text-o'></i> Report Grade & Expired (GJD)</button>
                        <button type='button' class='btn btn-default btn-block' onclick="location.href='<?php echo base_url();?>report/marketing/listwarnabyproduct'"><i class='fa fa-file-text-o'></i> List Warna by Product (GJD)</button>
                        <button type='button' class='btn btn-default btn-block' onclick="location.href='<?php echo base_url();?>report/marketing/stockhistorygjd'"><i class='fa fa-file-text-o'></i> Report Stock History (GJD)</button>
                        <button type='button' class='btn btn-default btn-block' onclick="location.href='<?php echo base_url();?>report/marketing/readygoodsgroup'"><i class='fa fa-file-text-o'></i> Report Ready Goods (GJD)</button>
                        <button type='button' class='btn btn-default btn-block' onclick="location.href='<?php echo base_url();?>report/marketing/readygoodscategory'"><i class='fa fa-file-text-o'></i> Report Ready Goods Category (GJD)</button>
                        <button type='button' class='btn btn-default btn-block' onclick="location.href='<?php echo base_url();?>report/marketing/readygoodscategorychanged'"><i class='fa fa-file-text-o'></i> Report Ready Changed Goods Category (GJD)</button>
                        <button type='button' class='btn btn-default btn-block' onclick="location.href='<?php echo base_url();?>report/marketing/goodstopush'"><i class='fa fa-file-text-o'></i> Report Goods To Push (GJD & GRG)</button>
                    </div>

                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </div>
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

</div>

<?php $this->load->view("admin/_partials/js.php") ?>

<script type="text/javascript">



</script>

</body>
</html>
