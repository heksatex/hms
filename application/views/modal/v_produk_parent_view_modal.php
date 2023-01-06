<form class="form-horizontal" id="edit_parent" name="edit_parent">
    <div class="col-md-6">
        <div class="form-group">
            <div class="col-md-12 col-xs-12">
                <div class="col-xs-4"><label>Nama</label></div>
                <div class="col-xs-8">
                <input type="text" name="nama" id="nama" class="form-control input-sm"  value="<?php echo $data['nama']?>">
                </div>  
            </div>
        </div>
        <div class="form-group">
            <div class="col-md-12 col-xs-12">
                <div class="col-xs-4"><label>Status</label></div>
                <div class="col-xs-8">
                    <select class="form-control input-sm" name="status" id="status">
                    <?php 
                        $arr_status = array(array('value' => 't', 'text' => 'Aktif'), array( 'value'=> 'f', 'text' => 'Tidak Aktif'));
                        foreach ($arr_status as $val) {
                            if($val['value'] == $data['status_parent']){?>
                                <option value="<?php echo $val['value']; ?>" selected><?php echo $val['text'];?></option>
                            <?php
                            }else{?>
                                <option value="<?php echo $val['value']; ?>" ><?php echo $val['text'];?></option>
                            <?php  
                            }
                    }?>
                    </select>  
                </div>  
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-12">
                <!-- Custom Tabs -->
            <div class="">
                <ul class="nav nav-tabs">
                   <li class="active"><a href="#tab_1" data-toggle="tab">Childs</a></li>
                </ul>
                <div class="tab-content"><br>
                    <div class="tab-pane active" id="tab_1">
                        <div class="col-md-12 table-responsive">
                            <table class="table table-condesed table-hover rlstable" width="100%" id ="table_child">
                                <head>
                                    <tr>
                                    <th class="style no">No.</th>
                                    <th class="style">Kode Produk</th>
                                    <th class="style">Nama Produk</th>
                                    <th class="style">Tanggal dibuat</th>
                                    <th class="style">Uom1</th>
                                    <th class="style">Uom2</th>
                                    <th class="style">Kategori</th>
                                    <th class="style">Status Produk</th>
                                    </tr>
                                </head>
                                <tbody>
                                    <?php
                                    $empty = TRUE;
                                        foreach ($produk as $row) {
                                            $kode_encrypt = encrypt_url($row->id);
                                            $empty = FALSE;
                                    ?>
                                    <tr class="num">
                                        <td></td>
                                        <td><?php echo $row->kode_produk;?></td>
                                        <td><?php echo '<a href="'.base_url('warehouse/produk/edit/'.$kode_encrypt).'" target="_blank">'.$row->nama_produk.'</a>';?></td>
                                        <td><?php echo $row->create_date;?></td>
                                        <td><?php echo $row->uom;?></td>
                                        <td><?php echo $row->uom_2;?></td>
                                        <td><?php echo $row->nama_category;?></td>
                                        <td><?php echo $row->nama_status;?></td>
                                    </tr>
                                    <?php 
                                        }
                                        if($empty == TRUE){
                                            ?>
                                            <tr>
                                                <td colspan="8" align="center">Tidak Ada Data</td>
                                            </tr>
                                            <?php
                                        }
                                    ?>
                                </tbody>
                            </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="form-group">
    </div>
    <footer class="main-footer" style="margin-left: 0px !important;">
        <div id="foot">
         <?php 
            $data['kode'] =  $data['id'];
            $data['mms']  =  $mms->kode;
            $this->load->view("admin/_partials/footer.php",$data) 
         ?>
        </div>
    </footer>
</form>



<script>
    $("#btn-ubah2").off("click").on("click",function(e) {
        $('#btn-ubah2').button('loading');
        
        var nama   = $('#nama').val();
        var status = $('#status').val();
        var id     = "<?php echo $data['id']?>";

        please_wait(function(){});
        $.ajax({
            type: "POST",
            dataType: "json",
            url :'<?php echo base_url('warehouse/produkparent/simpan')?>',
            beforeSend: function(e) {
                if(e && e.overrideMimeType) {
                    e.overrideMimeType("application/json;charset=UTF-8");
                }
            },
            data: { id:id, nama:nama, status:status},
            success: function(data){
                if(data.sesi == "habis"){
                    //alert jika session habis
                    alert_modal_warning(data.message);
                    window.location.replace('index');
                }else if(data.status == "failed"){
                    //jika ada form belum keiisi
                    unblockUI( function() {
                        setTimeout(function() { alert_notify(data.icon,data.message,data.type,function(){}); }, 1000);
                    });
                document.getElementById(data.field).focus();
                }else{
                    //jika berhasil disimpan/diubah
                    unblockUI( function() {                
                        setTimeout(function() { alert_notify(data.icon,data.message,data.type,function(){}); }, 1000);
                    });
                    $('#edit_data2').modal('hide');
                }
                $('#btn-ubah2').button('reset');

            },error: function (xhr, ajaxOptions, thrownError) {
                alert(xhr.responseText);
                unblockUI( function(){});
                $('#btn-ubah2').button('reset');
            }
        });
    });


</script>