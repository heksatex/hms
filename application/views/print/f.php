<html>
    <?php $this->load->view("print/header.php") ?>
    <style>
        #noregk3l{
            height: 20vh;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        #noregk3l #iop3i{
            margin-top: auto;
            line-height: 0.5;
        }
        #idah{
            width:80%;
            height: 38%;
            margin-top: 10%;
        }
        #iex1{
            width:60%;
            margin-top: 0px;
            margin-left: 22%
        }
    </style>
    <body>
        <?php foreach ($data as $key => $data) { ?>
            <div class="container-fluid" id="#is1i">
                <div class="row">
                    <div class="col-xs-4">
                        <div class="row">
                            <div class="data-center">
                                <img id="idah" src="data:image/jpg;base64,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">
                               
                                <img id="iex1" src="data:image/png;base64,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">
                                <div id="noregk3l">
                                    <div id="iop3i">
                                        <div>
                                            <p>No Registrasi K3L</p> <span class="k3l"><?= $data["k3l"] ?? "" ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="col-xs-3" style="margin-left: -10px;">
                        <div class="row">
                            <ul class="flex-container row">
                                <li class="flex-item" style="width: 15px;">
                                    <div class="translate-rotate">
                                        <div class="data-center-rotate">
                                            <div class="text-rotate"><?= "" ?></div>
                                            <div class="text-rotate"><?= "" ?></div>
                                            <div class="text-rotate" style="margin-top: -15px; margin-right: -30px;"><?= $data["no_pack_brc"] ?? "" ?></div>
                                        </div>
                                    </div>
                                </li>
                                <li class="flex-item" style=" width: 80px;margin-top: 3px; margin-left: -12px;">
                                    <img id="ieh54" style="margin-top: 3px;" src="data:image/png;base64,<?= $data["barcode"] ?? "" ?>">
                                </li>
                                <li class="flex-item" style=" width: 20px;  margin-left: -18px;">
                                    <div class="translate-rotate">
                                        <div class="data-center-rotate">
                                            <div class="text-rotate"><?= $data["barcode_id"] ?? "" ?></div>
                                            <div class="text-rotate"><?= "" ?></div>
                                            <div class="text-rotate"><?= $data["tanggal_buat"] ?? "" ?></div>
                                        </div>

                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-xs-5">
                        <div class="row">
                            <div class="data-center data-text">
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
                </div>
            </div>
        <?php }
        ?>
    </body>
</html>                  