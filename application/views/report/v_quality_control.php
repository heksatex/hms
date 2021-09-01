
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
      height: calc( 90vh - 250px );
      overflow-x: auto;
    }

    .btn-setTgl{
      height: 22px;
      min-width:  40px;
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
          <h3 class="box-title"><b>Quality Control [QC]</b></h3>
        </div>
        <div class="box-body">
           
            <form name="input" class="form-horizontal" role="form" method="POST" id="frm_periode" action="<?=base_url()?>report/Qualitycontrol/export_excel">
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
                    <div class="col-md-2"></div>
                    <div class="col-md-1 col-xs-2">
                      <button type="button" class="btn btn-default btn-xs btn-setTgl"  name="btn_h" onclick="setTgl('h');" >H</buton>
                    </div> 
                    <div class="col-md-1 col-xs-2">
                      <button type="button" class="btn btn-default btn-xs btn-setTgl"  name="btn_h1" onclick="setTgl('h1');" >H.1</buton>
                    </div>
                    <div class="col-md-1 col-xs-2">
                      <button type="button" class="btn btn-default btn-xs btn-setTgl "  name="btn_h-7" onclick="setTgl('h-7');" >H-7</buton>
                    </div>
                    <div class="col-md-1 col-xs-2">
                      <button type="button" class="btn btn-default btn-xs btn-setTgl "  name="btn_h-30" onclick="setTgl('h-30');" >H-30</buton>
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
                  </div>
                </div>
               
              </div>
              <div class="col-md-4">
                <button type="button" class="btn btn-sm btn-default" name="btn-generate" id="btn-generate" >Generate</button>
                <button type="submit" class="btn btn-sm btn-default" name="btn-generate" id="btn-excel" > <i class="fa fa-file-excel-o"></i> Excel</button>
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
                              <th  class="style no"  rowspan="2">No. </th>
                              <th  class='style' rowspan="2" style="min-width: 100px">Mesin</th>
                              <th  class='style' rowspan="2" style="min-width: 150px">Produk/Corak</th>
                              <th  class='style' rowspan="2" style="width: 5px; word-wrap: break-word; text-align: center;">Standar Mtr</th>
                              <th  class='style' rowspan="2" style="width: 5px; word-wrap: break-word; text-align: center;">Standar Kg</th>
                              <th  class='style' rowspan="2" style="text-align: center;">RPM</th>
                              <th  class='style' colspan="3" style="text-align: center;">Total Produksi</th>
                              <th  class='style' rowspan="2" style="text-align: center;">Efisensi (%)</th>
                              <th  class='style' colspan="3" style="text-align: center;">Grade</th>
                              <th  class='style' rowspan="2" >Ket</th>

                            </tr>
                            <tr>
                              <th class='style' style="text-align: center;">Mtr</th>
                              <th class='style' style="text-align: center;">Kg</th>
                              <th class='style' style="text-align: center;">Gl</th>
                              <th class='style' style="text-align: center;">A</th>
                              <th class='style' style="text-align: center;">B</th>
                              <th class='style' style="text-align: center;">C</th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr>
                              <td colspan="14" align="center">Tidak ada Data</td>
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

<?php $this->load->view("admin/_partials/js.php"); ?>

<!--script sendiri Global-->
<script type="text/javascript" src="<?php echo site_url('dist/js/myscript.js') ?>"></script>

<script type="text/javascript">

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

      tgldari   = $('#tgldari').val();
      tglsampai = $('#tglsampai').val();
      id_dept   = $('#departemen').val();

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
                url : "<?php echo site_url('report/qualitycontrol/loadData')?>",
                data: {tgldari:tgldari, tglsampai:tglsampai, id_dept:id_dept},
                success: function(data){

                  let no    = 1;
                  let empty = true;
                  let tbody = $("<tbody />");

                  $.each(data.record, function(key, value){

                    empty = false;
                    
                    // parents (list Mesin-mesin)
                    var tr = $("<tr>").append(
                               $("<td>").html(no),
                               $("<td>").text(value.nama_mesin),
                               $("<td colspan='4'>").text(''),
                               $("<td align='right'>").text(value.hph_mtr),
                               $("<td align='right'>").text(value.hph_kg),
                               $("<td align='right'>").text(value.hph_gl),
                               $("<td align='right'>").text(value.efisisensi),
                               $("<td align='right'>").text(value.grade_A),
                               $("<td align='right'>").text(value.grade_B),
                               $("<td align='right'>").text(value.grade_C),
                               $("<td>").text(''),

                    );
                    if(value.mrp.length > 0){
                      $.each(value.mrp, function(k, v){
                        tr = $("<tr>").append(
                               $("<td>").html(no++),
                               $("<td>").text(v.nama_mesin),
                               $("<td>").text(v.nama_produk),
                               $("<td colspan='3'>").text(''),
                               $("<td align='right'>").text(v.hph_mtr),
                               $("<td align='right'>").text(v.hph_kg),
                               $("<td align='right'>").text(v.hph_gl),
                               $("<td align='right'>").text(v.efisisensi),
                               $("<td align='right'>").text(v.grade_A),
                               $("<td align='right'>").text(v.grade_B),
                               $("<td align='right'>").text(v.grade_C),
                               $("<td>").text(''),

                        );
                        tbody.append(tr);
                      })
                    }else{
                      no++;
                    }

                    tbody.append(tr);
                    $("#example1").append(tbody); // append parents
                     
                  });

                if(empty == true){
                  var tr = $("<tr>").append($("<td colspan='12' align='center'>").text('Tidak ada Data'));
                  tbody.append(tr);
                }
                //$("#example1").append(tbody);

                $('#btn-generate').button('reset');
                $("#example1_processing").css('display','none'); // hidden loading

                },error : function(jqXHR, textStatus, errorThrown){
                  alert(jqXHR.responseText);
                  //alert('error data');
                  $('#btn-generate').button('reset');
                }
          });
      }
  });


</script>

</body>
</html>
