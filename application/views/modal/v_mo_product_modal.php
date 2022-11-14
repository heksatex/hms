<form class="form-horizontal">
	<div class="col-md-6">
		<div class="form-group">
			<div class="col-md-12 col-xs-12">
                <div class="col-xs-4"><label>Kode Produk</label></div>
                <div class="col-xs-8">
                  	<input type="text" name="kode_produk" id="kode_produk" class="form-control input-sm"  value="<?php echo htmlentities($prod['kode_produk'])?>" readonly="readonly"/>
                </div>  
             </div>
			<div class="col-md-12 col-xs-12">
                <div class="col-xs-4"><label>Nama Produk</label></div>
                <div class="col-xs-8">
                  	<input type="text" name="product" id="product"class="form-control input-sm"  value="<?php echo htmlentities($prod['nama_produk'])?>" readonly="readonly"/>
                </div>  
            </div>
			<div class="col-md-12 col-xs-12">
                <div class="col-xs-4"><label>Uom</label></div>
                <div class="col-xs-8">
                  	<input type="text" name="uom1" id="uom1"class="form-control input-sm"  value="<?php echo htmlentities($prod['uom'])?>" readonly="readonly"/>
                </div>  
            </div>
			<div class="col-md-12 col-xs-12">
                <div class="col-xs-4"><label>Uom2</label></div>
                <div class="col-xs-8">
                  	<input type="text" name="uom2" id="uom2"class="form-control input-sm"  value="<?php echo htmlentities($prod['uom_2'])?>" readonly="readonly"/>
                </div>  
            </div>
			<div class="col-md-12 col-xs-12">
                <div class="col-xs-4"><label>Kategori </label></div>
                <div class="col-xs-8">
                  	<input type="text" name="nama_kategori" id="nama_kategori" class="form-control input-sm"  value="<?php echo htmlentities($prod['nama_category'])?>" readonly="readonly"/>
                </div>  
            </div>
		</div>
	</div>
	<div class="form-group">
		<div class="col-md-6">
			<div class="col-md-12 col-xs-12">
	            <div class="col-xs-4"><label>Tanggal dibuat</label></div>
	            <div class="col-xs-8">
	                <input type="text" name="create" id="create" class="form-control input-sm"  value="<?php echo htmlentities($prod['create_date'])?>" readonly="readonly"/>
	            </div>  
	        </div>
		</div>
	</div>
</form>

