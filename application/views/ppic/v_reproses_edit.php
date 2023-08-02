
<!DOCTYPE html>
<html>
<head>
  <?php $this->load->view("admin/_partials/head.php") ?>
  <style type="text/css">
    table.table td .add {
        display: none;
    }
    .width-btn {
      width: 54px !important;
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
          $data['jen_status'] =  $reproses->status;
          $this->load->view("admin/_partials/statusbar.php", $data) 
        ?>
      </div>
    </section>

    <!-- Main content -->
    <section class="content">

      <!--  box content -->
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title"><b><?php echo $reproses->kode_reproses;?></b></h3>
          <div class="pull-right text-right" id="btn-header">
            <?php if($reproses->status=='draft'){?>
              <button class="btn btn-primary btn-sm" id="btn-import-produk" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing...">Tampilkan Semua Barang di Lokasi</button>            
            <?php }
            ?>
            </div>
        </div>
        <div class="box-body">
            <form class="form-horizontal">
                <div class="form-group">

                    <div class="col-md-6">
                        <div class="col-md-12 col-xs-12">
                            <div class="col-xs-4"><label>Kode Reproses </label></div>
                            <div class="col-xs-8">
                            <input type="text" class="form-control input-sm" name="kode_reproses" id="kode_reproses"  readonly="readonly" value = "<?php echo $reproses->kode_reproses;?>">                    
                            </div>                                    
                        </div>
                        <div class="col-md-12 col-xs-12">
                            <div class="col-xs-4"><label>Tanggal Dibuat </label></div>
                            <div class="col-xs-8 col-md-8">
                            <input type='text' class="form-control input-sm " name="tgl_buat" id="tgl_buat" readonly="readonly" value = "<?php echo $reproses->tanggal;?>">
                            </div>                                    
                        </div>

                        <div class="col-md-12 col-xs-12">
                            <div class="col-xs-4"><label>Jenis</label></div>
                            <div class="col-xs-8">
                            <input type='hidden' class="form-control input-sm " name="jenis" id="jenis" readonly="readonly" value = "<?php echo $reproses->id_jenis;?>">
                            <input type='text' class="form-control input-sm " name="nama_jenis" id="nama_jenis" readonly="readonly" value = "<?php echo $reproses->nama_jenis;?>">
                            </div>                                    
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="col-md-12 col-xs-12">
                            <div class="col-xs-4"><label>Notes </label></div>
                            <div class="col-xs-8">
                            <textarea type="text" class="form-control input-sm" name="note" id="note"><?php echo $reproses->note;?></textarea>
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
                    <li class="active"><a href="#tab_1" data-toggle="tab">Reproses Detail</a></li>
                    </ul>
                    <div class="tab-content over"><br>
                    <div class="tab-pane active" id="tab_1">

                        <!-- Tabel  -->
                        <div class="col-md-12 table-responsive over">
                        <table class="table table-condesed table-hover rlstable  over" width="100%" id="tableadjustment" >
                            <thead>                          
                            <tr>
                                <th class="style no">No.</th>
                                <th class="style">Kode Produk</th>                            
                                <th class="style">Nama Produk</th>
                                <th class="style">Lot</th>
                                <th class="style text-right" >Qty</th>
                                <th class="style">uom</th>
                                <th class="style text-right">Qty2</th>
                                <th class="style">Uom2</th>
                                <th class="style">Lokasi Asal</th>
                                <th class="style">Lot Baru</th>
                                <th class="style" style="min-width:50px;"></th>                            
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                                $no     = 1;
                                $empty  = true;
                                foreach ($details as $row) {
                                    $empty = false;
                            ?>
                                <tr>
                                    <td data-content="edit" data-id="row_order" data-isi="<?php echo $row->row_order; ?>"><?php echo $no++;?></td>
                                    <td><?php echo $row->kode_produk?></td>
                                    <td><?php echo $row->nama_produk?></td>
                                    <td><?php echo $row->lot?></td>
                                    <td class='text-right'><?php echo $row->qty?></td>
                                    <td ><?php echo $row->uom?></td>
                                    <td class='text-right'><?php echo $row->qty2?></td>
                                    <td ><?php echo $row->uom2?></td>
                                    <td ><?php echo $row->lokasi_asal?></td>
                                    <td><?php echo $row->lot_new?></td>
                                    <td align="center">
                                        <?php if($reproses->status == 'draft'){ ?>
                                                <a href="javascript:void(0)" class="delete" title="Hapus" data-toggle="tooltip" ><i class="fa fa-trash" style="color: red"></i></a>
                                        <?php } ?>
                                    </td>
                                </tr>

                            <?php 
                                }
                                if($empty == true){
                             ?>
                                <tr>
                                    <td colspan="11" align="left">Tidak ada Data</td>
                                </tr>
                            <?php
                                }
                            ?>
                            </tbody>
                            <tfoot>
                        </table>
                        </div>
                        <!-- Tabel  -->
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
   <?php $this->load->view("admin/_partials/modal.php") ?>
   <div id="foot">
    <?php $this->load->view("admin/_partials/footer.php") ?>
   </div>
  </footer>

</div>

<?php $this->load->view("admin/_partials/js.php") ?>

<script type="text/javascript">

    //set schedule_date
    var datenow=new Date();  
    datenow.setMonth(datenow.getMonth());
    $('#tanggal').datetimepicker({
        defaultDate: datenow,
        format : 'YYYY-MM-DD HH:mm:ss',
        ignoreReadonly: true,
    });


    //batal add row on batal button click
    $(document).on("click", ".batal", function(){
        var input = $(this).parents("tr").find('.prod');
        input.each(function(){
        $(this).parent("td").html($(this).val());
        }); 
        
        $(this).parents("tr").remove();
        $(".add-new").show();
    }); 

    //Tampilkan semua barang di lokasi (modal)
    $('#btn-import-produk').click(function(){
        $('#btn-import-produk').button('loading');
        var status = "<?php echo $reproses->status;?>";
        if(status == 'done'){
            alert_modal_warning('Status Reproses sudah Done ! ');
        }else if(status == 'cancel'){
            alert_modal_warning('Status Reproses sudah Batal ! ');
        }else{
            var kode_reproses = $('#kode_reproses').val();
            $('#btn-import-produk').button('reset');
            $("#tambah_data").modal({
                show: true,
                backdrop: 'static'
            })
            $(".tambah_data").html('<center><h5><img src="<?php echo base_url('dist/img/ajax-loader.gif') ?> "/><br>Please Wait...</h5></center>');
            $('.modal-title').text('Pilih Produk Untuk di Reproses');
            $.post('<?php echo site_url()?>ppic/reproses/import_produk',
                {kode_reproses      : kode_reproses},
                function(html){
                setTimeout(function() {$(".tambah_data").html(html); });
                }   
            );
        }
    });

    //klik button simpan
    $('#btn-simpan').click(function(){
        $('#btn-simpan').button('loading');
        please_wait(function(){});
        $.ajax({
            type: "POST",
            dataType: "json",
            url :'<?php echo base_url('ppic/reproses/simpan')?>',
            beforeSend: function(e) {
                if(e && e.overrideMimeType) {
                    e.overrideMimeType("application/json;charset=UTF-8");
                }
            },
            data: { kode_reproses : $('#kode_reproses').val(),
                    tanggal       : $('#tgl_buat').val(),
                    jenis         : $('#jenis').val(),
                    note          : $('#note').val(),
                    status        : 'edit',
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
                    $('#btn-simpan').button('reset');     
                    refresh_reproses();
                }else{
                    //jika berhasil disimpan/diubah
                    unblockUI( function() {
                        setTimeout(function() { alert_notify(data.icon,data.message,data.type,function(){}); }, 1000);
                    });
                    $('#btn-simpan').button('reset');
                }

            },error: function (xhr, ajaxOptions, thrownError) {
                alert('Error Simpan Data');
                unblockUI( function(){});
                $('#btn-simpan').button('reset');

            }
        });
    });


    //delete row di database
    $(document).on("click", ".delete", function(){ 
        $(this).parents("tr").find("td[data-content='edit']").each(function(){
            if($(this).attr('data-id')=="row_order"){
                $(this).html('<input type="hidden" class="form-control" value="' + ($(this).attr('data-isi')) + '" id="'+ $(this).attr('data-id') +'"> ');
            }
        });

        var kode_reproses   =  "<?php echo $reproses->kode_reproses; ?>";
        var row_order         = $(this).parents("tr").find("#row_order").val();  
        var this1  = $(this);

        bootbox.dialog({
        message: "Apakah Anda ingin menghapus data ?",
        title: "<i class='glyphicon glyphicon-trash'></i> Delete !",
        buttons: {
            danger: {
                label    : "Yes ",
                className: "btn-primary btn-sm",
                callback : function() {
                    this1.button('loading');
                    $.ajax({
                        dataType: "JSON",
                        url : '<?php echo site_url('ppic/reproses/hapus_reproses_items') ?>',
                        type: "POST",
                        data: {kode_reproses : kode_reproses, row_order : row_order  },
                        success: function(data){
                        if(data.sesi=='habis'){
                            //alert jika session habis
                            alert_modal_warning(data.message);
                            window.location.replace('../index');
                            this1.button('reset');
                        }else if(data.status == 'failed'){
                            alert_modal_warning(data.message);
                            this1.button('reset');
                            refresh_reproses();
                        }else{
                            this1.button('reset');
                            refresh_reproses();
                            $(".add-new").show();                   
                            alert_notify(data.icon,data.message,data.type,function(){});
                        }
                        },
                        error: function (xhr, ajaxOptions, thrownError){
                            alert('Error data');
                            alert(xhr.responseText);
                            this1.button('reset');
                        }
                    });
                }
            },
            success: {
                label    : "No",
                className: "btn-default  btn-sm",
                callback : function() {
                    $('.bootbox').modal('hide');
                }
            }
        }
        });
    });



     // Generate button click
    $(document).on("click", "#btn-cancel", function(e){

        e.preventDefault();

        var status_head = "<?php echo $reproses->status?>";

        if(status_head == 'cancel'){
            alert_modal_warning('Maaf, Data Tidak Bisa dibatalkan, Status Reproses Sudah Batal !');
        }else if(status_head == 'done'){
            alert_modal_warning('Maaf, Data Tidak Bisa dibatalkan, Status Reproses Sudah Done !');
        }else{

            var kode_reproses   =  "<?php echo $reproses->kode_reproses; ?>";
            bootbox.dialog({
                message: "Apakah Anda ingin membatalkan Data Reproses ?",
                title: "<i class='fa fa-warning'></i> Batal Reproses !",
                buttons: {
                    danger: {
                        label    : "Yes ",
                        className: "btn-primary btn-sm",
                        callback : function() {
                        please_wait(function(){});
                        $.ajax({
                            dataType: "JSON",
                            url : '<?php echo site_url('ppic/reproses/batal_reproses') ?>',
                            type: "POST",
                            data: {kode_reproses:kode_reproses},
                            success: function(data){
                                if(data.sesi=='habis'){
                                    //alert jika session habis
                                    alert_modal_warning(data.message);
                                    window.location.replace('../index');
                                }else if(data.status == 'failed'){
                                    unblockUI( function(){});
                                    alert_modal_warning(data.message);
                                    refresh_reproses();
                                }else{
                                    refresh_reproses();
                                    unblockUI( function() {
                                        setTimeout(function() { alert_notify(data.icon,data.message,data.type,function(){}); }, 1000);
                                    });
                                    $("#btn-header").load(location.href + " #btn-header>*");
                                }
                            },
                            error: function (xhr, ajaxOptions, thrownError){
                                //alert('Error Generate data');
                                alert(xhr.responseText);
                                refresh_reproses();
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
                            refresh_reproses();
                        }
                    }
                }
            });
        }
    });


    // Generate button click
    $(document).on("click", "#btn-generate", function(e){

        e.preventDefault();
        var status_head = "<?php echo $reproses->status?>";

        if(status_head == 'cancel'){
            alert_modal_warning('Maaf, Tidak Bisa Generate, Status Reproses Sudah Batal !');
        }else if(status_head == 'done'){
            alert_modal_warning('Maaf, Tidak Bisa Generate, Status Reproses Sudah Done !');
        }else{

            var kode_reproses   =  "<?php echo $reproses->kode_reproses; ?>";
            bootbox.dialog({
                message: "Apakah Anda ingin Generate Data ?",
                title: "<i class='fa fa-gear'></i> Generate Data !",
                buttons: {
                    danger: {
                        label    : "Yes ",
                        className: "btn-primary btn-sm",
                        callback : function() {
                        please_wait(function(){});
                        $.ajax({
                            dataType: "JSON",
                            url : '<?php echo site_url('ppic/reproses/generate_detail_reproses_items') ?>',
                            type: "POST",
                            data: {kode_reproses:kode_reproses},
                            success: function(data){
                            if(data.sesi=='habis'){
                                //alert jika session habis
                                alert_modal_warning(data.message);
                                window.location.replace('../index');
                            }else if(data.status == 'failed'){
                                unblockUI( function(){});
                                alert_modal_warning(data.message);
                                refresh_reproses();
                            }else{
                                refresh_reproses();
                                unblockUI( function() {
                                    setTimeout(function() { alert_notify(data.icon,data.message,data.type,function(){}); }, 1000);
                                });
                                $("#btn-header").load(location.href + " #btn-header>*");
                            }
                            },
                            error: function (xhr, ajaxOptions, thrownError){
                                alert('Error Generate data');
                                refresh_reproses();
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
                            refresh_reproses();
                        }
                    }
                }
            });
        }
    });
  

    //untuk merrefresh procurement order
    function refresh_reproses(){
        $("#tab_1").load(location.href + " #tab_1>*"); 
        $("#foot").load(location.href + " #foot");
        $("#status_bar").load(location.href + " #status_bar>*");
        
    }

</script>


</body>
</html>
