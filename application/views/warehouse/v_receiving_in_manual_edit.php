
<!DOCTYPE html>
<html>
<head>
  <?php $this->load->view("admin/_partials/head.php") ?>
</head>

  <style>
    button[id="btn-simpan"]{/*untuk hidden button simpan di top bar */
      display: none;
    }
  </style>

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
     <div id ="status_bar">
       <?php 
         $data['jen_status'] =  $head->status;
         $this->load->view("admin/_partials/statusbar.php", $data); 
       ?>
      </div>
    </section>

    <!-- Main content -->
    <section class="content">

      <!--  box content -->
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title"><b><?php echo $head->kode;?></b></h3>
          
        </div>
        <div class="box-body">
          <form class="form-horizontal" id="form_receiving">
            <div class="form-group">                  
              <div class="col-md-12" >
                <div id="alert"></div>
              </div>
            </div>
            <div class="form-group">

              <div class="col-md-6">

                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>No </label></div>
                  <div class="col-xs-8">
                    <input type="text" class="form-control input-sm" name="kode" id="kode" readonly="readonly" value="<?php  echo $head->kode;?>"/>
                  </div>
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Tanggal dibuat </label></div>
                  <div class="col-xs-8">
                    <input type="text" class="form-control input-sm" name="kode" id="kode" readonly="readonly" value="<?php  echo $head->tanggal;?>"/>
                  </div>
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Status </label></div>
                  <div class="col-xs-8">
                    <input type="text" class="form-control input-sm" name="status" id="status" readonly="readonly" value="<?php  echo $head->status;?>" />
                  </div>
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Creation Date </label></div>
                  <div class="col-xs-8">
                    <input type="text" class="form-control input-sm" name="creation_date" id="creation_date" readonly="readonly" value="<?php  echo $head->creation_date;?>" />
                  </div>
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Source Document </label></div>
                  <div class="col-xs-8">
                    <input type="text" class="form-control input-sm" name="source_doc" id="source_doc" readonly="readonly" value="<?php  echo $head->source_document;?>" />
                  </div>                                    
                </div>
              </div>
              <div class="col-md-6">
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Note </label></div>
                  <div class="col-xs-8">
                    <textarea type="text" class="form-control input-sm" name="note" id="note"><?php echo $head->note?></textarea>
                  </div>                                    
                </div>
              </div>

            </div>
           
          </form>

            <div class="row">
              <div class="col-md-12">
                <!-- Custom Tabs -->
                <div class="">
                  <ul class="nav nav-tabs " >
                    <li class="active"><a href="#tab_1" data-toggle="tab">Products</a></li>
                  </ul>
                  <div class="tab-content over"><br>
                    <div class="tab-pane active" id="tab_1">

                      <!-- Tabel  -->
                      <div class="col-md-12 table-responsive over">
                        <table class="table table-condesed table-hover table-responsive rlstable" id="table_items">
                          <head>
                            <tr>
                              <th class="style no">No</th>
                              <th class="style">kode Produk</th>
                              <th class="style">Nama Produk</th>
                              <th class="style">Lot</th>
                              <th class="style">Qty</th>
                              <th class="style">uom</th>
                              <th class="style">status</th>
                            </tr>
                          </head>
                          <tbody>
                            <?php 
                              $no = 1;
                              foreach ($items as $row) {?>
                                <tr>
                                  <td><?php echo $no++;?></td>
                                  <td><?php echo $row->kode_produk;?></td>
                                  <td><?php echo $row->nama_produk;?></td>
                                  <td><?php echo $row->lot;?></td>
                                  <td><?php echo $row->qty;?></td>
                                  <td><?php echo $row->uom;?></td>
                                  <td><?php echo $row->status;?></td>
                                </tr>

                            <?php 
                              }
                            ?>
                          </tbody>
                        </table>
                      </div>
                      <!-- Tabel  -->
                    </div>
                    <!-- /.tab-pane -->
              
                  </div>
                  <!-- /.tab-content -->
                </div>
                <!-- nav-tabs-custom -->
              </div>
              <!-- /.col -->
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
   <?php $this->load->view("admin/_partials/modal.php") ?>
   <?php $this->load->view("admin/_partials/footer.php") ?>
  </footer>

</div>

<?php $this->load->view("admin/_partials/js.php") ?>

</body>
</html>
