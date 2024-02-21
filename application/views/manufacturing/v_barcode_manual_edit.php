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

    .select2-container--default .select2-selection--single{
        height : 30px;
        font-size : 12px;
        padding: 5px 12px;
    }
    .nowrap{
        white-space: nowrap;
    }

    .notification {
        background: #f44336;
        color: white;
        font-family: 'PT Sans';
        font-size: 18px;
        padding: 8px;
        /* width: 100%; */
        min-height: 50px;
        margin-left: 230px;
        transition: transform 0.3s ease-in-out, margin 0.3s ease-in-out;
    }

    @media (min-width: 768px) {
        .sidebar-mini.sidebar-collapse .content-wrapper,
        .sidebar-mini.sidebar-collapse .right-side,
        .sidebar-mini.sidebar-collapse .notification,
        .sidebar-mini.sidebar-collapse .main-footer {
            margin-left:50px !important;
        }
    }
    .content-header2{
        padding: 100px 0px 0 0px;
    }
  </style>
</head>

<body class="hold-transition skin-black fixed sidebar-mini sidebar-collapse" > 
<!-- Site wrapper -->
<div class="wrapper">

  <!-- main -header -->
  <header class="main-header">
            <?php $this->load->view("admin/_partials/main-menu.php");
                if (!isset($access->status) || !$access->status) {
                    echo '<div class="notification"><div class="col-md-12 text-center"> PC ini tidak diizinkan membuat Barcode Manual <i class="fa fa-close" aria-hidden="true"></i> </div></div>';
                }
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
    <?php if (!isset($access->status) || !$access->status){?>
    <section class="content-header2">
            <?php }else{ ?>
    <section class="content-header">
            <?php } ?>
     <div id ="status_bar">
       <?php 
         $data['jen_status'] = $mrpm->status;
         $this->load->view("admin/_partials/statusbar.php", $data); 
       ?>
      </div>
    </section>

    <!-- Main content -->
    <section class="content">

      <!--  box content -->
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title">Form  Edit : <b><?php echo $mrpm->kode;?></b></h3>
        </div>

        <div class="box-body">
            <form class="form-horizontal">

                <div class="form-group">

                    <div class="col-md-6">
                        <div class="col-md-12 col-xs-12">
                            <div class="col-xs-4"><label>Kode </label></div>
                            <div class="col-xs-8">
                                <input type="text" class="form-control input-sm" name="kode" id="kode" readonly="readonly" value="<?php echo $mrpm->kode; ?>"/>
                            </div>
                        </div>
                        <div class="col-md-12 col-xs-12">
                            <div class="col-xs-4"><label>Tgl.dbuat </label></div>
                            <div class="col-xs-8 col-md-8">
                                <input type='text' class="form-control input-sm " name="tgl_buat" id="tgl_buat" readonly="readonly" value="<?php echo $mrpm->tanggal_buat?>" />
                            </div>
                        </div>   
                        <div class="col-md-12 col-xs-12">
                            <div class="col-xs-4"><label>Tgl.transaksi </label></div>
                            <div class="col-xs-8 col-md-8">
                                <input type='text' class="form-control input-sm " name="tgl_transaksi" id="tgl_transaksi" readonly="readonly" value="<?php echo $mrpm->tanggal_transaksi?>" />
                            </div>
                        </div>                                    
                        <div class="col-md-12 col-xs-12">
                            <div class="col-xs-4"><label>Marketing</label></div>
                            <div class="col-xs-8 col-md-8">
                                <select class="form-control input-sm select2" name="marketing" id="marketing" >
                                <option value=""></option>
                                    <?php 
                                        $selected = "";
                                        foreach ($sales_group as $row) {
                                            if($row->kode_sales_group == $mrpm->sales_group) $selected = "selected" ?? '';
                                            echo "<option value='".$row->kode_sales_group."' ".$selected.">".$row->nama_sales_group."</option>";
                                            $selected = "";
                                        }
                                    ?>
                                </select>
                            </div>                                    
                        </div>
                        <div class="col-md-12 col-xs-12">
                            <div class="col-xs-4"><label>Alasan</label></div>
                            <div class="col-xs-8 col-md-8">
                                <select class="form-control input-sm select2" name="type" id="type" >
                                <option value=""></option>
                                <?php 
                                        $selected = "";
                                        foreach ($type as $row) {
                                            if($row->id == $mrpm->id_type_adjustment) $selected = "selected" ?? '';
                                            echo "<option value='".$row->id."' ".$selected.">".$row->name_type."</option>";
                                            $selected = "";
                                        }
                                    ?>
                                </select> 
                            </div>                                    
                        </div>
                    </div>

                    <div class="col-md-6">                          
                        <div class="col-md-12 col-xs-12">
                            <div class="col-xs-4"><label>Notes </label></div>
                            <div class="col-xs-8">
                                <textarea type="text" class="form-control input-sm" name="notes" id="notes"><?php echo $mrpm->notes;?></textarea>
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
                    </div>
                    

                </div>

            </form>

            <div class="row">
                <div class="col-md-12">
                    <!-- Custom Tabs -->
                    <div class="">
                        <ul class="nav nav-tabs " >
                        <li class="active"><a href="#tab_1" data-toggle="tab">Detail</a></li>
                        </ul>
                        <div class="tab-content over"><br>
                            <div class="tab-pane active" id="tab_1">
                                 <!-- Tabel  -->
                                <div class="col-md-12 table-responsive over">
                                    <table class="table table-condesed table-hover rlstable over" width="100%" id="table_batch" >
                                        <label>Batch</label>
                                        <thead>                          
                                            <tr>
                                                <th class="style no">No.</th>
                                                <th class="style nowrap">Nama Produk</th>                            
                                                <th class="style nowrap">Corak Remark</th>
                                                <th class="style nowrap">Warna Remark</th>
                                                <th class="style nowrap">Quality</th>
                                                <th class="style nowrap">Jml Pcs</th>
                                                <th class="style nowrap">Qty</th>
                                                <th class="style nowrap">Qty2</th>
                                                <th class="style nowrap">Qty Jual</th>
                                                <th class="style nowrap">Qty2 Jual</th>
                                                <th class="style nowrap">Lbr.Jadi</th>
                                                <th class="style nowrap">K3L</th>
                                                <th class="style" style="min-width:50px;">#</th>        
                                            </tr>
                                        </thead>
                                        <tfoot>
                                            <?php if($mrpm->status != 'done' AND $mrpm->status != 'cancel'){?>
                                                <tr >
                                                    <td colspan="3" >
                                                        <a href="javascript:void(0)" class="add-new-batch" id="add-batch"><i class="fa fa-plus"></i> Tambah Data</a>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        <tfoot>
                                    </table>
                                </div>

                                 <!-- Tabel  -->
                                 <div class="col-md-12 table-responsive over">
                                    <table class="table table-condesed table-hover rlstable over" width="100%" id="table_batch_items" >
                                        <label>Batch Items</label>
                                        <thead>                          
                                            <tr>
                                                <th class="style no">No.</th>
                                                <th class="style">Nama Produk</th>                            
                                                <th class="style">Corak Remark</th>
                                                <th class="style">Warna Remark</th>
                                                <th class="style">Lot</th>
                                                <th class="style">Qty</th>
                                                <th class="style">Qty2</th>
                                                <th class="style">Qty Jual</th>
                                                <th class="style">Qty2 Jual</th>
                                                <th class="style">Lebar Jadi</th>
                                                <th class="style">#</th>        
                                            </tr>
                                        </thead>
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

   <div id="foot">
     <?php 
        $data['kode'] =  $mrpm->kode;
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

<script>
    
    // untuk focus after select2 close
    $(document).on('focus', '.select2', function(e) {
        if (e.originalEvent) {
            var s2element = $(this).siblings('select');
            s2element.select2('open');
            // Set focus back to select2 element on closing.
            s2element.on('select2:closing', function(e) {
                s2element.select2('focus');
            });
        }
    });

    $(document).on('select2:opening', '.select2', function(e) {
        if ($(this).attr('readonly') == 'readonly') {
            //   console.log( 'can not open : readonly' );
            e.preventDefault();
            $(this).select2('close');
            return false;
        } else {
            //   console.log( 'can be open : free' );
        }
    });

    $('.select2').select2({
        allowClear: true,
        placeholder: 'Pilih',
        width: '100%'
    });

    function htmlentities_script(str) {
        return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    function validAngka(a){
      if(!/^[0-9.]+$/.test(a.value)){
        a.value = a.value.substring(0,a.value.length-1000);
        alert_notify('fa fa-warning','Maaf, Inputan Qty Hanya Berupa Angka !','danger',function(){});
      }
    }

    $(".modal").on('hidden.bs.modal', function(){
        refresh();
    });

    function refresh(){
        // $("#tab_1").load(location.href + " #tab_1");
        table.ajax.reload( function(){});
        table2.ajax.reload( function(){});
        $("#foot").load(location.href + " #foot");
        $("#status_bar").load(location.href + " #status_bar>*");
    }

    var table;
    $(document).ready(function() {
        //datatables
        table = $('#table_batch').DataTable({ 
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
                "url": "<?php echo site_url('manufacturing/barcodemanual/get_data_batch_barcode_manual')?>",
                "type": "POST",
                "data": {"kode": "<?php echo $mrpm->kode;?>"}
            },
           
            "columnDefs": [
              { 
                "targets": [0,12], 
                "orderable": false, 
              },
              { 
                "targets": [5,6,7,8,9,10], 
                "className":"text-right nowrap",
              },
              { 
                "targets": [11], 
                "className":"nowrap",
              },
            ],
        });
 
    });

    var table2;
    $(document).ready(function() {
        //datatables
        table2 = $('#table_batch_items').DataTable({ 
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
                "url": "<?php echo site_url('manufacturing/barcodemanual/get_data_batch_items_barcode_manual')?>",
                "type": "POST",
                "data": {"kode": "<?php echo $mrpm->kode;?>"}
            },
           
            "columnDefs": [
              { 
                "targets": [0], 
                "orderable": false, 
              },
              { 
                "targets": [5,6,7,8,9], 
                "className":"text-right",
              },
              {
               'targets':10,
               'data' : 10,
               'checkboxes': {
                  'selectRow': true
                },
                'createdCell':  function (td, cellData, rowData, row, col){
                   var rowId = rowData[10];
                },
              },
            ],
            "select": {
              'style': 'multi'
            },
            'rowCallback': function(row, data, dataIndex){
               var rowId = data[10];
            }
        });
 
    });

    $(document).on("click", "#add-batch", function(e) {
        tambah_batch();
    });

    function tambah_batch(){
        var status = 'draft';
        var kode   = "<?php echo $mrpm->kode; ?>";
        let acces = "<?php echo $access->status ?? ''; ?>";

        if(acces == 0){
            alert_notify('fa fa-warning', 'PC ini tidak diizinkan membuat Barcode Manual  !', 'danger', function() {});
        }else if(status == 'done'){
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
            $.post('<?php echo site_url()?>manufacturing/barcodemanual/add_batch_modal',
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

    // btn simpan
    $(document).on("click", "#btn-simpan", function(e) {
        e.preventDefault();

        let kode        = $('#kode').val();
        let marketing   = $('#marketing').val();
        let type        = $('#type').val();
        let notes       = $('#notes').val();
        let status      = "<?php echo $mrpm->status; ?>";
        let acces       = "<?php echo $access->status ?? ''; ?>";
       
        if(acces == 0){
            alert_notify('fa fa-warning', 'PC ini tidak diizinkan membuat Barcode Manual  !', 'danger', function() {});
        }else if(status == 'cancel'){
            alert_modal_warning('Maaf, Tidak Bisa Disimpan, Status Sudah <b>cancel</b> !');
        } else if(status == 'done'){
            alert_modal_warning('Maaf, Tidak Bisa Disimpan, Status Sudah <b>Done</b> !');
        }else if (marketing.length === 0) {
            alert_notify('fa fa-warning', 'Marketing Harus dipilih !', 'danger', function() {});
            $('#marketing').select2('focus');
        }else if (type.length === 0) {
            alert_notify('fa fa-warning', 'Alasan Harus dipilih !', 'danger', function() {});
            $('#type').select2('focus');
        } else if (notes == '') {
            alert_notify('fa fa-warning', 'Notes tidak boleh kosong !', 'danger', function() {});
            $('#note').focus();
        } else {
            $('#btn-simpan').button('loading');
            please_wait(function() {});
            $.ajax({
                type: "POST",
                dataType: "json",
                url: '<?php echo base_url('manufacturing/barcodemanual/save_barcode_manual')?>',
                beforeSend: function(e) {
                    if (e && e.overrideMimeType) {
                        e.overrideMimeType("application/json;charset=UTF-8");
                    }
                },
                data: {
                    kode        :kode,
                    marketing   : marketing,
                    type        : type,
                    notes       : notes,
                },
                success: function(data) {
                    if (data.status == 'failed') {
                        unblockUI(function() {
                            setTimeout(function() {
                                alert_notify(data.icon, data.message, data.type,
                            function() {});
                            }, 1000);
                        });
                        if(data.field){
                            $('#' + data.field).focus();
                        }
                    } else {
                        unblockUI( function() {
                            setTimeout(function() { 
                                alert_notify(data.icon,data.message,data.type, function(){},1000); 
                            });
                        });
                        refresh();
                    }
                    $('#btn-simpan').button('reset');
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    unblockUI(function() {});
                    $('#btn-simpan').button('reset');
                    if(xhr.status == 401){
                        var err = JSON.parse(xhr.responseText);
                        alert(err.message);
                    }else{
                        alert("Error Simpan Data!")
                    }                   
                }
            });
        }
    });

    $(document).on("click", ".delete_batch", function(e) {
        let row_order = $(this).attr('data-row');
        delete_batch(this,row_order)
    });

    function delete_batch(btn,row){

        var kode        = "<?php echo $mrpm->kode; ?>";
        var row         = row;
        let acces       = "<?php echo $access->status ?? ''; ?>";

        if(acces == 0){
            alert_notify('fa fa-warning', 'PC ini tidak diizinkan membuat Barcode Manual  !', 'danger', function() {});
        }else if(status == 'cancel'){
            alert_modal_warning('Maaf, Tidak Bisa Generate, Status Sudah <b>cancel</b> !');
        } else if(status == 'done'){
            alert_modal_warning('Maaf, Tidak Bisa Generate, Status Sudah <b>Done</b> !');
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
                            url :'<?php echo base_url('manufacturing/barcodemanual/delete_mrp_batch')?>',
                            dataType: 'JSON',
                            data    : {kode:kode, row:row,},
                            success: function(data){
                                if(data.status == 'failed'){
                                    alert_modal_warning(data.message);
                                    unblockUI( function(){});
                                    btn_load.button('reset');
                                }else{
                                    btn_load.button('reset');
                                    unblockUI( function(){
                                        setTimeout(function() { alert_notify(data.icon,data.message,data.type,function(){});}, 1000);
                                    });
                                    refresh();
                                }

                            },error: function (xhr, ajaxOptions, thrownError) {
                                unblockUI(function() {});
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
                }
            });
        }
    }

    $(document).on("click", ".edit_batch", function(e) {
        let row_order = $(this).attr('data-row');
        edit_batch(row_order)
    });

    function edit_batch(row){
        var kode     =  "<?php echo $mrpm->kode; ?>";
        var status   =  "<?php echo $mrpm->status; ?>";
        let acces       = "<?php echo $access->status ?? ''; ?>";

        if(acces == 0){
            alert_notify('fa fa-warning', 'PC ini tidak diizinkan membuat Barcode Manual  !', 'danger', function() {});
        }else{
            $('#btn-tambah').button('reset');
                $("#tambah_data").modal({
                    show: true,
                    backdrop: 'static'
            })
            if(status== 'done' || status == 'cancel'){
                $("#tambah_data .modal-dialog .modal-content .modal-footer").html('<button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Tutup</button>');
            }
            $("#tambah_data").removeClass('modal fade lebar').addClass('modal fade lebar_mode');
            $("#tambah_data .modal-dialog .modal-content .modal-body").addClass('add_batch');
            $("#tambah_data .modal-dialog .modal-content .modal-footer #btn-tambah").attr('disabled',true);
    
            $(".tambah_data").html('<center><h5><img src="<?php echo base_url('dist/img/ajax-loader.gif') ?> "/><br>Please Wait...</h5></center>');
            $('.modal-title').text('Edit Data Batch');
            $.post('<?php echo site_url()?>manufacturing/barcodemanual/edit_batch_modal',
                    {kode:kode,row:row},
            ).done(function(html){
                    setTimeout(function() {
                        $(".add_batch").html(html)  
                    },1000);
                $("#tambah_data .modal-dialog .modal-content .modal-footer #btn-tambah").attr('disabled',false);
            });
        }
    }

    // btn generate
    $(document).on("click", "#btn-generate", function(e) {
        e.preventDefault();

        let kode        = $('#kode').val();
        let status      = "<?php echo $mrpm->status; ?>";
        let acces       = "<?php echo $access->status ?? ''; ?>";

        if(acces == 0){
            alert_notify('fa fa-warning', 'PC ini tidak diizinkan membuat Barcode Manual  !', 'danger', function() {});
        }else if(status == 'cancel'){
            alert_modal_warning('Maaf, Tidak Bisa Generate, Status Sudah <b>cancel</b> !');
        } else if(status == 'done'){
            alert_modal_warning('Maaf, Tidak Bisa Generate, Status Sudah <b>Done</b> !');
        } else {
            bootbox.confirm({
                message: "Apakah Anda ingin Generate Data ?",
                title: "<i class='fa fa-gear'></i> Generate Data !",
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
                        var btn_load    = $('#btn-generate');
                        btn_load.button('loading');
                        please_wait(function(){});
                        $.ajax({
                            type: "POST",
                            url :'<?php echo base_url('manufacturing/barcodemanual/generate_barcode_manual')?>',
                            dataType: 'JSON',
                            data    : {kode:kode},
                            success: function(data){
                                if(data.status == 'failed'){
                                    alert_modal_warning(data.message);
                                    unblockUI( function(){});
                                    btn_load.button('reset');
                                }else{
                                    btn_load.button('reset');
                                    unblockUI( function(){
                                        setTimeout(function() { alert_notify(data.icon,data.message,data.type,function(){});}, 1000);
                                    });
                                    refresh();
                                }

                            },error: function (xhr, ajaxOptions, thrownError) {
                                unblockUI(function() {});
                                btn_load.button('reset');
                                if(xhr.status == 401){
                                    var err = JSON.parse(xhr.responseText);
                                    alert(err.message);
                                }else{
                                    alert("Error Generate Data!")
                                }   
                            }
                        });

                    }
                }
            });
        }
    });

    // btn cancel
    $(document).on("click", "#btn-cancel", function(e) {
        e.preventDefault();

        let kode        = "<?php echo $mrpm->kode; ?>";
        let status      = "<?php echo $mrpm->status; ?>";
        let acces       = "<?php echo $access->status ?? ''; ?>";

        if(acces == 0){
            alert_notify('fa fa-warning', 'PC ini tidak diizinkan membuat Barcode Manual  !', 'danger', function() {});
        }else if(status == 'cancel'){
            alert_modal_warning('Maaf, Tidak Bisa Batalkan, Status Sudah <b>Cancel</b> !');
        } else if(status == 'done'){
            alert_modal_warning('Maaf, Tidak Bisa Batalkan, Status Sudah <b>Done</b> !');
        } else {
            bootbox.confirm({
                message: "Apakah Anda ingin membatalkan Data Barcode Manual ?",
                title: "<i class='fa fa-warning'></i> Batal Barcode Manual !",    
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
                        var btn_load    = $('#btn-cancel');
                        btn_load.button('loading');
                        please_wait(function(){});
                        $.ajax({
                            type: "POST",
                            url :'<?php echo base_url('manufacturing/barcodemanual/cancel_barcode_manual')?>',
                            dataType: 'JSON',
                            data    : {kode:kode},
                            success: function(data){
                                if(data.status == 'failed'){
                                    alert_modal_warning(data.message);
                                    unblockUI( function(){});
                                    btn_load.button('reset');
                                }else{
                                    btn_load.button('reset');
                                    unblockUI( function(){
                                        setTimeout(function() { alert_notify(data.icon,data.message,data.type,function(){});}, 1000);
                                    });
                                    refresh();
                                }

                            },error: function (xhr, ajaxOptions, thrownError) {
                                unblockUI(function() {});
                                btn_load.button('reset');
                                if(xhr.status == 401){
                                    var err = JSON.parse(xhr.responseText);
                                    alert(err.message);
                                }else{
                                    alert("Error cancel Data!")
                                }   
                            }
                        });

                    }
                }
            });
        }
    });
    
    $(document).on("click", "#btn-print", function(e) {
        print_lot(this)
    });
    // print
    function print_lot(btn){

        var myCheckboxes = table2.column(10).checkboxes.selected();
        var myCheckboxes_arr = new Array();

        $.each(myCheckboxes, function(index, rowId){        
            myCheckboxes_arr.push(rowId);
        });
        let kode            = "<?php echo $mrpm->kode; ?>";
        let desain_barcode  = $('#desain_barcode').val();
        let status          = "<?php echo $mrpm->status; ?>";

        if(status == 'draft'){
            alert_modal_warning('Maaf, Tidak Bisa di print, Status masih <b>Draft</b> !');
        } else if(status == 'cancel'){
            alert_modal_warning('Maaf, Tidak Bisa di print, Status <b>cancel</b> !');
        }else  if (myCheckboxes.length === 0) {
            alert_notify('fa fa-warning', 'Pilih LOT terlebih dahulu yang akan di print !', 'danger', function() {});
        }else  if (desain_barcode.length === 0) {
            alert_notify('fa fa-warning', 'Desain Barcode Harus dipilih !', 'danger', function() {});
            $('#desain_barcode').select2('focus');
        } else {
            var btn_load = $(btn);
            btn_load.button('loading');
            please_wait(function() {});
            $.ajax({
                type: "POST",
                dataType: "json",
                url: '<?php echo base_url('manufacturing/barcodemanual/print_barcode_manual')?>',
                beforeSend: function(e) {
                    if (e && e.overrideMimeType) {
                        e.overrideMimeType("application/json;charset=UTF-8");
                    }
                },
                data: {
                    kode            :kode,
                    desain_barcode  : desain_barcode,
                    quant_id_arr    : myCheckboxes_arr,
                },
                success: function(data) {
                    if (data.status == 'failed') {
                        unblockUI(function() {
                            setTimeout(function() {
                                alert_notify(data.icon, data.message, data.type,
                            function() {});
                            }, 1000);
                        });
                        if(data.field){
                            $('#' + data.field).focus();
                        }
                    } else {
                        var divp = document.getElementById('printed');
                        divp.innerHTML = data.data_print;
                        unblockUI( function() {
                                setTimeout(function() { alert_notify(data.icon,data.message,data.type,function(){ 
                                        print_voucher();
                                });},1000); 
                        });
                    }
                   btn_load.button('reset');
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    unblockUI(function() {});
                    btn_load.button('reset');
                    console.log(xhr);
                    if(xhr.status == 401){
                        // var err = JSON.parse(xhr.responseText);
                        alert(err.message);
                    }else{
                        alert("Error print Data!")
                    }                   
                }
            });
        }
    }

    // load new page print
    function print_voucher() {
        var win = window.open();
        win.document.write($("#printed").html());
        win.document.close();
        setTimeout(function(){ win.print(); win.close();}, 200);
    }
</script>


</body>
</html>
