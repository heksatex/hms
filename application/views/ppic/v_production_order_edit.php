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

    .td-reffNotes-items {
      display: table;
      table-layout: fixed;
      width: 200px; /* responsiveness */
      height: auto;
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

<body class="hold-transition skin-black fixed sidebar-mini" id="block-page">
<!-- Site wrapper -->
<div class="wrapper" >

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
  <div class="content-wrapper" >
    <!-- Content Header (Status - Bar) -->
    <section class="content-header"  >
      <div id ="status_bar">
       <?php 
         $data['jen_status'] =  $productionorder->status;
         $this->load->view("admin/_partials/statusbar.php", $data); 
       ?>
      </div>
    </section>

    <!-- Main content -->
    <section class="content">

      <!--  box content -->
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title"><b><?php echo $productionorder->kode_prod;?></b></h3>
        </div>
        <div class="box-body">

            <!-- form header -->
            <form class="form-horizontal" id="form_header" name="Production Order">
         
            <div class="form-group">                  
              <div class="col-md-12" >
                <div id="alert"></div>
              </div>
            </div>

            <!-- form-group -->
            <div class="form-group">

              <div class="col-md-6">
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Production Order </label></div>
                  <div class="col-xs-8">
                    <input type="text" class="form-control input-sm" name="kode_prod" id="kode_prod"  readonly="readonly" value="<?php echo $productionorder->kode_prod?>" />
                  </div>                                    
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Create Date </label></div>
                  <div class="col-xs-8 col-md-8">
                     <input type='text' class="form-control input-sm" name="tgl_buat" id="tgl_buat" readonly="readonly" value="<?php echo $productionorder->create_date?>"  />
                  </div>                                    
                </div>

                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Notes </label></div>
                  <div class="col-xs-8">
                    <textarea type="text" class="form-control input-sm" name="note" id="note"><?php echo $productionorder->notes?></textarea>
                  </div>                                    
                </div>
              </div>
              <!-- /.col-md-6 -->

              <div class="col-md-6">
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Schedule Date </label></div>
                  <div class="col-xs-8 col-md-8">
                    <input type='text' class="form-control input-sm" name="tgl" id="tgl" readonly="readonly" value="<?php echo $productionorder->schedule_date?>" />
                  </div>                                    
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Sales Order</label></div>
                  <div class="col-xs-8">
                    <input type="text" class="form-control input-sm" name="sales_order" id="sales_order" readonly="readonly"  value="<?php echo $productionorder->sales_order?>" readonly="readonly"/>
                  </div>                                    
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Departement Tujuan</label></div>
                  <div class="col-xs-8">
                    <select class="form-control input-sm" name="warehouse" id="warehouse" />
                    <option value="">Pilih Warehouse</option>
                      <?php 
                        foreach ($warehouse as $row) {
                          if($row->kode == $productionorder->warehouse){
                           echo "<option value=\"$row->kode\" selected>$row->nama</option>";
                     
                          }else{
                           echo "<option value=\"$row->kode\">$row->nama</option>";

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
                      $val = array('Not Urgent','Normal','Urgent','Very Urgent');
                      $i=0;
                      while($i<=3) {
                        if($val[$i] == $productionorder->priority){
                          echo "<option selected>$val[$i]</option>";
                        }else{
                          echo "<option>$val[$i]</option>";
                        }
                      $i++;
                      }
                    ?>
                    </select>
                       
                  </div>                                    
                </div>
              </div>
              <!-- /.col-md-6 -->

            </div>
            <!-- ./.form-group -->
            </form>
            <!-- /.form header -->


            <div class="row">
              <div class="col-md-12">
                <!-- Custom Tabs -->
                <div class="">
                  <ul class="nav nav-tabs " >
                    <li class="active"><a href="#tab_1" data-toggle="tab">Procurements</a></li>
                  </ul>
                  <div class="tab-content over"><br>
                    <div class="tab-pane active" id="tab_1">

                      <!-- Tabel  -->
                      <div class="col-md-12 table-responsive over">
                        <table class="table table-condesed table-hover rlstable  over" width="100%" id="procurements" >
                          <thead>                          
                            <tr>
                              <th class="style no">No.</th>
                              <th class="style">Contract Line</th>
                              <th class="style">BOM Knitting</th>
                              <th class="style">Schedule Date</th>
                              <th class="style" style="text-align: right;" >Qty</th>
                              <th class="style">Uom</th>
                              <th class="style">Reff Notes PPIC</th>
                              <th class="style">Status</th>
                              <th class="style"></th>
                              <th class="style"></th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php
                              $no = 1;
                              $color = '';
                              foreach ($details as $row) {
                               if($row->status == 'cancel') $color = 'red'; else $color = '';
                            ?>
                              <tr style="color:<?php echo $color;?>">
                                <td data-content="edit" data-id="row_order" data-isi="<?php echo $row->row_order."^|".$row->kode_produk."^|".htmlentities($row->nama_produk)."^|".$row->qty."^|".$row->uom."^|".htmlentities($row->reff_notes)."^|".$row->schedule_date."^|".$productionorder->sales_order."^|".$productionorder->warehouse."^|".$row->kode_bom;?>" data-isi2="<?php echo $row->row_order; ?>"><?php echo $no++.".";?></td>
                                <td class="text-wrap width-200" ><?php echo $row->nama_produk?></a></td>
                                <td class="text-wrap width-200" data-content="edit" data-id="bom" data-isi="<?php echo $row->kode_bom?>" data-isi2="<?php echo htmlentities($row->nama_bom)?>" data-isi3="<?php echo htmlentities($row->kode_produk);?>"><?php echo $row->nama_bom?></a></td>
                                <td class="text-wrap width-220" data-content="edit" data-id="schedule_date" data-isi="<?php echo $row->schedule_date;?>"><?php echo $row->schedule_date?></td>
                                <td class="width-100" data-content="edit" data-id="qty" data-isi="<?php echo $row->qty;?>" align="right"><?php echo number_format($row->qty,2)?></td>
                                <td class="width-100" ><?php echo $row->uom?></td>
                                <td class="td-reffNotes-items"  data-content="edit" data-id="reff" data-isi="<?php echo htmlentities($row->reff_notes);?>"> <?php echo $row->reff_notes?></td>
                                <td><?php if($row->status == 'cancel') echo 'Batal';  else echo $row->status;?></td>
                                <td class="width-200"  style="text-align: center;" >
                                  <?php if($row->status == 'draft'){?>
                                  <a href="javascript:void(0)" class="add" title="Simpan" data-toggle="tooltip" ><i class="fa fa-save"></i></a>
                                  <a href="javascript:void(0)" class="edit" title="Edit" data-toggle="tooltip" style="color: #FFC107;   margin-right: 24px;"><i class="fa fa-edit"></i></a>
                                  <a href="javascript:void(0)"  class="delete" title="Hapus" data-toggle="tooltip"><i class="fa fa-trash" style="color: red"></i></a>
                                  <a href="javascript:void(0)" class="cancel" title="Cancel" data-toggle="tooltip" style="margin-left: 20px;"><i class="fa fa-close"></i></a>
                                <?php }
                                    if($row->status == 'generated' OR $row->status == 'cancel'){?>
                                       <a href="javascript:void(0)" data-toggle="tooltip" title="Details" onclick="view_detail('<?php echo $productionorder->kode_prod; ?>','<?php echo $productionorder->sales_order; ?>','<?php echo $row->kode_produk?>','<?php echo htmlentities($row->nama_produk)?>','<?php echo $row->row_order?>','<?php echo $row->kode_bom?>')"><span class="glyphicon  glyphicon-share"></span></a>
                                <?php }
                                ?>
                                </td>
                                <td class="width-80">
                                  <?php if($row->status == 'draft'){?>
                                    <button type="button" class="btn btn-primary btn-xs btn-generate" title="Generate" data-toggle="tooltip" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing...">Generate</button>
                                  <?php }else if($row->status == 'generated'){?>
                                     <button type="button" class="btn btn-danger btn-xs btn-cancel-items width-btn" title="Batal Items" data-toggle="tooltip" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing...">Batal</button>
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
    </div>
  </footer>

</div>

<?php $this->load->view("admin/_partials/js.php") ?>

<script type="text/javascript">

  //validasi inputan harus angka
  function validAngka(a){
    if(!/^[0-9.]+$/.test(a.value)){
      a.value = a.value.substring(0,a.value.length-1000);
      alert_notify('fa fa-warning','Maaf, Inputan Qty Hanya Berupa Angka !','danger');
    }
  }

  //html entities javascript
  function htmlentities_script(str) {
    return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
  }

  //auto height in textarea
  function textAreaAdjust(o) {
    o.style.height = "1px";
    o.style.height = (25+o.scrollHeight)+"px";
  }

  //untuk merrefresh production order
  function refresh_production(){
    //$("#form_header").load(location.href + " #form_header");
    $("#tab_1").load(location.href + " #tab_1");
    $("#foot").load(location.href + " #foot");
    $("#status_bar").load(location.href + " #status_bar");
  }


  //untuk reload page setelah modal ditutup
  $(".modal").on('hidden.bs.modal', function(){
    refresh_production();
  });


  //modal view move items
  function view_detail(kode,sales_order,kode_produk,nama_produk,row_order,kode_bom){
      $("#view_data").modal({
          show: true,
          backdrop: 'static'
      })
      $(".view_body").html('<center><h5><img src="<?php echo base_url('dist/img/ajax-loader.gif') ?> "/><br>Please Wait...</h5></center>');
      $('.modal-title').text('Detail Items');
        $.post('<?php echo site_url()?>ppic/productionorder/view_detail_items',
          {kode:kode, sales_order:sales_order, kode_produk:kode_produk, nama_produk:nama_produk, nama_produk:nama_produk, row_order:row_order, kode_bom:kode_bom},
          function(html){
            setTimeout(function() {$(".view_body").html(html);});
          }   
       );
  }

  // Append table with add row form on add new button click
  $(document).on("click", ".add-new", function(){

    $(".add-new").hide();
    var index = $("#procurements tbody tr:last-child").index();
    var row   ='<tr class="">'
          + '<td></td>'
          + '<td class="text-wrap width-200"><select type="text" class="form-control input-sm prod" name="Product" id="product"></select><input type="hidden" class="form-control input-sm prodhidd" name="prodhidd" id="prodhidd"></td>'
          + '<td class="text-wrap width-200"><select type="text" class="form-control input-sm bom" name="BOM" id="bom"></select></select></td>'
          + '<td class="text-wrap width-300"><div class="input-group date" id="sch_date" ><input type="text" class="form-control input-sm" name="schedule_date" id="schedule_date" readonly="readonly"  /><span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span></div></td>'
          + '<td class="width-100"><input type="text" class="form-control input-sm qty" name="Qty" id="qty"  onkeyup="validAngka(this)" ></td>'
          + '<td class="width-100"><input type="text" class="form-control input-sm uom" name="Uom" id="uom" readonly></td>'
          + '<td class="text-wrap width-200"><textarea type="text" class="form-control input-sm" name="reff" id="reff"></textarea></td>'
          + '<td></td>'
          + '<td align="center"><button type="button" class="btn btn-primary btn-xs add width-btn" title="Simpan" data-toggle="tooltip">Simpan</button><a class="edit" title="Edit" data-toggle="tooltip"><i class="fa fa-edit"></i></a><button type="button" class="btn btn-danger btn-xs batal width-btn" title="Batal" data-toggle="tooltip">Batal</button></td>'
          + '<td></td>'
          + '</tr>';

        $('#procurements tbody').append(row);
        $("#procurements tbody tr").eq(index + 1).find(".add, .edit").toggle();
        $('[data-toggle="tooltip"]').tooltip();

        //set schedule date
        var datetomorrow=new Date();
        datetomorrow.setDate(datetomorrow.getDate() + 1);  
        $('#sch_date').datetimepicker({
          minDate : datetomorrow,
          format : 'YYYY-MM-DD HH:mm:ss',
          ignoreReadonly: true,
        });

        //select 2 product
        $('.prod').select2({
          allowClear: true,
          placeholder: "",
          ajax:{
                dataType: 'JSON',
                type : "POST",
                url : "<?php echo base_url();?>ppic/productionorder/get_produk_select2_so",
                //delay : 250,
                data : function(params){
                  return{
                    prod:params.term,
                    sales_order: $("#sales_order").val(),
                  };
                }, 
                processResults:function(data){
                  var results = [];
                  $.each(data, function(index,item){
                      results.push({
                          id:item.kode_produk,
                          text:item.nama_produk
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
                  url : '<?php echo site_url('ppic/productionorder/get_prod_by_id_so') ?>',
                  type: "POST",
                  data: {kode_produk: $(this).parents("tr").find("#product").val(), sales_order : $("#sales_order").val() },
                  success: function(data){
                    $('.prodhidd').val(data.nama_produk);
                    $('.qty').val(data.qty);
                    $('.uom').val(data.uom);

                    //untuk event selected select2 uom
                    var $newOption = $("<option></option>").val(data.kode_bom).text(data.nama_bom);
                    $(".bom").empty().append($newOption).trigger('change');
                  },
                  error: function (xhr, ajaxOptions, thrownError){
                    //alert('Error data');
                    //alert(xhr.responseText);
                  }
            });
        });

        //select 2 BOM
        $('.bom').select2({
          allowClear: true,
          placeholder: "",
          ajax:{
                dataType: 'JSON',
                type : "POST",
                url : "<?php echo base_url();?>ppic/productionorder/get_bom_select2_by_produk",
                //delay : 250,
                data : function(params){
                  return{
                    bom:params.term,
                    kode_produk: $(this).parents("tr").find("#product").val(),
                  };
                }, 
                processResults:function(data){
                  var results = [];

                  $.each(data, function(index,item){
                      results.push({
                          id:item.kode_bom,
                          text:item.nama_bom
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


  //simpan / edit row data ke database
  $(document).on("click", ".add", function(){
      var empty = false;
      var input = $(this).parents("tr").find('input[type="text"]');

      var empty2 = false;
      var select = $(this).parents("tr").find('select[type="text"]');

      //validasi tidak boleh kosong hanya select product saja
      select.each(function(){
        if(!$(this).val() && $(this).attr('name')=='Product' ){
          alert_notify('fa fa-warning',$(this).attr('name')+ ' Harus Diisi !','danger');
          empty2 = true;
        }

        if(!$(this).val() && $(this).attr('name')=='BOM' ){
          alert_notify('fa fa-warning',$(this).attr('name')+ ' Harus Diisi !','danger');
          empty2 = true;
        }

      });

      // validasi untuk inputan textbox
      input.each(function(){
        if(!$(this).val() && $(this).attr('name')!='reff'){
          alert_notify('fa fa-warning',$(this).attr('name')+ ' Harus Diisi !','danger');
          empty = true;
        }
      });

      if(!empty && !empty2){
        var kode  = "<?php echo $productionorder->kode_prod ?>";
        var kode_produk  = $(this).parents("tr").find("#product").val();
        var produk       = $(this).parents("tr").find("#prodhidd").val();
        var kode_bom     = $(this).parents("tr").find("#bom").val();
        var schedule_date= $(this).parents("tr").find("#schedule_date").val();
        var qty   = $(this).parents("tr").find("#qty").val();
        var uom   = $(this).parents("tr").find("#uom").val();
        var reff  = $(this).parents("tr").find("#reff").val();
        var row_order = $(this).parents("tr").find("#row_order").val();

        $.ajax({
          dataType: "JSON",
          url : '<?php echo site_url('ppic/productionorder/simpan_detail_production_order') ?>',
          type: "POST",
          data: {kode : kode, 
                kode_produk : kode_produk,
                produk  : produk,
                kode_bom: kode_bom,
                tgl   : schedule_date, 
                qty   : qty,
                uom   : uom,
                reff  : reff,
                row_order : row_order  },
          success: function(data){
            if(data.sesi=='habis'){
                //alert jika session habis
                alert_modal_warning(data.message);
                window.location.replace('../index');
            }else if(data.status == 'failed'){
                alert_modal_warning(data.message);
                refresh_production();
            }else{
                refresh_production();
                $(".add-new").show();                   
                alert_notify(data.icon,data.message,data.type);
            }
          },
          error: function (xhr, ajaxOptions, thrownError){
            alert('Error data');
            alert(xhr.responseText);
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

          }else if($(this).attr('data-id')=="bom"){

            var kode_bom = $(this).attr('data-isi');
            var nama_bom = $(this).attr('data-isi2');
            var kode_produk = $(this).attr('data-isi3');

            class_sel2_bom = 'sel2_bom'+row_order;
                       
            //select 2 bom by kode-produk
            $(this).html('<select type="text" class="form-control input-sm '+class_sel2_bom+'" id="bom" name="BOM" ></select> ');

            var $newOption = $("<option></option>").val(kode_bom).text(nama_bom);
            $('.sel2_bom'+row_order).empty().append($newOption).trigger('change');

            //select 2 BOM
            $('.sel2_bom'+row_order).select2({
              allowClear: true,
              placeholder: "",
              ajax:{
                    dataType: 'JSON',
                    type : "POST",
                    url : "<?php echo base_url();?>ppic/productionorder/get_bom_select2_by_produk",
                    //delay : 250,
                    data : function(params){
                      return{
                        bom:params.term,
                        kode_produk:kode_produk,
                      };
                    }, 
                    processResults:function(data){
                      var results = [];

                      $.each(data, function(index,item){
                          results.push({
                              id:item.kode_bom,
                              text:item.nama_bom
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
           

          }else if($(this).attr('data-id')=="schedule_date"){
            $(this).html('<div class="input-group date " id="sch_date2" ><input type="text" class="form-control input-sm" value="'+ htmlentities_script($(this).attr('data-isi')) +'" id="'+ $(this).attr('data-id') +'" name="'+ $(this).attr('data-id') +'" readonly="readonly"  /><span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span></div> ');
            var datetomorrow=new Date();
            datetomorrow.setDate(datetomorrow.getDate() + 1);  
            $('#sch_date2').datetimepicker({
                minDate : datetomorrow,
                format : 'YYYY-MM-DD HH:mm:ss',
                ignoreReadonly: true,
             });
          }else if($(this).attr('data-id')=='qty'){
            $(this).html('<input type="text"  class="form-control input-sm" value="'+ htmlentities_script($(this).attr('data-isi')) +'" id="'+ $(this).attr('data-id') +'" name="'+ $(this).attr('data-id') +'" onkeyup="validAngka(this)"> ');
          }else if($(this).attr('data-id')=="reff"){

            
            $(this).html('<textarea type="text" onkeyup="textAreaAdjust(this)" class="form-control input-sm" id="'+ $(this).attr('data-id') +'" name="'+ $(this).attr('data-id') +'">'+ htmlentities_script($(this).attr('data-isi')) +'</textarea>');
             
          }

        });  

        $(this).parents("tr").find(".add, .edit").toggle();
        $(this).parents("tr").find(".cancel, .delete").toggle();
        $(".add-new").hide();
        $(this).parents("tr").find(".btn-generate").hide();
     
    });

    // batal add row on batal button click
    $(document).on("click", ".batal", function(){
      var input = $(this).parents("tr").find('.prod');
      input.each(function(){
       $(this).parent("td").html($(this).val());
      }); 
      
      $(this).parents("tr").remove();
      $(".add-new").show();
    });

    //btn cancel edit
    $(document).on("click", ".cancel", function(){
      refresh_production();
      $(".add-new").show();
    });

    //delete row di database
    $(document).on("click", ".delete", function(){ 
      $(this).parents("tr").find("td[data-content='edit']").each(function(){
        if($(this).attr('data-id')=="row_order"){
          $(this).html('<input type="hidden" class="form-control" value="' + htmlentities_script($(this).attr('data-isi')) + '" id="'+ $(this).attr('data-id') +'"> ');
        }
      });
      var kode  =  "<?php echo $productionorder->kode_prod; ?>";
      var row_order = $(this).parents("tr").find("#row_order").val();  
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
                      url : '<?php echo site_url('ppic/productionorder/hapus_production_order_items') ?>',
                      type: "POST",
                      data: {kode : kode, 
                            row_order : row_order  },
                      success: function(data){
                        if(data.sesi=='habis'){
                            //alert jika session habis
                            alert_modal_warning(data.message);
                            window.location.replace('../index');
                        }else if(data.status == 'failed'){
                            alert_modal_warning(data.message);
                            refresh_production();
                        }else{
                            refresh_production();
                            $(".add-new").show();                   
                            alert_notify(data.icon,data.message,data.type);
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

  
    //klik btn generate detail
    $(document).on("click", ".btn-generate", function(){
      //$(this).parents("tr").find(".btn-generate").button("loading");

      $(this).parents("tr").find("td[data-content='edit']").each(function(){
        if($(this).attr('data-id')=="row_order"){
          $(this).html('<input type="hidden" class="form-control" value="' + htmlentities_script($(this).attr('data-isi')) + '" id="'+ $(this).attr('data-id') +'"> ');
        }
      });

      var kode  =  "<?php echo $productionorder->kode_prod; ?>";
      var row_order = $(this).parents("tr").find("#row_order").val(); 
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
                  url : '<?php echo site_url('ppic/productionorder/generate_detail_production_order') ?>',
                  type: "POST",
                  data: {kode : kode,row_order : row_order  },
                  success: function(data){
                    if(data.sesi=='habis'){
                        //alert jika session habis
                        alert_modal_warning(data.message);
                        window.location.replace('../index');
                    }else if(data.status == 'failed'){
                        unblockUI( function(){});
                        //alert(data.message);
                        alert_modal_warning(data.message);
                        refresh_production();
                    }else{
                        refresh_production();
                        unblockUI( function() {
                          setTimeout(function() { alert_notify(data.icon,data.message,data.type); }, 1000);
                        });
                        //$(this).parents("tr").find(".btn-generate").button("reset");
                     }
                  },
                  error: function (xhr, ajaxOptions, thrownError){
                    alert('Error data');
                    alert(xhr.responseText);
                    refresh_production();
                    unblockUI( function(){});
                    //$(this).parents("tr").find(".btn-generate").button("reset");
                  }
                });
              }
          },
          success: {
                label    : "No",
                className: "btn-default  btn-sm",
                callback : function() {
                  $('.bootbox').modal('hide');
                  refresh_production();
                }
          }
        }
        });
        
    });


    //batal items
    $(document).on("click", ".btn-cancel-items", function(){

      $(this).parents("tr").find("td[data-content='edit']").each(function(){
        if($(this).attr('data-id')=="row_order"){
          $(this).html('<input type="hidden" class="form-control" value="' + htmlentities_script($(this).attr('data-isi')) + '" id="'+ $(this).attr('data-id') +'"> ');
        }
      });
  
      var kode  =  "<?php echo $productionorder->kode_prod; ?>";
      var row_order = $(this).parents("tr").find("#row_order").val(); 
        bootbox.dialog({
        message: "Apakah Anda ingin membatalkan item Production Order ini ?",
        title: "<i class='fa fa-warning'></i> Batal Item Production Order !",
        buttons: {
          danger: {
              label    : "Yes ",
              className: "btn-primary btn-sm",
              callback : function() {
                please_wait(function(){});
                $.ajax({
                  dataType: "JSON",
                  url : '<?php echo site_url('ppic/productionorder/batal_detail_production_order') ?>',
                  type: "POST",
                  data: {kode : kode, row_order : row_order  },
                  success: function(data){
                    if(data.sesi=='habis'){
                        //alert jika session habis
                        alert_modal_warning(data.message);
                        window.location.replace('../index');
                    }else if(data.status == 'failed'){
                        unblockUI( function(){});
                        //alert(data.message);
                        alert_modal_warning(data.message);
                        refresh_production();
                    }else{
                        refresh_production();
                        unblockUI( function() {
                          setTimeout(function() { alert_notify(data.icon,data.message,data.type); }, 1000);
                        });
                     }
                  },
                  error: function (xhr, ajaxOptions, thrownError){
                    alert('Error data');
                    alert(xhr.responseText);
                    refresh_production();
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
                  refresh_production();
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
         url :'<?php echo base_url('ppic/productionorder/simpan')?>',
         beforeSend: function(e) {
            if(e && e.overrideMimeType) {
                e.overrideMimeType("application/json;charset=UTF-8");
            }
         },
         data: {kode_prod   : $('#kode_prod').val(),
                tgl         : $('#tgl').val(),
                note        : $('#note').val(),
                sales_order : $('#sales_order').val(),
                priority    : $('#priority').val(),
                warehouse   : $('#warehouse').val(),

          },success: function(data){
            if(data.sesi == "habis"){
              //alert jika session habis
              alert_modal_warning(data.message);
              window.location.replace('../index');
            }else if(data.status == "failed"){
              //jika ada form belum keiisi
              refresh_production();
              unblockUI( function() {
                setTimeout(function() { alert_notify(data.icon,data.message,data.type); }, 1000);
              });
              document.getElementById(data.field).focus();
              $('#btn-simpan').button('reset');
            }else{
              //jika berhasil disimpan/diubah
              refresh_production();
              unblockUI( function() {
                  setTimeout(function() { alert_notify(data.icon,data.message,data.type); }, 1000);
              });
            }
            $('#btn-simpan').button('reset');

          },error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.responseText);
            unblockUI( function(){});
            $('#btn-simpan').button('reset');

          }
      });
        window.setTimeout(function() {
       $(".alert").fadeTo(500, 0).slideUp(500, function(){
        $(this).remove(); });
      }, 3000);
    });
    

</script>


</body>
</html>
