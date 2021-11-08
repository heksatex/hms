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
                <div class="col-xs-4"><label>Product</label></div>
                <div class="col-xs-8">
                  	<input type="text" name="product" id="product"class="form-control input-sm"  value="<?php echo htmlentities($prod['nama_produk'])?>" readonly="readonly"/>
                </div>  
             </div>
		</div>
	</div>
	<div class="form-group">
		<div class="col-md-6">
			<div class="col-md-12 col-xs-12">
                <div class="col-xs-4"><label>Lebar</label></div>
                <div class="col-xs-8">
                  	<input type="text" name="lebar" id="lebar" class="form-control input-sm"  value="<?php echo htmlentities($prod['lebar'])?>" readonly="readonly"/>
                </div>  
             </div>
			<div class="col-md-12 col-xs-12">
	            <div class="col-xs-4"><label>Create Date</label></div>
	            <div class="col-xs-8">
	                <input type="text" name="create" id="create" class="form-control input-sm"  value="<?php echo htmlentities($prod['create_date'])?>" readonly="readonly"/>
	            </div>  
	        </div>
		</div>
	</div>
</form>

