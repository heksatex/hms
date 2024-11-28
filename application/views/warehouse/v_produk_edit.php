
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
                text-align: left;
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

            figure.zoom {
                background-position: 50% 50%;
                position: relative;
                overflow: hidden;
                cursor: zoom-in;
            }
            figure.zoom img:hover {
                opacity: 0;
            }
            figure.zoom img {
                transition: opacity 0.5s;
                display: block;
                width: 100%;
            }
        </style>
        <link rel="stylesheet" href="<?php echo base_url('dist/css/uploads/fileinput.min.css') ?>">
    </head>

    <body class="hold-transition skin-black fixed sidebar-mini">
        <!-- Site wrapper -->
        <div class="wrapper">

            <!-- main -header -->
            <header class="main-header">
                <?php $this->load->view("admin/_partials/main-menu.php") ?>
                <?php
                $data['deptid'] = $id_dept;
                $this->load->view("admin/_partials/topbar.php", $data)
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
                            <h3 class="box-title"><b>Form Edit - <?php echo $produk->kode_produk ?></b></h3>
                        </div>
                        <form class="form-horizontal" id="form-produk-edit" name="form-produk-edit" method="POST" action="<?php echo base_url('warehouse/produk/simpan') ?>" enctype="multipart/form-data">
                            <button class="hide" id="btn-save" type="submit"></button>
                            <input type="hidden" name="status" value="edit">
                            <input type="hidden" name="id" value="<?= $produk->id ?>">

                            <div class="box-body">

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
                                                    <input type="text" class="form-control input-sm" name="kodeproduk" id="kodeproduk" readonly="readonly" value="<?php echo $produk->kode_produk ?>"/>
                                                </div>                                    
                                            </div>
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-4"><label>Nama Produk </label></div>
                                                <div class="col-xs-8">
                                                    <input type="text" class="form-control input-sm" name="namaproduk" id="namaproduk" title="<?php echo htmlentities($produk->nama_produk) ?>" data-toggle="tooltip" value="<?php echo htmlentities($produk->nama_produk) ?>"/>
                                                </div>                                    
                                            </div>
                                            <!--  <div class="col-md-12 col-xs-12">
                                               <div class="col-xs-8">
                                            <?php if ($produk->dapat_dijual == 0) { ?>
                                                                                                                                                                                                                                                                                                                                                           <input type="checkbox" name="dapatdijual" id="dapatdijual" value="true">
                                            <?php } else { ?>
                                                                                                                                                                                                                                                                                                                                                           <input type="checkbox" name="dapatdijual" id="dapatdijual" checked value="true">
                                            <?php } ?>
                                                 <label>Dapat Dijual</label>
                                               </div>
                                               <div class="col-xs-8">                      
                                            <?php if ($produk->dapat_dibeli == 0) { ?>
                                                                                                                                                                                                                                                                                                                                                           <input type="checkbox" name="dapatdibeli" id="dapatdibeli" value="true">
                                            <?php } else { ?>
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
                                                <li class="pointer" onclick="cek_bom('<?php echo $produk->kode_produk ?>', '<?php echo htmlentities($produk->nama_produk) ?>')" data-toggle="tooltip" title="Lihat BoM Produk">
                                                    <span class="glyphicon glyphicon-list-alt"></span>
                                                    <span class="glyphicon-class"><?php echo $bom->jml_bom ?> BoM</span>
                                                </li>                        
                                                <li class="pointer" onclick="cek_mo('<?php echo $produk->kode_produk ?>', '<?php echo htmlentities($produk->nama_produk) ?>')"  data-toggle="tooltip" title="Lihat MO Produk">
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

                                <div class="row">
                                    <div class="col-md-12">
                                        <!-- Custom Tabs -->
                                        <div class="">
                                            <ul class="nav nav-tabs">
                                                <li class="active"><a href="#tab_1" data-toggle="tab">Konfigurasi Umum</a></li>
                                                <!-- <li><a href="#tab_2" data-toggle="tab">Persediaan</a></li> -->
                                                <!-- <li><a href="#tab_3" data-toggle="tab">Pembelian</a></li> -->
                                                <li><a href="#tab_4" data-toggle="tab">Catatan</a></li>
                                                <li><a href="#tab_5" data-toggle="tab">Akunting</a></li>
                                                <li><a href="#tab_6" data-toggle="tab">Pembelian</a></li>
                                            </ul>             
                                            <div class="tab-content"><br>

                                                <!-- tab1 Info Produk -->
                                                <div class="tab-pane active" id="tab_1">
                                                    <div class="col-md-12">

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
                                                                        <input type="text" class="form-control input-sm" name="lebargreige" id="lebargreige" value="<?php echo $produk->lebar_greige; ?>" style="text-align:right;">
                                                                    </div>
                                                                    <div class="col-xs-3">
                                                                        <select class="form-control input-sm" name="uom_lebargreige" id="uom_lebargreige" >
                                                                            <option value=""></option>
                                                                            <?php
                                                                            foreach ($uom as $row) {
                                                                                if ($row->short == $produk->uom_lebar_greige) {
                                                                                    echo "<option selected value='" . $row->short . "'>" . $row->short . "</option>";
                                                                                } else {
                                                                                    echo "<option value='" . $row->short . "'>" . $row->short . "</option>";
                                                                                }
                                                                            }
                                                                            ?>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-12 col-xs-12">
                                                                    <div class="col-xs-4">Lebar Jadi </div>
                                                                    <div class="col-xs-4">
                                                                        <input type="text" class="form-control input-sm" name="lebarjadi" id="lebarjadi" value="<?php echo $produk->lebar_jadi; ?>" style="text-align:right;">
                                                                    </div>
                                                                    <div class="col-xs-3">
                                                                        <select class="form-control input-sm" name="uom_lebarjadi" id="uom_lebarjadi" >
                                                                            <option value=""></option>
                                                                            <?php
                                                                            foreach ($uom as $row) {
                                                                                if ($row->short == $produk->uom_lebar_jadi) {
                                                                                    echo "<option selected value='" . $row->short . "'>" . $row->short . "</option>";
                                                                                } else {
                                                                                    echo "<option value='" . $row->short . "'>" . $row->short . "</option>";
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
                                                                        $val = array('stockable', 'consumable');
                                                                        for ($i = 0; $i <= 1; $i++) {
                                                                            if (strtolower($val[$i]) == strtolower($produk->type)) {
                                                                                ?>
                                                                                <option selected><?php echo $val[$i]; ?></option>
                                                                            <?php } else {
                                                                                ?>
                                                                                <option><?php echo $val[$i]; ?></option>
                                                                                <?php
                                                                            }
                                                                        }
                                                                        ?>
                                                                        </select>
                                                                    </div>               
                                                                </div>
                                                                <div class="col-md-12 col-xs-12">
                                                                    <div class="col-xs-4">UOM/Satuan</div>
                                                                    <div class="col-xs-4">                                
                                                                        <select class="form-control input-sm" name="uomproduk" id="uomproduk" />
                                                                        <option value=""></option>
                                                                        <?php
                                                                        foreach ($uom as $row) {
                                                                            if ($row->short == $produk->uom) {
                                                                                ?>
                                                                                <option value='<?php echo $row->short; ?>' selected><?php echo $row->short; ?></option>
                                                                            <?php } else {
                                                                                ?>                                    
                                                                                <option value='<?php echo $row->short; ?>'> <?php echo $row->short; ?></option>
                                                                                <?php
                                                                            }
                                                                        }
                                                                        ?>
                                                                        </select>
                                                                    </div>               
                                                                </div> 
                                                                <div class="col-md-12 col-xs-12">
                                                                    <div class="col-xs-4">UOM/Satuan 2</div>
                                                                    <div class="col-xs-4">
                                                                        <select class="form-control input-sm" name="uomproduk2" id="uomproduk2" />
                                                                        <option value=""></option>
                                                                        <?php
                                                                        foreach ($uom as $row) {
                                                                            if ($row->short == $produk->uom_2) {
                                                                                ?>
                                                                                <option value='<?php echo $row->short; ?>' selected><?php echo $row->short; ?></option>
                                                                            <?php } else {
                                                                                ?>                                    
                                                                                <option value='<?php echo $row->short; ?>'> <?php echo $row->short; ?></option>
                                                                                <?php
                                                                            }
                                                                        }
                                                                        ?>
                                                                        </select>
                                                                    </div>               
                                                                </div>
                                                                <div class="col-md-12 col-xs-12">
                                                                    <div class="col-xs-4">UOM/Satuan Beli</div>
                                                                    <div class="col-xs-4">
                                                                        <select class="form-control input-sm" name="uom_beli" id="uom_beli">
                                                                            <option value=""></option>
                                                                            <?php if (!is_null($uom_beli)) { ?>
                                                                                <option value='<?php echo $uom_beli->id; ?>' selected><?php echo $uom_beli->dari; ?></option>
                                                                            <?php } ?>
                                                                        </select>
                                                                        <small id="note_uom_beli" class="form-text text-muted">
                                                                            <?= $uom_beli->catatan ?? "" ?>
                                                                        </small>
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
                                                                        <?php
                                                                        foreach ($category as $row) {
                                                                            if (in_array($row->id, $masking))
                                                                                continue;
                                                                            if ($row->id == $produk->id_category) {
                                                                                ?>
                                                                                <option value='<?php echo $row->id; ?>' selected><?php echo $row->nama_category; ?></option>
                                                                            <?php } else {
                                                                                ?>                                    
                                                                                <option value='<?php echo $row->id; ?>'> <?php echo $row->nama_category; ?></option>
                                                                                <?php
                                                                            }
                                                                        }
                                                                        ?>
                                                                        </select>
                                                                    </div>               
                                                                </div>
                                                                <div class="col-md-12 col-xs-12">
                                                                    <div class="col-xs-4">Route Produksi</div>
                                                                    <div class="col-xs-8">
                                                                        <select class="form-control input-sm" name="routeproduksi" id="routeproduksi" />
                                                                        <option value=""></option>
                                                                        <?php
                                                                        foreach ($route as $row) {
                                                                            if ($row->nama_route == $produk->route_produksi) {
                                                                                ?>
                                                                                <option value='<?php echo $row->nama_route; ?>' selected><?php echo $row->nama_route; ?></option>
                                                                            <?php } else {
                                                                                ?>                                    
                                                                                <option value='<?php echo $row->nama_route; ?>'> <?php echo $row->nama_route; ?></option>
                                                                                <?php
                                                                            }
                                                                        }
                                                                        ?>
                                                                        </select>                                
                                                                    </div>               
                                                                </div>   
                                                                <div class="col-md-12 col-xs-12">
                                                                    <div class="col-xs-4">BoM</div>
                                                                    <div class="col-xs-8">
                                                                        <select class="form-control input-sm" name="bom" id="bom">
                                                                            <?php
                                                                            $arr_bm = array(array('value' => '1', 'text' => 'True'), array('value' => '0', 'text' => 'False'));
                                                                            foreach ($arr_bm as $val) {
                                                                                if ($produk->bom == $val['value']) {
                                                                                    ?>
                                                                                    <option value="<?php echo $val['value']; ?>" selected><?php echo $val['text'] ?></option>
                                                                                <?php } else {
                                                                                    ?>
                                                                                    <option value="<?php echo $val['value']; ?>" ><?php echo $val['text'] ?></option>
                                                                                    <?php
                                                                                }
                                                                            }
                                                                            ?>
                                                                        </select>
                                                                    </div>
                                                                </div>                             
                                                                <div class="col-md-12 col-xs-12">
                                                                    <div class="col-xs-4">Tanggal Dibuat</div>
                                                                    <div class="col-xs-8 col-md-8">
                                                                        <div class='input-group date' id='tanggaldibuat' >
                                                                            <input type='text' class="form-control input-sm" name="tanggaldibuat" id="tanggaldibuat" readonly="readonly" value="<?php echo $produk->create_date ?>"/>
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
                                                                            <?php
                                                                            foreach ($jenis_kain as $row) {
                                                                                if ($row->id == $produk->id_jenis_kain) {
                                                                                    ?>
                                                                                    <option value='<?php echo $row->id; ?>' selected><?php echo $row->nama_jenis_kain; ?></option>
                                                                                <?php } else {
                                                                                    ?>                                    
                                                                                    <option value='<?php echo $row->id; ?>'><?php echo $row->nama_jenis_kain; ?></option>
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
                                                                        <select class="form-control input-sm" name="statusproduk" id="status">
                                                                            <?php
                                                                            $arr_status = array(array('value' => 't', 'text' => 'Aktif'), array('value' => 'f', 'text' => 'Tidak Aktif'));
                                                                            foreach ($arr_status as $val) {
                                                                                if ($val['value'] == $produk->status_produk) {
                                                                                    ?>
                                                                                    <option value="<?php echo $val['value']; ?>" selected><?php echo $val['text']; ?></option>
                                                                                <?php } else {
                                                                                    ?>
                                                                                    <option value="<?php echo $val['value']; ?>" ><?php echo $val['text']; ?></option>
                                                                                    <?php
                                                                                }
                                                                            }
                                                                            ?>
                                                                        </select>                 
                                                                    </div>               
                                                                </div>                                                        
                                                            </div>
                                                        </div>
                                                        <!-- kanan -->
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <div class="col-md-12 col-xs-12">
                                                                    <div class="col-xs-4">Gambar Produk</div>
                                                                    <div class="col-xs-8">
                                                                        <input id="foto" name="foto" type="file">
                                                                        <small id="passwordHelpBlock" class="form-text text-muted">
                                                                            Gambar pixel 1:1 (rekomendasi) <span id="zoom"> Lihat <i class="fa fa-plus-circle"></i></span>
                                                                        </small>
                                                                    </div>               
                                                                </div>                            
                                                            </div>
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
                                                                        <textarea class="form-control input-sm" name="note" id="note"><?php echo $produk->note ?></textarea>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>


                                                    </div>
                                                </div>
                                                <!-- tab1 Info Produk -->

                                                <!-- tab2 Inventory -->
                                                <div class="tab-pane" id="tab_2">
                                                    <div class="col-md-12">
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
                                                    </div>
                                                </div>                  
                                                <!-- tab2 Inventory -->

                                                <!-- tab3 Pembelian -->
                                                <div class="tab-pane" id="tab_3">
                                                    <div class="col-md-12">
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
                                                                            <?php if (!is_null($uom_beli)) { ?>
                                                                                <option value='<?php echo $uom_beli->id; ?>' selected><?php echo $uom_beli->dari; ?></option>
                                                                            <?php } ?>
                                                                        </select>
                                                                        <small id="note_uom_beli" class="form-text text-muted">
                                                                            <?= $uom_beli->catatan ?? "" ?>
                                                                        </small>
                                                                    </div>                                             
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>                  
                                                <!-- tab3 Pembelian -->
                                                <div class="tab-pane" id="tab_4">
                                                    <div class="col-md-12 table-responsive over">
                                                        <table class="table table-condesed table-hover rlstable  over" width="100%">
                                                            <thead>
                                                            <th>Catatan</th>
                                                            </thead>
                                                            <tbody>
                                                                <?php foreach ($catatan as $key => $value) {
                                                                    ?>
                                                                    <tr>
                                                                        <td><?= $value->catatan ?></td>
                                                                    </tr>
                                                                <?php }
                                                                ?>
                                                                <tr>
                                                                    <td>
                                                                        <input type="text" class="form-control" name="catatan" id="catatan" /> </br>
                                                                        <input type="hidden" class="form-control" name="jenis_catatan" id="jenis_catatan" /> </br>
                                                                        <button class="btn btn-danger btn-sm" type="button" id="sbmt-catatan">Submit</button>
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>

                                                </div>
                                                <div class="tab-pane" id="tab_5">
                                                    <div class="form-group">
                                                        <div class="col-md-12">
                                                            <div class="col-xs-4"><label>COA Pembelian </label></div>
                                                            <div class="col-xs-8">
                                                                <select name="akun_pembelian" id="akun_pembelian" style="width:100%;" class="form-control">
                                                                    <option></option>
                                                                    <?php
                                                                    if (!is_null($coa)) {
                                                                        ?>
                                                                        <option value="<?= $coa->kode_coa ?>" selected><?= $coa->kode_coa . " - " . $coa->nama ?></option>
                                                                        <?php
                                                                    }
                                                                    ?>
                                                                </select>
                                                            </div> 
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <div class="col-md-12">
                                                            <div class="col-md-4">
                                                                <button class="btn btn-danger btn-sm" type="button" id="sbmt-coa">Submit</button>
                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>
                                                <div class="tab-pane" id="tab_6">
                                                    <div class="form-group">
                                                        <div class="col-xs-4"><label>Harga Pembelian </label></div>
                                                        <div class="col-xs-8">
                                                            <input type="text" class="form-control" name="harga_beli" id="harga_beli" value="<?= $harga->harga ?? "0.00" ?>">
                                                            <input type="hidden" name="harga_jenis" id="harga_jenis" value="<?= $harga->jenis ?? "pembelian" ?>">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <div class="col-md-12">
                                                            <div class="col-md-4">
                                                                <button class="btn btn-danger btn-sm" type="button" id="sbmt-harga">Submit</button>
                                                            </div>
                                                        </div>
                                                    </div>
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
        <div id="foot">
            <?php
            $data['kode'] = $produk->kode_produk;
            $data['mms'] = $mms->kode;
            $this->load->view("admin/_partials/footer.php", $data)
            ?>
        </div>
    </footer>

</div>

<?php
$image = base_url("upload/product/default.jpg");
if (is_file(FCPATH . "upload/product/{$produk->kode_produk}.jpg")) {
    $image = base_url("upload/product/{$produk->kode_produk}.jpg");
}
?>
<div class="modal fade lebar" id="view_datas" role="dialog">
    <div class="modal-dialog" >
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"></h4>
            </div>
            <form class="form-horizontal">
                <div class="modal-body">
                    <figure class="zoom" onmousemove="zoom(event)" 
                            style="background-image: url(<?= $image ?>); background-position: 7.8% 40.2135%;">
                        <img src="<?= $image ?>">
                    </figure>
                </div>
            </form>
            <div class="modal-footer">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view("admin/_partials/js.php") ?>
<script src="<?php echo base_url('dist/js/uploads/fileinput-canvas.js') ?>"></script>
<script src="<?php echo base_url('dist/js/uploads/fileinput.js') ?>"></script>
<script src="<?php echo base_url('dist/js/uploads/fileinput-sortable.js') ?>"></script>
<script type="text/javascript">
                        function zoom(e) {
                            var zoomer = e.currentTarget;
                            e.offsetX ? offsetX = e.offsetX : offsetX = e.touches[0].pageX
                            e.offsetY ? offsetY = e.offsetY : offsetX = e.touches[0].pageX
                            x = offsetX / zoomer.offsetWidth * 100
                            y = offsetY / zoomer.offsetHeight * 100
                            zoomer.style.backgroundPosition = x + '% ' + y + '%';
                        }
                        $("#sbmt-coa").off("click").unbind("click").on("click", function () {
                            confirmRequest("Produk", "Update COA untuk produk ini ? ", function () {
                                please_wait(function () {});
                                $.ajax({
                                    url: "<?php echo base_url(); ?>warehouse/produk/save_coa/<?= encrypt_url($produk->kode_produk) ?>",
                                                    type: "POST",
                                                    data: {
                                                        coa: $("#akun_pembelian").val(),
                                                        jenis: "pembelian"
                                                    }, success: function (data) {
                                                        location.reload();
                                                    }, error: function (xhr, ajaxOptions, thrownError) {
                                                        alert_notify(xhr.responseJSON.icon, xhr.responseJSON.message, xhr.responseJSON.type, function () {});
                                                        unblockUI(function () {});
                                                    }
                                                });
                                            });
                                        });

                                        $("#sbmt-harga").off("click").unbind("click").on("click", function () {
                                            confirmRequest("Produk", "Update Harga Beli untuk produk ini ? ", function () {
                                                please_wait(function () {});
                                                $.ajax({
                                                    url: "<?php echo base_url(); ?>warehouse/produk/save_harga/<?= encrypt_url($produk->kode_produk) ?>",
                                                                    type: "POST",
                                                                    data: {
                                                                        harga: $("#harga_beli").val(),
                                                                        jenis: $("#harga_jenis").val()
                                                                    }, success: function (data) {
                                                                        location.reload();
                                                                    }, error: function (xhr, ajaxOptions, thrownError) {
                                                                        alert_notify(xhr.responseJSON.icon, xhr.responseJSON.message, xhr.responseJSON.type, function () {});
                                                                        unblockUI(function () {});
                                                                    }
                                                                });
                                                            });
                                                        });

                                                        //select 2 akun coa
                                                        $('#akun_pembelian').select2({
                                                            allowClear: true,
                                                            placeholder: "",
                                                            ajax: {
                                                                dataType: 'JSON',
                                                                type: "POST",
                                                                url: "<?php echo base_url(); ?>warehouse/produk/get_coa",
                                                                delay: 250,
                                                                data: function (params) {
                                                                    return{
                                                                        search: params.term
                                                                    };
                                                                },
                                                                processResults: function (data) {
                                                                    var results = [];
                                                                    $.each(data.data, function (index, item) {
                                                                        results.push({
                                                                            id: item.kode_coa,
                                                                            text: item.kode_coa + " - " + item.nama
                                                                        });
                                                                    });
                                                                    return {
                                                                        results: results
                                                                    };
                                                                },
                                                                error: function (xhr, ajaxOptions, thrownError) {
                                                                    //alert('Error data');
                                                                    //alert(xhr.responseText);
                                                                }
                                                            }
                                                        });
                                                        //select 2 uom_beli
                                                        $("#uom_beli").select2({
                                                            allowClear: true,
                                                            placeholder: "Satuan Beli",
                                                            ajax: {
                                                                dataType: 'JSON',
                                                                type: "GET",
                                                                url: "<?php echo base_url(); ?>warehouse/produk/get_uom_beli",
                                                                delay: 250,
                                                                data: function (params) {
                                                                    return{
                                                                        nama: params.term,
                                                                        ke: $("#uomproduk").val()
                                                                    };
                                                                },
                                                                processResults: function (data) {
                                                                    var results = [];
                                                                    $.each(data.data, function (index, item) {
                                                                        results.push({
                                                                            id: item.id,
                                                                            text: item.text,
                                                                            catatan: item.catatan
                                                                        });
                                                                    });
                                                                    return {
                                                                        results: results
                                                                    };
                                                                },
                                                                error: function (xhr, ajaxOptions, thrownError) {
                                                                    alert_notify("fa fa-warning", xhr.responseJSON.message, "danger", function () {}, 500);
                                                                    //alert('Error data');
                                                                    //alert(xhr.responseText);
                                                                }
                                                            }
                                                        });

                                                        $("#uom_beli").on("select2:select", function () {
                                                            var selectedSelect2OptionSource = $("#uom_beli :selected").data().data.catatan;
                                                            $("#note_uom_beli").html(selectedSelect2OptionSource);
                                                        });

                                                        $("#uomproduk").on("change", function () {
                                                            $("#uom_beli").val('').trigger('change');
                                                            $("#note_uom_beli").html("");
                                                        });

                                                        $("#foto").fileinput({
                                                            showCaption: false,
                                                            dropZoneEnabled: false,
                                                            initialPreviewFileType: 'image',
                                                            initialPreviewAsData: true,
                                                            initialPreview: [
                                                                "<?= $image ?>"
                                                            ],
                                                            initialPreviewConfig: [
                                                                {caption: "", width: "", url: "<?php echo base_url('warehouse/produk/delete_image') ?>", key: "<?= $produk->id ?>"}
                                                            ],
                                                            allowedFileExtensions: ["jpg"]

                                                        }).on("filebeforedelete", function () {
                                                            confirmRequest("Product", "Hapus Gambar ? ", (() => {
                                                            }));
                                                        });
                                                        //set tgl buat
                                                        var datenow = new Date();
                                                        datenow.setMonth(datenow.getMonth());
                                                        $('#tanggal').datetimepicker({
                                                            defaultDate: datenow,
                                                            format: 'YYYY-MM-DD HH:mm:ss',
                                                            ignoreReadonly: true
                                                        });

                                                        var id_parent = "<?php echo $produk->id_parent ?>";
                                                        var nama_parent = "<?php echo htmlspecialchars($produk->nama_parent) ?>";

                                                        //untuk event selected select2 uom
                                                        var $newOptionuom = $("<option></option>").val(id_parent).text(nama_parent);
                                                        $("#product_parent").empty().append($newOptionuom).trigger('change');

                                                        //select 2 produk parent
                                                        $('#product_parent').select2({
                                                            allowClear: true,
                                                            placeholder: "",
                                                            ajax: {
                                                                dataType: 'JSON',
                                                                type: "POST",
                                                                url: "<?php echo base_url(); ?>warehouse/produk/get_product_parent_select2",
                                                                //delay : 250,
                                                                data: function (params) {
                                                                    return{
                                                                        nama: params.term
                                                                    };
                                                                },
                                                                processResults: function (data) {
                                                                    var results = [];
                                                                    $.each(data, function (index, item) {
                                                                        results.push({
                                                                            id: item.id,
                                                                            text: item.nama
                                                                        });
                                                                    });
                                                                    return {
                                                                        results: results
                                                                    };
                                                                },
                                                                error: function (xhr, ajaxOptions, thrownError) {
                                                                    //alert('Error data');
                                                                    //alert(xhr.responseText);
                                                                }
                                                            }
                                                        });


                                                        var id_sub_parent = "<?php echo $produk->id_sub_parent ?>";
                                                        var nama_sub_parent = "<?php echo htmlentities($produk->nama_sub_parent); ?>";

                                                        //untuk event selected select2 id_sub_parent
                                                        var $newOptionuom = $("<option></option>").val(id_sub_parent).html(nama_sub_parent);
                                                        $("#sub_parent").empty().append($newOptionuom).trigger('change');

                                                        //select 2 produk parent
                                                        $('#sub_parent').select2({
                                                            allowClear: true,
                                                            placeholder: "",
                                                            ajax: {
                                                                dataType: 'JSON',
                                                                type: "POST",
                                                                url: "<?php echo base_url(); ?>warehouse/produk/get_product_sub_parent_select2",
                                                                //delay : 250,
                                                                data: function (params) {
                                                                    return{
                                                                        nama: params.term
                                                                    };
                                                                },
                                                                processResults: function (data) {
                                                                    var results = [];
                                                                    $.each(data, function (index, item) {
                                                                        results.push({
                                                                            id: item.id,
                                                                            text: item.nama_sub_parent
                                                                        });
                                                                    });
                                                                    return {
                                                                        results: results
                                                                    };
                                                                },
                                                                error: function (xhr, ajaxOptions, thrownError) {
                                                                    //alert('Error data');
                                                                    //alert(xhr.responseText);
                                                                }
                                                            }
                                                        });

                                                        //klik button simpan
                                                        $('#btn-simpan').off("click").click(function () {
                                                            $('#btn-simpan').button('loading');
                                                            var dapatdijual = $('#dapatdijual').is(":checked");
                                                            if (dapatdijual === true) {
                                                                dapatdijual_value = 1;
                                                            } else {
                                                                dapatdijual_value = 0;
                                                            }

                                                            var dapatdibeli = $('#dapatdibeli').is(":checked");
                                                            if (dapatdibeli === true) {
                                                                dapatdibeli_value = 1;
                                                            } else {
                                                                dapatdibeli_value = 0;
                                                            }
//        $("#fotos").val(document.getElementById("foto").files[0]);
                                                            $("#btn-save").trigger("click");
                                                        });
                                                        $('#btn-simpan_').click(function () {
                                                            $('#btn-simpan_').button('loading');
                                                            var id = '<?php echo $produk->id; ?>';

                                                            var dapatdijual = $('#dapatdijual').is(":checked");
                                                            if (dapatdijual === true) {
                                                                dapatdijual_value = 1;
                                                            } else {
                                                                dapatdijual_value = 0;
                                                            }

                                                            var dapatdibeli = $('#dapatdibeli').is(":checked");
                                                            if (dapatdibeli === true) {
                                                                dapatdibeli_value = 1;
                                                            } else {
                                                                dapatdibeli_value = 0;
                                                            }

                                                            please_wait(function () {});
                                                            $.ajax({
                                                                type: "POST",
                                                                dataType: "json",
                                                                url: '<?php echo base_url('warehouse/produk/simpan') ?>',
                                                                beforeSend: function (e) {
                                                                    if (e && e.overrideMimeType) {
                                                                        e.overrideMimeType("application/json;charset=UTF-8");
                                                                    }
                                                                },
                                                                data: {id: id,
                                                                    kodeproduk: $('#kodeproduk').val(),
                                                                    namaproduk: $('#namaproduk').val(),
                                                                    dapatdijual: dapatdijual_value,
                                                                    dapatdibeli: dapatdibeli_value,
                                                                    lebarjadi: $('#lebarjadi').val(),
                                                                    uom_lebarjadi: $('#uom_lebarjadi').val(),
                                                                    lebargreige: $('#lebargreige').val(),
                                                                    uom_lebargreige: $('#uom_lebargreige').val(),
                                                                    typeproduk: $('#typeproduk').val().toLowerCase(),
                                                                    uomproduk: $('#uomproduk').val(),
                                                                    uomproduk2: $('#uomproduk2').val(),
                                                                    kategoribarang: $('#kategoribarang').val(),
                                                                    routeproduksi: $('#routeproduksi').val(),
                                                                    bom: $('#bom').val(),
                                                                    tanggaldibuat: $('#tanggaldibuat').val(),
                                                                    note: $('#note').val(),
                                                                    product_parent: $('#product_parent').val(),
                                                                    sub_parent: $('#sub_parent').val(),
                                                                    jenis_kain: $('#jenis_kain').val(),
                                                                    statusproduk: $('#status').val(), // aktif/tidak aktif
                                                                    status: 'edit'

                                                                }, success: function (data) {
                                                                    if (data.sesi === "habis") {
                                                                        //alert jika session habis
                                                                        alert_modal_warning(data.message);
                                                                        window.location.replace('index');
                                                                    } else if (data.status === "failed") {
                                                                        //jika ada form belum keiisi
                                                                        unblockUI(function () {
                                                                            setTimeout(function () {
                                                                                alert_notify(data.icon, data.message, data.type, function () {});
                                                                            }, 1000);
                                                                        });
                                                                        document.getElementById(data.field).focus();
                                                                    } else {
                                                                        //jika berhasil disimpan/diubah
                                                                        unblockUI(function () {
                                                                            setTimeout(function () {
                                                                                alert_notify(data.icon, data.message, data.type, function () {});
                                                                            }, 1000);
                                                                        });
                                                                        $("#foot").load(location.href + " #foot");

                                                                        var $newOptionuom = $("<option></option>").val(data.id).html(data.nama);
                                                                        $("#sub_parent").empty().append($newOptionuom).trigger('change');
                                                                    }
                                                                    $('#btn-simpan').button('reset');

                                                                }, error: function (xhr, ajaxOptions, thrownError) {
                                                                    alert(xhr.responseText);
                                                                    unblockUI(function () {});
                                                                    $('#btn-simpan').button('reset');
                                                                }
                                                            });
                                                        });
                                                        const formproedit = document.forms.namedItem("form-produk-edit");
                                                        formproedit.addEventListener(
                                                                "submit",
                                                                (event) => {
                                                            please_wait(function () {});
                                                            request("form-produk-edit").then(
                                                                    response => {
                                                                        if (response.data.status === "failed") {
                                                                            unblockUI(function () {
                                                                                setTimeout(function () {
                                                                                    alert_notify(response.data.icon, response.data.message, response.data.type, function () {});
                                                                                }, 500);
                                                                            });
                                                                            document.getElementById(response.data.field).focus();
                                                                        } else {
                                                                            $("#foot").load(location.href + " #foot");
                                                                            var $newOptionuom = $("<option></option>").val(data.id).html(data.nama);
                                                                            $("#sub_parent").empty().append($newOptionuom).trigger('change');
                                                                        }
                                                                    }).catch(e => {

                                                            }).finally(() => {
                                                                unblockUI(function () {});
                                                                $('#btn-simpan').button('reset');
                                                            });
                                                            event.preventDefault();
                                                        },
                                                                false
                                                                );

                                                        $("#zoom").on("click", function () {
                                                            $("#view_datas").modal({
                                                                show: true,
                                                                backdrop: 'static'
                                                            });
                                                        });

                                                        // cek list bom produk
                                                        function cek_bom(kode_produk, nama_produk) {
                                                            $("#view_data").modal({
                                                                show: true,
                                                                backdrop: 'static'
                                                            });
                                                            $(".view_body").html('<center><h5><img src="<?php echo base_url('dist/img/ajax-loader.gif') ?> "/><br>Please Wait...</h5></center>');
                                                            $('.modal-title').text('List BoM ' + nama_produk);
                                                            $.post('<?php echo site_url() ?>warehouse/produk/view_list_bom_produk_modal',
                                                                    {kode_produk: kode_produk},
                                                                    function (html) {
                                                                        setTimeout(function () {
                                                                            $(".view_body").html(html);
                                                                        }, 1000);
                                                                    }
                                                            );
                                                        }

                                                        // cek list MO produk
                                                        function cek_mo(kode_produk, nama_produk) {
                                                            $("#view_data").modal({
                                                                show: true,
                                                                backdrop: 'static'
                                                            });
                                                            $(".view_body").html('<center><h5><img src="<?php echo base_url('dist/img/ajax-loader.gif') ?> "/><br>Please Wait...</h5></center>');
                                                            $('.modal-title').text('List MO ' + nama_produk);
                                                            $.post('<?php echo site_url() ?>warehouse/produk/view_list_mo_produk_modal',
                                                                    {kode_produk: kode_produk},
                                                                    function (html) {
                                                                        setTimeout(function () {
                                                                            $(".view_body").html(html);
                                                                        }, 1000);
                                                                    }
                                                            );
                                                        }

                                                        // duplicate produk
                                                        $(document).on('click', '#btn-duplicate', function (e) {
                                                            e.preventDefault();
                                                            var id = "<?php echo $produk->id; ?>";
                                                            var kode_produk = "<?php echo $produk->kode_produk; ?>";
                                                            var duplicate = 'true';

                                                            if (id === "" || kode_produk === "") {
                                                                alert_modal_warning('Kode Produk Kosong !');
                                                            } else {
                                                                var url = '<?php echo base_url() ?>warehouse/produk/add';
                                                                window.open(url + '?id=' + id + '&&duplicate=' + duplicate + '&&kode_produk=' + kode_produk, '_blank');
                                                            }
                                                        });

                                                        $("#sbmt-catatan").off("click").on("click", function () {
                                                            confirmRequest("Catatan Produk", "Tambahkan Catatan", function () {
                                                                please_wait(function () {});
                                                                $.ajax({
                                                                    "url": "<?php echo site_url('warehouse/produk/save_catatan/' . $id) ?>",
                                                                    "type": "POST",
                                                                    "data": {
                                                                        jenis_catatan: "pembelian",
                                                                        catatan: $("#catatan").val(),
                                                                        produk: "<?= $produk->kode_produk ?>"
                                                                    }, success: function (data) {
                                                                        location.reload();
                                                                    }, error: function (xhr, ajaxOptions, thrownError) {
                                                                        alert_notify(xhr.responseJSON.icon, xhr.responseJSON.message, xhr.responseJSON.type, function () {});
                                                                        unblockUI(function () {});
                                                                    }

                                                                });
                                                            });
                                                        });
</script>


</body>
</html>
