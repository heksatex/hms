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
    .select2-container--focus {
        border: 1px solid #66afe9;
    }

    select[readonly].select2-hidden-accessible+.select2-container {
        pointer-events: none;
        touch-action: none;
    }

    select[readonly].select2-hidden-accessible+.select2-container .select2-selection {
        background: #eee;
        box-shadow: none;
    }

    select[readonly].select2-hidden-accessible+.select2-container .select2-selection__arrow,
    select[readonly].select2-hidden-accessible+.select2-container .select2-selection__clear {
        display: none;
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

<body class="hold-transition skin-black fixed sidebar-mini sidebar-collapse">
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
                <?php $this->load->view("admin/_partials/statusbar.php") ?>
            </section>

            <!-- Main content -->
            <section class="content">

                <!--  box content -->
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Form Tambah </h3>
                    </div>

                    <div class="box-body">
                        <form class="form-horizontal">

                            <div class="form-group">

                                <div class="col-md-6">
                                    <div class="col-md-12 col-xs-12">
                                        <div class="col-xs-4"><label>Kode </label></div>
                                        <div class="col-xs-8">
                                            <input type="text" class="form-control input-sm" name="kode" id="kode" readonly="readonly" />
                                        </div>
                                    </div>
                                    <div class="col-md-12 col-xs-12">
                                        <div class="col-xs-4"><label>Tgl.dbuat </label></div>
                                        <div class="col-xs-8 col-md-8">
                                            <input type='text' class="form-control input-sm " name="tgl_buat" id="tgl_buat" readonly="readonly" value="<?php echo date('Y-m-d H:i:s')?>" />
                                        </div>
                                    </div>   
                                    <div class="col-md-12 col-xs-12">
                                        <div class="col-xs-4"><label>Tgl.transaksi </label></div>
                                        <div class="col-xs-8 col-md-8">
                                            <input type='text' class="form-control input-sm " name="tgl_transaksi" id="tgl_transaksi" readonly="readonly" value="<?php echo date('Y-m-d H:i:s')?>" />
                                        </div>
                                    </div>                                    
                                    <div class="col-md-12 col-xs-12">
                                        <div class="col-xs-4"><label>Marketing</label></div>
                                        <div class="col-xs-8 col-md-8">
                                            <select class="form-control input-sm select2" name="marketing" id="marketing" >
                                            <option value=""></option>
                                                <?php foreach ($sales_group as $row) {
                                                        echo "<option value='".$row->kode_sales_group."'>".$row->nama_sales_group."</option>";
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
                                            <?php foreach ($type as $row) {?>
                                                <option value='<?php echo $row->id; ?>'><?php echo $row->name_type;?></option>
                                            <?php  }?>
                                            </select> 
                                        </div>                                    
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="col-md-12 col-xs-12">
                                        <div class="col-xs-4"><label>Notes </label></div>
                                        <div class="col-xs-8">
                                            <textarea type="text" class="form-control input-sm" name="notes" id="notes"></textarea>
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
            <?php $this->load->view("admin/_partials/modal.php") ?>

            <?php $this->load->view("admin/_partials/footer.php") ?>
        </footer>

    </div>

    <?php $this->load->view("admin/_partials/js.php") ?>


    <script type="text/javascript">
    // // untuk focus after select2 close
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
   

    // btn simpan
    $(document).on("click", "#btn-simpan", function(e) {
        e.preventDefault();

        let kode = $('#kode').val();
        let marketing = $('#marketing').val();
        let type = $('#type').val();
        let notes = $('#notes').val();
        let acces = "<?php echo $access->status; ?>";

        if(acces == 0){
            alert_notify('fa fa-warning', 'PC ini tidak diizinkan membuat Barcode Manual  !', 'danger', function() {});
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
                    marketing: marketing,
                    type: type,
                    notes: notes,
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
                        unblockUI(function() {
                            setTimeout(function() {
                                alert_notify(data.icon, data.message, data.type,function() {
                                window.location.replace('edit/'+data.isi);
                            }, 1000);
                            });
                        });
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

    </script>

</body>

</html>