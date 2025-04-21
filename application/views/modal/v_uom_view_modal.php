<form class="form-horizontal" id="edit_parent" name="edit_parent">
    <div class="col-md-6">
        <div class="form-group">
            <div class="col-md-12 col-xs-12">
                <div class="col-xs-4"><label>Nama</label></div>
                <div class="col-xs-8">
                    <input type="text" name="nama" id="nama" class="form-control input-sm" value="<?php echo $data['nama'] ?>">
                </div>
            </div>
            <div class="col-md-12 col-xs-12">
                <div class="col-xs-4"><label>Short</label></div>
                <div class="col-xs-8">
                    <input type="text" name="short" id="short" class="form-control input-sm" value="<?php echo $data['short'] ?>">
                </div>
            </div>
            <div class="col-md-12 col-xs-12">
                <div class="col-xs-4"><label>Jenis</label></div>
                <div class="col-xs-8">
                    <select class="form-control input-sm" name="jenis" id="jenis">
                        <option value=''></option>
                        <?php
                        $arr_jenis = array('panjang', 'berat', 'waktu', 'unit');
                        $i = 0;
                        foreach ($arr_jenis as $val1) {

                            if ($val1 == $data['jenis']) {
                                echo '<option selected>' . $val1 . '</option>';
                            } else {
                                echo '<option>' .  $val1 . '</option>';
                            }
                            $i++;
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div class="col-md-12 col-xs-12">
                <div class="col-xs-4"><label for="">Jual</label></div>
                <div class="col-xs-8">
                    <?php $arr_n = array('yes', 'no'); ?>
                    <select class="form-control input-sm" name="jual" id="jual">
                        <option value=''></option>
                        <?php
                        foreach ($arr_n as $val2) {
                            if ($val2 == $data['jual']) {
                                echo '<option selected>' . $val2 . '</option>';
                            } else {
                                echo '<option >' . $val2 . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div class="col-md-12 col-xs-12">
                <div class="col-xs-4"><label for="">Beli</label></div>
                <div class="col-xs-8">
                    <?php $arr_n2 = array('yes', 'no'); ?>
                    <select class="form-control input-sm" name="beli" id="beli">
                        <option value=''></option>
                        <?php
                        foreach ($arr_n2 as $val3) {
                            if ($val3 == $data['beli']) {
                                echo '<option selected>' . $val3 . '</option>';
                            } else {
                                echo '<option >' . $val3 . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>
            </div>
        </div>
        <div class="form-group">

        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <!-- Custom Tabs -->
        </div>
        <div class="form-group">
        </div>
        <footer class="main-footer" style="margin-left: 0px !important;">
            <div id="foot">
                <?php
                $data['kode'] =  $data['id'];
                $data['mms']  =  $mms->kode;
                $this->load->view("admin/_partials/footer.php", $data);
                ?>
            </div>
        </footer>
    </div>
</form>