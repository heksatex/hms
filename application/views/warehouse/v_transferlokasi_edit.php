
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
    if($tl->lokasi_tujuan != 'GJD'){
?>
    <style>
        table tr > *:nth-child(4),
        table tr > *:nth-child(5),
        table tr > *:nth-child(10),
        table tr > *:nth-child(11){
            display : none
        }
    </style>
<?php
    }
?>

   
</head>

<body class="hold-transition skin-black fixed sidebar-mini sidebar-collapse">
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
                <?php if($tl->dept_id == "GSP"){ ?>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-12 col-md-4 col-sm-4"><label>Lokasi Dari / Asal</label></div>
                  <div class="col-xs-12 col-md-8 col-sm-8">
                    <input type="text" class="form-control input-lg" name="lokasi_dari" id="lokasi_dari"  value="<?php echo $tl->lokasi_dari; ?>" placeholder="Scan Lokasi Dari">
                  </div>                                    
                </div>
                <?php } ?>
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
                <br>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-0 col-md-12 col-sm-12" id="total">
                    <label for="total" id="total_items"></label>
                  </div>
                </br>
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
                            <th class="style">Kode Produk</th>                            
                            <th class="style">Nama Produk</th>
                            <th class="style">Corak Remark</th>
                            <th class="style">Warna Remark</th>
                            <th class="style">Lokasi Asal</th>
                            <th class="style">Lot</th>
                            <th class="style">Qty1 [HPH]</th>                          
                            <th class="style">Qty2 [HPH]</th>                            
                            <th class="style">Qty1 [JUAL]</th>                            
                            <th class="style">Qty2 [JUAL]</th>                            
                            <th class="style no" style="min-width:50px;">#</th>
                          </tr>
                        </thead>
                        <tbody>
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
    $('#lokasi_dari').prop('disabled', true);
    $('#lokasi_tujuan').prop('disabled', true);
    $('#barcode_id').prop('disabled', true);
  }

  const playAudio = ((url) => {
      var audio = new Audio(url);
      audio.volume = 1;
      audio.play();
  });

 //untuk merrefresh 
  function refresh_transferLokasi(){
      // $("#tab_1").load(location.href + " #tab_1>*"); 
      $("#total").load(location.href + " #total");
      $("#foot").load(location.href + " #foot");
      $("#status_bar").load(location.href + " #status_bar>*");
      $("#tgl_gen").load(location.href + " #tgl_gen>*");
      table.ajax.reload( function(){});
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
                  lokasi_dari       : $("#lokasi_dari").val()

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
        data    : {kode:$('#kode').val(), dept_id:dept_id, lokasi_dari : $('#lokasi_dari').val(), lokasi_tujuan : $('#lokasi_tujuan').val(), barcode_id : barcode_id},
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
              playAudio("<?= base_url('dist/beep.MP3') ?>");
          }
          $('#btn-scan').button('reset');
                  
        },error: function (jqXHR, textStatus, errorThrown){
          alert(jqXHR.responseText);
          $('#btn-scan').button('reset');
        }
    });
    
  }

  $(document).on("click", ".delete_item_transfer", function(e) {
        let row_order = $(this).attr('data-row');
        let quant_id = $(this).attr('data-quant');
        let kode_tl   =  "<?php echo $tl->kode_tl?>";
        hapus_items(this,kode_tl,quant_id,row_order)
  });

  // hapus items
  function hapus_items(btn,kode_tl,quant_id,row_order){

    dept_id   = "<?php echo $tl->dept_id?>";
    var btn_load    = $(btn);
    btn_load.button('loading');
    $.ajax({
        dataType: "JSON",
        url     : '<?php echo base_url('warehouse/transferlokasi/hapus_items_barcode')?>',
        type    : "POST",
        data    : { kode:kode_tl, dept_id:dept_id, quant_id:quant_id,row_order:row_order},
        success: function(data){
          if(data.sesi == "habis"){
              //alert jika session habis
              alert('Waktu Anda telah habis ! ');
              window.location.replace('index.php');
              $('#btn-scan').button('reset');
              btn_load.button('reset');
          }else if(data.status == 'failed'){
              alert(data.message);
              //document.getElementById(data.name).focus();             
              $('#btn-scan').button('reset');
              refresh_transferLokasi();
              btn_load.button('reset');
          }else{
              //jika success 
              refresh_transferLokasi();
              alert_notify(data.icon,data.message,data.type,function(){});
              $('#btn-scan').button('reset');
              btn_load.button('reset');
          }
                  
        },error: function (jqXHR, textStatus, errorThrown){
            btn_load.button('reset');
            if(xhr.status == 401){
                var err = JSON.parse(xhr.responseText);
                alert(err.message);
            }else{
                alert("Error Simpan Data!")
            }
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


  var table;
  $(document).ready(function() {
        //datatables
        table = $('#table_detail').DataTable({ 
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
                "url": "<?php echo site_url('warehouse/transferlokasi/get_data_item')?>",
                "type": "POST",
                "data": {"kode": "<?php echo $tl->kode_tl;?>"},
            },
           
            "columnDefs": [
              { 
                "targets": [0,11], 
                "orderable": false, 
              },
              { 
                "targets": [7,8,9,10], 
                "className":"text-right nowrap",
              },
              { 
                "targets": [2], 
                "className":"nowrap",
              },
            ],
            "drawCallback": function( settings, start, end, max, total, pre ) {  
                console.log(this.fnSettings().json); /* for json response you can use it also*/ 
                let total_record = this.fnSettings().fnRecordsTotal(); // total number of rows
                $('#total_items').text( 'Total Lot yang di Transfer Lokasi : ' + total_record + ' Lot' )
            },
          
        });

  });


   
</script>


</body>
</html>
