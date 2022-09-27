
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
          <h3 class="box-title"><b>Pengiriman Greige</b></h3>
        </div>
        <div class="box-body">
           
            <form name="input" class="form-horizontal" role="form" method="POST" id="frm_periode" action="<?=base_url()?>report/pengirimangreige/export_excel">
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
                    <div class="col-md-2">
                        <label>View </label>
                    </div>
                    <div class="col-xs-4 col-sm-3 col-md-3">
                      <input type="radio" id="view" name="view[]" value="Detail">
                      <label for="detail">Detail</label>
                    </div>
                    <div class="col-xs-4 col-sm-3 col-md-3">
                      <input type="radio" id="view" name="view[]" value="Global" checked="checked">
                      <label for="global">Global</label>
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
                <button type="button" class="btn btn-sm btn-default" name="btn-generate" id="btn-generate" >Generate</button>
                <button type="submit" class="btn btn-sm btn-default" name="btn-excel" id="btn-excel" > <i class="fa fa-file-excel-o"></i> Excel</button>
              </div>
              <br>

              <div class="col-md-12">
                   <div class="panel panel-default" style="margin-bottom: 0px;">
                    <div id="advancedSearch" class="panel-collapse collapse" role="tabpanel" aria-labelledby="advanced" >
                      <div class="panel-body" style="padding: 5px">
                        <div class="form-group col-md-12" style="margin-bottom:0px">
                          <div class="col-md-5" >
                            <div class="form-group">
                              <div class="col-md-5">
                                <label>Dept Tujuan </label>
                              </div>
                              <div class="col-md-7">
                                <select type="text" class="form-control input-sm" name="tujuan" id="tujuan"  style="width:100% !important">
                                <option value="">-- Pilih Departemen --</option>
                                    <?php 
                                      foreach ($warehouse as $val) {
                                          echo "<option value='".$val->kode."'>".$val->nama."</option>";
                                      }
                                    ?>
                                </select>
                              </div>
                            </div>
                            <div class="form-group">
                              <div class="col-md-5">
                                <label>Status </label>
                              </div>
                              <div class="col-xs-4 col-sm-3 col-md-3">
                                  <label><input type="checkbox" name="status[]" value="ready"> Ready </label>
                              </div>
                              <div class="col-xs-4 col-sm-3 col-md-3">
                                  <label><input type="checkbox" name="status[]"  value="done" checked> Done </label>
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
                          <div class="col-md-5">
                            <div class="form-group">
                              <div class="col-md-5">
                                <label>Kode </label>
                              </div>
                              <div class="col-md-7">
                                <input type="text" class="form-control input-sm" name="kode" id="kode" placeholder="GRG OUT">
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
                              <th  class="style no" >No. </th>
                              <th  class='style' style="min-width: 80px">Kode</th>
                              <th  class='style' style="min-width: 80px">Tgl Kirim</th>
                              <th  class='style'>Origin</th>
                              <th  class='style'>Marketing</th>
                              <th  class='style'>Kode Produk</th>
                              <th  class='style' style="min-width: 150px">Nama Produk</th>
                              <th  class='style' style="min-width: 150px">Warna</th>
                              <th  class='style' id="head_lot">Lot</th>
                              <th  class='style' style="min-width: 80px">Qty1</th>
                              <th  class='style' style="min-width: 80px">Qty2</th>
                              <th  class='style'>Status</th>
                              <th  class='style' style="min-width: 80px">Reff Note</th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr>
                              <td colspan="12" align="center">Tidak ada Data</td>
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

  var d     = new Date();
  var month = d.getMonth();
  var day   = d.getDate();
  var day_1 = d.getDate();
  var year  = d.getFullYear();

  // set date tgldari
  $('#tgldari').datetimepicker({
      
      defaultDate : new Date(year, month, day_1, 00, 00, 00),
      format : 'D-MMMM-YYYY HH:mm:ss',
      ignoreReadonly: true,
      //maxDate: new Date(),
  });

  // set date tglsampai
  $('#tglsampai').datetimepicker({
      defaultDate : new Date(year, month, day, 23, 59, 59),
      format : 'D-MMMM-YYYY HH:mm:ss',
      ignoreReadonly: true,
      //maxDate: new Date(),
  });

  // disable enter
  $(window).keydown(function(event){
    if(event.keyCode == 13) {
      event.preventDefault();
      return false;
    }
  });

  $('#sales_group').select2({});

  $('#tujuan').select2({});

  // cek selisih saatu submit excel
  $('#frm_periode').submit(function(){

    tgldari   = $('#tgldari').data("DateTimePicker").date();
    tglsampai = $('#tglsampai').data("DateTimePicker").date();
    var check_status   = false;
    var checkboxes_arr = new Array(); 

    var checkboxes_arr = $('input[name="status[]"]').map(function(e, i) {
            if(this.checked == true){
                check_status = true;
              return i.value;
            }

    }).get();

    var timeDiff = 0;
    if (tglsampai) {
        timeDiff = (tglsampai - tgldari) / 1000; // 000 mengubah hasil milisecond ke bentuk second
    }
    selisih = Math.floor(timeDiff/(86400)); // 1 hari = 25 jam, 1 jam=60 menit, 1 menit= 60 second , 1 hari = 86400 second

    if(tglsampai < tgldari){ // cek validasi tgl sampai kurang dari tgl Dari
      alert_modal_warning('Maaf, Tanggal Sampai tidak boleh kurang dari Tanggal Dari !');
      return false;
    }else if (checkboxes_arr.length == 0) {
      alert_modal_warning('Status Harus Dipilih Salah satu !');
      return false;
    }else if( selisih > 30 ){
      alert_modal_warning('Maaf, Periode Tanggal tidak boleh lebih dari 30 hari !')
      return false;
    }

  });

  // btn generate
  $("#btn-generate").on('click', function(){

      tgldari    = $('#tgldari').val();
      tglsampai  = $('#tglsampai').val();
      tujuan     = $('#tujuan').val();
      kode       = $('#kode').val();
      sales_group= $('#sales_group').val();
      warna      = $('#warna').val();
      corak      = $('#corak').val();
      tgldari_2     = $('#tgldari').data("DateTimePicker").date();
      tglsampai_2   = $('#tglsampai').data("DateTimePicker").date();

      var check_status   = false;
      var checkboxes_arr = new Array(); 

      var checkboxes_arr = $('input[name="status[]"]').map(function(e, i) {
            if(this.checked == true){
                check_status = true;
              return i.value;
            }

      }).get();

      var radio_view= false;
      var radio_arr = new Array(); 

      var radio_arr = $('input[name="view[]"]').map(function(e, i) {
            if(this.checked == true){
                check_status = true;
              return i.value;
            }

      }).get();

      // cek selisi tanggal
      var timeDiff = 0;
      if (tglsampai_2) {
          timeDiff = (tglsampai_2 - tgldari_2) / 1000; // 000 mengubah hasil milisecond ke bentuk second
      }
      selisih = Math.floor(timeDiff/(86400)); // 1 hari = 25 jam, 1 jam=60 menit, 1 menit= 60 second , 1 hari = 86400 second

      if (checkboxes_arr.length == 0) {
        alert_modal_warning('Status Harus Dipilih Salah satu !');

      }else if(tglsampai_2 < tgldari_2){ // cek validasi tgl sampai kurang dari tgl Dari
        alert_modal_warning('Maaf, Tanggal Sampai tidak boleh kurang dari Tanggal Dari !');

      }else if(selisih > 30){
        alert_modal_warning('Maaf, Periode Tanggal tidak boleh lebih dari 30 hari !')

      }else{  
          $("#example1_processing").css('display',''); // show loading

          $('#btn-generate').button('loading');
          $("#example1 tbody").remove();
          $.ajax({
                type: "POST",
                dataType : "JSON",
                url : "<?php echo site_url('report/pengirimangreige/loadData')?>",
                data: {tgldari:tgldari, tglsampai:tglsampai, tujuan:tujuan, kode:kode, sales_group:sales_group, warna:warna, corak:corak, status_arr:checkboxes_arr, view_arr:radio_arr},
                success: function(data){

                  if(data.status == 'failed'){
                    $('#total_record').html('Total Data : 0');
                    alert_modal_warning(data.message);
                  }else{

                    $('#total_record').html(data.total_record);
                    if(data.view == 'Global'){
                      $('#head_lot').html('Total Lot');
                      width_lot = "style='min-width: 50px !important'; text-align:right";
                    }else{
                      $('#head_lot').html('Lot');;
                      width_lot = "style='min-width: 150px !important'";
                    }

                    let tbody = $("<tbody />");
                    let no    = 1;
                    let empty = true;
                    let link  = '';

                    $.each(data.record, function(key, value){
                        empty = false;
                        link = '<a href="<?=base_url()?>warehouse/pengirimanbarang/edit/'+value.kode_enc+'" data-toggle="tooltip" title="Lihat Pengiriman Barang" target="_blank">'+value.kode+'</a>'
                        var tr = $("<tr>").append(
                                 $("<td>").text(no++),
                                 $("<td>").html(link),
                                 $("<td>").text(value.tgl_kirim),
                                 $("<td>").text(value.origin),
                                 $("<td>").text(value.marketing),
                                 $("<td>").text(value.kode_produk),
                                 $("<td>").text(value.nama_produk),
                                 $("<td>").text(value.nama_warna),
                                 $("<td "+width_lot+">").text(value.lot),
                                 $("<td align='right'>").text(value.qty1),
                                 $("<td align='right'>").text(value.qty2),
                                 $("<td>").text(value.status),
                                 $("<td>").text(value.reff_note),
                        );
                        tbody.append(tr);
                    });
                    if(empty == true){
                      var tr = $("<tr>").append($("<td colspan='12' align='center'>").text('Tidak ada Data'));
                      tbody.append(tr);
                    }
                    $("#example1").append(tbody);
                }
                
                $('#btn-generate').button('reset');
                $("#example1_processing").css('display','none'); // hidden loading

                },error : function(jqXHR, textStatus, errorThrown){
                  alert(jqXHR.responseText);
                  //alert('error data');
                  $('#btn-generate').button('reset');
                  $("#example1_processing").css('display','none'); // hidden loading
                }
          });
      }
  });

</script>

</body>
</html>
