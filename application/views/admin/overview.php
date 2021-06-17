
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
      <?php if ($this->session->flashdata('flash')):  /*?>

      <div class="flash-data" data-flashdata="<?php $this->session->flashdata('flash'));?>"></div>

      <?php */endif;?>

      <!--  box content -->
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title">Form Name + Form ID</h3>
          
        </div>
        <div class="box-body">
          <form class="form-horizontal" >
            <div class="form-group">                  
              <div class="col-md-6 col-xs-12">
                <div class="col-xs-4"><label>KP/LOT </label></div>
                <div class="col-xs-8">
                  <input type="text" class="form-control" name="txKpDisplay" id="txtKp2" />
                </div>                                    
              </div>
              <div class="col-md-6 col-xs-12">
                <div class="col-xs-4"><label>Note </label></div>
                <div class="col-xs-8">
                  <input type="text" class="form-control" name="txtNote" id="txtNote" />
                </div>                                    
              </div>
            </div>

            <div class="form-group">
              <div class="col-md-6 col-xs-12">
                <div class="col-xs-4"><label>Qty1</label></div>
                <div class="col-xs-8">
                  <input type="text" class="form-control" name="txtQty1" id="txtQty1" />
                </div>                                    
              </div>
              <div class="col-md-6 col-xs-12">
                <div class="col-xs-4"><label>Uom1</label></div>
                <div class="col-xs-8">
                  <input type="text" class="form-control" name="txtUom1" id="txtUom1" />
                </div>                                    
              </div>
            </div>

            <div class="form-group">                 
              <div class="col-md-6 col-xs-12">
                <div class="col-xs-4"><label>Corak </label></div>
                <div class="col-xs-8">
                  <input type="text" class="form-control" name="txtCorak" id="txtCorak" />
                </div>                                    
              </div>
              <div class="col-md-6 col-xs-12">
              <div class="col-xs-4"><label>Warna</label></div>
                <div class="col-xs-8">
                  <input type="text" class="form-control" name="txtWarna" id="txtWarna" />
                </div>                                    
              </div>
            </div>
          </form>
             <div class="box-body table-responsive no-padding">
              <table class="table table-hover">
                <tr>
                  <th>ID</th>
                  <th>User</th>
                  <th>Date</th>
                  <th>Status</th>
                  <th>Reason</th>
                </tr>
                <tr>
                  <td>183</td>
                  <td>John Doe</td>
                  <td>11-7-2014</td>
                  <td><span class="label label-success">Approved</span></td>
                  <td>Bacon ipsum dolor sit amet salami venison chicken flank fatback doner.</td>
                </tr>
                <tr>
                  <td>219</td>
                  <td>Alexander Pierce</td>
                  <td>11-7-2014</td>
                  <td><span class="label label-warning">Pending</span></td>
                  <td>Bacon ipsum dolor sit amet salami venison chicken flank fatback doner.</td>
                </tr>
                <tr>
                  <td>657</td>
                  <td>Bob Doe</td>
                  <td>11-7-2014</td>
                  <td><span class="label label-primary">Approved</span></td>
                  <td>Bacon ipsum dolor sit amet salami venison chicken flank fatback doner.</td>
                </tr>
                <tr>
                  <td>175</td>
                  <td>Mike Doe</td>
                  <td>11-7-2014</td>
                  <td><span class="label label-danger">Denied</span></td>
                  <td>Bacon ipsum dolor sit amet salami venison chicken flank fatback doner.</td>
                </tr>
                <tr>
                  <td>175</td>
                  <td>Mike Doe</td>
                  <td>11-7-2014</td>
                  <td><span class="label label-danger">Denied</span></td>
                  <td>Bacon ipsum dolor sit amet salami venison chicken flank fatback doner.</td>
                </tr>
                <tr>
                  <td>175</td>
                  <td>Mike Doe</td>
                  <td>11-7-2014</td>
                  <td><span class="label label-danger">Denied</span></td>
                  <td>Bacon ipsum dolor sit amet salami venison chicken flank fatback doner.</td>
                </tr>
                <tr>
                  <td>175</td>
                  <td>Mike Doe</td>
                  <td>11-7-2014</td>
                  <td><span class="label label-danger">Denied</span></td>
                  <td>Bacon ipsum dolor sit amet salami venison chicken flank fatback doner.</td>
                </tr>
                <tr>
                  <td>175</td>
                  <td>Mike Doe</td>
                  <td>11-7-2014</td>
                  <td><span class="label label-danger">Denied</span></td>
                  <td>Bacon ipsum dolor sit amet salami venison chicken flank fatback doner.</td>
                </tr>
                <tr>
                  <td>175</td>
                  <td>Mike Doe</td>
                  <td>11-7-2014</td>
                  <td><span class="label label-danger">Denied</span></td>
                  <td>Bacon ipsum dolor sit amet salami venison chicken flank fatback doner.</td>
                </tr>
                <tr>
                  <td>175</td>
                  <td>Mike Doe</td>
                  <td>11-7-2014</td>
                  <td><span class="label label-danger">Denied</span></td>
                  <td>Bacon ipsum dolor sit amet salami venison chicken flank fatback doner.</td>
                </tr>
              </table>
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
