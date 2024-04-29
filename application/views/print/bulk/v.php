<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <link rel="shortcut icon"  href="<?php echo base_url('dist/img/favicon_heksa.ico') ?>">
        <!-- Bootstrap 3.3.6 -->
        <link rel="stylesheet" href="<?php echo base_url('bootstrap/css/bootstrap.min.css') ?>">
        <link rel="stylesheet" href="<?php echo base_url('dist/fa/css/font-awesome.min.css') ?>">
        <style>
            
            #noregk3l{
                display: flex;
                height: 100%;
                align-items: center;
                justify-content: center;
            }
            #iop3i{
                padding:0px 0px 0px 0%;
                font-size:1.2rem;
                display: inline-block;
                align-self: flex-end;
                padding-bottom: 10px;
            }
            #ieh54{
                width:100%;
                height:30%;
                padding: 5px 0px 5px 0px;
            }
            #iex1{
                display:block;
                width:60%;
                margin-left: -50px;
            }
            @media print {
                #is1i {
                    page-break-after: always;
                }
            }
        </style>
    </head>

    <body>
        <?php foreach ($data as $key => $data) { ?>
            <div class="container" id="#is1i">
                <div class="row">
                    <div class="col-xs-2">

                    </div>
                    <div class="col-xs-6">
                        <div class="row">
                            <div class="data-center">
                                <div id="noregk3l">

                                    <div id="iop3i">
                                        <div>
                                            <span style="
                                                  font-size: 3rem;
                                                  font-weight: 400;
                                                  padding: 5px 5px 5px 5px">BULK
                                            </span>
                                        </div>
                                        Packing List
                                        <span style="font-weight: 600;"><?= $data["pl"] ?? "" ?>
                                        </span>
                                        <div>
                                            <img id="ieh54" src="data:image/png;base64,<?= $data["barcode"] ?? "" ?>">
                                        </div>
                                        <div style="text-align: center;font-size: 180%;font-weight: 400;"><?= $data["barcode_id"] ?? "" ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-4">
                        <div class="row">
                            <div class="data-center">
                                <div id="noregk3l">
                                    <img id="iex1" src="data:image/png;base64,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">

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