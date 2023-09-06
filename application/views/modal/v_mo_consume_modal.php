<style>
    .box-title2{
        display:inline-block;
        font-size : 15px;
        margin:0;
        line-height:1;
        font-weight :600;
    }
    .info-box2{
        display: block;
        min-height: 90px;
        background: #fff;
        width: 100%;
        box-shadow: 0 15px 15px rgba(32, 21, 21, 0.18);
        border-radius: 2px;
        margin-bottom: 15px;
    }
</style>

<?php 
    if($sisa_qty < 0){
        $error_target = 'error_target';
    }else{
        $error_target = '';
    }
?>

<form class="form-horizontal" id="form_consume" name="form_consume">
	<div class="form-group">
		<div class="col-md-12 col-xs-12">
	            <div class="col-xs-12"><label  style="font-size: 15px; color: #5F9EA0">Hasil Produksi</label></div>
		</div>
	</div>
	<div class="col-md-6">
		<div class="form-group">
				<div class="col-md-12 col-xs-12">
					<div class="col-xs-4"><label>Target Qty</label></div>
					<div class="col-xs-8">
						<input type="text" name="txtQty" id="txtQty" class="form-control input-lg <?php echo $error_target; ?>" value="<?php echo $sisa_qty.' '.$uom_qty_sisa ?>" readonly="readonly" />
						<input type="hidden" name="qty_sisa" id="qty_sisa" class="form-control input-sm" value="<?php echo $sisa_qty?>" readonly="readonly" />
						<input type="hidden" name="txtkode" id="txtkode" class="form-control input-sm" value="<?php echo $kode ?>"  />		
						<input type="hidden" name="txtkode_produk" id="txtkode_produk" class="form-control input-sm" value="<?php echo $kode_produk ?>"  />		
						<input type="hidden" name="qty_prod" id="qty_prod" class="form-control input-sm" value="<?php echo $qty_prod ?>" />
					</div>  
				</div>
		</div>
		
		<div class="form-group">
			<div class="col-md-12 col-xs-12">
				<div class="col-md-12">
					<span id="ket_waste" style="font-size:12px" ></span>
				</div>
			</div>
		</div>
	</div>
    
	<div class="col-md-6">
        
	</div>

    <div class="form-group">
        <div class="col-md-8">
          <div class="box ">
            <div class="box-header with-border">
              <h6 class="box-title2">Total Barang jadi yang BELUM dan SUDAH memiliki Konsumsi</h6>
              
            </div>
            <div class="box-body">

            <div class="col-md-6 col-sm-6 col-xs-12">
                <div class="info-box2">
                    <span class="info-box-icon bg-yellow"><i class="fa fa-cube"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Belum</span>
                        <span class="info-box-number"><?php echo number_format($fg_no->mtr,2);?>  <small>Mtr</small></span>
                        <span class="info-box-number"><?php echo number_format($fg_no->kg,2);?>  <small>Kg</small></span>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-sm-6 col-xs-12">
                <div class="info-box2">
                    <span class="info-box-icon bg-blue"><i class="fa fa-cube"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Sudah</span>
                        <span class="info-box-number"><?php echo number_format($fg_yes->mtr,2);?> <small>Mtr</small></span>
                        <span class="info-box-number"><?php echo number_format($fg_yes->kg,2);?>  <small>Kg</small></span>
                    </div>
                </div>
            </div>
             
            </div>
          </div>
        </div>
    </div>
 


	<div class="row">
        <div class="col-md-12">
            <!-- Custom Tabs -->           
            <ul class="nav nav-tabs">
                <li class="active"><a href="#tab_1_modal" data-toggle="tab">Barang Jadi</a></li>
            </ul>
            <div class="tab-content"><br>
                <div class="tab-pane active" id="tab_1_modal">
                    <div class="col-md-6 col-xs-12">
                		<div class="form-group">
                			<div class="col-md-12">
	                			<label>List Lot/KP yang belum memiliki konsumsi</label>
                			</div>
                		</div>
                	</div>
                	<div class="col-xs-12 table-responsive">
                        <table class="table table-condesed table-hover table-responsive rlstable" id="tbl_produksi_waste"> 
                            <thead>
                                <tr>
                                    <th class="style no">No.</th>
                                    <th class="style" >Product</th>
                                    <th class="style" style="width: 180px;">Lot</th>
                                    <th class="style" style="width: 100px;">Qty</th>
                                    <th class="style" style="width: 100px;">Qty2</th>
                                    <th class="style" style="width: 65px;">Grade</th>
                                    <th class="style" style="max-width: 10px;">All <input type="checkbox" name="checkFGAll" id="checkFGAll"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $empty = true;
                                foreach ($list_fg as $row) {
                                    $empty = false;
                                    if($row->lot_adj != ''){
                                        $color = "style='color:red';";
                                    }else{
                                        $color = "";
                                    }
                                ?>
                                <tr class="num" <?php echo $color ?>>
                                    <td></td>
                                    <td><?php echo $row->nama_produk?></td>
                                    <td><?php echo $row->lot?></td>
                                    <td align="right"><?php echo number_format($row->qty,2)." ".$row->uom?></td>
                                    <td align="right"><?php echo number_format($row->qty2,2)." ".$row->uom2?></td>
                                    <td><?php echo $row->nama_grade?></td>
                                    <td>
                                    <?php if($row->lot_adj == ''){ ?>
                                        <input type="checkbox" class='checkFG' value="<?php echo $row->quant_id; ?>" value2="<?php echo $row->qty;?>" value3="<?php echo $row->lot;?>">
                                    <?php } ?>
                                    </td>
                                </tr>
                                <?php 
                                }
                                if($empty == true){
                                    echo "<tr>";
                                    echo "<td colspan='7' align='center'>Tidak Ada Data";
                                    echo "</td>";
                                    echo "</tr>";
                                }
                                ?>
                            </tbody>							
                                                
                        </table>
                        <small><b>*Jika terdapat baris yang berwarna <font color="red">MERAH</font> maka Product/Lot tersebut telah di proses ADJUSTMENT !!</b></small>
				    </div>
                    <div class="col-md-12">	
							<div class=" pull-right text-right">
					            <button type="button" id="btn-copy" class="btn btn-success btn-sm"  onclick="calculate_consume()" <?php if($empty == true OR count($konsumsi) == 0)echo "disabled"?>>Hitung Konsumsi Bahan </button>
							</div>					  
                	</div>
                </div>
            </div>
        </div>
    </div> 

</form>


<hr style="border: 1px solid ">
<form class="form-horizontal" id="konsumsi_bahan" name="form_konsumsi">
    <div class="form-group">
        <div class="col-md-8">
          <div class="box ">
            <div class="box-header with-border">
              <h6 class="box-title2">Total Bahan Baku yang BELUM dan SUDAH dikonsumsi</h6>
              
            </div>
            <div class="box-body">

            <div class="col-md-6 col-sm-6 col-xs-12">
                <div class="info-box2">
                    <span class="info-box-icon bg-yellow"><i class="fa fa-cubes"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Belum dikonsumsi</span>
                        <span class="info-box-number"><?php echo number_format($rm_ready->mtr,2)?> <small>Mtr</small></span>
                        <span class="info-box-number"><?php echo number_format($rm_ready->kg,2)?> <small>Kg</small></span>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-sm-6 col-xs-12">
                <div class="info-box2">
                    <span class="info-box-icon bg-blue"><i class="fa fa-cubes"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Sudah dikonsumsi</span>
                        <span class="info-box-number"><?php echo number_format($rm_done->mtr,2)?> <small>Mtr</small></span>
                        <span class="info-box-number"><?php echo number_format($rm_done->kg,2)?> <small>Kg</small></span>
                    </div>
                </div>
            </div>
             
            </div>
          </div>
        </div>
    </div>

    <div class="col-md-6 col-xs-12">
        <label style="font-size: 15px; color: #5F9EA0">Konsumsi Bahan</label>
    </div>		
    <?php if($cek_dept == 'PPIC' or $level == 'Super Administrator' or $level == 'Administrator'){
    ?>	
    <div class="col-md-6 col-xs-12">
        <div class="form-group">
			<div class="pull-right text-right">
			    <button type="button" id="btn-habis" class="btn btn-danger btn-sm"  onclick="habiskan_consume()" <?php if(count($konsumsi)== 0 )echo "disabled"?>>Habiskan Konsumsi Bahan </button>
		    </div>					  
	    </div>
    </div>
    <?php } ?>
    
	<div class="row">
		<div class="col-md-12">		
			<div class="col-xs-12 table-responsive">	
		    <table class="table table-condesed table-hover table-responsive rlstable" id="tabel_konsumsi"> 
				<thead>
				    <tr>
						<th class="style no">No.</th>
					    <th class="style">Kode Produk</th>
					    <th class="style">Product</th>
						<th class="style">Lot</th>
						<th class="style">Grade</th>
						<th class="style" style="text-align: right;">Qty</th>
						<th class="style">uom</th>
						<th class="style">Qty dikonsumsi</th>
						<th class="style" style="text-align: right;">Qty2</th>
						<th class="style">uom2</th>
						<th class="style">Qty2 dikonsumsi</th>
					</tr>
				</thead>
		 	    <tbody>
		 	    	<?php 
		 	    	$i = 1;
					$row_materials = false;
					$total_qty1 = 0;
					$total_qty2 = 0;
                    if($cek_dept == 'PPIC' or $level == 'Super Administrator' or $level == 'Administrator'){
                        $readonly   = '';
                    }else{
                        $readonly = "readonly";
                    }
		 	    	foreach ($konsumsi as $row) {
						$row_materials = true;
		 	    	?>
					<tr>
						<td><?php echo $i;?></td>
						<td><?php echo $row->kode_produk;?></td>
						<td><?php echo $row->nama_produk;?></td>
						<td><?php echo $row->lot;?></td>
						<td><?php echo $row->nama_grade;?></td>
						<td align="right"><?php echo number_format($row->qty,2)?></td>
						<td><?php echo $row->uom;?></td>
						<td>
							<input type="text" name="qty_konsum"  id="qty_konsum" class="form-control input-sm qty_konsum" data-index="<?php echo $i;?>" onkeyup="validAngka2(this)" <?php echo $readonly?> >
							<input type="hidden" name="jml_produk"  id="jml_produk" class="form-control input-sm jml_produk" value="<?php echo $row->jml_produk ?>">
							<input type="hidden" name="qty_rm"  id="qty_rm" class="form-control input-sm qty_rm" value="<?php echo $row->qty_rm ?>">
							<input type="hidden" name="quant_id"  id="quant_id" class="form-control input-sm quant_id" value="<?php echo $row->quant_id ?>">
							<input type="hidden" name="move_id"  id="move_id" class="form-control input-sm move_id" value="<?php echo $row->move_id ?>">
							<input type="hidden" name="additional"  id="additional" class="form-control input-sm additional" value="<?php echo $row->additional ?>">
							<input type="hidden" name="origin_prod"  id="origin_prod" class="form-control input-sm origin_prod" value="<?php echo $row->origin_prod ?>">
							<input type="hidden" name="kode_produk"  id="kode_produk" class="form-control input-sm kode_produk" value="<?php echo $row->kode_produk ?>">
							<input type="hidden" name="nama_produk"  id="nama_produk" class="form-control input-sm nama_produk" value="<?php echo htmlentities($row->nama_produk) ?>">
							<input type="hidden" name="qty_smi"  id="qty_smi" class="form-control input-sm qty_smi" value="<?php echo $row->qty ?>">
							<input type="hidden" name="uom"  id="uom" class="form-control input-sm uom" value="<?php echo $row->uom ?>">
							<input type="hidden" name="lot"  id="lot" class="form-control input-sm lot" value="<?php echo htmlentities($row->lot) ?>">
							<input type="hidden" name="qty2"  id="qty2" class="form-control input-sm qty2" value="<?php echo $row->qty2 ?>">
							<input type="hidden" name="uom2"  id="uom2" class="form-control input-sm uom2" value="<?php echo $row->uom2 ?>">
							<input type="hidden" name="reff_note"  id="reff_note" class="form-control input-sm reff_note" value="<?php echo $row->reff_note ?>">
							<input type="hidden" name="grade"  id="grade" class="form-control input-sm grade" value="<?php echo $row->nama_grade;?>">
							<input type="hidden" name="lbr_greige"  id="lbr_greige" class="form-control input-sm lbr_greige" value="<?php echo $row->lebar_greige ?>">
							<input type="hidden" name="uom_lbr_greige"  id="uom_lbr_greige" class="form-control input-sm uom_lbr_greige" value="<?php echo $row->uom_lebar_greige ?>">
							<input type="hidden" name="lbr_jadi"  id="lbr_jadi" class="form-control input-sm lbr_jadi" value="<?php echo $row->lebar_jadi ?>">
							<input type="hidden" name="uom_lbr_jadi"  id="uom_lbr_jadi" class="form-control input-sm uom_lbr_jadi" value="<?php echo $row->uom_lebar_jadi ?>">
							<input type="hidden" name="sales_order"  id="sales_order" class="form-control input-sm sales_order" value="<?php echo $row->sales_order ?>">
							<input type="hidden" name="sales_group"  id="sales_group" class="form-control input-sm sales_group" value="<?php echo $row->sales_group ?>">

						</td>
						<td align="right"><?php echo $row->qty2?></td>
						<td><?php echo $row->uom2;?></td>
						<td><input type="text" name="qty2_konsum"  id="qty2_konsum" class="form-control input-sm qty2_konsum" data-index="<?php echo $i;?>" <?php echo $readonly?> ></td>						
					</tr>
					<?php
					 $i++; 
					 $total_qty1 = $total_qty1 + $row->qty;
					 $total_qty2 = $total_qty2 + $row->qty2;
					}
					$total_lot = $i-1;
					if($row_materials == false){
						echo "<tr>";
						echo "<td colspan='11' align='center'>Tidak Ada Data";
						echo "</td>";
						echo "</tr>";
					}else{
						echo "<tr>";
						echo "<td colspan='3' align='center'><b>Total</b></td>";
						echo "<td><b>".$total_lot." Lot</b></td>";
						echo "<td></td>";
						echo "<td align='right'><b>".number_format($total_qty1,2)." </b></td>";
						echo "<td></td>";
						echo "<td><input type='text' class='form-control input-sm' name='total_qty1_konsum' id='total_qty1_konsum' readonly></td>";
						echo "<td align='right'><b>".number_format($total_qty2,2)." </b></td>";
						echo "<td></td>";
						echo "<td><input type='text' class='form-control input-sm' name='total_qty2_konsum' id='total_qty2_konsum' readonly></td>";
						echo "<td></td>";
						echo "</tr>";
					}
					?>
				</tbody>
			</table>
		    </div>
		</div>		
	</div>
</form>

<style type="text/css">
	.error{
		border:  1px solid red;
	}  
	.error2{
		border:  1px solid red;
	} 
    .error_target{
		border: 1px solid red;
		color : red
	}
</style>

<script>

    var konsumsi = "<?php echo count($konsumsi) ?>";

    $("#tambah_data .modal-dialog .modal-content .modal-footer").html('<button type="button" id="btn-produksi-consume" class="btn btn-primary btn-sm"> Simpan</button> <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Tutup</button>');

	function formatNumber(n) {
		return new Intl.NumberFormat('en-US').format(n);
	}

    //validasi qty
	function validAngka2(a){
	    if(!/^[0-9.]+$/.test(a.value)){
	        a.value = a.value.substring(0,a.value.length-1000);
	    }
	}

    function calculate_consume(){
       

        $(document).one("click","#btn-copy",function(e) {
            var row_order_rm = 1;
            var checkKp      = new Array();

            // alert('tes');
            var total_qty = 0;
            $(".checkFG:checked").each(function() {
                value2  = $(this).attr('value2');
                checkKp.push({
                                "quant_id" : $(this).val(),
                                "qty"      : value2,
                });
                total_qty = total_qty + parseFloat(value2);
            });

            if(total_qty == 0){
                alert_bootbox("Pilih Terlebih dahulu Lot/KP !");
            }else{
                // disable btn konsumsi bahan 
                $("#btn-habis").prop('disabled',true); 

                qty_inp = total_qty;
                var qty_prod = $('#qty_prod').val();
                var total_qty1_konsum = 0;
                var total_qty2_konsum = 0;
                var qty_konsum_show   = false;
                var deptid 		= "<?php echo $deptid?>";//dept id untuk log history

                if(qty_prod > 0){
                    if(deptid == 'WRD' || deptid == 'TWS' || deptid == 'WRP'){
                        loop = 0;
                        $('.qty_konsum').each(function(index,item){
                            qty_konsum_show = true;
                            var qty_konsum 	= $(item).val();
                            var qty_smi		= $(this).parents("tr").eq(0).find(".qty_smi").val();
                            var qty_rm 		= $(this).parents("tr").eq(0).find(".qty_rm").val();
                            var jml_produk 	= $(this).parents("tr").eq(0).find(".jml_produk").val();
                            var qty2 		= $(this).parents("tr").eq(0).find(".qty2").val();

                            if(loop == 0){
                                if(qty_prod != 0){
                                    var hitung 		= (qty_rm/qty_prod)*qty_inp;
                                    var sum_con 	= hitung;//hitung qty dikonsumsi
                                }else{
                                    var sum_con     = 0;
                                }
                            }else{
                                sum_con = sisa_sum_con;
                            }

                            if(sum_con > 0){
                                if(sum_con > qty_smi){
                                    $(item).eq(0).val(qty_smi);
                                    if(qty_smi == '' || qty_smi == 0){
                                        hitung2 = 0;	
                                    }else{
                                        hitung2 =  (qty2/qty_smi)*qty_smi;
                                    }
                                    hitung2_fix = hitung2.toFixed(2);
                                    $(this).parents("tr").eq(0).find(".qty2_konsum").val(hitung2_fix);
                                    tot_qty1 = qty_smi;
                                    tot_qty2 = hitung2_fix;

                                    sisa_sum_con = sum_con - qty_smi;                                
                                }else{

                                    var sum_con_fix = sum_con.toFixed(2);				
                                    // var sum_con_fix = sum_con;		
                                    $(item).eq(0).val(sum_con_fix);
                                    if(qty_smi == '' || qty_smi == 0){
                                        hitung2 = 0;	
                                    }else{
                                        hitung2 =  (qty2/qty_smi)*sum_con_fix;
                                    }
                                    hitung2_fix = hitung2.toFixed(2);
                                    $(this).parents("tr").eq(0).find(".qty2_konsum").val(hitung2_fix);
                                    //  console.log("qty2 = ("+qty2+"/"+qty_smi+")*"+sum_con_fix);
                                    tot_qty1 = sum_con_fix;
                                    tot_qty2 = hitung2_fix;

                                    sisa_sum_con = sum_con - qty_smi;

                                }
                                total_qty1_konsum = total_qty1_konsum + parseFloat(tot_qty1);
                                total_qty2_konsum = total_qty2_konsum + parseFloat(tot_qty2);
                            }
                            loop++;

                            // remove class error
                            $(item).removeClass('error');
                            $(item).parents("tr").find('#qty2_konsum').removeClass('error');

                        });
                    }else{
                        $('.qty_konsum').each(function(index,item){
                            qty_konsum_show = true;	
                            var qty_konsum 	= $(item).val();
                            var qty_smi		= $(this).parents("tr").eq(0).find(".qty_smi").val();
                            var qty_rm 		= $(this).parents("tr").eq(0).find(".qty_rm").val();
                            var jml_produk 	= $(this).parents("tr").eq(0).find(".jml_produk").val();
                            var qty2 		= $(this).parents("tr").eq(0).find(".qty2").val();
                            if(qty_prod != 0){
                                var hitung 		= (qty_rm/qty_prod)*qty_inp;
                                //var sum_con = (hitung/qty_rm)*qty_smi;//hitung qty dikonsumsi
                                var sum_con 	= (hitung/jml_produk);//hitung qty dikonsumsi
                            }else{
                                var sum_con     = 0;
                            }
                            // alert(sum_con)
                            if(sum_con > qty_smi){
                                $(item).eq(0).val(qty_smi);
                                if(qty_smi == '' || qty_smi == 0){
                                    hitung2 = 0;	
                                }else{
                                    hitung2 =  (qty2/qty_smi)*qty_smi;
                                }
                                hitung2_fix = hitung2.toFixed(2);
                                $(this).parents("tr").eq(0).find(".qty2_konsum").val(hitung2_fix);
                                tot_qty1 = qty_smi;
                                tot_qty2 = hitung2_fix;
                            }else{
                                var sum_con_fix = sum_con.toFixed(2);				
                                // var sum_con_fix = sum_con;		
                                $(item).eq(0).val(sum_con_fix);
                                if(qty_smi == '' || qty_smi == 0){
                                    hitung2 = 0;	
                                }else{
                                    hitung2 =  (qty2/qty_smi)*sum_con_fix;
                                }
                                hitung2_fix = hitung2.toFixed(2);
                                $(this).parents("tr").eq(0).find(".qty2_konsum").val(hitung2_fix);
                                //  console.log("qty2 = ("+qty2+"/"+qty_smi+")*"+sum_con_fix);
                                tot_qty1 = sum_con_fix;
                                tot_qty2 = hitung2_fix;
                            }

                            // remove class error
                            $(item).removeClass('error');
                            $(item).parents("tr").find('#qty2_konsum').removeClass('error');


                            total_qty1_konsum = total_qty1_konsum + parseFloat(tot_qty1);
                            total_qty2_konsum = total_qty2_konsum + parseFloat(tot_qty2);
                        });
                    }
                }else{
                    alert_bootbox("Maaf, Qty Target MO 0, Jadi tidak bisa mengkonsumsi bahan baku !");
                }
                if(qty_konsum_show){
                    document.getElementById('total_qty1_konsum').value = formatNumber(total_qty1_konsum.toFixed(2));
                    document.getElementById('total_qty2_konsum').value = formatNumber(total_qty2_konsum.toFixed(2));
                }
            }
        });


    }

    function habiskan_consume(){
        var total_qty1_konsum = 0;
        var total_qty2_konsum = 0;
        $('.qty_konsum').each(function(index,item){	
            var qty			= $(this).parents("tr").eq(0).find(".qty_smi").val();
            var qty2	    = $(this).parents("tr").eq(0).find(".qty2").val();

            // samakan dengan qty 
            $(this).val(qty); 
            $(this).parents("tr").eq(0).find(".qty2_konsum").val(qty2); 

            total_qty1_konsum = total_qty1_konsum + parseFloat(qty);
            total_qty2_konsum = total_qty2_konsum + parseFloat(qty2);

        });

		document.getElementById('total_qty1_konsum').value = formatNumber(total_qty1_konsum.toFixed(2));
		document.getElementById('total_qty2_konsum').value = formatNumber(total_qty2_konsum.toFixed(2));

        $('.checkFG').each(function(index,item){
            $(this).prop("checked", false);
            $('#checkFGAll').prop("checked", false);
        });

    }

    //checked All
    $('#checkFGAll').on("change", function(){
        $('.checkFG').prop("checked", $(this).prop("checked"));

        $('.qty_konsum').each(function(index,item){		
            $(item).val('');
            $(this).parents("tr").eq(0).find(".qty2_konsum").val('');
        });
        if(konsumsi > 0){
            $("#btn-habis").prop('disabled',false); 
        }
    });
    
    $(".checkFG").on("change", function(){
        var rm = false;
        // clear konsumsi bahan 
        $('.qty_konsum').each(function(index,item){	
            rm = true;
            $(item).val('');
            $(this).parents("tr").eq(0).find(".qty2_konsum").val('');
            $(item).removeClass('error');
            $(this).parents("tr").eq(0).find('.qty2_konsum').removeClass('error');
        });

        if(rm == true){
            document.getElementById('total_qty1_konsum').value = '';
            document.getElementById('total_qty2_konsum').value = '';
        }

        var checked = $(this).is(':checked');
        if(checked == false){
            $('#checkFGAll').prop("checked", false);
        }

        checkAllFG();
        if(rm == true && konsumsi > 0){
            $("#btn-habis").prop('disabled',false); 
        }
    });

    function checkAllFG(){

        var lengthClass = $(".checkFG").length;
        var loop        = 0;
        $('.checkFG').each(function(index,item){		
            var checked = $(this).is(':checked');

            if(checked == true){
                // alert(loop);
                loop++;
            }

            if(lengthClass == loop){
                $('#checkFGAll').prop("checked", true);
            }
        });
    }


	$(".qty_konsum").on("keyup", function(){

        var qty2_konsum_new = 0;
        var qty2			= $(this).parents("tr").eq(0).find(".qty2").val();
        var qty_smi			= $(this).parents("tr").eq(0).find(".qty_smi").val();
        var qty_konsum 		= $(this).val();


        if(parseFloat(qty_konsum) > qty_smi){
            qty_konsum_ = qty_smi;
            alert_bootbox("Qty Dikonsumsi tidak boleh lebih dari Qty");
            $(this).val(0);
        }else{
            qty_konsum_ = qty_konsum;
        }

        if(qty_smi > 0){
            // total qty 2 baru setalh qty1 dirubah
            hitung_qty2_new = (qty2/qty_smi)*qty_konsum_;
            if(hitung_qty2_new > qty2 ){
                hitung_fix      = qty2;
            }else{
                hitung_fix      = hitung_qty2_new.toFixed(2);
            }
            if(parseFloat(qty_konsum) > qty_smi){
                $(this).parents("tr").eq(0).find(".qty2_konsum").val(0);
            }else{
                $(this).parents("tr").eq(0).find(".qty2_konsum").val(hitung_fix);
            }
        }else{
            $(this).parents("tr").eq(0).find(".qty2_konsum").val(0);
        }

        // total bahan baku yg akan dikonsumsi
        var total_qty1_konsum = 0;
        $('.qty_konsum').each(function(index,item){		
            var qty1   = $(item).val();
            if(qty1!= ''){
                qty1   = $(item).val();
            }else{
                qty1   = 0;
            }
            total_qty1_konsum = total_qty1_konsum+parseFloat(qty1);
        });
        document.getElementById('total_qty1_konsum').value = formatNumber(total_qty1_konsum.toFixed(2));

        var total_qty2_konsum = 0;
        $('.qty2_konsum').each(function(index,item){		
			var qty2   = $(item).val();
			if(qty2!= ''){
				qty2   = $(item).val();
			}else{
				qty2   = 0;
			}
			total_qty2_konsum = total_qty2_konsum+parseFloat(qty2);
		});
		document.getElementById('total_qty2_konsum').value = formatNumber(total_qty2_konsum.toFixed(2));
    });


	$(".qty2_konsum").on("keyup", function(){
		var qty2			= $(this).parents("tr").eq(0).find(".qty2").val();
		var qty2_konsum 	= $(this).val();
		var total_qty2_konsum = 0;
		if(parseFloat(qty2_konsum) > qty2){
			alert_bootbox("Qty Dikonsumsi tidak boleh lebih dari Qty");
			$(this).val(0);
		}

		$('.qty2_konsum').each(function(index,item){		
			var qty2   = $(item).val();
			if(qty2!= ''){
				qty2   = $(item).val();
			}else{
				qty2   = 0;
			}
			total_qty2_konsum = total_qty2_konsum+parseFloat(qty2);
		});
		document.getElementById('total_qty2_konsum').value = formatNumber(total_qty2_konsum.toFixed(2));
	});


    $("#btn-produksi-consume").off("click").on("click",function(e) {
		
        e.preventDefault();
        var deptid 		= "<?php echo $deptid?>";//dept id untuk log history
        var kode   		= "<?php echo $kode?>";//kode MO
        var origin_mo 	= "<?php echo $origin_mo ?>";
        var valid 		= true;
        var empty_check = true;

        
        $(".checkFG:checked").each(function() {
            empty_check = false;
        });

        // if(empty_check){
        //     alert_bootbox("Pilih Terlebih dahulu Lot/KP !");
        //     valid = false;
        // }

        if(valid){

                var konsumsi_bahan = false;
                var arr            = new Array();
                // alert("masuk");

                $(".checkFG:checked").each(function() {
                    value3  = $(this).attr('value3');
                    arr.push({
                            "quant_id" : $(this).val(),
                            "lot"      : value3,
                    });
                });
                
        
                var arr2 = new Array();
                var input_rm = false;
				$('.qty_konsum').each(function(index,item){

                    qty          = $(item).parents("tr").find('#qty_smi').val();
                    qty_konsum   = $(item).parents("tr").find('#qty_konsum').val();

                    qty2         = $(item).parents("tr").find("#qty2").val();
                    qty2_konsum  = $(item).parents("tr").find('#qty2_konsum').val();
                    qty2_row     = $(item).parents("tr").find('#qty2_konsum');
                    if(parseFloat(qty) == parseFloat(qty_konsum)){
                        if(parseFloat(qty2) > parseFloat(qty2_konsum)){
                            input_rm = true
                            $(item).addClass('error');
                            qty2_row.addClass('error');
                        }else{
                            $(item).removeClass('error');
                            qty2_row.removeClass('error');
                        }
                    }else{
                        $(item).removeClass('error');
                        qty2_row.removeClass('error');
                    }

					if ((qty_konsum != "" || qty2_konsum != "") && ( qty_konsum != 0 || qty2_konsum ) && (qty_konsum != 0.00  || qty2_konsum != 0.00)) {
						arr2.push({
							kode 		: $("#txtkode").val(),
							qty_konsum  : $(item).parents("tr").find('#qty_konsum').val(),
                            qty2_konsum : $(item).parents("tr").find('#qty2_konsum').val(),
							quant_id 	: $(item).parents("tr").find('#quant_id').val(),
							move_id 	: $(item).parents("tr").find('#move_id').val(),
							additional 	: $(item).parents("tr").find('#additional').val(),
							kode_produk : $(item).parents("tr").find('#kode_produk').val(),
							nama_produk : $(item).parents("tr").find('#nama_produk').val(),
							qty_smi     : $(item).parents("tr").find('#qty_smi').val(),
							uom     	: $(item).parents("tr").find('#uom').val(),
							lot     	: $(item).parents("tr").find('#lot').val(),
							origin_prod : $(item).parents("tr").find('#origin_prod').val(),
							qty2        : $(item).parents("tr").find("#qty2").val(),
							uom2        : $(item).parents("tr").find("#uom2").val(),
							reff_note   : $(item).parents("tr").find("#reff_note").val(),
							qty_rm      : $(item).parents("tr").find('#qty_rm').val(),
							grade 		: $(item).parents("tr").find("#grade").val(),
							lbr_greige  : $(item).parents("tr").find('#lbr_greige').val(),
							uom_lbr_greige  : $(item).parents("tr").find('#uom_lbr_greige').val(),
							lbr_jadi    : $(item).parents("tr").find('#lbr_jadi').val(),
							uom_lbr_jadi  : $(item).parents("tr").find('#uom_lbr_jadi').val(),
							sales_order :$(item).parents("tr").find("#sales_order").val(),
							sales_group :$(item).parents("tr").find("#sales_group").val(),
						});				
					
						konsumsi_bahan = true;
					}
				});		
                // alert (JSON.stringify(arr2));

                if(konsumsi_bahan == false && empty_check == false){
                    alert_bootbox('Maaf, Konsumsi Bahan harus ada !');
                }else if(konsumsi_bahan == false){
                    alert_bootbox('Maaf, Konsumsi Bahan harus ada !');
                }else if(input_rm == true){	
					alert_bootbox('Maaf, Inputan Konsumsi Bahan baku tidak Valid !');
                }else{	
                    please_wait(function(){});
                    $('#btn-produksi-consume').button('loading');
                    $.ajax({
                        dataType: "JSON",
                        url : '<?php echo site_url('manufacturing/mO/save_consume_modal') ?>',
                        type: "POST",
                        data: { deptid 		: deptid, 
                                origin_mo 	: origin_mo,  
                                kode 		: kode,
                                kode_produk :$("#txtkode_produk").val(), 
                                data_fg 	: JSON.stringify(arr), 
                                data_rm 	: JSON.stringify(arr2), 
                                
                                },
                        success: function(data){

                            if(data.sesi == "habis"){
                                //alert jika session habis
                                alert_modal_warning(data.message);
                                window.location.replace('../index');
                                unblockUI( function(){});
                            }else if(data.status == "failed"){
                                $("#status_bar").load(location.href + " #status_bar");
                                $("#tab_1").load(location.href + " #tab_1");
                                $("#tab_2").load(location.href + " #tab_2");             
                                $("#foot").load(location.href + " #foot");
                                $('#btn-produksi-consume').button('reset');
                                alert(data.message);
                                unblockUI( function(){});
                            }else{
                                //jika berhasil disimpan
                                $("#status_bar").load(location.href + " #status_bar");
                                $("#tab_1").load(location.href + " #tab_1");
                                $("#tab_2").load(location.href + " #tab_2");             
                                $("#foot").load(location.href + " #foot");
                                $('#tambah_data').modal('hide');
                                $('#btn-produksi-consume').button('reset');
                                if(data.double == 'yes'){
                                    alert_modal_warning(data.message2);
                                }
                                //console.log(data.sql);
                                unblockUI( function(){
                                    setTimeout(function() { alert_notify(data.icon,data.message,data.type,function(){});}, 1000);
                                });
                                
                            }
                            $("#tambah_data .modal-dialog .modal-content .modal-body").removeClass('consume'); 
                            
                        },error: function (jqXHR, textStatus, errorThrown){
                            alert(jqXHR.responseTex+' error');
                            $('#btn-produksi-consume').button('reset');
                            unblockUI( function(){});
                        }
                    });
                }

        }




    });

</script>
