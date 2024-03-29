
<!DOCTYPE html>
<html lang="en">
<head>
  <?php $this->load->view("admin/_partials/head.php") ?>
  <link rel="stylesheet" type="text/css" href="<?php echo base_url('dist/css/tableScroll.css') ?>">
  <style type="text/css">
    
    h3{
      display: block !important;
      text-align: center !important;
    }

    .divListviewHead table  {
      display: block;
      max-height: calc( 100vh - 250px );
      overflow-x: auto;
    }

    .ws{
      white-space: nowrap;
    }

  </style>
</head>

<body class="hold-transition skin-black fixed sidebar-mini">
<!-- Site wrapper -->
<div class="wrapper">
  <!-- main -header -->
  <header class="main-header">
   <?php $this->load->view("admin/_partials/main-menu.php") ?>
   <?php $this->load->view("admin/_partials/topbar.php") ?>
  </header>

  <!-- Menu Side Bar -->
  <aside class="main-sidebar">
  <?php $this->load->view("admin/_partials/sidebar.php") ?>
  </aside>

  <!-- Content Wrapper-->
  <div class="content-wrapper">
    <!-- Content Header (Status - Bar) -->
    <section class="content-header">
    </section>

    <!-- Main content -->
    <section class="content">
      <!--  box content -->
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title"><b>Adjustment</b></h3>
        </div>
        <div class="box-body">
           
            <form name="input" class="form-horizontal" role="form" method="POST" id="frm_periode" >
              <div class="col-md-8">
                <div class="form-group">
                  <div class="col-md-12"> 
                    <div class="col-md-2">
                      <label>Tanggal </label>
                    </div>
                    <div class="col-md-4">
                      <div class='input-group'>
                        <input type="text" class="form-control input-sm" name="tgldari" id="tgldari" required="">
                        <span class="input-group-addon">
                          <span class="glyphicon glyphicon-calendar"></span>
                        </span>    
                      </div>
                    </div>
                    <div class="col-md-1">
                        <label>s/d</label>
                    </div>
                    <div class="col-md-4">
                      <div class='input-group'>
                        <input type="text" class="form-control input-sm" name="tglsampai" id='tglsampai' required="">
                        <span class="input-group-addon">
                          <span class="glyphicon glyphicon-calendar"></span>
                        </span>    
                      </div>
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <div class="col-md-12">
                    <div class="col-md-2"><label>Departemen</label></div>
                    <div class="col-md-4">
                        <select type="text" class="form-control input-sm" name="departemen" id="departemen" required="" style="width:100% !important" >
                        </select>
                      </div>                                    
                      <div class="col-md-1"></div>
                      <div class="col-md-4 panel-heading" role="tab" id="advanced" style="padding:0px 0px 0px 15px;  ">
                        <div data-toggle="collapse" href="#advancedSearch" aria-expanded="false" aria-controls="advancedSearch" class='collapsed'>
                          <label style="cursor:pointer;">
                            <i class="showAdvanced glyphicon glyphicon-triangle-bottom"></i>
                             Advanced 
                          </label>
                        </div>
                      </div>
                  </div>
                </div>
               
               
              </div>
              <div class="col-md-4">
                <button type="button" class="btn btn-sm btn-default" name="btn-generate" id="btn-generate" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing..."> Generate</button>
                <button type="button" class="btn btn-sm btn-default" name="btn-excel" id="btn-excel" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing..."> <i class="fa fa-file-excel-o" style="color:green"></i> Excel</button>
              </div>

              <br>
              <div class="col-md-12">
                    <div class="panel panel-default" style="margin-bottom: 0px;">
                      <div id="advancedSearch" class="panel-collapse collapse" role="tabpanel" aria-labelledby="advanced" >
                        <div class="panel-body" style="padding: 5px">
                          <div class="form-group col-md-12" style="margin-bottom:0px">
                            <div class="col-md-4" >
                              <div class="form-group">
                                <div class="col-md-5">
                                  <label>Kode Adjustment </label>
                                </div>
                                <div class="col-md-7">
                                    <input type="text" class="form-control input-sm" name="kode_adjustment" id="kode_adjustment" >
                                </div>
                              </div>
                              <div class="form-group">
                                <div class="col-md-5">
                                  <label>Lot </label>
                                </div>
                                <div class="col-md-7">
                                    <input type="text" class="form-control input-sm" name="lot" id="lot" >
                                </div>
                              </div> 
                              <div class="form-group">
                                <div class="col-md-5">
                                  <label>Nama Produk </label>
                                </div>
                                <div class="col-md-7">
                                    <input type="text" class="form-control input-sm" name="nama_produk" id="nama_produk">
                                </div>
                              </div>
                            </div>
                            <div class="col-md-4">
                              <div class="form-group">
                                <div class="col-md-5">
                                  <label>Type Adjustment </label>
                                </div>
                                <div class="col-md-7">
                                    <select type="text" class="form-control input-sm" name="type_adjustment" id="type_adjustment"  style="width:100% !important"> 
                                    <option value="">-- Pilih Type --</option>
                                    <?php 
                                      foreach ($type as $val) {
                                          echo "<option value='".$val->id."'>".$val->name_type."</option>";
                                      }
                                    ?>
                                    </select>
                                </div>
                              </div>
                              <div class="form-group">
                                <div class="col-md-5">
                                  <label>User</label>
                                </div>
                                <div class="col-md-7">
                                    <input type="text" class="form-control input-sm" name="user" id="user" >
                                </div>
                              </div>
                              <div class="form-group">
                                <div class="col-md-5">
                                  <label>Notes </label>
                                </div>
                                <div class="col-md-7">
                                    <input type="text" class="form-control input-sm" name="notes" id="notes" >
                                </div>
                              </div>
                            </div>
                            <div class="col-md-4">
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
              </div>
            </form>
            <!-- table ADJ IN  -->
            <div class="box-body">
                <div class="form-group">
                    <div class="col-md-2">
                          <label>Adjustment IN</label>
                    </div>
                    <div class="col-xs-5 col-md-10" id="total_lot"><label>Total Lot : 0</label></div>
                </div>
                <div class="col-xs-12 table-responsive example1 divListviewHead">
                  <div role="region" aria-labelledby="HeadersCol" tabindex="0" class="rowheaders">
                      <table id="example1" class="table table-condesed table-hover" border="0">
                            <thead>
                              <tr>
                                <th  class="style no"  >No. </th>
                                <th  class='style bb ws' style="min-width: 80px">Kode Adjustment</th>
                                <th  class='style bb ws' style="min-width: 100px">Type Adjustment</th>
                                <th  class='style bb ws' style="min-width: 80px">Tanggal</th>
                                <th  class='style bb ws' style="min-width: 150px">Nama Produk</th>
                                <th  class='style bb ws' style="min-width: 80px">Lot</th>
                                <th  class='style bb ws' >Qty Stock</th>
                                <th  class='style bb ws' >Qty Adj</th>
                                <th  class='style bb ws' >UoM</th>
                                <th  class='style bb ws' >Qty2 Stock</th>
                                <th  class='style bb ws' >Qty2 Adj</th>
                                <th  class='style bb ws' >UoM2</th>
                                <th  class='style bb ws' >Qty Move</th>
                                <th  class='style bb ws' >Qty Move2</th>
                                <th  class='style bb ws' style="min-width: 80px">User</th>
                                <th  class='style bb ws' style="min-width: 100px">Notes</th>
                              </tr>
                            </thead>
                            <tbody>
                              <tr>
                                <td colspan="16" >Tidak ada Data</td>
                              </tr>
                            </tbody>
                        </table>
                        <div id="example1_processing" class="table_processing" style="display: none">
                          Processing...
                        </div>
                  </div>
                </div>
            </div>

             <!-- table ADJ OUT  -->
             <div class="box-body">
                <div class="form-group">
                    <div class="col-md-2">
                          <label>Adjustment OUT</label>
                    </div>
                    <div class="col-xs-5 col-md-10" id="total_lot2"><label>Total Lot : 0</label></div>
                </div>
                <div class="col-xs-12 table-responsive example1 divListviewHead">
                  <div role="region" aria-labelledby="HeadersCol" tabindex="0" class="rowheaders">
                          <table id="example2" class="table table-condesed table-hover" border="0">
                            <thead>
                              <tr>
                                <th  class="style bb ws no"  >No. </th>
                                <th  class='style bb ws' style="min-width: 80px">Kode Adjustment</th>
                                <th  class='style bb ws' style="min-width: 100px">Type Adjustment</th>
                                <th  class='style bb ws' style="min-width: 80px">Tanggal</th>
                                <th  class='style bb ws' style="min-width: 150px">Nama Produk</th>
                                <th  class='style bb ws' style="min-width: 80px">Lot</th>
                                <th  class='style bb ws' >Qty Stock</th>
                                <th  class='style bb ws' >Qty Adj</th>
                                <th  class='style bb ws' >UoM</th>
                                <th  class='style bb ws' >Qty2 Stock</th>
                                <th  class='style bb ws' >Qty2 Adj</th>
                                <th  class='style bb ws' >UoM2</th>
                                <th  class='style bb ws' >Qty Move</th>
                                <th  class='style bb ws' >Qty Move2</th>
                                <th  class='style bb ws' style="min-width: 80px">User</th>
                                <th  class='style bb ws' style="min-width: 100px">Notes</th>
                              </tr>
                            </thead>
                            <tbody>
                              <tr>
                                <td colspan="16" >Tidak ada Data</td>
                              </tr>
                            </tbody>
                        </table>
                        <div id="example2_processing" class="table_processing" style="display: none">
                          Processing...
                        </div>
                  </div>
                </div>
              </div>
        </div>
        <!-- /.box-body -->
      </div>
      <!-- /.box -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

</div>

<?php $this->load->view("admin/_partials/js.php") ?>

<script type="text/javascript">

  //* Show collapse advanced search
  $('#advancedSearch').on('shown.bs.collapse', function () {
      $(".showAdvanced").removeClass("glyphicon-triangle-bottom").addClass("glyphicon-triangle-top");
  });

  //* Hide collapse advanced search
  $('#advancedSearch').on('hidden.bs.collapse', function () {
    $(".showAdvanced").removeClass("glyphicon-triangle-top").addClass("glyphicon-triangle-bottom");
  });


  // array tmp
  var tmp_filter = [];

  // set date tgldari
  $('#tgldari').datetimepicker({
      defaultDate : new Date(),
      format : 'D-MMMM-YYYY',
      ignoreReadonly: true
  });

  // set date tglsampai
  $('#tglsampai').datetimepicker({
      defaultDate : new Date(),
      format : 'D-MMMM-YYYY',
      ignoreReadonly: true
  });

  //select 2 Departementy
  $('#departemen').select2({
      allowClear: true,
      placeholder: "Select Departemen",
      ajax:{
            dataType: 'JSON',
            type : "POST",
            url : "<?php echo base_url();?>report/efisiensi/get_departement_select2",
            //delay : 250,
            data : function(params){
              return{
                nama:params.term,
              };
            }, 
            processResults:function(data){
              var results = [];
              $.each(data, function(index,item){
                results.push({
                    id:item.kode,
                    text:item.nama
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

  // btn generate
  $("#btn-generate").on('click', function(){
      tmp_filter  = [];
      tgldari   = $('#tgldari').val();
      tglsampai = $('#tglsampai').val();
      id_dept   = $('#departemen').val();
      kode_adjustment   = $('#kode_adjustment').val();
      lot       = $('#lot').val();
      nama_produk   = $('#nama_produk').val();
      type_adjustment   = $('#type_adjustment').val();
      user      = $('#user').val();
      notes     = $('#notes').val();

      if(tgldari == '' || tglsampai == ''){
        alert_modal_warning('Periode Tanggal Harus diisi !');

      }else if(id_dept == null){
        alert_modal_warning('Departemen Harus diisi !');

      }else{  
          $("#example1_processing").css('display',''); // show loading
          $("#example2_processing").css('display',''); // show loading

          $('#btn-generate').button('loading');
          $("#example1 tbody").remove();
          $("#example2 tbody").remove();
          $.ajax({
                type: "POST",
                dataType : "JSON",
                url : "<?php echo site_url('report/adjustment/loadData')?>",
                data: {tgldari:tgldari, tglsampai:tglsampai, id_dept:id_dept, load:'header', kode_adjustment:kode_adjustment, lot:lot, nama_produk:nama_produk, type_adjustment:type_adjustment, user:user, notes:notes},
                success: function(data){
                  $("#total_lot").html(data.total_lot_adj_in);;
                  $("#total_lot2").html(data.total_lot_adj_out);;
                  
                  // push data filter ke array tmp_filter
                  tmp_filter.push({'id_dept' : id_dept, 'tgldari' : tgldari, 'tglsampai' : tglsampai, 'kode_adjustment':kode_adjustment, 'lot':lot, 'nama_produk':nama_produk, 'type_adjustment':type_adjustment, 'user':user, 'notes':notes});
                  //alert('check arr '+JSON.stringify(tmp_filter));

                  body_grouping("example1",data.record)// ADJ IN
                  body_grouping("example2",data.record2)// ADJ OUT

                $('#btn-generate').button('reset');
                $("#example1_processing").css('display','none'); // hidden loading
                $("#example2_processing").css('display','none'); // hidden loading

                },error : function(jqXHR, textStatus, errorThrown){
                  alert(jqXHR.responseText);
                  //alert('error data');
                  $("#example1_processing").css('display','none'); // hidden loading
                  $("#example2_processing").css('display','none'); // hidden loading
                  $('#btn-generate').button('reset');
                }
          });
      }
  });


  // btn excel
  $('#btn-excel').click(function(){

    tgldari   = $('#tgldari').val();
    tglsampai = $('#tglsampai').val();
    id_dept   = $('#departemen').val();
    kode_adjustment   = $('#kode_adjustment').val();
    lot       = $('#lot').val();
    nama_produk   = $('#nama_produk').val();
    type_adjustment   = $('#type_adjustment').val();
    user      = $('#user').val();
    notes     = $('#notes').val();

    if(tgldari == '' || tglsampai == ''){
        alert_modal_warning('Periode Tanggal Harus diisi !');
    }else if(id_dept == null){
        alert_modal_warning('Departemen Harus diisi !');
    }else{ 
        $.ajax({
          "type":'POST',
          "url" : "<?php echo site_url('report/adjustment/export_excel')?>",
          "data": {tgldari:tgldari, tglsampai:tglsampai, id_dept:id_dept, kode_adjustment:kode_adjustment, lot:lot, nama_produk:nama_produk, type_adjustment:type_adjustment, user:user, notes:notes}, 
          "dataType":'json',
          beforeSend: function() {
            $('#btn-excel').button('loading');
          },error: function(){
            alert('Error Export Excel');
            $('#btn-excel').button('reset');
          }
      }).done(function(data){
          if(data.status =="failed"){
            alert_modal_warning(data.message);
          }else{
            var $a = $("<a>");
            $a.attr("href",data.file);
            $("body").append($a);
            $a.attr("download",data.filename);
            $a[0].click();
            $a.remove();
          }
          $('#btn-excel').button('reset');
      });

    }

  });

  $(document).on("click", ".group1", function(e){

    var kode      = '';
    var tbody_id  = '';
    var tampil    = false;
    var id_dept = '';
    var tgldari = '';
    var tglsampai = '';
    var kode_adjustment = '';
    var lot = '';
    var nama_produk = '';
    var type_adjustment = '';
    var user = '';
    var notes = '';
    for(i = 0; i < tmp_filter.length; i++){
      id_dept   = tmp_filter[i].id_dept;
      tgldari   = tmp_filter[i].tgldari;
      tglsampai = tmp_filter[i].tglsampai;
      kode_adjustment = tmp_filter[i].kode_adjustment;
      lot       = tmp_filter[i].lot;
      nama_produk = tmp_filter[i].nama_produk;
      type_adjustment = tmp_filter[i].type_adjustment;
      user      = tmp_filter[i].user;
      notes     = tmp_filter[i].notes;
    }

    // ambil data berdasarkan data-content='edit'
    $(this).parents("tr").find("td[data-content='edit']").each(function(){
        data_isi = $(this).attr('data-isi');
        tbody_id = $(this).attr('data-tbody');

        $(this).toggleClass("collapsed collapse"); // ganti collapsed, collapse
        if($(this).hasClass("collapsed") == true){
          tampil = true;
        }else if($(this).hasClass("collapsed") == false){
          tampil = false;
        }
        $(this).find(".glyphicon").toggleClass("glyphicon-plus glyphicon-minus"); // ganti icon + jadi minus

        if(tampil == true){
          $("#example1 tbody[data-parent='"+tbody_id+"']").remove();// remove child by groupby

        }else{

          this_icon = $(this);
          this_icon.html('<i class="fa fa-spinner fa-spin "></i>');
          this_icon.css('pointer-events','none');

          $.ajax({
            type : 'POST',
            dataType: 'json',
            url : "<?php echo site_url('report/adjustment/loadData')?>",
            data : {id_dept:id_dept, tgldari:tgldari, tglsampai:tglsampai, data_isi:data_isi, load:'item', view:'in',  kode_adjustment:kode_adjustment, lot:lot, nama_produk:nama_produk, type_adjustment:type_adjustment, user:user, notes:notes},
            success:function(data){
                
                body_items('example1',data.item,tbody_id);

                // kembalikan icon ke awal
                this_icon.css('pointer-events','');
                this_icon.html('<i class="glyphicon glyphicon-minus "></i>');

            },error : function(jqXHR, textStatus, errorThrown){
              //alert(jqXHR.responseText);
              alert('error load child');
              // kembalikan icon ke awal
              this_icon.css('pointer-events','');
              this_icon.html('<i class="glyphicon glyphicon-minus "></i>');
              
            }

          });


        }

    });

  });

  $(document).on("click", ".group2", function(e){

    var kode      = '';
    var tbody_id  = '';
    var tampil    = false;
    var id_dept = '';
    var tgldari = '';
    var tglsampai = '';
    var kode_adjustment = '';
    var lot = '';
    var nama_produk = '';
    var type_adjustment = '';
    var user = '';
    var notes = '';
    for(i = 0; i < tmp_filter.length; i++){
      id_dept   = tmp_filter[i].id_dept;
      tgldari   = tmp_filter[i].tgldari;
      tglsampai = tmp_filter[i].tglsampai;
      kode_adjustment = tmp_filter[i].kode_adjustment;
      lot       = tmp_filter[i].lot;
      nama_produk = tmp_filter[i].nama_produk;
      type_adjustment = tmp_filter[i].type_adjustment;
      user      = tmp_filter[i].user;
      notes     = tmp_filter[i].notes;
            
    }

    // ambil data berdasarkan data-content='edit'
    $(this).parents("tr").find("td[data-content='edit']").each(function(){
        data_isi = $(this).attr('data-isi');
        tbody_id = $(this).attr('data-tbody');

        $(this).toggleClass("collapsed collapse"); // ganti collapsed, collapse
        if($(this).hasClass("collapsed") == true){
          tampil = true;
        }else if($(this).hasClass("collapsed") == false){
          tampil = false;
        }
        $(this).find(".glyphicon").toggleClass("glyphicon-plus glyphicon-minus"); // ganti icon + jadi minus

        if(tampil == true){
          $("#example2 tbody[data-parent='"+tbody_id+"']").remove();// remove child by groupby

        }else{

          this_icon = $(this);
          this_icon.html('<i class="fa fa-spinner fa-spin "></i>');
          this_icon.css('pointer-events','none');

          $.ajax({
            type : 'POST',
            dataType: 'json',
            url : "<?php echo site_url('report/adjustment/loadData')?>",
            data : {id_dept:id_dept, tgldari:tgldari, tglsampai:tglsampai, data_isi:data_isi, load:'item', view:'out', kode_adjustment:kode_adjustment, lot:lot, nama_produk:nama_produk, type_adjustment:type_adjustment, user:user, notes:notes},
            success:function(data){
                
                body_items('example2',data.item,tbody_id);

                // kembalikan icon ke awal
                this_icon.css('pointer-events','');
                this_icon.html('<i class="glyphicon glyphicon-minus "></i>');

            },error : function(jqXHR, textStatus, errorThrown){
              //alert(jqXHR.responseText);
              alert('error load child');
              // kembalikan icon ke awal
              this_icon.css('pointer-events','');
              this_icon.html('<i class="glyphicon glyphicon-minus "></i>');
              
            }

          });


        }

    });

  });

  function body_grouping(id_table,record){

                  let no    = 1;  
                  let empty = true;
                  let icon  = '';
                  let style = '';
                  var group = 'group-of-rows-';

                  if(id_table == 'example1'){
                    $group_num = 'group1';
                  }else{
                    $group_num = 'group2';
                  }
              
                  $.each(record, function(key, value){
                    empty = false;
                    group = 'group-of-rows-'+no;

                    var tr = $("<tr >").append(
                              $("<td class='show collapsed "+$group_num+"' href='#' style='cursor:pointer;'  data-content='edit' data-isi='"+value.kode_produk+"' data-tbody='"+group+"'>").html("<i class='glyphicon glyphicon-plus' ></i>"),
                               $("<td>").text(''),
                               $("<td>").text(''),
                               $("<td>").text(''),
                               $("<td>").text(value.nama_produk),
                               $("<td>").text(value.tot_lot),
                               $("<td align='right'>").text(value.qty_stock),
                               $("<td align='right'>").text(value.qty),
                               $("<td>").text(value.uom),
                               $("<td align='right'>").text(value.qty2_stock),
                               $("<td align='right'>").text(value.qty2),
                               $("<td>").text(value.uom2),
                               $("<td align='right'>").text(value.qty_move),
                               $("<td align='right'>").text(value.qty_move2),
                    );
                    no++;
                    tbody = $("<tbody id='"+group+"'>").append(tr);
                    $("#"+id_table).append(tbody); // append parents

                  });

                  if(empty == true){
                    var tr = $("<tr>").append($("<td colspan='15' >").text('Tidak ada Data'));
                    tbody = $("<tbody id='"+group+"'>").append(tr);
                    $("#"+id_table).append(tbody); // append parents
                  }

  }

  function body_items(id_table,record,tbody_id){
                let tbody = $("<tbody data-parent='"+tbody_id+"' />");
                let row = '';
                let no  = 1;
                $.each(record, function(key, value) {
                          row +=  "<tr  style='background-color: #f2f2f2;' >";
                          row += "<td>"+no++ +"</td>";
                          row += "<td>"+value.kode_adjustment+"</td>";
                          row += "<td>"+value.type_adjustment+"</td>";
                          row += "<td>"+value.tanggal+"</td>";
                          row += "<td></td>";
                          row += "<td>"+value.lot+"</td>";
                          row += "<td align='right' >"+value.qty_stock+"</td>";
                          row += "<td align='right' >"+value.qty+"</td>";
                          row += "<td>"+value.uom+"</td>";
                          row += "<td align='right'>"+value.qty2_stock+"</td>";
                          row += "<td align='right'>"+value.qty2+"</td>";
                          row += "<td>"+value.uom2+"</td>";
                          row += "<td align='right'>"+value.qty_move+"</td>";
                          row += "<td align='right'>"+value.qty_move2+"</td>";
                          row += "<td>"+value.user+"</td>";
                          row += "<td>"+value.note+"</td>";
                          row += "</tr>";
                });
                tbody.append(row);
                $('#'+id_table+' tbody[id='+tbody_id+']').after(tbody);
                
  }

</script>

</body>
</html>
