<?php
$this->load->view('print/header');
?>
<body style="padding-top: 20px;">
    <div class="container">
        <div class="row">
            <div class="col-xs-4 col-md-4" style="margin-top: 3%;">
                <div class="row">
                    <!--<img class="img-responsive center-block" src="<?= $image["logo"]["path"] ?? "" ?>">-->
                    <img class="img-responsive center-block" src="data:image/jpg;base64,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">
                </div>
                <div class="row">
                    <img class="img-responsive center-block" src="data:image/jpg;base64,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">
                </div>
                <div class="row">
                    <div style="font-size: 6px;text-align: center;">
                        <p>No. Registrasi K3L :</p>
                        <span><?= $data["k3l"] ?? "" ?></span>
                    </div>
                </div>
            </div>
            <div class="col-xs-4 col-md-4">
                <div class="wrp">
                    <div class="list-data">
                        <p>Pattern</p>
                        <p><?= $data["pattern"] ?? "" ?></p>
                        <hr>
                    </div>
                    <div class="list-data">
                        <p>Color</p>
                        <p><?= $data["isi_color"] ?? "" ?></p>
                        <hr>
                    </div>
                    <div class="list-data">
                        <p><?= $data["isi_satuan_lebar"] ?? "" ?></p>
                        <p><?= $data["isi_lebar"] ?? "" ?></p>
                        <hr>
                    </div>
                    <div class="list-data">
                        <p><?= $data["isi_satuan_qty1"] ?? "" ?></p>
                        <p><?= $data["isi_qty1"] ?></p>
                        <hr>
                    </div>
                    <div class="list-data">
                        <p><?= $data["isi_satuan_qty2"] ?? "" ?></p>
                        <p><?= $data["isi_qty2"] ?? "" ?></p>
                        <hr>
                    </div>
                </div>
            </div>
            <div class="col-xs-2 col-md-2">
                <img class="img-responsive center-block img-barcode" src="data:image/png;base64,<?= $data["barcode"] ?? "" ?>">
            </div>
            <div class="col-xs-2 col-md-2">
                <div class="container1 text-rotate">
                    <div class="child"><?= $data["barcode_id"] ?? "" ?></div>
                    <div class="child"><?= $data["tanggal_buat"] ?? "" ?></div>
                    <div class="child"><?= $data["no_pack_brc"] ?? "" ?></div>
                </div>
            </div>
        </div>
</body>
<?php
$this->load->view('print/footer');
?>