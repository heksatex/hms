
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
    .nowrap{
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
          <h3 class="box-title"><b>Penerimaan Harian</b></h3>
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
                    <div class="col-md-2">
                      <label>Departemen </label>
                    </div>
                    <div class="col-md-4">
                      <select type="text" class="form-control input-sm" name="departemen" id="departemen" required="">
                      </select>
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <div class="col-md-12"> 
                    <div class="col-md-2">
                        <label>View </label>
                    </div>
                    <div class="col-xs-4 col-sm-3 col-md-3">
                      <input type="radio" id="view" name="view[]" value="Global" checked="checked">
                      <label for="global">Global</label>
                    </div>
                    <div class="col-xs-4 col-sm-3 col-md-3">
                      <input type="radio" id="view" name="view[]" value="Detail">
                      <label for="detail">Detail</label>
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
                <button type="button" class="btn btn-sm btn-default" name="btn-excel" id="btn-excel" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing..."> <i class="fa fa-file-excel-o " style="color:green"></i> Excel</button>
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
                                <label>Dept dari </label>
                              </div>
                              <div class="col-md-7">
                                <select type="text" class="form-control input-sm" name="dari" id="dari" style="width:100% !important"></select>
                              </div>
                            </div>
                            <div class="form-group">
                              <div class="col-md-5">
                                <label>Lokasi </label>
                              </div>
                              <div class="col-xs-4 col-sm-3 col-md-3">
                                  <label><input type="checkbox" name="lokasi[]" value="POS" > POS </label>
                              </div>
                            </div>
                            <div class="form-group">
                              <div class="col-md-5">
                                <label>Status </label>
                              </div>
                              <div class="col-xs-4 col-sm-3 col-md-3">
                                  <label><input type="checkbox" name="status[]" value="ready" > Ready </label>
                              </div>
                              <div class="col-xs-4 col-sm-3 col-md-3">
                                  <label><input type="checkbox" name="status[]"  value="done" checked> Done </label>
                              </div>
                            </div>
                          </div>
                          <div class="col-md-5">
                            <div class="form-group">
                              <div class="col-md-5">
                                <label>Kode </label>
                              </div>
                              <div class="col-md-7">
                                <input type="text" class="form-control input-sm" name="kode" id="kode" >
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
                  <div class="divListviewHead">
                    <div role="region" aria-labelledby="HeadersCol" tabindex="0" class="rowheaders">
                      <table id="example1" class="table table-condesed table-hover" border="0">
                          <thead>
                            <tr>
                              <th  class="style bb no" >No. </th>
                              <th  class='style bb'>Kode</th>
                              <th  class='style bb' style="min-width: 80px">Tgl Kirim</th>
                              <th  class='style bb'>Origin</th>
                              <th  class='style bb' style="min-width: 150px">Reff Picking</th>
                              <th  class='style bb nowrap'>Kode Produk</th>
                              <th  class='style bb' style="min-width: 150px">Nama Produk</th>
                              <th  class='style bb nowrap' id="head_lot">Lot</th>
                              <th  class='style bb'>Qty1</th>
                              <th  class='style bb'>Qty2</th>
                              <th  class='style bb'>Status</th>
                              <th  class='style bb'>Reff Note</th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr>
                              <td colspan="11" align="center">Tidak ada Data</td>
                            </tr>
                          </tbody>
                      </table>
                      <small><b>*Jika terdapat baris yang berwarna <font color="red">MERAH</font> maka Product/Lot tersebut telah di proses ADJUSTMENT !!</b></small>
                      <div id="example1_processing" class="table_processing" style="display: none">
                        Processing...
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
      
      defaultDate : new Date(year, month, day, 0o0, 0o0, 0o0),
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


  // btn excel
  $('#btn-excel').click(function(){

    tgldari    = $('#tgldari').val();
    tglsampai  = $('#tglsampai').val();
    departemen = $('#departemen').val();
    dept_dari  = $('#dari').val();
    kode       = $('#kode').val();
    corak      = $('#corak').val();
    tgldari_2     = $('#tgldari').data("DateTimePicker").date();
    tglsampai_2   = $('#tglsampai').data("DateTimePicker").date();
    lokasi    = $('input[name="lokasi[]"]').prop('checked');
  
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

    var timeDiff = 0;
    if (tglsampai_2) {
        timeDiff = (tglsampai_2 - tgldari_2) / 1000; // 000 mengubah hasil milisecond ke bentuk second
    }
    selisih = Math.floor(timeDiff/(86400)); // 1 hari = 25 jam, 1 jam=60 menit, 1 menit= 60 second , 1 hari = 86400 second

    if(tglsampai_2 < tgldari_2){ // cek validasi tgl sampai kurang dari tgl Dari
      alert_modal_warning('Maaf, Tanggal Sampai tidak boleh kurang dari Tanggal Dari !');
      // return false;
    }else if (checkboxes_arr.length == 0) {
      alert_modal_warning('Status Harus Dipilih Salah satu !');
      // return false;
    }else if( selisih > 30 ){
      alert_modal_warning('Maaf, Periode Tanggal tidak boleh lebih dari 30 hari !')
      // return false;
    }else{
      $.ajax({
          "type":'POST',
          "url" : "<?php echo site_url('report/penerimaanharian/export_excel_in')?>",
          "data": {tgldari:tgldari, tglsampai:tglsampai, departemen:departemen, dept_dari:dept_dari,status_arr:checkboxes_arr,  kode:kode, corak:corak, view_arr:radio_arr, lokasi_pos:lokasi },
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

      tgldari    = $('#tgldari').val();
      tglsampai  = $('#tglsampai').val();
      departemen = $('#departemen').val();
      dept_dari  = $('#dari').val();
      kode       = $('#kode').val();
      corak      = $('#corak').val();
      tgldari_2     = $('#tgldari').data("DateTimePicker").date();
      tglsampai_2   = $('#tglsampai').data("DateTimePicker").date();
      lokasi    = $('input[name="lokasi[]"]').prop('checked');

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
                data: {tgldari:tgldari, tglsampai:tglsampai, departemen:departemen, dept_dari:dept_dari,status_arr:checkboxes_arr,  kode:kode, corak:corak, view_arr:radio_arr, lokasi_pos:lokasi},
                success: function(data){

                  if(data.status == 'failed'){
                    $('#total_record').html('Total Data : 0');
                    alert_modal_warning(data.message);
                  }else{

                    $('#total_record').html(data.total_record);
                    if(data.view == 'Global'){
                      $('#head_lot').html('Total Lot');
                      width_lot = "style='min-width: 50px !important; text-align:right;' ";
                    }else{
                      $('#head_lot').html('Lot');;
                      width_lot = "style='min-width: 150px !important'";
                    }


                    let tbody = $("<tbody />");
                    let no    = 1;
                    let empty = true;

                    $.each(data.record, function(key, value){
                        empty = false;
                        if(value.in == 'Yes'){
                          link = '<a href="<?=base_url()?>warehouse/penerimaanbarang/edit/'+value.kode_enc+'" data-toggle="tooltip" title="Lihat Penerimaan Barang" target="_blank">'+value.kode+'</a>'
                        }else{
                          link = '<a href="<?=base_url()?>warehouse/pengirimanbarang/edit/'+value.kode_enc+'" data-toggle="tooltip" title="Lihat Pengiriman Barang" target="_blank">'+value.kode+'</a>'
                        }

                        if(value.lot_adj != ''){
                          color = "style='color:red';";
                        }else{
                          color = "";
                        }

                        var tr = $("<tr>").append(
                                 $("<td "+color+">").text(no++),
                                 $("<td "+color+">").html(link),
                                 $("<td "+color+">").text(value.tgl_kirim),
                                 $("<td "+color+">").text(value.origin),
                                 $("<td "+color+">").text(value.reff_picking),
                                 $("<td "+color+">").text(value.kode_produk),
                                 $("<td "+color+">").text(value.nama_produk),
                                 $("<td class='nowrap' "+width_lot+" "+color+">").text(value.lot),
                                 $("<td align='right' "+color+">").text(value.qty1),
                                 $("<td align='right' "+color+">").text(value.qty2),
                                 $("<td "+color+">").text(value.status),
                                 $("<td "+color+">").text(value.reff_note),
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
