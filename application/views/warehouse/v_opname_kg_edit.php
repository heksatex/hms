
<!DOCTYPE html>
<html>
<head>
  <?php $this->load->view("admin/_partials/head.php") ?>
  
  <style>
    button[id="btn-simpan"]{/*untuk hidden button simpan/cancel*/
        display: none;
    }
  </style>

</head>

<body class="hold-transition skin-black fixed sidebar-mini" "> 
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
            <h3 class="box-title"><b><?php echo $opn->kode_opname?></b></h3>
        </div>
        <div class="box-body">
          <form class="form-horizontal">
            <div class="form-group">                  
              <div class="col-md-12" >
                <div id="alert"></div>
              </div>
            </div>
            <div class="form-group">

              <div class="col-md-6">
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Kode  </label></div>
                  <div class="col-xs-8">
                    <input type="text" class="form-control input-sm" name="kode" id="kode"  readonly="readonly" value="<?php echo $opn->kode_opname;?>"/>                    
                  </div>                                    
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Tanggal dibuat</label></div>
                  <div class="col-xs-8 col-md-8">
                     <input type='text' class="form-control input-sm " name="tgl_buat" id="tgl_buat" readonly="readonly" value="<?php echo $opn->tanggal; ?>"  />
                  </div>                                    
                </div>

                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Departemen</label></div>
                  <div class="col-xs-8">
                    <input type='text' class="form-control input-sm " name="departemen" id="departemen" readonly="readonly" value="<?php echo $opn->departemen; ?>"  />
                  </div>                                    
                </div>

                <div class="col-md-12 col-xs-12" style="margin-bottom:10px">
                  <div class="col-xs-4"><label>User</label></div>
                  <div class="col-xs-8">
                    <input type='text' class="form-control input-sm " name="user" id="user" readonly="readonly" value="<?php echo $opn->nama_user; ?>"  />
                  </div>                                    
                </div>

              </div>

              <div class="col-md-6" >

                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Quant Id  </label></div>
                  <div class="col-xs-8">
                    <input type="text" class="form-control input-sm" name="quant_id" id="quant_id" value="<?php echo $opn->quant_id; ?>"readonly >                    
                  </div>                                    
                </div>

                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Kode Produk  </label></div>
                  <div class="col-xs-8">
                    <input type="text" class="form-control input-sm" name="kode_produk" id="kode_produk" value="<?php echo $opn->kode_produk; ?>" readonly >                    
                  </div>                                    
                </div>

                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Nama Produk  </label></div>
                  <div class="col-xs-8">
                    <input type="text" class="form-control input-sm" name="nama_produk" id="nama_produk" value="<?php echo $opn->nama_produk; ?>" readonly>                    
                  </div>                                    
                </div>

                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Barcode/Lot  </label></div>
                  <div class="col-xs-8">
                    <input type="text" class="form-control input-sm" name="lot" id="lot" value="<?php echo $opn->lot; ?>" readonly >                    
                  </div>                                    
                </div>

                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Qty1  </label></div>
                  <div class="col-xs-5">
                    <input type="text" class="form-control input-sm" name="qty" id="qty" value="<?php echo $opn->qty; ?>"  readonly>                    
                  </div> 
                  <div class="col-xs-3">
                    <input type="text" class="form-control input-sm" name="uom_qty" id="uom_qty" value="<?php echo $opn->uom; ?>" readonly >                    
                  </div>                                    
                </div>

                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Qty2  </label></div>
                  <div class="col-xs-5">
                    <input type="text" class="form-control input-sm" name="qty2" id="qty2" value="<?php echo $opn->qty2; ?>"  readonly>                    
                  </div> 
                  <div class="col-xs-3">
                    <input type="text" class="form-control input-sm" name="uom_qty2" id="uom_qty2" value="<?php echo $opn->uom2; ?>" readonly>                    
                  </div>                                    
                </div>

                <div class="col-md-12 col-xs-12" style="padding-bottom:10px;">
                  <div class="col-xs-4"><label>Lokasi Fisik  </label></div>
                  <div class="col-xs-8">
                    <input type="text" class="form-control input-sm" name="lokasi_fisik" id="lokasi_fisik" value="<?php echo $opn->lokasi_fisik; ?>" readonly>                    
                  </div>                                    
                </div>
                <br>

                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Qty Opname  </label></div>
                  <div class="col-xs-5">
                    <input type="text" class="form-control input-sm" name="qty_opname" id="qty_opname" value="<?php echo $opn->qty_opname; ?>" style="text-align:right" readonly>                    
                  </div>
                  <div class="col-xs-3">
                    <input type="text" class="form-control input-sm" name="uom_opname" id="uom_opname" value="<?php echo $opn->uom_opname?>" readonly>
                  </div>                                    
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
    <div id="foot">
      <?php 
        $data['kode'] =  $opn->kode_opname;
        $data['mms']  =  $mms->kode;
        $this->load->view("admin/_partials/footer.php",$data) 
     ?>
    </div>
  </footer>

</div>

<?php $this->load->view("admin/_partials/js.php") ?>

<script type="text/javascript">
   
</script>

</body>
</html>
