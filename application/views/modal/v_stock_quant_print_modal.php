
<form class="form-horizontal">
    <div class="form-group">
      <div class="col-md-12 col-xs-12">
        <div class="col-xs-12">
          <center><button type="button" id="print-knitting" class="btn btn-default btn-sm"> <i class="fa fa-barcode"></i> Tricot / Jacquard</button></center>
        </div>  
      </div>
    </div>
  <div class="form-group">
  </div>
</form>

<script type="text/javascript">

  $('#print-knitting').click(function(){
      event.preventDefault();
      var quant_id = '<?php echo encrypt_url($quant_id)?>';
      var url      = '<?php echo base_url() ?>warehouse/stockquants/print_knitting';
      window.open(url+'?quant_id='+ quant_id,'_blank');
      $('#print_data').modal('hide');
  });  

</script>