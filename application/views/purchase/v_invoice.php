<!DOCTYPE html>
<html lang="en">
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
                                <table id="tbl-inv" class="table">
                                    <thead>
                                        <tr>
                                           <th class="style">#</th>
                                           <th>Supplier</th>
                                           <th>Invoice</th>
                                           <th>TGL Inv</th>
                                           <th>SJ Supplier</th>
                                           <th>No PO</th>
                                           <th>Order Date</th>
                                           <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
        <?php $this->load->view("admin/_partials/js.php") ?>
        <script>
        $(function(){
            const table = $('#tbl-inv').DataTable({
                "iDisplayLength": 50,
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
                        "url": "<?php echo site_url('purchase/invoice/data') ?>",
                        "type": "POST"
                    },
                "columnDefs": [
                        {
                            "targets": [0,7],
                            "orderable": false
                        }
                    ]
                    
            });
        })
        </script>
    </body>
</html>