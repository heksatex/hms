
<!DOCTYPE html>
<html lang="en">
<head>
  <?php $this->load->view("admin/_partials/head.php") ?>
  <style type="text/css">
    
    h3{
      display: block !important;
      text-align: center !important;
    }

    .divListviewHead table  {
      display: block;
      height: calc( 100vh - 250px );
      overflow-x: auto;
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
           
            <form name="input" class="form-horizontal" role="form" method="POST" id="frm_periode" action="<?=base_url()?>report/rekapcacat/export_excel">
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
                <button type="button" class="btn btn-sm btn-default" name="btn-generate" id="btn-generate" >Generate</button>
                <button type="submit" class="btn btn-sm btn-default" name="btn-generate" id="btn-excel" > <i class="fa fa-file-excel-o"></i> Excel</button>
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
                              <label>Corak </label>
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
                                <select class="form-control input-sm grade" name="grade" id="grade"><option value="All">All</option><?php foreach($list_grade as $row){ echo "<option>".$row->nama_grade."</option>";}?></select>  
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
            <div class="col-sm-12 table-responsive">
              <div class="table_scroll">
                <div class="table_scroll_head">
                  <div class="divListviewHead">
                      <table id="example1" class="table" border="0">
                          <thead>
                            <tr>
                              <th  class="style no"  >No. </th>
                              <th  class='style' >MO</th>
                              <th  class='style' style="min-width: 120px">No Mesin</th>
                              <th  class='style' >SC</th>
                              <th  class='style' style="min-width: 80px">Tgl HPH</th>
                              <th  class='style' >kode Produk</th>
                              <th  class='style'  style="min-width: 150px">Nama Produk</th>
                              <th  class='style' >Lot</th>
                              <th  class='style' >Qty1</th>
                              <th  class='style' >Uom1</th>
                              <th  class='style' >Qty2</th>
                              <th  class='style' >Uom2</th>
                              <th  class='style' >Grade</th>
                              <th  class='style' >Point Cacat</th>
                              <th  class='style' >Kode Cacat</th>
                              <th  class='style' style="min-width: 120px" >Nama cacat</th>
                              <th  class='style' style="min-width: 100px" >Nama User</th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr>
                              <td colspan="16" align="center">Tidak ada Data</td>
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
                data: {tgldari:tgldari, tglsampai:tglsampai, id_dept:id_dept, corak:corak, mc:mc, lot:lot, user:user, grade:grade },
                success: function(data){

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
                  var tr = $("<tr>").append($("<td colspan='16' align='center'>").text('Tidak ada Data'));
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


</script>

</body>
</html>
