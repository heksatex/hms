<!DOCTYPE html>
<html>
    <head>
        <?php $this->load->view("admin/_partials/head.php") ?>

        <style type="text/css">
            button[id="btn-duplicate"]{/*untuk hidden button di top bar */
                display: none;
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
                            <h3 class="box-title">Form Add</h3>
                        </div>
                        <form class="form-horizontal" id="form-produk-add" name="form-produk-add" method="POST" action="<?php echo base_url('warehouse/produk/simpan') ?>" enctype="multipart/form-data">
                            <button class="hide" id="btn-save" type="submit"></button>
                            <div class="box-body">
                                <!--<form class="form-horizontal">-->

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
                                                <div class="col-xs-6">
                                                    <input type="text" class="form-control input-sm" name="namaproduk" id="namaproduk">
                                                </div>                                    
                                            </div>
                                            <div class="col-md-12 col-xs-12">
                                                <!-- <div class="col-xs-8">                      
                                                  <input type="checkbox" name="dapat_dijual" id="dapatdijual" value="true">
                                                  <label>Dapat Dijual</label>
                                                </div>
                                                <div class="col-xs-8">                      
                                                  <input type="checkbox" name="dapat_dibeli" id="dapatdibeli" value="true">
                                                  <label>Dapat Dibeli</label>
                                                </div> -->
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
                            <!--</form>-->

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
                                                    <!--<form class="form-horizontal">-->

                                                    <!-- konfigurasi umum -->
                                                    <div class="col-md-12">
                                                        <p class="text-light-blue"><strong>Konfigurasi Umum</strong></p>
                                                    </div>
                                                    <!-- kiri -->
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <div class="col-md-12 col-xs-12">
                                                                <div class="col-xs-4">Lebar Greige </div>
                                                                <div class="col-xs-4">
                                                                    <input type="text" class="form-control input-sm" name="lebargreige" id="lebargreige" style="text-align:right">
                                                                </div>
                                                                <div class="col-xs-3">
                                                                    <select class="form-control input-sm" name="uom_lebargreige" id="uom_lebargreige" >
                                                                        <option value=""></option>
                                                                        <?php
                                                                        foreach ($uom as $row) {
                                                                            echo "<option value='" . $row->short . "'>" . $row->short . "</option>";
                                                                        }
                                                                        ?>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-12 col-xs-12">
                                                                <div class="col-xs-4">Lebar Jadi </div>
                                                                <div class="col-xs-4">
                                                                    <input type="text" class="form-control input-sm" name="lebarjadi" id="lebarjadi" style="text-align:right">
                                                                </div>
                                                                <div class="col-xs-3">
                                                                    <select class="form-control input-sm" name="uom_lebarjadi" id="uom_lebarjadi" >
                                                                        <option value=""></option>
                                                                        <?php
                                                                        foreach ($uom as $row) {
                                                                            echo "<option value='" . $row->short . "'>" . $row->short . "</option>";
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
                                                                        if ($val[$i] == "Stockable") {
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
                                                                    <?php foreach ($uom as $row) { ?>
                                                                        <option value='<?php echo $row->short; ?>'><?php echo $row->short; ?></option>
                                                                    <?php } ?>
                                                                    </select>
                                                                </div>               
                                                            </div>
                                                            <div class="col-md-12 col-xs-12">
                                                                <div class="col-xs-4">UOM/Satuan 2</div>
                                                                <div class="col-xs-4">
                                                                    <select class="form-control input-sm" name="uomproduk2" id="uomproduk2" />
                                                                    <option value=""></option>
                                                                    <?php foreach ($uom as $row) { ?>
                                                                        <option value='<?php echo $row->short; ?>'><?php echo $row->short; ?></option>
                                                                    <?php } ?>
                                                                    </select>
                                                                </div>               
                                                            </div>
                                                            <div class="col-md-12 col-xs-12">
                                                                <div class="col-xs-4">UOM/Satuan Beli</div>
                                                                <div class="col-xs-4">
                                                                    <select class="form-control input-sm" name="uom_beli" id="uom_beli" />
                                                                    <option value=""></option>
                                                                    </select>
                                                                    <small id="note_uom_beli" class="form-text text-muted">

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
                                                                            // continue;
                                                                        ?>
                                                                        <option value='<?php echo $row->id; ?>'><?php echo $row->nama_category; ?></option>
                                                                    <?php } ?>
                                                                    </select>
                                                                </div>               
                                                            </div>
                                                            <div class="col-md-12 col-xs-12">
                                                                <div class="col-xs-4">Route Produksi</div>
                                                                <div class="col-xs-8">
                                                                    <select class="form-control input-sm" name="routeproduksi" id="routeproduksi" />
                                                                    <option value=""></option>
                                                                    <?php foreach ($route as $row) { ?>
                                                                        <option value='<?php echo $row->nama_route; ?>'><?php echo $row->nama_route; ?></option>
                                                                    <?php } ?>
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
                                                                        <input type='text' class="form-control input-sm" name="tanggaldibuat" id="tgldibuat" readonly="readonly" />
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
                                                                    <select type="text" class="form-control input-sm" name="sub_parent" id="sub_parent" >
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-12 col-xs-12">
                                                                <div class="col-xs-4">Jenis Kain</div>
                                                                <div class="col-xs-8">
                                                                    <select class="form-control input-sm" name="jenis_kain" id="jenis_kain" >
                                                                        <option value=""></option>
                                                                        <?php foreach ($jenis_kain as $row) { ?>
                                                                            <option value='<?php echo $row->id; ?>'><?php echo $row->nama_jenis_kain; ?></option>
                                                                        <?php } ?>
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
                                                                        <option value="t">Aktif</option>
                                                                        <option value="f">Tidak Aktif</option>
                                                                    </select>              
                                                                    <input type="hidden" name="autogenerate" id="autogenerate" value="0">
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
                                                                        Gambar pixel 1:1 (rekomendasi)
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
                                                                    <textarea class="form-control input-sm" name="note" id="note"></textarea>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!--</form>-->
                                                </div>
                                            </div>
                                            <!-- tab1 Info Produk -->

                                            <!-- tab2 Inventory -->
                                            <div class="tab-pane" id="tab_2">
                                                <div class="col-md-12">
                                                    <!--<form class="form-horizontal">-->
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
                                                    <!--</form>-->
                                                </div>
                                            </div>                  
                                            <!-- tab2 Inventory -->

                                            <!-- tab3 Pembelian -->
                                            <div class="tab-pane" id="tab_3">
                                                <div class="col-md-12">
                                                    <!--<form class="form-horizontal">-->
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
                                                                        <?php foreach ($uom as $row) { ?>
                                                                            <option value='<?php echo $row->short; ?>'><?php echo $row->short; ?></option>
                                                                        <?php } ?>
                                                                    </select>                 
                                                                </div>                                             
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!--</form>-->
                                                </div>
                                            </div>                  
                                            <!-- tab3 Pembelian -->

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
<script src="<?php echo base_url('dist/js/uploads/fileinput-canvas.js') ?>"></script>
<script src="<?php echo base_url('dist/js/uploads/fileinput.js') ?>"></script>
<script src="<?php echo base_url('dist/js/uploads/fileinput-sortable.js') ?>"></script>
<script type="text/javascript">
    $("#foto").fileinput({
        showCaption: false,
        dropZoneEnabled: false,
    });
    //set tgl buat
    var datenow = new Date();
    datenow.setMonth(datenow.getMonth());
    $('#tanggaldibuat').datetimepicker({
        defaultDate: datenow,
        format: 'YYYY-MM-DD HH:mm:ss',
        ignoreReadonly: true,
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
                    ke: $("#uomproduk").val(),
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
                    nama: params.term,
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

    //select 2 sub parent
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
                    nama: params.term,
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


    autogenerate_value = 0;

    //auto generate
    $('#auto_generate').change(function () {
        var auto_generate = $('#auto_generate').is(":checked");
        if (auto_generate == true) {
            autogenerate_value = 1;
            $('#kodeproduk').val('');
            $('#kodeproduk').attr('disabled', 'disabled');
        } else {
            autogenerate_value = 0;
            $('#kodeproduk').removeAttr('disabled');
            $('#kodeproduk').val('');
        }
        $("#autogenerate").val(autogenerate_value);
    });

    //select2 glacc
    $('.glacc').select2({
        allowClear: true,
        placeholder: "",
        ajax: {
            dataType: 'JSON',
            type: "POST",
            url: "<?php echo base_url(); ?>warehouse/produk/get_coa_list",
            //delay : 250,
            data: function (params) {
                return{
                    glacc: params.term,
                };
            },
            processResults: function (data) {
                var results = [];

                $.each(data, function (index, item) {
                    results.push({
                        id: item.kode_coa,
                        text: item.nama_coa
                    });
                });
                return {
                    results: results
                };
            },
            error: function (xhr, ajaxOptions, thrownError) {
                //alert('Error data');
                alert(xhr.responseText);
            }
        }
    });

    //klik button simpan
    $('#btn-simpan').off("click").click(function () {
        $('#btn-simpan').button('loading');
        var dapatdijual = $('#dapatdijual').is(":checked");
        if (dapatdijual == true) {
            dapatdijual_value = 1;
        } else {
            dapatdijual_value = 0;
        }

        var dapatdibeli = $('#dapatdibeli').is(":checked");
        if (dapatdibeli == true) {
            dapatdibeli_value = 1;
        } else {
            dapatdibeli_value = 0;
        }
//        $("#fotos").val(document.getElementById("foto").files[0]);
        $("#btn-save").trigger("click");
    });

    $('#btn-simpan_').click(function () {
        $('#btn-simpan_').button('loading');

        var dapatdijual = $('#dapatdijual').is(":checked");
        if (dapatdijual == true) {
            dapatdijual_value = 1;
        } else {
            dapatdijual_value = 0;
        }

        var dapatdibeli = $('#dapatdibeli').is(":checked");
        if (dapatdibeli == true) {
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
            data: {kodeproduk: $('#kodeproduk').val(),
                namaproduk: $('#namaproduk').val(),
                dapatdijual: dapatdijual_value,
                dapatdibeli: dapatdibeli_value,
                typeproduk: $('#typeproduk').val().toLowerCase(),
                uomproduk: $('#uomproduk').val(),
                uomproduk2: $('#uomproduk2').val(),
                lebarjadi: $('#lebarjadi').val(),
                uom_lebarjadi: $('#uom_lebarjadi').val(),
                lebargreige: $('#lebargreige').val(),
                uom_lebargreige: $('#uom_lebargreige').val(),
                kategoribarang: $('#kategoribarang').val(),
                routeproduksi: $('#routeproduksi').val(),
                bom: $('#bom').val(),
                tanggaldibuat: $('#tgldibuat').val(),
                note: $('#note').val(),
                status: 'tambah',
                statusproduk: $('#status').val(),
                product_parent: $('#product_parent').val(),
                sub_parent: $('#sub_parent').val(),
                jenis_kain: $('#jenis_kain').val(),
                autogenerate: autogenerate_value,

            }, success: function (data) {
                if (data.sesi == "habis") {
                    //alert jika session habis
                    alert_modal_warning(data.message);
                    window.location.replace('index');
                } else if (data.status == "failed") {
                    //jika ada form belum keiisi
                    $('#btn-simpan').button('reset');
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
                            alert_notify(data.icon, data.message, data.type, function () {
                                window.location.replace('edit/' + data.isi);
                            }, 1000);
                        });
                    });
                }
                $('#btn-simpan').button('reset');

            }, error: function (xhr, ajaxOptions, thrownError) {
                alert(xhr.responseText);
                unblockUI(function () {});
                $('#btn-simpan').button('reset');
            }
        });
    });

    const formproadd = document.forms.namedItem("form-produk-add");
    formproadd.addEventListener(
            "submit",
            (event) => {
        please_wait(function () {});
        request("form-produk-add").then(
                response => {
                    if (response.data.status === "failed") {
//                        window.location.replace('edit/' + response.data.isi);
                        unblockUI(function () {
                            setTimeout(function () {
                                alert_notify(response.data.icon, response.data.message, response.data.type, function () {});
                            }, 500);
                        });
                        document.getElementById(response.data.field).focus();
                    } else {
                        window.location.replace('edit/' + response.data.isi);
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
</script>


</body>
</html>
