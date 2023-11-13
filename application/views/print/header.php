<html>
    <link rel="stylesheet" href="<?php echo base_url("bootstrap/css/bootstrap.min.css") ?>">
    <link rel="stylesheet" href="<?php echo base_url("dist/fa/css/font-awesome.min.css") ?>">
    <link href="https://fonts.cdnfonts.com/css/code-128" rel="stylesheet">
    <style>
        .text-rotate {
            transform-origin: 0 0;
            transform: rotate(90deg);
        }
        .text-barcode {
            font-family: "Code 128", sans-serif;
            font-size: 22px;
        }
        .list-data{
            text-align: right;
        }
        s
        .list-data-item {
            display: inline;
        }
        .info-barcode {
            padding: 2px;
        }
        p{
            margin: 0;
        }
        @media print
        {
            /*            @page{
                            size: 2.36in 3in;
            
                        }*/
            body {
                font-size: 8pt;
                margin-top: 5px;
                margin-left: 5%;
            }
            .wrp {
                word-wrap: break-word;
                text-align: right;
                margin-top: 10%;
            }
            .text-rotate {
                transform-origin: 0 0;
                transform: rotate(90deg);
            }
            .text-rotate-e {
                font-size: 7px;
                position: fixed;
                top: 50%;
                font-weight: bold;
                transform-origin: 0 0;
                transform: rotate(90deg);
            }

            .img-responsive{
                padding-bottom: 1px;
            }
            .img-barcode{
                width: 100%;
                margin-top: 50%;
            }
            hr {
                margin: 0;
                height:2px;
                text-align:left;
                margin-left:0
            }
            .text-barcode {
                font-family: "Code 128", sans-serif;
                font-size: 20px;
                line-height: 1px;
                letter-spacing: 2px;
            }
            .barcode-align {
                text-align: center;
            }
            .list-data-item {
                float: right;
                font-size: 7pt;
            }
            .list-data{
                text-align: right;
                font-size: 10px;
            }
            .list-data hr {
                border-style: inset;
                border-width: 1px;
            }
            p{
                margin: 0;
            }
            .info-barcode {
                padding: 2px;
            }
            .image-rotasi {
                position: fixed;
                top: 37%;
                left: 55%;
                width: 35%;
                -webkit-transform: rotate(90deg);
                -moz-transform: rotate(90deg);
                -ms-transform: rotate(90deg);
                -o-transform: rotate(90deg);
                transform: rotate(90deg);
            }
            .image-rotasi-e {
                position: fixed;
                top: 37%;
                left: 25%;
                width: 35%;
                -webkit-transform: rotate(90deg);
                -moz-transform: rotate(90deg);
                -ms-transform: rotate(90deg);
                -o-transform: rotate(90deg);
                transform: rotate(90deg);
            }
            .container1 {
                display: flex;
                justify-content: space-evenly;
                margin-left: 10px;
                width: 100vh;
            }

            .container1::before, .container1::after {
                width: 30px;
                height: 30px;
            }
            .child{
                font-size: 6px;
                padding: 4px;
                margin-top:13%;
            }
        }

        .container1 {
            display: flex;
            justify-content: space-between;
            width: 200px;
        }

        .container1::before, .container1::after {
            width: 100%;
        }
    </style>