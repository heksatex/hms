
<form class="form-horizontal" id="form_add_batch" name="form_add_batch">
	<div class="form-group">
		<div class="col-md-12">
			<div class="col-md-12 col-xs-12">
                <div class="col-xs-4"><label>Departemen</label></div>
                <div class="col-xs-8">
                    <select type="text" class="form-control input-sm" name="departemen" id="departemen"  style="width:100% !important;" >
                    </select>
                </div>                                    
            </div>
			<div class="col-md-12 col-xs-12">
                <div class="col-xs-4"><label>Koreksi Apa</label></div>
                <div class="col-xs-8">
                    <select type="text" class="form-control input-sm select2" name="koreksi_apa" id="koreksi_apa" style="width:100% !important;" >
                            <option value=''></option>
                            <option value='mo'>MO / MG</option>
                            <option value='out'>Pengiriman</option>
                            <option value='in'>Penerimaan</option>
                    </select>
                </div>                                    
            </div>
			<span id="show_tipe" style="display: none;">
                <div class="col-md-12 col-xs-12">
                    <div class="col-xs-4"><label>Tipe</label></div>
                    <div class="col-xs-8">
                        <select type="text" class="form-control input-sm select2" name="tipe" id="tipe" style="width:100% !important;" >
                                <option value=''></option>
                                <option value='con'>Bahan Baku</option>
                                <option value='prod'>Barang Jadi</option>
                        </select>
                    </div>                                    
                </div>                                    
            </span>
            <div class="col-md-12 col-xs-12">
                <div class="col-xs-4"><label>Kode</label></div>
                <div class="col-xs-8">
                    <select type="text" class="form-control input-sm" name="kode" id="kode"  style="width:100% !important;" >
                    </select>
                </div>                                    
            </div>
			
		</div>		
	</div>
</form>



<script type="text/javascript">
    $(function () {
        // untuk focus after select2 close
        $(document).on('focus', '.select2', function (e) {
            if (e.originalEvent) {
                var s2element = $(this).siblings('select');
                s2element.select2('open');

                // Set focus back to select2 element on closing.
                s2element.on('select2:closing', function (e) {
                    s2element.select2('focus');
                });
            }
        });

        $('.select2').select2({
            placeholder: "Pilih",
            clear : true,
        });

        $(".select2").on('change', function (e) {
            $("#kode").empty().trigger('change')
        });

        $("#koreksi_apa").on('change', function (e) {
            if($(this).val() == 'mo'){
            $('#show_tipe').show();
            }else{
            $('#show_tipe').hide();
            }
        });

        $("#departemen").on('select2:unselect', function (e) {
            $("#kode").empty().trigger('change')
        });

        $("#departemen").on('change', function (e) {
            $("#kode").empty().trigger('change')
        });

        //select 2 Departemen
        $('#departemen').select2({
        allowClear: true,
        placeholder: "Pilih",
        ajax:{
                dataType: 'JSON',
                type : "POST",
                url : "<?php echo base_url();?>ppic/koreksimundur/get_departement_select2",
                //delay : 250,
                data : function(params){
                return{
                    nama:params.term,
                };
                }, 
                processResults:function(data){
                var results = [];
                $.each(data, function(index,item){
                    results.push({
                        id:item.kode,
                        text:item.nama
                    });
                });
                return {
                    results:results
                };
                },
                error: function (xhr, ajaxOptions, thrownError){
                //alert('Error data');
                //alert(xhr.responseText);
                }
        }
        });

        //select 2 kode
        $('#kode').select2({
            allowClear: true,
            placeholder: "Pilih",
            ajax:{
                    dataType: 'JSON',
                    type : "POST",
                    url : "<?php echo base_url();?>ppic/koreksimundur/get_kode_transaksi",
                    //delay : 250,
                    data : function(params){
                    return{
                        koreksi_apa:$('#koreksi_apa').val(),
                        departemen : $('#departemen').val(),
                        nama       :params.term,
                    };
                    }, 
                    processResults:function(data){
                    var results = [];
                    $.each(data, function(index,item){
                        results.push({
                            id:item.kode,
                            text:item.kode
                        });
                    });
                    return {
                        results:results
                    };
                    },
                    error: function (xhr, ajaxOptions, thrownError){
                    //alert('Error data');
                    //alert(xhr.responseText);
                    }
            }
        });
    });
</script>
