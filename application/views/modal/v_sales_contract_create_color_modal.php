
<form class="form-horizontal" id="form_create_color" name="form_create_color">
	<div class="form-group">
		<div class="col-md-6">
			<div class="col-md-12 col-xs-12">
				<div class="col-xs-4"><label>Tanggal</label></div>
				<div class="col-xs-8">
            <div class='input-group date' id='tanggal' >
              <input type='text' class="form-control input-sm" name="tgl_modal" id="tgl_modal" readonly="readonly"  />
              <span class="input-group-addon">
                <span class="glyphicon glyphicon-calendar"></span>
              </span>
            </div>	
				</div>
			</div>
		</div>		
	</div>
	<div class="form-group">
		<div class="col-md-6">
			<div class="col-md-12 col-xs-12">
				<div class="col-xs-4"><label>Warna</label></div>
				<div class="col-xs-8">
                    <input type="text" class="form-control input-sm" name="warna" id="warna"/>
					<input type="hidden"  class="form-control input-sm" name="sales_group" id="sales_group" value="<?php echo $sales_group?>">
				</div>
			</div>
		</div>		
	</div>
	<div class="form-group">
		<div class="col-md-6">
			<div class="col-md-12 col-xs-12">
				<div class="col-xs-4"><label>Notes</label></div>
				<div class="col-xs-8">
            <textarea type="text" class="form-control input-sm" name="notes" id="notes"></textarea>
				</div>
			</div>
		</div>		
	</div>
</form>


<script type="text/javascript">
  	
    //set tgl buat
    var datenow=new Date();  
    datenow.setMonth(datenow.getMonth());
    $('#tanggal').datetimepicker({
      defaultDate: datenow,
        format : 'YYYY-MM-DD HH:mm:ss',
        ignoreReadonly: true,
    });
	  
</script>