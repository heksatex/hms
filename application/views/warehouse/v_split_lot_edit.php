<!DOCTYPE html>
<html>
<head>
  <?php $this->load->view("admin/_partials/head.php") ?>
  <style>
    button[id="btn-generate"]{/*untuk hidden button generate*/
        display: none;
    }
  </style>
</head>

<body class="hold-transition skin-black fixed sidebar-mini" > 
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
     <?php $this->load->view("admin/_partials/statusbar.php") ?>
    </section>

    <!-- Main content -->
    <section class="content">

      <!--  box content -->
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title">Form Split Lot </h3>
        </div>

        <div class="box-body">
            <form class="form-horizontal">
             
                <div class="form-group">

                    <div class="col-md-6">
                        <div class="col-md-12 col-xs-12">
                            <div class="col-xs-4"><label>Kode  </label></div>
                            <div class="col-xs-8">
                                <input type="text" class="form-control input-sm" name="kode" id="kode"  readonly="readonly" value="<?php echo $split->kode_split;?>"/>                    
                            </div>                                    
                        </div>
                        <div class="col-md-12 col-xs-12">
                            <div class="col-xs-4"><label>Tanggal dibuat</label></div>
                            <div class="col-xs-8 col-md-8">
                                <input type='text' class="form-control input-sm " name="tgl_buat" id="tgl_buat" readonly="readonly" value="<?php echo $split->tanggal; ?>"  />
                            </div>                                    
                        </div>

                        <div class="col-md-12 col-xs-12" >
                            <div class="col-xs-4"><label>Departemen</label></div>
                            <div class="col-xs-8">
                                <input type='text' class="form-control input-sm " name="dapartemen" id="dapartemen" readonly="readonly" value="<?php echo $split->nama_departemen; ?>"  />
                            </div>                                    
                        </div>
                        <div class="col-md-12 col-xs-12" style="padding-bottom:10px;">
                            <div class="col-xs-4"><label>Note </label></div>
                            <div id="ta" class="col-xs-8">
                                <textarea class="form-control input-sm" name="note" id="note" ><?php echo htmlentities($split->note);?></textarea>
                            </div>                                    
                        </div>
                    </div>

                    <div class="col-md-6" >

                        <div class="col-md-12 col-xs-12">
                            <div class="col-xs-4"><label>Kode Produk  </label></div>
                                <div class="col-xs-8">
                                    <input type="hidden" class="form-control input-sm" name="quant_id" id="quant_id"  value="<?php echo $split->quant_id; ?>" readonly >  
                                    <input type="text" class="form-control input-sm" name="kode_produk" id="kode_produk"  value="<?php echo $split->kode_produk; ?>" readonly >                    
                                </div>                                    
                            </div>

                            <div class="col-md-12 col-xs-12">
                                <div class="col-xs-4"><label>Nama Produk  </label></div>
                                <div class="col-xs-8">
                                    <input type="text" class="form-control input-sm" name="nama_produk" id="nama_produk"   value="<?php echo $split->nama_produk; ?>" readonly>                    
                                </div>                                    
                            </div>

                            <div class="col-md-12 col-xs-12">
                                <div class="col-xs-4"><label>Barcode/Lot  </label></div>
                                <div class="col-xs-8">
                                    <input type="text" class="form-control input-sm" name="lot" id="lot"  value="<?php echo $split->lot; ?>"  readonly >                    
                                </div>                                    
                            </div>

                            <div class="col-md-12 col-xs-12">
                                <div class="col-xs-4"><label>Qty1  </label></div>
                                <div class="col-xs-5">
                                    <input type="text" class="form-control input-sm" name="qty" id="qty"  value="<?php echo $split->qty; ?>"  readonly>                    
                                </div> 
                                <div class="col-xs-3">
                                    <input type="text" class="form-control input-sm" name="uom_qty" id="uom_qty"  value="<?php echo $split->uom; ?>" readonly >                    
                                </div>                                    
                            </div>

                            <div class="col-md-12 col-xs-12">
                                <div class="col-xs-4"><label>Qty2  </label></div>
                                <div class="col-xs-5">
                                    <input type="text" class="form-control input-sm" name="qty2" id="qty2"  value="<?php echo $split->qty2; ?>" readonly>                    
                                </div> 
                                <div class="col-xs-3">
                                    <input type="text" class="form-control input-sm" name="uom_qty2" id="uom_qty2"  value="<?php echo $split->uom2; ?>" readonly>                    
                                </div>                                    
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
                    <li class="active"><a href="#tab_1" data-toggle="tab">Split Items</a></li>
                  </ul>
                  <div class="tab-content over"><br>
                    <div class="tab-pane active" id="tab_1">

                      <!-- Tabel  -->
                      <div class="col-md-12 table-responsive over">
                        <table class="table table-condesed table-hover rlstable  over" width="100%" id="table_items" >
                          <thead>                          
                            <tr>
                              <th class="style no">No.</th>
                              <th class="style" style="width:100px;" >Qty1</th>
                              <th class="style" width="80px">Uom</th>
                              <th class="style" style="width:100px;" >Qty2</th>
                              <th class="style" width="80px">Uom2</th>
                              <th class="style" width="100px">Lot Baru</th>
                              <th class="style" width="50px"></th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php
                                foreach ($split_items as $row) {
                                  ?>
                                    <tr class="num">
                                      <td></td>
                                      <td align="right"><?php echo number_format($row->qty,2)?></td>
                                      <td><?php echo $row->uom?></td>
                                      <td align="right"><?php echo number_format($row->qty2,2)?></td>
                                      <td><?php echo $row->uom2?></td>
                                      <td class="text-wrap width-200"><?php echo $row->lot_baru?></td>
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
    <div id="foot">
      <?php 
        $data['kode'] =  $split->kode_split;
        $data['mms']  =  $mms->kode;
        $this->load->view("admin/_partials/footer.php",$data) 
     ?>
    </div>
  </footer>

</div>

<?php $this->load->view("admin/_partials/js.php") ?>



</body>
</html>

