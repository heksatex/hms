
<!DOCTYPE html>
<html>
<head>
  <?php $this->load->view("admin/_partials/head.php") ?>
  <style type="text/css">
    table.table td .add {
        display: none;
    }
    .width-btn {
      width: 54px !important;
    }
    table.table td .cancel {
        display: none;
        color : red;
        margin: 10 0px;
        min-width:  24px;
    }
    @media screen and (min-width: 768px) {
      .over {
        overflow-x: visible !important;
      }
    }

    .select2-container{
      border-color: red !important;
    }
    
    .min-width-100{
      min-width: 100px;
    }

    /*
    @media screen and (max-width: 767px) {
      .over {
       overflow-y: scroll !important; 
      }
    }
    */
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
      <div id ="status_bar">
        <?php 
          $data['jen_status'] =  $adjustment->status;
          $this->load->view("admin/_partials/statusbar.php", $data) 
        ?>
      </div>
    </section>

    <!-- Main content -->
    <section class="content">

      <!--  box content -->
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title"><b><?php echo $adjustment->kode_adjustment;?></b></h3>
          <div class="pull-right text-right" id="btn-header">
            <?php if($adjustment->status=='draft'){?>
              <button class="btn btn-primary btn-sm" id="btn-import-produk" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing...">Tampilkan Semua Barang di Lokasi</button>            
            <?php }
            ?>
            </div>
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
                  <div class="col-xs-4"><label>Kode Adjustment </label></div>
                  <div class="col-xs-8">
                    <input type="text" class="form-control input-sm" name="kode_adjustment" id="kode_adjustment" readonly="readonly" value="<?php echo $adjustment->kode_adjustment?>"/>
                  </div>                                    
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Create Date </label></div>
                  <div class="col-xs-8 col-md-8">
                     <input type='text' class="form-control input-sm " name="tgl_buat" id="tgl_buat" readonly="readonly" value="<?php echo $adjustment->create_date?>"/>
                  </div>                                    
                </div>

                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Notes </label></div>
                  <div class="col-xs-8">
                    <textarea type="text" class="form-control input-sm" name="note" id="note"><?php echo $adjustment->note?></textarea>
                  </div>                                    
                </div>
              </div>

              <div class="col-md-6">
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Lokasi Adjustment</label></div>
                  <div class="col-xs-8">
                    <select class="form-control input-sm" name="lokasi_adjustment" id="lokasi_adjustment" disabled/>
                    <option value=""></option>
                    <?php foreach ($warehouse as $row) { 
                      if($row->nama == $adjustment->lokasi_adjustment){ ?>
                        <option value='<?php echo $row->kode; ?>' selected><?php echo $row->nama;?></option>
                      <?php }else{ ?>
                      <option value='<?php echo $row->kode; ?>'><?php echo $row->nama;?></option>
                    <?php }}?>
                    </select>
                  </div>                                    
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Kode Lokasi </label></div>
                  <div class="col-xs-8">
                    <input type="text" class="form-control input-sm" name="kode_lokasi" id="kode_lokasi"  readonly="readonly" value="<?php echo $adjustment->kode_lokasi?>"/>
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
                  <li class="active"><a href="#tab_1" data-toggle="tab">Adjustment Detail</a></li>
                </ul>
                <div class="tab-content over"><br>
                  <div class="tab-pane active" id="tab_1">

                    <!-- Tabel  -->
                    <div class="col-md-12 table-responsive over">
                      <table class="table table-condesed table-hover rlstable  over" width="100%" id="tableadjustment" >
                        <thead>                          
                          <tr>
                            <th class="style no">No.</th>
                            <th class="style">Product</th>                            
                            <th class="style">Lot</th>
                            <th class="style">UoM</th>
                            <th class="style">Qty Stock</th>
                            <th class="style">Qty Adjustment</th>
                            <th class="style">UoM 2</th>
                            <th class="style">Qty Stock 2</th>
                            <th class="style">Qty Adjustment 2</th>
                            <th class="style">Move ID</th>
                            <th class="style">Qty Move</th>
                            <th class="style"></th>                            
                          </tr>
                        </thead>
                        <tbody>
                          <?php
                            $no = 1;
                           
                            foreach ($details as $row) {
                          ?>
                            <tr >
                              
                              <td data-content="edit" data-id="row_order" data-isi="<?php echo $row->row_order; ?>" data-isi2="<?php echo $row->quant_id?>" ><?php echo $no++.".";?></td>
                              <td class="text-wrap width-400" data-content="edit" data-id="kode_produk" data-isi="<?php echo htmlentities($row->kode_produk) ?>" data-id2="prodhidd" data-isi2="<?php echo htmlentities($row->nama_produk) ?>"><?php echo '['.$row->kode_produk.'] '.$row->nama_produk;?></a></td>

                              <td class="text-wrap width-300" data-content="edit" data-name="Lot" data-id="lot" data-isi="<?php echo htmlentities($row->lot) ?>" ><?php echo $row->lot?></a></td>

                              <td class="text-wrap width-100" data-content="edit" data-name="Uom" data-id="uom" data-isi="<?php echo $row->uom ?>" ><?php echo $row->uom?></a></td>

                              <td class="text-wrap width-200" ><?php echo $row->qty_data?></a></td>

                              <td class="text-wrap width-200" data-content="edit" data-name="Qty Adjustment" data-id="qtyadjustment" data-isi="<?php echo $row->qty_adjustment ?>" ><?php echo $row->qty_adjustment?></a></td>

                              <td class="text-wrap width-100" data-content="edit" data-name="Uom2" data-id="uom2" data-isi="<?php echo $row->uom2 ?>" ><?php echo $row->uom2?></a></td>

                              <td class="text-wrap width-200" ><?php echo $row->qty_data2?></a></td>

                              <td class="text-wrap width-200" data-content="edit" data-name="Qty Adjustment2"  data-id="qtyadjustment2" data-isi="<?php echo $row->qty_adjustment2 ?>" ><?php echo $row->qty_adjustment2?></a></td>

                              <td class="text-wrap width-200" ><?php echo $row->move_id?></a></td>
                              <td class="text-wrap width-200" ><?php echo $row->qty_move?></a></td>
                              
                              <td class="width-200" align="center">
                                <?php if($adjustment->status == 'draft'){?>
                                <a href="javascript:void(0)" class="add" title="Simpan" data-toggle="tooltip" ><i class="fa fa-save"></i></a>               
                                <a href="javascript:void(0)" class="edit" title="Edit" data-toggle="tooltip" style="color: #FFC107; margin-right: 24px;"><i class="fa fa-edit"></i></a>
                                <a href="javascript:void(0)" class="delete" title="Hapus" data-toggle="tooltip" ><i class="fa fa-trash" style="color: red"></i></a>
                                <a href="javascript:void(0)" class="cancel" title="Cancel" data-toggle="tooltip" style="margin-left: 20px;"><i class="fa fa-close"></i></a>
                                <?php }?>
                              </td>

                            </tr>
                          <?php 
                            }
                          ?>
                        </tbody>
                        <tfoot>
                          <tr>
                            <td colspan="8">
                              <?php if($adjustment->status == 'draft'){?>
                              <a href="javascript:void(0)" class="add-new"><i class="fa fa-plus"></i> Tambah Data</a>
                            <?php } ?>
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
   </div>
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

  //pilih lokasi/departemen kode lokasi otomatis
  $("#lokasi_adjustment").change(function(){
      $.ajax({
            dataType: "JSON",
            url : '<?php echo site_url('warehouse/adjustment/get_stock_location_by_departemen') ?>',
            type: "POST",
            data: {kode_departemen   : $('#lokasi_adjustment').val()},
            success: function(data){
              $('#kode_lokasi').val(data.stock_location);
            },
            error: function (xhr, ajaxOptions, thrownError){
              alert('Error data');
              alert(xhr.responseText);
            }
      });
  });

  // Tambah data
  // Append table with add row form on add new button click
  $(document).on("click", ".add-new", function(){
    $(".add-new").hide();
    var index = $("#tableadjustment tbody tr:last-child").index();
    var row   ='<tr class="">'
          + '<td></td>'
          + '<td style="min-width:170px;"><select type="text" class="form-control input-sm prod" name="Product" id="product"></select></select><input type="hidden" class="form-control input-sm prodhidd" name="prodhidd" id="prodhidd"></td>'          
          + '<td style="min-width:100px;"><input type="text" class="form-control input-sm lot" name="Lot" id="lot"></td>'
          + '<td class="width-150"><input type="text" class="form-control input-sm uom" name="Uom" id="uom" readonly></td>'          
          + '<td></td>'          
          + '<td class="width-200"><input type="text" class="form-control input-sm qtyadjustment" name="Qty Adjustment" id="qtyadjustment"  onkeyup="validAngka(this)" ></td>'
          + '<td style="min-width:80px;"><select type="text" class="form-control input-sm uom2" name="Uom2" id="uom2"></select></select></td>'          
          + '<td class="width-200"></td>'          
          + '<td class="width-200"><input type="text" class="form-control input-sm qtyadjustment2" name="Qty Adjustment2" id="qtyadjustment2"  onkeyup="validAngka(this)" ></td>'
          + '<td></td>'
          + '<td></td>'
          + '<td align="center"><button type="button" class="btn btn-primary btn-xs add width-btn" title="Simpan" data-toggle="tooltip">Simpan</button><button type="button" class="btn btn-danger btn-xs batal width-btn" title="Batal" data-toggle="tooltip">Batal</button></td>'
          + '</tr>';

        $('#tableadjustment tbody').append(row);
        $("#tableadjustment tbody tr").eq(index + 1).find(".add, .edit").toggle();
        $('[data-toggle="tooltip"]').tooltip();

        //select 2 product mst_produk
        $('.prod').select2({
          allowClear: true,
          placeholder: "",
          ajax:{
                dataType: 'JSON',
                type : "POST",
                url : "<?php echo base_url();?>warehouse/adjustment/get_produk_adjustment_select2",
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

        $(".prod").change(function(){
            $.ajax({
                  dataType: "JSON",
                  url : '<?php echo site_url('warehouse/adjustment/get_produk_by_id') ?>',
                  type: "POST",
                  data: {kode_produk: $(this).parents("tr").find("#product").val(),
                         kode_lokasi: $('#kode_lokasi').val(),                         
                  },
                  success: function(data){
                    $('.prodhidd').val(data.nama_produk);                                        
                    //$('.qtystock').val(data.qty_data);
                    //$('.qtyadjustment').val(data.qty_data);
                    $('.uom').val(data.uom);
                    $('.uom2').val(data.uom2);
                    $newOption = $("<option></option>").val(data.uom2).text(data.uom2);
                    $('.uom2').empty().append($newOption).trigger('change');
                  },
                  error: function (xhr, ajaxOptions, thrownError){
                    alert('Error data');
                    alert(xhr.responseText);
                  }
            });
        });

        //select 2 uom 2 di table
        $('.uom2').select2({
          allowClear: true,
          placeholder: "",
          ajax:{
                dataType: 'JSON',
                type : "POST",
                url : "<?php echo base_url();?>warehouse/adjustment/get_uom_select2",
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
                  //alert(xhr.responseText);
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

  //Tampilkan semua barang di lokasi (modal)
  $('#btn-import-produk').click(function(){
    $('#btn-import-produk').button('loading');
    var status = "<?php echo $adjustment->status;?>";
      if(status == 'done'){
        alert_modal_warning('Status Adjustment sudah Done ! ');
      }else if(status == 'cancel'){
        alert_modal_warning('Status Adjustment sudah Batal ! ');
      }else{
        var kode_lokasi = $('#kode_lokasi').val();
        var kode_adjustment = $('#kode_adjustment').val();
        $('#btn-import-produk').button('reset');
        $("#tambah_data").modal({
            show: true,
            backdrop: 'static'
        })
        $(".tambah_data").html('<center><h5><img src="<?php echo base_url('dist/img/ajax-loader.gif') ?> "/><br>Please Wait...</h5></center>');
        $('.modal-title').text('Pilih Produk Untuk Adjustment');
          $.post('<?php echo site_url()?>warehouse/adjustment/import_produk',
            {kode_lokasi : kode_lokasi,
             kode_adjustment : kode_adjustment},
            function(html){
              setTimeout(function() {$(".tambah_data").html(html); });
            }   
         );
      }
  });

  //simpan / edit row data ke database
  $(document).on("click", ".add", function(e){

    e.preventDefault();

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
          alert_notify('fa fa-warning',' Uom  Harus Diisi !','danger');
          empty2 = true;
        }
        
    });


    // validasi untuk inputan textbox
    input.each(function(){
      if(!$(this).val()){
        alert_notify('fa fa-warning',$(this).attr('name')+ ' Harus Diisi !','danger',function(){});
        empty = true;
      }
    });
    
    if(!empty){
      var kode_adjustment   = "<?php echo $adjustment->kode_adjustment ?>";
      var kode_produk       = $(this).parents("tr").find("#product").val();
      var nama_produk       = $(this).parents("tr").find("#prodhidd").val();
      var lot               = $(this).parents("tr").find("#lot").val();
      var uom               = $(this).parents("tr").find("#uom").val();
      var qty_data          = $(this).parents("tr").find("#qtystock").val();
      var qty_adjustment    = $(this).parents("tr").find("#qtyadjustment").val();
      var uom2              = $(this).parents("tr").find("#uom2").val();
      var qty_data2         = $(this).parents("tr").find("#qtystock2").val();
      var qty_adjustment2   = $(this).parents("tr").find("#qtyadjustment2").val();
      var row_order         = $(this).parents("tr").find("#row_order").val();
      var quant_id          = $(this).parents("tr").find("#quant_id").val();

      $.ajax({
        dataType: "JSON",
        url : '<?php echo site_url('warehouse/adjustment/simpan_detail_adjustment_items') ?>',
        type: "POST",
        data: {kode_adjustment  : kode_adjustment,
              kode_produk       : kode_produk,
              nama_produk       : nama_produk,
              lot               : lot,
              uom               : uom,
              qty_data          : qty_data,
              qty_adjustment    : qty_adjustment,
              uom2              : uom2,
              qty_data2         : qty_data2,
              qty_adjustment2   : qty_adjustment2,
              quant_id          : quant_id,
              row_order         : row_order},
        success: function(data){
          if(data.sesi=='habis'){
              //alert jika session habis
              alert_modal_warning(data.message);
              window.location.replace('../index');
          }else if(data.status == 'failed'){
              alert_modal_warning(data.message);
              refresh_adjustment();
          }else{
              refresh_adjustment();
              $(".add-new").show();                   
              alert_notify(data.icon,data.message,data.type,function(){});
           }
        },
        error: function (xhr, ajaxOptions, thrownError){
          alert('Error data');
          alert(xhr.responseText);
        }
      }); 
    }

  });


  //klik button simpan
  $('#btn-simpan').click(function(e){

    e.preventDefault();

    var status_head = "<?php echo $adjustment->status?>";

    if(status_head == 'cancel'){
      alert_modal_warning('Maaf, Data Tidak Bisa Disimpan, Status Adjustment Sudah Batal !');
    }else if(status_head == 'done'){
      alert_modal_warning('Maaf, Data Tidak Bisa Disimpan, Status Adjustment Sudah Done !');
    }else{

      $('#btn-simpan').button('loading');
      please_wait(function(){});
      $.ajax({
         type: "POST",
         dataType: "json",
         url :'<?php echo base_url('warehouse/adjustment/simpan')?>',
         beforeSend: function(e) {
            if(e && e.overrideMimeType) {
                e.overrideMimeType("application/json;charset=UTF-8");
            }
         },
         data: {kode_adjustment   : $('#kode_adjustment').val(),
                create_date       : $('#tgl_buat').val(),
                lokasi_adjustment : $('#lokasi_adjustment').val(),
                kode_lokasi       : $('#kode_lokasi').val(),
                note              : $('#note').val(),
                status            : 'draft',

          },success: function(data){
            if(data.sesi == "habis"){
              //alert jika session habis
              alert_modal_warning(data.message);
              window.location.replace('index');
            }else if(data.status == "failed"){
                //jika ada form belum keiisi
                $('#btn-simpan').button('reset');
                unblockUI( function() {
                  setTimeout(function() { alert_notify(data.icon,data.message,data.type,function(){}); }, 1000);
                });
                document.getElementById(data.field).focus();
                refresh_adjustment();

            }else{
              //jika berhasil disimpan/diubah
              unblockUI( function() {
                  setTimeout(function() { alert_notify(data.icon,data.message,data.type,function(){}); }, 1000);
                });
              $('#btn-simpan').button('reset');
              refresh_adjustment();
            }

          },error: function (xhr, ajaxOptions, thrownError) {
            //alert(xhr.responseText);
            alert('Error Simpan Data');
            unblockUI( function(){});
            $('#btn-simpan').button('reset');

          }
      });
    }
  });


  // Edit row on edit button click
  $(document).on("click", ".edit", function(){  
    var quant = 'FALSE';
      $(this).parents("tr").find("td[data-content='edit']").each(function(){


        if($(this).attr('data-isi2') != 0 && $(this).attr('data-id')=="row_order"){
          quant = 'TRUE';
        }

        if($(this).attr('data-id')=="row_order"){
            $(this).html('<input type="hidden"  class="form-control" value="' + ($(this).attr('data-isi')) + '" id="'+ $(this).attr('data-id') +'"> <input type="hidden"  class="form-control quant_id" value="' + ($(this).attr('data-isi2')) + '" id="quant_id">');    

            row_order = $(this).attr('data-isi');
                    
        }else if($(this).attr('data-id')=='kode_produk' && quant == 'FALSE'){

            var kode_produk = ($(this).attr('data-isi'));
            var nama_produk = ($(this).attr('data-isi2'));

            class_sel2_prod = 't_sel2_prod'+row_order;
            class_nama_produk = 'e_nama_produk'+row_order;
            $(this).html('<select type="text"  class="form-control input-sm '+class_sel2_prod+'" id="product" name="Product" style="min-width:150px !important;"></select> ' + '<input type="hidden"  class="form-control '+class_nama_produk+' " value="' + $(this).attr('data-isi2') + '" id="'+ $(this).attr('data-id2') +'"> ');

            custom_nama = '['+kode_produk+'] '+nama_produk;
            $newOption = new Option(custom_nama, kode_produk, true, true);
            $('.t_sel2_prod'+row_order).append($newOption).trigger('change');
            //select 2 product
            $('.t_sel2_prod'+row_order).select2({
              allowClear: true,
              placeholder: "",
              ajax:{
                    dataType: 'JSON',
                    type : "POST",
                    url : "<?php echo base_url();?>warehouse/adjustment/get_produk_adjustment_select2",
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

            $('.t_sel2_prod'+row_order).change(function(){
                $.ajax({
                      dataType: "JSON",
                      url : '<?php echo site_url('warehouse/adjustment/get_produk_by_id') ?>',
                      type: "POST",
                      data: {kode_produk: $(".t_sel2_prod"+row_order).val() },
                      success: function(data){
                        $('.e_nama_produk'+row_order).val(data.nama_produk);
                        //$('.e_').val('1');
                        //$('.uom').val(data.uom);
                        var $newOptionuom = $("<option></option>").val(data.uom).text(data.uom);
                        $(".e_uom"+row_order).empty().append($newOptionuom).trigger('change');

                        var $newOptionuom2 = $("<option></option>").val(data.uom2).text(data.uom2);
                        $(".e_uom2"+row_order).empty().append($newOptionuom2).trigger('change');
                      },
                      error: function (xhr, ajaxOptions, thrownError){
                        alert('Error data');
                        alert(xhr.responseText);
                      }
                });
            });

        }else if($(this).attr('data-id')=='lot' && quant == 'FALSE'){
          $(this).html('<input type="text"  class="form-control input-sm " value="'+ ($(this).attr('data-isi')) +'" id="'+ $(this).attr('data-id') +'" name="'+ $(this).attr('data-id') +'"  style="min-width:100px !important;"> ');
        }else if($(this).attr('data-id')=='uom' && quant == 'FALSE'){

          class_uom = 'e_uom'+row_order;

          $(this).html('<select type="text"  class="form-control input-sm '+class_uom+'" id="'+ $(this).attr('data-id') +'" name="Uom" style="min-width:60px !important;"> ></select> ');

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
                  //alert('Error data');
                  //alert(xhr.responseText);
                }
          }
          }); 

        }else if($(this).attr('data-id')=='qtyadjustment'){
          $(this).html('<input type="text"  class="form-control input-sm" value="'+ ($(this).attr('data-isi')) +'" id="'+ $(this).attr('data-id') +'" name="'+ $(this).attr('data-id') +'" onkeyup="validAngka(this)"> ');
        }else if($(this).attr('data-id')=='uom2' && quant == 'FALSE'){
          class_uom = 'e_uom2'+row_order;

          $(this).html('<select type="text"  class="form-control input-sm '+class_uom+'" id="'+ $(this).attr('data-id') +'" name="Uom2" style="min-width:60px !important;"> ></select> ');

          var $newOptionuom = $("<option></option>").val($(this).attr('data-isi') ).text($(this).attr('data-isi') );
          $(".e_uom2"+row_order).empty().append($newOptionuom).trigger('change');

          $('.e_uom2'+row_order).select2({
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
                  //alert(xhr.responseText);
                }
          }
          }); 

        }else if($(this).attr('data-id')=='qtyadjustment2'&& quant == 'FALSE' ){
          $(this).html('<input type="text"  class="form-control input-sm" value="'+ ($(this).attr('data-isi')) +'" id="'+ $(this).attr('data-id') +'" name="'+ $(this).attr('data-id') +'" onkeyup="validAngka(this)" > ');        
        }
      });  
      $(this).parents("tr").find(".add, .edit").toggle();
      $(this).parents("tr").find(".cancel, .delete").toggle();
      $(".add-new").hide();      
  });


  //delete row di database
  $(document).on("click", ".delete", function(){ 
    $(this).parents("tr").find("td[data-content='edit']").each(function(){
      if($(this).attr('data-id')=="row_order"){
        $(this).html('<input type="hidden" class="form-control" value="' + ($(this).attr('data-isi')) + '" id="'+ $(this).attr('data-id') +'"> ');
      }
    });

    var kode_adjustment   =  "<?php echo $adjustment->kode_adjustment; ?>";
    var row_order         = $(this).parents("tr").find("#row_order").val();  

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
                    url : '<?php echo site_url('warehouse/adjustment/hapus_adjustment_items') ?>',
                    type: "POST",
                    data: {kode_adjustment : kode_adjustment, 
                          row_order : row_order  },
                    success: function(data){
                      if(data.sesi=='habis'){
                          //alert jika session habis
                          alert_modal_warning(data.message);
                          window.location.replace('../index');
                      }else if(data.status == 'failed'){
                          alert_modal_warning(data.message);
                          refresh_adjustment();
                      }else{
                          refresh_adjustment();
                          $(".add-new").show();                   
                          alert_notify(data.icon,data.message,data.type,function(){});
                       }
                    },
                    error: function (xhr, ajaxOptions, thrownError){
                      alert('Error data');
                      alert(xhr.responseText);
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
  });


  // Generate button click
  $(document).on("click", "#btn-generate", function(e){

    e.preventDefault();

    var status_head = "<?php echo $adjustment->status?>";

    if(status_head == 'cancel'){
      alert_modal_warning('Maaf, Tidak Bisa Generate, Status Adjustment Sudah Batal !');
    }else if(status_head == 'done'){
      alert_modal_warning('Maaf, Tidak Bisa Generate, Status Adjustment Sudah Done !');
    }else{
    
      var kode_adjustment   =  "<?php echo $adjustment->kode_adjustment; ?>";
      //$('#lokasi_adjustment').attr('disabled', false).attr('id', 'lokasi_adjustment');
      bootbox.dialog({
      message: "Apakah Anda ingin Generate Data ?",
      title: "<i class='fa fa-gear'></i> Generate Data !",
      buttons: {
        danger: {
            label    : "Yes ",
            className: "btn-primary btn-sm",
            callback : function() {
              please_wait(function(){});
              $.ajax({
                dataType: "JSON",
                url : '<?php echo site_url('warehouse/adjustment/generate_detail_adjustment_items') ?>',
                type: "POST",
                data: {kode_adjustment:kode_adjustment, kode_lokasi:$('#kode_lokasi').val(), lokasi_adj:$('#lokasi_adjustment').val()},
                success: function(data){
                  if(data.sesi=='habis'){
                      //alert jika session habis
                      alert_modal_warning(data.message);
                      window.location.replace('../index');
                  }else if(data.status == 'failed'){
                      unblockUI( function(){});
                      alert_modal_warning(data.message);
                      refresh_adjustment();
                  }else{
                      refresh_adjustment();
                      unblockUI( function() {
                      setTimeout(function() { alert_notify(data.icon,data.message,data.type,function(){}); }, 1000);
                      });
                      $("#btn-header").load(location.href + " #btn-header>*");
                   }
                },
                error: function (xhr, ajaxOptions, thrownError){
                  alert('Error Generate data');
                  //alert(xhr.responseText);
                  refresh_adjustment();
                  unblockUI( function(){});
                }
              });
            }
        },
        success: {
              label    : "No",
              className: "btn-default  btn-sm",
              callback : function() {
                $('.bootbox').modal('hide');
                refresh_adjustment();
              }
        }
      }
      });
    }
  });


  // Generate button click
  $(document).on("click", "#btn-cancel", function(e){

    e.preventDefault();

    var status_head = "<?php echo $adjustment->status?>";

    if(status_head == 'cancel'){
      alert_modal_warning('Maaf, Data Tidak Bisa dibatalkan, Status Adjustment Sudah Batal !');
    }else if(status_head == 'done'){
      alert_modal_warning('Maaf, Data Tidak Bisa dibatalkan, Status Adjustment Sudah Done !');
    }else{
    
      var kode_adjustment   =  "<?php echo $adjustment->kode_adjustment; ?>";
      //$('#lokasi_adjustment').attr('disabled', false).attr('id', 'lokasi_adjustment');
      bootbox.dialog({
      message: "Apakah Anda ingin membatalkan Data Adjustment ?",
      title: "<i class='fa fa-warning'></i> Batal Adjustment !",
      buttons: {
        danger: {
            label    : "Yes ",
            className: "btn-primary btn-sm",
            callback : function() {
              please_wait(function(){});
              $.ajax({
                dataType: "JSON",
                url : '<?php echo site_url('warehouse/adjustment/batal_adjustment') ?>',
                type: "POST",
                data: {kode_adjustment:kode_adjustment},
                success: function(data){
                  if(data.sesi=='habis'){
                      //alert jika session habis
                      alert_modal_warning(data.message);
                      window.location.replace('../index');
                  }else if(data.status == 'failed'){
                      unblockUI( function(){});
                      alert_modal_warning(data.message);
                      refresh_adjustment();
                  }else{
                      refresh_adjustment();
                      unblockUI( function() {
                      setTimeout(function() { alert_notify(data.icon,data.message,data.type,function(){}); }, 1000);
                      });
                      $("#btn-header").load(location.href + " #btn-header>*");
                   }
                },
                error: function (xhr, ajaxOptions, thrownError){
                  //alert('Error Generate data');
                  alert(xhr.responseText);
                  refresh_adjustment();
                  unblockUI( function(){});
                }
              });
            }
        },
        success: {
              label    : "No",
              className: "btn-default  btn-sm",
              callback : function() {
                $('.bootbox').modal('hide');
                refresh_adjustment();
              }
        }
      }
      });
    }
  });


  //btn cancel edit
  $(document).on("click", ".cancel", function(){
    $("#tab_1").load(location.href + " #tab_1");
    $(".add-new").show();
  });

  //untuk merrefresh procurement order
  function refresh_adjustment(){
      $("#tab_1").load(location.href + " #tab_1>*"); 
      $("#foot").load(location.href + " #foot");
      $("#status_bar").load(location.href + " #status_bar>*");
      
  }

  
  //html entities javascript
  function htmlentities_script(str) {
    return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
  }

  //validasi input angka
  function validAngka(a){
    if(!/^[0-9.]+$/.test(a.value)){
      a.value = a.value.substring(0,a.value.length-1000);
    }    
  }

</script>


</body>
</html>
