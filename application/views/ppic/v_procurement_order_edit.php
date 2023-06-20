
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
         $data['jen_status'] =  $procurementorder->status;
         $this->load->view("admin/_partials/statusbar.php", $data); 
       ?>
      </div>
    </section>

    <!-- Main content -->
    <section class="content">

      <!--  box content -->
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title"><b><?php echo $procurementorder->kode_proc;?></b></h3>
        </div>
        <div class="box-body">
            <form class="form-horizontal" id="form_header" name="Procurement Order">

            <div class="form-group">                  
              <div class="col-md-12" >
                <div id="alert"></div>
              </div>
            </div>

              <div class="form-group">

              <div class="col-md-6">
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Procurement Order </label></div>
                  <div class="col-xs-8">
                    <input type="text" class="form-control input-sm" name="kode_proc" id="kode_proc"  readonly="readonly" value="<?php echo $procurementorder->kode_proc?>" />
                  </div>                                    
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Create Date </label></div>
                  <div class="col-xs-8 col-md-8">
                     <input type='text' class="form-control input-sm" name="tgl_buat" id="tgl_buat" readonly="readonly" value="<?php echo $procurementorder->create_date?>"  />
                  </div>                                    
                </div>

                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Notes </label></div>
                  <div class="col-xs-8">
                    <textarea type="text" class="form-control input-sm" name="note" id="note"><?php echo $procurementorder->notes?></textarea>
                  </div>                                    
                </div>

                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Type </label></div>
                  <div class="col-xs-8">
                    <div class="col-xs-12 col-sm-12 col-md-12">
                      <?php 
                        $checked_type = "";
                        if($procurementorder->type == 'mto'){  
                          $checked_type = "checked";
                        }
                      ?>
                      <input type="radio" id="mto" name="type[]" value="mto"  <?php echo $checked_type;?> disabled >
                      <label for="mto">Make to Order</label>
                    </div>
                    <div class="col-xs-12 col-sm-3 col-md-12">
                      <?php 
                        $checked_type2 = "";
                        if($procurementorder->type == 'mts'){  
                          $checked_type2 = "checked";
                        }
                      ?>
                      <input type="radio" id="mts" name="type[]" value="mts" <?php echo $checked_type2;?> disabled >
                      <label for="mst">Make to Stock</label>
                    </div>
                    <div class="col-xs-12 col-sm-12 col-md-12">
                      <?php 
                        $checked_type3 = "";
                        if($procurementorder->type == 'pengiriman'){  
                          $checked_type3 = "checked";
                        }
                      ?>
                      <input type="radio" id="pengiriman" name="type[]" value="pengiriman" <?php echo $checked_type3;?> disabled >
                      <label for="pengiriman">Pengiriman</label>
                    </div>
                  </div>                                    
                </div>

                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Sales Order </label></div>
                  <div class="col-xs-8">
                    <div class="col-xs-6 col-sm-4 col-md-4">
                      <?php 
                        $checked = "";
                        if($procurementorder->show_sales_order == 'yes'){  
                          $checked = "checked";
                        }
                      ?>
                      <input type="radio" id="sc_true" name="sc[]" value="yes"  <?php echo $checked;?> disabled  >
                      <label for="yes">Yes</label>
                    </div>
                    <div class="col-xs-6 col-sm-4 col-md-4">
                      <?php 
                        $checked2 = "";
                        if($procurementorder->show_sales_order == 'no'){  
                          $checked2 = "checked";
                        }
                      ?>
                        <input type="radio" id="sc_false" name="sc[]" value="no" <?php echo $checked2;?> disabled >
                      <label for="no">No</label>
                    </div>
                  </div>                                    
                </div>

              </div>
              <!-- /.col-md-6 -->

              <div class="col-md-6">
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Schedule Date </label></div>
                  <div class="col-xs-8 col-md-8">
                    <input type='text' class="form-control input-sm" name="tgl" id="tgl" readonly="readonly" value="<?php echo $procurementorder->schedule_date?>" />
                  </div>                                    
                </div>
                <?php if($procurementorder->show_sales_order == 'yes'){ ?>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Production Order</label></div>
                  <div class="col-xs-8">
                    <input type="text" class="form-control input-sm" name="kode_prod" id="kode_prod" readonly="readonly"  value="<?php echo $procurementorder->kode_prod?>"/>
                  </div>                                    
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Sales Order</label></div>
                  <div class="col-xs-8">
                    <input type="text" class="form-control input-sm" name="sales_order" id="sales_order" readonly="readonly"  value="<?php echo $procurementorder->sales_order?>"/>
                  </div>                                    
                </div> 
                <?php } ?>
                <div  class="col-md-12 col-xs-12">
                  <?php if($procurementorder->type == 'mts'){
                    $label_dept = 'Departemen';
                  }else{
                    $label_dept = 'Departemen Tujuan';
                  }
                  ?>
                  <div class="col-xs-4"><label><?php echo $label_dept;?></label></div>
                  <div class="col-xs-8">
                    <select class="form-control input-sm" name="warehouse" id="warehouse" />
                      <?php
                        echo '<option value="">Pilih Warehouse</option>';
                        foreach ($warehouse as $row) {
                          if($row->kode == $procurementorder->warehouse){?>
                           <option value='<?php echo $row->kode; ?>' selected><?php echo $row->nama;?></option>
                        <?php
                          }else{?>
                          <option value='<?php echo $row->kode; ?>'><?php echo $row->nama;?></option>
                        <?php  
                          }
                        }
                        /*
                      if($cek_status > 0){
                        foreach ($warehouse  as $row) {
                          if($row->kode == $procurementorder->warehouse){
                            echo  "<option value='".$row->kode."' selected>". $row->nama."</option>";
                          }
                        }
                      }else{
                      }
                      */
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
                     for($i=0;$i<=3;$i++) {
                      if($val[$i] == $procurementorder->priority){?>
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
              <!-- /.col-md-6 -->

              </div>
              <!-- /.from-group -->

            </form>
            <!-- /.from- -->

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
                        <table class="table table-condesed table-hover rlstable  over" width="100%" id="procurements" >
                          <thead>                          
                            <tr>
                              <th class="style no">No.</th>
                              <th class="style" width="200px">Product</th>
                              <th class="style" width="150px">Schedule Date</th>
                              <th class="style" style="width:100px; text-align: right;" >Qty</th>
                              <th class="style" width="80px">Uom</th>
                              <th class="style" width="200px">Reff Notes PPIC</th>
                              <th class="style" width="60px">Status</th>
                              <th class="style" width="80px"></th>
                              <th class="style" width="50px"></th>
                            </tr>
                          </thead>
                          <tbody>
                            <?php
                              $no = 1;
                              foreach ($details as $row) {
                                if($row->status == 'cancel') $color = 'red'; else $color = '';
                            ?>
                              <tr style="color:<?php echo $color;?>">
                                <td data-content="edit" data-id="row_order" data-isi="<?php echo $row->row_order;?>"><?php echo $no++.".";?></td>
                                <td><?php echo '['.$row->kode_produk.'] '.$row->nama_produk?></a></td>
                                <td data-content="edit" data-id="schedule_date" data-isi="<?php echo $row->schedule_date;?>"><?php echo $row->schedule_date?></td>
                                <td data-content="edit" data-id="qty" data-name="Qty" data-isi="<?php echo $row->qty;?>" align="right"><?php echo number_format($row->qty,2)?></td>
                                <td><?php echo $row->uom?></td>
                                <td data-content="edit" data-id="reff" data-isi="<?php echo htmlentities($row->reff_notes);?>" class="text-wrap width-200"> <?php echo $row->reff_notes?></td>                               
                                <td><?php if($row->status == 'cancel') echo 'Batal';  else echo $row->status;?></td>
                                <td align="center">
                                  <?php if($row->status == 'draft'){?>
                                  <a href="javascript:void(0)" class="add" title="Simpan" data-toggle="tooltip" ><i class="fa fa-save"></i></a>
                                  <a href="javascript:void(0)" class="edit" title="Edit" data-toggle="tooltip" style="color: #FFC107;   margin-right: 24px;"><i class="fa fa-edit"></i></a>
                                  <a href="javascript:void(0)" class="delete" title="Hapus" data-toggle="tooltip"><i class="fa fa-trash" style="color: red"></i></a>
                                  <a href="javascript:void(0)" class="cancel" title="Cancel" data-toggle="tooltip" style="margin-left: 20px;"><i class="fa fa-close"></i></a>
                                <?php }
                                 if($row->status == 'generated' OR $row->status == 'cancel'){?>
                                       <a href="javascript:void(0)" data-toggle="tooltip" title="Details" onclick="view_detail('<?php echo $procurementorder->kode_proc; ?>','<?php echo $procurementorder->kode_prod; ?>','<?php echo $procurementorder->sales_order; ?>','<?php echo $row->kode_produk?>','<?php echo htmlentities($row->nama_produk)?>','<?php echo $row->row_order?>')"><span class="glyphicon  glyphicon-share"></span></a>

                                <?php }
                                ?>
                                </td>
                                <td>
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

  function validAngka(a){
    if(!/^[0-9.]+$/.test(a.value)){
      a.value = a.value.substring(0,a.value.length-1000);
      alert_notify('fa fa-warning','Maaf, Inputan Qty Hanya Berupa Angka !','danger',function(){});
    }
  }

  //html entities javascript
  function htmlentities_script(str) {
    return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
  }

  //modal view move items
  function view_detail(kode,kode_prod,sales_order,kode_produk,nama_produk,row_order){
      $("#view_data").modal({
          show: true,
          backdrop: 'static'
      })
      $(".view_body").html('<center><h5><img src="<?php echo base_url('dist/img/ajax-loader.gif') ?> "/><br>Please Wait...</h5></center>');
      $('.modal-title').text('Detail Items');
        $.post('<?php echo site_url()?>ppic/procurementorder/view_detail_items',
          {kode:kode, kode_prod:kode_prod, sales_order:sales_order, kode_produk:kode_produk, nama_produk:nama_produk, nama_produk:nama_produk, row_order:row_order},
          function(html){
            setTimeout(function() {$(".view_body").html(html);  });
          }   
       );
  }


  // Append table with add row form on add new button click
  $(document).on("click", ".add-new", function(){
  
    $(".add-new").hide();
    var index = $("#procurements tbody tr:last-child").index();
    var row   ='<tr class="">'
          + '<td></td>'
          + '<td><select type="text" class="form-control input-sm prod" name="Product" id="product" style="min-width:150px;"></select></select><input type="hidden" class="form-control input-sm prodhidd" name="prodhidd" id="prodhidd"></td>'
          + '<td><div class="input-group date width-150" id="sch_date" ><input type="text" class="form-control input-sm" name="schedule_date" id="schedule_date" readonly="readonly"  /><span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span></div></td>'
          + '<td><input type="text" class="form-control input-sm width-100 qty" name="Qty" id="qty"  onkeyup="validAngka(this)" ></td>'
          + '<td><select type="text" class="form-control input-sm width-80 uom" name="Uom" id="uom"><option value=""></option><?php foreach($uom as $row){?><option value="<?php echo $row->short; ?>"><?php echo $row->short;?></option>"<?php }?></select></td>'
          + '<td><textarea type="text" class="form-control input-sm width-150" name="reff" id="reff"></textarea></td>'
          + '<td></td>'
          + '<td align="center"><button type="button" class="btn btn-primary btn-xs add width-btn" title="Simpan" data-toggle="tooltip">Simpan</button><a class="edit" title="Edit" data-toggle="tooltip"><i class="fa fa-edit"></i></a><button type="button" class="btn btn-danger btn-xs batal width-btn" title="Batal" data-toggle="tooltip">Batal</button></td>'
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
                url : "<?php echo base_url();?>ppic/procurementorder/get_produk_procurement_order_select2",
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
                 // alert(xhr.responseText);
                }
          }
        });

        $(".prod").change(function(){
            $.ajax({
                  dataType: "JSON",
                  url : '<?php echo site_url('ppic/procurementorder/get_prod_by_id') ?>',
                  type: "POST",
                  data: {kode_produk: $(this).parents("tr").find("#product").val() },
                  success: function(data){
                    $('.prodhidd').val(data.nama_produk);
                    $('.uom').val(data.uom);
                  },
                  error: function (xhr, ajaxOptions, thrownError){
                  //  alert('Error data');
                  //  alert(xhr.responseText);
                  }
            });
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

  

  //untuk merrefresh procurement order
  function refresh_procurement(){
      $("#tab_1").load(location.href + " #tab_1");
      $("#foot").load(location.href + " #foot");
      $("#status_bar").load(location.href + " #status_bar");
  }

  //untuk reload page setelah modal ditutup
  $(".modal").on('hidden.bs.modal', function(){
    refresh_procurement();
  });


  //simpan / edit row data ke database
  $(document).on("click", ".add", function(e){
      e.preventDefault();

      var empty = false;
      var input = $(this).parents("tr").find('input[type="text"]');

      var empty2 = false;
      var select = $(this).parents("tr").find('select[type="text"]');

      //validasi tidak boleh kosong hanya select product saja
      select.each(function(){
        if(!$(this).val() && $(this).attr('name')=='Product' ){
          alert_notify('fa fa-warning',$(this).attr('name')+ ' Harus Diisi !','danger',function(){});
          empty2 = true;
        }
        if(!$(this).val() && $(this).attr('name')=='Uom' ){
          alert_notify('fa fa-warning',$(this).attr('name')+ ' Harus Diisi !','danger',function(){});
          empty2 = true;
        }
      });


      // validasi untuk qty = 0
      input.each(function(){
        if($(this).attr('name') =='Qty'){
          qty_val = parseFloat($(this).val());
          if(qty_val == false){
            alert_notify('fa fa-warning',$(this).attr('name')+ ' tidak boleh 0 !','danger',function(){});
            empty = true;
          }
        }
      });


      // validasi untuk inputan textbox
      input.each(function(){
        if(!$(this).val() && $(this).attr('name')!='reff'){
          alert_notify('fa fa-warning',$(this).attr('name')+ ' Harus Diisi !','danger',function(){});
          empty = true;
        }
      });
      
    
      if(!empty && !empty2){
        var kode  = "<?php echo $procurementorder->kode_proc ?>";
        var kode_produk  = $(this).parents("tr").find("#product").val();
        var produk       = $(this).parents("tr").find("#prodhidd").val();
        var schedule_date= $(this).parents("tr").find("#schedule_date").val();
        var qty   = $(this).parents("tr").find("#qty").val();
        var uom   = $(this).parents("tr").find("#uom").val();
        var reff  = $(this).parents("tr").find("#reff").val();
        var row_order = $(this).parents("tr").find("#row_order").val();
        //alert(qty);
        var btn_load = $(this);
        btn_load.button('loading');
        $.ajax({
          dataType: "JSON",
          url : '<?php echo site_url('ppic/procurementorder/simpan_detail_procurement_order') ?>',
          type: "POST",
          data: {kode : kode, 
                kode_produk : kode_produk,
                produk  : produk,
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
                refresh_procurement();
            }else{
                refresh_procurement();
                $(".add-new").show();                   
                alert_notify(data.icon,data.message,data.type,function(){});
             }
             btn_load.button('reset');
          },
          error: function (xhr, ajaxOptions, thrownError){
            alert('Error data');
            alert(xhr.responseText);
            btn_load.button('reset');
          }
        });
        
        
      }   
    });

    //untuk merrefresh procurement order
    function refresh_procurement(){
        $("#tab_1").load(location.href + " #tab_1");
        $("#foot").load(location.href + " #foot");
        $("#status_bar").load(location.href + " #status_bar");
    }

    // Edit row on edit button click
    $(document).on("click", ".edit", function(){  
        $(this).parents("tr").find("td[data-content='edit']").each(function(){
          if($(this).attr('data-id')=="row_order"){
            $(this).html('<input type="hidden"  class="form-control" value="' + $(this).attr('data-isi') + '" id="'+ $(this).attr('data-id') +'"> ');
          }else if($(this).attr('data-id')=="schedule_date"){
            $(this).html('<div class="input-group date" id="sch_date2" ><input type="text" class="form-control input-sm" value="'+ htmlentities_script($(this).attr('data-isi')) +'" id="'+ $(this).attr('data-id') +'" name="'+ $(this).attr('data-id') +'" readonly="readonly"  /><span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span></div> ');
            var datetomorrow=new Date();
            datetomorrow.setDate(datetomorrow.getDate() + 1);  
            $('#sch_date2').datetimepicker({
                minDate : datetomorrow,
                format : 'YYYY-MM-DD HH:mm:ss',
                ignoreReadonly: true,
             });
          }else if($(this).attr('data-id')=='qty'){
            $(this).html('<input type="text"  class="form-control" value="'+ htmlentities_script($(this).attr('data-isi')) +'" id="'+ $(this).attr('data-id') +'" name="'+ $(this).attr('data-name') +'" onkeyup="validAngka(this)"> ');
          }else if($(this).attr('data-id')=="reff"){
            $(this).html('<textarea type="text" class="form-control input-sm" id="'+ $(this).attr('data-id') +'" name="'+ $(this).attr('data-id') +'">'+ htmlentities_script($(this).attr('data-isi')) +'</textarea>');
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
      $("#tab_1").load(location.href + " #tab_1");
      $(".add-new").show();
    });

    //delete row di database
    //$(".delete").off("click").on("click",function(e) {
    $(document).on("click", ".delete", function(e){
      e.preventDefault();

      $(this).parents("tr").find("td[data-content='edit']").each(function(){
        if($(this).attr('data-id')=="row_order"){
          $(this).html('<input type="hidden" class="form-control" value="' + htmlentities_script($(this).attr('data-isi')) + '" id="'+ $(this).attr('data-id') +'"> ');
        }
      });
      var kode  =  "<?php echo $procurementorder->kode_proc; ?>";
      var row_order = $(this).parents("tr").find("#row_order").val();  
      var btn_load = $(this);
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
                      url : '<?php echo site_url('ppic/procurementorder/hapus_procurement_order_items') ?>',
                      type: "POST",
                      data: {kode : kode, 
                            row_order : row_order  },
                      beforeSend : function(){
                          btn_load.button('loading');
                      },
                      success: function(data){
                        if(data.sesi=='habis'){
                            //alert jika session habis
                            alert_modal_warning(data.message);
                            window.location.replace('../index');
                        }else if(data.status == 'failed'){
                            alert_modal_warning(data.message);
                            refresh_procurement();
                        }else{
                            refresh_procurement();
                            $(".add-new").show();                   
                            alert_notify(data.icon,data.message,data.type,function(){});
                         }
                          btn_load.button('reset');
                      },
                      error: function (xhr, ajaxOptions, thrownError){
                        alert('Error data');
                        alert(xhr.responseText);
                        btn_load.button('reset');
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
    

    $(document).on("click", ".btn-generate", function(e){
    //$(".btn-generate").off("click").on("click",function(e) {
      e.preventDefault();

      $(this).parents("tr").find("td[data-content='edit']").each(function(){
        if($(this).attr('data-id')=="row_order"){
          $(this).html('<input type="hidden" class="form-control" value="' + htmlentities_script($(this).attr('data-isi')) + '" id="'+ $(this).attr('data-id') +'"> ');
        }
      });
      
      var kode  =  "<?php echo $procurementorder->kode_proc; ?>";
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
                  url : '<?php echo site_url('ppic/procurementorder/generate_detail_procurement_order') ?>',
                  type: "POST",
                  data: {kode : kode,row_order : row_order  },
                  success: function(data){
                    if(data.sesi=='habis'){
                        //alert jika session habis
                        alert_modal_warning(data.message);
                        window.location.replace('../index');
                    }else if(data.status == 'failed'){
                        unblockUI( function(){});
                        alert_modal_warning(data.message);
                        refresh_procurement();
                    }else{
                        refresh_procurement();
                        unblockUI( function() {
                        setTimeout(function() { alert_notify(data.icon,data.message,data.type,function(){}); }, 1000);
                        });
                        //$(this).parents("tr").find(".btn-generate").button("reset");
                     }
                  },
                  error: function (xhr, ajaxOptions, thrownError){
                    alert('Error data');
                    alert(xhr.responseText);
                    refresh_procurement();
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
                  refresh_procurement();
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
  
      var kode  =  "<?php echo $procurementorder->kode_proc; ?>";
      var row_order = $(this).parents("tr").find("#row_order").val(); 
        bootbox.dialog({
        message: "Apakah Anda ingin membatalkan item Procurement Order ini ?",
        title: "<i class='fa fa-warning'></i> Batal Item Procurement Order !",
        buttons: {
          danger: {
              label    : "Yes ",
              className: "btn-primary btn-sm",
              callback : function() {
                please_wait(function(){});
                $.ajax({
                  dataType: "JSON",
                  url : '<?php echo site_url('ppic/procurementorder/batal_detail_procurement_order') ?>',
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
                        refresh_procurement();
                    }else{
                        refresh_procurement();
                        unblockUI( function() {
                          setTimeout(function() { alert_notify(data.icon,data.message,data.type,function(){}); }, 1000);
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
    $('#btn-simpan').click(function(e){

      e.preventDefault();

      $('#sc_true').attr('id','sc_true');
      $('#sc_false').attr('id','sc_false');
      $('#mts').attr('id','mts');
      $('#mto').attr('id','mto');
      $('#pengiriman').attr('id','pengiriman');

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

      $('#btn-simpan').button('loading');
      please_wait(function(){});
      $.ajax({
         type: "POST",
         dataType: "json",
         url :'<?php echo base_url('ppic/procurementorder/simpan')?>',
         beforeSend: function(e) {
            if(e && e.overrideMimeType) {
                e.overrideMimeType("application/json;charset=UTF-8");
            }
         },
         data: {kode_proc   : $('#kode_proc').val(),
                kode_prod   : $('#kode_prod').val(),
                tgl         : $('#tgl').val(),
                note        : $('#note').val(),
                sales_order : $('#sales_order').val(),
                priority    : $('#priority').val(),
                warehouse   : $('#warehouse').val(),
                type        : radio_type,
                show_sc     : radio_type_2,

          },success: function(data){
            if(data.sesi == "habis"){
              //alert jika session habis
              alert_modal_warning(data.message);
              window.location.replace('../index');
            }else if(data.status == "failed"){
                //jika ada form belum keiisi
                refresh_procurement();
                unblockUI( function() {
                  setTimeout(function() { alert_notify(data.icon,data.message,data.type,function(){}); }, 1000);
                });
                 document.getElementById(data.field).focus();
            }else{
                //jika berhasil disimpan/diubah
                refresh_procurement();
                unblockUI( function() {
                  setTimeout(function() { alert_notify(data.icon,data.message,data.type,function(){}); }, 1000);
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
    
    /*
   //klik button Batal
    $('#btn-cancel').click(function(){
       //$("#ref_warehouse").load(location.href + " #ref_warehouse");
       $("#ref_warehouse").ajax.reload();
    });
    */

</script>


</body>
</html>
