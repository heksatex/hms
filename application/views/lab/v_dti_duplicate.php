
<!DOCTYPE html>
<html>
<head>
  <?php $this->load->view("admin/_partials/head.php") ?>
  <!-- color picker -->
  <link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/colorpicker/bootstrap-colorpicker.min.css') ?>">
  <style type="text/css">
    .div1 {
      width: 100%;
      border: 1px solid;
      border-color: #d2d6de;
      padding: 50px;
      margin: 10px 0px 10px 0px;
      border-radius: 5px;
    }

    button[id="btn-generate"],
    button[id="btn-duplicate"]{
      display: none;
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

  </style>

</head>

<body class="hold-transition skin-black fixed sidebar-mini" >
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
          <h3 class="box-title">Form Add (Duplicate)</h3>
          
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
                  <div class="col-xs-4"><label>Tanggal dibuat </label></div>
                  <div class="col-xs-8 col-md-8">
                      <input type='text' class="form-control input-sm" name="tgl" id="tgl" readonly="readonly" value="<?php echo date('Y-m-d H:i:s')?>" />
                  </div>                                    
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Nama Warna </label></div>
                  <div class="col-xs-8">
                    <input type="text" class="form-control input-sm" name="warna" id="warna" value="<?php echo $color->nama_warna?>"/>
                  </div>                                    
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Notes </label></div>
                  <div class="col-xs-8">
                    <textarea type="text" class="form-control input-sm" name="note" id="note"><?php echo  $color->notes?></textarea>
                  </div>                                    
                </div>
                
              </div>

              <div class="col-md-6">
               <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Kode Warna </label></div>
                  <div class="col-xs-8 col-md-8">
                    <div class="input-group my-colorpicker" id="my-colorpicker">
                      <input type="text" class="form-control input-sm" id="kode_warna" name="kode_warna" value="<?php echo $color->kode_warna?>" >
                      <span class="input-group-addon" id='groupColor' >
                           <i id="wstyle" ></i>
                      </span>
                    </div>
                  </div>                                    
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label></label></div>
                  <div class="col-xs-8 col-md-8">
                    <div class="div1" id="content_colors" style="background-color: <?php echo $color->kode_warna?>;" >
                    </div>
                  </div>                                    
                </div>
              </div>

            </div>
          </form>
          <div class="row">
              <div class="col-md-12">
                <!-- Custom Tabs -->
                <div class="">
                  <ul class="nav nav-tabs">
                    <li class="active"><a href="#tab_1" data-toggle="tab">Details</a></li>
                  </ul>
                  <div class="tab-content"><br>
                    <div class="tab-pane active" id="tab_1">

                      <!-- Tabel Dye stuff  -->
                      <div class="col-md-6 table-responsive">
                        <table class="table table-condesed table-hover rlstable" width="100%" id="table_dyest" >
                          <label>Dyeing Stuff</label>
                          <tr>
                            <th class="style no">No.</th>
                            <th class="style">Product</th>
                            <th class="style">qty (%)</th>
                            <th class="style">uom</th>
                            <th class="style">reff notes</th>
                            <th class="style"></th>
                          </tr>
                          <tbody id="tbody_dye">
                           
                          </tbody>
                          <tfoot>
                            <tr>
                              <td colspan="8">
                                 <a href="javascript:void(0)" onclick="tambah_baris(false,'','','','','','')"><i class="fa fa-plus"></i> Tambah Data</a>
                              </td>
                            </tr>
                          </tfoot>
                        </table>
                      </div>
                      <!-- Tabel Dye stuff -->

                      <!-- Tabel AUX  -->
                      <div class="col-md-6 table-responsive">
                        <table class="table table-condesed table-hover rlstable" width="100%" id="table_aux" >
                          <label>Auxiliary</label>
                          <tr>
                            <th class="style no">No.</th>
                            <th class="style">Product</th>
                            <th class="style">qty (g/L)</th>
                            <th class="style">uom</th>
                            <th class="style">reff notes</th>
                            <th class="style"></th>
                          </tr>
                          <tbody id="tbody_aux">
                          </tbody>
                          <tfoot>
                            <tr>
                              <td colspan="8">
                                 <a href="javascript:void(0)" onclick="tambah_baris_aux(false,'','','','','','')"><i class="fa fa-plus"></i> Tambah Data</a>
                              </td>
                            </tr>
                          </tfoot>
                        </table>
                      </div>
                      <!-- Tabel AUX -->
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
  </footer>

</div>

<?php $this->load->view("admin/_partials/js.php") ?>
<!-- color picker -->
<script src="<?php echo site_url('plugins/colorpicker/bootstrap-colorpicker.min.js') ?>"></script>


<script type="text/javascript">

    $(".my-colorpicker").colorpicker();

    $('.my-colorpicker').colorpicker().on('changeColor', function (e) {
        $('#content_colors')[0].style.backgroundColor = e.color.toHex();
    });

    //validasi qty
	function validAngka(a){
	    if(!/^[0-9.]+$/.test(a.value)){
	        a.value = a.value.substring(0,a.value.length-1000);
	    }
	}

    <?php 
            $no = 1;
            foreach($dyest as $dye){
            ?>
                tambah_baris(true,'<?php echo $dye->kode_produk?>', '<?php echo $dye->nama_produk?>', '<?php echo $dye->qty?>', '<?php echo $dye->uom?>', '<?php echo $dye->reff_note?>');
                <?php 
                $no++;
            }
    ?>  

    function tambah_baris(data,kode_produk,nama_produk,qty,uom,reff_note){
        
        var index  = $("#table_dyest tbody[id='tbody_dye'] tr:last-child").index();
        if(index== -1){
            row = 0;
        }else{
            row  = parseInt($("#table_dyest tbody[id='tbody_dye'] tr:last-child td .row").val());
        }
        var ro     = row+1;
        delRow  = "delRow_dye(this)";
       
        var class_produk = 'kode_produk_'+ro;
        var produk       = 'nama_produk'+ro;
        var class_uom    = 'uom_'+ro;
        var row        = '<tr class="num">'
                    + '<td><input type="hidden"  name="row" class="row" value="'+ro+'"></td>'
                    + '<td  class="min-width-200">'
                        + '<select add="manual" type="text" class="form-control input-sm kode_produk '+class_produk+'" name="Product" id="kode_produk"></select>'
                        + '<input type="text" class="form-control input-sm nama_produk '+produk+'" name="nama_produk" id="nama_produk" value="'+nama_produk+'"></td>'
                    + '<td class="min-width-100"><input type="text" class="form-control input-sm qty" name="Qty" id="qty"  onkeyup="validAngka(this)" value="'+qty+'"></td>'
                    + '<td class="min-width-100"><select type="text" class="form-control input-sm uom '+class_uom+'" name="Uom" id="uom"></select></td>'
                    + '<td class="min-width-100"><textarea type="text" class="form-control input-sm" name="note" id="reff">'+reff_note+'</textarea></td>'
                    + '<td class="width-50" align="center"><a onclick="'+delRow+';"  href="javascript:void(0)"  data-toggle="tooltip" title="Hapus Data"><i class="fa fa-trash" style="color: red"></i> </a></td>'
                    + '</tr>';

        $('#table_dyest tbody[id="tbody_dye"] ').append(row);
        //$("#components tbody tr").eq(index + 1).find(".add, .edit").toggle();
        $('[data-toggle="tooltip"]').tooltip();

        var sel_produk  = $('#table_dyest tbody[id="tbody_dye"] tr .'+class_produk);
        var sel_uom     = $('#table_dyest tbody[id="tbody_dye"] tr .'+class_uom);
        var produk_hide = $('#table_dyest tbody[id="tbody_dye"] tr .'+produk);

        if(data==true){
            //untuk event selected select2 nama_produk
            custom_nama = '['+kode_produk+'] '+nama_produk;
            var $newOption = $("<option></option>").val(kode_produk).text(custom_nama);
            sel_produk.empty().append($newOption).trigger('change');

            var $newOption2 = $("<option></option>").val(uom).text(uom);
            sel_uom.empty().append($newOption2).trigger('change');

        }

        //select 2 product
        sel_produk.select2({
            ajax:{
                dataType: 'JSON',
                type : "POST",
                url : "<?php echo base_url();?>lab/dti/get_list_dye",
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
                    alert('Error data');
                    alert(xhr.responseText);
                }
            }
        });

        //jika nama produk diubah
        sel_produk.change(function(){
            
            $.ajax({
                dataType: "JSON",
                url : '<?php echo site_url('lab/dti/get_prod_by_id') ?>',
                type: "POST",
                data: {kode_produk: $(this).parents("tr").find("#kode_produk").val() },
                success: function(data){
                    produk_hide.val(data.nama_produk);
                    //untuk event selected select2 uom
                    var $newOptionuom = $("<option></option>").val(data.uom).text(data.uom);
                    sel_uom.empty().append($newOptionuom).trigger('change');
                },
                error: function (xhr, ajaxOptions, thrownError){
                    alert('Error data');
                    alert(xhr.responseText);
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
                    url : "<?php echo base_url();?>lab/dti/get_uom_select2",
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
                        alert('Error data');
                        alert(xhr.responseText);
                    }
            }
        });

    };

    <?php 
            $no = 1;
            foreach($aux as $auxs){
            ?>
                tambah_baris_aux(true,'<?php echo $auxs->kode_produk?>', '<?php echo $auxs->nama_produk?>', '<?php echo $auxs->qty?>', '<?php echo $auxs->uom?>', '<?php echo $auxs->reff_note?>','<?php echo $no;?>');
                <?php 
                $no++;
            }
            
    ?>

    function tambah_baris_aux(data,kode_produk,nama_produk,qty,uom,reff_note){

        var index  = $("#table_aux tbody[id='tbody_aux'] tr:last-child").index();
        
        if(index== -1){
          row = 0;
        }else{
          row  = parseInt($("#table_aux tbody[id='tbody_aux'] tr:last-child td .row").val());
        }
        var ro     = row+1;
        delRow  = "delRow_aux(this)";

        var class_produk = 'kode_produk_'+ro;
        var produk       = 'nama_produk'+ro;
        var class_uom    = 'uom_'+ro;
        var row    = '<tr class="num">'
                    + '<td><input type="hidden"  name="row" class="row" value="'+ro+'"></td>'
                    + '<td  class="min-width-200">'
                        + '<select add="manual" type="text" class="form-control input-sm kode_produk '+class_produk+'" name="Product" id="kode_produk"></select>'
                        + '<input type="text" class="form-control input-sm nama_produk '+produk+'" name="nama_produk" id="nama_produk" value="'+nama_produk+'"></td>'
                    + '<td class="min-width-100"><input type="text" class="form-control input-sm qty" name="Qty" id="qty"  onkeyup="validAngka(this)" value="'+qty+'"></td>'
                    + '<td class="min-width-100"><select type="text" class="form-control input-sm uom '+class_uom+'" name="Uom" id="uom"></select></td>'
                    + '<td class="min-width-100"><textarea type="text" class="form-control input-sm" name="note" id="reff">'+reff_note+'</textarea></td>'
                    + '<td class="width-50" align="center"><a onclick="'+delRow+'"  href="javascript:void(0)"  data-toggle="tooltip" title="Hapus Data"><i class="fa fa-trash" style="color: red"></i> </a></td>'
                    + '</tr>';


        $('#table_aux tbody[id="tbody_aux"] ').append(row);
        //$("#components tbody tr").eq(index + 1).find(".add, .edit").toggle();
        $('[data-toggle="tooltip"]').tooltip();

        var sel_produk  = $('#table_aux tbody[id="tbody_aux"] tr .'+class_produk);
        var sel_uom     = $('#table_aux tbody[id="tbody_aux"] tr .'+class_uom);
        var produk_hide = $('#table_aux tbody[id="tbody_aux"] tr .'+produk);

        if(data==true){
            //untuk event selected select2 nama_produk
            custom_nama = '['+kode_produk+'] '+nama_produk;
            var $newOption = $("<option></option>").val(kode_produk).text(custom_nama);
            sel_produk.empty().append($newOption).trigger('change');

            var $newOption2 = $("<option></option>").val(uom).text(uom);
            sel_uom.empty().append($newOption2).trigger('change');
        }


        //select 2 product
        sel_produk.select2({
            ajax:{
                dataType: 'JSON',
                type : "POST",
                url : "<?php echo base_url();?>lab/dti/get_list_aux",
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
                    alert('Error data');
                    alert(xhr.responseText);
                }
            }
        });

        //jika nama produk diubah
        sel_produk.change(function(){
            
            $.ajax({
                dataType: "JSON",
                url : '<?php echo site_url('lab/dti/get_prod_by_id') ?>',
                type: "POST",
                data: {kode_produk: $(this).parents("tr").find("#kode_produk").val() },
                success: function(data){
                    produk_hide.val(data.nama_produk);
                    //untuk event selected select2 uom
                    var $newOptionuom = $("<option></option>").val(data.uom).text(data.uom);
                    sel_uom.empty().append($newOptionuom).trigger('change');
                },
                error: function (xhr, ajaxOptions, thrownError){
                    alert('Error data');
                    alert(xhr.responseText);
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
                    url : "<?php echo base_url();?>lab/dti/get_uom_select2",
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
                        alert('Error data');
                        alert(xhr.responseText);
                    }
            }
        });

    };

    // hapus row
    function delRow_dye(r){		
        var i = r.parentNode.parentNode.rowIndex;
        document.getElementById("table_dyest").deleteRow(i);
    }

    function delRow_aux(r){		
        var i = r.parentNode.parentNode.rowIndex;
        document.getElementById("table_aux").deleteRow(i);
    }
    
    //klik button simpan
    $('#btn-simpan').click(function(){


      var arr   = new Array();
      var arr2  = new Array();
      var id_warna  = '<?php echo $id_warna?>';
      var id_varian = '<?php echo $id_varian?>';
      var nama_warna = '<?php echo $color->nama_warna?>';

      $("#table_dyest tbody[id='tbody_dye'] .kode_produk").each(function(index, element) {
					if ($(element).val()!=="") {
						arr.push({
							//0 : no++,
							kode_produk :$(element).val(),
							nama_produk :$(element).parents("tr").find("#nama_produk").val(),
							qty 		    :$(element).parents("tr").find("#qty").val(),
							uom 		    :$(element).parents("tr").find("#uom").val(),
							reff_note 	:$(element).parents("tr").find("#reff").val(),
						});
					}
			}); 

      $("#table_aux tbody[id='tbody_aux'] .kode_produk").each(function(index, element) {
					if ($(element).val()!=="") {
						arr2.push({
							//0 : no++,
							kode_produk :$(element).val(),
							nama_produk :$(element).parents("tr").find("#nama_produk").val(),
							qty 		    :$(element).parents("tr").find("#qty").val(),
							uom 		    :$(element).parents("tr").find("#uom").val(),
							reff_note 	:$(element).parents("tr").find("#reff").val(),
						});
					}
			}); 

      $('#btn-simpan').button('loading');
      please_wait(function(){});
      $.ajax({
         type: "POST",
         dataType: "json",
         url :'<?php echo base_url('lab/dti/simpan')?>',
         beforeSend: function(e) {
            if(e && e.overrideMimeType) {
                e.overrideMimeType("application/json;charset=UTF-8");
            }
         },
         data: {tanggal    : $('#tgl').val(),
                warna      : $('#warna').val(),
                note       : $('#note').val(),
                kode_warna : $('#kode_warna').val(),
                arr_dye    : JSON.stringify(arr),
                arr_aux    : JSON.stringify(arr2),
                id_warna   : id_warna,
                id_varian  : id_varian,
                nama_warna : nama_warna,
                status     : 'tambah',
                duplicate  : true
          },success: function(data){
            if(data.sesi == "habis"){
              //alert jika session habis
              alert_modal_warning(data.message);
              window.location.replace('index');
            }else if(data.status == "failed"){
              //jika ada form belum keiisi
              unblockUI( function() {
                setTimeout(function() { alert_notify(data.icon,data.message,data.type, function(){}); }, 1000);
              });
              document.getElementById(data.field).focus();
            }else{
             //jika berhasil disimpan/diubah
              unblockUI( function() {
                setTimeout(function() { 
                  alert_notify(data.icon,data.message,data.type, function(){
                    window.location.replace('edit/'+data.isi);
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
   
</script>


</body>
</html>
