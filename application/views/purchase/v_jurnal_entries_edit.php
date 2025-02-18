<!DOCTYPE html>
<html lang="en">
    <head>
        <?php $this->load->view("admin/_partials/head.php") ?>
        <link href="<?= base_url('dist/css/popup_img.css') ?>" rel="stylesheet">
        <style>
            #btn-cancel{
                display: none;
            }

            <?php if ($jurnal->status === "posted" || $jurnal->status === "cancel") {
                ?>
                #btn-simpan{
                    display: none;
                }
                <?php
            }
            if ($jurnal->status === "cancel") {
                ?>
                .btn-sm{
                    display: none;
                }
                <?php
            }
            ?>
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
                <?php
                $this->load->view("admin/_partials/sidebar.php");
                $listJurnal = ["PB"=>"Pembelian"];
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
                            <h3 class="box-title"><strong> <?= $jurnal->kode ?? "" ?> </strong></h3>
                            <div class="pull-right text-right" id="btn-header">
                                <?php if ($jurnal->status === "unposted") { ?>
                                    <button class="btn btn-success btn-sm" id="btn-update-status" data-status="posted"  data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing...">
                                        <i class="fa fa-check">&nbsp;Posted</i>
                                    </button>
                                <?php } ?>
                            </div>
                        </div>
                        <form  class="form-horizontal" method="POST" name="form-jurnal" id="form-jurnal" action="<?= base_url('purchase/jurnalentries/update/' . $id) ?>">
                            <button type="submit" style="display: none;" id="form-jurnal-submit"></button>
                            <div class="box-body">
                                <div class="col-xs-12">
                                    <div class="col-md-6 col-xs-12">
                                        <div class="form-group">
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-4">
                                                    <label class="form-label">Jurnal</label>
                                                </div>
                                                <div class="col-xs-8 col-md-8 text-uppercase">
                                                    <span><?= $listJurnal[$jurnal->tipe] ?? "" ?></span>
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
                                    </div>
                                    <div class="col-md-6 col-xs-12">
                                        
                                        <div class="form-group">
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-4">
                                                    <label class="form-label">Origin</label>
                                                </div>
                                                <div class="col-xs-8 col-md-8 text-uppercase">
                                                    <span><?= $jurnal->origin?></span>
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
                                                                <th>Partner</th>
                                                                <th>Account</th>
                                                                <th>Debit</th>
                                                                <th>Credit</th>
                                                                <th>Kurs</th>
                                                                <th>#</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php
                                                            $totalDebit = 0;
                                                            $totalKredit = 0;
                                                            foreach ($detail as $key => $value) {
                                                                ?>
                                                                <tr>
                                                                    <td><?= $key + 1 ?></td>
                                                                    <td><?= $value->nama ?></td>
                                                                    <td><?= $value->reff_note ?></td>
                                                                    <td><?= $value->supplier ?></td>
                                                                    <td style="width: 15%">
                                                                        <?= $value->kode_coa . " " . $value->account ?>
                                                                        <!--                                                                        <div class="form-group">
                                                                                                                                                    <select class="form-control kode_coa input-xs kode_coa_data_<?= $key ?>" style="width: 70%" data-row="<?= $key ?>"
                                                                                                                                                            name="kode_coa[<?= $value->id ?>]"  required <?= ($jurnal->status === 'unposted') ? '' : 'disabled' ?>>
                                                                                                                                                        <option></option>
                                                                        <?php
                                                                        if (!is_null($value->kode_coa)) {
                                                                            ?>
                                                                                                                                                                        <option value="<?= $value->kode_coa ?>"selected><?= $value->account ?></option>   
                                                                            <?php
                                                                        }
                                                                        ?>
                                                                                                                                                    </select>
                                                                        
                                                                                                                                                </div>-->
                                                                    </td>
                                                                    <td><?php
                                                                        if (strtolower($value->posisi) === "d") {
                                                                            $totalDebit += $value->nominal;
                                                                            echo number_format($value->nominal, 2);
                                                                        }
                                                                        ?></td>
                                                                    <td><?php
                                                                        if (strtolower($value->posisi) === "c") {
                                                                            $totalKredit += $value->nominal;
                                                                            echo number_format($value->nominal, 2);
                                                                        }
                                                                        ?></td>
                                                                    <td><?= number_format($value->kurs, 2) ?></td>
                                                                    <td><?= $value->kode_mua ?></td>
                                                                </tr>
                                                                <?php
                                                            }
                                                            if (count($detail) > 0) {
                                                                ?>
                                                                <tr>
                                                                    <td></td>
                                                                </tr>
                                                                <tr>
                                                                    <td colspan="3" class="text-center"><strong>Balance</strong></td>
                                                                    <td></td>
                                                                    <td></td>
                                                                    <td><strong><?= number_format($totalDebit,2) ?></strong></td>
                                                                    <td><strong><?= number_format($totalKredit,2) ?></strong></td>
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
                        url: "<?= base_url("purchase/jurnalentries/getcoa"); ?>",
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
                const form = document.forms.namedItem("form-jurnal");
                form.addEventListener(
                        "submit",
                        (event) => {
                    please_wait(function () {});
                    request("form-jurnal").then(
                            response => {
                                unblockUI(function () {
                                    alert_notify(response.data.icon, response.data.message, response.data.type, function () {});
                                }, 100);
                                if (response.status === 200)
                                    location.reload();
                            }
                    ).catch(err => {
                        unblockUI(function () {});
                        alert_modal_warning("Hubungi Dept IT");
                    });
                    event.preventDefault();
                },
                        false
                        );

                $("#btn-simpan").off("click").unbind("click").on("click", function () {
                    confirmRequest("Jurnal Entries", "Update Jurnal ? ", function () {
                        $("#form-jurnal-submit").trigger("click");
                    });
                });
                $("#btn-cancel").off("click").unbind("click").on("click", function () {
                    confirmRequest("Jurnal Entries", "Cancel Jurnal ? ", function () {
                        updateStatus();
                    });
                });
                $("#btn-update-status").off("click").unbind("click").on("click", function () {
                    confirmRequest("Jurnal Entries", "Posted Jurnal ? ", function () {
                        updateStatus("posted");

                    });
                });

                const updateStatus = ((status = "cancel") => {
                    $.ajax({
                        url: "<?= base_url("purchase/jurnalentries/update_status"); ?>",
                        type: "POST",
                        data: {
                            ids: "<?= $id ?>",
                            status: status
                        },
                        beforeSend: function (xhr) {
                            please_wait(function () {});
                        }, success: function (data) {
                            location.reload();
                        },
                        error: function (req, error) {
                            unblockUI(function () {
                                setTimeout(function () {
                                    alert_notify('fa fa-close', req?.responseJSON?.message, 'danger', function () {});
                                }, 500);
                            });
                        }
                    });
                });

            });
        </script>
    </body>
</html>