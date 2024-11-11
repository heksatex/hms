
<form class="form-horizontal">
    <div class="form-group">
      <div class="col-md-12 col-xs-12">
        <div class="col-xs-12">
          <center><button type="button" id="print-knitting" class="btn btn-default btn-sm"> <i class="fa fa-barcode"></i> Tricot / Jacquard</button></center>
        </div> 
         <div class="col-xs-12">
          <center><button type="button" id="print-gjd" class="btn btn-default btn-sm"> <i class="fa fa-barcode"></i> Gudang Jadi</button></center>
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


  $("#print-gjd").unbind( "click" );
	$("#print-gjd").off("click").on("click",function(e) {
      e.preventDefault();
      var quant_id = '<?php echo $quant_id?>';
      if(quant_id == '' ){
        alert_modal_warning('Maaf, Anda tidak bisa Print Barcode dikarenakan Lot nya Kosong !');
      }else{

         $(".print_data").html('<center><h5><img src="<?php echo base_url('dist/img/ajax-loader.gif') ?> "/><br>Please Wait...</h5></center>');
            $("#print_data").modal({
                show: true,
                backdrop: 'static'
            });
            $("#print_data .modal-dialog .modal-content .modal-footer .btn-print-barcode").remove();
            $('.modal-title').text('Pilih Desain Barcode dan K3L ');

            $.post('<?php echo site_url()?>warehouse/stockquants/print_modal',
            { quant_id:quant_id,},
                function(html){
                    setTimeout(function() {$(".print_data").html(html);  },1000);
                    $("#print_data .modal-dialog .modal-content .modal-footer").prepend('<button class="btn btn-default btn-sm btn-print-barcode" id="btn-print-barcode" name="btn-print" >Print</button>');

                }   
            );
      }
  });

</script>