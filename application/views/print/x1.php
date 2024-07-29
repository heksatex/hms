<html>
    <?php $this->load->view("print/header.php") ?>
    <style>
        #noregk3l{
            height: 90vh;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        #noregk3l #iop3i{
            margin-top: auto;
            line-height: 0.5;
        }
    </style>
    <body>
        <?php foreach ($data as $key => $data) { ?>
            <div class="container-fluid" id="#is1i">
                <div class="row">
                    <div class="col-xs-3">
                        <div class="row">
                            <div class="data-center">
                                <div id="noregk3l">
                                    <div id="iop3i" style="font-size: 50%;font-weight: 800;line-height: 1;">SORTED QUALITY
                                    </div>
                                    <div id="iop3i">
                                        <div>
                                           <span class="k3l"><?= $data["k3l"] ?? "" ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="col-xs-6">
                        <div class="row">
                            <div class="data-center" style="padding-top: 10%;">
                                <div id="isef_p">Pattern
                                </div>
                                <div id="i75tl"><?= $data["pattern"] ?? "" ?>
                                </div>
                                <div id="isef_p">Color
                                </div>
                                <div id="i75tl"><?= $data["isi_color"] ?? "" ?>
                                </div>
                                <div id="isef"><?= $data["isi_satuan_lebar"] ?? "" ?>
                                </div>
                                <div id="i75tl"><?= $data["isi_lebar"] ?? "" ?>
                                </div>
                                <div id="isef"><?= $data["isi_satuan_qty1"] ?? "" ?>
                                </div>
                                <div id="<?= isset($data["isi_qty1"]) ? "i75tl" : "" ?>"><?= $data["isi_qty1"] ?? "" ?>
                                </div>
                                <div id="<?= isset($data["isi_satuan_qty2"]) ? "isef" : "" ?>"><?= $data["isi_satuan_qty2"] ?? "" ?>
                                </div>
                                <div id="<?= isset($data["isi_qty2"]) ? "i75tl" : "" ?>"><?= $data["isi_qty2"] ?? "" ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-3">
                        <div class="row">
                            <ul class="flex-container row">
                                <li class="flex-item" style=" width: 80px; margin-left: -10px;margin-top: 3px;">
                                    <img id="ieh54" style="margin-top: 3px;" src="data:image/png;base64,<?= $data["barcode"] ?? "" ?>">
                                </li>
                                <li class="flex-item" style=" width: 20px;  margin-left: -18px;">
                                    <div class="translate-rotate">
                                        <div class="data-center-rotate">
                                            <div class="text-rotate"><?= $data["barcode_id"] ?? "" ?></div>
                                            <div class="text-rotate"><?= $data["tanggal_buat"] ?? "" ?></div>
                                            <div class="text-rotate"><?= $data["no_pack_brc"] ?? "" ?></div>
                                        </div>

                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        <?php }
        ?>
    </body>
</html>


