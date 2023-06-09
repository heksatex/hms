
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
      font-size: 12px;
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
      display: inline-block;
      text-align: center;
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

    .pointer{
      cursor:pointer;
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
          <h3 class="box-title"><b>Form Edit - <?php echo $produk->kode_produk?></b></h3>
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
                <div class="col-md-8 col-xs-12">
                  <div class="col-md-12 col-xs-12">
                    <div class="col-xs-4"><label>Kode Produk </label></div>
                    <div class="col-xs-8">
                      <input type="text" class="form-control input-sm" name="kodeproduk" id="kodeproduk" readonly="readonly" value="<?php echo $produk->kode_produk?>"/>
                    </div>                                    
                  </div>
                  <div class="col-md-12 col-xs-12">
                    <div class="col-xs-4"><label>Nama Produk </label></div>
                    <div class="col-xs-8">
                      <input type="text" class="form-control input-sm" name="namaproduk" id="namaproduk" title="<?php echo htmlentities($produk->nama_produk)?>" data-toggle="tooltip" value="<?php echo htmlentities($produk->nama_produk)?>"/>
                    </div>                                    
                  </div>
                 <!--  <div class="col-md-12 col-xs-12">
                    <div class="col-xs-8">
                      <?php if($produk->dapat_dijual == 0){?>
                        <input type="checkbox" name="dapatdijual" id="dapatdijual" value="true">
                      <?php }else{ ?>
                        <input type="checkbox" name="dapatdijual" id="dapatdijual" checked value="true">
                      <?php } ?>
                      <label>Dapat Dijual</label>
                    </div>
                    <div class="col-xs-8">                      
                      <?php if($produk->dapat_dibeli == 0){?>
                        <input type="checkbox" name="dapatdibeli" id="dapatdibeli" value="true">
                      <?php }else{ ?>
                        <input type="checkbox" name="dapatdibeli" id="dapatdibeli" checked value="true">
                      <?php } ?>                           
                      <label>Dapat Dibeli</label>
                    </div>
                  </div> -->
                </div>

                <div class="col-md-4 col-xs-12">
                <ul class="bs-glyphicons">
                  <li>
                    <span class="glyphicon glyphicon-inbox"></span>            
                    <span class="glyphicon-class"><?php echo $onhand->qty_onhand ?> On Hand</span>
                  </li>                        
                  <li>
                    <span class="glyphicon glyphicon-transfer"></span>
                    <span class="glyphicon-class"><?php echo $moves->jml_moves ?> Moves</span>
                  </li>
                  <li class="pointer" onclick="cek_bom('<?php echo $produk->kode_produk ?>','<?php echo htmlentities($produk->nama_produk) ?>')" data-toggle="tooltip" title="Lihat BoM Produk">
                    <span class="glyphicon glyphicon-list-alt"></span>
                    <span class="glyphicon-class"><?php echo $bom->jml_bom ?> BoM</span>
                  </li>                        
                  <li class="pointer" onclick="cek_mo('<?php echo $produk->kode_produk ?>','<?php echo htmlentities($produk->nama_produk) ?>' )"  data-toggle="tooltip" title="Lihat MO Produk">
                    <span class="glyphicon glyphicon-cog" ></span>
                    <span class="glyphicon-class"><?php echo $mo->jml_mo ?> MO</span>
                  </li>
                  <!--
                  <li>
                    <span class="glyphicon glyphicon-shopping-cart"></span> Purchases
                    <span class="glyphicon-class"></span>
                  </li>                        
                  <li>
                    <span class="glyphicon glyphicon-usd"></span> Sales
                    <span class="glyphicon-class"></span>
                  </li>
                  -->
                </ul>
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
                  <!-- <li><a href="#tab_2" data-toggle="tab">Persediaan</a></li> -->
                  <!-- <li><a href="#tab_3" data-toggle="tab">Pembelian</a></li> -->
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
                              <div class="col-xs-4">Lebar Greige</div>
                              <div class="col-xs-4">
                                <input type="text" class="form-control input-sm" name="lebargreige" id="lebargreige" value="<?php echo $produk->lebar_greige;?>" style="text-align:right;">
                              </div>
                              <div class="col-xs-3">
                                <select class="form-control input-sm" name="uom_lebargreige" id="uom_lebargreige" >
                                  <option value=""></option>
                                    <?php foreach ($uom as $row) {
                                            if($row->short == $produk->uom_lebar_greige){
                                              echo "<option selected value='".$row->short."'>".$row->short."</option>";

                                            }else{
                                              echo "<option value='".$row->short."'>".$row->short."</option>";
                                            }
                                          }
                                    ?>
                                </select>
                              </div>
                            </div>
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-4">Lebar Jadi </div>
                              <div class="col-xs-4">
                                <input type="text" class="form-control input-sm" name="lebarjadi" id="lebarjadi" value="<?php echo $produk->lebar_jadi;?>" style="text-align:right;">
                              </div>
                              <div class="col-xs-3">
                                <select class="form-control input-sm" name="uom_lebarjadi" id="uom_lebarjadi" >
                                  <option value=""></option>
                                    <?php foreach ($uom as $row) {
                                            if($row->short == $produk->uom_lebar_jadi){
                                              echo "<option selected value='".$row->short."'>".$row->short."</option>";
                                            }else{
                                              echo "<option value='".$row->short."'>".$row->short."</option>";
                                            }
                                          }
                                    ?>
                                </select>
                              </div>
                            </div>
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-4">Type</div>
                              <div class="col-xs-4">                                
                                <select class="form-control input-sm" name="typeproduk" id="typeproduk" />
                                  <?php 
                                  $val = array('stockable','consumable');
                                  for($i=0;$i<=1;$i++) {
                                    if(strtolower($val[$i]) == strtolower($produk->type)){?>
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
                                <select class="form-control input-sm" name="uomproduk" id="uomproduk" />
                                  <option value=""></option>
                                  <?php foreach ($uom as $row) {
                                    if($row->short==$produk->uom){?>
                                      <option value='<?php echo $row->short; ?>' selected><?php echo $row->short;?></option>
                                  <?php
                                    }else{?>                                    
                                      <option value='<?php echo $row->short; ?>'> <?php echo $row->short;?></option>
                                  <?php  }}?>
                                </select>
                              </div>               
                            </div> 
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-4">UOM/Satuan 2</div>
                              <div class="col-xs-4">
                                <select class="form-control input-sm" name="uom2" id="uomproduk2" />
                                  <option value=""></option>
                                  <?php foreach ($uom as $row) {
                                    if($row->short==$produk->uom_2){?>
                                      <option value='<?php echo $row->short; ?>' selected><?php echo $row->short;?></option>
                                  <?php
                                    }else{?>                                    
                                      <option value='<?php echo $row->short; ?>'> <?php echo $row->short;?></option>
                                  <?php  }}?>
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
                                  <?php foreach ($category as $row) {
                                    if($row->id==$produk->id_category){?>
                                      <option value='<?php echo $row->id; ?>' selected><?php echo $row->nama_category;?></option>
                                  <?php
                                    }else{?>                                    
                                      <option value='<?php echo $row->id; ?>'> <?php echo $row->nama_category;?></option>
                                  <?php  }}?>
                                </select>
                              </div>               
                            </div>
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-4">Route Produksi</div>
                              <div class="col-xs-8">
                                <select class="form-control input-sm" name="routeproduksi" id="routeproduksi" />
                                  <option value=""></option>
                                  <?php foreach ($route as $row) {
                                    if($row->nama_route==$produk->route_produksi){?>
                                      <option value='<?php echo $row->nama_route; ?>' selected><?php echo $row->nama_route;?></option>
                                  <?php
                                    }else{?>                                    
                                      <option value='<?php echo $row->nama_route; ?>'> <?php echo $row->nama_route;?></option>
                                  <?php  }}?>
                                </select>                                
                              </div>               
                            </div>   
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-4">BoM</div>
                              <div class="col-xs-8">
                                <select class="form-control input-sm" name="bom" id="bom">
                                  <?php 
                                  $arr_bm = array(array('value' => '1', 'text' => 'True'), array( 'value'=> '0', 'text' => 'False'));
                                  foreach ($arr_bm as $val) {
                                    if($produk->bom == $val['value']){
                                    ?>
                                      <option value="<?php echo $val['value']; ?>" selected><?php echo $val['text']?></option>
                                  <?php 
                                    }else{ ?>
                                     <option value="<?php echo $val['value']; ?>" ><?php echo $val['text']?></option>
                                  <?php  
                                    }
                                  } ?>
                                </select>
                              </div>
                            </div>                             
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-4">Tanggal Dibuat</div>
                              <div class="col-xs-8 col-md-8">
                                <div class='input-group date' id='tanggaldibuat' >
                                  <input type='text' class="form-control input-sm" name="tanggaldibuat" id="tanggaldibuat" readonly="readonly" value="<?php echo $produk->create_date?>"/>
                                  <span class="input-group-addon">
                                      <span class="glyphicon glyphicon-calendar"></span>
                                  </span>
                                </div>
                              </div>                                    
                            </div>
                            <div class="col-md-12 col-xs-12">
                                <div class="col-xs-4">Product Parent</div>
                                <div class="col-xs-8 col-md-8">
                                  <select type="text" class="form-control input-sm" name="product_parent" id="product_parent" > </select>
                                </div>                                    
                            </div>
                            <div class="col-md-12 col-xs-12">
                                <div class="col-xs-4">Sub Parent</div>
                                <div class="col-xs-8 col-md-8">
                                  <select type="text" class="form-control input-sm" name="sub_parent" id="sub_parent" > </select>
                                </div>
                            </div>
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-4">Jenis Kain</div>
                              <div class="col-xs-8">
                                <select class="form-control input-sm" name="jenis_kain" id="jenis_kain" >
                                  <option value=""></option>
                                  <?php foreach ($jenis_kain as $row) {
                                    if($row->id==$produk->id_jenis_kain){?>
                                      <option value='<?php echo $row->id; ?>' selected><?php echo $row->nama_jenis_kain;?></option>
                                  <?php
                                    }else{?>                                    
                                      <option value='<?php echo $row->id; ?>'><?php echo $row->nama_jenis_kain;?></option>
                                  <?php 
                                    }
                                    }
                                  ?>
                                </select>                                
                              </div>               
                            </div>
                          </div>
                        </div>
                        
                        <!-- gl accounts -->
                        <!-- <div class="col-md-12">
                          <p class="text-light-blue"><strong>GL Accounts</strong></p>
                        </div> -->
                        <!-- kiri -->
                        <!-- <div class="col-md-6">
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
                        </div> -->
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
                                    $arr_status = array(array('value' => 't', 'text' => 'Aktif'), array( 'value'=> 'f', 'text' => 'Tidak Aktif'));
                                    foreach ($arr_status as $val) {
                                      if($val['value'] == $produk->status_produk){?>
                                        <option value="<?php echo $val['value']; ?>" selected><?php echo $val['text'];?></option>
                                      <?php
                                      }else{?>
                                        <option value="<?php echo $val['value']; ?>" ><?php echo $val['text'];?></option>
                                      <?php  
                                      }
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
                                <textarea class="form-control input-sm" name="note" id="note"><?php echo $produk->note?></textarea>
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
                                  <option value=""></option>
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
    <div id="foot">
     <?php 
        $data['kode'] =  $produk->kode_produk;
        $data['mms']  =  $mms->kode;
        $this->load->view("admin/_partials/footer.php",$data) 
     ?>
    </div>
  </footer>

</div>

<?php $this->load->view("admin/_partials/js.php") ?>

<script type="text/javascript">


  //set tgl buat
  var datenow=new Date();  
  datenow.setMonth(datenow.getMonth());
  $('#tanggal').datetimepicker({
      defaultDate: datenow,
      format : 'YYYY-MM-DD HH:mm:ss',
      ignoreReadonly: true,
  });

  var id_parent      = "<?php echo $produk->id_parent ?>";
  var nama_parent    = "<?php echo htmlspecialchars($produk->nama_parent) ?>";
    
  //untuk event selected select2 uom
  var $newOptionuom = $("<option></option>").val(id_parent).text(nama_parent);
  $("#product_parent").empty().append($newOptionuom).trigger('change'); 

  //select 2 produk parent
  $('#product_parent').select2({
      allowClear: true,
      placeholder: "",
      ajax:{
            dataType: 'JSON',
            type : "POST",
            url : "<?php echo base_url();?>warehouse/produk/get_product_parent_select2",
            //delay : 250,
            data : function(params){
              return{
                nama:params.term,
              };
            }, 
            processResults:function(data){
              var results = [];
              $.each(data, function(index,item){
                results.push({
                    id:item.id,
                    text:item.nama
                });
              });
              return {
                results:results
              };
            },
            error: function (xhr, ajaxOptions, thrownError){
              //alert('Error data');
              //alert(xhr.responseText);
            }
      }
  });

  
  var id_sub_parent      = "<?php echo $produk->id_sub_parent ?>";
  var nama_sub_parent    = "<?php echo htmlentities($produk->nama_sub_parent); ?>"; 
    
  //untuk event selected select2 id_sub_parent
  var $newOptionuom = $("<option></option>").val(id_sub_parent).html(nama_sub_parent);
  $("#sub_parent").empty().append($newOptionuom).trigger('change'); 

  //select 2 produk parent
  $('#sub_parent').select2({
      allowClear: true,
      placeholder: "",
      ajax:{
            dataType: 'JSON',
            type : "POST",
            url : "<?php echo base_url();?>warehouse/produk/get_product_sub_parent_select2",
            //delay : 250,
            data : function(params){
              return{
                nama:params.term,
              };
            }, 
            processResults:function(data){
              var results = [];
              $.each(data, function(index,item){
                results.push({
                    id:item.id,
                    text:item.nama_sub_parent
                });
              });
              return {
                results:results
              };
            },
            error: function (xhr, ajaxOptions, thrownError){
              //alert('Error data');
              //alert(xhr.responseText);
            }
      }
  });

  //klik button simpan
    $('#btn-simpan').click(function(){
      $('#btn-simpan').button('loading');
      var id            = '<?php echo $produk->id; ?>';
      
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
         data: {id              : id,
                kodeproduk      : $('#kodeproduk').val(),
                namaproduk      : $('#namaproduk').val(),
                dapatdijual     : dapatdijual_value,
                dapatdibeli     : dapatdibeli_value,
                lebarjadi       : $('#lebarjadi').val(),
                uom_lebarjadi   : $('#uom_lebarjadi').val(),
                lebargreige     : $('#lebargreige').val(),
                uom_lebargreige : $('#uom_lebargreige').val(),
                typeproduk      : $('#typeproduk').val().toLowerCase(),
                uomproduk       : $('#uomproduk').val(),
                uomproduk2      : $('#uomproduk2').val(),
                kategoribarang  : $('#kategoribarang').val(),
                routeproduksi   : $('#routeproduksi').val(),
                bom             : $('#bom').val(),
                tanggaldibuat   : $('#tanggaldibuat').val(),
                note            : $('#note').val(),
                product_parent  : $('#product_parent').val(),
                sub_parent      : $('#sub_parent').val(),
                jenis_kain      : $('#jenis_kain').val(),
                statusproduk   : $('#status').val(),// aktif/tidak aktif
                status          : 'edit',

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
              unblockUI( function() {                
                setTimeout(function() { alert_notify(data.icon,data.message,data.type,function(){}); }, 1000);
                });
              $("#foot").load(location.href + " #foot");
            }
            $('#btn-simpan').button('reset');

          },error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.responseText);
            unblockUI( function(){});
            $('#btn-simpan').button('reset');
          }
      });
    });


    // cek list bom produk
    function cek_bom(kode_produk,nama_produk){
      $("#view_data").modal({
          show: true,
          backdrop: 'static'
      })
      $(".view_body").html('<center><h5><img src="<?php echo base_url('dist/img/ajax-loader.gif') ?> "/><br>Please Wait...</h5></center>');
      $('.modal-title').text('List BoM '+nama_produk);
      $.post('<?php echo site_url()?>warehouse/produk/view_list_bom_produk_modal',
          {kode_produk : kode_produk},
          function(html){
            setTimeout(function() {$(".view_body").html(html);  },1000);
          }   
      );
    }


    // cek list MO produk
    function cek_mo(kode_produk,nama_produk){
      $("#view_data").modal({
          show: true,
          backdrop: 'static'
      })
      $(".view_body").html('<center><h5><img src="<?php echo base_url('dist/img/ajax-loader.gif') ?> "/><br>Please Wait...</h5></center>');
      $('.modal-title').text('List MO '+nama_produk);
      $.post('<?php echo site_url()?>warehouse/produk/view_list_mo_produk_modal',
          {kode_produk : kode_produk},
          function(html){
            setTimeout(function() {$(".view_body").html(html);  },1000);
          }   
      );
    }
   
</script>


</body>
</html>
