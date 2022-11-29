<form class="form-horizontal">
	<div class="col-md-6">
		<div class="form-group">
            <div class="col-md-12 col-xs-12">
                <div class="col-xs-4"><label>Kode BOM</label></div>
                <div class="col-xs-8">
                  	<input type="text" name="kode_bom" id="kode_bom" class="form-control input-sm"  value="<?php echo htmlentities($bom['kode_bom'])?>" readonly="readonly"/>
                </div>  
             </div>
			<div class="col-md-12 col-xs-12">
                <div class="col-xs-4"><label>Nama BOM</label></div>
                <div class="col-xs-8">
                  	<input type="text" name="nama_bom" id="nama_bom"class="form-control input-sm"  value="<?php echo htmlentities($bom['nama_bom'])?>" readonly="readonly"/>
                </div>  
            </div>
			<div class="col-md-12 col-xs-12">
                <div class="col-xs-4"><label>Kode Produk</label></div>
                <div class="col-xs-8">
                  	<input type="text" name="kode_produk_bom" id="kode_produk_bom" class="form-control input-sm"  value="<?php echo htmlentities($bom['kode_produk'])?>" readonly="readonly"/>
                </div>  
             </div>
			<div class="col-md-12 col-xs-12">
                <div class="col-xs-4"><label>Nama Produk</label></div>
                <div class="col-xs-8">
                  	<input type="text" name="nama_produk_bom" id="nama_produk_bom" class="form-control input-sm"  value="<?php echo htmlentities($bom['nama_produk'])?>" readonly="readonly"/>
                </div>  
            </div>
			<div class="col-md-12 col-xs-12">
                <div class="col-xs-4"><label>Qty </label></div>
                <div class="col-xs-6">
                    <input type="text" name="qty_bom" id="qty_bom" class="form-control input-sm"  value="<?php echo htmlentities($bom['qty'])?>" readonly="readonly"/>
                </div> 
                <div class="col-xs-2">
                    <input type="text" name="uom_bom" id="uom_bom" class="form-control input-sm"  value="<?php echo htmlentities($bom['uom'])?>" readonly="readonly"/>
                </div>  
            </div>
		</div>
	</div>
	<div class="form-group">
		<div class="col-md-6">
			<div class="col-md-12 col-xs-12">
	            <div class="col-xs-4"><label>Tanggal dibuat</label></div>
	            <div class="col-xs-8">
	                <input type="text" name="create" id="create" class="form-control input-sm"  value="<?php echo htmlentities($bom['tanggal'])?>" readonly="readonly"/>
	            </div>  
	        </div>
            <div class="col-md-12 col-xs-12">
	            <div class="col-xs-4"><label>Status BOM</label></div>
	            <div class="col-xs-8">
	                <input type="text" name="status_produk" id="status_produk" class="form-control input-sm"  value="<?php echo htmlentities($bom['nama_status'])?>" readonly="readonly"/>
	            </div>  
	        </div>
		</div>
	</div>
</form>

