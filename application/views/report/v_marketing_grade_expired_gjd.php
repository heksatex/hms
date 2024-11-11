
<!DOCTYPE html>
<html lang="en">
<head>
  <?php $this->load->view("admin/_partials/head.php") ?>
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

    .ws{
      white-space: nowrap;
    }

  </style>
</head>

<body class="hold-transition skin-black fixed sidebar-mini" onload="$('#product').focus()">
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
      <!--  box content -->
      <div class="box ">
        <div class="box-header with-border">
          <h3 class="box-title"><b>Grade & Expired (GJD)</b></h3>
        </div>
        <div class="box-body ">
       
            <form name="input"  action="<?= base_url() ?>report/marketing/gradeexpiredgjdgroup"  role="form" method="get" >
                <div class="col-12 col-sm-12 col-md-6 col-lg-4">
                    <div class="box-body">    
                        <div class="form-group">
                            <label>Product/Corak</label>
                            <input type="text" class="form-control" name="product" id="product" placeholder="Product/Corak"/>
                        </div>
                        <div class="form-group">
                            <label>Warna</label>
                            <input type="text" class="form-control" name="color" placeholder="Warna"/>
                        </div>
                        <div class="form-group">
                            <label>Grade</label>
                            <select class="form-control" name="cmbGrade" id="cmbGrade">
                                    <?php 
                                        echo '<option value="All">All</option>';
                                        foreach($mst_grade as $grade){
                                            echo '<option value="'.$grade->nama_grade.'">'.$grade->nama_grade.'</option>';
                                        }
                                    ?>
                            </select>     
                        </div>
                        <div class="form-group">
                            <label>Expired</label>
                            <select class="form-control" name="cmbExpired" id="cmbExpired">
                                    <?php 
                                        $arr_ex = array('Ya','Tidak');
                                        echo '<option value="All">All</option>';
                                        foreach($arr_ex as $ex){
                                            echo '<option value="'.$ex.'">'.$ex.'</option>';
                                        }
                                    ?>
                            </select>     
                        </div>
                        <div class="form-group">
                            <label>Marketing</label>
                            <select class="form-control" name="cmbMarketing" id="cmbMarketing">
                                    <?php 
                                        echo '<option value="All">All</option>';
                                        foreach($mst_sales_group as $mkt){
                                            echo '<option value="'.$mkt->kode_sales_group.'">'.$mkt->nama_sales_group.'</option>';
                                        }
                                    ?>
                            </select>     
                        </div>
                        <div class="box-footer">
                            <button type="submit" name="submit" id="btn-proses" class="btn btn-primary btn-sm">Proses</button>                        
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

</div>

<?php $this->load->view("admin/_partials/js.php") ?>

<script type="text/javascript">
  $("form").submit(function(){
    let product = $("#product").val();
    if(product.length === 0){
      alert_notify('fa fa-warning','Product / Corak harus diisi !','danger',function(){});
      $("#product").focus();
      return false;
    }else{
      return true;
    }
  });
</script>

</body>
</html>
