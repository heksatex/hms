
<!DOCTYPE html>
<html lang="en">
<head>
  <?php $this->load->view("admin/_partials/head.php") ?>
</head>

  <style>
    button[id="btn-simpan"],
    button[id="btn-stok"]{/*untuk hidden button simpan di top */
      display: none;
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
            <h3 class="box-title" ><b><?php echo $list->kode;?></b></h3>
          </div>
          <div class="col-md-4 col-sm-4 col-xs-2">
            <center><label><h3 class="box-title">SCAN MODE</h3></label></center>
          </div>
          <div class="col-md-4 col-sm-4 col-xs-3">
            <div class="image pull-right text-right">
              <a href="<?php echo base_url('warehouse/pengirimanbarang/edit/'.encrypt_url($list->kode));?>" data-toggle="tooltip" title="Form Mode"> 
                <img src="<?php echo base_url('dist/img/barcode-form-icon.png'); ?>" style="width: 40%; height: auto; text-align: right; ">
              </a>
            </div>
          </div>
        </div>
        <div class="box-body">
          <form class="form-horizontal" id="scan">
              <div class="col-md-6">
                <div class="form-group"> 
                  <div class="col-md-12 col-xs-12">
                    <div class="col-xs-2"><label>Barcode</label></div>
                    <div class="col-xs-8">
                    <input type="hidden" class="form-control input-sm" name="kode" id="kode" value="<?php echo $list->kode;?>" />
                    <input type="hidden" class="form-control input-sm" id="valid" value="0" />
                    <input type="text" class="form-control input-sm" name="barcode" id="barcode" autofocus onkeypress="enter(event);" autocomplete="off" />
                    </div>
                    <div class=" col-xs-2">
                      <button type="button" id="scan" onclick="cek_data();" class="btn btn-primary btn-sm" >scan</button>
                    </div>                                    
                  </div>
                </div>
              </div>
              <div class="col-md-6">
               <center>
                <label class="label label-warning" style="font-size: 20px;">Valid Scan : <label id="dari"></label> / <label id="sampai"></label> </label> 
                </center>
               <br>
              </div>
              <div id="stat">
                <input type="hidden" class="form-control input-sm" name="status" id="status" value="<?php echo $move_id['status'];?>" />
              </div>
            <div class="row">
              <div class="col-md-12">
                <!-- tabel -->
                <div class="col-md-12 table-responsive">
                  <table class="table table-condesed table-hover rlstable" width="100%" id ="tbl_detail">
                   
                    <tr>
                      <th class="style no">No.</th>
                      <th class="style">Product</th>
                      <th class="style">Qty</th>
                      <th class="style">uom</th>
                      <th class="style">Lot</th>
                      <th class="style">Status</th>
                      <th class="style">Quant Id</th>
                    </tr>
                      <tbody>
                        <?php
                          $i=1;
                         foreach ($items as $row) {
                        ?>
                      <tr class="num" id="<?php echo $i;?>">
                        <td></td>
                        <td><?php echo $row->nama_produk?></td>
                        <td><?php echo $row->qty?></td>
                        <td><?php echo $row->uom?></td>
                        <td><?php echo $row->lot?>
                            <input type="hidden" name="lot"  id="lot" value="<?php echo $row->lot?>">
                            <input type="hidden" name="valid" id="valid" value="0" style="width: 20px;"></td>
                        <td><?php echo $row->status?></td>
                        <td><?php echo $row->quant_id?></td>
                       <?php 
                        $i++;
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

    <!-- Load Partial Modal -->
   <?php $this->load->view("admin/_partials/modal.php") ?>

</div>
<!--/. Site wrapper -->
<?php $this->load->view("admin/_partials/js.php") ?>


<script type="text/javascript">

  function alert_scan(message){
    bootbox.dialog({
      message: message,
      title: "<font color='red'><i class='glyphicon glyphicon-alert'></i></font> Warning !",
      buttons: {
        primary: {
              label    : "ok",
              className: "btn-primary  btn-sm",
              callback : function() {
                $('.bootbox').modal('hide');
                $('#barcode').focus();
              }
         }
      }
    });
  }

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
    var lot       = document.getElementsByName('lot');
    var cek       = document.getElementsByName('cek');
    var valid     = document.getElementsByName('valid');
    var tot_valid = 0;
    var invalid   = 1;
    var barcode = txtbarcode.trim();

    var lenRow =lot.length;
    if(txtbarcode == ""){//alert jika barcode scan kosong 
      var message =  "Barcode Tidak Boleh Kosong !";
      alert_scan(message);

    }else{
      for(var i=0; i<lenRow; i++){
       // alert('masuk ke'+i);
        var data = lot[i].value;
        if(barcode==data){
          //alert('sama');
          $('#barcode').val('');
          $('[name="valid"]').eq(i).val("1");
          document.getElementById(i+1).style.backgroundColor="#dff0d8";
          $('#barcode').focus();
          invalid = 0;
        }else{
          $('#barcode').val('');
          $('#barcode').focus();
        }

          tot_valid = tot_valid + parseInt(valid[i].value);
      }

     $('#dari').html(tot_valid);
     $('#valid').val(tot_valid);

      if(invalid == 1 ){
        var message =  "Barcode Tidak Valid !";
        alert_scan(message);
      }
    }
  }

  ///refresh div
  function refresh_div_out()
  {
      $("#status_bar").load(location.href + " #status_bar");
      $("#foot").load(location.href + " #foot");
      $("#tbl_detail").load(location.href + " #tbl_detail");
  }

  //untuk aksi kirim barang
  $(document).on('click','#btn-kirim',function(e){
    var lot  = document.getElementsByName('lot');
    var scan =  $('#valid').val();
    var total   = lot.length;
    var move_id = '<?php echo $move_id['move_id'];?>'; 
    var status  =  $('#status').val();
    var origin  = '<?php echo $move_id['origin'];?>'; 
    var deptid  = '<?php echo $move_id['dept_id'];?>'; 
    var method  = '<?php echo $move_id['method']?>';
    var baseUrl = '<?php echo base_url(); ?>';
    refresh_div_out();

    //alert(scan)
    if(scan<total && status =='ready' ){
      var message = "Maaf, Barcode Belum valid Semua !";
      alert_scan(message);     
   
    }else if(status =='draft' ){
      var message = "Maaf, Product Belum ready !";
      alert_modal_warning(message);
   
    }else if(status == 'done'){
      var message = 'Maaf, Data Sudah Terkirim !';
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
                  $('#btn-kirim').button('loading');
                  $.ajax({
                        type: 'POST',
                        dataType : 'json',
                        url : "<?php echo site_url('warehouse/pengirimanbarang/kirim_barang')?>",
                        data : {kode : $('#kode').val(), move_id:move_id, origin:origin, deptid:deptid,  method:method },
                        error: function (xhr, ajaxOptions, thrownError) { 
                          alert(xhr.responseText);
                          $('#btn-kirim').button('reset');
                        }
                  })
                  .done(function(response){
                    if(response.sesi == 'habis'){//jika session habis
                      alert_modal_warning(response.message);
                      window.location = baseUrl;//replace ke halaman login
                    }else if(response.status == 'draft' || response.status == 'ada' ||  response.status == 'not_valid'){
                     //jika ada item masih draft/status sudah terkirim/lokasi lot tidak valid
                      alert_modal_warning(response.message);
                      refresh_div_out();
                      $('#btn-kirim').button('reset');
                    }else{
                      if(response.backorder == "yes"){
                        alert_modal_warning(response.message2);
                      }
                      unblockUI( function() {
                        setTimeout(function() { alert_notify(response.icon,response.message,response.type); }, 1000);
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
