<html>
    <?php $this->load->view("print/header_hanger.php") ?>
    <body>
        <?php foreach ($data as $key) { 
            // var_dump($data);
            ?>
            <div class="flex-container flex-end-top" >
                <div class="row" style="padding-top:20px; padding-bottom:35px">
                    <div id="top">
                        <div class="row" >
                            <div class="col-xs-12">
                                <div id="isef_A_top">Corak :</div>
                            </div>
                        </div>
                    </div>                   
                    <div id="top" >
                        <div class="row">
                            <div class="col-xs-12">
                                <div id="isef_A_top"><?= $key->corak?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex-container flex-end">
                <div class="row"  style="padding-bottom:10px;  ">
                    <div id="bottom">
                        <div class="row">
                            <div class="col-xs-12">
                                <div id="isef_A_top">Corak :</div>
                                <div class="data-center">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="bottom" >
                        <div class="row">
                            <div class="col-xs-12">
                                <div id="isef_A_top"><?= $key->corak?></div>
                                <div class="data-center">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
             <div id="is1i"></div>
        <?php }
        ?>
    </body>
</html>

