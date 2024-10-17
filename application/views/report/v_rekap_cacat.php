
<!DOCTYPE html>
<html lang="en">
<head>
  <?php $this->load->view("admin/_partials/head.php") ?>
  <link rel="stylesheet" type="text/css" href="<?php echo base_url('dist/css/tableScroll.css') ?>">
  <style type="text/css">

    #pagination {
      display: inline-block;
      padding-left: 0;
      border-radius: 4px;
      padding-top: 5px;

    } 
    #pagination>a, #pagination>strong {
      position: relative;
      float: left;
      padding: 4px 8px;
      margin-left: -1px;
      line-height: 1.42857143;
      color: #337ab7;
      text-decoration: none;
      background-color: #fff;
      border: 1px solid #ddd;
    }

    #pagination2>a, #pagination2>strong {
      position: relative;
      float: left;
      padding: 4px 8px;
      margin-left: -1px;
      line-height: 1.42857143;
      color: #337ab7;
      text-decoration: none;
      background-color: #fff;
      border: 1px solid #ddd;
    }
    
    h3{
      display: block !important;
      text-align: center !important;
    }

    .divListviewHead table  {
      display: block;
      height: calc( 100vh - 250px );
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
          <h3 class="box-title"><b>Rekap Cacat</b></h3>
        </div>
        <div class="box-body">
           
            <form name="input" class="form-horizontal" role="form" method="POST" id="frm_periode">
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
                        <select type="text" class="form-control input-sm" name="departemen" id="departemen" required=""  >
                        </select>
                      </div>                                    
                      <div class="col-md-6 panel-heading" role="tab" id="advanced" style="padding:0px 0px 0px 15px;  ">
                          <label data-toggle="collapse" href="#advancedSearch" aria-expanded="false" aria-controls="advancedSearch" class='collapsed' style='cursor:pointer;'>
                              <i class="showAdvanced glyphicon glyphicon-triangle-bottom"></i>
                               Advanced 
                          </label>
                      </div>
                  </div>
                </div>               
         
              </div>
           
              <div class="col-md-4">
                <button type="button" class="btn btn-sm btn-default" name="btn-generate" id="btn-generate" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing..."> Generate</button>
                <button type="button" class="btn btn-sm btn-default" name="btn-excel" id="btn-excel" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing..." > <i class="fa fa-file-excel-o" style="color:green"></i> Excel</button>
              </div>
              <br>
              <div class="col-md-12">
                   <div class="panel panel-default" style="margin-bottom: 0px;">
                    <div id="advancedSearch" class="panel-collapse collapse" role="tabpanel" aria-labelledby="advanced" >
                      <div class="panel-body" style="padding: 5px">
                        <div class="col-md-4" >
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
                              <label>Produk / Corak </label>
                            </div>
                            <div class="col-md-7">
                                <input type="text" class="form-control input-sm" name="corak" id="corak" >
                            </div>
                          </div>
                        </div>
                        <div class="col-md-4">
                          <div class="form-group">
                            <div class="col-md-5">
                              <label>No Mesin </label>
                            </div>
                            <div class="col-md-7">
                                <input type="text" class="form-control input-sm" name="mc" id="mc" >
                            </div>
                          </div>
                          <div class="form-group">
                            <div class="col-md-5">
                              <label>User </label>
                            </div>
                            <div class="col-md-7">
                                <input type="text" class="form-control input-sm" name="user" id="user" >
                            </div>
                          </div>
                        </div>
                        <div class="col-md-4">
                          <div class="form-group">
                            <div class="col-md-5">
                              <label>Grade </label>
                            </div>
                            <div class="col-md-7">
                                  <select type="text" class="form-control input-sm select2" name="grade" id="grade" style="width:100% !important" multiple="">
                                    <!-- <option>All</option> -->
                                    <option>A</option>
                                    <option>B</option>
                                    <option>C</option>
                                    <option>F</option>
                                  </select>
                            </div>
                          </div>
                          <div class="form-group">
                             <div class="col-md-12">
                              <label><input type="checkbox" name="show" value="show_all"> Tampilkan Semua HPH </label>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
              </div>
            </form>

            <!-- table -->
            <div class="box-body">
                <div class="form-group">
                    <div class="col-md-6">
                        <label>
                            <div id='total_record'>Total Data : 0</div>
                        </label>
                    </div>
                    <div class="col-md-6">
                        <div class="pull-right text-right">
                          <div id='pagination'></div>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 table-responsive example1 divListviewHead">
                  <div role="region" aria-labelledby="HeadersCol" tabindex="0" class="rowheaders">
                      <table id="example1" class="table table-condesed table-hover" border="0">
                            <thead>
                              <tr>
                                <th  class="style bb ws no"  >No. </th>
                                <th  class='style bb ws' >MO</th>
                                <th  class='style bb ws' style="min-width: 120px">No Mesin</th>
                                <th  class='style bb ws' >SC</th>
                                <th  class='style bb ws' style="min-width: 80px">Tgl HPH</th>
                                <th  class='style bb ws' >kode Produk</th>
                                <th  class='style bb ws'  style="min-width: 150px">Nama Produk</th>
                                <th  class='style bb ws' >Lot</th>
                                <th  class='style bb ws' >Qty1</th>
                                <th  class='style bb ws' >Uom1</th>
                                <th  class='style bb ws' >Qty2</th>
                                <th  class='style bb ws' >Uom2</th>
                                <th  class='style bb ws' >Grade</th>
                                <th  class='style bb ws' >Point Cacat</th>
                                <th  class='style bb ws' >Kode Cacat</th>
                                <th  class='style bb ws' style="min-width: 120px" >Nama cacat</th>
                                <th  class='style bb ws' style="min-width: 100px" >Nama User</th>
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

  $('.select2').select2({});
  
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
  $("#btn-generate").on('click', function(e){

      e.preventDefault();

      tgldari   = $('#tgldari').val();
      tglsampai = $('#tglsampai').val();
      id_dept   = $('#departemen').val();
      corak     = $('#corak').val();
      mc        = $('#mc').val();
      lot       = $('#lot').val();
      user      = $('#user').val();
      jenis     = $('#jenis').val();      
      grade     = $('#grade').val();      
      show_hph  = $('input[name="show"]').is(":checked");

      if(tgldari == '' || tglsampai == ''){

        alert_modal_warning('Periode Tanggal Harus diisi !');

      }else if(id_dept == null){
        alert_modal_warning('Departemen Harus diisi !');
      }else{  
          $("#example1_processing").css('display',''); // show loading

          $('#btn-generate').button('loading');
          $("#example1 tbody").remove();
          $.ajax({
                type: "POST",
                dataType : "JSON",
                url : "<?php echo site_url('report/rekapcacat/loadData')?>",
                data: {tgldari:tgldari, tglsampai:tglsampai, id_dept:id_dept, corak:corak, mc:mc, lot:lot, user:user, grade:grade, show_hph:show_hph },
                success: function(data){
                  $('#total_record').html(data.total_record);
                  $('#pagination').html(data.pagination);
                  let tbody = $("<tbody />");
                  let no    = 0;
                  let empty = true;

                  $.each(data.record, function(key, value){
                      if(value.kode == ''){
                          num = '';
                      }else{
                          no=no+1;
                          num = no;
                      }
                      empty = false;
                      var tr = $("<tr>").append(
                               $("<td>").text(num),
                               $("<td>").text(value.kode),
                               $("<td>").text(value.nama_mesin),
                               $("<td>").text(value.sc),
                               $("<td>").text(value.tgl_hph),
                               $("<td>").text(value.kode_produk),
                               $("<td>").text(value.nama_produk),
                               $("<td>").text(value.lot),
                               $("<td>").text(value.qty1),
                               $("<td>").text(value.uom1),
                               $("<td>").text(value.qty2),
                               $("<td>").text(value.uom2),
                               $("<td>").text(value.grade),
                               $("<td>").text(value.point_cacat),
                               $("<td>").text(value.kode_cacat),
                               $("<td>").text(value.nama_cacat),
                               $("<td>").text(value.nama_user),
                      );
                      tbody.append(tr);
                  });
                if(empty == true){
                  var tr = $("<tr>").append($("<td colspan='16'>").text('Tidak ada Data'));
                  tbody.append(tr);
                }
                $("#example1").append(tbody);

                $('#btn-generate').button('reset');
                $("#example1_processing").css('display','none'); // hidden loading

                },error : function(jqXHR, textStatus, errorThrown){
                  alert(jqXHR.responseText);
                  //alert('error data');
                  $('#btn-generate').button('reset');
                }
          });
      }
      e.stopImmediatePropagation();
  });

  //jika next page ,isi akan rubah lagi
  $('#pagination').on('click','a',function(e){
      
      e.preventDefault(); 
      var pageNum = $(this).attr('data-ci-pagination-page');
      createBody(pageNum)
  });

  function createBody(pageNum){

      please_wait(function(){});
      $("#example1_processing").css('display',''); // show loading
      $("#example1 tbody").remove();
      tgldari   = $('#tgldari').val();
      tglsampai = $('#tglsampai').val();
      id_dept   = $('#departemen').val();
      corak     = $('#corak').val();
      mc        = $('#mc').val();
      lot       = $('#lot').val();
      user      = $('#user').val();
      jenis     = $('#jenis').val();      
      grade     = $('#grade').val();    
      show_hph  = $('input[name="show"]').is(":checked");

      $.ajax({
        type : 'POST',
        dataType: 'json',
        url  : '<?=base_url()?>report/rekapcacat/loadData/'+pageNum,
        data: {tgldari:tgldari, tglsampai:tglsampai, id_dept:id_dept, corak:corak, mc:mc, lot:lot, user:user, grade:grade,show_hph:show_hph },
        success: function(data){
          $('#pagination').html(data.pagination);
          $('#total_record').html(data.total_record);

          var tbody = $("<tbody id='0'/>");
          var no    = 0;
          var empty = true;
          $.each(data.record, function(key, value) {
            if(value.kode == ''){
              num = '';
            }else{
              no=no+1;
              num = no;
            }
            var tr = $("<tr>").append(
                               $("<td>").text(num),
                               $("<td>").text(value.kode),
                               $("<td>").text(value.nama_mesin),
                               $("<td>").text(value.sc),
                               $("<td>").text(value.tgl_hph),
                               $("<td>").text(value.kode_produk),
                               $("<td>").text(value.nama_produk),
                               $("<td>").text(value.lot),
                               $("<td>").text(value.qty1),
                               $("<td>").text(value.uom1),
                               $("<td>").text(value.qty2),
                               $("<td>").text(value.uom2),
                               $("<td>").text(value.grade),
                               $("<td>").text(value.point_cacat),
                               $("<td>").text(value.kode_cacat),
                               $("<td>").text(value.nama_cacat),
                               $("<td>").text(value.nama_user),
                      );
              tbody.append(tr);

          });

          if(empty == true){
              var tr = $("<tr>").append($("<td colspan='16'>").text('Tidak ada Data'));
              tbody.append(tr);
          }
          $("#example1").append(tbody);
          $("#example1_processing").css('display','none');
          unblockUI( function() {});
        },error: function (jqXHR, textStatus, errorThrown){
          alert('error load data');
          $("#example1_processing").css('display','none');
          unblockUI( function() {});
        }
      });
  }


   // btn generate
  $("#btn-excel").on('click', function(e){

      e.preventDefault();

      tgldari   = $('#tgldari').val();
      tglsampai = $('#tglsampai').val();
      id_dept   = $('#departemen').val();
      corak     = $('#corak').val();
      mc        = $('#mc').val();
      lot       = $('#lot').val();
      user      = $('#user').val();
      jenis     = $('#jenis').val();      
      grade     = $('#grade').val();      
      show_hph  = $('input[name="show"]').is(":checked");

      if(tgldari == '' || tglsampai == ''){
        alert_modal_warning('Periode Tanggal Harus diisi !');

      }else if(id_dept == null){
        alert_modal_warning('Departemen Harus diisi !');
      }else{   

        $.ajax({
          "type":'POST',
          "url" : "<?php echo site_url('report/rekapcacat/export_excel')?>",
          "data": {tgldari:tgldari, tglsampai:tglsampai, id_dept:id_dept, corak:corak, mc:mc, lot:lot, user:user, grade:grade, show_hph:show_hph },
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

</script>

</body>
</html>
