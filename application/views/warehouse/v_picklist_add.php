<html>
    <head>
        <?php $this->load->view("admin/_partials/head.php") ?>
        
        <style>
            #btn-print{
                display: none;
            }
        </style>

    </head>
    <body class="hold-transition skin-black fixed sidebar-mini">
        <div class="wrapper">
            <header class="main-header">
                <?php $this->load->view("admin/_partials/main-menu.php") ?>
                <?php
                $data['deptid'] = $id_dept;
                $this->load->view("admin/_partials/topbar.php", $data)
                ?>
            </header>
            <aside class="main-sidebar">
                <?php $this->load->view("admin/_partials/sidebar.php") ?>
            </aside>
            <div class="content-wrapper">
                <section class="content-header">
                    <?php $this->load->view("admin/_partials/statusbar.php") ?>
                </section>
                <section class="content">
                    <div class="box">
                        <div class="box-header with-border">
                            <h3 class="box-title">Form Tambah</h3>

                        </div>
                        <div class="box-body">
                            <form class="form-horizontal" method="POST" name="form-picklist" id="form-picklist" action="<?= base_url('warehouse/picklist/save') ?>">
                                <div class="form-group">                  
                                    <div class="col-md-12" >
                                        <div id="alert"></div>
                                        <button type="submit" id="btn_form_simpan" style="display: none"></button>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-md-6">
                                        <!--                                        <div class="col-md-12 col-xs-12">
                                                                                    <div class="col-xs-4"><label>Tanggal</label></div>
                                                                                    <div class="col-xs-8 col-md-8">
                                                                                        <input type='text' class="form-control input-sm" name="tgl_buat" id="tgl_buat" value="<?php echo date('Y-m-d') ?>"  />
                                                                                    </div>                                    
                                                                                </div>-->
                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-xs-4"><label class="form-label required">Tipe Bulk</label></div>
                                            <div class="col-xs-8 col-md-8">
                                                <select class="form-control input-sm select2" name="bulk" required>
                                                    <option></option>
                                                    <?php
                                                    foreach ($bulk as $key => $value) {
                                                        echo '<option value="' . $value->id . '">' . $value->name . '</option>';
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-xs-4"><label class="form-label required">Marketing</label></div>
                                            <div class="col-xs-8 col-md-8">
                                                <select class="form-control input-sm select2" name="sales" required>
                                                    <option></option>
                                                    <?php
                                                    foreach ($sales as $key => $value) {
                                                        if ($this->session->userdata('nama')['sales_group'] === $value->kode) {
                                                            echo '<option value="' . $value->kode . '" selected>' . $value->nama . '</option>';
                                                        } else {
                                                            echo '<option value="' . $value->kode . '">' . $value->nama . '</option>';
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-xs-4"><label class="form-label required">Jenis Jual</label></div>
                                            <div class="col-xs-8 col-md-8">
                                                <select class="form-control input-sm select2" name="jenis_jual" required>
                                                    <option></option>
                                                    <option value="export">EXPORT</option>
                                                    <option value="lokal">LOKAL</option>
                                                    <option value="lain-lain">Lain-Lain</option>
                                                </select>
                                            </div>                                    
                                        </div>
                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-xs-4"><label class="form-label" >Keterangan</label></div>
                                            <div class="col-xs-8 col-md-8">
                                                <textarea type="text" class="form-control input-sm resize-ta" id="ket" name="ket"></textarea>
                                            </div>                                    
                                        </div>
                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-xs-4"><label class="form-label" >SC</label></div>
                                            <div class="col-xs-8 col-md-8">
                                                <textarea type="text" class="form-control input-sm resize-ta" id="sc" name="sc"></textarea>
                                            </div>                                    
                                        </div>
                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-xs-4"><label class="form-label required">Customer</label></div>
                                            <div class="col-xs-8 col-md-8">
                                                <select class="form-control input-sm" name="customer" id="customer" required>

                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-xs-4"><label></label></div>
                                            <div class="col-xs-8 col-md-8">
                                                <textarea type="text" class="form-control input-sm resize-ta" name="alamat" id="alamat" readonly></textarea>
                                            </div>                                    
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </section>

            </div>
            <footer class="main-footer">
                <?php $this->load->view("admin/_partials/modal.php") ?>
                <?php $this->load->view("admin/_partials/footer.php") ?>
            </footer>

        </div>

        <?php $this->load->view("admin/_partials/js.php") ?>
        <script>
            $('.select2').select2({
                allowClear: true,
                placeholder: 'Pilih'
            });

            $("#customer").select2({
                allowClear: true,
                placeholder: 'Pilih',
                ajax: {
                    url: "<?= base_url('warehouse/picklist/get_cust') ?>",
                    delay: 250,
                    type: "POST",
                    data: function (params) {
                        var query = {
                            search: params.term
                        };

                        return query;
                    },
                    processResults: function (data) {
                        return {
                            results: $.map(JSON.parse(data), function (obj) {
                                return {
                                    id: obj.id,
                                    text: obj.text,
                                    address: obj.alamat
                                };
                            })
                        };
                    }
                }
            });

            $("#customer").on('select2:select', function (e) {
                $("#alamat").val($("#customer :selected").data().data.address);
            });
            $("#customer").on('select2:unselect', function (e) {
                $("#alamat").val("");
            });
            var datenow = new Date();
            datenow.setMonth(datenow.getMonth());
            $("#tgl_buat").datetimepicker({
                defaultDate: datenow,
                format: 'YYYY-MM-DD',
                ignoreReadonly: true
            });


            $("#btn-simpan").on('click', function () {
                $(this).off('click');
                $("#btn_form_simpan").trigger("click");
            });
            const formpicklist = document.forms.namedItem("form-picklist");

            formpicklist.addEventListener(
                    "submit",
                    (event) => {
                please_wait(function () {});
                request("form-picklist").then(
                        response => {
                            if (response.status === 200)
                                window.location.replace('<?php echo base_url('warehouse/picklist/edit/') ?>' + response.data.data);


                        }).catch(err => {
                    unblockUI(function () {});
                    alert_modal_warning("Hubungi Dept IT");
                });
                event.preventDefault();
            },
                    false
                    );

        </script>
    </body>
</html>