
<!DOCTYPE html>
<html>
<head>
  <?php $this->load->view("admin/_partials/head.php"); 
    if($tl->status == 'done' or $tl->status == 'cancel'){
  ?>
    <style>
      button[id="btn-scan"]{/*untuk hidden button simpan/cancel*/
        display: none;
      }
   </style>
<?php 
    }
?>

   
</head>

<body class="hold-transition skin-black fixed sidebar-mini">
<!-- Site wrapper -->
<div class="wrapper">

  <!-- main -header -->
  <header class="main-header">
   <?php $this->load->view("admin/_partials/main-menu.php") ?>
    <?php 
     $data['deptid']     = $id_dept;
     $data['hms_top']    = 'empty';// menghilangkan top bar tulisan HMS saat mode HP
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
          $data['jen_status'] =  $tl->status;
          $this->load->view("admin/_partials/statusbar.php", $data) 
        ?>
      </div>
    </section>

    <!-- Main content -->
    <section class="content">

      <!--  box content -->
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title"><b><?php echo $tl->kode_tl?></b></h3>
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
                    <input type="text" class="form-control input-sm" name="kode" id="kode" value="<?php echo $tl->kode_tl; ?>"  readonly="readonly" />                    
                  </div>                                    
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Tanggal Dibuat </label></div>
                  <div class="col-xs-8 col-md-8">
                     <input type='text' class="form-control input-sm " name="tgl_buat" id="tgl_buat" readonly="readonly" value="<?php echo $tl->tanggal_dibuat; ?>" />
                  </div>                                    
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Tanggal Kirim </label></div>
                  <div class="col-xs-8 col-md-8" id="tgl_gen">
                     <input type='text' class="form-control input-sm " name="tgl_kirim" id="tgl_kirim" readonly="readonly" value="<?php if($tl->tanggal_transfer != '0000-00-00 00:00:00'){echo $tl->tanggal_transfer; }; ?>" />
                  </div>                                    
                </div>

                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Note </label></div>
                  <div class="col-xs-8">
                    <textarea type="text" class="form-control input-sm" name="note" id="note"><?php echo $tl->note; ?></textarea>
                  </div>                                    
                </div>
              </div>

              <div class="col-md-6">
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Departemen</label></div>
                  <div class="col-xs-8">
                    <input type="text" class="form-control input-sm" name="departemen" id="departemen"  value="<?php echo $tl->departemen; ?>" readonly="readonly" >
                  </div>                                    
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-12 col-md-4 col-sm-4"><label>Lokasi Tujuan</label></div>
                  <div class="col-xs-12 col-md-8 col-sm-8">
                    <input type="text" class="form-control input-lg" name="lokasi_tujuan" id="lokasi_tujuan"  value="<?php echo $tl->lokasi_tujuan; ?>" placeholder="Scan Lokasi Tujuan">
                  </div>                                    
                </div>
                 <div class="col-md-12 col-xs-12">
                  <div class="col-xs-12 col-md-4 col-sm-4"><label>Scan Barcode</label></div>
                  <div class="col-xs-12 col-md-8 col-sm-8">
                    <input type="text" class="form-control input-lg" name="barcode_id" id="barcode_id"  placeholder="Scan Barcode ">
                  </div>                                    
                </div>
                <br>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-0 col-md-4 col-sm-4"></div>
                  <div class="col-xs-12 col-md-8 col-sm-8">
                    <button  type="button" class="btn btn-sm btn-primary" name="btn-scan" id="btn-scan" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing..."><i class="fa fa-barcode"></i> Scan </button>
                  </div>                                    
                </div>                
              </div>
            </div>           
          </form>

          <!-- Tabs  Details-->
          <div class="row">
            <div class="col-md-12">
              <div class="">
                <ul class="nav nav-tabs " >
                  <li class="active"><a href="#tab_1" data-toggle="tab">Details</a></li>
                </ul>
                <div class="tab-content over"><br>
                  <div class="tab-pane active" id="tab_1">
                    <!-- Table Resposive-->
                    <div class="col-md-12 table-responsive over">
                      <table class="table table-condesed table-hover rlstable  over" width="100%" id="table_detail" >
                        <thead>                          
                          <tr>
                            <th class="style no">No.</th>
                            <th class="style">Quant Id</th>                            
                            <th class="style">Kode Produk</th>                            
                            <th class="style">Nama Produk</th>
                            <th class="style">Lokasi Asal</th>
                            <th class="style">Lot</th>
                            <th class="style">Qty</th>
                            <th class="style">UoM</th>
                            <th class="style">Qty2</th>
                            <th class="style">UoM2</th>
                            <th class="style no"></th>
                          </tr>
                        </thead>
                        <tbody>
                            <?php
                                $no = 1;
                                $color = '';
                                foreach ($tli as $row) {
                            ?>
                                <tr>
                                  <td><?php echo $no++; ?></td>
                                  <td><?php echo $row->quant_id; ?></td>
                                  <td><?php echo $row->kode_produk; ?></td>
                                  <td><?php echo $row->nama_produk; ?></td>
                                  <td><?php echo $row->lokasi_asal; ?></td>
                                  <td><?php echo $row->lot; ?></td>
                                  <td align='right'><?php echo $row->qty; ?></td>
                                  <td><?php echo $row->uom; ?></td>
                                  <td align='right'><?php echo $row->qty2; ?></td>
                                  <td><?php echo $row->uom2; ?></td>
                                  <td>
                                    <?php if($tl->status == 'ready' or $tl->status == 'draft'){?>
                                    <a onclick="hapus_items('<?php  echo $tl->kode_tl ?>','<?php  echo $row->row_order ?>','<?php echo htmlentities($row->nama_produk) ?>','<?php  echo $row->quant_id ?>','<?php  echo htmlentities($row->lot) ?>')"  href="javascript:void(0)" data-toggle="tooltip" title="Hapus Data"><i class="fa fa-trash" style="color: red"></i> </a>
                                    <?php }?>

                                  </td>
                                </tr>
                            <?php
                                }
                            ?>
                        </tbody>
                      </table>
                    </div>
                    <!-- // Table Resposive-->
                  </div>
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
        $data['kode'] =  $tl->kode_tl;
        $data['mms']  =  $mms->kode;
        $this->load->view("admin/_partials/footer.php",$data) 
     ?>
    </div>
  </footer>

</div>

<?php $this->load->view("admin/_partials/js.php") ?>

<script type="text/javascript">
  status  = '<?php echo $tl->status;?>';
  if(status == 'done' || status == 'cancel'){
    $('#lokasi_tujuan').prop('disabled', true);
    $('#barcode_id').prop('disabled', true);
  }

 //untuk merrefresh 
  function refresh_transferLokasi(){
      $("#tab_1").load(location.href + " #tab_1>*"); 
      $("#foot").load(location.href + " #foot");
      $("#status_bar").load(location.href + " #status_bar>*");
      $("#tgl_gen").load(location.href + " #tgl_gen>*");
  }

  //klik button simpan
  $('#btn-simpan').click(function(){

    dept_id  = '<?php echo $tl->dept_id; ?>';
    status   = '<?php echo $tl->status; ?>'

    if(status == 'cancel'){
        var message = 'Maaf, Transfer Lokasi telah dibatalkan !';
        alert_modal_warning(message);
    }else if(status=='done'){
        var message = 'Maaf, Status Transfer Lokasi Sudah Done !';
        alert_modal_warning(message);
    }else{

        $('#btn-simpan').button('loading');
        please_wait(function(){});
        $.ajax({
           type: "POST",
           dataType: "json",
           url :'<?php echo base_url('warehouse/transferlokasi/simpan')?>',
           beforeSend: function(e) {
              if(e && e.overrideMimeType) {
                  e.overrideMimeType("application/json;charset=UTF-8");
              }
           },
           data: {kode              : $('#kode').val(),
                  note              : $('#note').val(),
                  departemen        : dept_id,
                  lokasi_tujuan     : $('#lokasi_tujuan').val(),

            },success: function(data){
              if(data.sesi == "habis"){
                //alert jika session habis
                alert_modal_warning(data.message);
                window.location.replace('index');
              }else if(data.status == "failed"){
                  $('#btn-simpan').button('reset');
                  unblockUI( function() {
                    setTimeout(function() { alert_notify(data.icon,data.message,data.type,function(){}); }, 1000);
                  });
                   document.getElementById(data.field).focus();
              }else{
               //jika berhasil disimpan/diubah
                unblockUI( function() {
                  setTimeout(function() { 
                    alert_notify(data.icon,data.message,data.type,function(){});},1000); 
                });
              }
              $('#btn-simpan').button('reset');
              refresh_transferLokasi();

            },error: function (xhr, ajaxOptions, thrownError) {
              alert(xhr.responseText);
              unblockUI( function(){});
              $('#btn-simpan').button('reset');

            }
        });
    }

  });


  //klik button transfer
  $('#btn-generate').click(function(){

    dept_id  = '<?php echo $tl->dept_id; ?>';
    status   = '<?php echo $tl->status; ?>'

    if(status == 'cancel'){
        var message = 'Maaf, Transfer Lokasi telah dibatalkan !';
        alert_modal_warning(message);
    }else if(status=='done'){
        var message = 'Maaf, Status Transfer Lokasi Sudah Done !';
        alert_modal_warning(message);
    }else{

      var kode_tl   =  "<?php echo $tl->kode_tl; ?>";
      bootbox.dialog({
      message: "Apakah Anda ingin Mentransfer Lokasi Data ?",
      title: "<i class='fa fa-send'></i> Transfer Data !",
      buttons: {
        danger: {
            label    : "Yes ",
            className: "btn-primary btn-sm",
            callback : function() {
              please_wait(function(){});
              $.ajax({
                 type: "POST",
                 dataType: "json",
                 url :'<?php echo base_url('warehouse/transferlokasi/generate')?>',
                 beforeSend: function(e) {
                    if(e && e.overrideMimeType) {
                        e.overrideMimeType("application/json;charset=UTF-8");
                    }
                 },
                 data: {kode          : $('#kode').val(),
                        dept_id       : dept_id,
                  },success: function(data){
                    if(data.sesi == "habis"){
                      //alert jika session habis
                      alert_modal_warning(data.message);
                      window.location.replace('index');
                    }else if(data.status == "failed"){
                        $('#btn-generate').button('reset');
                        alert_modal_warning(data.message);
                        unblockUI( function(){});
                    }else{
                      //jika berhasil disimpan
                      $('#btn-generate').button('reset');
                      unblockUI( function() {
                        setTimeout(function() { 
                          alert_notify(data.icon,data.message,data.type,function(){});},1000); 
                      });
                    }
                    refresh_transferLokasi();
                  },error: function (xhr, ajaxOptions, thrownError) {
                    alert(xhr.responseText);
                    unblockUI( function(){});
                    $('#btn-generate').button('reset');

                  }
              });
            }
        },
        success: {
              label    : "No",
              className: "btn-default  btn-sm",
              callback : function() {
                $('.bootbox').modal('hide');
                refresh_transferLokasi();
              }
        }
      }
      });

    }
  });


  $('#lokasi_tujuan, #barcode_id').keypress(function(event){
    var keycode = (event.keyCode ? event.keyCode : event.which);
    if(keycode == '13'){
       cek_validasi(); 
    }
  });
  

  $('#btn-scan').click(function(){
     //alert('tes');
     cek_validasi();
  });

  function cek_validasi(){
    //alert('tes');
    dept_id     = "<?php echo $tl->dept_id?>";
    barcode_id = $('#barcode_id').val();
    $('#barcode_id').val(''); /// clear barcode id
    
    $('#btn-scan').button('loading');
    $.ajax({
        dataType: "JSON",
        url     :'<?php echo base_url('warehouse/transferlokasi/scan_barcode')?>',
        type    : "POST",
        data    : {kode:$('#kode').val(), dept_id:dept_id, lokasi_tujuan : $('#lokasi_tujuan').val(), barcode_id : barcode_id},
        success: function(data){
          if(data.sesi == "habis"){
              //alert jika session habis
              alert('Waktu Anda telah habis ! ');
              window.location.replace('index.php');
              $('#btn-scan').button('reset');
          }else if(data.status == 'failed'){
              alert_notify(data.icon,data.message,data.type,function(){});
              document.getElementById(data.field).focus();             
              refresh_transferLokasi();
          }else{
              //jika success 
              $('#btn-scan').button('reset');
              $('#barcode_id').val('');
              refresh_transferLokasi();
              document.getElementById('barcode_id').focus();             
              alert_notify(data.icon,data.message,data.type,function(){});
          }
          $('#btn-scan').button('reset');
                  
        },error: function (jqXHR, textStatus, errorThrown){
          alert(jqXHR.responseText);
          $('#btn-scan').button('reset');
        }
    });
    
  }


  // hapus items
  function hapus_items(kode_tl,row_order,nama_produk,quant_id,lot){

    dept_id   = "<?php echo $tl->dept_id?>";
    $.ajax({
        dataType: "JSON",
        url     : '<?php echo base_url('warehouse/transferlokasi/hapus_items_barcode')?>',
        type    : "POST",
        data    : { kode:kode_tl, dept_id:dept_id, quant_id:quant_id, barcode_id:lot, nama_produk:nama_produk, row_order:row_order},
        success: function(data){
          if(data.sesi == "habis"){
              //alert jika session habis
              alert('Waktu Anda telah habis ! ');
              window.location.replace('index.php');
              $('#btn-scan').button('reset');
          }else if(data.status == 'failed'){
              alert(data.message);
              //document.getElementById(data.name).focus();             
              $('#btn-scan').button('reset');
              refresh_transferLokasi();

          }else{
              //jika success 
              refresh_transferLokasi();
              alert_notify(data.icon,data.message,data.type,function(){});
              $('#btn-scan').button('reset');

          }
                  
        },error: function (jqXHR, textStatus, errorThrown){
          alert(jqXHR.responseText);
          
        }
    });

  }


    // Generate button click
  $(document).on("click", "#btn-cancel", function(e){

    e.preventDefault();

    status   = '<?php echo $tl->status; ?>'

    if(status == 'cancel'){
        var message = 'Maaf, Transfer Lokasi telah dibatalkan !';
        alert_modal_warning(message);
    }else if(status=='done'){
        var message = 'Maaf, Status Transfer Lokasi Sudah Done !';
        alert_modal_warning(message);
    }else{
    
      var kode_tl   =  "<?php echo $tl->kode_tl; ?>";
      bootbox.dialog({
      message: "Apakah Anda ingin membatalkan Data Transfer Lokasi ?",
      title: "<i class='fa fa-warning'></i> Batal Transfer Lokasi !",
      buttons: {
        danger: {
            label    : "Yes ",
            className: "btn-primary btn-sm",
            callback : function() {
              please_wait(function(){});
              $.ajax({
                dataType: "JSON",
                url : '<?php echo site_url('warehouse/transferlokasi/batal_transfer_lokasi') ?>',
                type: "POST",
                data: {kode:kode_tl},
                success: function(data){
                  if(data.sesi=='habis'){
                      //alert jika session habis
                      alert_modal_warning(data.message);
                      window.location.replace('../index');
                  }else if(data.status == 'failed'){
                      unblockUI( function(){});
                      alert_modal_warning(data.message);
                      refresh_transferLokasi();
                  }else{
                      refresh_transferLokasi();
                      unblockUI( function() {
                      setTimeout(function() { alert_notify(data.icon,data.message,data.type,function(){}); }, 1000);
                      });
                   }
                },
                error: function (xhr, ajaxOptions, thrownError){
                  //alert('Error Generate data');
                  alert(xhr.responseText);
                  refresh_transferLokasi();
                  unblockUI( function(){});
                }
              });
            }
        },
        success: {
              label    : "No",
              className: "btn-default  btn-sm",
              callback : function() {
                $('.bootbox').modal('hide');
                refresh_transferLokasi();
              }
        }
      }
      });

    }
  });



   
</script>


</body>
</html>
