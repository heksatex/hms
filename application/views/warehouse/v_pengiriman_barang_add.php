
<!DOCTYPE html>
<html lang="en">
<head>
  <?php $this->load->view("admin/_partials/head.php") ?>

  <!--style>
    button[id="btn-stok"],button[id="btn-kirim"],button[id="btn-cancel"],button[id="btn-print"]{/*untuk hidden button di top bar pengiriman*/
      display: none;
    }
  </style-->


</head>

<body class="hold-transition skin-black fixed sidebar-mini">
 
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
      <div id ="status_bar">
       <?php 
       
        // $data['jen_status'] = $list->status;
         $data['deptid']     = $id_dept;
         $this->load->view("admin/_partials/statusbar.php", $data); 
       
       ?>
      </div>
    </section>

    <!-- Main content -->
    <section class="content">
      <!--  box content -->
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title"><b></b></h3>
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
                  <div class="col-xs-4"><label>Kode</label></div>
                  <div class="col-xs-8">
                    <input type="text" class="form-control input-sm" name="kode" id="kode" readonly="readonly"/>
                  </div>                                    
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Tanggal dibuat </label></div>
                  <div class="col-xs-8 col-md-8">
                    <input type='text' class="form-control input-sm" name="tgl" id="tgl" value="<?php echo date('Y-m-d H:i:s')?>" readonly="readonly"/>
                  </div>
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Origin </label></div>
                  <div class="col-xs-8 col-md-8">
                    <input type='text' class="form-control input-sm" name="origin" id="origin"  readonly="readonly" />
                  </div>
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Reff Picking </label></div>
                  <div class="col-xs-8 col-md-8">
                    <textarea class="form-control input-sm" name="reff_pick" id="reff_pick" readonly="readonly" ></textarea>
                  </div>
                </div>
             
              </div>

            </div>

            <div class="col-md-6" >
              <div class="form-group" >   

                <div class="col-md-12 col-xs-12" >
                  <div class="col-xs-4"><label>Tanggal Kirim </label></div>
                  <div class="col-xs-8 col-md-8">
                    <input type='text' class="form-control input-sm" name="tgl_transaksi" id="tgl_transaksi" readonly="readonly" value="<?php echo date('Y-m-d H:i:s')?>" />
                  </div>                                    
                </div>
               
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Tanggal Jatuh Tempo </label></div>
                  <div class="col-xs-8 col-md-8">
                      <div class='input-group date' id='tanggal2' >
                        <input type='text' class="form-control input-sm" name="tgl_jt" id="tgl_jt" readonly="readonly"  />
                        <span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar"></span>
                        </span>
                      </div>
                  </div>                                       
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Reff Note </label></div>
                  <div id="ta" class="col-xs-8">
                    <textarea class="form-control input-sm" name="note" id="note" ></textarea>
                  </div>                                    
                </div>

              </div>
            </div>
 
            <div class="row">
              <div class="col-md-12">
                <!-- Custom Tabs -->
                <div class="">
                  <ul class="nav nav-tabs">
                    <li class="active"><a href="#tab_1" data-toggle="tab">Products</a></li>
                    <li><a href="#tab_2" data-toggle="tab">Details</a></li>
                    <li><a href="#tab_3" data-toggle="tab">Stock Move</a></li>
                  </ul>
                  <div class="tab-content"><br>
                    <div class="tab-pane active" id="tab_1">
                      <!-- Tabel  -->
                      <div class="col-md-12 table-responsive">
                        <table class="table table-condesed table-hover rlstable" width="100%" id ="table_prod">
                          <label>Products</label>
                          <tr>
                            <th class="style no">No.</th>
                            <th class="style" style="width: 120px;">Kode Product</th>
                            <th class="style">Product</th>
                            <th class="style" style="text-align: right;">Qty</th>
                            <th class="style">uom</th>
                            <th class="style">Tersedia</th>
                            <th class="style">Status</th>
                          </tr>
                          <tbody>
                
                          </tbody>
                        </table>
                      </div>
                      <!-- Tabel  -->
                    </div>

                    <div class="tab-pane" id="tab_2">
                      <!-- Tabel  -->
                      <div class="col-md-12 table-responsive">
                        <table class="table table-condesed table-hover rlstable" width="100%" id ="table_items">
                          <label>Details Product</label>
                          <tr>
                            <th class="style no">No.</th>
                            <th class="style" style="width: 120px;">Kode Product</th>
                            <th class="style">Product</th>
                            <th class="style">lot</th>
                            <th class="style" style="text-align: right;">Qty</th>
                            <th class="style">uom</th>
                            <th class="style" style="text-align: right;">Qty2</th>
                            <th class="style">uom2 </th>
                            <th class="style">Reff Note</th>
                            <th class="style">Status</th>
                            <th class="style">Quant Id</th>
                            <th class="style no"></th>
                          </tr>
                          <tbody>
                
                          </tbody>
                        </table>
                      </div>
                      <!-- Tabel  -->
                    </div>

                    <div class="tab-pane" id="tab_3">
                       <div class="col-md-12"><label>Informasi Stock Move</label></div>
                        <div class="col-md-6">
                          <div class="form-group"> 
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-4"><label>Move Id</label></div>
                              <div class="col-xs-8">
                                 <?php echo ": "?>
                              </div>                                    
                            </div>
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-4"><label>Create Date </label></div>
                              <div class="col-xs-8 col-md-8">
                                 <?php echo ": "?>
                              </div>
                            </div>
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-4"><label>Origin </label></div>
                              <div class="col-xs-8 col-md-8">
                                  <?php echo ": "?>
                              </div>
                            </div>
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-4"><label>Method</label></div>
                              <div class="col-xs-8 col-md-8">
                                 <?php echo ": "?>
                              </div>
                            </div>
                          </div>
                        </div>
                         <div class="col-md-6">
                          <div class="form-group"> 
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-4"><label>Lokasi Dari</label></div>
                              <div class="col-xs-8">
                                 <?php echo ": "?>
                              </div>                                    
                            </div>
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-4"><label>Lokasi Tujuan </label></div>
                              <div class="col-xs-1 col-md-1">
                                <?php echo ": "?>
                              </div>
                              <div class="col-xs-6 col-md-6">
                                <select class="form-control input-sm" name="lokasi_tujuan" id="lokasi_tujuan" required="">
                                  <?php
                                      echo '<option value="">Pilih Lokasi Tujuan</option>';
                                      foreach ($warehouse as $row) {   ?>
                                        <option value='<?php echo $row->kode; ?>'><?php echo $row->nama;?></option>
                                    <?php  
                                      }
                                  ?>
                                </select>

                              </div>
                            </div>
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-4"><label>Status </label></div>
                              <div class="col-xs-8 col-md-8">
                                <?php echo ": "?>
                              </div>
                            </div>
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-4"><label>MO </label></div>
                              <div class="col-xs-8 col-md-8">
                                 <?php echo ": "?>
                              </div>
                            </div>
                          </div>
                        </div>
                    </div>
                    <!-- /.tab-pane -->
              
                  </div>
                  <!-- /.tab-content -->
                </div>
                <!-- nav-tabs-custom -->
              </div>
              <!-- /.col -->
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
  //var i = 2;
 
  var datenow=new Date();  
  datenow.setMonth(datenow.getMonth());
  /*
  $('#tanggal').datetimepicker({
      defaultDate: datenow,
      format : 'YYYY-MM-DD HH:mm:ss',
      ignoreReadonly: true,
  });
 */ 

  $('#tanggal2').datetimepicker({
      defaultDate: datenow,
      format : 'YYYY-MM-DD HH:mm:ss',
      ignoreReadonly: true,
  });


  //klik button simpan
  $("#btn-simpan").unbind( "click" );
  $('#btn-simpan').click(function(){

    var lokasi_tujuan = $('#lokasi_tujuan').val();

    if(lokasi_tujuan == ''){
      $('.nav.nav-tabs li,.tab-pane').removeClass('active');
      $('.nav.nav-tabs li:eq(2)').addClass('active');
      $('.tab-content .tab-pane:eq(2)').addClass('active')
      $('#lokasi_tujuan').focus();
      alert_notify('fa fa-warning','Lokasi Tujuan Harus Diisi !','danger',function(){});

    }else{

      var deptid = "<?php echo $id_dept; ?>"//parsing data id dept untuk log history
      $('#btn-simpan').button('loading');
      please_wait(function(){});
      var baseUrl = '<?php echo base_url(); ?>';
      $.ajax({
         type: "POST",
         dataType: "json",
         url : '<?php echo base_url('warehouse/pengirimanbarang/simpan')?>',
         data: { kode:$('#kode').val(), tgl_transaksi:$('#tgl_transaksi').val(), tgl_jt:$('#tgl_jt').val(), reff_note:$('#note').val(), deptid:deptid, lokasi_tujuan:lokasi_tujuan, type:'1' },
         success: function(data){
            if(data.sesi == "habis"){
              //alert jika session habis
              alert_modal_warning(data.message);             
              window.location.href = baseUrl;//replace ke halaman login
            }else if(data.status == "failed"){
              //jika ada form belum keisi
              unblockUI( function() {
                setTimeout(function() { alert_notify(data.icon,data.message,data.type,function(){}); }, 1000);
              });
             $('#btn-simpan').button('reset');
            }else if(data.status == "ada"){
              alert_modal_warning(data.message);
              unblockUI( function() {});
              $('#btn-simpan').button('reset');
            }else{
              //jika berhasil disimpan
              $('#kode').val(data.isi);
              unblockUI( function() {
                setTimeout(function() { 
                  alert_notify(data.icon,data.message,data.type, function(){
                   
                  window.location.replace('edit/'+data.kode_encrypt);
                },1000); 
                });
              });
             
              $('#btn-simpan').button('reset');
            }

          },error: function (xhr, ajaxOptions, thrownError) { 
            alert(xhr.responseText);
            setTimeout($.unblockUI, 1000); 
            unblockUI( function(){});
            $('#btn-simpan').button('reset');
          }
      });

    }


  });

</script>


</body>
</html>
