<!DOCTYPE html>
<html>
<head>
  <?php $this->load->view("admin/_partials/head.php") ?>
</head>

<body class="hold-transition skin-black fixed sidebar-mini" onload="get_default()"> 
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
          <h3 class="box-title">Form Opname Kg </h3>
        </div>

        <div class="box-body">
          <form class="form-horizontal">
            <div class="form-group">                  
              <div class="col-md-12" >
                <div id="alert"></div>
              </div>
            </div>
            <div class="form-group">

              <div class="col-md-6">
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Kode  </label></div>
                  <div class="col-xs-8">
                    <input type="text" class="form-control input-sm" name="kode" id="kode"  readonly="readonly" value="<?php echo $kode_opname;?>"/>                    
                  </div>                                    
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Tanggal dibuat</label></div>
                  <div class="col-xs-8 col-md-8">
                     <input type='text' class="form-control input-sm " name="tgl_buat" id="tgl_buat" readonly="readonly" value="<?php echo date('Y-m-d H:i:s')?>"  />
                  </div>                                    
                </div>

                <div class="col-md-12 col-xs-12" style="margin-bottom:10px">
                  <div class="col-xs-4"><label>Departemen</label></div>
                  <div class="col-xs-8">
                    <select class="form-control input-sm" name="departemen" id="departemen">
                    <option value="">Pilih Departemen</option>
                      <?php foreach ($warehouse as $row) { ?>
                        <option value='<?php echo $row->kode; ?>'><?php echo $row->nama;?></option>
                      <?php }?>
                    </select>
                  </div>                                    
                </div>
              </div>

              <div class="col-md-6" >

              <span id="show_scan" style="display: none;">
             
                <div class="col-md-12 col-xs-12" style="padding-bottom:10px;">
                    <div class="col-xs-12 col-md-8 col-sm-8">
                        <input type="text" class="form-control input-lg" name="txtlot" id="txtlot"  placeholder="Scan Barcode / Lot" style="text-transform:uppercase">
                    </div>                                    
                    <div class="col-xs-12 col-md-4 col-sm-4">
                        <button  type="button" class="btn btn-lg  btn-primary" name="btn-proses" id="btn-proses" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing..."><i class="fa fa-barcode"></i> Proses </button>
                    </div>
                </div> 

                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Quant Id  </label></div>
                  <div class="col-xs-8">
                    <input type="text" class="form-control input-sm" name="quant_id" id="quant_id" readonly >                    
                  </div>                                    
                </div>

                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Kode Produk  </label></div>
                  <div class="col-xs-8">
                    <input type="text" class="form-control input-sm" name="kode_produk" id="kode_produk" readonly >                    
                  </div>                                    
                </div>

                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Nama Produk  </label></div>
                  <div class="col-xs-8">
                    <input type="text" class="form-control input-sm" name="nama_produk" id="nama_produk" readonly>                    
                  </div>                                    
                </div>

                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Barcode/Lot  </label></div>
                  <div class="col-xs-8">
                    <input type="text" class="form-control input-sm" name="lot" id="lot" readonly >                    
                  </div>                                    
                </div>

                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Qty1  </label></div>
                  <div class="col-xs-5">
                    <input type="text" class="form-control input-sm" name="qty" id="qty" readonly>                    
                  </div> 
                  <div class="col-xs-3">
                    <input type="text" class="form-control input-sm" name="uom_qty" id="uom_qty"readonly >                    
                  </div>                                    
                </div>

                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Qty2  </label></div>
                  <div class="col-xs-5">
                    <input type="text" class="form-control input-sm" name="qty2" id="qty2" readonly>                    
                  </div> 
                  <div class="col-xs-3">
                    <input type="text" class="form-control input-sm" name="uom_qty2" id="uom_qty2" readonly>                    
                  </div>                                    
                </div>

                <div class="col-md-12 col-xs-12" style="padding-bottom:10px;">
                  <div class="col-xs-4"><label>Lokasi Fisik  </label></div>
                  <div class="col-xs-8">
                    <input type="text" class="form-control input-sm" name="lokasi_fisik" id="lokasi_fisik" readonly>                    
                  </div>                                    
                </div>
                <br>

                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Qty Opname  </label></div>
                  <div class="col-xs-5">
                    <input type="text" class="form-control input-sm" name="qty_opname" id="qty_opname" onkeyup="validAngka(this)"  style="text-align:right" >                    
                  </div>
                  <div class="col-xs-3">
                    <select class="form-control input-sm" name="uom_opname" id="uom_opname">
                        <option value="">Pilih UoM</option>
                        <?php foreach ($uom as $row) { 
                            if($row->short == 'Kg'){
                                echo "<option value='".$row->short."' selected>".$row->short."</option>";
                            }else{
                                echo "<option value='".$row->short."'>".$row->short."</option>";
                            }
                         }?>
                    </select>                  
                  </div>                                    
                </div>
                         
              </span>

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
   <?php $this->load->view("admin/_partials/footer.php") ?>
  </footer>

</div>

<?php $this->load->view("admin/_partials/js.php") ?>

<script type="text/javascript">

    function get_default(){
        $("#departemen").prop('selectedIndex',0)
    }
    
    function validAngka(a){
   
        if(!/^[0-9.]+$/.test(a.value)){
          //a.value = a.value.substring(0,a.value.length-1);
          a.value = a.value.replace(/[^0-9.]/, '')
          return true;
        }
    }


    $(document).on("change", "#departemen", function(){
        checkTampil();
    });


    function checkTampil(){
        if($('#departemen').val() != ''){
            $("#show_scan").show();

            $('#txtlot').val('');
            $('#quant_id').val('');
            $('#lot').val('');
            $('#kode_produk').val('');
            $('#nama_produk').val('');
            $('#qty').val('');
            $('#uom_qty').val('');
            $('#qty2').val('');
            $('#uom_qty2').val('');
            $('#lokasi_fisik').val('');
            $('#qty_opname').val('');

        }else{
            $("#show_scan").hide();
        }
    }

    function alert_scan(message,field){
      var dialog = bootbox.dialog({
          message: message,
          closeButton: false,
          title: "<font color='red'><i class='glyphicon glyphicon-alert'></i></font> Warning !",
          buttons: {
              confirm: {
                  label: 'Ok',
                  className: 'btn-primary btn-sm',
                  callback : function() {
                    $('.bootbox').modal('hide');
                    $('#'+field).focus();
                  }
              },
          },
      });
      dialog.init(function(){
        dialog.find([type='button']).focus();
      });
    }

    $(document).on("click", "#btn-proses", function(){
        proses();
    });


    $('#txtlot').keydown(function(event){
        if(event.keyCode == 13) {
           event.preventDefault();
            proses();
          //return false;
        }
    });

    
    function proses(){
        
        let txtlot      = $("#txtlot").val();  
        let departemen  = $('#departemen').val();

        // kosongkan informasi
        $('#quant_id').val('');
        $('#lot').val('');
        $('#kode_produk').val('');
        $('#nama_produk').val('');
        $('#qty').val('');
        $('#uom_qty').val('');
        $('#qty2').val('');
        $('#uom_qty2').val('');
        $('#lokasi_fisik').val('');
        $('#qty_opname').val('');
        
        if(txtlot == ''){
          alert_scan('Barcode / Lot tidak boleh kosong !','txtlot');
        }else if(departemen == ''){
          alert_scan('Departemen tidak boleh kosong !','departemen');
        }else{
            $('#btn-proses').button('loading');

            $.ajax({
            type     : "POST",
            dataType : "json",
            url :'<?php echo base_url('warehouse/opnamekg/search_barcode')?>',
            data: {txtlot:txtlot, departemen:departemen},
            success: function(data){
                if(data.sesi == "habis"){
                    //alert jika session habis
                    alert_modal_warning(data.message);
                    window.location.replace('index');
                }else if(data.status == "failed"){
                    alert_notify(data.icon,data.message,data.type,function(){});
                    document.getElementById(data.field).focus();             
                }else{

                    alert_notify(data.icon,data.message,data.type,function(){});
                        
                    quant_id    = $('#quant_id').val(data.result['quant_id']);
                    kode_produk = $('#kode_produk').val(data.result['kode_produk']);
                    nama_produk = $('#nama_produk').val(data.result['nama_produk']);
                    lot         = $('#lot').val(data.result['lot']);
                    qty         = $('#qty').val(data.result['qty']);
                    uom_qty     = $('#uom_qty').val(data.result['uom']);
                    qty2        = $('#qty2').val(data.result['qty2']);
                    uom_qty2    = $('#uom_qty2').val(data.result['uom2']);
                    lokasi_fisik= $('#lokasi_fisik').val(data.result['lokasi_fisik']);

                    $('#txtlot').val('');

                     
                }
                $('#btn-proses').button('reset');

            },error: function (xhr, ajaxOptions, thrownError) {
                alert(xhr.responseText);
                $('#btn-proses').button('reset');
                    
            }
            });
        }
    }


  //klik button simpan
  $('#btn-simpan').click(function(){
    $('#btn-simpan').button('loading');
    please_wait(function(){});

    let quant_id    = $('#quant_id').val();
    let lot         = $('#lot').val();
    let kode_produk = $('#kode_produk').val();
    let nama_produk = $('#nama_produk').val();
    let qty         = $('#qty').val();
    let uom_qty     = $('#uom_qty').val();
    let qty2        = $('#qty2').val();
    let uom_qty2    = $('#uom_qty2').val();
    let lokasi_fisik= $('#lokasi_fisik').val();
    let qty_opname  = $('#qty_opname').val();
    let uom_opname  = $('#uom_opname').val();
    let departemen  = $('#departemen').val();

    $.ajax({
        type: "POST",
        dataType: "json",
        url :'<?php echo base_url('warehouse/opnamekg/simpan')?>',
        beforeSend: function(e) {
          if(e && e.overrideMimeType) {
              e.overrideMimeType("application/json;charset=UTF-8");
          }
        },
         data: {lot:lot, quant_id:quant_id, kode_produk:kode_produk, nama_produk:nama_produk, lokasi_fisik:lokasi_fisik, qty:qty, uom_qty:uom_qty, qty2:qty2, uom_qty2:uom_qty2, qty_opname:qty_opname, uom_opname:uom_opname, departemen:departemen
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
           //jika berhasil disimpan
              unblockUI( function() {
                setTimeout(function() { alert_notify(data.icon,data.message,data.type,function(){}); }, 1000);
              });
          }

          $('#quant_id').val('');
          $('#lot').val('');
          $('#kode_produk').val('');
          $('#nama_produk').val('');
          $('#qty').val('');
          $('#uom_qty').val('');
          $('#qty2').val('');
          $('#uom_qty2').val('');
          $('#lokasi_fisik').val('');
          $('#qty_opname').val('');

          $('#kode').val(data.kode_opname);
          $('#btn-simpan').button('reset');

        },error: function (xhr, ajaxOptions, thrownError) {
          alert(xhr.responseText);
          unblockUI( function(){});
          $('#btn-simpan').button('reset');

        }
    });
  });

   
</script>


</body>
</html>
