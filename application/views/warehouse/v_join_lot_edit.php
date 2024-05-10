<!DOCTYPE html>
<html>
<head>
  <?php $this->load->view("admin/_partials/head.php") ?>
  <!-- <link rel="stylesheet" type="text/css" href="<?php echo base_url('dist/css/tableScroll.css') ?>"> -->
</head>

<body class="hold-transition skin-black fixed sidebar-mini" onload="$('#barcode_id').focus()">  
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
         $data['jen_status'] = $join->status;
         $this->load->view("admin/_partials/statusbar.php", $data); 
       ?>
      </div>
    </section>

    <!-- Main content -->
    <section class="content">

      <!--  box content -->
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title">Form Edit Join Lot : <b><?php echo $join->kode_join; ?></b></h3>
        </div>

        <div class="box-body">
            <form class="form-horizontal">
             
                <div class="form-group">

                    <div class="col-md-6">
                        <div class="col-md-12 col-xs-12">
                            <div class="col-xs-4"><label>Kode  </label></div>
                            <div class="col-xs-8">
                                <input type="text" class="form-control input-sm" name="kode" id="kode"  readonly="readonly" value="<?php echo $join->kode_join;?>">                    
                            </div>                                    
                        </div>
                        <div class="col-md-12 col-xs-12">
                            <div class="col-xs-4"><label>Tanggal dibuat</label></div>
                            <div class="col-xs-8 col-md-8">
                                <input type='text' class="form-control input-sm " name="tgl_buat" id="tgl_buat" readonly="readonly" value="<?php echo $join->tanggal_buat; ?>"  />
                            </div>                                    
                        </div>
                        
                        <div class="col-md-12 col-xs-12">
                            <div class="col-xs-4"><label>Tanggal transaksi</label></div>
                            <div class="col-xs-8 col-md-8">
                                <input type='text' class="form-control input-sm " name="tgl_buat" id="tgl_buat" readonly="readonly" value="<?php echo $join->tanggal_transaksi; ?>"  />
                            </div>                                    
                        </div>

                        <div class="col-md-12 col-xs-12" >
                            <div class="col-xs-4"><label>Departemen</label></div>
                            <div class="col-xs-8">
                                <input type='text' class="form-control input-sm " name="departemen" id="departemen" readonly="readonly" value="<?php echo $join->departemen; ?>"  />
                            </div>                                    
                        </div>
                        <div class="col-md-12 col-xs-12" style="padding-bottom:10px;">
                            <div class="col-xs-4"><label>Note </label></div>
                            <div id="ta" class="col-xs-8">
                                <textarea class="form-control input-sm" name="note" id="note" ><?php echo htmlentities($join->note);?></textarea>
                            </div>                                    
                        </div>
                        <div class="col-md-12 col-xs-12">
                            <div class="col-xs-4"><label>Tanda Join</label></div>
                            <div class="col-xs-8">
                                <input type="checkbox" name="tanda_join" id="tanda_join" <?php echo $join->tanda_join == 'true'? 'checked' : '' ?> value="true">
                            </div>                                    
                        </div>
                    </div>

                    <div class="col-md-6" id="scan_lot" >
                        <?php if($join->status == 'draft'){ ?>
                        <div class="form-group">
                            <div class="col-md-12 col-xs-12">
                                <div class="col-xs-12 col-md-12 col-sm-12">
                                    <input type="text" class="form-control input-lg" name="barcode_id" id="barcode_id"  placeholder="Scan Barcode / Lot">
                                </div>                                    
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-12 col-xs-12">
                                <div class="col-xs-12 col-md-12 col-sm-12"> 
                                    <button  type="button" class="btn btn-sm btn-primary" name="btn-scan" id="btn-scan" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing..."><i class="fa fa-barcode"></i> Scan </button>
                                    <button  type="button" class="btn btn-primary btn-sm" id="btn-import-produk">Tampilkan Barang di Lokasi</button>       
                                </div>
                            </div>    
                        </div>  
                        <?php } ?>
                    </div>

                </div>
            
            </form>

            <div class="row">
              <div class="col-md-12">
                <!-- Custom Tabs -->
                <div class="">
                  <ul class="nav nav-tabs " >
                    <li class="active"><a href="#tab_1" data-toggle="tab">Join Items</a></li>
                  </ul>
                  <div class="tab-content "><br>
                    <div class="tab-pane active" id="tab_1">

                        <!-- Tabel  -->
                        <div class="col-md-12 table-responsive ">
                            <table class="table table-condesed table-hover rlstable " width="100%" id="table_items" style="border-bottom:0px !important">
                            <label>Lot yang akan Join</label>
                            <thead>                          
                                <tr>
                                    <th class="style no">No.</th>
                                    <th class="style" width="min-width:80px">Kode Produk</th>
                                    <th class="style" style="min-width:80px;" >Nama Produk</th>
                                    <th class="style" style="min-width:100px;" >Corak Remark</th>
                                    <th class="style" style="min-width:100px;" >Warna Remark</th>
                                    <th class="style" style="min-width:100px;" >Lot</th>
                                    <th class="style" style="min-width:50px;" >Grade</th>
                                    <th class="style text-right" style="min-width:80px;" >Qty</th>
                                    <th class="style text-right" style="min-width:80px;" >Qty2</th>
                                    <th class="style text-right" style="min-width:80px;" >Qty Jual</th>
                                    <th class="style text-right" style="min-width:80px;" >Qty2 Jual</th>
                                    <th class="style text-right" style="min-width:80px;" >Lbr.Jadi</th>
                                    <th class="style" style="min-width:50px;" >MKT</th>
                                    <th class="style text-center" width="50px">#</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    foreach ($join_items as $row) {
                                    ?>
                                        <tr class="num">
                                        <td data-content="edit" data-id="row_order" data-isi="<?php echo $row->row_order; ?>" data-isi2="<?php echo $row->quant_id?>"></td>
                                        <td><?php echo $row->kode_produk?></td>
                                        <td><?php echo $row->nama_produk?></td>
                                        <td><?php echo $row->corak_remark; ?></td>
                                        <td><?php echo $row->warna_remark; ?></td>
                                        <td><?php echo $row->lot; ?></td>
                                        <td><?php echo $row->grade; ?></td>
                                        <td align="right"><?php echo number_format($row->qty,2).' '.$row->uom;?></td>
                                        <td align="right"><?php echo number_format($row->qty2,2).' '.$row->uom2; ?></td>
                                        <td align="right"><?php echo number_format($row->qty_jual,2).' '.$row->uom_jual; ?></td>
                                        <td align="right"><?php echo number_format($row->qty2_jual,2).' '.$row->uom2_jual; ?></td>
                                        <td align="right"><?php echo $row->lebar_jadi.' '.$row->uom_lebar_jadi; ?></td>
                                        <td><?php echo $row->nama_sales_group; ?></td>
                                        <td class="width-50" align="center">
                                            <?php if($join->status == 'draft'){?>
                                                <a href="javascript:void(0)" class="delete" title="Hapus" data-toggle="tooltip" ><i class="fa fa-trash" style="color: red"></i></a>
                                            <?php }?>
                                        </td>
                                        </tr>
                                    <?php 
                                    }
                                ?>
                            
                            </tbody>
                            </table>
                        </div>
                        <!-- Tabel  -->
                        <div class="col-md-12 table-responsive ">
                            <table class="table table-condesed table-hover rlstable" width="100%" id ="table_result_join">
                                <label>Lot Hasil Join</label>
                                <thead>                          
                                    <tr>
                                        <th class="style no">No.</th>
                                        <th class="style" width="min-width:80px">Kode Produk</th>
                                        <th class="style" style="min-width:80px;" >Nama Produk</th>
                                        <th class="style" style="min-width:100px;" >Corak Remark</th>
                                        <th class="style" style="min-width:100px;" >Warna Remark</th>
                                        <th class="style" style="min-width:100px;" >Lot</th>
                                        <th class="style" style="min-width:50px;" >Grade</th>
                                        <th class="style text-right" style="min-width:80px;" >Qty</th>
                                        <th class="style text-right" style="min-width:80px;" >Qty2</th>
                                        <th class="style text-right" style="min-width:80px;" >Qty Jual</th>
                                        <th class="style text-right" style="min-width:80px;" >Qty2 Jual</th>
                                        <th class="style text-right" style="min-width:80px;" >Lbr.Jadi</th>
                                        <th class="style" style="min-width:50px;" >MKT</th>
                                        <th class="style text-center" width="50px">#</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                       if($join->status == 'done'){
                                        ?>
                                            <tr class="num">
                                                <td></td>
                                                <td><?php echo $join->kode_produk?></td>
                                                <td><?php echo $join->nama_produk?></td>
                                                <td><?php echo $join->corak_remark; ?></td>
                                                <td><?php echo $join->warna_remark; ?></td>
                                                <td><?php echo $join->lot; ?></td>
                                                <td><?php echo $join->grade; ?></td>
                                                <td align="right"><?php echo number_format($join->qty,2).' '.$join->uom;?></td>
                                                <td align="right"><?php echo number_format($join->qty2,2).' '.$join->uom2; ?></td>
                                                <td align="right"><?php echo number_format($join->qty_jual,2).' '.$join->uom_jual; ?></td>
                                                <td align="right"><?php echo number_format($join->qty2_jual,2).' '.$join->uom2_jual; ?></td>
                                                <td align="right"><?php echo $join->lebar_jadi.' '.$join->uom_lebar_jadi; ?></td>
                                                <td><?php echo $join->nama_sales_group; ?></td>
                                                <td class="width-50 " align="center" >  
                                                    <?php if($join->status == 'done'){?>
                                                        <a href="javascript:void(0)" class="print_barcode" title="Print Barcode" data-toggle="tooltip" data-status="<?php echo $join->status?>"><i class="fa fa-print" style="color: blue"></i></a>
                                                    <?php }?>
                                                </td>
                                            </tr>
                                        <?php 
                                        }
                                    ?>
                                
                                </tbody>
                            </table>   
                        </div>             

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
                $data['kode'] =  $join->kode_join;
                $data['mms']  =  $mms->kode;
                $this->load->view("admin/_partials/footer.php",$data) 
            ?>
        </div>
    </footer>

</div>

<?php $this->load->view("admin/_partials/js.php") ?>
<?php $this->load->view("admin/_partials/modal.php") ?>

<script type="text/javascript">
    

    $('#btn-import-produk').click(function(){
        var status = "<?php echo $join->status;?>";
        if(status == 'done'){
            alert_modal_warning('Status Join Lot sudah Done ! ');
        }else if(status == 'cancel'){
            alert_modal_warning('Status Join lot sudah Batal ! ');
        }else{
            var kode_join = "<?php echo $join->kode_join; ?>";
            var dept_id   = "<?php echo $join->dept_id;?>";
            $('#btn-import-produk').button('reset');
            $("#tambah_data").modal({
                show: true,
                backdrop: 'static'
            })
            $(".tambah_data").html('<center><h5><img src="<?php echo base_url('dist/img/ajax-loader.gif') ?> "/><br>Please Wait...</h5></center>');
            $('.modal-title').text('Pilih Produk Untuk Join Lot');
            $.post('<?php echo site_url()?>warehouse/joinlot/import_produk_join',
                {kode_join:kode_join,dept_id:dept_id},
                function(html){
                setTimeout(function() {$(".tambah_data").html(html); });
                }   
            );
        }
    });

    $('#barcode_id').keydown(function(event){
        if(event.keyCode == 13) {
           event.preventDefault();
           search_lot();
          //return false;
        }
    });

    // scan barcode
    $(document).on("click", "#btn-scan", function(){
        search_lot();
    });

    function refresh(){
        $("#tab_1").load(location.href + " #tab_1");
        $("#foot").load(location.href + " #foot");
        $("#status_bar").load(location.href + " #status_bar>*");
    }

    function search_lot(){
        let kode_join   = "<?php echo $join->kode_join;?>";
        let dept        = "<?php echo $join->dept_id; ?>";
        let lot         = $("#barcode_id").val();
        if(status == 'done'){
            alert_modal_warning('Status Join Lot sudah Done ! ');
        }else if(status == 'cancel'){
            alert_modal_warning('Status Join lot sudah Batal ! ');
        }else if(kode.length === 0){
            alert_notify("fa fa-warning","Kode Join Kosong !","danger",function(){});
        }else if(lot.length === 0){
            alert_notify("fa fa-warning","Barcode / Lot Harus diisi !","danger",function(){});
            $('#barcode_id').focus();
        }else{
            $.ajax({
                    type     : "POST",
                    dataType : "json",
                    url      : '<?php echo base_url('warehouse/joinlot/search_lot_join')?>',
                    beforeSend: function(e) {
                        if(e && e.overrideMimeType) {
                            e.overrideMimeType("application/json;charset=UTF-8");
                        }                  
                        $('#btn-scan').button('loading');
                        please_wait(function(){});
                    },
                    data: {kode_join:kode_join, lot:lot, dept:dept}, 
                    success: function(data){
                        unblockUI(function () {}, 100);
                        alert_notify(data.icon, data.message, data.type, function () {});
                        $('#barcode_id').val('');
                        $('#btn-scan').button('reset');
                        refresh();
                            
                    },error: function (xhr, ajaxOptions, thrownError) {
                        unblockUI(function() {});
                        $('#btn-scan').button('reset');
                        if(xhr.status == 401){
                            var err = JSON.parse(xhr.responseText);
                            alert(err.message);
                        }else{
                            alert("Error scan Data!")
                        }    
                    }
            });
        }
    }


    //delete row 
    $(document).on("click", ".delete", function(){ 
        $(this).parents("tr").find("td[data-content='edit']").each(function(){
            if($(this).attr('data-id')=="row_order"){
                $(this).html('<input type="hidden" class="form-control" value="' + ($(this).attr('data-isi')) + '" id="'+ $(this).attr('data-id') +'" data-quant="' + ($(this).attr('data-isi2')) + '"> ');
            }
        });

        var kode_join   =  "<?php echo $join->kode_join; ?>";
        var row_order   = $(this).parents("tr").find("#row_order").val();  
        var quant_id    = $(this).parents("tr").find("#row_order").attr('data-quant');  
            var this1       = $(this);

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
                            url : '<?php echo site_url('warehouse/joinlot/delete_join_lot_items') ?>',
                            beforeSend: function(e) {
                                if(e && e.overrideMimeType) {
                                    e.overrideMimeType("application/json;charset=UTF-8");
                                }                  
                                please_wait(function(){});
                            },
                            type: "POST",
                            data: {kode_join:kode_join, quant_id:quant_id, row_order:row_order},
                            success: function(data){
                              
                                this1.button('reset');
                                alert_notify(data.icon,data.message,data.type,function(){});
                                refresh();
                                unblockUI(function () {}, 100);
                            },
                            error: function (xhr, ajaxOptions, thrownError){
                                this1.button('reset');
                                unblockUI(function() {});
                                if(xhr.status == 401){
                                    var err = JSON.parse(xhr.responseText);
                                    alert(err.message);
                                }else{
                                    alert("Error Simpan Data!")
                                }   
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


    //btn-cancel 
    $(document).on("click", "#btn-cancel", function(e){ 
      
        e.preventDefault();
        let status_head = "<?php echo $join->status?>";

        if(status_head == 'cancel'){
            alert_modal_warning('Maaf, Data Tidak Bisa dibatalkan, Status Join Lot Sudah Batal !');
        }else if(status_head == 'done'){
            alert_modal_warning('Maaf, Data Tidak Bisa dibatalkan, Status Join Lot Sudah Done !');
        }else{

            let kode_join   =  "<?php echo $join->kode_join; ?>";

            bootbox.dialog({
                message: "Apakah Anda ingin membatalkan Data Join Lot ini ?",
                title: "<i class='glyphicon glyphicon-trash'></i> Batal Join Lot !",
                buttons: {
                    danger: {
                        label    : "Yes ",
                        className: "btn-primary btn-sm",
                        callback : function() {
                            $("#btn-cancel").button('loading');
                            $.ajax({
                                dataType: "JSON",
                                url : '<?php echo site_url('warehouse/joinlot/cancel_join_lot') ?>',
                                beforeSend: function(e) {
                                    if(e && e.overrideMimeType) {
                                        e.overrideMimeType("application/json;charset=UTF-8");
                                    }                  
                                    please_wait(function(){});
                                },
                                type: "POST",
                                data: {kode_join:kode_join},
                                success: function(data){
                                    $("#btn-cancel").button('reset');
                                    alert_notify(data.icon,data.message,data.type,function(){});
                                    refresh();
                                    unblockUI(function () {}, 100);
                                },
                                error: function (xhr, ajaxOptions, thrownError){
                                    $("#btn-cancel").button('reset');
                                    unblockUI(function() {});
                                    if(xhr.status == 401){
                                        var err = JSON.parse(xhr.responseText);
                                        alert(err.message);
                                    }else{
                                        alert("Error Simpan Data!")
                                    }   
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
        }
    });


    // simpan
    $(document).on("click", "#btn-simpan", function(){
            
        let kode = "<?php echo $join->kode_join;?>";
        let dept = "<?php echo $join->dept_id;?>";
        let note = $("#note").val();
        var status = "<?php echo $join->status;?>";
        let tanda = $("input:checked").val();
        if(tanda == 'true'){
            tanda_join = 'true';
        }else{
            tanda_join = 'false'
        }
        if(status == 'done'){
            alert_modal_warning('Status Join Lot sudah Done ! ');
        }else if(status == 'cancel'){
            alert_modal_warning('Status Join lot sudah Batal ! ');
        }else if(dept.length === 0){
            alert_notify("fa fa-warning","Departemen Kosong !","danger",function(){});
            $('#departemen').focus();
        }else if(note.length === 0){
            alert_notify("fa fa-warning","Note Harus diisi !","danger",function(){});
            $('#note').focus();
        }else{
            $.ajax({
                    type     : "POST",
                    dataType : "json",
                    url      : '<?php echo base_url('warehouse/joinlot/save_join_lot')?>',
                    beforeSend: function(e) {
                        if(e && e.overrideMimeType) {
                            e.overrideMimeType("application/json;charset=UTF-8");
                        }                  
                        $('#btn-simpan').button('loading');
                        please_wait(function(){});
                    },
                    data: {kode:kode, dept:dept, note:note, tanda_join:tanda_join}, 
                    success: function(data){
                       if(data.status == 'failed'){
                            unblockUI( function() {
                                setTimeout(function() { alert_notify(data.icon,data.message,data.type,function(){ 
                                        $('#btn-simpan').button('reset');
                                });},1000); 
                            });
                            refresh();
                        }else{
                            unblockUI(function() {
                                setTimeout(function() {
                                    alert_notify(data.icon, data.message, data.type,function() {
                                    }, 1000);
                                });
                            });
                            refresh();
                            $('#btn-simpan').button('reset');
                        }
                            
                    },error: function (xhr, ajaxOptions, thrownError) {
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


    //btn-generate 
    $(document).on("click", "#btn-generate", function(e){ 
      
        e.preventDefault();
        let status_head = "<?php echo $join->status?>";
        if(status_head == 'cancel'){
            alert_modal_warning('Maaf, Data Tidak Bisa di Generate, Status Join Lot Sudah Batal !');
        }else if(status_head == 'done'){
            alert_modal_warning('Maaf, Data Tidak Bisa di Generate, Status Join Lot Sudah Done !');
        }else{

            let kode_join   =  "<?php echo $join->kode_join; ?>";

            bootbox.dialog({
                message: "Apakah Anda ingin Generate Data Join Lot ini ?",
                title: "<i class='glyphicon glyphicon-trash'></i> Generate !",
                buttons: {
                    danger: {
                        label    : "Yes ",
                        className: "btn-primary btn-sm",
                        callback : function() {
                            $("#btn-generate").button('loading');
                            $.ajax({
                                dataType: "JSON",
                                url : '<?php echo site_url('warehouse/joinlot/generate_join_lot') ?>',
                                beforeSend: function(e) {
                                    if(e && e.overrideMimeType) {
                                        e.overrideMimeType("application/json;charset=UTF-8");
                                    }                  
                                    please_wait(function(){});
                                },
                                type: "POST",
                                data: {kode_join:kode_join},
                                success: function(data){
                                    $("#btn-generate").button('reset');
                                    alert_notify(data.icon,data.message,data.type,function(){});
                                    if(data.status == 'success'){
                                        $('#scan_lot').css('display','none');
                                    }
                                    refresh();
                                    unblockUI(function () {}, 100);

                                },
                                error: function (xhr, ajaxOptions, thrownError){
                                    $("#btn-generate").button('reset');
                                    unblockUI(function() {});
                                    if(xhr.status == 401){
                                        var err = JSON.parse(xhr.responseText);
                                        alert(err.message);
                                    }else{
                                        alert("Error Simpan Data!")
                                    }   
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
        }
    });


    //modal mode print
    $(document).on('click','.print_barcode',function(e){
        e.preventDefault();
        let status_head = $(this).attr('data-status');
        if(status_head == 'cancel'){
            alert_modal_warning('Maaf, Data Tidak Bisa di Print, Status Join Lot Sudah Batal !');
        }else if(status_head == 'draft'){
            alert_modal_warning('Maaf, Data Tidak Bisa di Print, Status Join Lot masih Draft !');
        }else{

            var kode = $('#kode').val();
            $(".print_data").html('<center><h5><img src="<?php echo base_url('dist/img/ajax-loader.gif') ?> "/><br>Please Wait...</h5></center>');
            $("#print_data").modal({
                show: true,
                backdrop: 'static'
            });
            $("#print_data .modal-dialog .modal-content .modal-footer #btn-print").remove();

            $('.modal-title').text('Pilih Desain Barcode dan K3L ');
            var  kode = '<?php echo $join->kode_join?>';
            $.post('<?php echo site_url()?>warehouse/joinlot/print_modal',
            { kode:kode},
                function(html){
                    setTimeout(function() {$(".print_data").html(html);  },1000);
                    $("#print_data .modal-dialog .modal-content .modal-footer").prepend('<button class="btn btn-default btn-sm" id="btn-print" name="btn-print" >Print</button>');

                }   
            );
        }
    });

   
</script>


</body>
</html>
