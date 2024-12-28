
<style>
    .error{
		border:  1px solid red !important;
	}
    /*create auto number di tabel*/
    .rlstable {
        counter-reset: row-num;
    }
    .rlstable tbody tr.num  {
        counter-increment: row-num;
    }

    .rlstable tr.num td:first-child::before {
        content: counter(row-num) ". ";
    }
    .rlstable tr.num td:first-child {
        text-align: center;
    }
    .min-width-80 {
        min-width : 80px;
    }
    .min-width-100 {
        min-width : 100px;
    }
    .min-width-120 {
        min-width : 120px;
        
    }

    .min-width-150 {
        min-width : 150px;
    }

    .min-width-200 {
        min-width : 200px;
        
    }

</style>
<form class="form-horizontal" id="form_add_item" name="form_add_item">
	<div class="col-md-6">
            <div class="form-group">
                <div class="col-md-12 col-xs-12">
                    <div class="col-12 col-md-12 col-lg-4"><label>Kode Produk</label></div>
                    <div class="col-12 col-md-12 col-lg-8">
                        <input type="text" class="form-control input-sm  " name="kode_produk" id="kode_produk" value="<?php echo htmlentities($kode_produk); ?>" readonly>
                    </div>
                </div>
                <div class="col-md-12 col-xs-12">
                    <div class="col-12 col-md-12 col-lg-4"><label>Nama Produk</label></div>
                    <div class="col-12 col-md-12 col-lg-8">
                        <input type="text" class="form-control input-sm  " name="nama_produk" id="nama_produk" value="<?php echo htmlentities($nama_produk); ?>" readonly>
                    </div>
                </div>
                <div class="col-md-12 col-xs-12">
                    <div class="col-12 col-md-12 col-lg-4"><label>Qty1 Target </label></div>
                    <div class="col-12 col-md-12 col-lg-4">
                        <input type="text" class="form-control input-sm text-right" name="qty" id="qty" value="<?php echo $data_produk->qty;?>" data-decimal="2" oninput="enforceNumberValidation(this)"readonly/>
                    </div>
                    <div class="col-12 col-md-12 col-lg-4">
                        <input type="text" class="form-control input-sm uom" name="uom_qty" id="uom_qty"  value="<?php echo $data_produk->uom;?>" readonly >
                    </div>
                </div>
		    </div>	
           
	</div>
    <div class="col-md-6">
    </div>
    <div class="row">
        <div class="col-md-12">
            <!-- Custom Tabs -->
            <ul class="nav nav-tabs">
                <li class="active"><a href="#tab_1" data-toggle="tab">Items</a></li>
            </ul>
            <div class="tab-content"><br>
                <div class="tab-pane active" id="tab_1">
                    <!-- Tabel  items-->
                    <div class="col-md-12 table-responsive">
                        <table class="table table-condesed table-hover table-responsive rlstable" id="items" > 
                            <thead>
                                <tr>
                                    <th class="style " width="20px  ">No.</th>
                                    <th class="style nowrap" width="200px">Lot</th>
                                    <th class="style nowrap" width="50px">Grade</th>
                                    <th class="style nowrap" width="100px" style="text-align: right;">Qty</th>
                                    <th class="style nowrap" width="80px" >Uom</th>
                                    <th class="style nowrap" width="100px" style="text-align: right;">Qty2</th>
                                    <th class="style nowrap" width="80px" >Uom</th>
                                    <th class="style" style="width: 100px;">Lbr Greige</th>
                                    <th class="style" style="width: 65px;">Uom.Lbr Grg</th>
                                    <th class="style" style="width: 100px;">Lbr Jadi</th>
                                    <th class="style" style="width: 65px;">Uom.Lbr Jadi</th>
                                    <th class="style nowrap">Reff Note</th>
                                    <th class="style" width="50px">#</th>
                                </tr>
                            </thead>
                            <tbody id="tbody_items"></tbody>
                            <tfoot>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th>Total</th>
                                    <th style="text-align: right;" id="total_tqty1">0</th>
                                    <th></th>
                                    <th style="text-align: right;" id="total_tqty2">0</th>
                                </tr>
                                <tr>
                                      <td colspan="6">
                                        <a href="javascript:void(0)" class="add-new-item"  onclick="tambah_baris(false,'items','','','','',`<?php echo $data_produk->uom;?>`,'',`<?php echo $data_produk->uom_2;?>`,'','','',`<?php echo $data_produk->uom_lebar_greige;?>`,'',`<?php echo $data_produk->uom_lebar_jadi;?>`)"><i class="fa fa-plus"></i> Tambah Data</a>
                                      </td>
                                </tr>
                            <tfoot>
                        </table>
                        <div id="example1_processing" class="table_processing" style="display: none">
                            Processing...
                        </div>
                    </div>
                    <!-- Tabel  -->
                </div>
            </div>
        </div>
                        
    </div>
</form>


<script>

    //fungsi panggil tambah_baris() ketika enter di qty
	function enter(e){
		if(e.keyCode === 13){
	        e.preventDefault(); 
            tambah_baris(false,'items','','','','',`<?php echo $data_produk->uom;?>`,'',`<?php echo $data_produk->uom_2;?>`,'','','',`<?php echo $data_produk->uom_lebar_greige;?>`,'',`<?php echo $data_produk->uom_lebar_jadi;?>`)
	    }
        total();
	}

   
	const dataSatuan = {
            allowClear: true,
            placeholder: "Pilih",
            ajax: {
                dataType: 'JSON',
                type: "POST",
                url: "<?php echo site_url('warehouse/penerimaanbarang/get_uom_select2')?>",
                data: function (params) {
                    return{
                        params: params.term,
                    };
                },
                processResults: function (data) {
                    var results = [];

                    $.each(data, function (index, item) {
                        results.push({
                            id: item.short,
                            text: item.short
                        });
                    });
                    return {
                        results: results
                    };
                },
                error: function (xhr, ajaxOptions, thrownError) {

                }
            }
    };

    const dataGrade = {
            allowClear: true,
            placeholder: "Pilih",
            ajax: {
                dataType: 'JSON',
                type: "POST",
                url: "<?php echo site_url('warehouse/penerimaanbarang/get_list_grade_select2')?>",
                data: function (params) {
                    return{
                        params: params.term,
                    };
                },
                processResults: function (data) {
                    var results = [];

                    $.each(data, function (index, item) {
                        results.push({
                            id: item.nama_grade,
                            text: item.nama_grade
                        });
                    });
                    return {
                        results: results
                    };
                },
            }
    };

    function validAngka(a){
        let tmp_char = '';
		let len      = a.value.length;
		let text     = a.value;
		for(let i = 0; i < len; i++){
			if(len > 1){
				var char = text.substring(i,i+1);
			}else{
				var char = text;
			}
			if(/^[0-9.]+$/.test(char)){
				char1 = char; 
				tmp_char += char1;
	    	}else{
				char.replace(/[^0-9.-]/, '')
			}
		}
		// alert(tmp_char);
		a.value = "";
		a.value = tmp_char;

        total();
	}

    function total(){
        var qty = 0;
		var qty = document.getElementsByName('txtqty[]');
		inx_qty  = qty.length-1;
		var tot_qty = 0;
		var qty_isi = 0;

		for(var i=0; i<=inx_qty; i++){
			if(qty[i].value!=''){
				qty_isi = qty[i].value;
			}else{
				qty_isi = 0;
			}
			tot_qty = tot_qty + parseFloat(qty_isi);
		}
        $('#total_tqty1').html(formatNumber(roundToTwo(tot_qty)));

        var qty2 = 0;
		var qty2 = document.getElementsByName('txtqty2[]');
		inx_qty2  = qty2.length-1;
		var tot_qty2 = 0;
		var qty2_isi = 0;

		for(var i=0; i<=inx_qty2; i++){
			if(qty2[i].value!=''){
				qty2_isi = qty2[i].value;
			}else{
				qty2_isi = 0;
			}
			tot_qty2 = tot_qty2 + parseFloat(qty2_isi);
		}
        $('#total_tqty2').html(formatNumber(roundToTwo(tot_qty2)));
    }

    function roundToTwo(num) {
        return +(Math.round(num + "e+2")  + "e-2");
    }

    function formatNumber(n) {
		return new Intl.NumberFormat('en-US').format(n);
	}

    // get data list item lot
    var kode = `<?php echo $kode; ?>`;
    $.ajax({
          url : '<?php echo site_url('warehouse/penerimaanbarang/get_items_lot') ?>',
          type: "POST",
          dataType : "JSON",
          data: {kode:kode},
          beforeSend: function(e) {
              $('#items tbody').remove();
              $("#btn-duplicate").hide();
              $("#example1_processing").css('display',''); 
          },
          success: function(data){
                           
                $('#items').append("<tbody id='tbody_items'></tbody>");
                $.each(data.record1, function(key, value) {
                    tambah_baris(true,'items',value.kode_produk,value.nama_produk, value.lot, value.qty, value.uom, value.qty2, value.uom2, value.grade, value.reff_note, value.lebar_greige, value.uom_lebar_greige, value.lebar_jadi, value.uom_lebar_jadi);
                });
                
                $("#example1_processing").css('display','none'); 
          },
          error: function (xhr, ajaxOptions, thrownError){
              alert('Error data');
              $("#example1_processing").css('display','none'); 
              alert(xhr.responseText);
          }

      });

    
    //untuk tambah baris
	function tambah_baris(data,table,kode_produk,nama_produk,lot,qty,uom,qty2,uom2,grade,reff_note,lebar_greige,uom_lebar_greige,lebar_jadi,uom_lebar_jadi){
		var lot_ = document.getElementsByName('txtlot[]');
		var inx_lot = lot_.length-1;
		var tambah = true;

        
        //cek Lot apa ada yg kosong
		$('.txtlot').each(function(index,value){
			if($(value).val().trim().length === 0){
    			alert_notify('fa fa-warning','Lot tidak boleh kosong !','danger',function(){});
		        $(value).addClass('error'); 
		   	    tambah = false;
			}else{
			    $(value).removeClass('error'); 
			}
		});	

        //cek Qty apa ada yg kosong
		$('.txtqty').each(function(index,value){
			if($(value).val().trim().length === 0 || parseFloat($(value).val()) === 0.00 ){
    			alert_notify('fa fa-warning','Qty tidak boleh kosong !','danger',function(){});
		        $(value).addClass('error'); 
		   	    tambah = false;
			}else{
			    $(value).removeClass('error'); 
			}
		});	

        //cek satuan apa ada yg kosong
		$('.txtuom').each(function(index,value){
			if($(value).val()==''){
    			alert_notify('fa fa-warning','Uom tidak boleh kosong !','danger',function(){});
		        $(value).addClass('error'); 
		   	    tambah = false;
			}else{
			    $(value).removeClass('error'); 
			}
		});	

        // //cek Qty apa ada yg kosong
		// $('.txtqty2').each(function(index,value){
		// 	if($(value).val().trim().length === 0 || parseFloat($(value).val()) === 0.00 ){
    	// 		alert_notify('fa fa-warning','Qty2 tidak boleh kosong !','danger',function(){});
		//         $(value).addClass('error'); 
		//    	    tambah = false;
		// 	}else{
		// 	    $(value).removeClass('error'); 
		// 	}
		// });	

        // //cek satuan apa ada yg kosong
		// $('.txtuom2').each(function(index,value){
		// 	if($(value).val()=='' || $(value).val() == null){
    	// 		alert_notify('fa fa-warning','Uom2 tidak boleh kosong !','danger',function(){});
        //         $(value).parents('td').find('span span.selection span.select2-selection').addClass('error'); 
		//    	    tambah = false;
		// 	}else{
        //         $(value).parents('td').find('span span.selection span.select2-selection').removeClass('error'); 

		// 	}
		// });	
		

		if(tambah){ 

            // uom1_default = `<?php echo $data_produk->uom; ?>`;
            // uom2_default = `<?php echo $data_produk->uom_2; ?>`;
            // uomlbrjadi  = `<?php echo  $data_produk->uom_lebar_jadi; ?>`;
		
		    html='<tr class="num">'
		    + '<td></td>'
		    //+ '<td></td>'
		    +  '<td class="min-width-150"><input type="text" name="txtlot[]"  id="txtlot" class="form-control input-sm txtlot min-width-150" onkeypress="enter(event);" value="'+lot+'"></td>'		   
            + '<td class="min-width-100"><select class="form-control input-sm grade min-width-100" name="grade" id="grade" style="width:100% !important;"></select></td>'
		    + '<td class="min-width-80"><input type="text" name="txtqty[]"  id="txtqty" class="form-control input-sm min-width-80 text-right txtqty"   onkeypress="enter(event);"  onkeyup="validAngka(this)" data-decimal="2" oninput="enforceNumberValidation(this)" value="'+qty+'" ></td>'
            + '<td class="min-width-80"><input type="text" name="txtuom"  id="txtuom" class="form-control input-sm min-width-80 txtuom" value="" readonly style="width:100% !important;"></td>'
		    + '<td class="min-width-80"><input type="text" name="txtqty2[]" id="txtqty2" class="form-control input-sm min-width-80 text-right txtqty2"  onkeypress="enter(event);"   onkeyup="validAngka(this)"  data-decimal="2" oninput="enforceNumberValidation(this)" value="'+qty2+'" ></td>'
            + '<td class="min-width-80"><input type="text" name="txtuom2"  id="txtuom2" class="form-control input-sm min-width-80 txtuom2"  readonly style="width:100% !important;"></td>'
            + '<td class="min-width-100"><input type="text" name="txt_lebar_greige"  id="txt_lebar_greige" class="form-control input-sm min-width-100 txt_lebar_greige" style="width:100% !important;" onkeypress="enter(event);" value="'+lebar_greige+'" ></td>'
            + '<td class="min-width-100"><select type="text" name="txt_uom_lebar_greige"  id="txt_uom_lebar_greige" class="form-control input-sm min-width-80 txt_uom_lebar_greige" style="width:100% !important;"></select></td>'
            + '<td class="min-width-100"><input type="text" name="txt_lebar_jadi"  id="txt_lebar_jadi" class="form-control input-sm min-width-100 txt_lebar_jadi" style="width:100% !important;" onkeypress="enter(event);" value="'+lebar_jadi+'" ></td>'
            + '<td class="min-width-100"><select type="text" name="txt_uom_lebar_jadi"  id="txt_uom_lebar_jadi" class="form-control input-sm min-width-80 txt_uom_lebar_jadi" style="width:100% !important;"></select></td>'
            + '<td class="min-width-150"><input type="text" name="txt_reff_note"  id="txt_reff_note" class="form-control input-sm min-width-150 txt_reff_note" style="width:100% !important;" onkeypress="enter(event);" value="'+reff_note+'" ></td>'
		    + '<td><button type="button" class="btn btn-xs btn-danger" onclick="delRow(this);"    data-toggle="tooltip" title="Hapus Data"><i class="fa fa-trash" </i> </a></td>'
            + '</tr>';
		    $('#items tbody').append(html);
	        lot_[inx_lot+1].focus();
            
		    $(".grade").select2(dataGrade);
		    $(".txt_uom_lebar_jadi").select2(dataSatuan);
		    $(".txt_uom_lebar_greige").select2(dataSatuan);

			// var produk = document.getElementsByName('txtproduk');
		    // var inx_txtproduk = produk.length-1;

            var uom1 = document.getElementsByName('txtuom');
		    var inx_uom_1 = uom1.length-1;
            let satuan = uom;
            $(".txtuom").eq(inx_uom_1).val(satuan)

            var uom22 = document.getElementsByName('txtuom2');
		    var inx_uom_2 = uom22.length-1;
            let satuan2 = uom2;
            $(".txtuom2").eq(inx_uom_2).val(satuan2)

            var txt_grade = document.getElementsByName('grade');
		    var inx_txt_grade = txt_grade.length-1;
            $newOption = new Option(grade, grade, true, true);
            $(".grade").eq(inx_txt_grade).append($newOption).trigger('change');
		    
            var uom_lebar_jadi_txt = document.getElementsByName('txt_uom_lebar_jadi');
		    var inx_uom_lebar_jadi = uom_lebar_jadi_txt.length-1;
            let satuan_lbr_jadi =uom_lebar_jadi; 
            $newOption = new Option(satuan_lbr_jadi, satuan_lbr_jadi, true, true);
            $(".txt_uom_lebar_jadi").eq(inx_uom_lebar_jadi).append($newOption).trigger('change');

            var uom_lebar_greige_txt = document.getElementsByName('txt_uom_lebar_greige');
		    var inx_uom_lebar_greige = uom_lebar_greige_txt.length-1;
            let satuan_lbr_grg = uom_lebar_greige;
            $newOption = new Option(satuan_lbr_grg, satuan_lbr_grg, true, true);
            $(".txt_uom_lebar_greige").eq(inx_uom_lebar_greige).append($newOption).trigger('change');

		}
	}

    function delRow(r){	  
	    var i = r.parentNode.parentNode.rowIndex;
	  	document.getElementById("items").deleteRow(i);
        total();
	}

    // validasi decimal
    function enforceNumberValidation(ele) {
            if ($(ele).data('decimal') != null) {
                // found valid rule for decimal
                var decimal = parseInt($(ele).data('decimal')) || 0;
                var val = $(ele).val();
                if (decimal > 0) {
                    var splitVal = val.split('.');
                    if (splitVal.length == 2 && splitVal[1].length > decimal) {
                        // user entered invalid input
                        $(ele).val(splitVal[0] + '.' + splitVal[1].substr(0, decimal));
                    }
                } else if (decimal == 0) {
                    // do not allow decimal place
                    var splitVal = val.split('.');
                    if (splitVal.length > 1) {
                        // user entered invalid input
                        $(ele).val(splitVal[0]); // always trim everything after '.'
                    }
                }
            }
    }



    //simpan data 
	$("#btn-tambah").unbind( "click" );
	$("#btn-tambah").off("click").on("click",function(e) {
		e.preventDefault();

        var valid = true;
		var btn   = $(this);
		
        //cek lot apa ada yg kosong
		$('.txtlot').each(function(index,value){
			if($(value).val().trim().length ===0){
    			alert_notify('fa fa-warning','Lot tidak boleh kosong !','danger',function(){});
                $(value).addClass('error'); 
                valid = false;
			}else{
			    $(value).removeClass('error'); 
			}
		});	

        //cek Qty apa ada yg kosong
		$('.txtqty').each(function(index,value){
			if($(value).val().trim().length ===0 || parseFloat($(value).val()) === 0.00 ){
    			alert_notify('fa fa-warning','Qty tidak boleh kosong !','danger',function(){});
		        $(value).addClass('error'); 
                valid = false;
			}else{
			    $(value).removeClass('error'); 
			}
		});	

        //cek satuan apa ada yg kosong
		$('.txtuom').each(function(index,value){
			if($(value).val()=='' ||  $(value).val() == null){
    			alert_notify('fa fa-warning','Uom tidak boleh kosong !','danger',function(){});
		        $(value).addClass('error'); 
		   	    valid = false;
			}else{
			    $(value).removeClass('error'); 
			}
		});	

        //cek Qty apa ada yg kosong
		// $('.txtqty2').each(function(index,value){
		// 	if($(value).val().trim().length ===0 || parseFloat($(value).val()) === 0.00 ){
                    // alert_notify('fa fa-warning','Qty2 tidak boleh kosong !','danger',function(){});
		//         $(value).addClass('error'); 
		//    	    valid = false;
		// 	}else{
		// 	    $(value).removeClass('error'); 
		// 	}
		// });	

        // //cek satuan apa ada yg kosong
		// $('.txtuom2').each(function(index,value){
		// 	if($(value).val()=='' || $(value).val()== null){
                  // alert_notify('fa fa-warning','Uom2 tidak boleh kosong !','danger',function(){});
        //         $(value).parents('td').find('span span.selection span.select2-selection').addClass('error');
		//    	    valid = false;
		// 	}else{
        //         $(value).parents('td').find('span span.selection span.select2-selection').removeClass('error');
		// 	}
		// });	


        if(valid){

			var i = 0;
			var arr = new Array();
            var kode_produk = "<?php echo $kode_produk; ?>";
            var nama_produk = `<?php echo $nama_produk; ?>`;
            var origin_prod = `<?php echo $origin_prod; ?>`;
				
			$(".txtqty").each(function(index, element) {
				if ($(element).val()!=="") {
					
					arr.push({
							//0 : no++,
                            kode_produk : kode_produk,
                            nama_produk : nama_produk,
							lot 		:$(element).parents("tr").find("#txtlot").val(),
							grade 		:$(element).parents("tr").find("#grade").val(),
							qty 		:$(element).parents("tr").find("#txtqty").val(),
							uom 		:$(element).parents("tr").find("#txtuom").val(),
							qty2 		:$(element).parents("tr").find("#txtqty2").val(),
							uom2 		:$(element).parents("tr").find("#txtuom2").val(),
                            lebar_greige:$(element).parents("tr").find("#txt_lebar_greige").val(),
							uom_lebar_greige:$(element).parents("tr").find("#txt_uom_lebar_greige").val(),
							lebar_jadi 	    :$(element).parents("tr").find("#txt_lebar_jadi").val(),
							uom_lebar_jadi  :$(element).parents("tr").find("#txt_uom_lebar_jadi").val(),
							reff_note 		:$(element).parents("tr").find("#txt_reff_note").val(),
					});
				}
			}); 

			// alert (JSON.stringify(arr));
            if(kode_produk.length == 0){
                alert_notify('fa fa-warning','Data Produk Kosong !','danger',function(){});
			}else{
                var kode = `<?php echo $kode; ?>`;
                var dept_id = `<?php echo $deptid; ?>`;

				btn.button('loading');
                please_wait(function(){});
                var data = {
                    kode : kode,
                    kode_produk: kode_produk,
                    nama_produk: nama_produk,
                    origin_prod: origin_prod,
                    dept_id    : dept_id,
                    data_lot   : JSON.stringify(arr),
                };
                $.ajax({
                    dataType: "JSON",
                    type: "POST",
                    url: "<?php echo site_url('warehouse/penerimaanbarang/save_detail_add_quant_penerimaan_modal')?>",
                    data: data,
                    success: function (data) {
                        if(data.sesi=='habis'){
                            //alert jika session habis
                            alert_modal_warning(data.message);
                            window.location.replace('../index');
                            $('#btn-tambah').button('reset');
                            unblockUI( function(){});
                        }else if(data.status == 'kosong'){
                            //var pesan = "Lot "+data.lot+ " Sudah diinput !"       
                            alert_modal_warning(data.message);
                            unblockUI( function(){});
                            $('#btn-tambah').button('reset');

                        }else{
                            $("#table_prod").load(location.href + " #table_prod");
                            $("#table_items").load(location.href + " #table_items");
                            $("#status_bar").load(location.href + " #status_bar");
                            $("#tab_3").load(location.href + " #tab_3");
                            $("#foot").load(location.href + " #foot");
                            $('#tambah_data').modal('hide');
                            $('#btn-tambah').button('reset');
                            unblockUI( function() {
                                setTimeout(function() { alert_notify(data.icon,data.message,data.type,function(){}); }, 1000);
                            });
                        }
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        alert(xhr.responseText);
						btn.button('reset');
                    },
                    complete: function (data) {
						btn.button('reset');
                        unblockUI( function(){});
                    }
                });
            }
        } 

    });

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

</script>
