
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
          <h3 class="box-title"><b>HPH Finbrushing</b></h3>
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
                    <div class="col-md-2 col-sm-2">
                      <label>Shift </label>
                    </div>
                    <div class="col-xs-4 col-sm-3 col-md-2">
                        <label><input type="checkbox" name="shift[]" value="Pagi"> Pagi </label>
                    </div>
                    <div class="col-xs-4 col-sm-3 col-md-2">
                        <label><input type="checkbox" name="shift[]"  value="Siang"> Siang </label>
                    </div>
                    <div class="col-xs-4 col-sm-3 col-md-2">
                        <label><input type="checkbox" name="shift[]"  value="Malam"> Malam </label>
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <div class="col-md-12">
                    <div class="col-md-4">
                      <label>
                          <div id='total_record'>Total Data : 0</div>
                      </label>
                    </div>
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
                <button type="button" class="btn btn-sm btn-default" name="btn-excel" id="btn-excel" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing..."> <i class="fa fa-file-excel-o"  style="color:green"></i> Excel</button>
              </div>
              <br>
              <div class="col-md-12">
                   <div class="panel panel-default" style="margin-bottom: 0px;">
                    <div id="advancedSearch" class="panel-collapse collapse" role="tabpanel" aria-labelledby="advanced" >
                      <div class="panel-body" style="padding: 5px">
                        <div class="col-md-4" >
                          <div class="form-group">
                            <div class="col-md-5">
                              <label>MG </label>
                            </div>
                            <div class="col-md-7">
                                <input type="text" class="form-control input-sm" name="mo" id="mo" >
                            </div>
                          </div>
                          <div class="form-group">
                            <div class="col-md-5">
                              <label>No Go </label>
                            </div>
                            <div class="col-md-7">
                                <input type="text" class="form-control input-sm" name="no_go" id="no_go"  placeholder="No. Greige Out">
                            </div>
                          </div>
                          <div class="form-group">
                            <div class="col-md-5">
                              <label>Corak </label>
                            </div>
                            <div class="col-md-7">
                                <input type="text" class="form-control input-sm" name="corak" id="corak" placeholder="Corak / Nama Produk">
                            </div>
                          </div>
                          <div class="form-group">
                            <div class="col-md-5">
                              <label>Warna </label>
                            </div>
                            <div class="col-md-7">
                                <input type="text" class="form-control input-sm" name="warna" id="warna" >
                            </div>
                          </div> 
                        </div>
                        <div class="col-md-4">
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
                              <label>No Mesin </label>
                            </div>
                            <div class="col-md-7">
                                <!--input type="text" class="form-control input-sm" name="mc" id="mc" -->
                                <select type="text" class="form-control input-sm" name="mc" id="mc"  style="width:100% !important"> 
                                <option value="">-- Pilih No Mesin --</option>
                                <?php 
                                  foreach ($mesin as $val) {
                                      echo "<option value='".$val->nama_mesin."'>".$val->nama_mesin."</option>";
                                  }
                                ?>
                                </select>
                            </div>
                          </div>
                          <div class="form-group">
                              <div class="col-md-5">
                                <label>Sales Contract </label>
                              </div>
                              <div class="col-md-7">
                                  <input type="text" class="form-control input-sm" name="sales_order" id="sales_order" >
                              </div>
                            </div>
                            <div class="form-group">
                              <div class="col-md-5">
                                <label>Marketing </label>
                              </div>
                              <div class="col-md-7">
                                  <select type="text" class="form-control input-sm" name="sales_group" id="sales_group"  style="width:100% !important"> 
                                    <option value="">-- Pilih Marketing --</option>
                                    <?php 
                                      foreach ($mst_sales_group as $val) {
                                          echo "<option value='".$val->kode_sales_group."'>".$val->nama_sales_group."</option>";
                                      }
                                    ?>
                                  </select>
                              </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                          <div class="form-group">
                            <div class="col-md-5">
                              <label>User </label>
                            </div>
                            <div class="col-md-7">
                                <input type="text" class="form-control input-sm" name="user" id="user" >
                            </div>
                          </div>
                          <div class="form-group">
                            <div class="col-md-5">
                              <label>Jenis </label>
                            </div>
                            <div class="col-md-7">
                                <select type="text" class="form-control input-sm" name="jenis" id="jenis" >
                                  <option>All</option>
                                  <option>HPH</option>
                                  <option>Waste</option>
                                </select>
                            </div>
                          </div>
                          <div class="form-group">
                              <div class="col-md-5">
                                <label>Reproses </label>
                              </div>
                              <div class="col-xs-4 col-sm-3 col-md-3">
                                  <label><input type="checkbox" name="reproses[]" value="reproses" checked>  </label>
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
                <div class="col-xs-12 table-responsive example1 divListviewHead">
                    <div role="region" aria-labelledby="HeadersCol" tabindex="0" class="rowheaders">
                      <table id="example1" class="table table-condesed table-hover" border="0">
                          <thead>
                            <tr>
                              <th  class="style bb ws no" >No. </th>
                              <th  class='style bb ws' style="min-width: 80px">Tgl HPH</th>
                              <th  class='style bb ws'>Kode Produk</th>
                              <th  class='style bb ws' style="min-width: 200px">Nama Produk</th>
                              <th  class='style bb ws' style="min-width: 200px">Warna</th>
                              <th  class='style bb ws'>SC</th>
                              <th  class='style bb ws'>MG</th>
                              <th  class='style bb ws'>No Greige Out </th>
                              <th  class='style bb ws' style="min-width: 100px">No Mesin</th>
                              <th  class='style bb ws' style="min-width: 150px">Lot</th>
                              <th  class='style bb ws'>GL</th>
                              <th  class='style bb ws'>Qty1</th>
                              <th  class='style bb ws'>Uom1</th>
                              <th  class='style bb ws'>Qty2</th>
                              <th  class='style bb ws'>Uom2</th>
                              <th  class='style bb ws'>Grade</th>
                              <th  class='style bb ws'>L.Greige</th>
                              <th  class='style bb ws'>L.Jadi</th>
                              <th  class='style bb ws'>Marketing</th>
                              <th  class='style bb ws'>Reff Note </th>
                              <th  class='style bb ws'>Lokasi </th>
                              <th  class='style bb ws' style="min-width: 100px" >Nama User</th>
                              <th  class='style bb ws'>Keterangan </th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr>
                              <td colspan="22" >Tidak ada Data</td>
                            </tr>
                          </tbody>
                      </table>
                      <div id="example1_processing" class="table_processing" style="display: none; z-index:5;">
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

  // select 2 mesin
  $('#mc').select2({});

  var d     = new Date();
  var month = d.getMonth();
  var day   = d.getDate();
  var year  = d.getFullYear();

  // set date tgldari
  $('#tgldari').datetimepicker({
      defaultDate : new Date(year, month, day, 0o0, 0o0, 0o0),
      format : 'D-MMMM-YYYY HH:mm:ss',
      ignoreReadonly: true,
      maxDate: new Date(),
  });

  // set date tglsampai
  $('#tglsampai').datetimepicker({
      defaultDate : new Date(year, month, day, 23, 59, 59),
      format : 'D-MMMM-YYYY HH:mm:ss',
      ignoreReadonly: true,
      maxDate: new Date(year, month, day, 23, 59, 59),
  });

   // disable enter
   $(window).keydown(function(event){
    if(event.keyCode == 13) {
      event.preventDefault();
      return false;
    }
  });

  // cek selisih satu submit excel
  $('#btn-excel').click(function(){

    tgldari   = $('#tgldari').val();
    tglsampai = $('#tglsampai').val();
    mo        = $('#mo').val();
    corak     = $('#corak').val();
    mc        = $('#mc').val();
    lot       = $('#lot').val();
    user      = $('#user').val();
    jenis     = $('#jenis').val();
    sales_order     = $('#sales_order').val();
    sales_group     = $('#sales_group').val();
    no_go           = $('#no_go').val();
    warna           = $('#warna').val();
    tgldari_2 = $('#tgldari').data("DateTimePicker").date();
    tglsampai_2 = $('#tglsampai').data("DateTimePicker").date();
    reproses    = $('input[name="reproses[]"]').prop('checked');

    var check_shif    = false;
    var checkboxes_arr =  new Array(); 

    var checkboxes_arr = $('input[name="shift[]"]').map(function(e, i) {
            if(this.checked == true){
              check_shif = true;
              return i.value;
            }

    }).get();

    var timeDiff = 0;
    if (tglsampai_2) {
        timeDiff = (tglsampai_2 - tgldari_2) / 1000; // 000 mengubah hasil milisecond ke bentuk second
    }
    selisih = Math.floor(timeDiff/(86400)); // 1 hari = 25 jam, 1 jam=60 menit, 1 menit= 60 second , 1 hari = 86400 second

    if(tglsampai_2 < tgldari_2){ // cek validasi tgl sampai kurang dari tgl Dari
      alert_modal_warning('Maaf, Tanggal Sampai tidak boleh kurang dari Tanggal Dari !');
      // return false;

    }else if(check_shif == true && selisih > 30 ){
      alert_modal_warning('Maaf, Jika Shift di Ceklist (v) maka Periode Tanggal tidak boleh lebih dari 30 hari !')
      // return false;
    }else{
      $.ajax({
          "type":'POST',
          "url": "<?php echo site_url('report/HPHfinbrushing/export_excel_hph')?>",
          "data": {tgldari:tgldari, tglsampai:tglsampai, mo:mo, corak:corak, mc:mc, lot:lot, sales_order:sales_order, sales_group:sales_group,  no_go:no_go, warna:warna, user:user, jenis:jenis, shift :checkboxes_arr,reproses:reproses},
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


  // btn generate
  $("#btn-generate").on('click', function(){
      
      tgldari   = $('#tgldari').val();
      tglsampai = $('#tglsampai').val();
      mo        = $('#mo').val();
      corak     = $('#corak').val();
      mc        = $('#mc').val();
      lot       = $('#lot').val();
      user      = $('#user').val();
      jenis     = $('#jenis').val();
      sales_order     = $('#sales_order').val();
      sales_group     = $('#sales_group').val();
      no_go           = $('#no_go').val();
      warna           = $('#warna').val();
      tgldari_2 = $('#tgldari').data("DateTimePicker").date();
      tglsampai_2 = $('#tglsampai').data("DateTimePicker").date();
      reproses    = $('input[name="reproses[]"]').prop('checked');
      var check_shif  = false;

      checkboxes_arr =  new Array(); 

      var checkboxes_arr = $('input[name="shift[]"]').map(function(e, i) {
            if(this.checked == true){
              check_shif = true;
              return i.value;
            }

      }).get();

      // cek selisi tanggal
      var timeDiff = 0;
      if (tglsampai_2) {
          timeDiff = (tglsampai_2 - tgldari_2) / 1000; // 000 mengubah hasil milisecond ke bentuk second
      }
      selisih = Math.floor(timeDiff/(86400)); // 1 hari = 25 jam, 1 jam=60 menit, 1 menit= 60 second , 1 hari = 86400 second

      if(tgldari == '' || tglsampai == ''){
        alert_modal_warning('Periode Tanggal Harus diisi !');

      }else if(tglsampai_2 < tgldari_2){ // cek validasi tgl sampai kurang dari tgl Dari
        alert_modal_warning('Maaf, Tanggal Sampai tidak boleh kurang dari Tanggal Dari !');

      }else if(check_shif == true && selisih > 30){
        alert_modal_warning('Maaf, Jika Shift di Ceklist (v) maka Periode Tanggal tidak boleh lebih dari 30 hari !')

      }else{  
        
          $("#example1_processing").css('display','');// show loading processing in table
          $('#btn-generate').button('loading');
          $("#example1 tbody").remove();
          $.ajax({
                type: "POST",
                dataType : "JSON",
                url : "<?php echo site_url('report/HPHfinbrushing/loadData')?>",
                data: {tgldari:tgldari, tglsampai:tglsampai, mo:mo, corak:corak, mc:mc, lot:lot, sales_order:sales_order, sales_group:sales_group,no_go:no_go, warna:warna,  user:user, jenis:jenis, shift :checkboxes_arr,reproses:reproses },
                success: function(data){

                  if(data.status == 'failed'){
                    $('#total_record').html('Total Data : 0');
                    alert_modal_warning(data.message);
                  }else{

                    $('#total_record').html(data.total_record);

                    let tbody = $("<tbody />");
                    let no    = 1;
                    let empty = true;
                    let temp_mg = '';
                    let sum_mg  = 0;
                    let sum_qty = 0;
                    let sum_qty2 = 0;

                    $.each(data.record, function(key, value){
                        empty = false;

                        if(temp_mg != '' && temp_mg != value.kode){
                          result =  total_group(temp_mg,sum_mg,sum_qty,sum_qty2);
                          sum_mg = 0;
                          sum_qty = 0;
                          sum_qty2 = 0;
                          temp_mg = value.kode
                          tbody.append(result);
                          no = 1;
                        }

                        var tr = $("<tr>").append(
                                 $("<td>").text(no++),
                                 $("<td>").text(value.tgl_hph),
                                 $("<td>").text(value.kode_produk),
                                 $("<td>").text(value.nama_produk),
                                 $("<td>").text(value.nama_warna),
                                 $("<td>").text(value.sc),
                                 $("<td>").text(value.kode),
                                 $("<td>").text(value.no_go),
                                 $("<td>").text(value.nama_mesin),
                                 $("<td>").text(value.lot),
                                 $("<td align='right'>").text(value.gl),
                                 $("<td align='right'>").text(value.qty1),
                                 $("<td>").text(value.uom1),
                                 $("<td align='right'>").text(value.qty2),
                                 $("<td>").text(value.uom2),
                                 $("<td>").text(value.grade),
                                 $("<td>").text(value.lbr_greige),
                                 $("<td>").text(value.lbr_jadi),
                                 $("<td>").text(value.marketing),
                                 $("<td>").text(value.reff_note),
                                 $("<td>").text(value.lokasi),
                                 $("<td>").text(value.nama_user),
                                 $("<td>").text(value.keterangan),
                        );
                        tbody.append(tr);
                        temp_mg = value.kode;
                        sum_mg++;
                        sum_qty = sum_qty + parseFloat(value.qty1);
                        sum_qty2 = sum_qty2 + parseFloat(value.qty2);
                    });
                    if(empty == true){
                      var tr = $("<tr>").append($("<td colspan='22' >").text('Tidak ada Data'));
                      tbody.append(tr);
                    }else{
                      result =  total_group(temp_mg,sum_mg,sum_qty,sum_qty2);
                      tbody.append(result);
                    }
                    $("#example1").append(tbody);
                }

                $('#btn-generate').button('reset');
                $("#example1_processing").css('display','none');// hidden loading processing in table

                },error : function(jqXHR, textStatus, errorThrown){
                  alert(jqXHR.responseText);
                  //alert('error data');
                  $("#example1_processing").css('display','none');// hidden loading processing in table
                  $('#btn-generate').button('reset');
                }
          });
          
      }
  });

  function total_group(mg,sum_mg,sum_qty,sum_qty2)
  { 
      let row = '';
          row += "<tr>";
          row += "<td class='style_total' colspan='5'></td>";
          row += "<td class='style_total' colspan='2'>Total "+mg+"</td>";
          row += "<td class='style_total' colspan='4' align='right'>"+sum_mg+"</td>";
          row += "<td class='style_total' align='right'>"+formatNumber(sum_qty)+"</td>";
          row += "<td class='style_total' ></td>";
          row += "<td class='style_total' align='right'>"+formatNumber(sum_qty2)+"</td>";
          row += "<td class='style_total' colspan='9'></td>";
          row += "</tr>";
      return row;
  }

  function formatNumber(n) {
    return new Intl.NumberFormat('en-US').format(n);
  }

</script>

</body>
</html>
