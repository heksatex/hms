<html>

    <style>
        * {
            box-sizing: border-box;
        }
        body {
            margin: 0;
        }
        .gjs-row{
            display:flex;
            justify-content:flex-start;
            align-items:stretch;
            flex-wrap:nowrap;
            padding:10px;
            border: solid;
        }
        .gjs-cell{
            min-height:75px;
            flex-grow:1;
            flex-basis:100%;
            padding:0.5px;
        }
        #idah{
            color:black;
        }
        #iex1{
            color:black;
        }
        #isef{
            padding:10px;
        }
        #ieh54{
            color:black;
        }
        #i75tl{
            padding:10px;
        }
        #i3a7r{
            padding:10px;
        }
        #i6wsc{
            padding:10px;
        }
        #igvhs{
            padding:10px;
        }
        #iop3i{
            padding:10px;
        }
        #idybl{
            padding:10px;
        }
        @media (max-width: 992px){
            #i0op{
                min-height:226.56px;
                height:226.56px;
            }
            #igct{
                text-align:left;
                flex-basis:34%;
                /*margin-right: 1%;*/
            }
            #idah{
                display:block;
                width:88%;
                margin:10% 0px 5% 10%;
            }
            #iex1{
                width:88%;
                margin:0px 0px 5% 10%;
            }
            #i2uo{
                flex-basis:65%;
                margin-right: 1%;
            }
            #isef{
                text-align:right;
                font-size:67%;
                padding:2px 10px 2px 10px;
                font-family:Arial, Helvetica, sans-serif;
                font-weight:600;
            }
            #iuua{
                flex-basis:30%;
            }
            #ieh54{
                width:50%;
                text-align:left;
                margin:1% 0px 0px 0px;
                height:93%;
            }
            #i75tl{
                text-align:right;
                font-size:64%;
                padding:2px 10px 2px 10px;
                font-family:Arial, Helvetica, sans-serif;
                font-weight:400;
                border-bottom: solid;
                border-width: thin;
            }
            #i3a7r{
                padding:0px 0px 0px 0px;
                margin: 7% 0% 0% 0%;
                font-size:60%;
                transform:rotateZ(270deg);
                display:inline;
                right:4.5%;
                /*top:15%;*/
                position:fixed;
            }
            #i6wsc{
                padding:0px 0px 0px 0px;
                margin: 25% 0% 0% 0%;
                font-size:60%;
                transform:rotateZ(270deg);
                display:inline;
                right:7.5%;
                /*top:43%;*/
                position:fixed;
            }
            #igvhs{
                padding:0px 0px 0px 0px;
                margin: 45% 0% 0% 0%;
                font-size:60%;
                transform:rotateZ(270deg);
                display:inline;
                right:5%;
                /*top:78%;*/
                position:fixed;
            }
            #iop3i{
                padding:0px 0px 0px 0px;
                font-size:45%;
                text-align:center;
            }
            #idybl{
                padding:4px 0px 0px 0px;
                font-size:65%;
                text-align:center;
            }
        }
        @media (max-width: 768px){
            .gjs-row{
                /*flex-wrap:wrap;*/
            }
        }

    </style>
    <body id="is1i">
        <div class="gjs-row" id="i0op">
            <div class="gjs-cell" id="igct"><img id="idah" src="data:image/png;base64,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">
                <div style="bottom: 10%;position: fixed; left: 7%;">
                    <div id="iop3i">No Registrasi K3L
                    </div>
                    <div id="idybl"><?= $data["k3l"] ?? "" ?>
                    </div>
                </div>
            </div>
            <div class="gjs-cell" id="i2uo">
                <div id="isef">Pattern
                </div>
                <div id="i75tl"><?= $data["pattern"] ?? "" ?>
                </div>
                <div id="isef">Color
                </div>
                <div id="i75tl"><?= $data["isi_color"] ?? "" ?>
                </div>
                <div id="isef"><?= $data["isi_satuan_lebar"] ?? "" ?>
                </div>
                <div id="i75tl"><?= $data["isi_lebar"] ?? "" ?>
                </div>
                <div id="isef"><?= $data["isi_satuan_qty1"] ?? "" ?>
                </div>
                <div id="i75tl"><?= $data["isi_qty1"] ?>
                </div>
                <div id="isef"><?= $data["isi_satuan_qty2"] ?? "" ?>
                </div>
                <div id="i75tl"><?= $data["isi_qty2"] ?? "" ?>
                </div>
            </div>
            <div class="gjs-cell" id="iuua">
                <img id="ieh54" src="data:image/png;base64,<?= $data["barcode"] ?? "" ?>">
                <div id="i3a7r"><?= $data["barcode_id"] ?? "" ?>
                </div>
                <div id="i6wsc"><?= $data["tanggal_buat"] ?? "" ?>
                </div>
                <div id="igvhs"><?= $data["no_pack_brc"] ?? "" ?>
                </div>
            </div>
        </div>
    </body>
</html>