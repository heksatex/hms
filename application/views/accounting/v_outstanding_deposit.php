<!DOCTYPE html>
<html>

<head>
    <?php $this->load->view("admin/_partials/head.php") ?>
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
            </section>
            <section class="content">
                <div class="box">
                    <div class="box-body">
                        <div class="col-xs-12 table-responsive">
                            <table id="tbl-list-outsanding" class="table table-striped">
                                <thead>
                                    <tr>
                                        <th class="no">No</th>
                                        <th>No Pelunasan</th>
                                        <th>Customer</th>
                                        <th>Tanggal</th>
                                        <th>Curr</th>
                                        <th>Kurs</th>
                                        <th>Total (Rp)</th>
                                        <th>Total (Valas)</th>
                                        <th width="80">Action</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
    <?php $this->load->view("admin/_partials/js.php") ?>
    <script>
        var tanggal = "";
        $(function() {
            const table = $("#tbl-list-outsanding").DataTable({
                iDisplayLength: 50,
                processing: true,
                serverSide: true,
                order: [],
                scrollX: true,
                scrollY: "calc(100vh - 250px)",
                paging: true,
                lengthChange: true,
                searching: true,
                ordering: true,
                info: true,
                autoWidth: false,
                stateSave: false,
                ajax: {
                    url: "<?php echo site_url('accounting/outstandingdeposit/list_data_deposit') ?>",
                    type: "POST",
                },
                columnDefs: [{
                        targets: [0],
                        orderable: false
                    },
                    {
                        targets: [5, 6, 7],
                        className: "text-right"
                    },
                    {
                        targets: [8], // kolom terakhir = action
                        orderable: false,
                        searchable: false,
                        className: "text-center",
                        render: function(data, type, row) {

                            let id = row[8]; // primary key
                            let noPelunasan = row[1]; // no pelunasan

                            return `
                                <button 
                                    class="btn btn-xs btn-warning btn-nonaktif"
                                    data-id="${id}"
                                    data-no="${noPelunasan}">
                                    <i class="fa fa-ban"></i>
                                </button>
                            `;
                        }
                    }
                ]

            });

            // event klik refund
            $('#tbl-list-outsanding').on('click', '.btn-nonaktif', function() {

                let id = $(this).data('id');
                let noPelunasan = $(this).data('no');

                bootbox.confirm({
                    message: `
                        No Pelunasan: <b>${noPelunasan}</b><br>
                        Deposit ini akan <b class="text-danger">DINONAKTIFKAN</b>.<br>
                        Data tidak akan digunakan lagi untuk pelunasan.<br><br>
                        Apakah Anda yakin?
                    `,
                    title: "<i class='fa fa-warning text-danger'></i> Konfirmasi Nonaktif Deposit",
                    buttons: {
                        confirm: {
                            label: 'Ya, Nonaktifkan',
                            className: 'btn-warning btn-sm'
                        },
                        cancel: {
                            label: 'Batal',
                            className: 'btn-default btn-sm'
                        }
                    },
                    callback: function(result) {
                        if (result) {
                            nonaktifDeposit(id, noPelunasan);
                        } else {
                            alert_notify(
                                'fa fa-info',
                                'Aksi dibatalkan',
                                'info',
                                function() {}
                            );
                        }
                    }
                });

            });



            function nonaktifDeposit(id, noPelunasan) {
                $.ajax({
                    url: "<?php echo site_url('accounting/outstandingdeposit/nonaktif') ?>",
                    type: "POST",
                    dataType: "json",
                    data: {
                        id: id,
                        no_pelunasan: noPelunasan
                    },
                    success: function(res) {
                        if (res.status) {
                            alert_notify(
                                'fa fa-check',
                                'Deposit berhasil dinonaktifkan',
                                'success',
                                function() {}
                            );
                            $('#tbl-list-outsanding').DataTable().ajax.reload(null, false);
                        } else {
                            alert_notify(
                                'fa fa-times',
                                res.message || 'Gagal menonaktifkan deposit',
                                'danger',
                                function() {}
                            );
                        }
                    },
                    error: function() {
                        alert_notify(
                            'fa fa-times',
                            'Terjadi kesalahan server',
                            'danger',
                            function() {}
                        );
                    }
                });
            }



        });
    </script>
</body>

</html>