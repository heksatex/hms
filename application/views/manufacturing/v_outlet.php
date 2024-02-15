<!DOCTYPE html>
<html lang="en">

<head>
    <?php $this->load->view("admin/_partials/head.php") ?>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('dist/css/tableScroll.css') ?>">
    <style>
    .bb {
        border-bottom: 2px solid #ddd !important;
    }
    .pad_left_empty {
        padding-left: 0px;
    }
    .pad-left-right{
        padding-left:0px;
        padding-right:5px;
    }
    .info-box2{
        display: block;
        min-height: 80px;
        background: #fff;
        width: 100%;
        box-shadow: 0 15px 15px rgba(32, 21, 21, 0.18);
        border-radius: 2px;
        margin-bottom: 3px;
        cursor:pointer;
    }
    .info-box2-focus{
        border:2px solid;
        border-color:#3c8dbc;
    }
    .info-box-radio{
      border-top-left-radius: 2px;
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
        border-bottom-left-radius: 2px;
        display: block;
        float: left;
        height: 80px;
        width: 30px;
        text-align: center;
        /* font-size: 45px; */
        line-height: 80px;
    }
    .info-box-icon2{
        border-top-left-radius: 2px;
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
        border-bottom-left-radius: 2px;
        display: block;
        float: left;
        height: 80px;
        width: 80px;
        text-align: center;
        font-size: 45px;
        line-height: 80px;
        background: rgba(0, 0, 0, 0.2);
    }
    .info-box-content2{
        padding: 5px 10px;
        margin-left: 120px;
    }
    .ws{
        white-space: nowrap;
    }
    .cursor-pointer{
        cursor: pointer;
    }
  
    @media screen and (max-width: 1200px) {
        .search-inlet { display:inline-block; }
        .info-search-inlet2  { display:inline-block; }
        .info-search-inlet1  { display:none; }
    }
    @media screen and (min-width:1201px) {
        .search-inlet  { display:none; }
        .info-search-inlet2  { display:none; }
        .info-search-inlet1  { display:inline-block; }
    }
    .error_target{
		border: 1px solid red !important;
		color : red
	}
    .alert_target{
        color : red;
        font-weight: bold;
    }

    @media screen and (min-width:1366px) and (min-height:768px) {
        .divListviewHead table  {
            display: block;
            /* height: calc( 100vh - 0vh ); */
            /* max-height: calc( 100vh - 600px );
            min-height: calc( 100vh - 750px ); */
            min-height: calc( 100vh - 700px );
            max-height: calc( 100vh - 600px );
            /* height: 5%;  */
            overflow-x: auto;
        }
    }

    @media screen and (max-width:1366px) AND (max-height:768px) {
        .divListviewHead table  {
            display: block;
            min-height: calc( 100vh - 500px );;
            max-height: calc( 100vh - 450px );;
            overflow-x: auto;
        }
    }

    .loading_content_inlet{
        z-index: 50;
        background: rgba(255,255,255,0.7);
        border-radius: 3px;
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        display: block;
    }

    .loading_content_inlet i{
        position: absolute;
        top: 50%;
        left: 50%;
        margin-left: -15px;
        margin-top: -15px;
        color: #000;
        font-size: 30px;
    }
    .info_data_inlet{
        display:block;
        overflow:hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
    }
    .info_status{
        font-weight:bold;
        padding-left:15px;
        padding-right:15px;
        text-align:center;
        width :100%;
        margin-bottom: 5px;
        border: 1px solid transparent;
            border-top-color: transparent;
            border-right-color: transparent;
            border-bottom-color: transparent;
            border-left-color: transparent;
        border-radius: 4px;
        display: inline-block;
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
    }
    .info_status_done{
        color: #3c763d;
        background-color: #dff0d8;
    }
    .info_status_process{
        color: #8a6d3b;
        background-color: #fcf8e3;
    }
    .info_status_cancel{
        color: #a94442;
        background-color: #f2dede;
    }
    .info_status_default{
        color: #636b6f;
        background-color: #f0f0f0;
    }
    </style>
</head>

<body class="hold-transition skin-black fixed sidebar-mini sidebar-collapse" onload="$('#txt_search_lot').focus()">
    <!-- Site wrapper -->
    <div class="wrapper">
        <!-- main -header -->
        <header class="main-header">
            <?php 
                $this->load->view("admin/_partials/main-menu.php");
                $data['deptid'] = $id_dept;
                $this->load->view("admin/_partials/topbar.php", $data)
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
            <section class="content-header">
            </section>

            <!-- Main content -->
            <section class="content">
                <!--  box content -->
                <div class="box">
                    <div class="box-header with-border">
                        <center>
                            <h3 class="box-title"><b>OUTLET</b></h3>
                        </center>
                    </div>
                    <div class="box-body">
                        <form class="form-horizontal">
                            <div class="col-md-4">
                                <div class="col-md-12">
                                    <div class="form-group">                                                          
                                        <div class="col-lg-12 col-xs-12 col-md-12 " style="margin-bottom:5px">
                                            <input type="text" class="form-control input-sm" name="txt_search_lot" id="txt_search_lot" placeholder="cari KP/Lot" >
                                            <small class="info-search-inlet1"><b>*double clik kp/lot untuk di proses</b></small>
                                            <small class="info-search-inlet2"><b>*double clik / click button Pilih kp/lot untuk di proses</b></small>
                                        </div>
                                        <div class="col-xs-12 table-responsive example1 divListviewHead">
                                            <div role="region" aria-labelledby="HeadersCol" tabindex="0" class="rowheaders">
                                                <table id="table_list_inlet" class="table table-condesed table-hover" border="0" style="margin-bottom:0px;">
                                                    <thead>
                                                        <tr>
                                                            <th class="style bb no">No</th>
                                                            <th class="style bb">KP/Lot</th>
                                                            <th class="style bb">MC</th>
                                                            <th class="style bb "></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                            echo '<tr>';
                                                            echo '<td colspan="4">Data KP/Lot tidak ada</td>';
                                                            echo '</tr>';
                                                        ?>
                                                    </tbody>
                                                </table>
                                                <small class="info-search-inlet1"><b>*Limit Display 50 KP/Lot</b></small>
                                                <small class="info-search-inlet2"><b>*Limit Display 50 KP/Lot</b></small>
                                                <div id="example1_processing" class="table_processing" style="display: none; z-index:5;">
                                                    Processing...
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.col md 12 -->
                                <div class="col-md-12">
                                    <div class="form-group load_data_inlet">
                                        <div class="col-12 col-sm-12 col-md-12" id="info_status">
                                            <span class="info_status ">Status : -</span>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group" style="margin-bottom:0px;">
                                                <div class="col-12 col-sm-5 col-md-5 col-xl-4">
                                                    <label class="info_data_inlet">No. Mesin</label>
                                                </div>
                                                <div class="col-12 col-sm-7 col-md-7 col-xl-8">
                                                    <label>:</label>
                                                    <span id="info_mc" class="data_inlet"></span>
                                                </div>
                                                <div class="col-12 col-sm-5 col-md-5 col-xl-4">
                                                    <label class="info_data_inlet">KP / Lot</label>
                                                </div>
                                                <div class="col-12 col-sm-7 col-md-7 col-xl-8">
                                                    <label>:</label>
                                                    <span id="info_lot" class="data_inlet"></span>
                                                    <input type="hidden" name="id_inlet" id="id_inlet" class="data_inlet" >
                                                </div>
                                                <div class="col-12 col-sm-5 col-md-5  col-xl-4">
                                                    <label class="info_data_inlet">MG GJD </label>
                                                </div>
                                                <div class="col-12 col-sm-7 col-md-7 col-xl-8">
                                                    <label>:</label>
                                                    <span id="info_mg_gjd" class="data_inlet"></span>
                                                </div>
                                                <div class="col-12 col-sm-5 col-md-5">
                                                    <label class="info_data_inlet">Marketing </label>
                                                </div>
                                                <div class="col-12 col-sm-7 col-md-7">
                                                    <label>:</label>
                                                    <span id="info_marketing" class="data_inlet"></span>
                                                </div>
                                                <div class="col-12 col-sm-5 col-md-5  col-xl-4">
                                                    <label class="info_data_inlet">Corak Remark </label>
                                                </div>
                                                <div class="col-12 col-sm-7 col-md-7 col-xl-8">
                                                    <label>:</label>
                                                    <span id="info_corak_remark" class="data_inlet"></span>
                                                </div>
                                                <div class="col-12 col-sm-5 col-md-5 col-xl-4">
                                                    <label class="info_data_inlet">Warna Remark </label>
                                                </div>
                                                <div class="col-12 col-sm-7 col-md-7 col-xl-8">
                                                    <label>:</label>
                                                    <span id="info_warna_remark" class="data_inlet"></span>
                                                </div>                                        
                                            </div>
                                            <!-- /.form group -->
                                        </div>
                                        <div class="col-md-12 ">
                                            <div class="form-group" style="margin-bottom:0px;">
                                                <div class="col-12 col-sm-5 col-md-5">
                                                    <label class="info_data_inlet">Lebar/Pcs </label>
                                                </div>
                                                <div class="col-12 col-sm-7 col-md-7">
                                                    <label>:</label>
                                                    <span id="info_lebar" class="data_inlet"></span>
                                                </div>
                                                <div class="col-12 col-sm-5 col-md-5">
                                                    <label class="info_data_inlet" >Quality </label>
                                                </div>
                                                <div class="col-12 col-sm-7 col-md-7">
                                                    <label>:</label>
                                                    <span id="info_quality" class="data_inlet"></span>
                                                </div>
                                                <div class="col-12 col-sm-5 col-md-5">
                                                    <label class="info_data_inlet" class="data_inlet">Benang </label>
                                                </div>
                                                <div class="col-12 col-sm-7 col-md-7">
                                                    <label>:</label>
                                                    <span id="info_benang" class="data_inlet"></span>
                                                </div>
                                                <div class="col-12 col-sm-5 col-md-5">
                                                    <label class="info_data_inlet">Jenis Kain </label>
                                                </div>
                                                <div class="col-12 col-sm-7 col-md-7">
                                                    <label>:</label>
                                                    <span id="info_jenis_kain" class="data_inlet"></span>
                                                </div>
                                                <div class="col-12 col-sm-5 col-md-5">
                                                    <label class="info_data_inlet" class="data_inlet">Gramasi </label>
                                                </div>
                                                <div class="col-12 col-sm-7 col-md-7">
                                                    <label>:</label>
                                                    <span id="info_gramasi" class="data_inlet"></span>
                                                </div>
                                                <div class="col-12 col-sm-5 col-md-5">
                                                    <label class="info_data_inlet">Berat/mtr/panel (Kg) </label>
                                                </div>
                                                <div class="col-12 col-sm-7 col-md-7">
                                                    <label>:</label>
                                                    <span id="info_berat" class="data_inlet"></span>
                                                </div>
                                            </div>                                 
                                            <!-- /.form group -->
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group" style="margin-bottom:0px;">                                            
                                                <div class="col-12 col-sm-12 col-md-12" id="info-btn-hph" style="margin-bottom:5px">
                                                    <button type="button" class="btn btn-block btn-default info_data_inlet" name="hph" id="btn-hph" title="Detail HPH" disabled>Detail HPH</button>
                                                </div>
                                                <div class="col-12 col-sm-12 col-md-12" id="info-btn-done">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- /col-md-12-->
                            </div>
                            <!-- /col-md-4-->

                            <div class="col-md-8">
                                <!-- /.col-md-6-->
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <div class="box box-danger">
                                            <div class="box-body load_data_inlet" style="display: block; padding-bottom:10px">

                                                <div class="col-12 col-sm-12 col-md-12  col-xl-12">
                                                    <label>Potongan Ke :</label>
                                                    <span id="info_potongan"></span>
                                                </div>

                                                <div class="col-md-6 col-xs-12 ">
                                                    <div class="col-md-12 col-xs-12 pad_left_empty">
                                                        <div class="col-xs-12 col-sm-12 col-lg-4 pad_left_empty"><label>Sisa Qty HPH</label>
                                                        </div>
                                                        <div class="col-xs-6 col-sm-6 col-lg-4">
                                                            <input type='text' class="form-control input-sm text-right" name="sisa_hph_mtr" id="sisa_hph_mtr" readonly="readonly" >
                                                        </div>
                                                        <div class="col-xs-6 col-sm-6 col-lg-4">
                                                            <input type='text' class="form-control input-sm text-right" name="sisa_hph_kg" id="sisa_hph_kg" readonly="readonly" />
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-12 col-xs-12 pad_left_empty">
                                                        <div class="col-xs-12 col-sm-12 col-lg-4 pad_left_empty"><label>Qty Yrd Potong </label>
                                                        </div>
                                                        <div class="col-xs-12 col-sm-8 col-lg-4">
                                                            <input type='text' class="form-control input-sm text-right input_list" name="qty_yrd_potong" id="qty_yrd_potong" data-decimal="2" onkeyup="validAngka(this)" oninput="enforceNumberValidation(this)">
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-12 col-xs-12 pad_left_empty">
                                                        <div class="col-xs-12  col-sm-12 col-lg-4 pad_left_empty"><label>Qty Mtr HPH </label>
                                                        </div>
                                                        <div class="col-xs-12 col-sm-8 col-lg-4 ">
                                                            <input type='text' class="form-control input-sm text-right input_list" name="qty_mtr_hph" id="qty_mtr_hph" data-decimal="2" onkeyup="validAngka(this)" oninput="enforceNumberValidation(this)"/>
                                                        </div>
                                                        <small class="col-xs-6 col-sm-6 col-lg-4 " id="alert_mtr">
                                                        </small>
                                                    </div>
                                                    <div class="col-sm-12 col-xs-12 pad_left_empty">
                                                        <div class="col-xs-12 col-sm-12 col-lg-4 pad_left_empty"><label>Qty Kg HPH </label>
                                                        </div>
                                                        <div class="col-xs-12 col-sm-8 col-lg-4 ">
                                                            <input type='text' class="form-control input-sm text-right input_list" name="qty_kg_hph" id="qty_kg_hph" data-decimal="2" onkeyup="validAngka(this)" oninput="enforceNumberValidation(this)"/>
                                                        </div>
                                                        <small class="col-xs-6 col-sm-6 col-lg-4" id="alert_kg">
                                                        </small>
                                                    </div>
                                                </div>

                                                <div class="col-md-6 col-xs-12">
                                                    <div class="col-md-12 col-xs-12 pad_left_empty">
                                                        <div class="col-12 col-sm-5 col-md-5 pad_left_empty"><label class="info_data_inlet">Desain Barcode</label>
                                                        </div>
                                                        <div class="col-12 col-sm-7 col-md-7">
                                                            <label>:</label>
                                                            <span id="info_desain_barcode" class="data_inlet"></span>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12 col-xs-12 pad_left_empty">
                                                        <div class="col-12 col-sm-5 col-md-5 pad_left_empty"><label class="info_data_inlet">No Registrasi K3L</label>
                                                        </div>
                                                        <div class="col-12 col-sm-7 col-md-7">
                                                            <label>:</label>
                                                            <span id="info_k3l" class="data_inlet"></span>
                                                        </div>
                                                        <div class="col-12 col-sm-5 col-md-5 pad_left_empty"></div>
                                                        <div class="col-12 col-sm-7 col-md-7">
                                                            <small id="nama_k3l" class="info_data_inlet"></small>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12 col-xs-12 pad_left_empty" id="tampil_remark_by_grade" style="display: none;">
                                                        <div class="col-12 col-sm-5 col-md-5 pad_left_empty"><label>Remark C</label>
                                                        </div>
                                                        <div class="col-12 col-sm-8 col-md-7">
                                                            <select class="form-control input-sm " name="remark_by_grade " id="remark_by_grade" >
                                                                <?php foreach ($list_remark as $row) {
                                                                        echo "<option>".$row->nama."</option>";
                                                                    }
                                                                ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>                                               
                                                <div class="col-md-12">
                                                  <div class="col-md-4">
                                                    <label>Grade</label>
                                                    <label for="A">A</label><br>
                                                      <div class="col-md-12 col-sm-12 col-xs-12 pad_left_empty">
                                                        <div class="info-box2" >
                                                            <div class="info-box-radio">
                                                              <input type="radio" id="a" name="grade_hph" value="A" data-index=0 class="input_list" >
                                                            </div>
                                                            <span class="info-box-icon2 bg-green-active"><h1>A</h1></span>
                                                            <div class="info-box-content2">
                                                            <!-- info-box-text -->
                                                                <span class="info-box-text"> <small>+ HPH</small></span>
                                                                <span class="info-box-text"> <small>+ Barcode</small></span>
                                                                <span class="info-box-text"> <small>+ Stock</small></span>
                                                            </div>
                                                        </div>
                                                        <div class="info-box2">
                                                            <div class="info-box-radio">
                                                              <input type="radio" id="b" name="grade_hph" value="B" data-index=1 class="input_list">
                                                            </div>
                                                            <span class="info-box-icon2 bg-green-active""><h1>B</h1></span>
                                                            <div class="info-box-content2">
                                                                <span class="info-box-text"> <small>+ HPH</small></span>
                                                                <span class="info-box-text"> <small>+ Barcode</small></span>
                                                                <span class="info-box-text"> <small>+ Stock</small></span>
                                                            </div>
                                                        </div>
                                                        <div class="info-box2">
                                                            <div class="info-box-radio">
                                                              <input type="radio" id="c" name="grade_hph" value="C" data-index=2 class="input_list">
                                                            </div>
                                                            <span class="info-box-icon2 bg-green-active"><h1>C</h1></span>
                                                            <div class="info-box-content2">
                                                                <span class="info-box-text"> <small>+ HPH</small></span>
                                                                <span class="info-box-text"> <small>+ Barcode</small></span>
                                                                <span class="info-box-text"> <small>+ Stock</small></span>
                                                            </div>
                                                        </div>
                                                        <div class="info-box2">
                                                            <div class="info-box-radio">
                                                              <input type="radio" id="f" name="grade_hph" value="F" data-index=3 class="input_list">
                                                            </div>
                                                            <span class="info-box-icon2 bg-default"><h1>F</h1></span>
                                                            <div class="info-box-content2">
                                                            </div>
                                                        </div>
                                                    </div>
                                                  </div>

                                                  <div class="col-md-4">
                                                      <div class="col-lg-12 col-xs-12 col-md-12 table-responsive pad_left_empty" id="tampil_table_list_uom_jual" style="display:none">
                                                        <label>Qty Uom Jual</label>
                                                            <table class="table table-condesed table-hover rlstable" width="100%"
                                                                id="table_process_hph_grade">
                                                                <thead>
                                                                    <tr>
                                                                        <th class="style bb no">No</th>
                                                                        <th class="style bb ws">Uom Jual</th>
                                                                        <th class="style bb ws">Qty Jual</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                <?php
                                                                    foreach($data_oum_jual as $datas ){
                                                                        echo '<tr class="num list_uom_jual">';
                                                                        echo '<td></td>';
                                                                        echo '<td>'.$datas->nama.'</td>';
                                                                        echo '<td align="right"><input type="text" class="form-control input-sm text-right uom_jual input_list" id="uom_jual" name="uom_jual" data-decimal="2" data-id="'.$datas->short.'" onkeyup="validAngka(this)" oninput="enforceNumberValidation(this)"></td>';
                                                                        echo '</tr>';
                                                                    }
                                                                ?>
                                                                </tbody>
                                                            </table>                                                       
                                                        </div>
                                                  </div>
                                                  <div class="col-md-4">
                                                    <div class="col-md-12 col-xs-12 pad_left_empty tampil_label_barcode" style="display:none">
                                                        <div class="col-xs-12">
                                                            <label>Barcode</label>
                                                        </div>
                                                        <div class="col-xs-6 col-md-6 ">
                                                            <select class="form-control input-sm input_list" name="uom_label_barcode " id="uom_label_barcode"  >
                                                                <option value="">Pilih</option>
                                                                <?php foreach ($data_oum as $row) {
                                                                        echo "<option value='".$row->short."'>".$row->short."</option>";
                                                                    }
                                                                ?>
                                                            </select>
                                                        </div>
                                                        <div class="col-xs-6 col-md-6">
                                                            <input type='text' class="form-control input-sm text-right " name="qty_label" id="qty_label" readonly="readonly" data-decimal="2" oninput="enforceNumberValidation(this)" placeholder="Qty1 Jual">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12 col-xs-12 pad_left_empty tampil_label_barcode" style="display:none">
                                                        <div class="col-xs-6 col-md-6 ">
                                                            <select class="form-control input-sm input_list" name="uom2_label_barcode " id="uom2_label_barcode" >
                                                                <option value="">Pilih</option>
                                                                <?php foreach ($data_oum as $row) {
                                                                        echo "<option value='".$row->short."'>".$row->short."</option>";
                                                                    }
                                                                ?>
                                                            </select>
                                                        </div>
                                                        <div class="col-xs-6 col-md-6">
                                                            <input type='text' class="form-control input-sm text-right" name="qty2_label" id="qty2_label" readonly="readonly" data-decimal="2" oninput="enforceNumberValidation(this)" placeholder="Qty2 Jual">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12 col-xs-12 pad_left_empty tampil_label_barcode" style="display:none; padding-top:10px">
                                                        <div class="col-xs-6 col-md-6">
                                                          <input type='text' class="form-control input-sm text-right input_list" name="lebar_jadi_label" id="lebar_jadi_label"  >
                                                        </div>
                                                        <div class="col-xs-6 col-md-6 ">
                                                            <select class="form-control input-sm " name="uom_lebar_jadi_label_barcode" id="uom_lebar_jadi_label_barcode" >
                                                                <option value=""></option>
                                                                <?php foreach ($data_oum as $row) {
                                                                        echo "<option value='".$row->short."'>".$row->short."</option>";
                                                                    }
                                                                ?>
                                                            </select>
                                                        </div>
                                                        <div class="col-xs-12 col-md-12">
                                                            <label><input type="checkbox" name="print" value="Print"> Print Label Barcode</label>
                                                        </div>
                                                        <!-- <div class="col-xs-6 col-md-6">
                                                            <label><input type="radio" name="print" value="Print"> Print </label>
                                                        </div> -->
                                                    </div>
                                                    <div class="col-md-12 col-xs-12 pad_left_empty" style="padding-top:10px">
                                                        <button type="button" name="btn-simpan" id="btn-simpan" class="btn btn-sm btn-primary" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing...">Simpan</button>
                                                    </div>
                                                  </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- box box-danger -->
                                    </div>
                                    <!-- /form group -->
                                </div>
                                <!-- col-md-12 -->
                            </div>
                            <!-- col-md-8 -->

                        </form>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </section>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->

    </div>

    <?php $this->load->view("admin/_partials/js.php") ?>

    <?php $this->load->view("admin/_partials/modal.php") ?>

    <script type="text/javascript">

        // setInterval( get_list_lot_hph, 5000 );
        // setInterval( get_count_inlet, 5000 );

        // body onload
        get_list_lot_hph();
        // get_count_inlet();

        // $("#qty_yrd_potong").click(function () {
        //     $("html, body").animate({ scrollTop: $(".box .box-danger").offset().top }, 300);
        //     return true;
        // });

        var arr_tmp_grade = [];

        function myFunction(get){

            arr_tmp_grade = [];
            id  = $(get).attr('data-id');
            lot = $(get).attr('data-lot');
            // clear value
            $("input[type=text].input_list").val('');
            $("#qty_label").val('');
            $("#uom_label_barcode").val('');
            $("#qty2_label").val('');
            $("#uom2_label_barcode").val('');
            $(".data_inlet").html('');
            
            $("#lebar_jadi_label").val('');
            $("#uom_lebar_jadi_label_barcode").val('');

            $("input[name=grade_hph]").prop("checked",false);
            $('.info-box2-focus').toggleClass('info-box2-focus');
      
            $('#info_potongan').html('-');
            $('#sisa_hph_mtr').val('');
            $('#sisa_hph_kg').val('');
            $('#info_status').html(' <span class="info_status ">Status : -</span>');
            $.ajax({
                type     : "POST",
                dataType : "json",
                url :'<?php echo base_url('manufacturing/outlet/search_data_inlet')?>',
                beforeSend: function(e) {
                    if(e && e.overrideMimeType) {
                        e.overrideMimeType("application/json;charset=UTF-8");
                    }                           
                    $('.load_data_inlet').append('<div class="loading_content_inlet"><i class="fa fa-refresh fa-spin"></i></div>');

                },
                data: {id:id,lot:lot},
                success: function(data){
                    if(data.sesi == "habis"){
                        //alert jika session habis
                        alert_modal_warning(data.message);
                        window.location.replace('index');
                    }else if(data.status == 'failed'){
                        alert_notify(data.icon,data.message,data.type,function(){});
                        $('.load_data_inlet div.loading_content_inlet').remove('');
                        $("#info-btn-hph").html('');
                        $("#info-btn-hph").append('<button type="button" class="btn btn-block btn-default info_data_inlet" name="btn-done" id="btn-hph" title="Detail HPH" disabled>Detail HPH</button>');
                        $("#info-btn-done").html('');
                      
                    }else{
                        id_inlet = '';
                        status_inlet = '';
                        $.each(data.record, function(key, value) {
                            $('#id_inlet').val(value.id);
                            $('#info_mc').html(value.nama_mesin);
                            $('#info_lot').html(value.lot);
                            $('#info_mg_gjd').html(value.kode_mrp);

                            $('#info_marketing').html(value.nama_marketing);
                            $('#info_corak_remark').html(value.corak_remark);
                            $('#info_warna_remark').html(value.warna_remark);
                            $('#info_lebar').html(value.lebar_jadi+' '+value.uom_lebar_jadi);
                            $('#info_quality').html(value.quality);
                            $('#info_benang').html(value.benang);
                            $('#info_jenis_kain').html(value.nama_jenis_kain);
                            $('#info_gramasi').html(value.gramasi);
                            $('#info_berat').html(value.berat);
                            $('#info_desain_barcode').html(value.desain_barcode);
                            $('#info_k3l').html(value.k3l);
                            $('#nama_k3l').html(value.nama_k3l);
                            $('#lebar_jadi_label').val(value.lebar_jadi);
                            $('#uom_lebar_jadi_label_barcode').val(value.uom_lebar_jadi);
                            $('.info_status').html(value.nama_status);
                            $('.info_status').addClass(value.tipe_alert);
                            id_inlet = value.id;
                            status_inlet = value.status;
                        });
                        $("#info-btn-hph").html('');
                        $("#info-btn-hph").append(' <button type="button" class="btn btn-block btn-default info_data_inlet" name="btn-done" id="btn-hph" onclick="get_detail_hph('+id_inlet+')" title="Detail HPH" >Detail HPH</button>');

                        $("#info-btn-done").html('');
                        if(status_inlet == 'draft' ||  status_inlet == 'process'){
                            $("#info-btn-done").append(' <button type="button" class="btn btn-block btn-success info_data_inlet" name="btn-done" id="btn-done"  title="Done HPH" >Done HPH</button>');
                        }

                        if(data.sisa_target != ''){
                            $('#info_potongan').html(data.sisa_target['potongan_ke']);
                            $('#sisa_hph_mtr').val(data.sisa_target['qty']);
                            $('#sisa_hph_kg').val(data.sisa_target['qty2']);
                        }

                        // clear alert
                        $('#qty_mtr_hph').removeClass('error_target');
                        $('#alert_mtr').html('');
                        $('#alert_mtr').removeClass('alert_target');

                        $('#qty_kg_hph').removeClass('error_target');
                        $('#alert_kg').html('');
                        $('#alert_kg').removeClass('alert_target');
                        setTimeout(function() { alert_notify(data.icon,data.message,data.type,function(){
                            // $('#qty_yrd_potong').filter(function() { return $(this).val() == "" }).first().focus();
                            $('.load_data_inlet div.loading_content_inlet').remove('');
                            $('#qty_yrd_potong').focus();
                        });},1000); 
                    }
                },error: function (xhr, ajaxOptions, thrownError) {
                    alert(xhr.responseText)
                    $('#btn-proses').button('reset');
                    // clear alert
                    $('.load_data_inlet div.loading_content_inlet').remove('');
                }
            });

        }

        function get_detail_hph(param){
            $("#view_data").modal({
                show: true,
                backdrop: 'static'
            })
            $(".view_body").html('<center><h5><img src="<?php echo base_url('dist/img/ajax-loader.gif') ?> "/><br>Please Wait...</h5></center>');
            title = 'Detail HPH';
            if(param){
                $('.modal-title').text(title);
                    $.post('<?php echo site_url()?>manufacturing/outlet/view_detail_hph_modal',
                    {param:param},
                    function(html){
                        setTimeout(function() {$(".view_body").html(html);});
                    }   
                );
            }

        }

        // get count inlet
        // function get_count_inlet(){

        //     $.ajax({
        //         type     : "POST",
        //         dataType : "json",
        //         url :'<?php echo base_url('manufacturing/outlet/get_count_lot_inlet')?>',
        //         beforeSend: function(e) {
        //             if(e && e.overrideMimeType) {
        //                 e.overrideMimeType("application/json;charset=UTF-8");
        //             }                               
        //             $('.load_inlet').append('<div class="loading_content_inlet"><i class="fa fa-refresh fa-spin"></i></div>');
        //         },
        //         success: function(data){

        //             $("#jml_lot_blm_inlet").html(data.jml_lot_blm_inlet);
        //             $("#jml_lot_inlet").html(data.jml_lot_inlet);
        //             $('.load_inlet .loading_content_inlet').remove('');
                  
        //         },error: function (xhr, ajaxOptions, thrownError) {
        //             alert(xhr.responseText)
        //         }
        //     });

        // }

        
        $(document).on("keyup", "#txt_search_lot", function(){
            get_list_lot_hph();
        });

        function get_list_lot_hph(){

            let lot = $('#txt_search_lot').val();

            $.ajax({
                type     : "POST",
                dataType : "json",
                url :'<?php echo base_url('manufacturing/outlet/get_list_lot_hph')?>',
                beforeSend: function(e) {
                    if(e && e.overrideMimeType) {
                        e.overrideMimeType("application/json;charset=UTF-8");
                    }                               
                    $("#table_list_inlet tbody").remove();
                    $("#example1_processing").css('display','');// show loading processing in table
                },
                data: {lot:lot},
                success: function(data){
                    if(data.sesi == "habis"){
                        //alert jika session habis
                        alert_modal_warning(data.message);
                        window.location.replace('index');
                    }else{

                        var tbody = $("<tbody />");
                        var no    = 1;
                        loop      = 3;
                        $.each(data.record, function(key, value) {
                            // for(a=1; a<loop; a++){
                            lot_encr = htmlentities_script(value.lot);
                            var tr  = "<tr class='num' ondblclick='myFunction(this)' data-id="+value.id+" data-lot="+value.lot+">"
                                    + "<td class='cursor-pointer'>"+no++ +".</td>"
                                    + "<td class='cursor-pointer'>"+value.lot+"</td>"
                                    + "<td class='cursor-pointer'>"+value.nama_mesin+"</td>"
                                    + "<td class='cursor-pointer'><button type='button' id='btn-search-inlet' class='btn btn-xs btn-primary search-inlet' onclick='myFunction(this)'data-id='"+value.id+"' data-lot='"+value.lot+"'><i class='fa fa-check'></i>  Pilih </td>"
                                    + "</tr>";
                                    tbody.append(tr);
                            // }
                        });

                        if(data.record == 0){
                            tr = "<tr><td colspan='3'>Data KP/Lot tidak ada</td></tr>"
                            tbody.append(tr);
                        }
                        $("#table_list_inlet tbody").empty();
                        $("#table_list_inlet").append(tbody);
                        $("#example1_processing").css('display','none');// hidden loading processing in table
                    }
                },error: function (xhr, ajaxOptions, thrownError) {
                    alert(xhr.responseText)
                    $('#btn-proses').button('reset');
                    $("#example1_processing").css('display','none');// hidden loading processing in table
                }
            });

        }

        function cek_info_kp(param){
            $("#view_data").modal({
                show: true,
                backdrop: 'static'
            })
            $(".view_body").html('<center><h5><img src="<?php echo base_url('dist/img/ajax-loader.gif') ?> "/><br>Please Wait...</h5></center>');
            if(param == 0){
                title = 'List KP/Lot Belum Inlet';
                open  = true;
            }else{
                title = 'List KP/Lot Sudah Inlet';
                open  = false;
            }

            if(open){
                $('.modal-title').text(title);
                    $.post('<?php echo site_url()?>manufacturing/outlet/view_detail_lot_modal',
                    {param:param},
                    function(html){
                        setTimeout(function() {$(".view_body").html(html);});
                    }   
                );
            }

        }

        // load new page print
        function print_voucher() {
            var win = window.open();
            win.document.write($("#printed").html());
            win.document.close();
            setTimeout(function(){ win.print(); win.close();}, 200);
            $('#qty_yrd_potong').focus();
        }

        // simpan outlet
        $(document).on("click", "#btn-simpan", function(){
            
            let id = $("#id_inlet").val();
            let sisa_hph_mtr= $("#sisa_hph_mtr").val();
            let sisa_hph_kg = $("#sisa_hph_kg").val();
            let hph_yard    = $("#qty_yrd_potong").val();
            let hph_mtr     = $("#qty_mtr_hph").val();
            let hph_kg      = $("#qty_kg_hph").val();
            let grade_radio = $("input[name='grade_hph']:checked").val();
            let qty_label   = $("#qty_label").val();
            let uom_label_barcode = $("#uom_label_barcode").val();
            let qty2_label  = $("#qty2_label").val();
            let uom2_label_barcode = $("#uom2_label_barcode").val();
            let lebar_jadi_label = $("#lebar_jadi_label").val();
            let uom_lebar_jadi_label_barcode = $("#uom_lebar_jadi_label_barcode").val();
            let remark_by_grade = $("#remark_by_grade").val();
            let print           = $("input[name=print]").is(':checked');
            let arr = new Array();
            $("#table_process_hph_grade tbody tr.list_uom_jual .uom_jual").each(function(index, element) {
					if ($(element).val()!=="") {
						arr.push({
                            uom_jual 		: $(element).attr('data-id'),// uom
							value_uom_jual  : $(element).val(),
						});
					}
			}); 

            if(id == ''){
                alert_notify("fa fa-warning","Data Inlet Kosong !","danger",function(){});
            }else if(grade_radio == ''){
                alert_notify("fa fa-warning","Grade Harus dipilih !","danger",function(){});
            }else{
                // alert("tes");
                $.ajax({
                    type     : "POST",
                    dataType : "json",
                    url :'<?php echo base_url('manufacturing/outlet/save_outlet')?>',
                    beforeSend: function(e) {
                        if(e && e.overrideMimeType) {
                            e.overrideMimeType("application/json;charset=UTF-8");
                        }                  
                        $('#btn-simpan').button('loading');
                        please_wait(function(){});
                    },
                    data: { id:id,
                            sisa_hph_mtr:sisa_hph_mtr,
                            sisa_hph_kg:sisa_hph_kg,
                            hph_mtr:hph_mtr,
                            hph_kg :hph_kg,
                            grade_hph    :grade_radio, 
                            arr_uom_jual    :JSON.stringify(arr),
                            qty_label       : qty_label,
                            uom_label_barcode : uom_label_barcode,
                            qty2_label      : qty2_label,
                            uom2_label_barcode : uom2_label_barcode,
                            lebar_jadi_label: lebar_jadi_label,
                            uom_lebar_jadi_label_barcode:uom_lebar_jadi_label_barcode,
                            remark_by_grade:remark_by_grade,
                            print:print
                    },
                    success: function(data){
                        if(data.sesi == "habis"){
                            //alert jika session habis
                            alert_modal_warning(data.message);
                            window.location.replace('index');
                        }else if(data.status == 'failed'){
                            unblockUI( function() {
                                setTimeout(function() { alert_notify(data.icon,data.message,data.type,function(){ 
                                        $('#btn-simpan').button('reset');
                                });},1000); 
                            });

                            if(data.field!=''){
                                $('#'+data.field).focus();
                            }
                        }else{
                            $('#info_status').html(' <span class="info_status ">Status : -</span>');
                            var divp = document.getElementById('printed');
                            divp.innerHTML = data.data_print;
                            unblockUI( function() {
                                setTimeout(function() { alert_notify(data.icon,data.message,data.type,function(){ 
                                        if(print && grade_radio != 'F'){
                                            print_voucher();
                                        }
                                        $('#btn-simpan').button('reset');
                                });},1000); 
                            });

                            // update_status
                            $('.info_status').html(data.nama_status);
                            $('.info_status').addClass(data.tipe_alert);

                            get_list_lot_hph();
                            // get_count_inlet();

                            // clear_value
                            $("input[type=text].input_list").val('');
                            $("#qty_label").val('');
                            $("#uom_label_barcode").val('');
                            $("#qty2_label").val('');
                            $("#uom2_label_barcode").val('');
                            $("#lebar_jadi_label").val(lebar_jadi_label);
                            // $("#uom_lebar_jadi_label_barcode").val('');
                            $("#remark_by_grade").prop('selectedIndex', 0);
                            if(data.sisa_target != ''){
                                $('#info_potongan').html(data.sisa_target['potongan_ke']);
                                $('#sisa_hph_mtr').val(data.sisa_target['qty']);
                                $('#sisa_hph_kg').val(data.sisa_target['qty2']);
                            }else{
                                $('#info_potongan').html('-');
                                $('#sisa_hph_mtr').val('');
                                $('#sisa_hph_kg').val('');
                            }
                            $('#qty_yrd_potong').focus();

                            // clear alert
                            $('#alert_kg').removeClass('alert_target');
                            $('#qty_kg_hph').removeClass('error_target');
                            $('#alert_kg').html('');
                            $('#qty_mtr_hph').removeClass('error_target');
                            $('#alert_mtr').html('');
                            $('#alert_mtr').removeClass('alert_target');
                            
                        }
                            
                    },error: function (xhr, ajaxOptions, thrownError) {
                        alert(xhr.responseText)
                        $('#btn-simpan').button('reset');
                        unblockUI( function() {});
                    }
                });
            }


        });


        $(document).on("keyup", "#qty_yrd_potong", function(){
            value_yrd = $('#qty_yrd_potong').val();
            if(isNaN(value_yrd)){
                value_yrd = 0;
            }
            result = konversi_uom('Yrd','Mtr',value_yrd);
            if(result == 0){
                result = '';
            }
            $('#qty_mtr_hph').val(result);
            $('#table_process_hph_grade tbody tr.list_uom_jual input[data-id="Yrd"]').val(value_yrd);
            $('#table_process_hph_grade tbody tr.list_uom_jual input[data-id="Mtr"]').val(result);
            check_target('mtr');
        });

        // konvert mtr to yrd
        $(document).on("keyup", "#qty_mtr_hph", function(){
            value_mtr = $('#qty_mtr_hph').val();
            if(isNaN(value_mtr)){
                value_mtr = 0;
            }
            result = konversi_uom('Mtr','Yrd',value_mtr);
            if(result == 0){
                result = '';
            }
            $('#qty_yrd_potong').val(result);
            $('#table_process_hph_grade tbody tr.list_uom_jual input[data-id="Mtr"]').val(value_mtr);
            $('#table_process_hph_grade tbody tr.list_uom_jual input[data-id="Yrd"]').val(result);
            check_target('mtr');
        });

        // value kg to uom jual kg
        $(document).on("keyup", "#qty_kg_hph", function(){
            value_kg = $('#qty_kg_hph').val();
            $('#table_process_hph_grade tbody tr.list_uom_jual input[data-id="Kg"]').val(value_kg);
            check_target('kg');

        });


        $(document).on("change", "#uom_label_barcode", function(){
            uom_label_barcode = $(this).val();
            get_value_uom(uom_label_barcode,'#qty_label');
        });

        $(document).on("change", "#uom2_label_barcode", function(){
            uom_label_barcode = $(this).val();
            get_value_uom(uom_label_barcode,'#qty2_label');
        });

        function get_value_uom(uom,key){
            $("#table_process_hph_grade tbody tr.list_uom_jual .uom_jual").each(function(index, element) {
                if($(element).attr('data-id') == uom){
                    $(key).val($(element).val());
                    return false;
                }else{
                    $(key).val('');
                }
			}); 
        }

        function check_target(field){
            sisa_mtr = $('#sisa_hph_mtr').val();
            sisa_kg  = $('#sisa_hph_kg').val();

            value_mtr = $('#qty_mtr_hph').val();
            value_kg = $('#qty_kg_hph').val();
            if(field == 'mtr'){
                if(parseFloat(value_mtr) > parseFloat(sisa_mtr)){
                    $('#qty_mtr_hph').addClass('error_target');
                    $('#alert_mtr').html('*melebihi Target !!');
                    $('#alert_mtr').addClass('alert_target');
                }else{
                    $('#qty_mtr_hph').removeClass('error_target');
                    $('#alert_mtr').html('');
                    $('#alert_mtr').removeClass('alert_target');
                }
            }else if(field == 'kg'){
                if(parseFloat(value_kg) > parseFloat(sisa_kg)){
                    $('#qty_kg_hph').addClass('error_target');
                    $('#alert_kg').addClass('alert_target');
                    $('#alert_kg').html('*melebihi Target !!');
                }else{
                    $('#alert_kg').removeClass('alert_target');
                    $('#qty_kg_hph').removeClass('error_target');
                    $('#alert_kg').html('');
                }
            }

        }

        // $(document).on("click", "input[name='grade_hph']", function(){
        //     show_hide(arr_tmp_grade);
        //     arr_tmp_grade = [];
        // });

        $(document).on("click",".info-box2", function(){
            $('.info-box2-focus').toggleClass('info-box2-focus');
            $(this).toggleClass('info-box2-focus');
            var id = $(this).find("input[name='grade_hph']").attr("id");
            // document.getElementById(id_area).click();
            $("#"+id).prop("checked",true);
            $("#"+id).focus();
            // alert('info')
            show_hide(arr_tmp_grade)
            arr_tmp_grade = [];
            arr_tmp_grade.push(id);
        });


        function show_hide(check_before){

            let grade_check = $("input[name='grade_hph']:checked").val();
            // value = $( $("input[name='grade_hph']:checked")).attr('id');

            if(check_before[0] == 'f' && check_before[0]){
                $('#qty_yrd_potong').val('');
                $('#qty_kg_hph').val('');
                $('#qty_mtr_hph').val('');
                $(".list_uom_jual td input[type=text].input_list").val('');
                $('#uom_label_barcode').val('');
                $('#qty_label').val('');
                $('#uom2_label_barcode').val('');
                $('#qty2_label').val('');               
            }

            // alert(grade_check);
            if(grade_check == 'C'){
                $('#tampil_remark_by_grade').show();
                $("#tampil_table_list_uom_jual").show();
                $('.tampil_label_barcode').show();
            }else if(grade_check == 'F'){
                $('#tampil_remark_by_grade').hide();
                $('.tampil_label_barcode').hide();
                $("#tampil_table_list_uom_jual").hide();
                if($('#sisa_hph_mtr').val() < 0){
                    $('#qty_mtr_hph').val(0);
                }else{
                    $('#qty_mtr_hph').val($('#sisa_hph_mtr').val());
                }

                if($('#sisa_hph_kg').val() < 0){
                    $('#qty_kg_hph').val(0);
                }else{
                    $('#qty_kg_hph').val($('#sisa_hph_kg').val());
                }
                result = konversi_uom('Mtr','Yrd',$('#sisa_hph_mtr').val());
                if(result > 0){
                    $('#qty_yrd_potong').val(result);
                }else{
                    $('#qty_yrd_potong').val('');
                }
                check_target('kg');
                check_target('mtr');
            }else if(grade_check == 'A' || grade_check == 'B'){
                $("#tampil_table_list_uom_jual").show();
                $('.tampil_label_barcode').show();
                $('#tampil_remark_by_grade').hide();
            }
           
        }
        
        const inputs = Array.prototype.slice.call(
            document.querySelectorAll('.input_list')
        );

        const radios = Array.prototype.slice.call(
            document.querySelectorAll('input[name=grade_hph]')
        );

        inputs.forEach((input) => {
            input.addEventListener('keydown', (event) => {
                const num = Number(event.which);
                // if (num && num >= 0 && num <= 9) { // Only allow numbers
                if (num == 13) { // Only event enter
                    console.log(input.value.length );

                    if (input.value.length >= input.maxLength) {
                        event.preventDefault();
                        focusNext();
                    }
                    if(event.target.nodeName === 'SELECT'){
                        event.preventDefault();
                        focusNext();
                    }
                }
            });
        });

        function focusNext() {
            const currInput = document.activeElement;
            const currInputIndex = inputs.indexOf(currInput);
            const nextinputIndex =
                (currInputIndex + 1) % inputs.length;
            const input = inputs[nextinputIndex];
            // alert(currInputIndex)
            if(currInputIndex >=2 && currInputIndex <= 5){
                const currRadioIndex = radios.indexOf(currInput) + 1;
                $('input[name=grade_hph]')[currRadioIndex].checked = true;
                // const currRadioIndexNow = radios.indexOf(currInput) - 1;
                let id_area = $("input[name='grade_hph']:checked").attr('id');
                document.getElementById(id_area).click();
            }
            if(nextinputIndex == 7){
                const inputa = inputs[0];
                inputa.focus();
                $("#f").prop("checked",false);
                $('.info-box2-focus').toggleClass('info-box2-focus');
                arr_tmp_grade = [];
                // clear 
                $('#qty_yrd_potong').val('');
                $('#qty_kg_hph').val('');
                $('#qty_mtr_hph').val('');
                $(".list_uom_jual td input[type=text].input_list").val('');
                $('#uom_label_barcode').val('');
                $('#qty_label').val('');
                $('#uom2_label_barcode').val('');
                $('#qty2_label').val('');
            }else{
                input.focus();
            }
                       
        }

        //html entities javascript
        function htmlentities_script(str) {
            return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
        }

        function konversi_uom(uom1From,uom2To,valueUom){

            let arr_konversi = new Array();
            list_konversi = <?php echo $uom_konversi?>
            // alert(JSON.stringify(list_konversi));
            result_convert = 0;
            $.each(list_konversi, function(key, val) {
                if(val.uom1 == uom1From && val.uom2 == uom2To){
                    // alert(val.faktor);
                    result_convert = valueUom*val.faktor;
                }
            });

            fixed = roundNum(result_convert)
            return fixed;

        }

        //validasi round decimal 
        function roundNum(number){
            return +(Math.round(number + "e+2") + "e-2");
        }

        // validasi only angka
        function validAngka(a){   
            if(!/^[0-9.]+$/.test(a.value)){
            //a.value = a.value.substring(0,a.value.length-1);
            a.value = a.value.replace(/[^0-9.-]/, '')
            return true;
            }
        }

        // validasi decimal
        function enforceNumberValidation(ele) {
            if ($(ele).data('decimal') != null) {
                // found valid rule for decimal
                var decimal = parseInt($(ele).data('decimal')) || 0;
                var val = $(ele).val();
                if (decimal > 0) {
                    var splitVal = val.split('.');
                    if (splitVal.length == 2 && splitVal[1].length > decimal) {
                        // user entered invalid input
                        $(ele).val(splitVal[0] + '.' + splitVal[1].substr(0, decimal));
                    }
                } else if (decimal == 0) {
                    // do not allow decimal place
                    var splitVal = val.split('.');
                    if (splitVal.length > 1) {
                        // user entered invalid input
                        $(ele).val(splitVal[0]); // always trim everything after '.'
                    }
                }
            }
        }

        // done outlet
        $(document).on("click", "#btn-done", function(e){
            e.preventDefault();
            let id = $("#id_inlet").val();

            if(id == ''){
                alert_notify("fa fa-warning","Data Inlet Kosong !","danger",function(){});
            }else{
                bootbox.confirm({
                    message: "Anda yakin ingin menyelesaikan HPH ini ?",
                    title: "Done HPH !",
                    buttons: {
                            confirm: {
                                label: 'Yes',
                                className: 'btn-primary btn-sm'
                            },
                            cancel: {
                                label: 'No',
                                className: 'btn-default btn-sm'
                            },
                    },
                    callback: function (result) {
                        if(result == true){
                            please_wait(function(){});
                            $('#btn-done').button('loading');
                            $.ajax({
                                    type: 'POST',
                                    dataType : 'json',
                                    url :'<?php echo base_url('manufacturing/outlet/done_hph')?>',
                                    beforeSend: function(e) {
                                        if(e && e.overrideMimeType) {
                                            e.overrideMimeType("application/json;charset=UTF-8");
                                        }                  
                                        $('#btn-done').button('loading');
                                        please_wait(function(){});
                                    },
                                    data: { id:id},
                                    error: function (xhr, ajaxOptions, thrownError) { 
                                        alert("Error Done HPH");
                                        $('#btn-done').button('reset');
                                        unblockUI( function(){});
                                    }
                            })
                            .done(function(data){
                                if(data.sesi == "habis"){
                                    alert_modal_warning(data.message);
                                    window.location.replace('index');
                                }else if(data.status == 'failed'){
                                    unblockUI( function() {
                                        setTimeout(function() { alert_notify(data.icon,data.message,data.type,function(){ 
                                            $('#btn-done').button('reset');
                                            });},1000); 
                                        });
                                }else{
                                    $('#info_status').html(' <span class="info_status ">Status : -</span>');
                                    unblockUI( function() {
                                        setTimeout(function() { alert_notify(data.icon,data.message,data.type,function(){ 
                                                $('#btn-done').button('reset');
                                            });},1000); 
                                    });

                                    // update_status
                                    $('.info_status').html(data.nama_status);
                                    $('.info_status').addClass(data.tipe_alert);

                                    get_list_lot_hph();
                                    // get_count_inlet();
                                            
                                }
                        
                            });
                        }
                    }
                });
            }
        });
        
    </script>

</body>

</html>