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
              <div class="col-md-12">
                <div class="col-md-12 col-xs-12">                  
                  <div class="col-md-12 col-xs-12">
                    <div class="col-xs-2"><label>Nama User </label></div>
                    <div class="col-xs-6">
                      <input type="text" class="form-control input-sm" name="namauser" id="namauser"/>
                    </div>                                                        
                  </div>
                  <div class="col-md-12 col-xs-12">
                    <div class="col-xs-2"><label>Login </label></div>
                    <div class="col-xs-6">
                      <input type="text" class="form-control input-sm" name="login" id="login">
                    </div>
                  </div>
                  <div class="col-md-12 col-xs-12">
                    <div class="col-xs-2"><label>Tanggal Dibuat </label></div>
                    <div class="col-xs-3">
                      <div class='input-group date' id='tanggaldibuat' >
                        <input type='text' class="form-control input-sm" name="tgldibuat" id="tgldibuat" readonly="readonly" />
                        <span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar"></span>
                        </span>
                      </div>
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
                <ul class="nav nav-tabs">
                  <li class="active"><a href="#tab_1" data-toggle="tab">Hak Akses</a></li>                  
                </ul>             
                <div class="tab-content"><br>

                  <!-- tab1 Hak Akses -->
                  <div class="tab-pane active" id="tab_1">
                    <div class="col-md-12">
                      <form class="form-horizontal">

                        <!-- sales -->
                        <div class="col-md-12">
                          <p class="text-light-blue"><strong>Sales</strong></p>
                        </div>
                          
                            <?php 
                              // set jml baris
                              $jml_kolom = $count_sales/2;
                              $jml_baris = intval($jml_kolom);
                              $count     = 1;
                              $tambah_kolom = TRUE; 
                              foreach ($sales as $val) {

                                if($count == 1){

                                  echo '<div class="col-md-6">';
                                  echo '<div class="form-group">';
                                }else if($count > $jml_baris AND $tambah_kolom == TRUE){
                                  echo '<div class="col-md-6">';
                                  echo '<div class="form-group">';
                                  $tambah_kolom = FALSE;
                                }

                                $kode    = $val->kode.',';
                                $nama = $val->nama;
                            ?>
                                <div class="col-md-12 col-xs-12">
                                  <div class="col-xs-8"><?php echo $nama; ?></div>
                                  <div class="col-xs-4">                                                                
                                    <input type="checkbox" name="chk[]"  value="<?php echo $val->kode; ?>">
                                  </div>               
                                </div>
                            <?php
                                if($count == $jml_baris){
                                    echo '</div>';
                                    echo '</div>';
                                }

                                $count++;
                              }
                              // penutup div col-md-6, dan form-group
                              echo '</div>';
                              echo '</div>';
                    
                            ?>
                        
                        
                        <!-- ppic -->
                        <div class="col-md-12">
                          <p class="text-light-blue"><strong>PPIC</strong></p>
                        </div>

                          <?php 
                              // set jml baris
                              $jml_kolom = $count_ppic/2;
                              $jml_baris = intval($jml_kolom);
                              $count     = 1;
                              $tambah_kolom = TRUE; 
                              foreach ($ppic as $val) {

                                if($count == 1){

                                  echo '<div class="col-md-6">';
                                  echo '<div class="form-group">';
                                }else if($count > $jml_baris AND $tambah_kolom == TRUE){
                                  echo '<div class="col-md-6">';
                                  echo '<div class="form-group">';
                                  $tambah_kolom = FALSE;
                                }

                                $kode    = $val->kode.',';
                                $nama = $val->nama;
                            ?>
                                <div class="col-md-12 col-xs-12">
                                  <div class="col-xs-8"><?php echo $nama; ?></div>
                                  <div class="col-xs-4">                                                                
                                    <input type="checkbox" name="chk[]"  value="<?php echo $val->kode; ?>">
                                  </div>               
                                </div>
                            <?php
                                if($count == $jml_baris){
                                    echo '</div>';
                                    echo '</div>';
                                }

                                $count++;
                              }
                              // penutup div col-md-6, dan form-group
                              echo '</div>';
                              echo '</div>';
                    
                            ?>
                        

                        <!-- manufacturing -->
                        <div class="col-md-12">
                          <p class="text-light-blue"><strong>Manufacturing</strong></p>
                        </div>

                          <?php 
                              // set jml baris
                              $jml_kolom = $count_mo/2;
                              $jml_baris = intval($jml_kolom);
                              $count     = 1;
                              $tambah_kolom = TRUE; 
                              foreach ($mo as $val) {

                                if($count == 1){

                                  echo '<div class="col-md-6">';
                                  echo '<div class="form-group">';
                                }else if($count > $jml_baris AND $tambah_kolom == TRUE){
                                  echo '<div class="col-md-6">';
                                  echo '<div class="form-group">';
                                  $tambah_kolom = FALSE;
                                }

                                $kode    = $val->kode.',';
                                $nama = $val->nama;
                            ?>
                                <div class="col-md-12 col-xs-12">
                                  <div class="col-xs-8"><?php echo $nama; ?></div>
                                  <div class="col-xs-4">                                                                
                                      <input type="checkbox" name="chk[]"  value="<?php echo $val->kode; ?>">
                                  </div>               
                                </div>
                            <?php
                                if($count == $jml_baris){
                                    echo '</div>';
                                    echo '</div>';
                                }

                                $count++;
                              }
                              // penutup div col-md-6, dan form-group
                              echo '</div>';
                              echo '</div>';
                          ?>

                        <!-- warehouse -->
                        <div class="col-md-12">
                          <p class="text-light-blue"><strong>Warehouse</strong></p>
                        </div>
                       
                        <!-- lab -->
                        <div class="col-md-12">
                          <p class="text-light-blue"><strong>Lab</strong></p>
                        </div>

                          <?php 
                              // set jml baris
                              $jml_kolom = $count_lab/2;
                              $jml_baris = intval($jml_kolom);
                              $count     = 1;
                              $tambah_kolom = TRUE; 
                              foreach ($lab as $val) {

                                if($count == 1){

                                  echo '<div class="col-md-6">';
                                  echo '<div class="form-group">';
                                }else if($count > $jml_baris AND $tambah_kolom == TRUE){
                                  echo '<div class="col-md-6">';
                                  echo '<div class="form-group">';
                                  $tambah_kolom = FALSE;
                                }

                                $kode    = $val->kode.',';
                                $nama = $val->nama;
                            ?>
                                <div class="col-md-12 col-xs-12">
                                  <div class="col-xs-8"><?php echo $nama; ?></div>
                                  <div class="col-xs-4">                                                                
                                    
                                    <input type="checkbox" name="chk[]"  value="<?php echo $val->kode; ?>">
                                  
                                  </div>               
                                </div>
                            <?php
                                if($count == $jml_baris){
                                    echo '</div>';
                                    echo '</div>';
                                }

                                $count++;
                              }
                              // penutup div col-md-6, dan form-group
                              echo '</div>';
                              echo '</div>';
                    
                            ?>


                        <!-- report -->
                        <div class="col-md-12">
                          <p class="text-light-blue"><strong>Report</strong></p>
                        </div>

                          <?php 
                              // set jml baris
                              $jml_kolom = $count_report/2;
                              $jml_baris = intval($jml_kolom);
                              $count     = 1;
                              $tambah_kolom = TRUE; 
                              foreach ($report as $val) {

                                if($count == 1){

                                  echo '<div class="col-md-6">';
                                  echo '<div class="form-group">';
                                }else if($count > $jml_baris AND $tambah_kolom == TRUE){
                                  echo '<div class="col-md-6">';
                                  echo '<div class="form-group">';
                                  $tambah_kolom = FALSE;
                                }

                                $kode    = $val->kode.',';
                                $nama = $val->nama;
                            ?>
                                <div class="col-md-12 col-xs-12">
                                  <div class="col-xs-8"><?php echo $nama; ?></div>
                                  <div class="col-xs-4">                                                                
                                    <input type="checkbox" name="chk[]"  value="<?php echo $val->kode; ?>">
                                  </div>               
                                </div>
                            <?php
                                if($count == $jml_baris){
                                    echo '</div>';
                                    echo '</div>';
                                }

                                $count++;
                              }
                              // penutup div col-md-6, dan form-group
                              echo '</div>';
                              echo '</div>';
                    
                            ?>
                       

                        <!-- setting -->
                        <div class="col-md-12">
                          <p class="text-light-blue"><strong>Setting</strong></p>
                        </div>
                        <?php 
                              // set jml baris
                              $jml_kolom = $count_report/2;
                              $jml_baris = intval($jml_kolom);
                              $count     = 1;
                              $tambah_kolom = TRUE; 
                              foreach ($setting as $val) {

                                if($count == 1){

                                  echo '<div class="col-md-6">';
                                  echo '<div class="form-group">';
                                }else if($count > $jml_baris AND $tambah_kolom == TRUE){
                                  echo '<div class="col-md-6">';
                                  echo '<div class="form-group">';
                                  $tambah_kolom = FALSE;
                                }

                                $kode    = $val->kode.',';
                                $nama = $val->nama;
                            ?>
                                <div class="col-md-12 col-xs-12">
                                  <div class="col-xs-8"><?php echo $nama; ?></div>
                                  <div class="col-xs-4">                                                                
                                    <input type="checkbox" name="chk[]"  value="<?php echo $val->kode; ?>">
                                  </div>               
                                </div>
                            <?php
                                if($count == $jml_baris){
                                    echo '</div>';
                                    echo '</div>';
                                }

                                $count++;
                              }
                              // penutup div col-md-6, dan form-group
                              echo '</div>';
                              echo '</div>';
                    
                            ?>

                      </form>

                    </div>
                  </div>
                  <!-- tab1 Info Produk -->

                </div>   
              </div>                
            </div>            
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

<script type="text/javascript">

  window.onload = function(){//hidden button
    $('#btn-generate').hide();
    $('#btn-cancel').hide();
    $('#btn-print').hide();
  }
  
  //set tgl buat
  var datenow=new Date();  
  datenow.setMonth(datenow.getMonth());
  $('#tanggaldibuat').datetimepicker({
      defaultDate: datenow,
      format : 'YYYY-MM-DD HH:mm:ss',
      ignoreReadonly: true,
  });

  //generate chk yg checked apa saja
  function gen_chk_akses(){
    var arr = $.map($('input:checkbox:checked'), function(e, i) {
      return e.value;
    });
    return arr;
  }

  //klik button simpan
  $('#btn-simpan').click(function(){
    $('#btn-simpan').button('loading');
    
    var arr_chk_akses = gen_chk_akses();

    arr_chk_akses = arr_chk_akses.join(',');

    please_wait(function(){});
      $.ajax({
         type: "POST",
         dataType: "json",
         url :'<?php echo base_url('setting/user/simpan')?>',
         beforeSend: function(e) {
            if(e && e.overrideMimeType) {
                e.overrideMimeType("application/json;charset=UTF-8");
            }
         },
         data: {namauser        : $('#namauser').val(),
                login           : $('#login').val(),
                tanggaldibuat   : $('#tgldibuat').val(),
                arrchkakses     : arr_chk_akses,                
                status          : 'tambah',

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
                setTimeout(function() { 
                  alert_notify(data.icon,data.message,data.type, function(){
                  window.location.replace('edit/'+data.isi);
                },1000); 
                });
              });
              $('#btn-simpan').button('reset');
            }

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
