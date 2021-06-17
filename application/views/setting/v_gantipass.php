
<!DOCTYPE html>
<html>
<head>
  <?php $this->load->view("admin/_partials/head.php") ?>
  <style>
    
    .bs-glyphicons {
      padding-left: 0;
      padding-bottom: 1px;
      margin-bottom: 20px;
      list-style: none;
      overflow: hidden;
    }

    .bs-glyphicons li {
      float: left;
      width: 25%;
      height: 50px;
      padding: 10px;
      margin: 0 -1px -1px 0;
      font-size: 15px;
      line-height: 1.4;
      text-align: elft;
      border: 1px solid #ddd;
    }

    .bs-glyphicons .glyphicon {
      margin-top: 5px;
      margin-bottom: 10px;
      font-size: 20px;
    }

    .bs-glyphicons .glyphicon-class {
      display: block;
      text-align: left;
      word-wrap: break-word; /* Help out IE10+ with class names */
    }

    .bs-glyphicons li:hover {
      background-color: rgba(86, 61, 124, .1);
    }

    @media (min-width: 768px) {
      .bs-glyphicons li {
        width: 50%;
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
          <h3 class="box-title">Ganti Password</h3>
        </div>
        <div class="box-body">
          <form class="form-horizontal">
            
            <div class="form-group">                  
              <div class="col-md-12" >
                <div id="alert"></div>
              </div>
            </div>

            <div class="form-group">
              <div class="col-md-12">
                <div class="col-md-12 col-xs-12">                  
                  <div class="col-md-12 col-xs-12">
                    <div class="col-xs-2"><label>Nama User </label></div>
                    <div class="col-xs-6">
                      <input type="text" class="form-control input-sm" name="namauser" id="namauser" value="<?php echo htmlentities($user->nama) ?>" disabled>                      
                    </div>                                                        
                  </div>
                  <div class="col-md-12 col-xs-12">
                    <div class="col-xs-2"><label>Login </label></div>
                    <div class="col-xs-6">
                      <input type="text" class="form-control input-sm" name="login" id="login" value="<?php echo htmlentities($user->username) ?>" disabled>                      
                    </div>                                                        
                  </div>
                  <div class="col-md-12 col-xs-12">
                    <div class="col-xs-2"><label>Password Lama </label></div>
                    <div class="col-xs-6">
                      <input type="password" class="form-control input-sm" name="password_lama" id="password_lama">
                    </div>
                  </div>
                  <div class="col-md-12 col-xs-12">
                    <div class="col-xs-2"><label>Password Baru </label></div>
                    <div class="col-xs-6">
                      <input type="password" class="form-control input-sm" name="password_baru" id="password_baru">
                    </div>
                  </div>
                  <div class="col-md-12 col-xs-12">
                    <div class="col-xs-2"><label>Ulangi Password Baru </label></div>
                    <div class="col-xs-6">
                      <input type="password" class="form-control input-sm" name="ulangi_password_baru" id="ulangi_password_baru">
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

<script type="text/javascript">

  window.onload = function(){//hidden button
    $('#btn-generate').hide();
    $('#btn-cancel').hide();
    $('#btn-print').hide();
  }

  //klik button simpan
    $('#btn-simpan').click(function(){
      $('#btn-simpan').button('loading');

      please_wait(function(){});
      $.ajax({
         type: "POST",
         dataType: "json",
         url :'<?php echo base_url('setting/ganti_pass/simpan')?>',
         beforeSend: function(e) {
            if(e && e.overrideMimeType) {
                e.overrideMimeType("application/json;charset=UTF-8");
            }
         },
         data: {login                 : $('#login').val(),
                passwordlama          : $('#password_lama').val(),
                passwordbaru          : $('#password_baru').val(),
                ulangipasswordbaru    : $('#ulangi_password_baru').val(),
                status                : 'edit',

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
              document.getElementById(data.field).focus();
            }else{
             //jika berhasil disimpan/diubah
              unblockUI( function() {                
                setTimeout(function() { alert_notify(data.icon,data.message,data.type); }, 1000);
                });
            }
            $('#btn-simpan').button('reset');

          },error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.responseText);
            unblockUI( function(){});
            $('#btn-simpan').button('reset');
          }
      });
        window.setTimeout(function() {
       $(".alert").fadeTo(500, 0).slideUp(500, function(){
        $(this).remove(); });
      }, 3000);
    });
   
</script>


</body>
</html>
