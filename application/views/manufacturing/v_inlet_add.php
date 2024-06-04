<!DOCTYPE html>
<html>
<head>
  <?php $this->load->view("admin/_partials/head.php") ?>
  <style type="text/css">
        @media (min-width: 300px) {
            .btn-style-proc {
            padding-left: 30px !important;
            }
        }

        select[readonly].select2-hidden-accessible + .select2-container {
            pointer-events: none;
            touch-action: none;
        }

        select[readonly].select2-hidden-accessible + .select2-container .select2-selection {
            background: #eee;
            box-shadow: none;
        }

        select[readonly].select2-hidden-accessible + .select2-container .select2-selection__arrow,
        select[readonly].select2-hidden-accessible + .select2-container .select2-selection__clear {
            display: none;
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
          <h3 class="box-title">Form Add Inlet </h3>
        </div>

        <div class="box-body">
            <form class="form-horizontal">
      
                <div class="form-group"> 
                    <div class="col-md-6">
                        <div class="col-md-10 col-xs-12">
                            <div class="col-xs-12">
                            <input type="text" class="form-control input-lg" name="txtlot" id="txtlot" placeholder="Scan KP / Lot" />
                            </div>                                    
                        </div>
                        <div class="col-xs-4 col-md-1 btn-style-proc" >
                            <button type="button" id="btn-proses" name="submit" class="btn btn-primary btn-lg" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing..."><i class="fa fa-barcode"></i> Proses</button>
                        </div>
                    </div>
                   
                </div>
             
                <div class="form-group">

                    <div class="col-md-6">
                        <div class="col-md-12 col-xs-12">
                            <div class="col-xs-4"><label>Tanggal Inlet</label></div>
                            <div class="col-xs-8 col-md-8">
                                <input type='text' class="form-control input-sm " name="tgl_buat" id="tgl_buat" readonly="readonly" value="<?php echo date('Y-m-d H:i:s')?>"  />
                            </div>                                    
                        </div>
                        <div class="col-md-12 col-xs-12">
                            <div class="col-xs-4"><label>KP / Lot  </label></div>
                            <div class="col-xs-8">
                                <input type="hidden" class="form-control input-sm" name="quant_id" id="quant_id"  readonly="readonly" />                    
                                <input type="text" class="form-control input-sm" name="lot" id="lot"  readonly="readonly" />                    
                            </div>                                    
                        </div>
                        <div class="col-md-12 col-xs-12">
                            <div class="col-xs-4"><label>Marketing</label></div>
                            <div class="col-xs-8 col-md-8">
                                <select class="form-control input-sm select2" name="marketing" id="marketing" >
                                  <option value=""></option>
                                    <?php foreach ($sales_group as $row) {
                                            echo "<option value='".$row->kode_sales_group."'>".$row->nama_sales_group."</option>";
                                          }
                                    ?>
                                </select>
                            </div>                                    
                        </div>
                        <div class="col-md-12 col-xs-12">
                            <div class="col-xs-4"><label>MG GJD</label></div>
                            <div class="col-xs-8 col-md-8">
                                <input type='text' class="form-control input-sm " name="kode_mrp" id="kode_mrp" readonly="readonly" />
                            </div>                                    
                        </div>
                        <div class="col-md-12 col-xs-12">
                            <div class="col-xs-4"><label>Kode Produk</label></div>
                            <div class="col-xs-8 col-md-8">
                                <input type='text' class="form-control input-sm " name="kode_produk" id="kode_produk" readonly="readonly" />
                            </div>                                    
                        </div>
                        <div class="col-md-12 col-xs-12">
                            <div class="col-xs-4"><label>Nama Produk / Corak</label></div>
                            <div class="col-xs-8 col-md-8">
                                <input type='text' class="form-control input-sm " name="nama_produk" id="nama_produk" readonly="readonly" />
                            </div>                                    
                        </div>
                        <div class="col-md-12 col-xs-12">
                            <div class="col-xs-4"><label>warna</label></div>
                            <div class="col-xs-8 col-md-8">
                                <input type='text' class="form-control input-sm " name="warna" id="warna" readonly="readonly" />
                            </div>                                    
                        </div>
                        <div class="col-md-12 col-xs-12">
                            <div class="col-xs-4"><label>Corak Remark</label></div>
                            <div class="col-xs-8 col-md-8">
                                <input type='text' class="form-control input-sm " name="corak_remark" id="corak_remark"  />
                            </div>                                    
                        </div>
                        <div class="col-md-12 col-xs-12">
                            <div class="col-xs-4"><label>Warna Remark</label></div>
                            <div class="col-xs-8 col-md-8">
                                <input type='text' class="form-control input-sm " name="warna_remark" id="warna_remark"  />
                            </div>                                    
                        </div>
                        <div class="col-md-12 col-xs-12">
                            <div class="col-xs-4"><label>Jenis Kain</label></div>
                            <div class="col-xs-8 col-md-8">
                                <select class="form-control input-sm select2" name="jenis_kain" id="jenis_kain" >
                                  <option value=""></option>
                                  <?php foreach ($jenis_kain as $row) {?>
                                    <option value='<?php echo $row->id; ?>'><?php echo $row->nama_jenis_kain;?></option>
                                  <?php  }?>
                                </select>    
                            </div>                                    
                        </div>
                        <div class="col-md-12 col-xs-12" id="tampil_gramasi" style="display: inline;">
                            <div class="col-xs-4"><label>Gramasi</label></div>
                            <div class="col-xs-8 col-md-8">
                                <input type='text' class="form-control input-sm " name="gramasi" id="gramasi" onkeyup="validAngka(this)" />
                            </div>                                    
                        </div>
                        <div class="col-md-12 col-xs-12" id="tampil_berat" style="display: inline;">
                            <div class="col-xs-4"><label>Berat/Mtr/panel (kg)</label></div>
                            <div class="col-xs-8 col-md-8">
                                <input type='text' class="form-control input-sm " name="berat" id="berat" onkeyup="validAngka(this)"/>
                            </div>                                    
                        </div>
                        <div class="col-md-12 col-xs-12">
                            <div class="col-xs-4"><label>Lebar Jadi / Pcs</label></div>
                            <div class="col-xs-8 col-md-8">
                                <input type='text' class="form-control input-sm " name="lebar_jadi" id="lebar_jadi"  />
                            </div>                                    
                        </div>
                        <div class="col-md-12 col-xs-12">
                            <div class="col-xs-4"><label>Uom Lebar Jadi</label></div>
                            <div class="col-xs-8 col-md-8">
                                <select class="form-control input-sm select2" name="uom_lebar_jadi" id="uom_lebar_jadi" >
                                  <option value=""></option>
                                    <?php foreach ($uom as $row) {
                                            echo "<option value='".$row->short."'>".$row->short."</option>";
                                          }
                                    ?>
                                </select>
                            </div>                                    
                        </div>                        
                        <div class="col-md-12 col-xs-12">
                            <div class="col-xs-4"><label>Benang</label></div>
                            <div class="col-xs-8 col-md-8">
                                <input type='text' class="form-control input-sm " name="benang" id="benang"  autocomplete="on" />
                            </div>                                    
                        </div>
                        <div class="col-md-12 col-xs-12">
                            <div class="col-xs-4"><label>Quality</label></div>
                            <div class="col-xs-8 col-md-8">
                                <select class="form-control input-sm select2" name="quality" id="quality" >
                                  <option value=""></option>
                                  <?php foreach ($quality as $row) {?>
                                    <option value='<?php echo $row->id; ?>'><?php echo $row->nama;?></option>
                                  <?php  }?>
                                </select>   
                            </div>                                    
                        </div>
                        <div class="col-md-12 col-xs-12">
                            <div class="col-xs-4"><label>Desain Barcode</label></div>
                            <div class="col-xs-8 col-md-8">
                                <select class="form-control input-sm select2" name="desain_barcode" id="desain_barcode" >
                                  <option value=""></option>
                                  <?php foreach ($desain_barcode as $row) {?>
                                    <option value='<?php echo $row->kode_desain; ?>'><?php echo $row->kode_desain;?></option>
                                  <?php  }?>
                                </select> 
                            </div>                                    
                        </div>
                        <div class="col-md-12 col-xs-12">
                            <div class="col-xs-4"><label>Kode K3L</label></div>
                            <div class="col-xs-8 col-md-8">
                                <select class="form-control input-sm select2" name="k3l" id="k3l" >
                                  <option value=""></option>
                                  <?php foreach ($kode_k3l as $row) {?>
                                    <option value='<?php echo $row->kode; ?>'><?php echo $row->kode;?></option>
                                  <?php  }?>
                                </select> 
                            </div>                                    
                        </div>
                        <div class="col-md-12 col-xs-12">
                            <div class="col-xs-4"><label>No Mesin</label></div>
                            <div class="col-xs-8 col-md-8">
                                <select class="form-control input-sm select2" name="mesin" id="mesin" >
                                    <option value=""></option>
                                    <?php 
                                    foreach ($mesin as $val) {
                                        echo "<option value='".$val->mc_id."'>".$val->nama_mesin."</option>";
                                    }
                                    ?>
                                </select>  
                            </div>                                    
                        </div>
                        <div class="col-md-12 col-xs-12">
                            <div class="col-xs-4"><label>Operator</label></div>
                            <div class="col-xs-8 col-md-8">
                                <input type='text' class="form-control input-sm " name="operator" id="operator" autocomplete="on" />
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


<script type="text/javascript">

    // // untuk focus after select2 close
    $(document).on('focus', '.select2', function (e) {
        if (e.originalEvent) {
            var s2element = $(this).siblings('select');
            s2element.select2('open');

            // Set focus back to select2 element on closing.
            s2element.on('select2:closing', function (e) {
                s2element.select2('focus');
            });
        }
    });

    $(document).on('select2:opening', '.select2', function (e) {
        if( $(this).attr('readonly') == 'readonly') {
            //   console.log( 'can not open : readonly' );
            e.preventDefault();
            $(this).select2('close');
            return false;
        }else{
            //   console.log( 'can be open : free' );
        }
    });

    $('.select2').select2({
        allowClear: true,
        placeholder: 'Pilih',
        width: '100%'
    });

    $(document).on("click", "#btn-proses", function(){
        proses();
    });

    $('#txtlot').keydown(function(event){
        if(event.keyCode == 13) {
           event.preventDefault();
            proses();
        }
    });
       
    function validAngka(a){   
        if(!/^[0-9.]+$/.test(a.value)){
          //a.value = a.value.substring(0,a.value.length-1);
          a.value = a.value.replace(/[^0-9.]/, '')
          return true;
        }
    }

    function clear_value(){
        // kosongkan informasi
        $('#lot').val('');

        $('#quant_id').val('');
        $('#kode_mrp').val('');
        // $('#marketing').val('');
        $('#marketing').val('').trigger('change');
        $('#kode_produk').val('');
        $('#nama_produk').val('');
        $('#warna').val('');

        $('#corak_remark').val('');
        $('#warna_remark').val('');
        $('#lebar_jadi').val('');
        $('#uom_lebar_jadi').val('').trigger('change');
        
        
        $('#jenis_kain').val('').trigger('change');
        $('#gramasi').val('');
        $('#berat').val('');
        $('#benang').val('');
        $('#quality').val('').trigger('change');
        $('#desain_barcode').val('').trigger('change');
        $('#k3l').val('').trigger('change');
        $('#mesin').val('').trigger('change');
        $('#operator').val('');
        return;
    }

    function proses(){
        let txtlot = $("#txtlot").val();   
        clear_value();
        if(txtlot == ''){
            alert_notify('fa fa-warning','KP / Lot tidak boleh kosong !','danger',function(){});
            $('#txtlot').focus();
        }else{
            $('#btn-proses').button('loading');
           
            $.ajax({
                type     : "POST",
                dataType : "json",
                url :'<?php echo base_url('manufacturing/inlet/search_lot')?>',
                beforeSend: function(e) {
                    if(e && e.overrideMimeType) {
                        e.overrideMimeType("application/json;charset=UTF-8");
                    }                               
                },
                data: {txtlot : txtlot},
                success: function(data){
                    if(data.sesi == "habis"){
                        //alert jika session habis
                        alert_modal_warning(data.message);
                        window.location.replace('index');
                    }else{
                        empty = true;
                        $.each(data.record, function(key, value) {
                            empty  = false;
                            $('#quant_id').val(value.quant_id);
                            $('#lot').val(value.lot);
                            $('#kode_mrp').val(value.kode_mrp);

                            // $('#marketing').val(value.marketing);
                            $('#marketing').val(value.kd_marketing).trigger('change');
                            // $('#marketing').attr( "readonly", true ).attr('name', 'marketing');

                            $('#kode_produk').val(value.kode_produk);
                            $('#nama_produk').val(value.nama_produk);
                            $('#warna').val(value.warna);
                             // $('#jenis_kain').val(value.id_jenis_kain);
                             $('#jenis_kain').val(value.id_jenis_kain).trigger('change');
                            $('#jenis_kain').attr( "readonly", true ).attr('name', 'jenis_kain');

                            $('#lebar_jadi').val(value.lebar_jadi);
                            // $('#uom_lebar_jadi').val(value.uom_lebar_jadi);
                            $('#uom_lebar_jadi').val(value.uom_lebar_jadi).trigger('change');

                            $('#berat').val(value.berat);
                            $('#gramasi').val(value.gramasi);
                           
                        });
                    
                        setTimeout(function() { alert_notify(data.icon,data.message,data.type,function(){});},1000); 
                        $('#txtlot').val('');
                        $('#txtlot').focus();
                        checkTampil();
                        // $('#jenis_kain').attr( "disabled", true ).attr('id', 'jenis_kain');

                    }
                    $('#btn-proses').button('reset');
                },error: function (xhr, ajaxOptions, thrownError) {
                    // alert(xhr.responseText);
                    $('#btn-proses').button('reset');
                }
            });

        }
    }
    
    $('#jenis_kain').change(checkTampil);
    function checkTampil() {
        var jenis_kain = $('#jenis_kain').val();
        // Tricot,Spandex.Tulle,Vitrage,Double Needle
        if(jenis_kain == 7 || jenis_kain == 8 || jenis_kain == 6 || jenis_kain == 5 || jenis_kain == 10){
            $('#uom_lebar_jadi').val('Inch');
            // $('#uom_lebar_jadi').attr( "readonly", true ).attr('name', 'uom_lebar_jadi');
            $('#tampil_gramasi').show();
            $('#berat').val('');
            $('#tampil_berat').hide();

        }else if(jenis_kain == 4 || jenis_kain == 3 || jenis_kain == 2 || jenis_kain == 1){// Brukad,Jacquard,Kerudung, Renda
            $('#uom_lebar_jadi').attr( "readonly", false ).attr('name', 'uom_lebar_jadi');
            $('#tampil_berat').show();
            $('#gramasi').val('');
            $('#tampil_gramasi').hide();
        }else{
            $("#uom_lebar_jadi").attr("readonly", false).attr('name', 'uom_lebar_jadi');
            $("#tampil_berat").show();
            $("#tampil_gramasi").show();
            $('#uom_lebar_jadi').val('');
        }
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
   
        let quant_id    = $('#quant_id').val();
        let lot         = $('#lot').val();
        let kode_mrp    = $('#kode_mrp').val();
        let marketing      = $('#marketing').val();
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
    
        if(lot == ''){
            alert_notify('fa fa-warning','KP / Lot tidak boleh kosong !','danger',function(){});
        }else if(kode_produk == ''){
            alert_notify('fa fa-warning','Kode Produk tidak boleh kosong !','danger',function(){});
        }else if(nama_produk == ''){
            alert_notify('fa fa-warning','Nama Produk tidak boleh kosong !','danger',function(){});
        }else if(corak_remark == ''){
            alert_notify('fa fa-warning','Corak Remark tidak boleh kosong !','danger',function(){});
            $('#corak_remark').focus();
        }else if(warna_remark == ''){
            alert_notify('fa fa-warning','Warna Remark tidak boleh kosong !','danger',function(){});
            $('#warna_remark').focus();
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
                data: {quant_id:quant_id,lot:lot,kode_mrp:kode_mrp, marketing:marketing, kode_produk:kode_produk, nama_produk:nama_produk, corak_remark:corak_remark, warna_remark:warna_remark, lebar_jadi:lebar_jadi, uom_lebar_jadi:uom_lebar_jadi, jenis_kain:jenis_kain, gramasi:gramasi, berat:berat, benang:benang, quality:quality, desain_barcode:desain_barcode, k3l:k3l, mesin:mesin, operator},
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
                        $('#txtlot').val('');
                        $('#txtlot').focus();
                        clear_value();
                    }
                    $('#btn-simpan').button('reset');
                },error: function (xhr, ajaxOptions, thrownError) {
                    alert(xhr.responseText);
                    $('#btn-simpan').button('reset');
                    unblockUI( function(){});
                }
            });
        }
    }

   
</script>


</body>
</html>
