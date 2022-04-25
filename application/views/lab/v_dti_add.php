
<!DOCTYPE html>
<html>
<head>
  <?php $this->load->view("admin/_partials/head.php") ?>
  <!-- color picker -->
  <link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/colorpicker/bootstrap-colorpicker.min.css') ?>">
  <style type="text/css">
    .div1 {
      width: 100%;
      border: 1px solid;
      border-color: #d2d6de;
      padding: 50px;
      margin: 10px 0px 10px 0px;
      border-radius: 5px;
    }

  </style>

</head>

<body class="hold-transition skin-black fixed sidebar-mini" onload="get_default()">
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
                  <div class="col-xs-4"><label>Tanggal dibuat </label></div>
                  <div class="col-xs-8 col-md-8">
                      <input type='text' class="form-control input-sm" name="tgl" id="tgl" readonly="readonly" value="<?php echo date('Y-m-d h:i:s')?>" />
                  </div>                                    
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Nama Warna </label></div>
                  <div class="col-xs-8">
                    <input type="text" class="form-control input-sm" name="warna" id="warna"/>
                  </div>                                    
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Notes </label></div>
                  <div class="col-xs-8">
                    <textarea type="text" class="form-control input-sm" name="note" id="note"></textarea>
                  </div>                                    
                </div>
                
              </div>

              <div class="col-md-6">
               <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Kode Warna </label></div>
                  <div class="col-xs-8 col-md-8">
                    <div class="input-group my-colorpicker" id="my-colorpicker">
                      <input type="text" class="form-control input-sm" id="kode_warna" name="kode_warna"  >
                      <span class="input-group-addon" id='groupColor'>
                           <i id="wstyle" ></i>
                      </span>
                    </div>
                  </div>                                    
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label></label></div>
                  <div class="col-xs-8 col-md-8">
                    <div class="div1" id="content_colors"  >
                    </div>
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
<!-- color picker -->
<script src="<?php echo site_url('plugins/colorpicker/bootstrap-colorpicker.min.js') ?>"></script>


<script type="text/javascript">

  $(".my-colorpicker").colorpicker();

  $('.my-colorpicker').colorpicker().on('changeColor', function (e) {
      $('#content_colors')[0].style.backgroundColor = e.color.toHex();
  });

  function get_default(){
    $('#kode_warna').val('');
    $('#wstyle').css("background-color", "");
  }
 
  //klik button simpan
    $('#btn-simpan').click(function(){
      $('#btn-simpan').button('loading');
      please_wait(function(){});
      $.ajax({
         type: "POST",
         dataType: "json",
         url :'<?php echo base_url('lab/dti/simpan')?>',
         beforeSend: function(e) {
            if(e && e.overrideMimeType) {
                e.overrideMimeType("application/json;charset=UTF-8");
            }
         },
         data: {tanggal    : $('#tgl').val(),
                warna      : $('#warna').val(),
                note       : $('#note').val(),
                kode_warna : $('#kode_warna').val(),
                status     : 'tambah'

          },success: function(data){
            if(data.sesi == "habis"){
              //alert jika session habis
              alert_modal_warning(data.message);
              window.location.replace('index');
            }else if(data.status == "failed"){
              //jika ada form belum keiisi
              unblockUI( function() {
                setTimeout(function() { alert_notify(data.icon,data.message,data.type, function(){}); }, 1000);
              });
              document.getElementById(data.field).focus();
            }else{
             //jika berhasil disimpan/diubah
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
            alert(xhr.responseText);
            unblockUI( function(){});
            $('#btn-simpan').button('reset');
          }
      });
    });
   
</script>


</body>
</html>
