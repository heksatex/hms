
<form class="form-horizontal">
  <div class="col-md-5  col-xs-6">
    <div class="form-group">
      <div class="col-md-12 col-xs-12">
        <div class="col-xs-12">
          <center>
            <button type="button" id="print-idn" class="btn btn-default btn-sm"><img style="width: 15px" src="<?php echo base_url('dist/img/flag-idn.png') ?>"  > IND</button>
          </center>
        </div>  
      </div>
    </div>
  </div>
  <div class="form-group">
    <div class="col-md-6 col-xs-6">
      <div class="col-md-12 col-xs-12">
        <div class="col-xs-12">
          <center>
            <button type="button" id="print-eng" class="btn btn-default btn-sm"><img style="width: 15px" src="<?php echo base_url('dist/img/flag-eng.png') ?>"  > ENG</button>
          </center>
        </div>  
      </div>
    </div>
  </div>
</form>

<script type="text/javascript">

  $('#print-idn').click(function(){
      event.preventDefault();
      var  so = '<?php echo encrypt_url($so['so'])?>';
      var url = '<?php echo base_url() ?>sales/salescontract/print_view_idn';
      window.open(url+'?so='+ so,'_blank');
      $('#print_data').modal('hide');
  });  

  $('#print-eng').click(function(){
      event.preventDefault();
      var  so = '<?php echo encrypt_url($so['so'])?>';
      var url = '<?php echo base_url() ?>sales/salescontract/print_view_eng';
      window.open(url+'?so='+ so,'_blank');
      $('#print_data').modal('hide');

  });  

</script>