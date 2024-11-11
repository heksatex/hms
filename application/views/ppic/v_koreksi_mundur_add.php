
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
          <h3 class="box-title">Form Tambah</h3>
          
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
                    <div class="col-xs-4"><label>Kode Koreksi </label></div>
                    <div class="col-xs-8">
                        <input type="text" class="form-control input-sm" name="kode_koreksi" id="kode_koreksi"  readonly="readonly" />                    
                    </div>                                    
                  </div>
                  <div class="col-md-12 col-xs-12">
                    <div class="col-xs-4"><label>Tanggal dibuat </label></div>
                    <div class="col-xs-8 col-md-8">
                        <input type='text' class="form-control input-sm " name="tgl_buat" id="tgl_buat" readonly="readonly" value="<?php echo date('Y-m-d H:i:s')?>"  />
                    </div>                                    
                  </div>
                  <div class="col-md-12 col-xs-12">
                    <div class="col-xs-4"><label>Tanggal Transaksi </label></div>
                    <div class="col-xs-8 col-md-8">
                        <input type='text' class="form-control input-sm " name="tgl_buat" id="tgl_buat" readonly="readonly" value="<?php echo date('Y-m-d H:i:s')?>"  />
                    </div>                                    
                  </div>
              </div>
              <div class="col-md-6">
                  <div class="col-md-12 col-xs-12">
                    <div class="col-xs-4"><label>Notes </label></div>
                    <div class="col-xs-8">
                        <textarea type="text" class="form-control input-sm" name="note" id="note"></textarea>
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
   <?php $this->load->view("admin/_partials/modal.php") ?>
   <?php $this->load->view("admin/_partials/footer.php") ?>
  </footer>

</div>

<?php $this->load->view("admin/_partials/js.php") ?>

<script type="text/javascript">

  //klik button simpan
  $('#btn-simpan').click(function(){
    $('#btn-simpan').button('loading');
    please_wait(function(){});
    $.ajax({
       type: "POST",
       dataType: "json",
       url :'<?php echo base_url('ppic/koreksimundur/simpan')?>',
       beforeSend: function(e) {
          if(e && e.overrideMimeType) {
              e.overrideMimeType("application/json;charset=UTF-8");
          }
       },
       data: { note : $('#note').val(),
        },success: function(data){
          if(data.sesi == "habis"){
            //alert jika session habis
            alert_modal_warning(data.message);
            window.location.replace('index');
          }else if(data.status == "failed"){
              //jika ada form belum keiisi
              unblockUI( function() {
                setTimeout(function() { alert_notify(data.icon,data.message,data.type,function(){}); }, 1000);
              });
               document.getElementById(data.field).focus();
          }else{
           //jika berhasil disimpan/diubah
            $('#kode_koreksi_encr').val(data.isi);
            unblockUI( function() {
              setTimeout(function() { 
                alert_notify(data.icon,data.message,data.type, function(){
                window.location.replace('edit/'+data.isi);
              },1000); 
              });
            });
          }
          $('#btn-simpan').button('reset');

        },error: function (xhr, ajaxOptions, thrownError) {
          $('#btn-simpan').button('reset');
          alert(xhr.responseText);
          unblockUI( function(){});
        }
    });
  });


</script>


</body>
</html>
