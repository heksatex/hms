<!DOCTYPE html>
<html lang="en">
    <head>
        <?php $this->load->view("admin/_partials/head.php") ?>
        <link href="<?= base_url('dist/css/popup_img.css') ?>" rel="stylesheet">


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
                <?php
                $this->load->view("admin/_partials/sidebar.php");
                ?>
            </aside>
            <div class="content-wrapper">
                <section class="content-header" >
                    <div id ="status_bar">
                        <?php
                        $data['jen_status'] = $jurnal->status;
                        $this->load->view("admin/_partials/statusbar.php", $data);
                        ?>
                    </div>
                </section>
                <section class="content">
                    <div class="box">
                        <div class="box-header with-border">
                            <h3 class="box-title">Jurnal &nbsp;<strong> <?= $jurnal->kode ?? "" ?> </strong></h3>
                            <div class="pull-right text-right" id="btn-header">

                            </div>
                        </div>
                        <form  class="form-horizontal" method="POST" name="form-inv" id="form-inv" action="<?= base_url('purchase/jurnal/update/' . $id) ?>">
                            <div class="box-body">
                                <div class="col-xs-12">
                                    <div class="col-md-6 col-xs-12">
                                        <div class="form-group">
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-4">
                                                    <label class="form-label">Origin</label>
                                                </div>
                                                <div class="col-xs-8 col-md-8 text-uppercase">
                                                    <span><?= $jurnal->origin ?></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-4">
                                                    <label class="form-label">Tanggal Dibuat</label>
                                                </div>
                                                <div class="col-xs-8 col-md-8 text-uppercase">
                                                    <span><?= $jurnal->tanggal_dibuat ?></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-4">
                                                    <label class="form-label">Periode</label>
                                                </div>
                                                <div class="col-xs-8 col-md-8 text-uppercase">
                                                    <span><?= $jurnal->periode ?></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-xs-12">
                                        <div class="form-group">
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-4">
                                                    <label class="form-label">Tanggal Posting</label>
                                                </div>
                                                <div class="col-xs-8 col-md-8 text-uppercase">
                                                    <span><?= $jurnal->tanggal_posting ?></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-4">
                                                    <label class="form-label">Reff Note</label>
                                                </div>
                                                <div class="col-xs-8 col-md-8 text-uppercase">
                                                    <textarea class="form-control" id="reff_note" name="reff_note"><?= $jurnal->reff_note ?></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="box-footer">
                                <div class="row">
                                    <div class="colxs-12">
                                        <ul class="nav nav-tabs">
                                            <li class="active"><a href="#tab_1" data-toggle="tab">Item</a></li>
                                            <!--<li><a href="#tab_2" data-toggle="tab">RFQ & BID</a></li>-->
                                        </ul>
                                        <div class="tab-content"><br>
                                            <div class="tab-pane active" id="tab_1">
                                                <div class="table-responsive over">
                                                    <table id="tbl-jurnal" class="table">
                                                        <thead>
                                                            <tr>
                                                                <th class="no">#</th>
                                                                <th>Name</th>
                                                                <th>Reff Note</th>
                                                                <th>Supplier</th>
                                                                <th>Account</th>
                                                                <th>Debit</th>
                                                                <th>Credit</th>
                                                                <th>Curr</th>
                                                                <th>Kurs</th>
                                                                <th>#</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php
                                                            foreach ($detail as $key => $value) {
                                                                ?>
                                                                <tr>
                                                                    <td><?= $key + 1 ?></td>
                                                                    <td><?= $value->nama ?></td>
                                                                    <td><?= $value->reff_note ?></td>
                                                                    <td><?= $value->supplier ?></td>
                                                                    <td style="width: 15%">
                                                                        <div class="form-group">
                                                                                <select class="form-control kode_coa input-xs kode_coa_data_<?= $key ?>" style="width: 70%" data-row="<?= $key ?>"
                                                                                        name="kode_coa[<?= $value->id ?>]"  required <?= ($jurnal->status === 'draft') ? '' : 'disabled' ?>>
                                                                                    <option></option>
                                                                                    <?php
                                                                                    if (!is_null($value->kode_coa)) {
                                                                                        ?>
                                                                                        <option value="<?= $value->kode_coa ?>"selected><?= $value->account ?></option>   
                                                                                        <?php
                                                                                    }
                                                                                    ?>
                                                                                </select>
                                                                            
                                                                        </div>
                                                                    </td>
                                                                    <td><?= (strtolower($value->posisi) === "d") ? number_format($value->nominal, 2) : "" ?></td>
                                                                    <td><?= (strtolower($value->posisi) === "c") ? number_format($value->nominal, 2) : "" ?></td>
                                                                    <td><?= number_format($value->nominal_curr, 2) ?></td>
                                                                    <td><?= $value->kode_mua ?></td>
                                                                </tr>
                                                                <?php
                                                            }
                                                            ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </section>
            </div>
        </div>
        <footer class="main-footer">
            <?php
            $this->load->view("admin/_partials/footer.php");
            ?>
        </footer>
        <?php $this->load->view("admin/_partials/js.php") ?>
        <script>
            $(function () {
                //select 2 akun coa
                $('.kode_coa').select2({
                    allowClear: true,
                    placeholder: "PIlih Coa",
                    ajax: {
                        dataType: 'JSON',
                        type: "POST",
                        url: "<?php echo base_url(); ?>purchase/jurnal/getcoa",
                        delay: 250,
                        data: function (params) {
                            return{
                                search: params.term
                            };
                        },
                        processResults: function (data) {
                            var results = [];
                            $.each(data.data, function (index, item) {
                                results.push({
                                    id: item.kode_coa,
                                    text: item.kode_coa + " - " + item.nama
                                });
                            });
                            return {
                                results: results
                            };
                        },
                        error: function (xhr, ajaxOptions, thrownError) {
                            //alert('Error data');
                            //alert(xhr.responseText);
                        }
                    }
                });
            })
        </script>
    </body>
</html>