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
        }
        .gjs-cell{
            min-height:75px;
            flex-grow:1;
            flex-basis:100%;
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
                flex-basis:30%;
            }
            #idah{
                display:block;
                width:80%;
                margin:10% 0px 10% 10%;
            }
            #iex1{
                width:80%;
                margin:0px 0px 10% 10%;
            }
            #i2uo{
                flex-basis:69%;
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
                font-size:45%;
                transform:rotateZ(270deg);
                display:inline-block;
                right:6.3%;
                top:15%;
                position:absolute;
            }
            #i6wsc{
                padding:0px 0px 0px 0px;
                font-size:45%;
                transform:rotateZ(270deg);
                display:inline-block;
                right:8%;
                top:43%;
                position:absolute;
            }
            #igvhs{
                padding:0px 0px 0px 0px;
                font-size:45%;
                transform:rotateZ(270deg);
                display:inline-block;
                right:8%;
                top:78%;
                position:absolute;
            }
            #iop3i{
                padding:0px 0px 0px 0px;
                font-size:45%;
                text-align:center;
            }
            #idybl{
                padding:4px 0px 0px 0px;
                font-size:50%;
                text-align:center;
            }
            hr{
                width: 100%;
                /*border: solid;*/
                /*padding: 0% 0% 0% 0%;*/
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
            <div class="gjs-cell" id="igct">
                <div style="margin-bottom: 100%; margin-top: 100%;">
                    <div id="iop3i" style="font-size: 40%;font-weight: 800;">MARKED ITEMS
                    </div>
                </div>
<!--                
-->                <div style="margin-top: 20%;">
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
                <div id="igvhs"><?= $data["no_pack_brc"] ?? "" ?>
                </div>
            </div>
        </div>
    </body>
</html>