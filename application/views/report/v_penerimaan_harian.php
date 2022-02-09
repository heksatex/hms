
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
          <h3 class="box-title"><b>Penerimaan Harian</b></h3>
        </div>
        <div class="box-body">
           
            <form name="input" class="form-horizontal" role="form" method="POST" id="frm_periode" action="<?=base_url()?>report/penerimaanharian/export_excel_in">
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
                      <label>Departemen </label>
                    </div>
                    <div class="col-md-4">
                      <select type="text" class="form-control input-sm" name="departemen" id="departemen" required="">
                      </select>
                    </div>
                    <div class="col-md-1">
                        <label>Dari</label>
                    </div>
                    <div class="col-md-4">
                      <select type="text" class="form-control input-sm" name="dari" id="dari" >
                      </select>
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <div class="col-md-12"> 
                    <div class="col-md-2 col-sm-2">
                      <label>Status </label>
                    </div>
                    <div class="col-xs-4 col-sm-3 col-md-2">
                        <label><input type="checkbox" name="status[]" value="ready" checked> Ready </label>
                    </div>
                    <div class="col-xs-4 col-sm-3 col-md-2">
                        <label><input type="checkbox" name="status[]"  value="done" checked> Done </label>
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <div class="col-md-12">
                    <div class="col-md-4">
                      <label>
                          <div id='total_record'>Total Lot : 0</div>
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
                              <th  class='style'>Kode</th>
                              <th  class='style' style="min-width: 80px">Tgl Kirim</th>
                              <th  class='style'>Origin</th>
                              <th  class='style' style="min-width: 150px">Reff Picking</th>
                              <th  class='style'>Kode Produk</th>
                              <th  class='style' style="min-width: 150px">Nama Produk</th>
                              <th  class='style' style="min-width: 150px">Lot</th>
                              <th  class='style'>Qty1</th>
                              <th  class='style'>Qty2</th>
                              <th  class='style'>Status</th>
                              <th  class='style'>Reff Note</th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr>
                              <td colspan="11" align="center">Tidak ada Data</td>
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
  var day_1 = d.getDate()-1;
  var year  = d.getFullYear();

  // set date tgldari
  $('#tgldari').datetimepicker({
      
      defaultDate : new Date(year, month, day_1, 07, 00, 00),
      format : 'D-MMMM-YYYY HH:mm:ss',
      ignoreReadonly: true,
      //maxDate: new Date(),
  });

  // set date tglsampai
  $('#tglsampai').datetimepicker({
      defaultDate : new Date(year, month, day, 07, 00, 00),
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

  //select 2 Departement
  $('#departemen').select2({
      allowClear: true,
      placeholder: "Select Departemen",
      ajax: {
        dataType: 'JSON',
        type: "POST",
        url: "<?php echo base_url(); ?>report/penerimaanharian/get_departement_select2",
        //delay : 250,
        data: function(params) {
          return {
            nama: params.term,
          };
        },
        processResults: function(data) {
          var results = [];
          $.each(data, function(index, item) {
            results.push({
              id: item.kode,
              text: item.nama
            });
          });
          return {
            results: results
          };
        },
        error: function(xhr, ajaxOptions, thrownError) {
          //alert('Error data');
          //alert(xhr.responseText);
        }
      }
  });

  //select 2 Departement
  $('#dari').select2({
      allowClear: true,
      placeholder: "Select Departemen",
      ajax: {
        dataType: 'JSON',
        type: "POST",
        url: "<?php echo base_url(); ?>report/penerimaanharian/get_departement_select2",
        //delay : 250,
        data: function(params) {
          return {
            nama: params.term,
          };
        },
        processResults: function(data) {
          var results = [];
          $.each(data, function(index, item) {
            results.push({
              id: item.kode,
              text: item.nama
            });
          });
          return {
            results: results
          };
        },
        error: function(xhr, ajaxOptions, thrownError) {
          //alert('Error data');
          //alert(xhr.responseText);
        }
      }
  });


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
      departemen = $('#departemen').val();
      dept_dari  = $('#dari').val();
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


      // cek selisi tanggal
      var timeDiff = 0;
      if (tglsampai_2) {
          timeDiff = (tglsampai_2 - tgldari_2) / 1000; // 000 mengubah hasil milisecond ke bentuk second
      }
      selisih = Math.floor(timeDiff/(86400)); // 1 hari = 25 jam, 1 jam=60 menit, 1 menit= 60 second , 1 hari = 86400 second

      if(tgldari == '' || tglsampai == ''){

        alert_modal_warning('Periode Tanggal Harus diisi !');

      }else if (departemen == null) {
        alert_modal_warning('Departemen Harus diisi !');

      }else if (checkboxes_arr.length == 0) {
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
                url : "<?php echo site_url('report/penerimaanharian/loadData')?>",
                data: {tgldari:tgldari, tglsampai:tglsampai, departemen:departemen, dept_dari:dept_dari,status_arr:checkboxes_arr},
                success: function(data){

                  if(data.status == 'failed'){
                    $('#total_record').html('Total Lot : 0');
                    alert_modal_warning(data.message);
                  }else{

                    $('#total_record').html(data.total_record);

                    let tbody = $("<tbody />");
                    let no    = 1;
                    let empty = true;

                    $.each(data.record, function(key, value){
                        empty = false;
                        var tr = $("<tr>").append(
                                 $("<td>").text(no++),
                                 $("<td>").html('<a href="<?=base_url()?>warehouse/penerimaanbarang/edit/'+value.kode_enc+'" data-toggle="tooltip" title="Lihat Pengiriman Barang" target="_blank">'+value.kode+'</a>'),
                                 $("<td>").text(value.tgl_kirim),
                                 $("<td>").text(value.origin),
                                 $("<td>").text(value.reff_picking),
                                 $("<td>").text(value.kode_produk),
                                 $("<td>").text(value.nama_produk),
                                 $("<td>").text(value.lot),
                                 $("<td align='right'>").text(value.qty1),
                                 $("<td align='right'>").text(value.qty2),
                                 $("<td>").text(value.status),
                                 $("<td>").text(value.reff_note),
                        );
                        tbody.append(tr);
                    });
                    if(empty == true){
                      var tr = $("<tr>").append($("<td colspan='11' align='center'>").text('Tidak ada Data'));
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
                }
          });
      }
  });

</script>

</body>
</html>
