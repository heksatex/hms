<!DOCTYPE html>
<html>
<head>
  <?php $this->load->view("admin/_partials/head.php") ?>
  <link rel="stylesheet" type="text/css" href="<?php echo base_url('dist/css/tableScroll.css') ?>">
  <style type="text/css">
    @media (min-width: 300px) {
        .btn-style-proc {
         padding-left: 30px !important;
        }
    }
    .nowrap{
        white-space: nowrap;
    }
    .max-width-5{
        max-width:5px
    }
    /* .divListviewHead table  {
        display: block;
        min-height: calc( 100vh - 680px);
        max-height: calc( 100vh - 600px );
        overflow-x: auto;
    } */
  </style>
</head>

<body class="hold-transition skin-black fixed sidebar-mini" onload="$('#corak_remark').focus()"> 
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
     <div id ="status_bar">
       <?php 
         $data['jen_status'] = $inlet->status;
         $this->load->view("admin/_partials/statusbar.php", $data); 
       ?>
      </div>
    </section>

    <!-- Main content -->
    <section class="content">

      <!--  box content -->
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title">Form  Edit Inlet : <b><?php echo $inlet->lot;?></b></h3>
        </div>

        <div class="box-body">
            <form class="form-horizontal">

                <div class="form-group">

                    <div class="col-md-6">
                        <div class="col-md-12 col-xs-12">
                            <div class="col-xs-4"><label>Tanggal Inlet</label></div>
                            <div class="col-xs-8 col-md-8">
                                <input type='text' class="form-control input-sm " name="tgl_buat" id="tgl_buat" readonly="readonly" value="<?php echo $inlet->tanggal?>"  />
                            </div>                                    
                        </div>
                        <div class="col-md-12 col-xs-12">
                            <div class="col-xs-4"><label>KP / Lot  </label></div>
                            <div class="col-xs-8">
                                <input type="hidden" name="quant_id" id="quant_id" value="<?php echo $inlet->quant_id;?>" readonly="readonly"  > 
                                <input type="hidden" name="id" id="id" value="<?php echo $id;?>" readonly="readonly" >
                                <input type="text" class="form-control input-sm" name="lot" id="lot"  readonly="readonly" value="<?php echo $inlet->lot; ?>"/>                    
                            </div>                                    
                        </div>
                        <div class="col-md-12 col-xs-12">
                            <div class="col-xs-4"><label>Marketing</label></div>
                            <div class="col-xs-8 col-md-8">
                                <select class="form-control input-sm select2" name="marketing" id="marketing" disabled>
                                  <option value="">-- Pilih Marketing --</option>
                                    <?php 
                                        $selected = "";
                                        foreach ($sales_group as $row) {
                                            if($row->kode_sales_group == $inlet->sales_group) $selected = "selected" ?? '';
                                            echo "<option value='".$row->kode_sales_group."' ".$selected.">".$row->nama_sales_group."</option>";
                                            $selected = "";
                                        }
                                    ?>
                                </select>
                            </div>                                    
                        </div>
                        <div class="col-md-12 col-xs-12">
                            <div class="col-xs-4"><label>MG GJD</label></div>
                            <div class="col-xs-8 col-md-8">
                                <input type='text' class="form-control input-sm " name="kode_mrp" id="kode_mrp" readonly="readonly" value="<?php echo $inlet->kode_mrp; ?>"/>
                            </div>                                    
                        </div>
                        <div class="col-md-12 col-xs-12">
                            <div class="col-xs-4"><label>Kode Produk</label></div>
                            <div class="col-xs-8 col-md-8">
                                <input type='text' class="form-control input-sm " name="kode_produk" id="kode_produk" readonly="readonly" value="<?php echo $inlet->kode_produk; ?>"/>
                            </div>                                    
                        </div>
                        <div class="col-md-12 col-xs-12">
                            <div class="col-xs-4"><label>Nama Produk / Corak</label></div>
                            <div class="col-xs-8 col-md-8">
                                <input type='text' class="form-control input-sm " name="nama_produk" id="nama_produk" readonly="readonly" value="<?php echo htmlentities($inlet->nama_produk); ?>"/>
                            </div>                                    
                        </div>
                        <div class="col-md-12 col-xs-12">
                            <div class="col-xs-4"><label>warna</label></div>
                            <div class="col-xs-8 col-md-8">
                                <input type='text' class="form-control input-sm " name="warna" id="warna" readonly="readonly" value="<?php echo htmlentities($inlet->nama_warna); ?>" />
                            </div>                                    
                        </div>
                        <div class="col-md-12 col-xs-12">
                            <div class="col-xs-4"><label>Corak Remark</label></div>
                            <div class="col-xs-8 col-md-8">
                                <input type='text' class="form-control input-sm " name="corak_remark" id="corak_remark" value="<?php echo htmlentities($inlet->corak_remark); ?>" />
                            </div>                                    
                        </div>
                        <div class="col-md-12 col-xs-12">
                            <div class="col-xs-4"><label>Warna Remark</label></div>
                            <div class="col-xs-8 col-md-8">
                                <input type='text' class="form-control input-sm " name="warna_remark" id="warna_remark" value="<?php echo htmlentities($inlet->warna_remark); ?>" />
                            </div>                                    
                        </div>
                        <div class="col-md-12 col-xs-12">
                            <div class="col-xs-4"><label>Lebar Jadi / Pcs</label></div>
                            <div class="col-xs-8 col-md-8">
                                <input type='text' class="form-control input-sm " name="lebar_jadi" id="lebar_jadi" value="<?php echo $inlet->lebar_jadi; ?>" />
                            </div>                                    
                        </div>
                        <div class="col-md-12 col-xs-12">
                            <div class="col-xs-4"><label>Uom Lebar Jadi</label></div>
                            <div class="col-xs-8 col-md-8">
                                <select class="form-control input-sm select2" name="uom_lebar_jadi" id="uom_lebar_jadi"  >
                                    <option value="">-- Pilih Satuan Lebar Jadi --</option>
                                    <?php 
                                        $selected = "";
                                        foreach ($uom as $row) {
                                            if($row->short == $inlet->uom_lebar_jadi) $selected = "selected" ?? '';
                                            echo "<option value='".$row->short."' ".$selected.">".$row->short."</option>";
                                            $selected = "";
                                        }
                                    ?>
                                </select>                
                            </div>                   
                        </div>
                        <div class="col-md-12 col-xs-12">
                            <div class="col-xs-4"><label>Jenis Kain</label></div>
                            <div class="col-xs-8 col-md-8">
                                <select class="form-control input-sm select2" name="jenis_kain" id="jenis_kain" disabled >
                                    <option value="">-- Pilih Jenis Kain --</option>
                                    <?php 
                                        $selected = "";
                                        foreach ($jenis_kain as $row) {
                                            if($row->id == $inlet->id_jenis_kain) $selected = "selected" ?? '';
                                            echo "<option value='".$row->id."' ".$selected.">".$row->nama_jenis_kain."</option>";
                                            $selected = "";

                                        }
                                    ?>
                                </select> 
                            </div>                                    
                        </div>
                        <div class="col-md-12 col-xs-12" id="tampil_gramasi" style="display: inline;">
                            <div class="col-xs-4"><label>Gramasi</label></div>
                            <div class="col-xs-8 col-md-8">
                                <input type='text' class="form-control input-sm " name="gramasi" id="gramasi" value="<?php echo $inlet->gramasi; ?>" onkeyup="validAngka(this)"/>
                            </div>                                    
                        </div>
                        <div class="col-md-12 col-xs-12" id="tampil_berat" style="display: inline;"> 
                            <div class="col-xs-4"><label>Berat/Mtr/panel (kg)</label></div>
                            <div class="col-xs-8 col-md-8">
                                <input type='text' class="form-control input-sm " name="berat" id="berat" value="<?php echo $inlet->berat; ?>" onkeyup="validAngka(this)"/>
                            </div>                                    
                        </div>
                        <div class="col-md-12 col-xs-12">
                            <div class="col-xs-4"><label>Benang</label></div>
                            <div class="col-xs-8 col-md-8">
                                <input type='text' class="form-control input-sm " name="benang" id="benang"  value="<?php echo $inlet->benang; ?>" />
                            </div>                                    
                        </div>
                        <div class="col-md-12 col-xs-12">
                            <div class="col-xs-4"><label>Quality</label></div>
                            <div class="col-xs-8 col-md-8">
                                <select class="form-control input-sm select2" name="quality" id="quality" >
                                    <option value="">-- Pilih Quality --</option>
                                    <?php 
                                        $selected = "";
                                        foreach ($quality as $row) {
                                            if($row->id == $inlet->id_quality) $selected = "selected" ?? '';
                                            echo "<option value='".$row->id."' ".$selected.">".$row->nama."</option>";
                                            $selected = "";
                                        }
                                    ?>
                                </select> 
                            </div>                                    
                        </div>
                        <div class="col-md-12 col-xs-12">
                            <div class="col-xs-4"><label>Desain Barcode</label></div>
                            <div class="col-xs-8 col-md-8">
                                <select class="form-control input-sm select2" name="desain_barcode" id="desain_barcode" >
                                    <option value="">-- Pilih Desain Barcode --</option>
                                    <?php 
                                        $selected = "";
                                        foreach ($desain_barcode as $row) {
                                            if($row->kode_desain == $inlet->desain_barcode) $selected = "selected" ?? '';
                                            echo "<option value='".$row->kode_desain."' ".$selected.">".$row->kode_desain."</option>";
                                            $selected = "";
                                        }
                                    ?>
                                </select>
                            </div>                                    
                        </div>
                        <div class="col-md-12 col-xs-12">
                            <div class="col-xs-4"><label>Kode K3L</label></div>
                            <div class="col-xs-8 col-md-8">
                                <select class="form-control input-sm select2" name="k3l" id="k3l" >
                                    <option value="">-- Pilih Kode K3L --</option>
                                    <?php 
                                        $selected = "";
                                        foreach ($kode_k3l as $row) {
                                            if($row->kode == $inlet->kode_k3l) $selected = "selected" ?? '';
                                            echo "<option value='".$row->kode."' ".$selected.">".$row->kode."</option>";
                                            $selected = "";
                                        }
                                    ?>
                                </select>
                            </div>                                    
                        </div>
                        <div class="col-md-12 col-xs-12">
                            <div class="col-xs-4"><label>No Mesin</label></div>
                            <div class="col-xs-8 col-md-8">
                                <select class="form-control input-sm select2" name="mesin" id="mesin" >
                                    <option value="">-- Pilih Mesin --</option>
                                    <?php 
                                        $selected = "";
                                        foreach ($mesin as $row) {
                                            if($row->mc_id == $inlet->mc_id) $selected = "selected" ?? '';
                                            echo "<option value='".$row->mc_id."' ".$selected.">".$row->nama_mesin."</option>";
                                            $selected = "";
                                        }
                                    ?>
                                </select>
                            </div>                                    
                        </div>
                        <div class="col-md-12 col-xs-12">
                            <div class="col-xs-4"><label>Operator</label></div>
                            <div class="col-xs-8 col-md-8">
                                <input type='text' class="form-control input-sm " name="operator" id="operator"  value="<?php echo $inlet->operator; ?>"/>
                            </div>                                    
                        </div>
                     
                    </div>

                    <div class="col-md-6">
                        <div class="col-xs-12 table-responsive example1 divListviewHead">
                            <div role="region" aria-labelledby="HeadersCol" tabindex="0" class="rowheaders">
                                <table id="table_hasil_hph" class="table table-condesed table-hover" border="0" style="margin-bottom:0px;">
                                    <thead>
                                        <th class="bb no">No.</th>
                                        <th class="bb" width="200px">Keterangan</th>
                                        <th class="bb" >HPH Mtr</th>
                                        <th class="bb" >HPH Kg</th>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td colspan="4">Tidak Ada Data</td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div id="example1_processing" class="example1_processing table_processing" style="display: none; z-index:5;">
                                    Processing...
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-xs-12 table-responsive example1 divListviewHead" style="margin-top:10px">
                            <div role="region" aria-labelledby="HeadersCol" tabindex="0" class="rowheaders">
                                <table id="table_hasil_hph_grade" class="table table-condesed table-hover" border="0" style="margin-bottom:0px;">
                                    <thead>
                                        <th class="bb no">No.</th>
                                        <th class="bb nowrap" width="100px">Grade</th>
                                        <th class="bb nowrap" width="100px">Total Qty[Mtr]</th>
                                        <th class="bb nowrap" width="100px">Total Qty2[Kg]</th>
                                        <th class="bb nowrap" width="100px">Total Pcs</th>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td colspan="5">Tidak Ada Data</td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div id="example1_processing" class="example1_processing table_processing" style="display: none; z-index:5;">
                                    Processing...
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
                        <ul class="nav nav-tabs " >
                            <li class="active"><a href="#tab_1" data-toggle="tab">Details HPH</a></li>
                        </ul>
                        <div class="tab-content over"><br>
                            <div class="tab-pane active" id="tab_1">
                                 <!-- Tabel  -->
                                <div class="col-md-12 table-responsive over">
                                    <table class="table table-condesed table-hover rlstable over" width="100%" id="table_hph" >
                                        <thead>                          
                                            <tr>
                                                <th class="style no">No.</th>
                                                <th class="style nowrap">Tgl.Buat</th>                            
                                                <th class="style nowrap">Kode Produk</th>
                                                <th class="style nowrap">Nama Produk</th>
                                                <th class="style nowrap">Corak Remark</th>
                                                <th class="style nowrap">Warna Remark</th>
                                                <th class="style nowrap">Lot</th>
                                                <th class="style nowrap">Grade</th>
                                                <th class="style nowrap">Qty</th>
                                                <th class="style nowrap">Qty2</th>
                                                <th class="style nowrap">Qty Jual</th>
                                                <th class="style nowrap">Qty2 Jual</th>
                                                <th class="style nowrap">Lbr.Jadi</th>
                                                <th class="style ">Lokasi Sekarang</th>
                                                <th class="style nowrap">User HPH</th>
                                                <th class="style nowrap">#</th>
                                            </tr>
                                        </thead>
                                    </table>
                                    <small><b>*Jika terdapat baris yang berwarna <font color="red">MERAH</font> maka Product/Lot tersebut telah di proses SPLIT !!</b></small>
                                    <br>
                                    &nbsp;
                                </div>
                                <!-- /Tabel  -->
                            </div>
                            <!-- /.tab-pane -->
                        </div>
                        <!-- /.tab-content -->
                    </div>
                    <!-- nav-tabs-custom -->
                </div>
                <!-- /.col -->
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

   <div id="foot">
     <?php 
        $data['kode'] =  $inlet->id;
        $data['mms']  =  $mms->kode;
        $this->load->view("admin/_partials/footer.php",$data) 
     ?>
    </div>
  </footer>

</div>

<div id="load_modal">
    <!-- Load Partial Modal -->
   <?php $this->load->view("admin/_partials/modal.php") ?>
</div>

<?php $this->load->view("admin/_partials/js.php") ?>


<script type="text/javascript">

    $(".modal").on('hidden.bs.modal', function(){
        refresh();
        get_hasil_hph();
    });

    function refresh(){
        table.ajax.reload( function(){});
        $("#foot").load(location.href + " #foot");
        $("#status_bar").load(location.href + " #status_bar>*");
    }

    $('.select2').select2({
        allowClear: true,
        placeholder: 'Pilih',
        width:'100%'

    });

    function validAngka(a){   
        if(!/^[0-9.]+$/.test(a.value)){
          //a.value = a.value.substring(0,a.value.length-1);
          a.value = a.value.replace(/[^0-9.]/, '')
          return true;
        }
    }

    var jenis_kain  = "<?php echo $inlet->id_jenis_kain?>";
    if(jenis_kain == 7 || jenis_kain == 8 || jenis_kain == 6 || jenis_kain == 5 || jenis_kain == 10){
        // $('#uom_lebar_jadi').attr( "disabled", true ).attr('name', 'uom_lebar_jadi');
        $('#tampil_gramasi').show();
        $('#tampil_berat').hide();
    }else{
        $('#uom_lebar_jadi').attr( "disabled", false ).attr('name', 'uom_lebar_jadi');
        $('#tampil_berat').show();
        $('#tampil_gramasi').hide();
    }
    
    //event enter di input operator
    document.getElementById('operator').onkeypress = function(event) {
      if (event.which == 13) {
        simpan_inlet();
      }
    }

    // btn simpan
    $(document).on("click", "#btn-simpan", function(e){
        e.preventDefault();
        simpan_inlet();
    });
    
    // simpan inlet
    function simpan_inlet(){
        
        let id          = $('#id').val();
        let lot         = $('#lot').val();
        let kode_mrp    = $('#kode_mrp').val();
        let marketing   = $('#marketing').val();
        let kode_produk = $('#kode_produk').val();
        let nama_produk = $('#nama_produk').val();
        let corak_remark= $('#corak_remark').val();
        let warna_remark= $('#warna_remark').val();
        let lebar_jadi  = $('#lebar_jadi').val();
        let uom_lebar_jadi   = $('#uom_lebar_jadi').val();    
        let jenis_kain  = $('#jenis_kain').val();
        let gramasi     = $('#gramasi').val();
        let berat       = $('#berat').val();
        let benang      = $('#benang').val();
        let quality     = $('#quality').val();
        let desain_barcode     = $('#desain_barcode').val();
        let k3l         = $('#k3l').val();
        let mesin       = $('#mesin').val();
        let operator    = $('#operator').val();
        let quant_id    = $('#quant_id').val();
        let status      = "<?php echo $inlet->status; ?>";

        if(status == 'done'){
            alert_modal_warning("Data Inlet tidak bisa dirubah, Data Inlet sudah <b> Done </b> !");
        }else if(status == 'cancel'){
            alert_modal_warning("Data Inlet tidak bisa dirubah, Data Inlet sudah <b> Cancel </b> !");
        }else if(lot == ''){
            alert_notify('fa fa-warning','KP / Lot tidak boleh kosong !','danger',function(){});
        }else if(kode_produk == ''){
            alert_notify('fa fa-warning','Kode Produk tidak boleh kosong !','danger',function(){});
        }else if(nama_produk == ''){
            alert_notify('fa fa-warning','Nama Produk tidak boleh kosong !','danger',function(){});
        }else if(corak_remark == ''){
            alert_notify('fa fa-warning','Corak Remark tidak boleh kosong !','danger',function(){});
        }else if(warna_remark == ''){
            alert_notify('fa fa-warning','Warna Remark tidak boleh kosong !','danger',function(){});
        }else{
            $('#btn-simpan').button('loading');
            please_wait(function(){});
            $.ajax({
                type     : "POST",
                dataType : "json",
                url :'<?php echo base_url('manufacturing/inlet/save_inlet')?>',
                beforeSend: function(e) {
                    if(e && e.overrideMimeType) {
                        e.overrideMimeType("application/json;charset=UTF-8");
                    }                               
                },
                data: {id:id,lot:lot,kode_mrp:kode_mrp, marketing:marketing, kode_produk:kode_produk, nama_produk:nama_produk, corak_remark:corak_remark, warna_remark:warna_remark, lebar_jadi:lebar_jadi, uom_lebar_jadi:uom_lebar_jadi, jenis_kain:jenis_kain, gramasi:gramasi, berat:berat, benang:benang, quality:quality, desain_barcode:desain_barcode, k3l:k3l, mesin:mesin, operator:operator, quant_id:quant_id},
                success: function(data){
                    if(data.sesi == "habis"){
                        //alert jika session habis
                        alert_modal_warning(data.message);
                        window.location.replace('index');
                    }else if(data.status == 'failed'){
                        unblockUI( function() {
                            setTimeout(function() { alert_notify(data.icon,data.message,data.type,function(){}); }, 1000);
                        });
                        $('#'+data.field).focus();
                    }else{
                        unblockUI( function() {
                            setTimeout(function() { 
                            alert_notify(data.icon,data.message,data.type, function(){},1000); 
                            });
                        });
                    }
                    $('#btn-simpan').button('reset');
                    $("#foot").load(location.href + " #foot");
                    $("#status_bar").load(location.href + " #status_bar");
                }
                ,error: function (xhr, ajaxOptions, thrownError) {
                    // alert(xhr.response.essage);
                    unblockUI( function(){});
                    $('#btn-simpan').button('reset');
                }
            });
        }
    }


    var table;
    $(document).ready(function() {
        //datatables
        table = $('#table_hph').DataTable({ 
            "processing": true, 
            "serverSide": true, 
            "order": [], 

            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
             
            "ajax": {
                "url": "<?php echo site_url('manufacturing/inlet/get_data_hph_inlet')?>",
                "type": "POST",
                "data": {"kode": "<?php echo $inlet->id;?>"}
            },
           
            "columnDefs": [
                {
                    "targets":[0],
                    "className":"max-width-5"
                },
                { 
                    "targets": [0], 
                    "orderable": false, 
                },
                { 
                    "targets": [8,9,10,11], 
                    "className":"text-right nowrap",
                },
                {
                    "targets" : 15,
                    'checkboxes': {
                        'selectRow': true
                    },
                },
            ],
            "createdRow": function( row, data, dataIndex){
              if( data[16].includes('SPL') == true ){
                $(row).addClass('text-red');
              }
            }  ,
            "select": {
                'style': 'multi'
            },
             
        });
 
    });

    $(document).on("click", ".edit_lot", function(e) {
        let quant_id = $(this).attr('data-quant');
        edit_lot(quant_id);
    });

    function edit_lot(quant_id){
        let id   =  "<?php echo $inlet->id; ?>";
        $('#btn-tambah').button('reset');
        $("#tambah_data").modal({
                show: true,
                backdrop: 'static'
        })
        $("#tambah_data").removeClass('modal fade lebar').addClass('modal fade lebar_mode');
        $("#tambah_data .modal-dialog .modal-content .modal-body").addClass('lot_hph');
        $("#tambah_data .modal-dialog .modal-content .modal-footer #btn-tambah").attr('disabled',true);

        $("#tambah_data .modal-dialog .modal-content .modal-footer #btn-print").remove();

        $(".tambah_data").html('<center><h5><img src="<?php echo base_url('dist/img/ajax-loader.gif') ?> "/><br>Please Wait...</h5></center>');
        $('.modal-title').text('Edit Data HPH Lot');
        $.post('<?php echo site_url()?>manufacturing/inlet/edit_lot_modal',
            {id:id,quant_id:quant_id},
        ).done(function(html){
            setTimeout(function() {
                $(".lot_hph").html(html)  
            },1000);
            $("#tambah_data .modal-dialog .modal-content .modal-footer").prepend('<button class="btn btn-default btn-sm" id="btn-print" name="btn-print" >Print</button>');
            $("#tambah_data .modal-dialog .modal-content .modal-footer #btn-tambah").attr('disabled',false);
        }).fail(function(response) {
            var err = JSON.parse(response.responseText);
            if(response.status == 401){
                alert(err.message);
            }else{
                alert(err.message)
                $(".lot_hph").html(err.message);
            }   
        });
    }

    get_hasil_hph();

    function get_hasil_hph(){

        let id   =  "<?php echo $inlet->id; ?>";
        $.ajax({
            type     : "POST",
            dataType : "json",
            url :'<?php echo base_url('manufacturing/inlet/get_data_total_hasil_hph')?>',
            beforeSend: function(e) {
                if(e && e.overrideMimeType) {
                    e.overrideMimeType("application/json;charset=UTF-8");
                }                               
                $("#table_hasil_hph tbody").remove();
                // $("#table_hasil_hph_grade tbody").remove();
                $(".example1_processing").css('display','');// show loading processing in table
            },
            data: {id:id},
            success: function(data){
                    var tbody = $("<tbody />");
                    var no    = 1;
                    var qty_target = 0;
                    var qty2_target = 0;
                    var color = '';
                    var color2 = '';
                    $.each(data.hasil_hph, function(key, value) {

                        if(value.ket == 'Qty Target' || value.ket == 'Belum diproses'){
                            qty_target = qty_target + parseFloat(value.qty);
                            qty2_target = qty2_target + parseFloat(value.qty2);
                        }
                        if(value.ket == 'Sudah diproses'){
                            if(value.qty > qty_target){
                                color = "text-red";
                            }
                            if(value.qty2 > qty2_target){
                                color2 = "text-red";
                            }
                        }
                            
                        var tr  = "<tr>"
                                + "<td class='cursor-pointer'>"+no++ +".</td>"
                                + "<td class='cursor-pointer'>"+value.ket+"</td>"
                                + "<td class='cursor-pointer text-right "+color+" '>"+value.qty+"</td>"
                                + "<td class='cursor-pointer text-right "+color2+" '>"+value.qty2+"</td>"
                                + "</tr>";
                                tbody.append(tr);
                    });

                    if(data.hasil_hph == 0){
                        tr = "<tr><td colspan='4'>Tidak Ada Data</td></tr>"
                        tbody.append(tr);
                    }
                    $("#table_hasil_hph tbody").empty();
                    // $("#table_hasil_hph_grade tbody").empty();
                    $("#table_hasil_hph").append(tbody);

                    var tbody = $("<tbody />");
                    var no    = 1;
                    $.each(data.hasil_hph_grade, function(key, value) {
                        var tr  = "<tr>"
                                + "<td class='cursor-pointer'>"+no++ +".</td>"
                                + "<td class='cursor-pointer'>"+value.nama_grade+"</td>"
                                + "<td class='cursor-pointer text-right'>"+value.total_qty+"</td>"
                                + "<td class='cursor-pointer text-right'>"+value.total_qty2+"</td>"
                                + "<td class='cursor-pointer text-right'>"+value.total_pcs+"</td>"
                                + "</tr>";
                                tbody.append(tr);
                    });

                    if(data.hasil_hph_grade == 0){
                        tr = "<tr><td colspan='5'>Tidak Ada Data</td></tr>"
                        tbody.append(tr);
                    }
                    $("#table_hasil_hph_grade tbody").empty();
                    $("#table_hasil_hph_grade").append(tbody);

                    $(".example1_processing").css('display','none');// hidden loading processing in table
            },error: function (xhr, ajaxOptions, thrownError) {
                $(".example1_processing").css('display','none');// hidden loading processing in table
                if(xhr.status == 401){
                    var err = JSON.parse(xhr.responseText);
                    alert(err.message);
                }else{
                    alert("Error Load Data!")
                }  
            }
        });

    }


    $(document).on('click','#btn-print',function(e){
            e.preventDefault();

            var myCheckboxes = table.column(15).checkboxes.selected();
            var myCheckboxes_arr = new Array();

            $.each(myCheckboxes, function(index, rowId){        
                myCheckboxes_arr.push(rowId);
            });

            if (myCheckboxes.length === 0) {
                alert_notify('fa fa-warning', 'Pilih LOT terlebih dahulu yang akan di print !', 'danger', function() {});
            }else{
                $(".print_data").html('<center><h5><img src="<?php echo base_url('dist/img/ajax-loader.gif') ?> "/><br>Please Wait...</h5></center>');
                $("#print_data").modal({
                    show: true,
                    backdrop: 'static'
                });
                $("#print_data .modal-dialog .modal-content .modal-footer #btn-print-modal").remove();

                $('.modal-title').text('Pilih Desain Barcode  ');
                $.post('<?php echo site_url()?>manufacturing/inlet/print_modal',
                {  id :  "<?php echo $inlet->id; ?>", data:myCheckboxes_arr},
                    function(html){
                        setTimeout(function() {$(".print_data").html(html);  },1000);
                        $("#print_data .modal-dialog .modal-content .modal-footer").prepend('<button class="btn btn-default btn-sm" id="btn-print-modal" name="btn-print-modal" >Print</button>');

                    }   
                );
            }
        });
   
</script>


</body>
</html>
