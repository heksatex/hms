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
        .select2-container--focus{
		    border:  1px solid #66afe9;
        }
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
                                <select class="form-control input-sm select2" name="uom_lebar_jadi" id="uom_lebar_jadi" disabled >
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
     <?php 
        $data['kode'] =  $inlet->id;
        $data['mms']  =  $mms->kode;
        $this->load->view("admin/_partials/footer.php",$data) 
     ?>
    </div>
  </footer>

</div>

<?php $this->load->view("admin/_partials/js.php") ?>


<script type="text/javascript">

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
        $('#uom_lebar_jadi').attr( "disabled", true ).attr('name', 'uom_lebar_jadi');
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


   
</script>


</body>
</html>
