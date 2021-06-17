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
                        <!-- kiri -->
                        <div class="col-md-6">
                          <div class="form-group">
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-8">Sales Contract</div>
                              <div class="col-xs-4">
                                <!--<input type="checkbox" name="chk_salescontract" id="chk_salescontract" checked="checked" value="true">-->
                                <input type="checkbox" name="chk[]" value="mms37">
                              </div>               
                            </div>                            
                          </div>
                        </div>
                        <!-- kanan -->
                        <div class="col-md-6">
                          <div class="form-group">
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-8">Customer</div>
                              <div class="col-xs-4">
                                <input type="checkbox" name="chk[]" value="mms57">
                              </div>               
                            </div>
                          </div>
                        </div>
                        
                        <!-- ppic -->
                        <div class="col-md-12">
                          <p class="text-light-blue"><strong>PPIC</strong></p>
                        </div>
                        <!-- kiri -->
                        <div class="col-md-6">
                          <div class="form-group">
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-8">Order Planning</div>
                              <div class="col-xs-4">                                
                                <input type="checkbox" name="chk[]" value="mms38">
                              </div>               
                            </div>
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-8">Procurement Purchase</div>
                              <div class="col-xs-4">
                                <input type="checkbox" name="chk[]" value="mms50">
                              </div>               
                            </div>
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-8">BoM</div>
                              <div class="col-xs-4">
                                <input type="checkbox" name="chk[]" value="mms73">
                              </div>               
                            </div>                              
                          </div>
                        </div>
                        <!-- kanan -->
                        <div class="col-md-6">
                          <div class="form-group">
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-8">Procurement Order</div>
                              <div class="col-xs-4">
                                <input type="checkbox" name="chk[]" value="mms39">
                              </div>               
                            </div>
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-8">Production Order</div>
                              <div class="col-xs-4">
                                <input type="checkbox" name="chk[]" value="mms17">
                              </div>               
                            </div>
                          </div>
                        </div>

                        <!-- manufacturing -->
                        <div class="col-md-12">
                          <p class="text-light-blue"><strong>Manufacturing</strong></p>
                        </div>
                        <!-- kiri -->
                        <div class="col-md-6">
                          <div class="form-group">
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-8">MO Cutting Shearing</div>
                              <div class="col-xs-4">                                
                                <input type="checkbox" name="chk[]" value="mms7">
                              </div>               
                            </div>
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-8">MO Jacquard</div>
                              <div class="col-xs-4">                                
                                <input type="checkbox" name="chk[]" value="mms4">
                              </div>               
                            </div>
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-8">MO Tricot</div>
                              <div class="col-xs-4">
                                <input type="checkbox" name="chk[]" value="mms5">
                              </div>               
                            </div>
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-8">MO Warping Dasar</div>
                              <div class="col-xs-4">
                                <input type="checkbox" name="chk[]" value="mms2">
                              </div>               
                            </div>
                          </div>
                        </div>
                        <!-- kanan -->
                        <div class="col-md-6">
                          <div class="form-group">
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-8">MO Inspecting 1</div>
                              <div class="col-xs-4">
                                <input type="checkbox" name="chk[]" value="mms8">
                              </div>               
                            </div>
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-8">MO Raschel</div>
                              <div class="col-xs-4">
                                <input type="checkbox" name="chk[]" value="mms6">
                              </div>               
                            </div>                            
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-8">MO Twisting</div>
                              <div class="col-xs-4">                                
                                <input type="checkbox" name="chk[]" value="mms1">
                              </div>               
                            </div>                            
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-8">MO Warping Panjang</div>
                              <div class="col-xs-4">                                
                                <input type="checkbox" name="chk[]" value="mms3">
                              </div>               
                            </div>                            
                          </div>
                        </div>

                        <!-- warehouse -->
                        <div class="col-md-12">
                          <p class="text-light-blue"><strong>Warehouse</strong></p>
                        </div>
                        <!-- kiri -->
                        <div class="col-md-6">
                          <div class="form-group">
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-8">Cutting Shearing</div>
                              <div class="col-xs-4">
                                <input type="checkbox" name="chk[]" value="mms40,mms41">
                              </div>               
                            </div>
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-8">Gudang Greige</div>
                              <div class="col-xs-4">
                                <input type="checkbox" name="chk[]" value="mms33,mms42">
                              </div>               
                            </div>
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-8">Jacquard</div>
                              <div class="col-xs-4">
                                <input type="checkbox" name="chk[]" value="mms18,mms19">
                              </div>               
                            </div>
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-8">Receiving</div>
                              <div class="col-xs-4">
                                <input type="checkbox" name="chk[]" value="mms53,mms54,mms71">
                              </div>               
                            </div>
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-8">Stock Quants</div>
                              <div class="col-xs-4">
                                <input type="checkbox" name="chk[]" value="mms52">
                              </div>               
                            </div>
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-8">Twisting</div>
                              <div class="col-xs-4">
                                <input type="checkbox" name="chk[]" value="mms43,mms44">
                              </div>               
                            </div>
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-8">Warping Panjang</div>
                              <div class="col-xs-4">
                                <input type="checkbox" name="chk[]" value="mms47,mms48">
                              </div>               
                            </div>
                          </div>
                        </div>
                        <!-- kanan -->
                        <div class="col-md-6">
                          <div class="form-group">
                            <div class="col-md-12 col-xs-12">                              
                              <div class="col-xs-8">Gudang Benang</div>
                              <div class="col-xs-4">
                                <input type="checkbox" name="chk[]" value="mms49,mms51">
                              </div>               
                            </div>                            
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-8">Inspecting 1</div>
                              <div class="col-xs-4">
                                <input type="checkbox" name="chk[]" value="mms23,mms24">
                              </div>               
                            </div>
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-8">Produk</div>
                              <div class="col-xs-4">
                                <input type="checkbox" name="chk[]" value="mms56">
                              </div>               
                            </div>                            
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-8">Stock Moves</div>
                              <div class="col-xs-4">
                                <input type="checkbox" name="chk[]" value="mms55">
                              </div>               
                            </div>                            
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-8">Tricot</div>
                              <div class="col-xs-4">
                                <input type="checkbox" name="chk[]" value="mms14,mms15">
                              </div>               
                            </div>
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-8">Warping Dasar</div>
                              <div class="col-xs-4">
                                <input type="checkbox" name="chk[]" value="mms45,mms46">
                              </div>               
                            </div>
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-8">Adjustment</div>
                              <div class="col-xs-4">
                                <input type="checkbox" name="chk[]" value="mms72">
                              </div>               
                            </div>
                          </div>
                        </div>


                        <!-- report -->
                        <div class="col-md-12">
                          <p class="text-light-blue"><strong>Report</strong></p>
                        </div>
                        <!-- kiri -->
                        <div class="col-md-6">
                          <div class="form-group">
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-8">Print MO</div>
                              <div class="col-xs-4">
                                <input type="checkbox" name="chk[]" value="mms74">
                              </div>               
                            </div>  
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-8">Cacat</div>
                              <div class="col-xs-4">
                                <input type="checkbox" name="chk[]" value="mms75">
                              </div>               
                            </div>
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-8">Efisiensi</div>
                              <div class="col-xs-4">
                                <input type="checkbox" name="chk[]" value="mms80">
                              </div>               
                            </div>
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-8">Rekap Cacat</div>
                              <div class="col-xs-4">
                                <input type="checkbox" name="chk[]" value="mms81">
                              </div>               
                            </div>
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-8">Adjustment</div>
                              <div class="col-xs-4">
                                <input type="checkbox" name="chk[]" value="mms86">
                              </div>               
                            </div>                            
                          </div>
                        </div>

                        <!-- kanan -->
                        <div class="col-md-6">
                          <div class="form-group">
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-8">Produksi Warping Dasar</div>
                              <div class="col-xs-4">
                                <input type="checkbox" name="chk[]" value="mms76">
                              </div>               
                            </div>    
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-8">HPH Warping Dasar</div>
                              <div class="col-xs-4">
                                <input type="checkbox" name="chk[]" value="mms77">
                              </div>               
                            </div>       
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-8">Produksi Tricot</div>
                              <div class="col-xs-4">
                                <input type="checkbox" name="chk[]" value="mms78">
                              </div>               
                            </div>    
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-8">HPH Tricot</div>
                              <div class="col-xs-4">
                                <input type="checkbox" name="chk[]" value="mms79">
                              </div>               
                            </div>  
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-8">Produksi Warping Panjang</div>
                              <div class="col-xs-4">
                                <input type="checkbox" name="chk[]" value="mms82">
                              </div>               
                            </div>    
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-8">HPH Warping Panjang</div>
                              <div class="col-xs-4">
                                <input type="checkbox" name="chk[]" value="mms83">
                              </div>               
                            </div> 
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-8">Produksi Jacquard</div>
                              <div class="col-xs-4">
                                <input type="checkbox" name="chk[]" value="mms84">
                              </div>               
                            </div>    
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-8">HPH Jacquard</div>
                              <div class="col-xs-4">
                                <input type="checkbox" name="chk[]" value="mms85">
                              </div>               
                            </div>                     
                          </div>
                        </div>

                        <!-- setting -->
                        <div class="col-md-12">
                          <p class="text-light-blue"><strong>Setting</strong></p>
                        </div>
                        <!-- kiri -->
                        <div class="col-md-6">
                          <div class="form-group">
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-8">User Manajemen</div>
                              <div class="col-xs-4">
                                <input type="checkbox" name="chk[]" value="mms90">
                              </div>               
                            </div>                            
                          </div>
                        </div>
                        <!-- kanan -->
                        <div class="col-md-6">
                          <div class="form-group">
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-8">Ganti Password</div>
                              <div class="col-xs-4">
                                <input type="checkbox" name="chk[]" value="mms91">
                              </div>               
                            </div>                            
                          </div>
                        </div>

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
