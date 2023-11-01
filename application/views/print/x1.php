<?php
$this->load->view('print/header');
?>
<body style="padding-top: 20px;">
    <div class="container">
        <div class="row">
            <div class="col-xs-4 col-md-4" style="margin-top: 3%;">
                <div class="row">
                    <div style="font-size: 8px; font-weight: bold;text-align: center; margin-top: 60%;">
                        <span>SORTED QUALITY</span>
                    </div>
                    <div style="font-size: 6px;text-align: center; margin-top: 75%;">
                        <span><?= $data["k3l"] ?? "" ?>asas</span>
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
                    <div class="child"><?= $data["no_pack_brc"] ?? "" ?></div>
                </div>
            </div>
        </div>
</body>
<?php
$this->load->view('print/footer');
?>