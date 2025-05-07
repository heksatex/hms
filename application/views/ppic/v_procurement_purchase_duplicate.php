
<!DOCTYPE html>
<html>
<head>
  <?php $this->load->view("admin/_partials/head.php") ?>
  <style>
    .min-width-full{
        min-width: 100%;
    }

    @media screen and (min-width: 768px) {
/*      .over {
         overflow-x: visible !important; 
         overflow: visible !important;
      }*/
    }

    .min-width-200{
        min-width: 200px;;
    }

    .min-width-100{
        min-width: 100px;
    }

    .min-width-80{
        min-width: 80px;;
    }
    
    .select2-container {
        width: 100% !important;
    }
    .error{
	  	border:  1px solid red !important;
  	} 

    .bootstrap-datetimepicker {
        z-index: 9999 !important;
    }
  </style>
</head>

<body class="hold-transition skin-black fixed sidebar-mini">
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
          <h3 class="box-title">Form Tambah (Duplicate)</h3>          
        </div>
        <div class="box-body">
          <form class="form-horizontal">
            <div class="form-group">                  
              <div class="col-md-12" >
                <div id="alert"></div>
              </div>
            </div>
            <div class="form-group">

              <div class="col-md-6">
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Procurement Purchase </label></div>
                  <div class="col-xs-8">
                    <input type="text" class="form-control input-sm" name="kode_pp" id="kode_pp"  readonly="readonly" />
                    <input type="hidden" class="form-control input-sm" name="kode_pp_en" id="kode_pp_en"  readonly="readonly" />
                  </div>                                    
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Create Date </label></div>
                  <div class="col-xs-8 col-md-8">
                     <input type='text' class="form-control input-sm" name="tgl_buat" id="tgl_buat" readonly="readonly" value="<?php echo date('Y-m-d H:i:s')?>"  />
                  </div>                                    
                </div>

                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Reff Notes </label></div>
                  <div class="col-xs-8">
                    <textarea type="text" class="form-control input-sm" name="note" id="note"><?php echo  $procurementpurchase->notes?></textarea>
                  </div>                                    
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Type </label></div>
                  <div class="col-xs-8">
                    <div class="col-xs-12 col-sm-12 col-md-12">
                      <input type="radio" id="mto" name="type[]" value="mto">
                      <label for="mto">Make to Order</label>
                    </div>
                    <div class="col-xs-12 col-sm-12 col-md-12">
                      <input type="radio" id="pengiriman" name="type[]" value="pengiriman">
                      <label for="pengiriman">Pengiriman</label>
                    </div>
                  </div>                                    
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Sales Order </label></div>
                  <div class="col-xs-8">
                    <div class="col-xs-6 col-sm-4 col-md-4">
                      <input type="radio" id="sc_true" name="sc[]" value="yes">
                      <label for="yes">Yes</label>
                    </div>
                    <div class="col-xs-6 col-sm-4 col-md-4">
                      <input type="radio" id="sc_false" name="sc[]" value="no">
                      <label for="no">No</label>
                    </div>
                  </div>                                    
                </div>
              </div>

              <div class="col-md-6">
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Schedule Date </label></div>
                  <div class="col-xs-8 col-md-8">
                    <div class='input-group date' id='tanggal' >
                      <input type='text' class="form-control input-sm" name="tgl" id="tgl" readonly="readonly"  />
                      <span class="input-group-addon">
                          <span class="glyphicon glyphicon-calendar"></span>
                      </span>
                    </div>
                  </div>                                    
                </div>
                <span id="show_sc" style="display: none;">
                  <div class="col-md-12 col-xs-12">
                    <div class="col-xs-4"><label>Production Order </label></div>
                    <div class="col-xs-8">
                      <div class='input-group'>
                        <input type="text" class="form-control input-sm" name="kode_prod" id="kode_prod" readonly="readonly" value="<?php echo $procurementpurchase->kode_prod; ?>">
                        <span class="input-group-addon">
                            <a href="#" class="sc"><span class="glyphicon  glyphicon-share"></span></a>
                        </span>
                      </div>
                    </div>                                    
                  </div>
                  <div class="col-md-12 col-xs-12">
                    <div class="col-xs-4"><label>Sales Order </label></div>
                    <div class="col-xs-8 col-md-8">
                        <input type="text" class="form-control input-sm" name="sales_order" id="sales_order" readonly="readonly" value="<?php echo $procurementpurchase->sales_order; ?>"" />
                    </div>                                    
                  </div>
                </span>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Departement Tujuan</label></div>
                  <div class="col-xs-8">
                    <select class="form-control input-sm" name="warehouse" id="warehouse" />
                        <?php
                        echo '<option value="">Pilih Warehouse</option>';
                        foreach ($warehouse as $row) {
                            if($row->kode == $procurementpurchase->warehouse){?>
                            <option value='<?php echo $row->kode; ?>' selected><?php echo $row->nama;?></option>
                            <?php
                            }else{?>
                            <option value='<?php echo $row->kode; ?>'><?php echo $row->nama;?></option>
                            <?php  
                            }
                        }
                      ?>
                    </select>
                  </div>                                    
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Priority </label></div>
                  <div class="col-xs-8">
                    <select class="form-control input-sm" name="priority" id="priority" />
                    <option value="">Pilih Priority</option>
                    <?php 
                    $val = array('Normal','Urgent');
                    for($i=0;$i<=1;$i++) {
                      if($val[$i] == $procurementpurchase->priority){?>
                         <option selected><?php echo $val[$i];?></option>
                      <?php
                        }else{?>
                        <option><?php echo $val[$i];?></option>
                      <?php  }
                    }?>
                    </select>
                  </div>                                    
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-12">
                <!-- Custom Tabs -->
                <div class="">
                  <ul class="nav nav-tabs " >
                    <li class="active"><a href="#tab_1" data-toggle="tab">Procurements Lines</a></li>
                  </ul>
                  <div class="tab-content over"><br>
                    <div class="tab-pane active" id="tab_1">

                      <!-- Tabel  -->
                      <div class="col-md-12 table-responsive over">
                        <table class="table table-condesed table-hover rlstable  over" width="100%" id="table_items" >
                          <thead>                          
                            <tr>
                              <th class="style no">No.</th>
                              <th class="style" width="200px">Product</th>
                              <th class="style" width="150px">Schedule Date</th>
                              <th class="style" style="width:100px; text-align: right;" >Qty Beli</th>
                              <th class="style" width="80px">Uom Beli</th>
                              <th class="style" style="width:100px; text-align: right;" >Qty</th>
                              <th class="style" width="80px">Uom</th>
                              <th class="style" width="200px">Notes</th>
                              <th class="style" width="60px">Status</th>
                              <th class="style" style="width: 80px; text-align: center;"></th>                            
                            </tr>
                          </thead>
                          <tbody id="tbody_items">
                          </tbody>
                          <tfoot>
                            <tr>
                              <td colspan="10">
                                 <a href="javascript:void(0)" onclick="tambah_baris(false,'','','','','','')"><i class="fa fa-plus"></i> Tambah Data</a>
                              </td>
                            </tr>
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
           
          </form>
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
  </footer>


</div>

<?php $this->load->view("admin/_partials/js.php") ?>

<script type="text/javascript">

    //set schedule_date
    var datenow=new Date();  
    datenow.setMonth(datenow.getMonth());
    $('#tanggal').datetimepicker({
        defaultDate: datenow,
        format : 'YYYY-MM-DD HH:mm:ss',
        ignoreReadonly: true,
    });

    // modal view production order
    $(document).on('click','.sc',function(e){
        e.preventDefault();
            $("#view_data").modal('show');
        $(".view_body").html('<center><h5><img src="<?php echo base_url('dist/img/ajax-loader.gif') ?> "/><br>Please Wait...</h5></center>');
        $('.modal-title').text('View Production Order');
            $.post('<?php echo site_url()?>ppic/procurementorder/list_production_order_modal',
            function(html){
                setTimeout(function() {$(".view_body").html(html);  },1000);
            }   
        );
    });

    //pilih data pada modal view production order
    $(document).on('click', '.pilih', function (e) {
        document.getElementById("kode_prod").value = $(this).attr('kode_prod');
        document.getElementById("sales_order").value = $(this).attr('sales_order');
        $('#view_data').modal('hide');
    });

    $(document).on("change", "input[name='sc[]']", function(){
        checkTampil('sc');
    });

    function checkTampil(show){
        if(show == 'sc'){

          var radio_type = $('input[name="sc[]"]').map(function(e, i) {
                  if(this.checked == true){
                      return i.value;
                  }
          }).get();
          
          if(radio_type == 'yes'){
              $('#show_sc').show();

          }else if(radio_type == 'no'){
              $('#show_sc').hide();
              $('#kode_prod').val('');
              $('#sales_order').val('');
          }
        }
    }
    
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

    //klik button simpan
    $('#btn-simpan').click(function(){
        
        var arr   = new Array();
        $("#table_items tbody[id='tbody_items'] .kode_produk").each(function(index, element) {
            if ($(element).val()!=="") {
                arr.push({
                    kode_produk :$(element).val(),
                    // nama_produk :$(element).parents("tr").find("#nama_produk").val(),
                    schedule_date:$(element).parents("tr").find("#schedule_date").val(),
                    qty 		:$(element).parents("tr").find("#qty").val(),
                    uom 		:$(element).parents("tr").find("#uom").val(),
                    qty_beli:$(element).parents("tr").find("#qty_beli").val(),
                    uom_beli:$(element).parents("tr").find("#uom_beli").val(),
                    reff_note 	:$(element).parents("tr").find("#reff").val(),
                });
            }
        }); 

    $('#btn-simpan').button('loading');
        var radio_type = $('input[name="type[]"]').map(function(e, i) {
                if(this.checked == true){
                    return i.value;
                }
        }).get();

        var radio_type_2 = $('input[name="sc[]"]').map(function(e, i) {
                if(this.checked == true){
                    return i.value;
                }
        }).get();
        please_wait(function(){});
        $.ajax({
            type: "POST",
            dataType: "json",
            url :'<?php echo base_url('ppic/procurementpurchase/simpan_duplicate')?>',
            beforeSend: function(e) {
                if(e && e.overrideMimeType) {
                    e.overrideMimeType("application/json;charset=UTF-8");
                }
            },
            data: {kode_prod   : $('#kode_prod').val(),
                    kode_pp     : $('#kode_pp').val(),
                    kode_pp_asal : "<?php echo $kode_pp?>",
                    tgl         : $('#tgl').val(),
                    note        : $('#note').val(),
                    sales_order : $('#sales_order').val(),
                    priority    : $('#priority').val(),
                    warehouse   : $('#warehouse').val(),
                    type        : radio_type,
                    show_sc     : radio_type_2,
                    arr_items    : JSON.stringify(arr),

            },success: function(data){
                if(data.sesi == "habis"){
                //alert jika session habis
                alert_modal_warning(data.message);
                window.location.replace('index');
                }else if(data.status == "failed"){
                    //jika ada form belum keiisi
                    unblockUI( function() {
                    setTimeout(function() { alert_notify(data.icon,data.message,data.type); }, 1000);
                    });
                    //document.getElementById(data.field).focus();
                }else{
                  //jika berhasil disimpan/diubah
                  $('#kode_pp').val(data.isi);
                  $('#kode_pp_en').val(data.kode_encrypt);
                  unblockUI( function() {
                      setTimeout(function() { 
                      alert_notify(data.icon,data.message,data.type, function(){
                      
                      window.location.replace('edit/'+$('#kode_pp_en').val());
                      },1000); 
                      });
                  });
                }
                $('#btn-simpan').button('reset');

            },error: function (xhr, ajaxOptions, thrownError) {
                alert(xhr.responseText);
                unblockUI( function(){});
                $('#btn-simpan').button('reset');

            }
        });
    });


     //klik button generate
    $('#btn-generate').click(function(){
       if($('#kode_pp').val() == ""){
        var message = 'Data Masih Kosong !';
        alert_modal_warning(message);
       }
    });

    //klik button Batal
    $('#btn-cancel').click(function(){
       if($('#kode_pp').val() == ""){
        var message = 'Data Masih Kosong !';
        alert_modal_warning(message);
       }
    });


    //klik button print
    $('#btn-print').click(function(){
       if($('#kode_pp').val() == ""){
        var message = 'Data Masih Kosong !';
        alert_modal_warning(message);
       }
    });

    //validasi qty
    function validAngka(a){
        if(!/^[0-9.]+$/.test(a.value)){
            a.value = a.value.substring(0,a.value.length-1000);
        }
    }

    function enter(e,table){
        if(e.keyCode === 13){
            tambah_baris(false,'','','','','','') //panggil fungsi tambah baris
	        e.preventDefault(); 
	    }
	}

    // hapus row
    function delRow(r){		
        var i = r.parentNode.parentNode.rowIndex;
        document.getElementById("table_items").deleteRow(i);
    }
    
    
    //html entities javascript
    function htmlentities_script(str) {
      return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    <?php 
        $no = 1;
        foreach($details as $val){
        $note = str_replace(array("","\n"), '', $val->reff_notes);
        $nama_produk = $val->nama_produk;
    ?>
            tambah_baris(true,'<?php echo $val->kode_produk?>', `<?php echo htmlentities($nama_produk)?>`, '<?php echo $val->schedule_date?>', '<?php echo $val->qty?>', '<?php echo $val->uom?>', `<?php echo $note?>`);
    <?php 
            $no++;
        }
    ?>  

    function tambah_baris(data,kode_produk,nama_produk,schedule_date,qty,uom,reff_note) {

        var index  = $("#table_items tbody[id='tbody_items'] tr:last-child").index();
        if(index== -1){
            row = 0;
        }else{
            row  = parseInt($("#table_items tbody[id='tbody_items'] tr:last-child td .row").val());
        }
        var tambah  = true;
        var tbl     = "#table_items tbody[id='tbody_items'] ";       
        var np      = $(tbl+" td input[name='Product']");
        var inx_np  = np.length-1;
        var n_qty   = $(tbl+" td input[name='Qty']");
		    var inx_n_qty = n_qty.length-1;
        var n_uom     = $(tbl+" td input[name='Uom']");
		    var inx_n_uom = n_uom.length-1;
        var product_same_arr = [];
        var event      = "enter(event)";


        $(tbl+' .kode_produk').each(function(index,value){
          if($(value).val()=='' || $(value).val() == null){
              alert_notify('fa fa-warning','Product Harus Diisi !','danger',function(){});
              var s2element = $(this).parents(tbl).find(np[inx_np]).siblings('select');
              s2element.select2('open');
              s2element.on('select2:closing', function (e) {
                  s2element.select2('focus');
              });
              $(this).parents('td').find('span span.selection span.select2-selection').addClass('error'); 
              tambah = false;
          }else{
              value = $(value).val();
            
              if(product_same_arr.indexOf(value) == -1){
                  product_same_arr.push(value);
                  $(this).parents('td').find('span span.selection span.select2-selection').removeClass('error'); 
              }else{
                  tambah    = false;
                  $(this).parents('td').find('span span.selection span.select2-selection').addClass('error'); 
                  alert_notify('fa fa-warning','Product tidak boleh sama ','danger',function(){});
              }
              
          }
        });

        //cek qty apa ada yg kosong
        $(tbl+' .qty').each(function(index,value){
          if($(value).val()==''){
              alert_notify('fa fa-warning','Qty Harus Diisi !','danger',function(){});
              $(this).parents(tbl).find(n_qty[inx_n_qty]).focus();
              $(value).addClass('error'); 
              tambah = false;
          }else{
              $(value).removeClass('error'); 
          }
        });

        //cek uom apa ada yg kosong
        $(tbl+' .uom').each(function(index,value){
          if($(value).val()=='' || $(value).val() == null){
              alert_notify('fa fa-warning','Uom Harus Diisi !','danger',function(){});
              var s2element = $(this).parents(tbl).find(uom[inx_n_uom]).siblings('select');
              s2element.select2('open');
              s2element.on('select2:closing', function (e) {
                  s2element.select2('focus');
              });
              $(this).parents('td').find('span span.selection span.select2-selection').addClass('error'); 
              tambah = false;
          }else{
              $(this).parents('td').find('span span.selection span.select2-selection').removeClass('error'); 
          }
        });

        if(tambah){
            var ro     = row+1;
            var delRow  = "delRow(this)";

            var class_produk = 'kode_produk_'+ro;
            var produk       = 'nama_produk'+ro;
            var class_uom    = 'uom_'+ro;
            var class_uom_beli= 'uom_beli'+ro;
            var class_cata_uom_beli= 'uom_beli_note'+ro;
            var row        = '<tr class="">'
                        + '<td><input type="hidden"  name="row" class="row" value="'+ro+'">'+ro+'.</td>'
                        + '<td  class="min-width-200">'
                            + '<select add="manual" type="text" class="form-control input-sm kode_produk '+class_produk+' min-width-full" name="Product" id="kode_produk"></select>'
                            // + '<input type="hidden" class="form-control input-sm nama_produk '+produk+'" name="nama_produk" id="nama_produk" value="'+nama_produk+'"></td>'
                        + '<td><div class="input-group min-width-full date sch_date" id="sch_date" min-width-full><input type="text" class="form-control input-sm" name="schedule_date" id="schedule_date" readonly="readonly" /><span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span></div></td>'
                        + '<td class="min-width-100"><input type="text" class="form-control input-sm qty_beli" name="Qty Beli" id="qty_beli"  onkeyup="validAngka(this)" onkeypress="'+event+'"></td>'
                        + '<td class="min-width-100"><select type="text" class="form-control input-sm uom_beli '+class_uom_beli+'" name="Uom Beli" id="uom_beli"></select><small id="uom_beli_note" class="form-text text-muted '+class_cata_uom_beli+'"></small></td>'
                        + '<td class="min-width-100"><input type="text" class="form-control input-sm qty" name="Qty" id="qty"  onkeyup="validAngka(this)" onkeypress="'+event+'" value="'+qty+'"></td>'
                        + '<td class="min-width-100"><select type="text" class="form-control input-sm uom '+class_uom+'" name="Uom" id="uom"></select></td>'
                        + '<td class="min-width-100"><textarea type="text" class="form-control input-sm" name="note" id="reff" onkeypress="'+event+'">'+reff_note+'</textarea></td>'
                        +'<td></td>'
                        + '<td class="width-50" align="center"><a onclick="'+delRow+';"  href="javascript:void(0)"  data-toggle="tooltip" title="Hapus Data"><i class="fa fa-trash" style="color: red"></i> </a></td>'
                        + '</tr>';
            $('#table_items tbody[id="tbody_items"] ').append(row);
            //$("#components tbody tr").eq(index + 1).find(".add, .edit").toggle();
            $('[data-toggle="tooltip"]').tooltip();

            var sel_produk  = $('#table_items tbody[id="tbody_items"] tr .'+class_produk);
            var sel_uom     = $('#table_items tbody[id="tbody_items"] tr .'+class_uom);
            var produk_hide = $('#table_items tbody[id="tbody_items"] tr .'+produk);
            var sel_uom_beli= $('#table_items tbody[id="tbody_items"] tr .'+class_uom_beli);
            // var cata_uom_beli= $('#table_items tbody[id="tbody_items"] tr .'+class_cata_uom_beli);
            if(data==true){
                //untuk event selected select2 nama_produk
                custom_nama = '['+kode_produk+'] '+nama_produk;
                var $newOption = $("<option></option>").val(kode_produk).text(custom_nama);
                sel_produk.empty().append($newOption).trigger('change');

                var $newOption2 = $("<option></option>").val(uom).text(uom);
                sel_uom.empty().append($newOption2).trigger('change');

            }

            var datetomorrow=new Date();
            datetomorrow.setDate(datetomorrow.getDate() + 1);  
            $('.sch_date').datetimepicker({
            minDate : datetomorrow,
            format : 'YYYY-MM-DD HH:mm:ss',
            ignoreReadonly: true,
            });

            //select 2 product
            sel_produk.select2({
                ajax:{
                    dataType: 'JSON',
                    type : "POST",
                    url : "<?php echo base_url();?>ppic/procurementpurchase/get_produk_procurement_purchase_select2",
                    //delay : 250,
                    data : function(params){
                        return{
                        prod:params.term
                        };
                    }, 
                    processResults:function(data){
                        var results = [];

                        $.each(data, function(index,item){
                            results.push({
                                id:item.kode_produk,
                                text:'['+item.kode_produk+'] '+item.nama_produk
                            });
                        });
                        return {
                        results:results
                        };
                    },
                    error: function (xhr, ajaxOptions, thrownError){
                        // alert('Error data');
                        // alert(xhr.responseText);
                    }
                }
            });

            //jika nama produk diubah
            sel_produk.change(function(){
                $.ajax({
                    dataType: "JSON",
                    url : '<?php echo site_url('ppic/procurementpurchase/get_prod_by_id') ?>',
                    type: "POST",
                    data: {kode_produk: $(this).parents("tr").find("#kode_produk").val()  },
                    success: function(data){
                        // $('.prodhidd').val(data.nama_produk);
                        // $('.uom').val(data.uom);
                        produk_hide.val(data.nama_produk);
                        //untuk event selected select2 uom
                        var $newOptionuom = $("<option></option>").val(data.uom).text(data.uom);
                        sel_uom.empty().append($newOptionuom).trigger('change');
                    },
                    error: function (xhr, ajaxOptions, thrownError){
                    // alert('Error data');
                    // alert(xhr.responseText);
                    }
                });
            });

            //select 2 uom
            sel_uom.select2({
                allowClear: true,
                placeholder: "",
                ajax:{
                        dataType: 'JSON',
                        type : "POST",
                        url : "<?php echo base_url();?>ppic/procurementpurchase/get_list_uom_select2",
                        data : function(params){
                            return{
                                prod:params.term,
                                kode_produk: $(this).parents("tr").find("#kode_produk").val() 
                            };
                        }, 
                        processResults:function(data){
                            var results = [];
                            $.each(data, function(index,item){
                                results.push({
                                    id:item.uom,
                                    text:item.uom
                                });
                            });
                            return {
                                results:results
                            };
                        },
                        error: function (xhr, ajaxOptions, thrownError){
                            // alert('Error data');
                            // alert(xhr.responseText);
                        }
                }
            });

            sel_uom_beli.select2({
                allowClear: true,
                placeholder: "",
                ajax: {
                    url : "<?php echo base_url();?>ppic/procurementpurchase/get_list_uom_beli_select2",
                    delay: 250,
                    type: "POST",
                    data: function (params) {
                      return{
                                prod:params.term,
                                kode_produk: $(this).parents("tr").find("#kode_produk").val() 
                            };
                    },
                    processResults: function (data) {
                        return {
                            results: $.map(JSON.parse(data), function (obj) {
                                return {
                                    id: obj.id,
                                    text: obj.uom,
                                    catatan: obj.catatan,
                                    nilai:obj.nilai
                                };
                            })
                        };
                    }
                }
           
            });

            sel_uom_beli.on('select2:select', function (e) {
              var gt_cata_uom_beli = $('#table_items tbody[id="tbody_items"] tr .'+class_uom_beli+' :selected').data().data.catatan;
              $('.'+class_cata_uom_beli).html(gt_cata_uom_beli);
            });

        }

    }


    $(document).on("keyup", ".qty_beli", function(){
        let qty_beli = $(this).val();
        let uom_bei  = $(this).parents("tr").find("#uom_beli").val(); // id nilai konversi uom
        let get_nilai = $(this).parents("tr").find("#uom_beli").find(':selected').data().data.nilai;
        result    = qty_beli*get_nilai;
        $(this).parents("tr").find("#qty").val(result);
    });

  
    
   
</script>


</body>
</html>
