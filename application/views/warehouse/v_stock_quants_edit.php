
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
   <?php 
     $data['deptid']     = $id_dept;
     $this->load->view("admin/_partials/topbar.php",$data)
   ?>
  </header>

  <!-- Menu Side Bar -->
  <aside class="main-sidebar">
  <?php 
    $this->load->view("admin/_partials/sidebar.php");
  ?>
  </aside>

  <!-- Content Wrapper-->
  <div class="content-wrapper">
    <!-- Content Header (Status - Bar) -->
    <section class="content-header" >
      
    </section>

    <!-- Main content -->
    <section class="content">
      <!--  box content -->
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title"><b><?php echo $list->kode_produk.' - '.$list->nama_produk ;?></b></h3>
        </div>
        <div class="box-body">
          <form class="form-horizontal">
            <div class="form-group">                  
              <div class="col-md-12" >
                <div id="alert"></div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group"> 

                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>ID</label></div>
                  <div class="col-xs-8">
                    <input type="text" class="form-control input-sm" name="quant_id" id="quant_id" value="<?php echo $list->quant_id; ?>" readonly="readonly"/>
                  </div>                                    
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Tanggal dibuat</label></div>
                  <div class="col-xs-8 col-md-8">
                    <input type='text' class="form-control input-sm" name="tgl" id="tgl"  value="<?php echo $list->create_date; ?>" readonly="readonly" />
                  </div>
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Kode Produk </label></div>
                  <div class="col-xs-8 col-md-8">
                    <input type='text' class="form-control input-sm" name="kode_produk" id="kode_produk" value="<?php echo $list->kode_produk; ?>" readonly="readonly" />
                  </div>
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Nama Produk </label></div>
                  <div class="col-xs-8 col-md-8">
                    <input type='text' class="form-control input-sm" name="nama_produk" id="nama_produk" value="<?php echo htmlentities($list->nama_produk); ?>" readonly="readonly" />
                  </div>
                </div>
                 <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Lot </label></div>
                  <div class="col-xs-8 col-md-8">
                    <input type='text' class="form-control input-sm" name="lot" id="lot" value="<?php echo $list->lot; ?>" readonly="readonly" />
                  </div>
                </div>
                 <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Nama Grade </label></div>
                  <div class="col-xs-8 col-md-8">
                    <select class="form-control input-sm" name="nama_grade" id="nama_grade" >
                       <option value=""></option>
                        <?php 
                          foreach ($list_grade as $row) {
                            if($list->nama_grade == $row->nama_grade){
                              echo "<option selected>".$row->nama_grade."</option>";
                            }else{
                              echo "<option >".$row->nama_grade."</option>";
                            }
                          }
                        ?>
                    </select>
                  </div>
                </div>
             
              </div>

            </div>
            <div class="col-md-6" >
              <div class="form-group">   
               
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Qty </label></div>
                  <div class="col-xs-8">
                      <input type='text' class="form-control input-sm" name="qty" id="qty" value="<?php echo $list->qty; ?>" readonly="readonly"   />
                  </div>                                    
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Uom </label></div>
                  <div id="ta" class="col-xs-8">
                    <input type='text' class="form-control input-sm" name="uom" id="uom" value="<?php echo $list->uom; ?>" readonly="readonly"   />
                  </div>                                    
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Qty 2 </label></div>
                  <div id="ta" class="col-xs-8">
                    <input type='text' class="form-control input-sm" name="qty2" id="qty2"  value="<?php echo $list->qty2; ?>"  />
                  </div>                                    
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Uom 2 </label></div>
                  <div class="col-xs-8">
                    <select class="form-control input-sm " id="uom2" name="uom2">
                        <option value=""></option>
                        <?php 
                          foreach ($list_uom as $row) {
                            if($list->uom2 == $row->short){
                              echo "<option selected>".$row->short."</option>";
                            }else{
                              echo "<option >".$row->short."</option>";
                            }
                          }
                        ?>
                    </select>
                  </div>                                    
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Lokasi </label></div>
                  <div id="ta" class="col-xs-8">
                    <input type='text' class="form-control input-sm" name="lokasi" id="lokasi" value="<?php echo $list->lokasi; ?>" readonly="readonly"   />
                  </div>                                    
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Reff Note </label></div>
                  <div id="ta" class="col-xs-8">
                    <textarea class="form-control" name="ref_note" id="ref_note" readonly="readonly"><?php echo $list->reff_note;?></textarea>
                  </div>                                    
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Reserve Move </label></div>
                  <div id="ta" class="col-xs-8">
                    <input type='text' class="form-control input-sm" name="reserve_move" id="reserve_move" value="<?php echo $list->reserve_move; ?>" readonly="readonly"   />
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
     <?php $this->load->view("admin/_partials/footer.php") ?>
    </div>
  </footer>

    <!-- Load Partial Modal -->
   <?php $this->load->view("admin/_partials/modal.php") ?>

</div>
<!--/. Site wrapper -->
<?php $this->load->view("admin/_partials/js.php") ?>


<script type="text/javascript">
  //$('#uom2').select2({});


  //klik button simpan
  $('#btn-simpan').click(function(){

      $('#btn-simpan').button('loading');
      please_wait(function(){});
      $.ajax({
         type: "POST",
         dataType: "json",
         url :'<?php echo base_url('warehouse/stockquants/simpan')?>',
         beforeSend: function(e) {
            if(e && e.overrideMimeType) {
                e.overrideMimeType("application/json;charset=UTF-8");
            }
         },
         data: {nama_grade : $('#nama_grade').val(),
                qty2       : $('#qty2').val(),
                uom2       : $('#uom2').val(),
                nama_produk: $('#nama_produk').val(),
                lot        : $('#lot').val(),
                quant_id   : $('#quant_id').val(),

          },success: function(data){
            if(data.sesi == "habis"){
              //alert jika session habis
              alert_modal_warning(data.message);
              window.location.replace('index');
            }else if(data.status == "failed"){
                //jika ada form belum keiisi
                $('#btn-simpan').button('reset');
                unblockUI( function() {
                  setTimeout(function() { alert_notify(data.icon,data.message,data.type); }, 1000);
                });
                $("#foot").load(location.href+" #foot>*",""); 
            }else{
              //jika berhasil disimpan/diubah
              unblockUI( function() {
                  setTimeout(function() { alert_notify(data.icon,data.message,data.type); }, 1000);
                });
              $('#btn-simpan').button('reset');
              $("#foot").load(location.href+" #foot>*",""); 
            }

          },error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.responseText);
            //alert('Error Simpan Data');
            unblockUI( function(){});
            $('#btn-simpan').button('reset');

          }
      });
  });




</script>


</body>
</html>
