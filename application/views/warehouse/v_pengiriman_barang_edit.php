
<!DOCTYPE html>
<html lang="en">
<head>
  <?php $this->load->view("admin/_partials/head.php") ?>
  <style type="text/css">
    
    table.table td .add {
        display: none;
    }
    .width-btn {
      width: 54px !important;
    }
    table.table td .cancel {
        display: none;
        color : red;
        margin: 10 0px;
        min-width:  24px;
    }
  </style>
</head>

<body class="hold-transition skin-black fixed sidebar-mini">

  <!--jika javascript browser tidak aktif-->
  <noscript class="noscript">
    <div id="javascript-notice">
      <h3>Java Script Tidak Aktif !</h3>
      <label>Harap Aktifakn Javascript di Browser Anda, untuk mengakses halaman ini !! </label>
    </div>
  </noscript>

<!-- Site wrapper -->
<div class="wrapper">

  <!-- main -header -->
  <header class="main-header">
   <?php $this->load->view("admin/_partials/main-menu.php") ?>
   <?php 
     $data['deptid']     = $list->dept_id;
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
       
         $data['jen_status'] = $list->status;
         $data['deptid']     = $list->dept_id;
         $this->load->view("admin/_partials/statusbar.php", $data); 
       
       ?>
      </div>
    </section>

    <!-- Main content -->
    <section class="content">
      <!--  box content -->
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title"><b><?php echo $list->kode;?></b></h3>
          <div class="image pull-right text-right">
            <a href="<?php echo base_url('warehouse/pengirimanbarang/edit_barcode/'.encrypt_url($list->kode));?>" data-toggle="tooltip" title="Barcode Mode"> 
              <img src="<?php echo base_url('dist/img/barcode-scan-icon.jpg'); ?>" style="width: 50%; height: auto; text-align: right;">
            </a>
          </div>
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
                    <input type="text" class="form-control input-sm" name="kode" id="kode" value="<?php echo $list->kode;?>" readonly="readonly"/>
                  </div>                                    
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Tanggal dibuat </label></div>
                  <div class="col-xs-8 col-md-8">
                    <input type='text' class="form-control input-sm" name="tgl" id="tgl" value="<?php echo $list->tanggal;?>" readonly="readonly" />
                  </div>
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Origin </label></div>
                  <div class="col-xs-8 col-md-8">
                    <input type='text' class="form-control input-sm" name="origin" id="origin" value="<?php echo $list->origin;?>" readonly="readonly" />
                  </div>
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Reff Picking </label></div>
                  <div class="col-xs-8 col-md-8">
                    <textarea class="form-control input-sm" name="reff_pick" id="reff_pick" readonly="readonly" ><?php echo $list->reff_picking; ?></textarea>
                  </div>
                </div>
             
              </div>

            </div>

            <div class="col-md-6" >
              <div class="form-group" >   

                <div class="col-md-12 col-xs-12" >
                  <div class="col-xs-4"><label>Tanggal Kirim </label></div>
                  <div class="col-xs-8 col-md-8">
                    <div id="tgl_btn">
                      <?php if($list->status=='draft' or $list->status =='ready') {
                          $tgl_kirim = date('Y-m-d H:i:s');
                        ?>
                      <?php }else{
                          $tgl_kirim = $list->tanggal_transaksi;
                            }
                        ?>
                        <input type='text' class="form-control input-sm" name="tgl_transaksi" id="tgl_transaksi"  value="<?php echo $tgl_kirim?>" readonly="readonly" />
                    </div>
                  </div>                                    
                </div>
               
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Tanggal Jatuh Tempo </label></div>
                  <div class="col-xs-8">
                    <input type='text' class="form-control input-sm" name="tgl_jt" id="tgl_jt"  readonly="readonly"   value="<?php echo $list->tanggal_jt;?>"/>
                  </div>                                    
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Reff Note </label></div>
                  <div id="ta" class="col-xs-8">
                    <textarea class="form-control input-sm" name="note" id="note" ><?php echo $list->reff_note; ?></textarea>
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
                            <th class="style" style="text-align: right;width: 100px;">Qty</th>
                            <th class="style">uom</th>
                            <th class="style">Tersedia</th>
                            <th class="style">Status</th>
                            <th class="style" style="width: 80px"></th>

                          </tr>
                          <tbody id="tbody_products">
                            <?php
                              foreach ($items as $row) {
                                if($row->sum_qty > $row->qty) $color = "red"; else if($row->sum_qty < $row->qty) $color ='blue' ; else $color="black";
                            ?>  
                              <tr class="num">
                                <td data-content="edit" data-id="row_order" data-isi="<?php echo $row->row_order."^|".$row->kode_produk."^|".$smove['move_id']."^|".$row->origin_prod?>"></td>
                                <td><?php echo $row->kode_produk;?></td>
                                <td>
                                  <?php if($smove['method']!="GRG|OUT" OR $row->status_barang=='done'){echo $row->nama_produk;}else{?>
                                  <a href="javascript:void(0)" onclick="tambah('<?php echo htmlentities($row->nama_produk); ?>', '<?php echo $row->kode_produk ?>','<?php echo $list->move_id ?>')"><?php echo $row->nama_produk?></a>
                                  <?php }?>
                                  <?php if($row->status_barang == 'done' OR $row->status_barang == 'cancel'){echo "";}else{?>
                                   <a href="javascript:void(0)" onclick="tambah_quant('<?php echo htmlentities($row->nama_produk); ?>','<?php echo $row->kode_produk ?>','<?php echo $list->move_id ?>','<?php echo $row->origin_prod?>')" data-toggle="tooltip" title="Tambah Quant">
                                     <span class="glyphicon  glyphicon-share"></span></a>
                                  <?php }?>
                                </td>
                                <td data-content="edit" data-id="qty" data-isi="<?php echo $row->qty;?>"  align="right"><?php echo number_format($row->qty,2)?></td>
                                <td><?php echo $row->uom?></td>
                                <td style="color:<?php echo $color;?>"><?php if($row->sum_qty) echo number_format($row->sum_qty,2)?></td>
                                <td><?php if($row->status_barang == 'cancel') echo 'Batal';  else echo $row->status_barang;?></td>
                                <td align="center">
                                  <?php if(($row->status_barang == 'draft' OR $row->status_barang == 'ready') AND $list->type_created == 1) {?>
                                      <a href="javascript:void(0)" class="add" title="Simpan" data-toggle="tooltip" ><i class="fa fa-save"></i></a>
                                      <a href="javascript:void(0)" class="edit" title="Edit" data-toggle="tooltip" style="color: #FFC107;   margin-right: 24px;"><i class="fa fa-edit"></i></a>
                                      <a href="javascript:void(0)" class="delete" title="Hapus" data-toggle="tooltip"><i class="fa fa-trash" style="color: red"></i></a>
                                      <a href="javascript:void(0)" class="cancel" title="Cancel" data-toggle="tooltip" style="margin-left: 20px;"><i class="fa fa-close"></i></a>
                                   <?php } ?>
                                </td>
                              </tr>
                            <?php 
                              }
                            ?>
                          </tbody>
                          <?php if($list->type_created == 1 AND ($list->status == 'draft' OR $list->status == 'ready')){?>
                          <tfoot>
                            <tr>
                              <td colspan="8">
                                <a href="javascript:void(0)" class="add-new"><i class="fa fa-plus"></i> Tambah Data</a>
                              </td>
                            </tr>
                          <tfoot>
                          <?php }?>
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
                            <?php
                              foreach ($smi as $row) {
                            ?>
                              <tr class="num">
                                <td></td>
                                <td><?php echo $row->kode_produk;?></td>
                                <td><?php echo $row->nama_produk?></td>
                                <td><?php echo $row->lot?></td>
                                <td align="right"><?php echo number_format($row->qty,2)?></td>
                                <td><?php echo $row->uom?></td>
                                <td align="right"><?php echo number_format($row->qty2,2)?></td>
                                <td><?php echo $row->uom2?></td>
                                <td><?php echo $row->reff_note?></td>
                                <td><?php if($row->status == 'cancel') echo 'Batal';  else echo $row->status;?></td>
                                <td><?php echo $row->quant_id?></td>
                                <td class="no" align="center" >
                                 <a onclick="hapus('<?php  echo $list->kode ?>','<?php  echo $list->move_id ?>','<?php  echo $row->kode_produk ?>','<?php  echo htmlentities($row->nama_produk) ?>', '<?php  echo $row->quant_id ?>', '<?php  echo $row->row_order ?>', '<?php  echo $row->status ?>', '<?php  echo $row->origin_prod ?>')"  href="javascript:void(0)"><i class="fa fa-trash" style="color: red"></i> 
                                 </a>                               
                               </td>
                              </tr>
                            <?php 
                              }
                            ?>
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
                                 <?php echo ": ".$smove['move_id'];?>
                              </div>                                    
                            </div>
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-4"><label>Create Date </label></div>
                              <div class="col-xs-8 col-md-8">
                                 <?php echo ": ".$smove['create_date'];?>
                              </div>
                            </div>
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-4"><label>Origin </label></div>
                              <div class="col-xs-8 col-md-8">
                                 <?php echo ": ".$smove['origin'];?>
                              </div>
                            </div>
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-4"><label>Method</label></div>
                              <div class="col-xs-8 col-md-8">
                                 <?php echo ": ".$smove['method'];?>
                              </div>
                            </div>
                          </div>
                        </div>
                         <div class="col-md-6">
                          <div class="form-group"> 
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-4"><label>Lokasi Dari</label></div>
                              <div class="col-xs-8">
                                 <?php echo ": ".$smove['lokasi_dari'];?>
                              </div>                                    
                            </div>
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-4"><label>Lokasi Tujuan </label></div>
                              <div class="col-xs-8 col-md-8">
                                 <?php echo ": ".$smove['lokasi_tujuan'];?>
                              </div>
                            </div>
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-4"><label>Status </label></div>
                              <div class="col-xs-8 col-md-8">
                                 <?php if($smove['status'] == 'cancel') echo ': Batal';  else echo ': '.$smove['status'];?>
                                 <input type="hidden" name="status" id="status" value="<?php echo $smove['status']?>">
                              </div>
                            </div>
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-4"><label>MO </label></div>
                              <div class="col-xs-8 col-md-8">
                                 <?php
                                 if(!empty($mo['kode'])){
                                    echo ": ".$mo['kode'];
                                 }else{
                                    echo ": -";
                                 }
                                  ?>
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


  // validasi angka
  function validAngka(a){
    if(!/^[0-9.]+$/.test(a.value)){
      a.value = a.value.substring(0,a.value.length-1000);
      alert_notify('fa fa-warning','Maaf, Inputan Qty Hanya Berupa Angka !','danger',function(){});
    }
  }

  //html entities javascript
  function htmlentities_script(str) {
    return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
  }


  //untuk tambah details dari stock_kain greige
  function tambah(nama_produk,kode_produk,move_id)
  {
      //$("#edit_data").modal('show');
      $("#tambah_data").modal({
          show: true,
          backdrop: 'static'
      })
      var deptid = "<?php echo $list->dept_id; ?>"//parsing data id dept untuk log history
      $(".tambah_data").html('<center><h5><img src="<?php echo base_url('dist/img/ajax-loader.gif') ?> "/><br>Please Wait...</h5></center>');
      //$("#btn-ubah").attr("disabled", false);
      $('.modal-title').text('Tambah Details Product');
        $.post('<?php echo site_url()?>warehouse/pengirimanbarang/tambah_data_details',
          {nama_produk : nama_produk, kode_produk : kode_produk, move_id : move_id, deptid : deptid},
          function(html){
            setTimeout(function() {$(".tambah_data").html(html);  },2000);
          }   
       );
  }

   //untuk tambah details dari tabel stock quant
  function tambah_quant(nama_produk,kode_produk,move_id,origin_prod)
  {
      //$("#edit_data").modal('show');
      $("#tambah_data").modal({
          show: true,
          backdrop: 'static'
      })
      var deptid = "<?php echo $list->dept_id; ?>"//parsing data id dept untuk log history
      $(".tambah_data").html('<center><h5><img src="<?php echo base_url('dist/img/ajax-loader.gif') ?> "/><br>Please Wait...</h5></center>');
      $('.modal-title').text('Tambah Details Product Qty');
        $.post('<?php echo site_url()?>warehouse/pengirimanbarang/tambah_data_details_quant',
          {nama_produk   : nama_produk, kode_produk : kode_produk, move_id : move_id, deptid : deptid, origin : $('#origin').val(), origin_prod : origin_prod},
          function(html){
            setTimeout(function() {$(".tambah_data").html(html);  },2000);
          }   
       );
  }

  //untuk merefresh data
  function refresh_div_out()
  {
      $("#status_bar").load(location.href + " #status_bar");
      $("#table_items").load(location.href + " #table_items");
      $("#table_prod").load(location.href + " #table_prod");
      $("#tab_3").load(location.href + " #tab_3");
      $("#foot").load(location.href + " #foot");
      $("#tgl_btn").load(location.href + " #tgl_btn"); 
  }

  //untuk hapus details item
  function hapus(kode,move_id,kode_produk,nama_produk,quant_id,row_order,status,origin_prod)
  {
    var baseUrl     = '<?php echo base_url(); ?>';
    var deptid      = "<?php echo $list->dept_id; ?>";//parsing data id dept untuk log history
    var status_head =  "<?php echo $list->status;?>" ;

    if(status_head == 'done' || status == 'done'){
        alert_modal_warning('Maaf, Tidak bisa Dihapus. Data Sudah Terkirim !');
    }else{
      bootbox.dialog({
          message: "Apakah Anda ingin menghapus data ?",
          title: "<i class='glyphicon glyphicon-trash'></i> Delete !",
          buttons: {
            danger: {
                label    : "Yes ",
                className: "btn-primary btn-sm",
                callback : function() {
                      $.ajax({
                            dataType: "json",
                            type: 'POST',
                            url : "<?php echo site_url('warehouse/pengirimanbarang/hapus_details_items')?>",
                            data : {kode : kode, move_id : move_id, kode_produk : kode_produk, nama_produk : nama_produk, quant_id : quant_id, row_order : row_order, deptid : deptid, origin_prod : origin_prod },
                            error: function (xhr, ajaxOptions, thrownError) { 
                            alert(xhr.responseText);
                            }
                      }).done(function(response){
                        if(response.sesi == 'habis'){
                          alert_modal_warning(response.message);
                          window.location = baseUrl;//replace ke halaman login
                        }else if(response.status == 'failed'){
                          alert_modal_warning(response.message);
                          refresh_div_out();                
                        }else{
                          refresh_div_out();                
                          alert_notify(response.icon,response.message,response.type,function(){});
                          parent.fadeOut('slow');
                        }
                      })
                }
            },
            success: {
                  label    : "No",
                  className: "btn-default  btn-sm",
                  callback : function() {
                  $('.bootbox').modal('hide');
                  }
            }
          }
      });
    }
  }  


  //klik button simpan
  $("#btn-simpan").unbind( "click" );
  $('#btn-simpan').click(function(){
    var deptid = "<?php echo $list->dept_id; ?>"//parsing data id dept untuk log history
    $('#btn-simpan').button('loading');
    please_wait(function(){});
    var move_id = '<?php echo $smove['move_id'];?>';
    var baseUrl = '<?php echo base_url(); ?>';
      $.ajax({
         type: "POST",
         dataType: "json",
         url :'<?php echo base_url('warehouse/pengirimanbarang/simpan')?>',
         beforeSend: function(e) {
            if(e && e.overrideMimeType) {
                e.overrideMimeType("application/json;charset=UTF-8");
            }
         },
         data: {move_id : move_id, kode : $('#kode').val(),tgl_transaksi : $('#tgl_transaksi').val(),reff_note : $('#note').val(),deptid:deptid
          },success: function(data){
            if(data.sesi == "habis"){
              //alert jika session habis
              alert_modal_warning(data.message);             
              ///window.location.href = baseUrl;//replace ke halaman login
            }else if(data.status == "failed"){
              //jika ada form belum keisi
              unblockUI( function() {
                setTimeout(function() { alert_notify(data.icon,data.message,data.type,function(){}); }, 1000);
              });
              document.getElementById(data.field).focus();//focus ke field yang belum keisi
              refresh_div_out();
             $('#btn-simpan').button('reset');
            }else if(data.status == "ada"){
              alert_modal_warning(data.message);
              unblockUI( function() {});
              refresh_div_out();
              $('#btn-simpan').button('reset');
            }else{
              //jika berhasil disimpan/diubah
              unblockUI( function() {
                  setTimeout(function() { alert_notify(data.icon,data.message,data.type,function(){}); }, 1000);
              });
              refresh_div_out();
              $('#btn-simpan').button('reset');
            }

          },error: function (xhr, ajaxOptions, thrownError) { 
            alert(xhr.responseText);
            setTimeout($.unblockUI, 1000); 
            unblockUI( function(){});
            refresh_div_out();
            $('#btn-simpan').button('reset');
          }
      });
    });


  //klik button cek stock
  $("#btn-stok").unbind( "click" );
  $('#btn-stok').click(function(){
    var deptid = "<?php echo $list->dept_id; ?>"//parsing data id dept untuk log history
    $('#btn-stok').button('loading');
    please_wait(function(){});
    var move_id = '<?php echo $smove['move_id'];?>';
    var baseUrl = '<?php echo base_url(); ?>';
      $.ajax({
         type: "POST",
         dataType: "json",
         url :'<?php echo base_url('warehouse/pengirimanbarang/cek_stok')?>',
         beforeSend: function(e) {
            if(e && e.overrideMimeType) {
                e.overrideMimeType("application/json;charset=UTF-8");
            }
         },
         data: {move_id : move_id, kode : $('#kode').val(), deptid : deptid, origin : $('#origin').val()
          },success: function(data){
            if(data.sesi == "habis"){
              //alert jika session habis
              alert_modal_warning(data.message);
              window.location = baseUrl;//replace ke halaman login
            }else if(data.status == "failed"){
              alert_modal_warning(data.message);
              unblockUI( function() {});
              $('#btn-stok').button('reset');
              refresh_div_out();
              if(data.status_kurang == "yes"){
                alert_notify(data.icon2,data.message2,data.type2,function(){});
              }
            }else{

              if(data.terpenuhi == "yes"){
                unblockUI( function() {});
              }else{
                unblockUI( function() {
                    setTimeout(function() { alert_notify(data.icon,data.message,data.type,function(){}); }, 1000);
                });
              }

              refresh_div_out();
              $('#btn-stok').button('reset');            
            }

          },error: function (xhr, ajaxOptions, thrownError) { 
            alert(xhr.responseText);
            setTimeout($.unblockUI, 1000); 
            unblockUI( function(){});
            refresh_div_out();
            $('#btn-stok').button('reset');
          }
      });
    });


  //klik button kirim barang
  $("#btn-kirim").unbind( "click" );
  $(document).on('click','#btn-kirim',function(e){
      var move_id = '<?php echo $smove['move_id'];?>';
      var status  = $('#status').val();
      var method  = '<?php echo $smove['method'];?>';
      var deptid  = "<?php echo $list->dept_id; ?>"//parsing data id dept untuk log history
         
      if(status == 'cancel'){
        var message = 'Maaf, Data Tidak bisa Dikirim, Data Sudah dibatalkan !';
        alert_modal_warning(message);
        
      }else if(status=='done'){
        var message = 'Maaf, Data Sudah Terkirim !';
        alert_modal_warning(message);

      }else if(status=='draft'){
        var message = "Maaf, Product Belum ready !";
        alert_modal_warning(message);

      }else{
        bootbox.dialog({
        message: "Anda yakin ingin mengirim ?",
        title: "<i class='glyphicon glyphicon-send'></i> Send !",
        buttons: {
          danger: {
              label    : "Yes ",
              className: "btn-primary btn-sm",
              callback : function() {
                    please_wait(function(){});
                    $('#btn-kirim').button('loading');
                    $.ajax({
                          type: 'POST',
                          dataType : 'json',
                          url : "<?php echo site_url('warehouse/pengirimanbarang/kirim_barang')?>",
                          data : {kode : $('#kode').val(), move_id : move_id, method : method, origin : $('#origin').val(),deptid:deptid},
                          error: function (xhr, ajaxOptions, thrownError) { 
                          alert(xhr.responseText);
                          $('#btn-kirim').button('reset');
                          unblockUI( function(){});
                          refresh_div_out();
                        }
                    })
                    .done(function(response){
                      if(response.sesi == 'habis'){//jika session habis
                        alert_modal_warning(response.message);
                        window.location.replace('../index');
                      }else if(response.status == 'draft' || response.status == 'ada'  ||  response.status == 'not_valid' ){
                        //jika ada item masih draft/status sudah terkirim/lokasi lot tidak valid
                        unblockUI( function(){});
                        alert_modal_warning(response.message);                      
                        refresh_div_out();                 
                        $('#btn-kirim').button('reset');
                      }else{
                        if(response.backorder == "yes"){
                          alert_modal_warning(response.message2);
                        }
                        unblockUI( function() {
                          setTimeout(function() { alert_notify(response.icon,response.message,response.type,function(){}); }, 1000);
                        });
                        refresh_div_out();                     
                        $('#btn-kirim').button('reset');
                      }
                    })
              }
          },
          success: {
                label    : "No",
                className: "btn-default  btn-sm",
                callback : function() {
                $('.bootbox').modal('hide');
                }
          }
        }
      });
    }
  });


  // tambah data product

  $(document).on("click", ".add-new", function(){
    var lokasi =  "<?php echo $smove['lokasi_dari'] ?>";
    $(".add-new").hide();
    var index = $("#table_prod tbody[id='tbody_products'] tr:last-child").index();
    var row   ='<tr class="">'
          + '<td></td>'
          + '<td><input type="text" class="form-control input-sm kode_produk" name="kode_produk" id="kode_produk" readonly></td>'
          + '<td class="width-220"><select type="text" class="form-control input-sm prod" name="Product" id="product"></select></select><input type="hidden" class="form-control input-sm prodhidd" name="prodhidd" id="prodhidd"></td>'
          + '<td width="100px"><input type="text" class="form-control input-sm qty" name="Qty" id="qty"  onkeyup="validAngka(this)" ></td>'
          + '<td width="100px"><input type="text" class="form-control input-sm uom" name="Uom" id="uom" readonly></td>'
          + '<td></td>'
          + '<td></td>'
          + '<td align="center"><button type="button" class="btn btn-primary btn-xs add width-btn" title="Simpan" data-toggle="tooltip">Simpan</button><a class="edit" title="Edit" data-toggle="tooltip"><i class="fa fa-edit"></i></a><button type="button" class="btn btn-danger btn-xs batal width-btn" title="Batal" data-toggle="tooltip">Batal</button></td>'
          + '</tr>';

          $("#table_prod tbody[id='tbody_products']").append(row);
          $("#table_prod tbody[id='tbody_products'] tr").eq(index + 1).find(".add, .edit").toggle();
          $('[data-toggle="tooltip"]').tooltip();

       
          //select 2 product
          $('.prod').select2({
            allowClear: true,
            placeholder: "",
            ajax:{
                  dataType: 'JSON',
                  type : "POST",
                  url : "<?php echo base_url();?>warehouse/pengirimanbarang/get_produk_pengirimanbarang_select2",
                  data : function(params){
                    return{
                      prod:params.term,
                      lokasi:lokasi
                    };
                  }, 
                  processResults:function(data){
                    var results = [];

                    $.each(data, function(index,item){
                        results.push({
                            id:item.kode_produk,
                            text:'['+item.kode_produk+'] '+item.nama_produk
                        });
                    });
                    return {
                      results:results
                    };
                  },
                  error: function (xhr, ajaxOptions, thrownError){
                    //alert('Error data');
                   // alert(xhr.responseText);
                  }
            }
          });

        $(".prod").change(function(){
            $.ajax({
                  dataType: "JSON",
                  url : "<?php echo base_url();?>warehouse/pengirimanbarang/get_produk_pengirimanbarang_by_kode",
                  type: "POST",
                  data: {kode_produk: $(this).parents("tr").find("#product").val() },
                  success: function(data){
                    $('.kode_produk').val(data.kode_produk);
                    $('.prodhidd').val(data.nama_produk);
                    $('.uom').val(data.uom);
                  },
                  error: function (xhr, ajaxOptions, thrownError){
                  //  alert('Error data');
                  //  alert(xhr.responseText);
                  }
            });
        });
  });

  //batal add row on batal button click
  $(document).on("click", ".batal", function(){
    var input = $(this).parents("tr").find('.prod');
    input.each(function(){
      $(this).parent("td").html($(this).val());
    }); 
      
    $(this).parents("tr").remove();
    $(".add-new").show();
  }); 

  // cancel edit 
  $(document).on("click", ".cancel", function(){
      $("#table_prod").load(location.href + " #table_prod");
      $(".add-new").show();
  });

    //simpan / edit row data ke database
  $(document).on("click", ".add", function(e){
      e.preventDefault();

      var empty = false;
      var input = $(this).parents("tr").find('input[type="text"]');

      var empty2 = false;
      var select = $(this).parents("tr").find('select[type="text"]');

      //validasi tidak boleh kosong hanya select product saja
      select.each(function(){
        if(!$(this).val() && $(this).attr('name')=='Product' ){
          alert_notify('fa fa-warning',$(this).attr('name')+ ' Harus Diisi !','danger',function(){});
          empty2 = true;
        }
      });

      // validasi untuk inputan textbox
      input.each(function(){
        if(!$(this).val() && $(this).attr('name')!='reff'){
          alert_notify('fa fa-warning',$(this).attr('name')+ ' Harus Diisi !','danger',function(){});
          empty = true;
        }
      });
      
    
      if(!empty && !empty2){
        var kode         = "<?php echo $list->kode ?>";
        var kode_produk  = $(this).parents("tr").find("#product").val();
        var nama_produk  = $(this).parents("tr").find("#prodhidd").val();
        var qty   = $(this).parents("tr").find("#qty").val();
        var uom   = $(this).parents("tr").find("#uom").val();
        var row_order = $(this).parents("tr").find("#row_order").val();
        
        $.ajax({
          dataType: "JSON",
          url : '<?php echo site_url('warehouse/pengirimanbarang/simpan_product_pengiriman_barang') ?>',
          type: "POST",
          data: {kode : kode, 
                kode_produk : kode_produk,
                nama_produk : nama_produk,
                qty         : qty,
                uom         : uom,
                row_order   : row_order  },
          success: function(data){
            if(data.sesi=='habis'){
                //alert jika session habis
                alert_modal_warning(data.message);
                window.location.replace('../index');
            }else if(data.status == 'failed'){
                alert_modal_warning(data.message);
                refresh_div_out();
            }else{
                 refresh_div_out();
                $(".add-new").show();                   
                alert_notify(data.icon,data.message,data.type,function(){});
             }
          },
          error: function (xhr, ajaxOptions, thrownError){
            alert('Error Simpan Produk');
            //alert(xhr.responseText);
          }
        });
      }   
    });


    // Edit row on edit button click
    $(document).on("click", ".edit", function(){  

        $(this).parents("tr").find("td[data-content='edit']").each(function(){
          if($(this).attr('data-id')=="row_order"){
            $(this).html('<input type="hidden"  class="form-control" value="' +htmlentities_script($(this).attr('data-isi')) + '" id="'+ $(this).attr('data-id') +'"> ');
          }else if($(this).attr('data-id')=='qty'){
            $(this).html('<input type="text"  class="form-control" value="'+ ($(this).attr('data-isi')) +'" id="'+ $(this).attr('data-id') +'" name="'+ $(this).attr('data-id') +'" onkeyup="validAngka(this)"> ');
          }

        });  

        $(this).parents("tr").find(".add, .edit").toggle();
        $(this).parents("tr").find(".cancel, .delete").toggle();
        $(".add-new").hide();
    });


    // delete produk di table
    $(document).on("click",".delete",function(e) {

      e.preventDefault();

      $(this).parents("tr").find("td[data-content='edit']").each(function(){
        if($(this).attr('data-id')=="row_order"){
          $(this).html('<input type="hidden" class="form-control" value="' + htmlentities_script($(this).attr('data-isi')) + '" id="'+ $(this).attr('data-id') +'"> ');
        }
      });
      var kode  =  "<?php echo $list->kode; ?>";
      var deptid  = "<?php echo $list->dept_id; ?>";
      var row_order = $(this).parents("tr").find("#row_order").val();  
      bootbox.dialog({
        message: "Apakah Anda ingin menghapus data ?",
        title: "<i class='glyphicon glyphicon-trash'></i> Delete !",
        buttons: {
          danger: {
              label    : "Yes ",
              className: "btn-primary btn-sm",
              callback : function() {
                  $.ajax({
                      dataType: "JSON",
                      url : '<?php echo site_url('warehouse/pengirimanbarang/hapus_products_pengiriman_barang') ?>',
                      type: "POST",
                      data: {kode:kode,  row_order:row_order, dept_id : deptid },
                      success: function(data){
                        if(data.sesi=='habis'){
                            //alert jika session habis
                            alert_modal_warning(data.message);
                            window.location.replace('../index');
                        }else if(data.status == 'failed'){
                            alert_modal_warning(data.message);
                            refresh_div_out();
                        }else{
                            refresh_div_out();
                            $(".add-new").show();                   
                            alert_notify(data.icon,data.message,data.type,function(){});
                         }
                      },
                      error: function (xhr, ajaxOptions, thrownError){
                        //alert('Error Hapus Produk');
                        alert(xhr.responseText);
                      }
                    });
              }
          },
          success: {
                label    : "No",
                className: "btn-default  btn-sm",
                callback : function() {
                  $('.bootbox').modal('hide');
                }
          }
        }
        });
    });

    //modal mode print
    $(document).on('click','#btn-print',function(e){
        e.preventDefault();
        var kode       = "<?php echo $list->kode; ?>";
        var departemen = "<?php echo $list->dept_id; ?>";
        var status     = $("#status").val();

        if(kode == ""){
          alert_modal_warning('Kode Pengiriman Barang Kosong !');
        }else if(status != 'done'){
          alert_modal_warning('Print Pengiriman Barang Hanya bisa di Print saat statusnya "Terkirim" ! ');
        }else{
          var url = '<?php echo base_url() ?>warehouse/pengirimanbarang/print_pengiriman_barang';
          window.open(url+'?kode='+ kode+'&&departemen='+ departemen,'_blank');
        }
    });

</script>


</body>
</html>
