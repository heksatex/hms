<!DOCTYPE html>
<html>
<head>
  <?php $this->load->view("admin/_partials/head.php") ?>

  <style type="text/css">
    table.table td .add {
        display: none;
    }
    
    table.table td .cancel {
        display: none;
        color : red;
        margin: 10 0px;
        min-width:  24px;
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
                  <div class="col-xs-4"><label>Nama Produk </label></div>
                  <div class="col-xs-8">
                    <select type="text" class="form-control input-sm" name="sel2_nama_produk" id="sel2_nama_produk"/></select>
                  </div>
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Nama BOM </label></div>
                  <div class="col-xs-8">
                    <input type="text" class="form-control input-sm" name="nama_bom" id="nama_bom"  value="<?php echo htmlentities($head->nama_bom); ?>"/>
                    <input type="hidden" class="form-control input-sm" name="nama_produk" id="nama_produk" readonly="readonly"  value="<?php echo htmlentities($head->nama_produk) ?>" />
                  </div>
                </div>

             
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Qty </label></div>
                  <div class="col-xs-4">
                    <input type="text" class="form-control input-sm" name="qty" id="qty" value="<?php echo $head->qty?>" onkeyup="validAngka(this)" />
                  </div>
                  <div class="col-xs-4">
                     <select class="form-control input-sm" name="sel2_uom" id="sel2_uom" > </select>    
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
                              <th class="style">Nama Produk</th>
                              <th class="style">Qty</th>
                              <th class="style">Uom</th>
                              <th class="style">Note</th>
                              <th class="style"></th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php 
                              $no = 1;
                              foreach ($items as $row) {?>
                                <tr>
                                  <td data-content="edit" data-id="row_order" data-isi="<?php echo $row->row_order."^|".$row->kode_produk."^|".htmlentities($row->nama_produk)."^|".$row->qty."^|".$row->uom."^|".htmlentities($row->note)."^|".$row->kode_bom?>" data-isi2="<?php echo $row->row_order; ?>"><?php echo $no++;?></td>
                                  <td class="text-wrap width-400" data-content="edit" data-id="kode_produk" data-isi="<?php echo $row->kode_produk?>" data-id2="prodhidd" data-isi2="<?php echo htmlentities($row->nama_produk)?>"><?php echo '['.$row->kode_produk.'] '.$row->nama_produk;?></td>
                                  <td class="width-100" data-content="edit" data-id="qty" data-isi="<?php echo $row->qty ?>"><?php echo number_format($row->qty,2);?></td>
                                  <td class="width-100" data-content="edit" data-id="uom" data-isi="<?php echo $row->uom ?>"><?php echo $row->uom;?></td>
                                  <td class="text-wrap width-300" data-content="edit" data-id="note" data-isi="<?php echo htmlentities($row->note) ?>"><?php echo $row->note;?></td>
                                  <td class="width-200" align="center">
                                    <a href="javascript:void(0)" class="add" title="Simpan" data-toggle="tooltip" ><i class="fa fa-save"></i></a>
                                    <a href="javascript:void(0)" class="edit" title="Edit" data-toggle="tooltip" style="color: #FFC107;   margin-right: 24px;"><i class="fa fa-edit"></i></a>
                                    <a href="javascript:void(0)" class="delete" title="Hapus" data-toggle="tooltip"><i class="fa fa-trash" style="color: red"></i></a>
                                    <a href="javascript:void(0)" class="cancel" title="Cancel" data-toggle="tooltip" style="margin-left: 20px;"><i class="fa fa-close"></i></a>
                                  </td>
                                </tr>
                            <?php 
                              }
                            ?>
                          </tbody>
                          <tfoot>
                            <tr>
                              <td colspan="7">
                                <a href="javascript:void(0)" class="add-new"><i class="fa fa-plus"></i> Tambah Data</a>
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
    <div id="foot">
      <?php $this->load->view("admin/_partials/footer.php") ?>
    <div id="foot">
  </footer>

</div>

<?php $this->load->view("admin/_partials/js.php") ?>

<script type="text/javascript">

    //html entities javascript
    function htmlentities_script(str) {
        return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    function validAngka(a){
      if(!/^[0-9.]+$/.test(a.value)){
        a.value = a.value.substring(0,a.value.length-1000);
        alert_notify('fa fa-warning','Maaf, Inputan Qty Hanya Berupa Angka !','danger');
      }
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
          },
          error: function (xhr, ajaxOptions, thrownError){
              alert('Error data');
              alert(xhr.responseText);
          }
      });
    });

    
    //select 2 uom
    $('#sel2_uom').select2({
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
              alert('Error data');
              alert(xhr.responseText);
            }
      }
    });


    // Append table with add row form on add new button click
    $(document).on("click", ".add-new", function(){
    
      $(".add-new").hide();
      var index = $("#components tbody tr:last-child").index();
      var row   ='<tr class="">'
            + '<td></td>'
            + '<td  class="width-400"><select type="text" class="form-control input-sm prod" name="Product" id="tproduct"></select></select><input type="hidden" class="form-control input-sm prodhidd" name="prodhidd" id="tprodhidd"></td>'
            + '<td class="width-100"><input type="text" class="form-control input-sm qty" name="Qty" id="tqty"  onkeyup="validAngka(this)" ></td>'
            + '<td class="width-100"><select type="text" class="form-control input-sm uom" name="Uom" id="tuom"></select></td>'
            + '<td class="width-300"><textarea type="text" class="form-control input-sm" name="note" id="tnote"></textarea></td>'
            + '<td class="width-200" align="center"><button type="button" class="btn btn-primary btn-xs add width-btn" title="Simpan" data-toggle="tooltip">Simpan</button><a class="edit" title="Edit" data-toggle="tooltip"><i class="fa fa-edit"></i></a><button type="button" class="btn btn-danger btn-xs batal width-btn" title="Batal" data-toggle="tooltip">Batal</button></td>'
            + '</tr>';

          $('#components tbody').append(row);
          $("#components tbody tr").eq(index + 1).find(".add, .edit").toggle();
          $('[data-toggle="tooltip"]').tooltip();

          //select 2 product
          $('.prod').select2({
            allowClear: true,
            placeholder: "",
            ajax:{
                  dataType: 'JSON',
                  type : "POST",
                  url : "<?php echo base_url();?>ppic/billofmaterials/get_produk_bom_select2",
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
                            text:'['+item.kode_produk+'] '+item.nama_produk
                        });
                    });
                    return {
                      results:results
                    };
                  },
                  error: function (xhr, ajaxOptions, thrownError){
                  //  alert('Error data');
                  //  alert(xhr.responseText);
                  }
            }
          });

          $(".prod").change(function(){
              $.ajax({
                    dataType: "JSON",
                    url : '<?php echo site_url('ppic/billofmaterials/get_prod_by_id') ?>',
                    type: "POST",
                    data: {kode_produk: $(this).parents("tr").find("#tproduct").val() },
                    success: function(data){
                      $('.prodhidd').val(data.nama_produk);
                      $('.qty').val('1');
                      //$('.uom').val(data.uom);

                      var $newOptionuom = $("<option></option>").val(data.uom).text(data.uom);
                      $(".uom").empty().append($newOptionuom).trigger('change');
                    },
                    error: function (xhr, ajaxOptions, thrownError){
                    //  alert('Error data');
                    //  alert(xhr.responseText);
                    }
              });
          });


          //select 2 uom di table
          $('.uom').select2({
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
                  //  alert('Error data');
                  //  alert(xhr.responseText);
                  }
            }
          });       
    });

    //batal add row on batal button click
    $(document).on("click", ".batal", function(){
      var input = $(this).parents("tr").find('.prod');
      input.each(function(){
        $(this).parent("td").html($(this).val());
      }); 
        
      $(this).parents("tr").remove();
      $(".add-new").show();
    }); 

    

    //simpan / edit row data ke database
    $(document).on("click", ".add", function(){
      var empty = false;
      var input = $(this).parents("tr").find('input[type="text"]');

      var empty2 = false;
      var select = $(this).parents("tr").find('select[type="text"]');


      //validasi tidak boleh kosong select2
      select.each(function(){
        if(!$(this).val() && $(this).attr('name')=='Product' ){
          alert_notify('fa fa-warning',' Nama Produk Harus Diisi !','danger');
          empty2 = true;
        }

        if(!$(this).val() && $(this).attr('name')=='Uom' ){
          alert_notify('fa fa-warning',' Uom Harus Diisi !','danger');
          empty2 = true;
        }
      });


      // validasi untuk inputan textbox
      input.each(function(){
        if(!$(this).val() && $(this).attr('name') =='Qty'){
          alert_notify('fa fa-warning',' Qty Harus Diisi !','danger');
          empty = true;
        }

      });


      if(!empty && !empty2){
        
        var kode  = "<?php echo $head->kode_bom; ?>";
        var kode_produk  = $(this).parents("tr").find("#tproduct").val();
        var nama_produk  = $(this).parents("tr").find("#tprodhidd").val();
        var qty   = $(this).parents("tr").find("#tqty").val();
        var uom   = $(this).parents("tr").find("#tuom").val();
        var note  = $(this).parents("tr").find("#tnote").val();
        var row_order = $(this).parents("tr").find("#row_order").val();
        var btn_loading   = $(this);
        btn_loading.button('loading');
        
        $.ajax({
          dataType: "JSON",
          url : '<?php echo site_url('ppic/billofmaterials/simpan_bom_items') ?>',
          type: "POST",
          data: {kode : kode, 
                kode_produk : kode_produk,
                nama_produk : nama_produk,
                qty   : qty, 
                uom   : uom,
                note  : note,             
                row_order : row_order  },
          success: function(data){
            if(data.sesi=='habis'){
                //alert jika session habis
                alert_modal_warning(data.message);
                window.location.replace('../index');
            }else if(data.status == 'failed'){
                refresh_bom();
                alert_modal_warning(data.message);
            }else{
                refresh_bom();
                $(".add-new").show();                   
                alert_notify(data.icon,data.message,data.type);
            }
            btn_loading.button('loading');
          },
          error: function (xhr, ajaxOptions, thrownError){
            alert('Error data');
            alert(xhr.responseText);
            btn_loading.button('loading');
          }
        });
        
      }   
    });


    // Edit row on edit button click
    $(document).on("click", ".edit", function(){  
        $(this).parents("tr").find("td[data-content='edit']").each(function(){

          if($(this).attr('data-id')=="row_order"){
            $(this).html('<input type="hidden"  class="form-control" value="' + htmlentities_script($(this).attr('data-isi')) + '" id="'+ $(this).attr('data-id') +'"> ');

            row_order = $(this).attr('data-isi2');
          }else if($(this).attr('data-id')=='kode_produk'){

            var kode_produk = ($(this).attr('data-isi'));
            var nama_produk = ($(this).attr('data-isi2'));

            class_sel2_prod = 't_sel2_prod'+row_order;
            class_nama_produk = 'e_nama_produk'+row_order;
            //alert(class_sel2_prod);
            //select 2 nama produk dan textfield kode_produknya
            $(this).html('<select type="text"  class="form-control input-sm '+class_sel2_prod+' " id="tproduct" name="Product" ></select> ' + '<input type="hidden"  class="form-control '+class_nama_produk+' " value="' + $(this).attr('data-isi2') + '" id="t'+ $(this).attr('data-id2') +'"> ');

            //var $newOption = $("<option></option>").val(kode_produk).text(nama_produk);
            //$('.t_sel2_prod'+row_order).empty().append($newOption).trigger('change');
            
            //var $newOption = $("<option></option>").val(kode_produk).text(nama_produk);
            //$('.t_sel2_prod'+row_order).append("<option value ='"+kode_produk+"' selected)[MF118] "+nama_produk+"</option>");
            custom_nama = '['+kode_produk+'] '+nama_produk;
            $newOption = new Option(custom_nama, kode_produk, true, true);
            $('.'+class_sel2_prod).append($newOption).trigger('change');
            //select 2 product
            $('.'+class_sel2_prod).select2({
              allowClear: true,
              placeholder: "",
              ajax:{
                    dataType: 'JSON',
                    type : "POST",
                    url : "<?php echo base_url();?>ppic/billofmaterials/get_produk_bom_select2",
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
                              text:'['+item.kode_produk+'] '+item.nama_produk
                          });
                      });
                      return {
                        results:results
                      };
                    },
                    error: function (xhr, ajaxOptions, thrownError){
                    //  alert('Error data');
                    //  alert(xhr.responseText);
                    }
              }
            });
        
            $('.'+class_sel2_prod).change(function(){
                var this1 = $(this);
                $.ajax({
                      dataType: "JSON",
                      url : '<?php echo site_url('ppic/billofmaterials/get_prod_by_id') ?>',
                      type: "POST",
                      data: {kode_produk: $(this).parents("tr").find("#tproduct").val() },
                      success: function(data){
                        this1.parents('tr').find("td #tprodhidd").val(data.nama_produk);
                        this1.parents('tr').find("td #tuom").val(data.uom);

                        //$('.e_nama_produk'+row_order).val(data.nama_produk);
                        //$('.e_').val('1');
                        //$('.uom').val(data.uom);

                        var $newOptionuom = $("<option></option>").val(data.uom).text(data.uom);
                        this1.parents('tr').find('td #tuom').empty().append($newOptionuom).trigger('change');
                      },
                      error: function (xhr, ajaxOptions, thrownError){
                        alert('Error data');
                        alert(xhr.responseText);
                      }
                });
            });
            
          
          }else if($(this).attr('data-id')=='qty'){
            $(this).html('<input type="text"  class="form-control input-sm" value="'+ ($(this).attr('data-isi')) +'" id="t'+ $(this).attr('data-id') +'" name="Qty" onkeyup="validAngka(this)"> ');
            
          }else if($(this).attr('data-id')=='uom'){

            class_uom = 'e_uom'+row_order;

            $(this).html('<select type="text"  class="form-control input-sm '+class_uom+'" id="t'+ $(this).attr('data-id') +'" name="Uom" ></select> ');

            var $newOptionuom = $("<option></option>").val($(this).attr('data-isi') ).text($(this).attr('data-isi') );
            $(".e_uom"+row_order).empty().append($newOptionuom).trigger('change');

            $('.e_uom'+row_order).select2({
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
                    alert('Error data');
                    alert(xhr.responseText);
                  }
            }
            });       
          
          }else if($(this).attr('data-id')=="note"){
            
            $(this).html('<textarea type="text" class="form-control input-sm" id="t'+ $(this).attr('data-id') +'" name="'+ $(this).attr('data-id') +'">'+ htmlentities_script($(this).attr('data-isi')) +'</textarea>');
          }

        });  

        $(this).parents("tr").find(".add, .edit").toggle();
        $(this).parents("tr").find(".cancel, .delete").toggle();
        $(".add-new").hide();

    });

    
    //btn cancel edit
    $(document).on("click", ".cancel", function(){
      $(".add-new").show();
      refresh_bom();

    });

    //delete row di database
    $(document).on("click", ".delete", function(){ 
      $(this).parents("tr").find("td[data-content='edit']").each(function(){
        if($(this).attr('data-id')=="row_order"){
          $(this).html('<input type="hidden" class="form-control" value="' + htmlentities_script($(this).attr('data-isi')) + '" id="'+ $(this).attr('data-id') +'"> ');
        }
      });
      var kode  =  "<?php echo $head->kode_bom; ?>";
      var row_order = $(this).parents("tr").find("#row_order").val();  
      var icon_loading= $(this);
      
      bootbox.dialog({
        message: "Apakah Anda ingin menghapus data ?",
        title: "<i class='glyphicon glyphicon-trash'></i> Delete !",
        buttons: {
          danger: {
              label    : "Yes ",
              className: "btn-primary btn-sm",
              callback : function() {
                  $.ajax({
                      dataType: "JSON",
                      url : '<?php echo site_url('ppic/billofmaterials/hapus_bom_items') ?>',
                      type: "POST",
                      data: {kode : kode, 
                            row_order : row_order  },
                      beforeSend: function(e) {
                        icon_loading.button('loading');
                      },
                      success: function(data){
                        if(data.sesi=='habis'){
                            //alert jika session habis
                            alert_modal_warning(data.message);
                            window.location.replace('../index');
                        }else if(data.status == 'failed'){
                            refresh_bom();
                            alert_modal_warning(data.message);
                        }else{
                            refresh_bom();
                            $(".add-new").show();                   
                            alert_notify(data.icon,data.message,data.type);
                        }
                        icon_loading.button('reset');
                      },
                      error: function (xhr, ajaxOptions, thrownError){
                        alert('Error data');
                        alert(xhr.responseText);
                        icon_loading.button('reset');
                      }
                    });
              }
          },
          success: {
                label    : "No",
                className: "btn-default  btn-sm",
                callback : function() {
                  $('.bootbox').modal('hide');
                  refresh_bom();
                }
          }
        }
        });
        
    });
    

    //klik button simpan
    $('#btn-simpan').click(function(){

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
                  uom            : $('#sel2_uom').val()

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
                $("#box-title").load(location.href + " #box-title");
                refresh_bom();

              }else{
                //jika berhasil disimpan
                unblockUI( function() {
                  setTimeout(function() { 
                    alert_notify(data.icon,data.message,data.type,1000); 
                  });
                });
                $("#box-title").load(location.href + " #box-title");
                refresh_bom();
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
