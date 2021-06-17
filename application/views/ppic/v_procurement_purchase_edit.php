
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
         $data['jen_status'] =  $procurementpurchase->status;
         $this->load->view("admin/_partials/statusbar.php", $data); 
       ?>
      </div>
    </section>

    <!-- Main content -->
    <section class="content">

      <!--  box content -->
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title"><b><?php echo $procurementpurchase->kode_pp;?></b></h3>
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
                    <input type="text" class="form-control input-sm" name="kode_pp" id="kode_pp"  readonly="readonly" value="<?php echo $procurementpurchase->kode_pp?>" />
                  </div>                                    
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Create Date </label></div>
                  <div class="col-xs-8 col-md-8">
                     <input type='text' class="form-control input-sm" name="tgl_buat" id="tgl_buat" readonly="readonly" value="<?php echo $procurementpurchase->create_date?>"  />
                  </div>                                    
                </div>

                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Reff Notes </label></div>
                  <div class="col-xs-8">
                    <textarea type="text" class="form-control input-sm" name="note" id="note"><?php echo $procurementpurchase->notes?></textarea>
                  </div>                                    
                </div>
              </div>

              <div class="col-md-6">
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Schedule Date </label></div>
                  <div class="col-xs-8 col-md-8">
                    <input type='text' class="form-control input-sm" name="tgl" id="tgl" readonly="readonly" value="<?php echo $procurementpurchase->schedule_date?>" />
                  </div>                                    
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Production Order</label></div>
                  <div class="col-xs-8">
                    <input type="text" class="form-control input-sm" name="kode_prod" id="kode_prod" readonly="readonly"  value="<?php echo $procurementpurchase->kode_prod?>"/>
                  </div>                                    
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Sales Order</label></div>
                  <div class="col-xs-8">
                    <input type="text" class="form-control input-sm" name="sales_order" id="sales_order" readonly="readonly"  value="<?php echo $procurementpurchase->sales_order?>"/>
                  </div>                                    
                </div>        
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Warehouse</label></div>
                  <div class="col-xs-8">
                    <select class="form-control input-sm" name="warehouse" id="warehouse" />
                      <?php
                      /*
                      if($cek_status > 0){
                        foreach ($warehouse  as $row) {
                          if($row->kode == $procurementpurchase->warehouse){
                            echo  "<option value='".$row->kode."' selected>". $row->nama."</option>";
                          }
                        }
                      }else{
                      }
                      */
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
                    $val = array('Not Urgent','Normal','Urgent','Very Urgent');
                     for($i=0;$i<=3;$i++) {
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
                        <table class="table table-condesed table-hover rlstable  over" width="100%" id="procurements" >
                          <thead>                          
                            <tr>
                              <th class="style no">No.</th>
                              <th class="style" width="200px">Product</th>
                              <th class="style" width="150px">Schedule Date</th>
                              <th class="style" style="width:100px; text-align: right;" >Qty</th>
                              <th class="style" width="80px">Uom</th>
                              <th class="style" width="200px">Notes</th>
                              <th class="style" width="60px">Status</th>
                              <th class="style" style="width: 80px; text-align: center;">
                                <?php
                                  if($procurementpurchase->status == 'done' OR $procurementpurchase->status == 'cancel'){
                                ?>   
                                   <a href="javascript:void(0)" data-toggle="tooltip" title="Details" onclick="view_detail('<?php echo $procurementpurchase->kode_pp; ?>','<?php echo $procurementpurchase->kode_prod; ?>','<?php echo $procurementpurchase->sales_order; ?>')"><span class="glyphicon  glyphicon-share"></span></a>
                                <?php
                                  }
                                ?>

                              </th>                            
                            </tr>
                          </thead>
                          <tbody>
                            <?php
                              $no = 1;
                              foreach ($details as $row) {
                            ?>
                              <tr class="">
                                <td data-content="edit" data-id="row_order" data-isi="<?php echo $row->row_order."^|".$row->kode_produk."^|".htmlentities($row->nama_produk)."^|".$row->qty."^|".$row->uom."^|".htmlentities($row->reff_notes)."^|".$row->schedule_date."^|".$procurementpurchase->sales_order."^|".$procurementpurchase->kode_prod."^|".$procurementpurchase->warehouse;?>"><?php echo $no++.".";?></td>
                                <td><?php echo '['.$row->kode_produk.'] '.$row->nama_produk;?></a></td>
                                <td data-content="edit" data-id="schedule_date" data-isi="<?php echo $row->schedule_date;?>"><?php echo $row->schedule_date?></td>
                                <td data-content="edit" data-id="qty" data-isi="<?php echo $row->qty;?>" align="right"><?php echo number_format($row->qty,2)?></td>
                                <td><?php echo $row->uom?></td>
                                <td data-content="edit" data-id="reff" data-isi="<?php echo htmlentities($row->reff_notes);?>" class="text-wrap width-200"> <?php echo $row->reff_notes?></td>                               
                                <td><?php if($row->status == 'cancel') echo 'Batal';  else echo $row->status;?></td>
                                <td align="center">
                                  <?php if($row->status == 'draft'){?>
                                  <a href="javascript:void(0)" class="add" title="Simpan" data-toggle="tooltip" ><i class="fa fa-save"></i></a>
                                  <a href="javascript:void(0)" class="edit" title="Edit" data-toggle="tooltip" style="color: #FFC107;   margin-right: 24px;"><i class="fa fa-edit"></i></a>
                                  <a href="javascript:void(0)" class="delete" title="Hapus" data-toggle="tooltip"><i class="fa fa-trash" style="color: red"></i></a>
                                  <a href="javascript:void(0)" class="cancel" title="Cancel" data-toggle="tooltip" style="margin-left: 20px;"><i class="fa fa-close"></i></a>
                                <?php }?>
                                </td>                               
                              </tr>
                            <?php 
                              }
                            ?>
                          </tbody>
                          <tfoot>
                            <?php 
                              if($cek_status == 0 ){?>
                                <tr>
                                  <td colspan="8">
                                    <a href="javascript:void(0)" class="add-new"><i class="fa fa-plus"></i> Tambah Data</a>
                                  </td>
                                </tr>
                              <?php }?>
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
      alert_notify('fa fa-warning','Maaf, Inputan Qty Hanya Berupa Angka !','danger');
    }
  }

  //html entities javascript
  function htmlentities_script(str) {
    return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
  }

  //modal view move items
  function view_detail(kode_pp,kode_prod,sales_order){
      $("#view_data").modal({
          show: true,
          backdrop: 'static'
      })
      $(".view_body").html('<center><h5><img src="<?php echo base_url('dist/img/ajax-loader.gif') ?> "/><br>Please Wait...</h5></center>');
      $('.modal-title').text('Detail Items');
        $.post('<?php echo site_url()?>ppic/procurementpurchase/view_detail_items',
          {kode_pp:kode_pp, kode_prod:kode_prod, sales_order:sales_order},
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
          + '<td><select type="text" class="form-control input-sm prod" name="Product" id="product"></select></select><input type="hidden" class="form-control input-sm prodhidd" name="prodhidd" id="prodhidd"></td>'
          + '<td><div class="input-group date" id="sch_date" ><input type="text" class="form-control input-sm" name="schedule_date" id="schedule_date" readonly="readonly"  /><span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span></div></td>'
          + '<td><input type="text" class="form-control input-sm qty" name="Qty" id="qty"  onkeyup="validAngka(this)" ></td>'
          + '<td><input type="text" class="form-control input-sm uom" name="Uom" id="uom"></td>'
          + '<td><textarea type="text" class="form-control input-sm" name="reff" id="reff"></textarea></td>'
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
                url : "<?php echo base_url();?>ppic/procurementpurchase/get_produk_procurement_purchase_select2",
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
                  //alert(xhr.responseText);
                  //alert('Error data');
                }
          }
        });

        $(".prod").change(function(){
            $.ajax({
                  dataType: "JSON",
                  url : '<?php echo site_url('ppic/procurementpurchase/get_prod_by_id') ?>',
                  type: "POST",
                  data: {kode_produk: $(this).parents("tr").find("#product").val() },
                  success: function(data){
                    $('.prodhidd').val(data.nama_produk);
                    $('.uom').val(data.uom);
                  },
                  error: function (xhr, ajaxOptions, thrownError){
                   // alert('Error data');
                   // alert(xhr.responseText);
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

  //refresh procurement purchase
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
      });


      // validasi untuk inputan textbox
      input.each(function(){
        if(!$(this).val() && $(this).attr('name')!='reff'){
          alert_notify('fa fa-warning',$(this).attr('name')+ ' Harus Diisi !','danger');
          empty = true;
        }
      });
      
    
      if(!empty && !empty2){
        var kode  = "<?php echo $procurementpurchase->kode_pp ?>";
        var kode_produk  = $(this).parents("tr").find("#product").val();
        var produk       = $(this).parents("tr").find("#prodhidd").val();
        var schedule_date= $(this).parents("tr").find("#schedule_date").val();
        var qty   = $(this).parents("tr").find("#qty").val();
        var uom   = $(this).parents("tr").find("#uom").val();
        var reff  = $(this).parents("tr").find("#reff").val();
        var row_order = $(this).parents("tr").find("#row_order").val();

        $.ajax({
          dataType: "JSON",
          url : '<?php echo site_url('ppic/procurementpurchase/simpan_detail_procurement_purchase') ?>',
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
            }else{
                refresh_procurement();
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
            $(this).html('<input type="text"  class="form-control" value="'+ htmlentities_script($(this).attr('data-isi')) +'" id="'+ $(this).attr('data-id') +'" name="'+ $(this).attr('data-id') +'" onkeyup="validAngka(this)"> ');
          }else if($(this).attr('data-id')=="reff"){
            $(this).html('<textarea type="text" class="form-control input-sm" id="'+ $(this).attr('data-id') +'" name="'+ $(this).attr('data-id') +'">'+ htmlentities_script($(this).attr('data-isi')) +'</textarea>');
          }

        });  

        $(this).parents("tr").find(".add, .edit").toggle();
        $(this).parents("tr").find(".cancel, .delete").toggle();
        $(".add-new").hide();
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
    $(document).on("click", ".delete", function(){ 
      $(this).parents("tr").find("td[data-content='edit']").each(function(){
        if($(this).attr('data-id')=="row_order"){
          $(this).html('<input type="hidden" class="form-control" value="' + htmlentities_script($(this).attr('data-isi')) + '" id="'+ $(this).attr('data-id') +'"> ');
        }
      });
      var kode  =  "<?php echo $procurementpurchase->kode_pp; ?>";
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
                      url : '<?php echo site_url('ppic/procurementpurchase/hapus_procurement_purchase_items') ?>',
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
                            refresh_procurement();
                        }else{
                            refresh_procurement();
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

    $(document).on("click", "#btn-generate", function(){
     
      var kode  =  "<?php echo $procurementpurchase->kode_pp; ?>";
      var status_head = "<?php echo $procurementpurchase->status?>";

      if(status_head == 'cancel'){
        alert_modal_warning('Maaf, Procurement Purchase Sudah dibatalkan !');

      }else if(status_head == 'done'){
        alert_modal_warning('Maaf, Product Sudah Generated !');
      }else{
     
        bootbox.dialog({
        message: "Apakah Anda ingin Generate Data ?",
        title: "<i class='fa fa-gear'></i> Generate Data !",
        buttons: {
          danger: {
              label    : "Yes ",
              className: "btn-primary btn-sm",
              callback : function() {
                please_wait(function(){});
                $('#btn-generate').button('loading');
                $.ajax({
                  dataType: "JSON",
                  url : '<?php echo site_url('ppic/procurementpurchase/generate_procurement_purchase') ?>',
                  type: "POST",
                  data: {kode : kode },
                  success: function(data){
                    if(data.sesi=='habis'){
                        //alert jika session habis
                        alert_modal_warning(data.message);
                        window.location.replace('../index');
                    }else if(data.status == 'failed'){
                        alert_modal_warning(data.message);
                        refresh_procurement();
                        unblockUI( function() {});
                        $('#btn-generate').button('reset');
                    }else{
                        refresh_procurement();
                        unblockUI( function() {
                          setTimeout(function() { alert_notify(data.icon,data.message,data.type); }, 1000);
                        });
                        $('#btn-generate').button('reset');
                        
                     }
                  },
                  error: function (xhr, ajaxOptions, thrownError){
                    alert('Error data');
                    alert(xhr.responseText);
                    unblockUI( function(){});
                    $('#btn-generate').button('reset');
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
        
    });


    //batal procurement purchase
    $(document).on("click", "#btn-cancel", function(){
  
      var kode  =  "<?php echo $procurementpurchase->kode_pp; ?>";
      var kode_prod = "<?php echo $procurementpurchase->kode_prod; ?>";
      var sales_order = "<?php echo $procurementpurchase->sales_order; ?>";

      var status  = "<?php echo $procurementpurchase->status; ?>";

      if(status == 'cancel'){
        var message = 'Maaf, Procurement Purchase Sudah dibatalkan !';
        alert_modal_warning(message);
      }else if(status == 'draft'){
         var message = 'Maaf, Status Procurement Purchase Masih draft !';
        alert_modal_warning(message);
      }else{
        bootbox.dialog({
        message: "Apakah Anda ingin membatalkan Procurement Purchase ini ?",
        title: "<i class='fa fa-warning'></i> Batal Procurements Purchase !",
        buttons: {
          danger: {
              label    : "Yes ",
              className: "btn-primary btn-sm",
              callback : function() {
                please_wait(function(){});
                $.ajax({
                  dataType: "JSON",
                  url : '<?php echo site_url('ppic/procurementpurchase/batal_procurement_purchase') ?>',
                  type: "POST",
                  data: {kode : kode, kode_prod:kode_prod, sales_order:sales_order},
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
                          setTimeout(function() { alert_notify(data.icon,data.message,data.type); }, 1000);
                        });
                     }
                  },
                  error: function (xhr, ajaxOptions, thrownError){
                    alert('Error data');
                    alert(xhr.responseText);
                    refresh_procurement();
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
                  refresh_procurement();
                }
          }
        }
        });

      }

    }); 

    
    //klik button simpan
    $('#btn-simpan').click(function(){
      $('#btn-simpan').button('loading');
      please_wait(function(){});
      $.ajax({
         type: "POST",
         dataType: "json",
         url :'<?php echo base_url('ppic/procurementpurchase/simpan')?>',
         beforeSend: function(e) {
            if(e && e.overrideMimeType) {
                e.overrideMimeType("application/json;charset=UTF-8");
            }
         },
         data: {kode_pp   : $('#kode_pp').val(),
                kode_prod   : $('#kode_prod').val(),
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
              refresh_procurement();
              $('#btn-simpan').button('reset');
              unblockUI( function() {
                setTimeout(function() { alert_notify(data.icon,data.message,data.type); }, 1000);
              });
              document.getElementById(data.field).focus();
            }else{
              //jika berhasil disimpan/diubah
              refresh_procurement();
              unblockUI( function() {
                setTimeout(function() { alert_notify(data.icon,data.message,data.type); }, 1000);
              });
              $('#btn-simpan').button('reset');
            }

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
    
   //klik button Batal
    $('#btn-cancel').click(function(){
       $("#ref_warehouse").load(location.href + " #ref_warehouse");
    });

</script>


</body>
</html>
