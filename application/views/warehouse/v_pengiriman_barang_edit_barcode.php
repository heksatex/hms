
<!DOCTYPE html>
<html lang="en">
<head>
  <?php $this->load->view("admin/_partials/head.php") ?>
</head>

  <style>
    button[id="btn-simpan"],
    button[id="btn-print"],
    button[id="btn-cancel"],
    button[id="btn-stok"]{/*untuk hidden button simpan di top */
      display: none;
    }
    .validScan{
      background-color: #dff0d8;
    }
  </style>

<body class="hold-transition skin-black fixed sidebar-mini" >
<!-- Site wrapper -->
<div class="wrapper">

  <!-- main -header -->
  <header class="main-header">
   <?php $this->load->view("admin/_partials/main-menu.php") ?>
   <?php 
     $data['deptid']     = $list->dept_id;
     $data['hms_top']    = 'empty';// menghilangkan top bar tulisan HMS saat mode HP
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
          <div class="col-md-4 col-sm-4 col-xs-7">
            <h3 class="box-title"><b><?php echo $list->kode;?></b></h3>
          </div>
          <div class="col-md-4 col-sm-4 col-xs-2">
            <center><label><h3 class="box-title">SCAN MODE</h3></label></center>
          </div>
          <div class="col-md-4 col-sm-4 col-xs-3">
            <div class="image pull-right text-right">
              <a href="<?php echo base_url('warehouse/pengirimanbarang/edit/'.encrypt_url($list->kode));?>" data-toggle="tooltip" title="List Mode">  
                <img src="<?php echo base_url('dist/img/barcode-form-icon.png'); ?>" style="width: 40%; height: auto; text-align: right;">
              </a>
            </div>
          </div>
        </div>
        <div class="box-body">
            <?php 
            if($akses_menu > 0 ){
              $disabled = '';
            }else{
              $disabled = 'disabled';
            }
            ?>
          <form class="form-horizontal" id="scan">
              <div class="col-md-6">
                <div class="form-group"> 
                  <div class="col-md-12 col-xs-12">
                    <!--div class="col-xs-2"><label>Barcode</label></div-->
                    <div class="col-xs-9">
                    <input type="hidden" class="form-control input-sm" name="kode" id="kode" value="<?php echo $list->kode;?>"/>
                    <input type="hidden" class="form-control input-sm" id="valid" value="0" />
                      <input type="text" class="form-control input-lg" name="barcode" id="barcode" autofocus onkeypress="enter(event);" autocomplete="off" placeholder="Scan Barcode / Lot" <?php echo $disabled?>/>
                    </div>
                    <div class=" col-xs-2">
                      <button type="button" id="btn-scan" onclick="cek_data();" class="btn btn-primary btn-lg" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing..." <?php echo $disabled?>>Scan</button>
                    </div>                                    
                  </div>
                </div>
              </div>

              <div class="col-md-6">
               <center>
                <label class="label label-success" style="font-size: 20px;" id="counter_valid">Valid Scan : <?php echo $count?> / <?php echo $count_all?> </label> 
                </center>
               <br>
              </div>
              <div id="stat">
                <input type="hidden" class="form-control input-sm" name="status" id="status" value="<?php echo $move_id['status'];?>" readonly/>
              </div>

            <div class="row">
              <div class="col-md-12">
                <!-- tabel -->
                <div class="col-md-12 table-responsive">
                  <table class="table table-condesed table-hover rlstable" width="100%" id ="tbl_detail">
                    <tr>
                      <th class="style no">No.</th>
                      <th class="style" style="width: 120px;">Kode Product</th>
                      <th class="style">Product</th>
                      <th class="style" style="text-align: right;">Qty</th>
                      <th class="style">uom</th>
                      <th class="style" style="text-align: right;">Qty2</th>
                      <th class="style">uom2</th>
                      <th class="style">Lot</th>
                      <th class="style">Reff Note</th>
                      <th class="style">Status</th>
                      <th class="style">Quant Id</th>
                    </tr>
                    <tbody>
                        <?php
                         
                         foreach ($items as $row) {
                           if($row->valid == 't'){
                            $color = 'num validScan';
                           }else{
                             $color = 'num';
                           }
                        ?>
                      <tr class="<?php echo $color;?>" >
                        <td></td>
                        <td><?php echo $row->kode_produk?></td>
                        <td><?php echo $row->nama_produk?></td>
                        <td align="right"><?php echo $row->qty?></td>
                        <td><?php echo $row->uom?></td>
                        <td  align="right"><?php echo $row->qty2?></td>
                        <td><?php echo $row->uom2?></td>
                        <td><?php echo $row->lot?>
                        <td><?php echo $row->reff_note?></td>
                            <!--input type="hidden" name="lot"  id="lot" value="<?php echo $row->lot?>">
                            <input type="hidden" name="valid" id="valid" value="0" style="width: 20px;"></td-->
                        <td><?php echo $row->status?></td>
                        <td><?php echo $row->quant_id?></td>
                      </tr>
                       <?php 
                        }
                        ?>
                    </tbody>
                  </table>
                </div>
                <!-- /.tabel -->
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
     <?php $this->load->view("admin/_partials/footer.php") ?>
    </div>
  </footer>
</div>
<!--/. Site wrapper -->
<?php $this->load->view("admin/_partials/js.php") ?>

<script type="text/javascript">

  status  = $('#status').val();
  if(status == 'done' || status == 'cancel'){
    $('#btn-scan').prop('disabled', true);
    $('#barcode').prop('disabled', true);
  }

  function alert_scan(message){
    var dialog = bootbox.dialog({
      	message: message,
    		closeButton: false,
        title: "<font color='red'><i class='glyphicon glyphicon-alert'></i></font> Warning !",
        buttons: {
            confirm: {
                label: 'ok',
                className: 'btn-primary btn-sm',
                callback : function() {
                  $('.bootbox').modal('hide');
                  $('#barcode').focus();
                }
            },
        },
  	});
    dialog.init(function(){
      dialog.find([type='button']).focus();
  	});
  }
  
  //relaoad page 
  $('[name="valid"]').val('0');

  //untuk counter scan
  var lot = document.getElementsByName('lot');
  $('#dari').html('0');
  $('#sampai').html(lot.length);

  //untuk enter di textfield barcode
  function enter(e)
  {
    if(e.keyCode === 13){
          e.preventDefault(); 
          cek_data(); //panggil fungsi tambah baris
      }
  }

  //untuk cek valid barcode
  function cek_data() {
    var txtbarcode = $('#barcode').val();
    var barcode   = txtbarcode.trim().toUpperCase();
    var deptid  = '<?php echo $move_id['dept_id'];?>';

    var lenRow =lot.length;
    if(txtbarcode == ""){//alert jika barcode scan kosong 
      var message =  "Barcode Tidak Boleh Kosong !";
      alert_scan(message);
      $('#barcode').focus();
    }else{

      $('#btn-scan').button('loading');

      $.ajax({
         type: "POST",
         dataType: "json",
         url :'<?php echo base_url('warehouse/pengirimanbarang/valid_barcode_out')?>',
         beforeSend: function(e) {
            if(e && e.overrideMimeType) {
                e.overrideMimeType("application/json;charset=UTF-8");
            }
         },
         data: {kode:$('#kode').val(), deptid:deptid, txtbarcode:txtbarcode
          },success: function(data){
            if(data.sesi == "habis"){
              //alert jika session habis
              alert_scan(data.message);
              window.location = baseUrl;//replace ke halaman login
            }else if(data.status == "failed"){
              //alert_scan(data.message);
              refresh_div_out();
              alert_notify(data.icon,data.message,data.type,function(){});
              
            }else{
              alert_notify(data.icon,data.message,data.type,function(){});
              refresh_div_out();
            }
            $('#barcode').focus();
            $('#barcode').val('');
            $('#btn-scan').button('reset');

          },error: function (xhr, ajaxOptions, thrownError) { 
            $('#btn-proses').button('reset');
            alert(xhr.responseText);
            refresh_div_out();
          }
      });
    }
  }

   ///refresh div
  function refresh_div_out(){
      $("#status_bar").load(location.href + " #status_bar");
      $("#foot").load(location.href + " #foot");
      $("#tbl_detail").load(location.href + " #tbl_detail");
      $("#counter_valid").load(location.href + " #counter_valid");
  }

    //untuk aksi kirim barang
  $(document).on('click','#btn-kirim',function(e){
    var lot  = document.getElementsByName('lot');
    var scan =  $('#valid').val();
    var total   = lot.length;
    var move_id = '<?php echo $move_id['move_id'];?>'; 
    var status  =  $('#status').val();
    var deptid  = '<?php echo $move_id['dept_id'];?>';
    var origin  = '<?php echo $move_id['origin'];?>';  
    var baseUrl = '<?php echo base_url(); ?>';
    var method  = '<?php echo $move_id['method']?>';

    //alert(scan)
    if(status == 'cancel'){
        var message = 'Maaf, Data Tidak bisa Dikirim, Data Sudah dibatalkan !';
        alert_modal_warning(message);

    }else if(status == 'done'){
        var message = 'Maaf, Data Sudah Terkirim !';
        alert_modal_warning(message);

    }else if(scan<total && status =='ready' ){
      var message = "Maaf, Barcode Belum valid Semua !";
      alert_scan(message);
   
    }else if(status =='draft' ){
      var message = "Maaf, Product Belum ready !";
      alert_modal_warning(message);

    }else{
      bootbox.dialog({
      message: "Anda yakin ingin mengirim ?",
      title: "<i class='glyphicon glyphicon-send'></i> Send !",
      buttons: {
        primary: {
            label    : "Yes ",
            className: "btn-primary btn-sm",
            callback : function() {
                  please_wait(function(){});
                  $('#btn-kirim').button('loading');
                  $.ajax({
                        type: 'POST',
                        dataType : 'json',
                        url : "<?php echo site_url('warehouse/pengirimanbarang/kirim_barang')?>",
                        data : {kode:$('#kode').val(), move_id:move_id, origin:origin, deptid:deptid, method:method, mode:"scan" },
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
                      window.location = baseUrl;//replace ke halaman login
                      //window.location.replace('../index');
                    }else if(response.status == 'draft' || response.status == 'ada' ||  response.status == 'not_valid'){
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
                      $("#stat").load(location.href + " #stat");
                    }
                  })
            }
        },
        default: {
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

</script>


</body>
</html>
