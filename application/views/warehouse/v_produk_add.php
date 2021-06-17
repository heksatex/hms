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
                    <div class="col-xs-2"><label>Kode Produk </label></div>
                    <div class="col-xs-6">
                      <input type="text" class="form-control input-sm" name="kodeproduk" id="kodeproduk"/>
                    </div>                                    
                    <div class="col-xs-2">
                      <input type="checkbox" name="auto_generate" id="auto_generate">
                      <label>Auto Generate</label>
                    </div>                                    
                  </div>
                  <div class="col-md-12 col-xs-12">
                    <div class="col-xs-2"><label>Nama Produk </label></div>
                    <div class="col-xs-8">
                      <input type="text" class="form-control input-sm" name="namaproduk" id="namaproduk">
                    </div>                                    
                  </div>
                  <div class="col-md-12 col-xs-12">
                    <div class="col-xs-8">                      
                      <input type="checkbox" name="dapat_dijual" id="dapatdijual" value="true">
                      <label>Dapat Dijual</label>
                    </div>
                    <div class="col-xs-8">                      
                      <input type="checkbox" name="dapat_dibeli" id="dapatdibeli" value="true">
                      <label>Dapat Dibeli</label>
                    </div>
                      <!--
                      <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="dapatdijual" checked value="vdapatdijual"> Dapat Dijual
                        <br>                              
                        <input type="checkbox" class="custom-control-input" id="dapatdibeli" checked value="vdapatdibeli"> Dapat Dibeli
                      </div>
                      -->
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
                  <li class="active"><a href="#tab_1" data-toggle="tab">Konfigurasi Umum</a></li>
                  <li><a href="#tab_2" data-toggle="tab">Persediaan</a></li>
                  <li><a href="#tab_3" data-toggle="tab">Pembelian</a></li>                  
                </ul>             
                <div class="tab-content"><br>

                  <!-- tab1 Info Produk -->
                  <div class="tab-pane active" id="tab_1">
                    <div class="col-md-12">
                      <form class="form-horizontal">

                        <!-- konfigurasi umum -->
                        <div class="col-md-12">
                          <p class="text-light-blue"><strong>Konfigurasi Umum</strong></p>
                        </div>
                        <!-- kiri -->
                        <div class="col-md-6">
                          <div class="form-group">
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-4">Type</div>
                              <div class="col-xs-4">
                                <select class="form-control input-sm" name="typeproduk" id="typeproduk" />
                                  <?php 
                                  $val = array('stockable','consumable');
                                  for($i=0;$i<=1;$i++) {
                                    if($val[$i] == "Stockable"){?>
                                      <option selected><?php echo $val[$i];?></option>
                                    <?php
                                      }else{?>
                                      <option><?php echo $val[$i];?></option>
                                    <?php  }
                                  }?>
                                </select>
                              </div>               
                            </div>
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-4">UOM/Satuan</div>
                              <div class="col-xs-4">
                                <select class="form-control input-sm" name="uom" id="uomproduk" />
                                  <option value=""></option>
                                    <?php foreach ($uom as $row) {?>
                                      <option value='<?php echo $row->short; ?>'><?php echo $row->short;?></option>
                                    <?php  }?>
                                </select>
                              </div>               
                            </div>
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-4">UOM/Satuan 2</div>
                              <div class="col-xs-4">
                                <select class="form-control input-sm" name="uom2" id="uomproduk2" />
                                  <option value=""></option>
                                    <?php foreach ($uom as $row) {?>
                                      <option value='<?php echo $row->short; ?>'><?php echo $row->short;?></option>
                                    <?php  }?>
                                </select>
                              </div>               
                            </div>                            
                          </div>
                        </div>
                        <!-- kanan -->
                        <div class="col-md-6">
                          <div class="form-group">
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-4">Kategori Barang</div>
                              <div class="col-xs-8">
                                <select class="form-control input-sm" name="kategoribarang" id="kategoribarang" />
                                  <option value=""></option>
                                  <?php foreach ($category as $row) {?>
                                    <option value='<?php echo $row->id; ?>'><?php echo $row->nama_category;?></option>
                                  <?php  }?>
                                </select>
                              </div>               
                            </div>
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-4">Route Produksi</div>
                              <div class="col-xs-8">
                                <select class="form-control input-sm" name="routeproduksi" id="routeproduksi" />
                                  <option value=""></option>
                                  <?php foreach ($route as $row) {?>
                                    <option value='<?php echo $row->nama_route; ?>'><?php echo $row->nama_route;?></option>
                                  <?php  }?>
                                </select>                                
                              </div>               
                            </div>
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-4">BoM</div>
                              <div class="col-xs-8">
                                <select class="form-control input-sm" name="bom" id="bom">
                                  <option value="1">True</option>
                                  <option value="0">False</option>
                                </select>
                              </div>
                            </div>                            
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-4">Tanggal Dibuat</div>
                              <div class="col-xs-8 col-md-8">
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
                        
                        <!-- gl accounts -->
                        <div class="col-md-12">
                          <p class="text-light-blue"><strong>GL Accounts</strong></p>
                        </div>
                        <!-- kiri -->
                        <div class="col-md-6">
                          <div class="form-group">
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-4">Sales Account</div>
                              <div class="col-xs-8">
                                <select type="text" class="form-control input-sm glacc" name="salesacc" id="salesacc">
                                </select>
                              </div>               
                            </div>
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-4">Inventory Account</div>
                              <div class="col-xs-8">
                                <select type="text" class="form-control input-sm glacc" name="invacc" id="invacc">
                                </select>
                              </div>               
                            </div>
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-4">C.O.G.S Account</div>
                              <div class="col-xs-8">
                                <select type="text" class="form-control input-sm glacc" name="cogsacc" id="cogsacc">
                                </select>
                              </div>               
                            </div>
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-4">Adjustment Account</div>
                              <div class="col-xs-8">
                                <select type="text" class="form-control input-sm glacc" name="adjacc" id="adjacc">
                                </select>
                              </div>               
                            </div>
                          </div>
                        </div>
                        <!-- kanan -->
                        <div class="col-md-6">
                        </div>

                        <!-- lainnya -->
                        <div class="col-md-12">
                          <p class="text-light-blue"><strong>Lainnya</strong></p>
                        </div>
                        <!-- kiri -->
                        <div class="col-md-6">
                          <div class="form-group">
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-4">Status</div>
                              <div class="col-xs-4">
                                <select class="form-control input-sm" name="status" id="status">
                                  <?php 
                                    $val = array('Aktif','Tidak Aktif');
                                    for($i=0;$i<=1;$i++) {
                                      if($val[$i] == "Aktif"){?>
                                        <option selected><?php echo $val[$i];?></option>
                                      <?php
                                        }else{?>
                                        <option><?php echo $val[$i];?></option>
                                      <?php  }
                                  }?>
                                </select>                 
                              </div>               
                            </div>                            
                          </div>
                        </div>
                        <!-- kanan -->
                        <div class="col-md-6">
                        </div>

                        <!-- reff note -->
                        <div class="col-md-12">
                          <p class="text-light-blue"><strong>Notes</strong></p>
                        </div>
                        <!-- kiri -->
                        <div class="col-md-6">
                          <div class="form-group">
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-4">Deskripsi Barang</div>
                              <div id="ta" class="col-md-8 col-xs-8">
                                <textarea class="form-control input-sm" name="note" id="note"></textarea>
                              </div>
                            </div>
                          </div>
                        </div>

                      </form>
                    </div>
                  </div>
                  <!-- tab1 Info Produk -->

                  <!-- tab2 Inventory -->
                  <div class="tab-pane" id="tab_2">
                    <div class="col-md-12">
                      <form class="form-horizontal">
                        <!-- kiri -->
                        <div class="col-md-6">
                          <div class="form-group">
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-4">Qty On Hand</div>
                              <div class="col-xs-4">
                                <input type="text" class="form-control input-sm" name="qtyonhand" id="qtyonhand"/>
                              </div>               
                              <div class="col-xs-4">
                                <a href="#" class="add"><i class="fa fa-long-arrow-right"></i> details</a>
                              </div>                                
                            </div>
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-4">Incoming</div>
                              <div class="col-xs-4">
                                <input type="text" class="form-control input-sm" name="incoming" id="incoming"/>
                              </div>               
                              <div class="col-xs-4">
                                <a href="#" class="add"><i class="fa fa-long-arrow-right"></i> details</a>
                              </div>                                
                            </div>
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-4">Sold</div>
                              <div class="col-xs-4">
                                <input type="text" class="form-control input-sm" name="sold" id="sold"/>
                              </div>               
                            </div>
                          </div>
                        </div>
                        <!-- kanan -->
                        <div class="col-md-6">
                          <div class="form-group">
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-4">Reorder Level</div>
                              <div class="col-xs-4">
                                <input type="text" class="form-control input-sm" name="reorderlevel" id="reorderlevel"/>
                              </div>               
                            </div>
                          </div>
                        </div>
                      </form>
                    </div>
                  </div>                  
                  <!-- tab2 Inventory -->

                  <!-- tab3 Pembelian -->
                  <div class="tab-pane" id="tab_3">
                    <div class="col-md-12">
                      <form class="form-horizontal">
                        <!-- kiri -->
                        <div class="col-md-6">
                          <div class="form-group">
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-4">Metode Costing</div>
                              <div class="col-xs-4">
                                <select class="form-control input-sm" name="metodecosting" id="metodecosting" disabled="true">                                  
                                  <option>Average Price</option>
                                </select>                 
                              </div>                                             
                            </div>
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-4">Real Price</div>
                              <div class="col-xs-4">
                                <input type="text" class="form-control input-sm" name="realprice" id="realprice"/>
                              </div>                                             
                            </div>
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-4">Cost Price</div>
                              <div class="col-xs-4">
                                <input type="text" class="form-control input-sm" name="costprice" id="costprice"/>
                              </div>               
                            </div>
                          </div>
                        </div>
                        <!-- kanan -->
                        <div class="col-md-6">
                          <div class="form-group">
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-4">UOM Pembelian</div>
                              <div class="col-xs-4">
                                <select class="form-control input-sm" name="uompembelian" id="uompembelian">
                                  <option value="">-- Pilih Satuan --</option>
                                    <?php foreach ($uom as $row) {?>
                                      <option value='<?php echo $row->short; ?>'><?php echo $row->short;?></option>
                                    <?php  }?>
                                </select>                 
                              </div>                                             
                            </div>
                          </div>
                        </div>
                      </form>
                    </div>
                  </div>                  
                  <!-- tab3 Pembelian -->

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
  
  autogenerate_value = 0;

  //auto generate
  $('#auto_generate').change(function(){
    var auto_generate   = $('#auto_generate').is(":checked");    
    if (auto_generate == true) {
      autogenerate_value = 1;      
      $('#kodeproduk').val('');
      $('#kodeproduk').attr('disabled','disabled');
    }else{
      autogenerate_value = 0;
      $('#kodeproduk').removeAttr('disabled');
      $('#kodeproduk').val('');
    }    
  });

  //select2 glacc
  $('.glacc').select2({
    allowClear: true,
    placeholder: "",
    ajax:{
          dataType: 'JSON',
          type : "POST",
          url : "<?php echo base_url();?>warehouse/produk/get_coa_list",
          //delay : 250,
          data : function(params){
            return{
              glacc:params.term,
            };
          }, 
          processResults:function(data){
            var results = [];

            $.each(data, function(index,item){
                results.push({        
                    id:item.kode_coa,            
                    text:item.nama_coa
                });
            });
            return {
              results:results
            };
          },
          error: function (xhr, ajaxOptions, thrownError){
            //alert('Error data');
            alert(xhr.responseText);
          }
    }
  });

  //klik button simpan
    $('#btn-simpan').click(function(){
      $('#btn-simpan').button('loading');
      
      var dapatdijual   = $('#dapatdijual').is(":checked");
      if (dapatdijual == true) {
        dapatdijual_value = 1;
      }else{
        dapatdijual_value = 0;
      }
      
      var dapatdibeli   = $('#dapatdibeli').is(":checked");
      if (dapatdibeli == true) {
        dapatdibeli_value = 1;
      }else{
        dapatdibeli_value = 0;
      }

      please_wait(function(){});
      $.ajax({
         type: "POST",
         dataType: "json",
         url :'<?php echo base_url('warehouse/produk/simpan')?>',
         beforeSend: function(e) {
            if(e && e.overrideMimeType) {
                e.overrideMimeType("application/json;charset=UTF-8");
            }
         },
         data: {kodeproduk      : $('#kodeproduk').val(),
                namaproduk      : $('#namaproduk').val(),
                dapatdijual     : dapatdijual_value,
                dapatdibeli     : dapatdibeli_value,
                typeproduk      : $('#typeproduk').val().toLowerCase(),
                uomproduk       : $('#uomproduk').val(),
                uomproduk2      : $('#uomproduk2').val(),
                kategoribarang  : $('#kategoribarang').val(),
                routeproduksi   : $('#routeproduksi').val(),
                bom             : $('#bom').val(),
                tanggaldibuat   : $('#tgldibuat').val(),
                note            : $('#note').val(),
                status          : 'tambah',
                autogenerate    : autogenerate_value,

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
