<!DOCTYPE html>
<html>
<head>
  <?php $this->load->view("admin/_partials/head.php") ?>

  <style type="text/css">

    button[id="btn-simpan"],button[id="btn-cancel"]{/*untuk hidden button di top bar */
        display: none;
    }

    table.table td .add {
        display: none;
    }
    
    table.table td .cancel {
        display: none;
        color : red;
        margin: 10 0px;
        min-width:  24px;
    }

    .min-width-200{
        min-width: 200px;;
    }

    .min-width-100{
        min-width: 100px;
    }

    .select2-container {
        width: 100% !important;
    }

   
  </style>

</head>

<body class="hold-transition skin-black fixed sidebar-mini"  onload="reloadItems()">
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
          <h3 class="box-title" id="box-title"><b><?php echo $head->kode_bom.' - '.$head->nama_bom;?></b></h3>          
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
                  <div class="col-xs-4"><label>Kode BOM </label></div>
                  <div class="col-xs-8">
                    <input type="text" class="form-control input-sm" name="kode_bom" id="kode_bom" value="<?php echo $head->kode_bom; ?>" readonly="readonly"/>
                  </div>
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Tanggal Dibuat </label></div>
                  <div class="col-xs-8">
                    <input type="text" class="form-control input-sm" name="tanggal" id="tanggal" value="<?php echo $head->tanggal;?>"  readonly="readonly">
                  </div>
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Nama Produk </label></div>
                  <div class="col-xs-8" id="div-nama_produk">
                    <select type="text" class="form-control input-sm" name="sel2_nama_produk" id="sel2_nama_produk" ></select>
                  </div>
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Nama BOM </label></div>
                  <div class="col-xs-8" >
                    <input type="text" class="form-control input-sm" name="nama_bom" id="nama_bom"  value="<?php echo htmlentities($head->nama_bom); ?>" readonly="readonly">
                    <input type="hidden" class="form-control " name="nama_produk" id="nama_produk" readonly="readonly"  value="<?php echo htmlentities($head->nama_produk) ?>" />
                  </div>
                </div>
             
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Qty </label></div>
                  <div class="col-xs-4">
                    <input type="text" class="form-control input-sm" name="qty" id="qty" value="<?php echo $head->qty?>" onkeyup="validAngka(this)" readonly="readonly">
                  </div>
                  <div class="col-xs-4" id="div-uom">
                     <select class="form-control input-sm sel2_uom" name="sel2_uom" id="sel2_uom" disabled="true"> </select>    
                  </div>
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Qty2 </label></div>
                  <div class="col-xs-4">
                    <input type="text" class="form-control input-sm" name="qty2" id="qty2" value="<?php echo $head->qty2?>"  onkeyup="validAngka(this)"  readonly="readonly">
                  </div>
                  <div class="col-xs-4" id="div-uom2">
                     <select class="form-control input-sm sel2_uom" name="sel2_uom2" id="sel2_uom2" disabled="true" >
                  </select>    
                  </div>
                </div>
                <div class="col-md-12 col-xs-12">
                    <div class="col-xs-4"><label>Status</label></div>
                    <div class="col-xs-4">
                        <select class="form-control input-sm" name="status" id="status">
                        <?php 
                          $arr_status = array(array('value' => 't', 'text' => 'Aktif'), array( 'value'=> 'f', 'text' => 'Tidak Aktif'));
                          foreach ($arr_status as $val) {
                            if($val['value'] == $head->status_bom){?>
                                <option value="<?php echo $val['value']; ?>" selected><?php echo $val['text'];?></option>
                                <?php
                            }else{?>
                                 <option value="<?php echo $val['value']; ?>" ><?php echo $val['text'];?></option>
                                <?php  
                            }
                          }?>
                        </select>                 
                    </div>               
                  </div>         

              </div>

            </div>
           
          </form>

           <div class="row">
              <div class="col-md-12">
                <!-- Custom Tabs -->
                <div class="">
                  <ul class="nav nav-tabs " >
                    <li class="active"><a href="#tab_1" data-toggle="tab">Components</a></li>
                  </ul>
                  <div class="tab-content over"><br>
                    <div class="tab-pane active" id="tab_1">

                      <!-- Tabel  -->
                      <div class="col-md-12 table-responsive over">
                        <table class="table table-condesed table-hover table-responsive rlstable" id="components">
                          <thead>
                            <tr>
                              <th class="style no">No</th>
                              <th class="style ">Nama Produk</th>
                              <th class="style text-right">Qty</th>
                              <th class="style">Uom</th>
                              <th class="style text-right">Qty2</th>
                              <th class="style">Uom2</th>
                              <th class="style">Note</th>
                              <th class="style"></th>
                            </tr>
                          </thead>
                          <tbody>
                            
                          </tbody>
                          <tfoot>
                            <tr>
                              <td colspan="7">
                              </td>
                            </tr>
                          </tfoot>
                        </table>
                        <div id="example1_processing" class="table_processing" style="display: none">
                              Processing...
                        </div>
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
    <div id="foot">
      <?php $this->load->view("admin/_partials/footer.php") ?>
    <div id="foot">
  </footer>

</div>

<style type="text/css">
	.error{
		border:  1px solid red !important;
	}  

</style>

<?php $this->load->view("admin/_partials/js.php") ?>

<script type="text/javascript">

    //html entities javascript
    function htmlentities_script(str) {
        return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    function validAngka(a){
      if(!/^[0-9.]+$/.test(a.value)){
        a.value = a.value.substring(0,a.value.length-1000);
        alert_notify('fa fa-warning','Maaf, Inputan Qty Hanya Berupa Angka !','danger',function(){});
      }
    }

    function formatNumber(n) {
	  	return new Intl.NumberFormat('en-US',{minimumFractionDigits: 2,}).format(n);
    }

    
 

    //untuk merefresh 
    function refresh_bom(){
        $("#tab_1").load(location.href + " #tab_1");
        $("#foot").load(location.href + " #foot");
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

    
    // $("#sel2_nama_produk").select2({disabled:true})

    // $('select option:not(selected)').prop('disabled',true);
    var h_kode_produk   = '<?php echo $head->kode_produk ?>';
    var h_nama_produk   = '<?php echo $head->nama_produk ?>';

    //untuk event selected select2 nama bom
    custom_nama = '['+h_kode_produk+'] '+h_nama_produk;
    var $newOption = $("<option></option>").val(h_kode_produk).text(custom_nama);
    $("#sel2_nama_produk").empty().append($newOption).trigger('change');

    var h_uom      = '<?php echo $head->uom ?>';
    //untuk event selected select2 uom
    var $newOptionuom = $("<option></option>").val(h_uom).text(h_uom);
    $("#sel2_uom").empty().append($newOptionuom).trigger('change');

    var h_uom2      = '<?php echo $head->uom2 ?>';
    //untuk event selected select2 uom
    var $newOptionuom2 = $("<option></option>").val(h_uom2).text(h_uom2);
    $("#sel2_uom2").empty().append($newOptionuom2).trigger('change');


    $("select").prop("disabled", true);

   
    $(document).on('click','#btn-edit', function(e){
    
        $("#btn-simpan").show();//tampilkan btn-simpan
        $("#btn-cancel").show();//tampilkan btn-cancel
        $("#btn-edit").hide();//sembuyikan btn-edit

        $("#nama_bom").attr("readonly", false);
        $("#qty").attr("readonly", false);
        $("#qty2").attr("readonly", false);
        
        $('#sel2_nama_produk').attr('disabled', false).attr('id', 'sel2_nama_produk');
        $('#sel2_uom').attr('disabled', false).attr('id', 'sel2_uom');
        $('#sel2_uom2').attr('disabled', false).attr('id', 'sel2_uom2');
        $('#status').attr('disabled', false).attr('id', 'status');

        //select 2 product
        $('#sel2_nama_produk').select2({
          allowClear: true,
          placeholder: "",
          ajax:{
                dataType: 'JSON',
                type : "POST",
                url : "<?php echo base_url();?>ppic/billofmaterials/get_produk_bom_select2",
                //delay : 250,
                data : function(params){
                  return{
                    prod:params.term,
                  };
                }, 
                processResults:function(data){
                  var results = [];
                  $.each(data, function(index,item){
                    results.push({
                        id:item.kode_produk,
                        //text:item.nama_produk
                        text:'['+item.kode_produk+'] '+item.nama_produk

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

        //jika nama_bom diubah
        $("#sel2_nama_produk").change(function(){
    
          $.ajax({
              dataType: "JSON",
              url : '<?php echo site_url('ppic/billofmaterials/get_prod_by_id') ?>',
              type: "POST",
              data: {kode_produk: $("#sel2_nama_produk").val() },
              success: function(data){
                $('#kode_produk').val(data.kode_produk);
                $('#nama_bom').val(data.nama_produk);
                $('#nama_produk').val(data.nama_produk);
                $('#qty').val(data.qty);
                //untuk event selected select2 uom
                var $newOptionuom = $("<option></option>").val(data.uom).text(data.uom);
                $("#sel2_uom").empty().append($newOptionuom).trigger('change');
                var $newOptionuom2 = $("<option></option>").val(data.uom2).text(data.uom2);
                $("#sel2_uom2").empty().append($newOptionuom2).trigger('change');
              },
              error: function (xhr, ajaxOptions, thrownError){
                  alert('Error data');
                  alert(xhr.responseText);
              }
          });
        });

         //select 2 uom
        $('.sel2_uom').select2({
          allowClear: true,
          placeholder: "",
          ajax:{
                dataType: 'JSON',
                type : "POST",
                url : "<?php echo base_url();?>ppic/billofmaterials/get_uom_select2",
                data : function(params){

                  return{
                    prod:params.term,
                  };
                }, 
                processResults:function(data){
                  var results = [];
                  $.each(data, function(index,item){
                    results.push({
                        id:item.short,
                        text:item.short
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

        $("#example1_processing").css('display',''); 
        var kode_bom  = "<?php echo $head->kode_bom; ?>";
        $.ajax({
            url : '<?php echo site_url('ppic/billofmaterials/get_bom_items_for_edit') ?>',
            type: "POST",
            dataType : "JSON",
            data: {kode_bom:kode_bom},
            beforeSend: function(e) {
                $('#components tbody').remove();
            },
            success: function(data){
             
                var row = '';
                $('#components').append("<tbody></tbody>");
                $.each(data.record1, function(key, value) {
                    tambah_baris(true,value.kode_produk,value.nama_produk ,value.qty, value.uom,value.qty2, value.uom2, value.note);
                });

                // replace tfoot
                $('#components tfoot tr').remove();
                event = "tambah_baris(false,'','','','','','','')";
                var tr ='<tr><td colspan="7"> <a href="javascript:void(0)" onclick="'+event+'"><i class="fa fa-plus"></i> Tambah Data</a></td></tr>';
                $('#components tfoot').append(tr);

                $("#example1_processing").css('display','none'); 
                
            },
            error: function (xhr, ajaxOptions, thrownError){
                alert('Error data');
                alert(xhr.responseText);
                $("#example1_processing").css('display','none'); 

            }

        });

    });


    $(document).on('click','#btn-cancel', function(e){

        var dialog = bootbox.dialog({
            title: "<font color='red'><i class='fa fa-warning'></i></font> Warning !",
            message: "<p>Tinggalkan perubahan yang belum anda simpan </p>",
            size: 'medium',
            buttons: {
              ok: {
                label: "Yes",
                className: 'btn-primary btn-sm',
                callback: function(){
                  reloadForm()
                }
              },
              cancel: {
                  label: "No",
                  className: 'btn-default btn-sm',
                    callback: function(){
                  }
              },
            }
        });
         

    });


    function reloadForm(){
        $("#btn-simpan").hide();//sembuyikan btn-simpan
        $("#btn-cancel").hide();//sembuyikan btn-cancel
        $("#btn-edit").show();//tampilkan btn-edit

        $("#nama_bom").attr("readonly", true);
        $("#qty").attr("readonly", true);
        $("#qty2").attr("readonly", true);

        $("#sel2_nama_produk").prop("disabled", true);
        $("#sel2_uom").attr("disabled", true);
        $("#sel2_uom2").attr("disabled", true);
        $('#status').attr('disabled', true);

        $('#sel2_nama_produk').removeClass('select2-hidden-accessible');
        $('#div-nama_produk span.select2-container--default').remove();

        $('#sel2_uom').removeClass('select2-hidden-accessible');
        $('#div-uom span.select2-container--default').remove();

        $('#sel2_uom2').removeClass('select2-hidden-accessible');
        $('#div-uom2 span.select2-container--default').remove();

        reloadItems();
    }


    function tambah_baris(data,kode_produk,nama_produk,qty,uom,qty2,uom2,reff_note){
        var tambah = true;

        var index = $("#components tbody tr:last-child").index();
        if(index == -1 ){
            row = 0;
        }else{
            row = parseInt($("#components tbody tr:last-child td .row").val());
        }
        
        event   = "enter(event)";
        delRow  = "delRowItems(this)";
        row_idx = row;
        tbl     = "#components tbody";

        var np = $(tbl+" td input[name='Product']");
        var inx_np = np.length-1;

        var n_qty     = $(tbl+" td input[name='Qty']");
        var inx_n_qty =  n_qty.length-1
        var n_uom     = $(tbl+" td input[name='Uom']");
        var inx_n_uom = n_uom.length-1;

        var n_qty2     = $(tbl+" td input[name='Qty2']");
        var inx_n_qty2 = n_qty2.length-1
        var n_uom2     = $(tbl+" td input[name='Uom2']");
        var inx_n_uom2 = n_uom2.length-1;


        //cek Product apa ada yg kosong
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
           
              $(this).parents('td').find('span span.selection span.select2-selection').removeClass('error'); 
        
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

            var class_produk = 'kode_produk_'+ro;
            var produk       = 'nama_produk'+ro;
            var class_uom    = 'uom_'+ro;
            var class_uom2   = 'uom2_'+ro;
            var row          = '<tr class="num">'
                              + '<td><input type="hidden"  name="row" class="row" value="'+ro+'"></td>'
                              + '<td  class="min-width-200">'
                                  + '<select add="manual" type="text" class="form-control input-sm kode_produk '+class_produk+'" name="tProduct" id="tkode_produk"></select>'
                                  + '<input type="text" class="form-control input-sm nama_produk '+produk+'" name="Product" id="tnama_produk" value="'+htmlentities_script(nama_produk)+'"></td>'
                              + '<td class="min-width-100"><input type="text" class="form-control input-sm qty" name="Qty" id="tqty"  onkeyup="validAngka(this)" onkeypress="'+event+'"  value="'+qty+'"></td>'
                              + '<td class="min-width-100"><select type="text" class="form-control input-sm uom '+class_uom+'" name="Uom" id="tuom"></select></td>'
                              + '<td class="min-width-100"><input type="text" class="form-control input-sm qty2" name="Qty2" id="tqty2"  onkeyup="validAngka(this)" onkeypress="'+event+'"  value="'+qty2+'"></td>'
                              + '<td class="min-width-100"><select type="text" class="form-control input-sm uom2 '+class_uom2+'" name="Uom2" id="tuom2"></select></td>'
                              + '<td class="min-width-100"><textarea type="text" class="form-control input-sm" name="note" id="treff" onkeypress="'+event+'"  >'+htmlentities_script(reff_note)+'</textarea></td>'
                              + '<td class="width-50" align="center"><a onclick="'+delRow+'"  href="javascript:void(0)"  data-toggle="tooltip" title="Hapus Data"><i class="fa fa-trash" style="color: red"></i> </a></td>'
                              + '</tr>';

            $(tbl).append(row);
            $('[data-toggle="tooltip"]').tooltip();

            var sel_produk = $('#components tr .'+class_produk);
            var sel_uom    = $('#components tr .'+class_uom);
            var sel_uom2   = $('#components tr .'+class_uom2);
            var produk_hide= $('#components tr .'+produk);

            if(data==true){
                //untuk event selected select2 nama_produk
                custom_nama = '['+kode_produk+'] '+nama_produk;
                var $newOption = $("<option></option>").val(kode_produk).text(custom_nama);
                sel_produk.empty().append($newOption).trigger('change');

                var $newOption2 = $("<option></option>").val(uom).text(uom);
                sel_uom.empty().append($newOption2).trigger('change');

                var $newOption3 = $("<option></option>").val(uom2).text(uom2);
                sel_uom2.empty().append($newOption3).trigger('change');
            }

            //select 2 product
            sel_produk.select2({
                ajax:{
                    dataType: 'JSON',
                    type : "POST",
                    url : "<?php echo base_url();?>ppic/billofmaterials/get_produk_bom_select2",
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
                        console.log(xhr.responseText);
                    }
                }
            });
          

            if(data==false){
              // alert('added belum '+JSON.stringify());
              var s2element = $(this).parents(tbl).find(np[inx_np]).siblings('select');
              s2element.select2('open');
              s2element.on('select2:closing', function (e) {
                s2element.select2('focus');
              });
            }
              
            //jika nama produk diubah
            sel_produk.change(function(){
                
                $.ajax({
                    dataType: "JSON",
                    url : '<?php echo site_url('ppic/billofmaterials/get_prod_by_id') ?>',
                    type: "POST",
                    data: {kode_produk: $(this).parents("tr").find("#tkode_produk").val() },
                    success: function(data){
                        produk_hide.val(data.nama_produk);
                        //untuk event selected select2 uom
                        var $newOptionuom = $("<option></option>").val(data.uom).text(data.uom);
                        sel_uom.empty().append($newOptionuom).trigger('change');
                        var $newOptionuom2 = $("<option></option>").val(data.uom2).text(data.uom2);
                        sel_uom2.empty().append($newOptionuom2).trigger('change');
                    },
                    error: function (xhr, ajaxOptions, thrownError){
                        console.log(xhr.responseText);
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
                        url : "<?php echo base_url();?>ppic/billofmaterials/get_uom_select2",
                        data : function(params){
                            return{
                                prod:params.term,
                            };
                        }, 
                        processResults:function(data){
                            var results = [];
                            $.each(data, function(index,item){
                                results.push({
                                    id:item.short,
                                    text:item.short
                                });
                            });
                            return {
                                results:results
                            };
                        },
                        error: function (xhr, ajaxOptions, thrownError){
                            //alert('Error data');
                            console.log(xhr.responseText);
                        }
                }
            });

            //select 2 uom2
            sel_uom2.select2({
                allowClear: true,
                placeholder: "",
                ajax:{
                        dataType: 'JSON',
                        type : "POST",
                        url : "<?php echo base_url();?>ppic/billofmaterials/get_uom_select2",
                        data : function(params){
                            return{
                                prod:params.term,
                            };
                        }, 
                        processResults:function(data){
                            var results = [];
                            $.each(data, function(index,item){
                                results.push({
                                    id:item.short,
                                    text:item.short
                                });
                            });
                            return {
                                results:results
                            };
                        },
                        error: function (xhr, ajaxOptions, thrownError){
                            //alert('Error data');
                            console.log(xhr.responseText);
                        }
                }
            });

        }

    }

    function delRowItems(r){		
	  	var i = r.parentNode.parentNode.rowIndex;
		  document.getElementById("components").deleteRow(i);
    }

    function enter(e,table){
      if(e.keyCode === 13){
	        e.preventDefault(); 
	        tambah_baris(false,'','','','','','',''); //panggil fungsi tambah baris
	    }
	  }
	

    //reload component bom items
    function  reloadItems(){

        var kode_bom = "<?php echo $head->kode_bom;?>";

        $.ajax({
            url : '<?php  echo site_url('ppic/billofmaterials/get_bom_items_for_edit')?>',
            type: "POST",
            dataType : "JSON",
            data:{kode_bom : kode_bom},
            beforeSend : function(e){
                $("#components tbody").remove();
                $("#example1_processing").css('display','');
                
            },
            success : function(data){
                //setTimeout(function() {$('#components tbody').html(html);  });
                let no    = 1;
                // $('#components').append("<tbody></tbody>");
                let tbody = $("<tbody />");
                $.each(data.record1, function(key, value) {
                  value_qty = parseFloat(value.qty);
                  value_qty2 = parseFloat(value.qty2);
                  var tr = $("<tr>").append(
                      $("<td>").text(no++),
                      $("<td>").text("["+value.kode_produk+"] "+value.nama_produk),
                      $("<td align='right'>").text(formatNumber(value_qty.toFixed(2))),
                      $("<td>").text(value.uom),
                      $("<td align='right'>").text(formatNumber(value_qty2.toFixed(2))),
                      $("<td>").text(value.uom2),
                      $("<td>").text(value.note),
                    );
                    tbody.append(tr);
                  });
                $("#components").append(tbody);
                $("#example1_processing").css('display','none');
                $('#components tfoot tr').remove();

            },
            error : function (xhr, ajaxOptions, thrownError){
              alert('Error Load items');
            }

        });
    }


 
    //klik button simpan
    $('#btn-simpan').click(function(){

        var arr   = new Array();
        var empty = false;
        var empty2 = false;
        var empty3 = false;
        $("#components tbody #tkode_produk").each(function(index, element) {
              if ($(element).val()!=="" && $(element).val()!==null) {
                arr.push({
                  kode_produk :$(element).val(),
                  nama_produk :$(element).parents("tr").find("#tnama_produk").val(),
                  qty 		    :$(element).parents("tr").find("#tqty").val(),
                  uom 		    :$(element).parents("tr").find("#tuom").val(),
                  qty2 		    :$(element).parents("tr").find("#tqty2").val(),
                  uom2 		    :$(element).parents("tr").find("#tuom2").val(),
                  reff_note 	:$(element).parents("tr").find("#treff").val(),
                });
              }else{
                empty = true;
                $(this).parents('td').find('span span.selection span.select2-selection').addClass('error'); 
                alert_notify('fa fa-warning','Product Harus Diisi !','danger',function(){});
              }
        });

        $("#components tbody #tqty").each(function(index, element) {
					if ($(element).val()=='' ) {
            empty2 = true;
            alert_notify('fa fa-warning','Qty Harus Diisi !','danger',function(){});
            $(this).addClass('error'); 
          }else if($(element).val()==0){
            empty2 = true;
            alert_notify('fa fa-warning','Qty tidak boleh Kosong !','danger',function(){});
            $(this).addClass('error'); 
					}else{
            $(this).removeClass('error'); 
          }
		    }); 
        
        $("#components tbody #tuom").each(function(index, element) {
            if ($(element).val()=="" || $(element).val()==null ) {
              empty3 = true;
              alert_notify('fa fa-warning','Uom Harus Diisi !','danger',function(){});
              $(this).parents('td').find('span span.selection span.select2-selection').addClass('error'); 
            }else{
              $(this).parents('td').find('span span.selection span.select2-selection').removeClass('error'); 
            }
        }); 


        // $("#components tbody .qty2").each(function(index, element) {
				// 	if ($(element).val()=="" ) {
        //     empty2 = true;
        //     alert_notify('fa fa-warning','Qty2 Harus Diisi !','danger',function(){});
        //     $(this).addClass('error'); 
				// 	}else{
        //     $(this).removeClass('error'); 
        //   }
		    // }); 

        // $("#components tbody .uom2").each(function(index, element) {
				// 	if ($(element).val()=="" ) {
        //     empty2 = true;
        //     alert_notify('fa fa-warning','Uom2 Harus Diisi !','danger',function(){});
        //     $(this).addClass('error'); 
				// 	}else{
        //     $(this).removeClass('error'); 
        //   }
		    // }); 

        if(!empty && !empty2 && !empty3 ){

          $('#btn-simpan').button('loading');
          please_wait(function(){});
          $.ajax({
            type: "POST",
            dataType: "json",
            url :'<?php echo base_url('ppic/billofmaterials/simpan')?>',
            beforeSend: function(e) {
                if(e && e.overrideMimeType) {
                    e.overrideMimeType("application/json;charset=UTF-8");
                }
            },
            data: {kode_bom       : $('#kode_bom').val(),
                    nama_bom       : $('#nama_bom').val(),
                    kode_produk    : $('#sel2_nama_produk').val(),
                    nama_produk    : $('#nama_produk').val(),
                    qty            : $('#qty').val(),
                    uom            : $('#sel2_uom').val(),
                    qty2           : $('#qty2').val(),
                    uom2           : $('#sel2_uom2').val(),
                    status         : $('#status').val(),
                    arr_item       : JSON.stringify(arr),
              },success: function(data){
                if(data.sesi == "habis"){
                  //alert jika session habis
                  alert_modal_warning(data.message);
                  window.location.replace('index');
                }else if(data.status == "failed"){
                  //jika ada form belum keiisi
                  unblockUI( function() {
                    setTimeout(function() { alert_notify(data.icon,data.message,data.type,function(){}); }, 1000);
                  });
                  $("#box-title").load(location.href + " #box-title");
                  // refresh_bom();
                    document.getElementById(data.field).focus();//focus ke field yang belum keisi
                }else{
                  //jika berhasil disimpan
                  unblockUI( function() {
                    setTimeout(function() { 
                      alert_notify(data.icon,data.message,data.type,function(){}); 
                    },1000);
                  });
                  reloadForm();
                  $("#box-title").load(location.href + " #box-title");
                  // refresh_bom();
                }
                $("#foot").load(location.href + " #foot");
                $('#btn-simpan').button('reset');

              },error: function (xhr, ajaxOptions, thrownError) {
                alert(xhr.responseText);
                unblockUI( function(){});
                $('#btn-simpan').button('reset');
              }
          });

        }
    });
   
</script>


</body>
</html>
