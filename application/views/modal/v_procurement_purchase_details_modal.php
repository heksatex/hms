 <form class="form-horizontal">

 	<div class="form-group">
		<div class="col-md-12 col-xs-12">
	        <div class="col-xs-12"><label style="font-size: 15px;">Calls For Bids</label></div>
	   </div>
	</div>
	<div class="form-group"  id="table_2">
		 <div class="col-md-12 table-responsive">
		    <table class="table table-condesed table-hover table-responsive rlstable">
		        <tr>
		            <th class="style no">No.</th>
		            <th class="style">Kode</th>
		            <th class="style">Create Date</th>
		            <th class="style">Schedule Date</th>
		            <th class="style">Sales Order</th>
		            <th class="style">Production Order</th>
		            <th class="style">Priority</th>
		            <th class="style">Warehouse</th>
		            <th class="style">Note</th>
		            <th class="style">Status</th>
		        </tr>
		        <tbody>
		            <?php
		            $i =1;
		            foreach ($cfb as $row) {
		            ?>
		            <tr>
		                <td><?php echo $i++.'.';?></td>
		                <td><?php echo $row->kode_cfb?></td>
		                <td><?php echo $row->create_date?></td>
		                <td><?php echo $row->schedule_date?></td>
		                <td><?php echo $row->sales_order?></td>
		                <td><?php echo $row->kode_prod?></td>
		                <td><?php echo $row->priority?></td>
		                <td><?php echo $row->nama_dept?></td>
		                <td><?php echo $row->notes?></td>
		                <td><?php if($row->status == 'cancel') echo 'Batal';  else echo $row->status;?></td>
		            </tr>
		            <?php 
		             }
		            ?>
		        
		        </tbody>
		    </table>
		</div>
	</div>
	<hr style="border: 1px solid ">

    <div class="form-group">
		<div class="col-md-12 col-xs-12">
			<div class="col-xs-12"><label style="font-size: 15px;">Penerimaan Barang (IN)</label></div>
		</div>
	</div>
    <div class="form-group"  id="table_1">
		 <div class="col-md-12 table-responsive">
		    <table class="table table-condesed table-hover table-responsive rlstable">
		        <tr>
		            <th class="style no">No.</th>
		            <th class="style">Kode</th>
		            <th class="style">Tanggal Dibuat</th>
		            <th class="style">Origin</th>
		            <th class="style">Lokasi Tujuan</th>
		            <th class="style">Departemen</th>
		            <th class="style">Reff Note</th>
		            <th class="style">Status</th>
		        </tr>
		        <tbody>
		            <?php
		            $i =1;
		            foreach ($penerimaan as $row) {
		            	$kode_encrypt = encrypt_url($row->kode);
		            ?>
		            <tr>
		                <td><?php echo $i++.'.';?></td>
		                <td><?php echo '<a href="'.base_url('warehouse/penerimaanbarang/edit/'.$kode_encrypt).'" target="_blank">'.$row->kode.'</a>';?></td>
		                <td><?php echo $row->tanggal?></td>
		                <td><?php echo $row->origin?></td>
		                <td><?php echo $row->lokasi_tujuan?></td>
		                <td><?php echo $row->departemen?></td>
		                <td><?php echo $row->reff_note?></td>
		                <td><?php if($row->status == 'cancel') echo 'Batal';  else echo $row->status;?></td>
		               
		            </tr>
		            <?php 
		             }
		            ?>
		        
		        </tbody>
		    </table>
		</div>
	</div>
	<hr style="border: 1px solid ">



	<div class="form-group">
		<div class="col-md-12 col-xs-12">
	        <div class="col-xs-12"><label style="font-size: 15px;">Pengiriman Barang (OUT)</label></div>
		</div>
	</div>
	<div class="form-group"  id="table_3">
		 <div class="col-md-12 table-responsive">
		    <table class="table table-condesed table-hover table-responsive rlstable">
		        <tr>
		            <th class="style no">No.</th>
		            <th class="style">Kode</th>
		            <th class="style">Tanggal Dibuat</th>
		            <th class="style">Origin</th>
		            <th class="style">Lokasi Tujuan</th>
		            <th class="style">Departemen</th>
		            <th class="style">Reff Note</th>
		            <th class="style">Status</th>
		        </tr>
		        <tbody>
		            <?php
		            $i =1;
		            foreach ($pengiriman as $row) {
		            	$kode_encrypt = encrypt_url($row->kode);
		            ?>
		            <tr>
		                <td><?php echo $i++.'.';?></td>
		                <td><?php echo '<a href="'.base_url('warehouse/pengirimanbarang/edit/'.$kode_encrypt).'" target="_blank">'.$row->kode.'</a>';?></td>
		                <td><?php echo $row->tanggal?></td>
		                <td><?php echo $row->origin?></td>
		                <td><?php echo $row->lokasi_tujuan?></td>
		                <td><?php echo $row->departemen?></td>
		                <td><?php echo $row->reff_note?></td>
		                <td><?php if($row->status == 'cancel') echo 'Batal';  else echo $row->status;?></td>
		               
		            </tr>
		            <?php 
		             }
		            ?>
		        
		        </tbody>
		    </table>
		</div>
	</div>
</form>
