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
        * {
            box-sizing: border-box;
        }
        body {
            margin: 0;
            font-family:Arial, Helvetica, sans-serif;

        }

        body {
            margin-left: 5px;
            margin-right: 15px;
            margin-top : 20px
            margin-bottom : 20px
        }

        #top {
            padding:0px 0px 0px 0px;
            /* padding-bottom:50px; */
            /* position: static; */
        }

        #bottom {
            top: 98%;
            padding:0px 0px 0px 0px;
            /* transform: rotateZ(3.142rad); */
            left: 0;
        }

        #bottom:last-of-type {
            margin-top: auto;
        }

        #isef_A_top{
            text-align:LEFT;
            font-size:100%;
            padding:2px 20px 2px 2px;
            font-family:Arial, Helvetica, sans-serif;
            font-weight:600;
            text-transform: uppercase;
        }

        #isef_B_top{
            text-align:right;
            font-size:100%;
            padding:2px 2px 2px 25px;
            font-family:Arial, Helvetica, sans-serif;
            font-weight:600;
            text-transform: uppercase;
            /* margin-right:-20px */
        }


        #isef_C_top{
            text-align:left;
            font-size:100%;
            padding:2px 0px 2px 0px;
            font-family:Arial, Helvetica, sans-serif;
            font-weight:600;
        }
       
        @media print {
            #is1i {
                /* page-break-after: always; */
                break-after:page
            }
        }

        .flex-container {
            /* padding: 0;
            margin: 0;
            list-style: none;
            float: left;
            width: 120px;
            padding: 10px;
            margin-top: 10px; */
            /* border: 1px solid silver; */
            height: 50%;

            padding-right: 15px;
            padding-left: 15px;
            margin-right: auto;
            margin-left: auto;
            
            -ms-box-orient: horizontal;
            display: -webkit-box;
            display: -moz-box;
            display: -ms-flexbox;
            display: -moz-flex;
            display: -webkit-flex;
            display: flex;
            
            -webkit-flex-flow: row wrap;
            flex-flow: row wrap;
        }

        .flex-end-top{
            transform: rotateZ(3.142rad);
              -webkit-align-content: flex-end; 
            align-content: flex-end; 
            padding-right : 20px;
        }

        .flex-end { 
            -webkit-align-content: flex-end; 
            align-content: flex-end; 
            padding-left : 20px;
        }

    </style>
    </head>
    <body>
        <?php foreach ($data as $key) { 
            foreach($key as $key1){
            // var_dump($data);
            ?>
            <div class="flex-container flex-end-top" >
                <div class="row" style="padding-top:20px; padding-bottom:35px">
                    <div id="top">
                        <div class="row" >
                            <div class="col-xs-2">
                                <div id="isef_A_top">Article</div>
                                <div class="data-center">
                                </div>
                            </div>
                            <div class="col-xs-1">
                                <div id="isef_B_top">:</div>
                                <div class="data-center " >
                                </div>
                            </div>
                            <div class="col-xs-8">
                                <div id="isef_C_top"><?= $key1['article']?></div>
                                <div class="data-left ">
                                </div>
                            </div>
                        </div>
                    </div>                   
                    <div id="top" >
                        <div class="row">
                            <div class="col-xs-2">
                                <div id="isef_A_top">Color</div>
                                <div class="data-center">
                                </div>
                            </div>
                            <div class="col-xs-1">
                                <div id="isef_B_top">:</div>
                                <div class="data-center " >
                                </div>
                            </div>
                            <div class="col-xs-8">
                                <div id="isef_C_top"><?= $key1['color']?></div>
                                <div class="data-left ">
                                </div>
                            </div>
                        </div>
                    </div>
                     <div id="top" >
                        <div class="row">
                            <div class="col-xs-2">
                                <div id="isef_A_top">Size</div>
                                <div class="data-center">
                                </div>
                            </div>
                            <div class="col-xs-1">
                                <div id="isef_B_top">:</div>
                                <div class="data-center " >
                                </div>
                            </div>
                            <div class="col-xs-8">
                                <div id="isef_C_top"><?= $key1['size']?></div>
                                <div class="data-left ">
                                </div>
                            </div>
                        </div>
                    </div>
               
                </div>
            </div>
            <div class="flex-container flex-end">
                <div class="row"  style="padding-bottom:10px;  ">
                    <div id="bottom">
                        <div class="row">
                            <div class="col-xs-2">
                                <div id="isef_A_top">Article</div>
                                <div class="data-center">
                                </div>
                            </div>
                            <div class="col-xs-1">
                                <div id="isef_B_top">:</div>
                                <div class="data-center " >
                                </div>
                            </div>
                            <div class="col-xs-8">
                                <div id="isef_C_top"><?= $key1['article']?></div>
                                <div class="data-left ">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="bottom" >
                        <div class="row">
                            <div class="col-xs-2">
                                <div id="isef_A_top">Color</div>
                                <div class="data-center">
                                </div>
                            </div>
                            <div class="col-xs-1">
                                <div id="isef_B_top">:</div>
                                <div class="data-center " >
                                </div>
                            </div>
                            <div class="col-xs-8">
                                <div id="isef_C_top"><?= $key1['color']?></div>
                                <div class="data-left ">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="bottom">
                        <div class="row">
                            <div class="col-xs-2">
                                <div id="isef_A_top">Size</div>
                                <div class="data-center">
                                </div>
                            </div>
                            <div class="col-xs-1">
                                <div id="isef_B_top">:</div>
                                <div class="data-center " >
                                </div>
                            </div>
                            <div class="col-xs-8">
                                <div id="isef_C_top"><?= $key1['size']?></div>
                                <div class="data-left ">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
             <div id="is1i"></div>
        <?php }
        }
        ?>
    </body>
</html>

