
<!DOCTYPE html>
<html>
<head>
  <?php $this->load->view("admin/_partials/head.php") ?>
  <style type="text/css">
    table.table td .add {
        display: none;
    }
    .width-btn {
      width: 52px !important;
    }
    table.table td .cancel {
        display: none;
        color : red;
        margin: 10 0px;
        min-width:  24px;
    }
    @media screen and (min-width: 768px) {
      .over {
        overflow-x: visible !important;
      }
    }

    .select2-container{
      border-color: red !important;
    }
    
    .min-width-100{
      min-width: 100px;
    }

    .error{
      border:  1px solid red !important;
    }

    .reserve_move{
      color:red;
    }

    .nowrap{
        white-space: break-word;
    }
    /*
    @media screen and (max-width: 767px) {
      .over {
       overflow-y: scroll !important; 
      }
    }
    */
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
      <div id ="status_bar">
        <?php 
          $data['jen_status'] =  $koreksi->status;
          $this->load->view("admin/_partials/statusbar.php", $data) 
        ?>
      </div>
    </section>

    <!-- Main content -->
    <section class="content">

      <!--  box content -->
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title"><b><?php echo $koreksi->kode_koreksi;?></b></h3>
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
                    <div class="col-xs-4"><label>Kode Koreksi </label></div>
                    <div class="col-xs-8">
                        <input type="text" class="form-control input-sm" name="kode_koreksi" id="kode_koreksi"  readonly="readonly" value='<?php echo $koreksi->kode_koreksi; ?>'/>                    
                    </div>                                    
                  </div>
                  <div class="col-md-12 col-xs-12">
                    <div class="col-xs-4"><label>Tanggal dibuat </label></div>
                    <div class="col-xs-8 col-md-8">
                        <input type='text' class="form-control input-sm " name="tgl_buat" id="tgl_buat" readonly="readonly" value="<?php echo $koreksi->tanggal_dibuat; ?>"  />
                    </div>                                    
                  </div>
                  <div class="col-md-12 col-xs-12">
                    <div class="col-xs-4"><label>Tanggal Transaksi </label></div>
                    <div class="col-xs-8 col-md-8">
                        <input type='text' class="form-control input-sm " name="tgl_buat" id="tgl_buat" readonly="readonly"  value="<?php echo $koreksi->tanggal_transaksi; ?>"  />
                    </div>                                    
                  </div>
              </div>
              <div class="col-md-6">
                  <div class="col-md-12 col-xs-12">
                    <div class="col-xs-4"><label>Notes </label></div>
                    <div class="col-xs-8">
                        <textarea type="text" class="form-control input-sm" name="note" id="note"><?php echo $koreksi->note?></textarea>
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
                  <li class="active"><a href="#tab_1" data-toggle="tab">Detail</a></li>
                  <li class=""><a href="#tab_2" data-toggle="tab">Mutasi</a></li>
                </ul>
                <div class="tab-content over"><br>
                  <div class="tab-pane active" id="tab_1">
                    <form name="input" class="form-horizontal" role="form" method="POST">
                      
                      <div class="col-md-6">
                        <div class="form-group">
                          <div class="col-md-12">
                            <?php if($koreksi->status == 'draft' || $koreksi->status == 'process') { ?>
                            <div class="input-group">
                                <button type="button" class="btn btn-primary btn-sm" id="btn-tampil">Pilih Produk </button> &nbsp;
                            </div>
                            <?php }?>
                          </div>
                        </div>
                      </div>
                    </form> 
                      <!-- Tabel  detail-->
                      <div class="col-md-12 table-responsive">
                        <table class="table table-condesed table-hover table-responsive rlstable" id="table_batch" > 
                            <label>Batch</label>
                            <thead>
                              <tr>
                                  <th class="style " width="20px  ">No.</th>
                                  <th class="style nowrap" width="80px" >Batch</th>
                                  <th class="style nowrap" width="80px" >Departemen</th>
                                  <th class="style nowrap" width="80px" >Koreksi</th>
                                  <th class="style nowrap" width="80px" >Tipe</th>
                                  <th class="style nowrap" width="80px" >Koreksi Lebih/Kurang</th>
                                  <th class="style nowrap" width="80px" >kode Transaksi</th>
                                  <th class="style nowrap" width="100px" >Kode Produk</th>
                                  <th class="style nowrap" width="120px" >Nama Produk</th>
                                  <th class="style nowrap" width="120px" >Koreksi Qty1</th>
                                  <th class="style nowrap" width="120px" >Koreksi Qty2</th>
                                  <th class="style nowrap" width="80px" >Status</th>
                                  <th class="style" width="50px">#</th>
                              </tr>
                            </thead>
                            <tbody></tbody>                                    
                        </table>
                      </div>
                      <!-- Tabel  -->

                      <!-- Tabel2  -->
                      <div class="col-md-12 table-responsive over">
                        <table class="table table-condesed table-hover rlstable over" width="100%" id="table_batch_items" >
                            <label>Batch Items</label>
                            <thead>                          
                              <tr>
                                  <th class="style no">No.</th>
                                  <th class="style nowrap">No Batch</th>                            
                                  <th class="style">Kode Produk</th>                            
                                  <th class="style">Nama Produk</th>                            
                                  <th class="style">Grade</th>
                                  <th class="style">Lot</th>
                                  <th class="style">Qty</th>
                                  <th class="style">Qty2</th>
                                  <th class="style">Qty Move</th>
                                  <th class="style">Qty2 Move</th>
                              </tr>
                            </thead>
                        </table>
                      </div>
                      <!-- Tabel2  -->
                  </div>
                  <!-- /.tab-pane1 -->
                  <div class="tab-pane" id="tab_2">

                    <!-- Tabel3  -->
                      <div class="col-md-12 table-responsive over">
                        <table class="table table-condesed table-hover rlstable over" width="100%" id="table_koreksi_mutasi" >
                            <label>Koreksi Mutasi</label>
                            <thead>                          
                              <tr>
                                  <th class="style no">No.</th>
                                  <th class="style nowrap">Departemen</th>                            
                                  <th class="style">Tahun</th>                            
                                  <th class="style">Bulan</th>                            
                                  <th class="style">No Batch</th>
                                  <th class="style">Tgl Proses Mutasi</th>
                                  <th class="style">Status</th>
                                  <th class="style"></th>
                              </tr>
                            </thead>
                        </table>
                      </div>
                      <!-- Tabel3  -->
                  </div>
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
    <?php $this->load->view("admin/_partials/modal.php") ?>
    <div id="foot">
     <?php 
        $data['kode'] =  $koreksi->kode_koreksi;
        $data['mms']  =  $mms->kode;
        $this->load->view("admin/_partials/footer.php",$data) 
     ?>
    </div>
  </footer>

</div>

<?php $this->load->view("admin/_partials/js.php") ?>

<script type="text/javascript">

    //Tampilkan semua barang di lokasi (modal)
    $('#btn-tampil').click(function(){
          var kode_koreksi = $('#kode_koreksi').val();
          $("#tambah_data").modal({
              show: true,
              backdrop: 'static'
          })
          $(".tambah_data").html('<center><h5><img src="<?php echo base_url('dist/img/ajax-loader.gif') ?> "/><br>Please Wait...</h5></center>');
          $('.modal-title').text('Cari Produk Untuk di Koreksi');
            $.post('<?php echo site_url()?>ppic/koreksimundur/cari_produk',
              {kode_koreksi : kode_koreksi},
              function(html){
                setTimeout(function() {$(".tambah_data").html(html); });
              }   
          );
    });

    //klik button simpan
    $('#btn-simpan').click(function(){

      var status_head = "<?php echo $koreksi->status;?>";
      var kode_koreksi = "<?php echo $koreksi->kode_koreksi;?>";
      if(status_head == 'cancel'){
        alert_modal_warning('Maaf, Data Tidak Bisa Disimpan, Status Sudah Batal !');
      }else if(status_head == 'done'){
        alert_modal_warning('Maaf, Data Tidak Bisa Disimpan, Status Sudah Done !');
      }else{
        $('#btn-simpan').button('loading');
        please_wait(function(){});
        $.ajax({
          type: "POST",
          dataType: "json",
          url :'<?php echo base_url('ppic/koreksimundur/simpan')?>',
          beforeSend: function(e) {
              if(e && e.overrideMimeType) {
                  e.overrideMimeType("application/json;charset=UTF-8");
              }
          },
          data: { kode_koreksi:kode_koreksi, note : $('#note').val(),
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
                //jika berhasil disimpan/diubah
                unblockUI( function() {
                    setTimeout(function() { alert_notify(data.icon,data.message,data.type,function(){}); }, 1000);
                  });
                $('#btn-simpan').button('reset');
              }
              $('#btn-simpan').button('reset');

            },error: function (xhr, ajaxOptions, thrownError) {
              $('#btn-simpan').button('reset');
              alert(xhr.responseText);
              unblockUI( function(){});
            }
        });
      }
    });


    $(document).on("click", "#add-batch", function(e) {
        tambah_batch();
    });

    function tambah_batch(){
        var status = "<?php echo $koreksi->status; ?>";
        var kode   = "<?php echo $koreksi->kode_koreksi; ?>";

        if(status == 'done'){
            alert_modal_warning('Maaf, Anda tidak bisa tambah batch, Status sudah <b>Done</b> ! ');
        }else if(status == 'cancel'){
            alert_modal_warning('Maaf, Anda tidak bisa tambah batch, Status sudah <b>Cancel</b> ! ');
        }else{
            $('#btn-tambah').button('reset');
            $("#tambah_data").modal({
                show: true,
                backdrop: 'static'
            })
            $("#tambah_data").removeClass('modal fade lebar').addClass('modal fade lebar_mode');
            $("#tambah_data .modal-dialog .modal-content .modal-body").addClass('add_batch');
            $("#tambah_data .modal-dialog .modal-content .modal-footer #btn-tambah").attr('disabled',true);

            // var deptid = //parsing data id dept untuk log history
            $(".tambah_data").html('<center><h5><img src="<?php echo base_url('dist/img/ajax-loader.gif') ?> "/><br>Please Wait...</h5></center>');
            $('.modal-title').text('Tambah Data Batch');
            $.post('<?php echo site_url()?>ppic/koreksimundur/add_batch_modal',
                {kode:kode},
            ).done(function(html){
                setTimeout(function() {
                    $(".add_batch").html(html)  
                },1000);
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
    }

    $(document).ready(function() {
        //datatables
        const  table = $('#table_batch').DataTable({ 
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
                "url": "<?php echo site_url('ppic/koreksimundur/get_data_batch')?>",
                "type": "POST",
                "data": {"kode": "<?php echo $koreksi->kode_koreksi;?>"}
            },
           
            "columnDefs": [
              { 
                "targets": [0,12], 
                "orderable": false, 
              },
              { 
                "targets": [9,10], 
                "className":"text-right ",
              },
              { 
                "targets": [8], 
                "className":"nowrap",
              },
            ],
        });

        const  table2 = $('#table_batch_items').DataTable({ 
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
                "url": "<?php echo site_url('ppic/koreksimundur/get_data_batch_items')?>",
                "type": "POST",
                "data": {"kode": "<?php echo $koreksi->kode_koreksi;?>"}
            },
           
            "columnDefs": [
              { 
                "targets": [0], 
                "orderable": false, 
              },
              { 
                "targets": [6,7,8,9], 
                "className":"text-right",
              },
              // { 
              //   "targets": [13], 
              //   "className":"nowrap",
              // },
            ],
        });

        const  table3 = $('#table_koreksi_mutasi').DataTable({ 
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
                "url": "<?php echo site_url('ppic/koreksimundur/get_data_koreksi_mutasi')?>",
                "type": "POST",
                "data": {"kode": "<?php echo $koreksi->kode_koreksi;?>"}
            },
           
            "columnDefs": [
              { 
                "targets": [0,7], 
                "orderable": false, 
              },
              // { 
              //   "targets": [8,9], 
              //   "className":"text-right ",
              // },
              { 
                "targets": [4], 
                "className":"nowrap",
              },
            ],
        });
 
        $(".modal").on('hidden.bs.modal', function(){
            refresh();
        });
    
        function refresh(){
            table.ajax.reload( function(){});
            table2.ajax.reload( function(){});
            table3.ajax.reload( function(){});
            $("#foot").load(location.href + " #foot");
            $("#status_bar").load(location.href + " #status_bar>*");
        }
  

        $(document).on("click", ".delete_batch", function(e) {
            let row_order = $(this).attr('data-row');
            let batch = $(this).attr('data-batch');
            delete_batch(this,row_order,batch)
        });

        function delete_batch(btn,row,batch){
            var kode        = "<?php echo $koreksi->kode_koreksi; ?>";
            var row         = row;
            var batch       = batch;
            let status       = "<?php echo $koreksi->status ?? ''; ?>";

            if(status == 'cancel'){
                alert_modal_warning('Maaf, Tidak Bisa dihapus, Status Sudah <b>cancel</b> !');
            } else if(status == 'done'){
                alert_modal_warning('Maaf, Tidak Bisa dihapus, Status Sudah <b>Done</b> !');
            } else {
                bootbox.confirm({
                    message: "Apakah Anda ingin menghapus data ini ?",
                    title: "<i class='glyphicon glyphicon-trash' style='color: red'></i> Delete !",
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
                            var btn_load    = $(btn);
                            btn_load.button('loading');
                            please_wait(function(){});
                            $.ajax({
                                type: "POST",
                                url :'<?php echo base_url('ppic/koreksimundur/hapus_batch')?>',
                                dataType: 'JSON',
                                data    : {kode:kode, row:row, batch:batch},
                                success: function(data){
                                    btn_load.button('reset');
                                    unblockUI( function(){
                                        setTimeout(function() { alert_notify(data.icon,data.message,data.type,function(){});}, 1000);
                                    });
                                    refresh();
                                },error: function (xhr, ajaxOptions, thrownError) {
                                    unblockUI(function() {});
                                    btn_load.button('reset');
                                    if(xhr.status == 401){
                                        var err = JSON.parse(xhr.responseText);
                                        alert(err.message);
                                    }else{
                                        alert("Error Hapus Data!")
                                    }   
                                }
                            });

                        }
                    }
                });
            }
        }


        $(document).on("click", ".koreksi_batch", function(e) {
            let row_order = $(this).attr('data-row');
            let batch = $(this).attr('data-batch');
            koreksi_batch(this,row_order,batch)
        });

        function koreksi_batch(btn,row,batch){
            var kode        = "<?php echo $koreksi->kode_koreksi; ?>";
            var row         = row;
            var batch       = batch;
            let status       = "<?php echo $koreksi->status ?? ''; ?>";

            if(status == 'cancel'){
                alert_modal_warning('Maaf, Tidak Bisa dihapus, Status Sudah <b>cancel</b> !');
            } else if(status == 'done'){
                alert_modal_warning('Maaf, Tidak Bisa dihapus, Status Sudah <b>Done</b> !');
            } else {
                bootbox.confirm({
                    message: "Apakah Anda melakukan Koreksi di Batch ini "+batch+" ?",
                    title: "Koreksi !",
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
                            var btn_load    = $(btn);
                            btn_load.button('loading');
                            please_wait(function(){});
                            $.ajax({
                                type: "POST",
                                url :'<?php echo base_url('ppic/koreksimundur/koreksi_batch')?>',
                                dataType: 'JSON',
                                data    : {kode:kode, row:row, batch:batch},
                                success: function(data){
                                    btn_load.button('reset');
                                    unblockUI( function(){
                                        setTimeout(function() { alert_notify(data.icon,data.message,data.type,function(){});}, 1000);
                                    });
                                    refresh();
                                },error: function (xhr, ajaxOptions, thrownError) {
                                    unblockUI(function() {});
                                    btn_load.button('reset');
                                    if(xhr.status == 401){
                                        var err = JSON.parse(xhr.responseText);
                                        alert(err.message);
                                    }else{
                                        alert("Error Koreksi Batch !");
                                    }   
                                }
                            });

                        }
                    }
                });
            }
        }

        
        $(document).on("click", ".proses_mutasi", function(e) {
            let id = $(this).attr('data-row');
            proses_mutasi(this,id)
        });

        function proses_mutasi(btn,id) {
            var kode        = "<?php echo $koreksi->kode_koreksi; ?>";
            var id          = id;
            let status       = "<?php echo $koreksi->status ?? ''; ?>";

            if(status == 'cancel'){
                alert_modal_warning('Maaf, Tidak Bisa dihapus, Status Sudah <b>cancel</b> !');
            } else if(status == 'done'){
                alert_modal_warning('Maaf, Tidak Bisa dihapus, Status Sudah <b>Done</b> !');
            } else {
                bootbox.confirm({
                    message: "Apakah Anda melakukan Proses Mutasi ?",
                    title: "Proses Mutasi !",
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
                            var btn_load    = $(btn);
                            btn_load.button('loading');
                            please_wait(function(){});
                            $.ajax({
                                type: "POST",
                                url :'<?php echo base_url('ppic/koreksimundur/proses_mutasi')?>',
                                dataType: 'JSON',
                                data    : {kode:kode, id:id},
                                success: function(data){
                                    btn_load.button('reset');
                                    unblockUI( function(){
                                        setTimeout(function() { alert_notify(data.icon,data.message,data.type,function(){});}, 1000);
                                    });
                                    refresh();
                                },error: function (xhr, ajaxOptions, thrownError) {
                                    unblockUI(function() {});
                                    btn_load.button('reset');
                                    if(xhr.status == 401){
                                        var err = JSON.parse(xhr.responseText);
                                        alert(err.message);
                                    }else{
                                        alert("Error Koreksi Batch !");
                                    }   
                                }
                            });

                        }
                    }
                });
            }
        }


        $(document).on("click", "#btn-done", function(e) {
            var kode        = "<?php echo $koreksi->kode_koreksi; ?>";
            let status      = "<?php echo $koreksi->status; ?>";

            if(status == 'cancel'){
                alert_modal_warning('Maaf, Tidak Bisa di Done kan, Status Sudah <b>cancel</b> !');
            } else if(status == 'done'){
                alert_modal_warning('Maaf, Tidak Bisa di Done kan, Status Sudah <b>Done</b> !');
            } else {
                bootbox.confirm({
                    message: "Apakah Anda ingin menyelesaikan Koreksi Mundur ini ?",
                    title: "Done Koreksi Mundur !",
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
                            var btn_load    = $(this);
                            btn_load.button('loading');
                            please_wait(function(){});
                            $.ajax({
                                type: "POST",
                                url :'<?php echo base_url('ppic/koreksimundur/done_koreksi_mundur')?>',
                                dataType: 'JSON',
                                data    : {kode:kode,},
                                success: function(data){
                                    btn_load.button('reset');
                                    unblockUI( function(){
                                        setTimeout(function() { alert_notify(data.icon,data.message,data.type,function(){});}, 1000);
                                    });
                                    refresh();
                                },error: function (xhr, ajaxOptions, thrownError) {
                                    unblockUI(function() {});
                                    btn_load.button('reset');
                                    if(xhr.status == 401){
                                        var err = JSON.parse(xhr.responseText);
                                        alert(err.message);
                                    }else{
                                        alert("Error Done Data!")
                                    }   
                                }
                            });

                        }
                    }
                });
            }
        });

        $(document).on("click", "#btn-cancel", function(e) {
            var kode        = "<?php echo $koreksi->kode_koreksi; ?>";
            let status      = "<?php echo $koreksi->status; ?>";

            if(status == 'cancel'){
                alert_modal_warning('Maaf, Tidak Bisa dibatalkan, Status Sudah <b>Cancel</b> !');
            } else if(status == 'done'){
                alert_modal_warning('Maaf, Tidak Bisa dibatalkan, Status Sudah <b>Done</b> !');
            } else if(status == 'process'){
                alert_modal_warning('Maaf, Tidak Bisa dibatalkan, Status Masih <b>Process</b> !');
            } else {
                bootbox.confirm({
                    message: "Apakah Anda ingin membatalkan Koreksi Mundur ini ?",
                    title: "Batal Koreksi Mundur !",
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
                            var btn_load    = $(this);
                            btn_load.button('loading');
                            please_wait(function(){});
                            $.ajax({
                                type: "POST",
                                url :'<?php echo base_url('ppic/koreksimundur/cancel_koreksi_mundur')?>',
                                dataType: 'JSON',
                                data    : {kode:kode,},
                                success: function(data){
                                    btn_load.button('reset');
                                    unblockUI( function(){
                                        setTimeout(function() { alert_notify(data.icon,data.message,data.type,function(){});}, 1000);
                                    });
                                    refresh();
                                },error: function (xhr, ajaxOptions, thrownError) {
                                    unblockUI(function() {});
                                    btn_load.button('reset');
                                    if(xhr.status == 401){
                                        var err = JSON.parse(xhr.responseText);
                                        alert(err.message);
                                    }else{
                                        alert("Error Cancel Data!")
                                    }   
                                }
                            });

                        }
                    }
                });
            }
        });


    });



</script>


</body>
</html>
