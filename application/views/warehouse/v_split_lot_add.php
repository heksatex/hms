<!DOCTYPE html>
<html>
<head>
  <?php $this->load->view("admin/_partials/head.php") ?>
  <style>
        .min_width_100{
            min-width:100px;
        }
        .min_width_150{
            min-width:150px;
        }
  </style>
</head>

<body class="hold-transition skin-black fixed sidebar-mini" onload="get_default()"> 
<!-- Site wrapper -->
<div class="wrapper">

  <!-- main -header -->
  <header class="main-header">
    <?php $this->load->view("admin/_partials/main-menu.php") ?>
    <?php
      $data['deptid']     = $id_dept;
      $this->load->view("admin/_partials/topbar.php",$data)
    ?>
  </header>

  <!-- Menu Side Bar -->
  <aside class="main-sidebar">
  <?php $this->load->view("admin/_partials/sidebar.php") ?>
  </aside>

  <!-- Content Wrapper-->
  <div class="content-wrapper">
    <!-- Content Header (Status - Bar) -->
    <section class="content-header">
     <?php $this->load->view("admin/_partials/statusbar.php") ?>
    </section>

    <!-- Main content -->
    <section class="content">

      <!--  box content -->
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title">Form Split Lot </h3>
        </div>

        <div class="box-body">
            <form class="form-horizontal">
             
                <div class="form-group">

                    <div class="col-md-6">
                        <div class="col-md-12 col-xs-12">
                            <div class="col-xs-4"><label>Kode  </label></div>
                            <div class="col-xs-8">
                                <input type="text" class="form-control input-sm" name="kode" id="kode"  readonly="readonly" value="<?php echo $kode_split;?>"/>                    
                            </div>                                    
                        </div>
                        <div class="col-md-12 col-xs-12">
                            <div class="col-xs-4"><label>Tanggal dibuat</label></div>
                            <div class="col-xs-8 col-md-8">
                                <input type='text' class="form-control input-sm " name="tgl_buat" id="tgl_buat" readonly="readonly" value="<?php echo date('Y-m-d H:i:s')?>"  />
                            </div>                                    
                        </div>

                        <div class="col-md-12 col-xs-12" >
                            <div class="col-xs-4"><label>Departemen</label></div>
                            <div class="col-xs-8">
                                <select class="form-control input-sm" name="departemen" id="departemen">
                                <option value="">Pilih Departemen</option>
                                <?php foreach ($warehouse as $row) { ?>
                                    <option value='<?php echo $row->kode; ?>'><?php echo $row->nama;?></option>
                                <?php }?>
                                </select>
                            </div>                                    
                        </div>
                        <div class="col-md-12 col-xs-12" style="padding-bottom:10px;">
                            <div class="col-xs-4"><label>Note </label></div>
                            <div id="ta" class="col-xs-8">
                                <textarea class="form-control input-sm" name="note" id="note" ></textarea>
                            </div>                                    
                        </div>
                    </div>

                    <div class="col-md-6" >
                        
                            <div class="col-md-12 col-xs-12 " style="padding-bottom:10px;">
                                <div class="col-xs-12 col-md-4 col-sm-4 " style>
                                    <button type="button" class="btn btn-primary btn-sm" id="btn-import-produk" >Tampilkan Barang di Lokasi</button>            
                                </div>
                            </div> 


                            <div class="col-md-12 col-xs-12">
                                <div class="col-xs-4"><label>Kode Produk  </label></div>
                                <div class="col-xs-8">
                                    <input type="hidden" class="form-control input-sm" name="quant_id" id="quant_id" readonly >  
                                    <input type="text" class="form-control input-sm" name="kode_produk" id="kode_produk" readonly >                    
                                </div>                                    
                            </div>

                            <div class="col-md-12 col-xs-12">
                                <div class="col-xs-4"><label>Nama Produk  </label></div>
                                <div class="col-xs-8">
                                    <input type="text" class="form-control input-sm" name="nama_produk" id="nama_produk" readonly>                    
                                </div>                                    
                            </div>

                            <div class="col-md-12 col-xs-12">
                                <div class="col-xs-4"><label>Barcode/Lot  </label></div>
                                <div class="col-xs-8">
                                    <input type="text" class="form-control input-sm" name="lot" id="lot" readonly >                    
                                </div>                                    
                            </div>

                            <div class="col-md-12 col-xs-12">
                                <div class="col-xs-4"><label>Qty1  </label></div>
                                <div class="col-xs-5">
                                    <input type="text" class="form-control input-sm" name="qty" id="qty" readonly>                    
                                </div> 
                                <div class="col-xs-3">
                                    <input type="text" class="form-control input-sm" name="uom_qty" id="uom_qty"readonly >                    
                                </div>                                    
                            </div>

                            <div class="col-md-12 col-xs-12">
                                <div class="col-xs-4"><label>Qty2  </label></div>
                                <div class="col-xs-5">
                                    <input type="text" class="form-control input-sm" name="qty2" id="qty2" readonly>                    
                                </div> 
                                <div class="col-xs-3">
                                    <input type="text" class="form-control input-sm" name="uom_qty2" id="uom_qty2" readonly>                    
                                </div>                                    
                            </div>

                            <span id="show_qty_jual" style="display: none;">
                                <div class="col-md-12 col-xs-12">
                                    <div class="col-xs-4"><label>Corak Remark  </label></div>
                                    <div class="col-xs-8">
                                        <input type="text" class="form-control input-sm" name="corak_remark" id="corak_remark" readonly>                    
                                    </div>                                    
                                </div>
                                <div class="col-md-12 col-xs-12">
                                    <div class="col-xs-4"><label>Warna Remark  </label></div>
                                    <div class="col-xs-8">
                                        <input type="text" class="form-control input-sm" name="warna_remark" id="warna_remark" readonly>                    
                                    </div>                                    
                                </div>
                                <div class="col-md-12 col-xs-12">
                                    <div class="col-xs-4"><label>Qty1 Jual  </label></div>
                                    <div class="col-xs-5">
                                        <input type="text" class="form-control input-sm" name="qty_jual" id="qty_jual" readonly>                    
                                    </div> 
                                    <div class="col-xs-3">
                                        <input type="text" class="form-control input-sm" name="uom_qty_jual" id="uom_qty_jual"readonly >                    
                                    </div>                                    
                                </div>

                                <div class="col-md-12 col-xs-12">
                                    <div class="col-xs-4"><label>Qty2 Jual </label></div>
                                    <div class="col-xs-5">
                                        <input type="text" class="form-control input-sm" name="qty2_jual" id="qty2_jual" readonly>                    
                                    </div> 
                                    <div class="col-xs-3">
                                        <input type="text" class="form-control input-sm" name="uom_qty2_jual" id="uom_qty2_jual" readonly>                    
                                    </div>                                    
                                </div>
                                <div class="col-md-12 col-xs-12">
                                    <div class="col-xs-4"><label>Lebar Jadi </label></div>
                                    <div class="col-xs-5">
                                        <input type="text" class="form-control input-sm" name="lebar_jadi" id="lebar_jadi" readonly>                    
                                    </div> 
                                    <div class="col-xs-3">
                                        <input type="text" class="form-control input-sm" name="uom_lebar_jadi" id="uom_lebar_jadi" readonly>                    
                                    </div>                                    
                                </div>
                            </span>

                    </div>

                </div>
            
            </form>

            <div class="row">
              <div class="col-md-12">
                <!-- Custom Tabs -->
                <div class="">
                  <ul class="nav nav-tabs " >
                    <li class="active"><a href="#tab_1" data-toggle="tab">Split Items</a></li>
                  </ul>
                  <div class="tab-content over"><br>
                    <div class="tab-pane active" id="tab_1">

                      <!-- Tabel  -->
                      <div class="col-md-12 table-responsive over">
                        <table class="table table-condesed table-hover rlstable  over" width="100%" id="table_items" >
                          <thead>                          
                            <tr>
                              <th class="style" width="50px">No.</th>
                              <th class="style" style="width:100px;" >Qty1</th>
                              <th class="style" width="80px">Uom</th>
                              <th class="style" style="width:100px;" >Qty2</th>
                              <th class="style" width="80px">Uom2</th>
                              <th class="style" width="100px">Lot Baru</th>
                              <th class="style" width="50px"></th>
                            </tr>
                          </thead>
                          <tbody>
                           
                          </tbody>
                          <tfoot>
                            <tr>
                              <td colspan="8">
                                <a href="javascript:void(0)" onclick="tambah_baris()"><i class="fa fa-plus"></i> Tambah Data</a>
                              </td>
                            </tr>
                          <tfoot>
                        </table>
                      </div>
                      <!-- Tabel  -->
                    </div>
                    <!-- /.tab-pane -->
              
                  </div>
                  <!-- /.tab-content -->
                </div>
                <!-- nav-tabs-custom -->
              </div>
              <!-- /.col -->
            </div>

        </div>
        <!-- /.box-body -->
      </div>
      <!-- /.box -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <footer class="main-footer">
   <?php $this->load->view("admin/_partials/modal.php") ?>

   <?php $this->load->view("admin/_partials/footer.php") ?>
  </footer>

</div>

<?php $this->load->view("admin/_partials/js.php") ?>
<style type="text/css">
	.error{
		border:  1px solid red;
	}  
</style>

<script type="text/javascript">

    function get_default(){
        $("#departemen").prop('selectedIndex',0)
    }
    
    // validasi decimal/ onlynumber
	function enforceNumberValidation(ele) {
            if ($(ele).data('decimal') != null) {
                // found valid rule for decimal
                var decimal = parseInt($(ele).data('decimal')) || 0;
                var val = $(ele).val();
                if (decimal > 0) {
					var value   = ele.value;
					var numbers = value.replace(/[^0-9.-]/g, '');
                    var splitVal = numbers.split('.');
                    if (splitVal.length == 2 && splitVal[1].length > decimal) {
                        // user entered invalid input
						splitVal0  = splitVal[0].replace(/[^0-9.-]/g, '');
						splitVal1  = splitVal[1].substr(0, decimal).replace(/[^0-9]/g, "");
                        $(ele).val(splitVal0 + '.' + splitVal1);
                    }else{
						$(ele).val(numbers)
					}
                } else if (decimal == 0) {

					var value = ele.value;
					var numbers = value.replace(/[^0-9]/g, "");
					ele.value = numbers;
                }
            }
    }


    $(document).on("change", "#departemen", function(){
        checkTampil();
    });

    function checkTampil(){
        var dept = $('#departemen').val();
        if($('#departemen').val() != ''){
            if(dept == 'GJD'){
                $("#show_qty_jual").show();

                $('#table_items thead tr').remove();
                var row = '<tr><th class="style" width="50px">No.</th>'
                        +'<th class="style" style="width:150px;" >Corak Remark</th>'
                        +'<th class="style" style="width:150px;" >Warna Remark</th>'
                        +'<th class="style" style="width:100px;" >Qty1</th>'
                        +'<th class="style" width="80px">Uom</th>'
                        +'<th class="style" style="width:100px;" >Qty2</th>'
                        +'<th class="style" width="80px">Uom2</th>'
                        +'<th class="style" style="width:100px;" >Qty1 Jual</th>'
                        +'<th class="style" width="80px">Uom Jual</th>'
                        +'<th class="style" style="width:100px;" >Qty2 Jual</th>'
                        +'<th class="style" width="80px">Uom2 Jual</th>'
                        +'<th class="style" style="width:100px;" >Lbr Jadi</th>'
                        +'<th class="style" width="80px">Uom Lbr.Jadi</th>'
                        +'<th class="style" width="100px">Lot Baru</th>'
                        +'<th class="style" width="50px"></th><tr>';
                $('#table_items thead').append(row);
            }else{
                $("#show_qty_jual").hide();
                $('#table_items thead tr').remove();
                var row = '<tr><th class="style" width="50px">No.</th>'
                        +'<th class="style" style="width:100px;" >Qty1</th>'
                        +'<th class="style" width="80px">Uom</th>'
                        +'<th class="style" style="width:100px;" >Qty2</th>'
                        +'<th class="style" width="80px">Uom2</th>'
                        +'<th class="style" width="100px">Lot Baru</th>'
                        +'<th class="style" width="50px"></th><tr>';
                $('#table_items thead').append(row);
            }

            $('#qty_jual').val('');
            $('#uom_qty_jual').val('');
            $('#qty2_jual').val('');
            $('#uom_qty2_jual').val('');

            $('#txtlot').val('');
            $('#quant_id').val('');
            $('#lot').val('');
            $('#kode_produk').val('');
            $('#nama_produk').val('');
            $('#qty').val('');
            $('#uom_qty').val('');
            $('#qty2').val('');
            $('#uom_qty2').val('');            
            $("#table_items tbody tr").remove();

        }else{
            $("#show_qty_jual").hide();
        }
    }

    function alert_scan(message,field){
      var dialog = bootbox.dialog({
          message: message,
          closeButton: false,
          title: "<font color='red'><i class='glyphicon glyphicon-alert'></i></font> Warning !",
          buttons: {
              confirm: {
                  label: 'Ok',
                  className: 'btn-primary btn-sm',
                  callback : function() {
                    $('.bootbox').modal('hide');
                    $('#'+field).focus();
                  }
              },
          },
      });
      dialog.init(function(){
        dialog.find([type='button']).focus();
      });
    }


    //Tampilkan semua barang di lokasi (modal)
    $('#btn-import-produk').click(function(){
        // $('#btn-import-produk').button('loading');
        // $('#btn-import-produk').button('reset');
        var departemen = $('#departemen').val();
        if(departemen == ''){
            alert_scan('Departemen Harus dipilih ! ','departemen');
            // document.getElementById('departemen').focus();
        }else{
            $("#view_data").modal({
                show: true,
                backdrop: 'static'
            })
                $(".view_body").html('<center><h5><img src="<?php echo base_url('dist/img/ajax-loader.gif') ?> "/><br>Please Wait...</h5></center>');
                $('.modal-title').text('Pilih Produk Untuk di Split');
                $.post('<?php echo site_url()?>warehouse/splitlot/import_produk',
                {departemen : departemen},
                function(html){
                    setTimeout(function() {$(".view_body").html(html); });
                }   
            );
        }
    });


    $(document).on('click', '.pilih', function (e) {
      document.getElementById("quant_id").value = $(this).attr('quant_id');
      document.getElementById("kode_produk").value = $(this).attr('kode_produk');
      document.getElementById("nama_produk").value = $(this).attr('nama_produk');
      document.getElementById("lot").value = $(this).attr('lot');
      document.getElementById("qty").value = $(this).attr('qty');
      document.getElementById("uom_qty").value = $(this).attr('uom');
      document.getElementById("qty2").value = $(this).attr('qty2');
      document.getElementById("uom_qty2").value = $(this).attr('uom2');
      document.getElementById("qty_jual").value = $(this).attr('qty_jual');
      document.getElementById("uom_qty_jual").value = $(this).attr('uom_jual');
      document.getElementById("qty2_jual").value = $(this).attr('qty2_jual');
      document.getElementById("uom_qty2_jual").value = $(this).attr('uom2_jual');
      document.getElementById("corak_remark").value = $(this).attr('corak_remark');
      document.getElementById("warna_remark").value = $(this).attr('warna_remark');
      document.getElementById("lebar_jadi").value = $(this).attr('lebar_jadi');
      document.getElementById("uom_lebar_jadi").value = $(this).attr('uom_lebar_jadi');
      $('#view_data').modal('hide');
      $("#table_items tbody tr").remove();
    });


    function enter(e){
		if(e.keyCode === 13){
	        e.preventDefault(); 
	        tambah_baris(); //panggil fungsi tambah baris
	    }
	}

    function tambah_baris(){
        var tambah = true;
        var dept   = $("#departemen").val();

        tbl_uom_jual_value = "";
        tbl_uom2_jual_value = "";
        tbl_warna_remark_value = "";
        tbl_warna_remark_value = "";
        tbl_lebar_jadi_value = "";
        tbl_uom_lebar_jadi_value = "";

        tbl_uom_value = $('#uom_qty').val();
        tbl_uom2_value = $('#uom_qty2').val();
        tbl_corak_remark_value = $('#corak_remark').val();
        tbl_warna_remark_value = $('#warna_remark').val();
        tbl_lebar_jadi_value   = $('#lebar_jadi').val();
        tbl_uom_lebar_jadi_value   = $('#uom_lebar_jadi').val();

        //cek lot apa ada yg kosong
		$('.tbl_qty').each(function(index,value){
            if(dept == 'GJD'){
                qty_value = parseFloat($('#qty').val());
                if($(value).val()=='' && qty_value ){
                  alert_notify('fa fa-warning','Qty1 tidak boleh kosong !','danger',function(){});
                  $(value).addClass('error'); 
                     tambah = false;
                }else{
                  $(value).removeClass('error'); 
                }
            }else{
                if($(value).val()=='' ){
                  alert_notify('fa fa-warning','Qty1 tidak boleh kosong !','danger',function(){});
                  $(value).addClass('error'); 
                     tambah = false;
                }else{
                  $(value).removeClass('error'); 
                }
            }
		});	

        $('.tbl_qty2').each(function(index,value){
            qty2_stock = parseFloat($("#qty2").val());
			if($(value).val() === "" && (qty2_stock !==0) ){
              alert_notify('fa fa-warning','Qty2 tidak boleh kosong !','danger',function(){});
		      $(value).addClass('error'); 
		   	  tambah = false;
			}else{
			  $(value).removeClass('error'); 
			}
		});	

        if(dept == 'GJD'){

            $('.tbl_corak_remark').each(function(index,value){
                if($(value).val() === "" ){
                    alert_notify('fa fa-warning','Corak Remark tidak boleh kosong !','danger',function(){});
                    $(value).addClass('error'); 
                    tambah = false;
                }else{
                    $(value).removeClass('error'); 
                }
            });	

            $('.tbl_warna_remark').each(function(index,value){
                if($(value).val() === "" && $('#warna_remark').val() !='' ){
                    alert_notify('fa fa-warning','Warna Remark tidak boleh kosong !','danger',function(){});
                    $(value).addClass('error'); 
                    tambah = false;
                }else{
                    $(value).removeClass('error'); 
                }
            });

            $('.tbl_qty_jual').each(function(index,value){
                if($(value).val() === "" ){
                    alert_notify('fa fa-warning','Qty Jual tidak boleh kosong !','danger',function(){});
                    $(value).addClass('error'); 
                    tambah = false;
                }else{
                    $(value).removeClass('error'); 
                }
            });	

            $('.tbl_uom_jual').each(function(index,value){
                if($(value).val() === "" ){
                    alert_notify('fa fa-warning','Uom Qty Jual tidak boleh kosong !','danger',function(){});
                    $(value).addClass('error'); 
                    tambah = false;
                }else{
                    $(value).removeClass('error'); 
                }
            });	
            tbl_uom_jual_value = $("#uom_jual").val();
            tbl_uom2_jual_value = $("#uom_jual").val();

            $('.tbl_lebar_jadi').each(function(index,value){
                if($(value).val() === "" && $('#lebar_jadi').val() ){
                    alert_notify('fa fa-warning','Lebar Jadi tidak boleh kosong !','danger',function(){});
                    $(value).addClass('error'); 
                    tambah = false;
                }else{
                    $(value).removeClass('error'); 
                }
            });	

            $('.tbl_uom_lebar_jadi').each(function(index,value){
                if($(value).val() === "" && $('#lebar_jadi').val() ){
                    alert_notify('fa fa-warning','Uom Lebar Jadi tidak boleh kosong !','danger',function(){});
                    $(value).addClass('error'); 
                    tambah = false;
                }else{
                    $(value).removeClass('error'); 
                }
            });	
        }

        if(tambah){
            var tbl_qty = document.getElementsByClassName('tbl_qty');
		    var inx_tbl_qty = tbl_qty.length-1;
            // if(dept == 'GJD'){
            //     var tbl_uom_lebar_jadi = document.getElementsByClassName('tbl_uom_lebar_jadi');
            //     var inx_tbl_uom_lebar_jadi = tbl_uom_lebar_jadi.length-1;
            //     // alert(inx_tbl_uom_lebar_jadi)
            // }
            var index = $("#table_items tbody tr:last-child").index();
            var row   ='<tr class="num">'
                row  +='<td></td>';
                    if(dept == 'GJD'){
                            row  += '<td class="width-200"><input type="text" class="form-control input-sm min_width_150 tbl_corak_remark" name="Corak Remark" id="tbl_corak_remark" value="'+tbl_corak_remark_value+'" ></td>';
                            row  += '<td class="width-200"><input type="text" class="form-control input-sm min_width_150 tbl_warna_remark" name="Warna Remark" id="tbl_warna_remark" value="'+tbl_warna_remark_value+'" ></td>';
                    }
                row  += '<td class="width-200"><input type="text" class="form-control input-sm min_width_100 tbl_qty text-right" name="Qty1" id="tbl_qty1"  onkeypress="enter(event);" data-decimal="2" oninput="enforceNumberValidation(this)"></td>';
                row  += '<td class="width-200"><input type="text" class="form-control input-sm min_width_100 tbl_uom" name="Uom" id="tbl_uom" value="'+tbl_uom_value+'" readonly></td>';
                row  += '<td class="width-200"><input type="text" class="form-control input-sm min_width_100 tbl_qty2 text-right" name="Qty2" id="tbl_qty2"  onkeypress="enter(event);" data-decimal="2" oninput="enforceNumberValidation(this)"></td>';
                row  += '<td class="width-200"><input type="text" class="form-control input-sm min_width_100 tbl_uom2" name="Uom2" id="tbl_uom2" value="'+tbl_uom2_value+'" readonly></td>';
                    if(dept == 'GJD'){
                        row  += '<td class="width-200"><input type="text" class="form-control input-sm min_width_100 tbl_qty_jual text-right" name="Qty1 Jual" id="tbl_qty1_jual"  onkeypress="enter(event);" data-decimal="2" oninput="enforceNumberValidation(this)" ></td>';
                        row  += '<td class="width-200"><select type="text" class="form-control input-sm min_width_100 tbl_uom_jual" name="Uom Jual" id="tbl_uom_jual" onkeypress="enter(event);"><option value=""></option><?php foreach($list_uom as $row){?><option value="<?php echo $row->short; ?>"><?php echo $row->short;?></option>"<?php }?></select></td>';
                        row  += '<td class="width-200"><input type="text" class="form-control input-sm min_width_100 tbl_qty2_jual text-right" name="Qty2 Jual" id="tbl_qty2_jual"  onkeypress="enter(event);" data-decimal="2" oninput="enforceNumberValidation(this)" ></td>';
                        row  += '<td class="width-200"><select type="text" class="form-control input-sm min_width_100 tbl_uom2_jual" name="Uom2 Jual" id="tbl_uom2_jual" onkeypress="enter(event);"><option value=""></option><?php foreach($list_uom as $row){?><option value="<?php echo $row->short; ?>"><?php echo $row->short;?></option>"<?php }?></select></td>';
                        row  += '<td class="width-200"><input type="text" class="form-control input-sm min_width_100 tbl_lebar_jadi" name="Uom2" id="tbl_lebar_jadi" value="'+tbl_lebar_jadi_value+'"  onkeypress="enter(event);"></td>';
                        row  += '<td class="width-200"><select type="text" class="form-control input-sm min_width_100 tbl_uom_lebar_jadi" name="Uom2" id="tbl_uom_lebar_jadi"   onkeypress="enter(event);"><option value=""></option><?php foreach($list_uom as $row){?><option value="<?php echo $row->short; ?>"><?php echo $row->short;?></option>"<?php }?></select></td>';
                    }

                row  += '<td></td>';
                row  += '<td><a onclick="delRow(this);"  href="javascript:void(0)"  data-toggle="tooltip" title="Hapus Data"><i class="fa fa-trash" style="color: red"></i> </a></td>';
                row  += '</tr>';

            $('#table_items tbody').append(row);
            // $("#table_items tbody tr").eq(index + 1).find(".add, .edit").toggle();
            // $('[data-toggle="tooltip"]').tooltip();
            // if(dept == 'GJD'){
            //     var option = new Option('Inch', 'Inch');
            //     $('#table_items tbody tr').eq(index+1).find('#tbl_uom_lebar_jadi').append(option);
            //     // alert('tes');
            // }
            
            tbl_qty[inx_tbl_qty+1].focus();


        }
    }

    	//hapus row 
	function delRow(r){	  
	    var i = r.parentNode.parentNode.rowIndex;
	  	document.getElementById("table_items").deleteRow(i);
	}

    //klik button Generate
    $(document).on("click", "#btn-generate", function(e){
        
        e.preventDefault();
   
        let tambah      = true;
        let items_split = false;
        let quant_id    = $('#quant_id').val();
        let kode_produk = $('#kode_produk').val();
        let nama_produk = $('#nama_produk').val();
        let lot         = $('#lot').val();
        let qty         = $('#qty').val();
        let uom_qty     = $('#uom_qty').val();
        let qty2        = $('#qty2').val();
        let uom_qty2    = $('#uom_qty2').val();    
        let qty_jual    = $('#qty_jual').val();
        let uom_qty_jual= $('#uom_qty_jual').val();
        let qty2_jual   = $('#qty2_jual').val();
        let uom_qty2_jual= $('#uom_qty2_jual').val();    
        let departemen  = $('#departemen').val();
        let note        = $('#note').val();
        let corak_remark = $('#corak_remark').val();
        let warna_remark = $('#warna_remark').val();
        let lebar_jadi = $('#lebar_jadi').val();
        let uom_lebar_jadi = $('#uom_lebar_jadi').val();
        let arr         = new Array();

        if(departemen == ''){
            alert_notify('fa fa-warning','Departemen Harus dipilih !','danger',function(){});
        }else if(kode_produk == ''){
            alert_notify('fa fa-warning','Kode Produk tidak boleh kosong !','danger',function(){});
        }else if(nama_produk == ''){
            alert_notify('fa fa-warning','Nama Produk tidak boleh kosong !','danger',function(){});
        }else if(lot == ''){
            alert_notify('fa fa-warning','Barcode / Lot tidak boleh kosong !','danger',function(){});
        }else if(qty == '' || qty == 0 && departemen != 'GJD'){
            alert_notify('fa fa-warning','Qty1 tidak boleh kosong !','danger',function(){});
        }else if(uom_qty == ''){
            alert_notify('fa fa-warning','Uom Qty1 tidak boleh kosong !','danger',function(){});
        }else{

            

            if(departemen == 'GJD'){
                $('.tbl_corak_remark').each(function(index,value){
                    if ($(value).val()!=="" ) {
                        arr.push({
                            corak_remark        : $(value).parents("tr").find("#tbl_corak_remark").val(),
                            warna_remark        : $(value).parents("tr").find("#tbl_warna_remark").val(),
                            qty1        : $(value).parents("tr").find("#tbl_qty1").val(),
                            uom_qty1    : $(value).parents("tr").find("#tbl_uom").val(),
                            qty2        : $(value).parents("tr").find("#tbl_qty2").val(),
                            uom_qty2    : $(value).parents("tr").find("#tbl_uom2").val(),
                            qty1_jual   : $(value).parents("tr").find("#tbl_qty1_jual").val(),
                            uom_qty1_jual : $(value).parents("tr").find("#tbl_uom_jual").val(),
                            qty2_jual   : $(value).parents("tr").find("#tbl_qty2_jual").val(),
                            uom_qty2_jual : $(value).parents("tr").find("#tbl_uom2_jual").val(),
                            lebar_jadi : $(value).parents("tr").find("#tbl_lebar_jadi").val(),
                            uom_lebar_jadi : $(value).parents("tr").find("#tbl_uom_lebar_jadi").val(),
                        });
                        items_split = true;
                    }
                });
            }else{

                $('.tbl_qty').each(function(index,value){
                    if ($(value).val()!=="") {
                        arr.push({
                            qty1        : $(value).parents("tr").find("#tbl_qty1").val(),
                            uom_qty1    : $(value).parents("tr").find("#tbl_uom").val(),
                            qty2        : $(value).parents("tr").find("#tbl_qty2").val(),
                            uom_qty2    : $(value).parents("tr").find("#tbl_uom2").val(),
                            qty1_jual   : $(value).parents("tr").find("#tbl_qty1_jual").val(),
                            uom_qty1_jual : $(value).parents("tr").find("#tbl_uom_jual").val(),
                            qty2_jual   : $(value).parents("tr").find("#tbl_qty2_jual").val(),
                            uom_qty2_jual : $(value).parents("tr").find("#tbl_uom2_jual").val(),
                        });
                        items_split = true;
                    }
            });

            }

            // cek tbl_qty
            $('.tbl_qty').each(function(index,value){
                if(dept == 'GJD'){
                    qty_value = parseFloat($('#qty').val());
                    if($(value).val()=='' && qty_value ){
                    alert_notify('fa fa-warning','Qty1 tidak boleh kosong !','danger',function(){});
                    $(value).addClass('error'); 
                        tambah = false;
                    }else{
                    $(value).removeClass('error'); 
                    }
                }else{
                    if($(value).val()=='' ){
                    alert_notify('fa fa-warning','Qty1 tidak boleh kosong !','danger',function(){});
                    $(value).addClass('error'); 
                        tambah = false;
                    }else{
                    $(value).removeClass('error'); 
                    }
                }
            });	

            $('.tbl_qty2').each(function(index,value){
                // alert($("#qty2").val());
                qty2_stock = parseFloat($("#qty2").val());
                if($(value).val() === "" && (qty2_stock !==0) ){
                    alert_notify('fa fa-warning','Qty2 tidak boleh kosong !','danger',function(){});
                    $(value).addClass('error'); 
                    tambah = false;
                }else{
                    $(value).removeClass('error'); 
                }
            });	

            if(dept == 'GJD'){

                $('.tbl_corak_remark').each(function(index,value){
                    if($(value).val() === "" ){
                        alert_notify('fa fa-warning','Corak Remark tidak boleh kosong !','danger',function(){});
                        $(value).addClass('error'); 
                        tambah = false;
                    }else{
                        $(value).removeClass('error'); 
                    }
                });	

                $('.tbl_warna_remark').each(function(index,value){
                    if($(value).val() === "" && $('#warna_remark').val() !='' ){
                        alert_notify('fa fa-warning','Warna Remark tidak boleh kosong !','danger',function(){});
                        $(value).addClass('error'); 
                        tambah = false;
                    }else{
                        $(value).removeClass('error'); 
                    }
                });	

                $('.tbl_qty_jual').each(function(index,value){
                    if($(value).val() === "" ){
                        alert_notify('fa fa-warning','Qty Jual tidak boleh kosong !','danger',function(){});
                        $(value).addClass('error'); 
                        tambah = false;
                    }else{
                        $(value).removeClass('error'); 
                    }
                });	

                $('.tbl_uom_jual').each(function(index,value){
                    if($(value).val() === "" ){
                        alert_notify('fa fa-warning','Uom Qty Jual tidak boleh kosong !','danger',function(){});
                        $(value).addClass('error'); 
                        tambah = false;
                    }else{
                        $(value).removeClass('error'); 
                    }
                });	

                $('.tbl_lebar_jadi').each(function(index,value){
                    if($(value).val() === "" && $('#lebar_jadi').val() ){
                        alert_notify('fa fa-warning','Lebar Jadi tidak boleh kosong !','danger',function(){});
                        $(value).addClass('error'); 
                        tambah = false;
                    }else{
                        $(value).removeClass('error'); 
                    }
                });	

                $('.tbl_uom_lebar_jadi').each(function(index,value){
                    if($(value).val() === "" && $('#lebar_jadi').val() ){
                        alert_notify('fa fa-warning','Uom Lebar Jadi tidak boleh kosong !','danger',function(){});
                        $(value).addClass('error'); 
                        tambah = false;
                    }else{
                        $(value).removeClass('error'); 
                    }
                });	
            }

            if(tambah){
        
                if(items_split == false ){
                    alert_notify('fa fa-warning','Split Items masih Kosong  !','danger',function(){});
                }else{

                    bootbox.dialog({
                    message: "Apakah Anda yakin ingin Generate Data ?",
                    title  : "<i class='fa fa-gear'></i> Generate Data !",
                    buttons: {
                        danger: {
                            label    : "Yes ",
                            className: "btn-primary btn-sm",
                            callback : function() {
                                please_wait(function(){});
                                $('#btn-generate').button('loading');

                                $.ajax({
                                    type: "POST",
                                    dataType: "json",
                                    url :'<?php echo base_url('warehouse/splitlot/generate_split')?>',
                                    beforeSend: function(e) {
                                        if(e && e.overrideMimeType) {
                                            e.overrideMimeType("application/json;charset=UTF-8");
                                        }
                                    },
                                    data: {lot:lot, quant_id:quant_id, kode_produk:kode_produk, nama_produk:nama_produk, corak_remark:corak_remark, warna_remark:warna_remark, qty:qty, uom_qty:uom_qty, qty2:qty2, uom_qty2:uom_qty2, qty_jual:qty_jual, uom_qty_jual:uom_qty_jual, qty2_jual:qty2_jual, uom_qty2_jual:uom_qty2_jual, lebar_jadi:lebar_jadi, uom_lebar_jadi:uom_lebar_jadi, departemen:departemen, note:note, data_split:JSON.stringify(arr),
                                    },success: function(data){
                                        if(data.status == "failed"){
                                            //jika ada form belum keiisi
                                            unblockUI( function() {
                                                setTimeout(function() { alert_notify(data.icon,data.message,data.type,function(){}); }, 1000);
                                            });
                                            if(data.field!= ''){
                                                document.getElementById(data.field).focus();
                                            }
                                        }else{
                                        //jika berhasil disimpan
                                            unblockUI( function() {
                                                setTimeout(function() { alert_notify(data.icon,data.message,data.type,function(){
                                                    window.location.replace('edit/'+data.isi);
                                                }); }, 1000);
                                            });
                                        }
                                        $('#btn-generate').button('reset');

                                    },error: function (xhr, ajaxOptions, thrownError) {
                                        unblockUI(function() {});
                                        $('#btn-generate').button('reset');
                                        if(xhr.status == 401){
                                            var err = JSON.parse(xhr.responseText);
                                            alert(err.message);
                                        }else{
                                            alert("Error Generate Data !");
                                        }  

                                    }
                                });
                           
                            }
                        },
                        success: {
                            label    : "No",
                            className: "btn-default  btn-sm",
                            callback : function() {
                                $('.bootbox').modal('hide');
                            }
                        }
                    }
                    });
                }
            }
        }
    });


    $(document).on("keyup", ".tbl_qty_jual", function(){
        let qty_jual = $(this).val();
        let uom_jual = $(this).parents("tr").find("#tbl_uom_jual").val();
        if(uom_jual == 'Yrd'){
            result = konversi_uom('Yrd','Mtr',qty_jual);
            if(result == 0){
                result = '';
            }
            $(this).parents("tr").find("#tbl_qty1").val(result);
            
        }

    });

	function konversi_uom(uom1From,uom2To,valueUom){

            let arr_konversi = new Array();
            list_konversi = <?php echo $uom_konversi?>
            // alert(JSON.stringify(list_konversi));
            result_convert = 0;
            $.each(list_konversi, function(key, val) {
                if(val.uom1 == uom1From && val.uom2 == uom2To){
                    // alert(val.faktor);
                    result_convert = valueUom*val.faktor;
                }
            });

            fixed = roundNum(result_convert)
            return fixed;

    }

    
    //validasi round decimal 
    function roundNum(number){
        return +(Math.round(number + "e+2") + "e-2");
    }
	
  



   
</script>


</body>
</html>
