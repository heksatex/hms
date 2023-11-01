<?php
$this->load->view('print/header');
?>
<body style="padding-top: 20px;">
    <div class="container">
        <div class="row">
            <div class="col-xs-4 col-md-4" style="margin-top: 3%;">
                <div class="row">
                    <img class="img-responsive center-block" src="data:image/jpg;base64,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"></div>
                <div class="row">
                    <div style="font-size: 6px;text-align: center; margin-top: 60%;">
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