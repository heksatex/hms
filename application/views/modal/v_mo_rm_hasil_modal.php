 <form class="form-horizontal">
 	<div class="row">                  
        <div class="col-xs-12 col-md-12">
            <div class="col-xs-5 col-md-3">
                <label>Kode Produk</label>                            
            </div>            
            <div class="col-xs-7 col-md-9"> 
              <label>:</label>                            
              <?php echo $kode_produk; ?>                      
            </div>
            <div class="col-xs-5 col-md-3">                          
                <label>Product </label>
            </div>
            <div class="col-xs-7 col-md-9"> 
                <label>:</label>                            
                <?php echo $nama_produk; ?>
            </div>
        </div>
    </div>
    <div class="form-group">
		 <div class="col-md-12 table-responsive">
		    <table class="table table-condesed table-hover table-responsive rlstable">
		        <tr>
		            <th class="style no">No.</th>
                    <th class="style">Kode Produk</th>
                    <th class="style">Product</th>
                    <th class="style">Lot</th>
                    <th class="style" style="text-align: right;">Qty</th>
                    <th class="style">uom</th>
                    <th class="style" style="text-align: right;">Qty2</th>
                    <th class="style">uom2</th>
		        </tr>
		        <tbody>
                    <?php
                        $total_qty = 0;
		           		$total_qty2 = 0;
                        foreach ($list_rm_hasil as $row) {
                    ?>
                    <tr class="num">
                        <td></td>
                        <td><?php echo $row->kode_produk?></td>
                        <td><?php echo $row->nama_produk?></td>
                        <td><?php echo $row->lot?></td>
                        <td align="right"><?php echo number_format($row->qty,2)?></td>
                        <td><?php echo $row->uom?></td>
                        <td align="right"><?php echo number_format($row->qty2,2)?></td>
                        <td><?php echo $row->uom2?></td>                                  
                    </tr>
                    <?php 
                        $total_qty  = $total_qty + $row->qty;
                    	$total_qty2 = $total_qty2 + $row->qty2;
                        }
                    ?>
                    <tr>
		           		<th colspan="4" style="text-align: right;">Total Qty</th>
		           		<th style="text-align: right"><?php echo number_format($total_qty,2)?></th>
		           		<td></td>
                        <th style="text-align: right"><?php echo number_format($total_qty2,2)?></th>
		           	</tr>
                </tbody>
		    </table>
		</div>
	</div>
</form>
