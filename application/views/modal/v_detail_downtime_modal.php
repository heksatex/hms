
<form class="form-horizontal" id="edit" name="edit_parent">
    <div class="col-md-6">
        <div class="form-group">
            <div class="col-md-12 col-xs-12">
                <div class="col-xs-4"><label>Nama Mesin</label></div>
                <div class="col-xs-8">
                    <input type="text" name="nama" id="nama" class="form-control input-sm"  value="<?php echo $nama_mesin;?>" readonly>
                </div>  
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="col-md-12 table-responsive">
                <table class="table table-condesed table-hover rlstable" width="100%" id ="table_child">
                    <head>
                        <tr>
                            <th class="style no">No.</th>
                            <th class="style">Waktu</th>
                            <th class="style">Status</th>
                        </tr>
                    </head>
                    <tbody>
                    <?php
                        $empty = TRUE;
                        foreach ($list as $row) {
                            $empty = FALSE;
                            if($row->state == '1'){
                                $color  = "";
                                $status = 'UP';
                            }else{
                                $status = 'DOWN';
                                $color  = "color:red";
                            }
                    ?>
                        <tr class="num" style="<?php echo $color;?>">
                            <td></td>
                            <td><?php echo date("d-F-Y H:i:s", strtotime($row->timelog));?></td>
                            <td><?php echo $status;?></td>
                        </tr>
                    <?php 
                        if($empty == TRUE){
                    ?>
                        <tr>
                            <td colspan="3" align="center">Tidak Ada Data</td>
                        </tr>
                    <?php
                        }
                        }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="form-group">
    </div>
</form>