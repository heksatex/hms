<!DOCTYPE html>
<html lang="en">

<head>
  <?php $this->load->view("admin/_partials/head.php") ?>
  <link rel="stylesheet" type="text/css" href="<?php echo base_url('dist/css/tableScroll.css') ?>">
  <style type="text/css">
    h3 {
      display: block !important;
      text-align: center !important;
    }

    .divListviewHead table {
      display: block;
      height: calc(96vh - 250px);
      overflow-x: auto;
    }
    /*
    .btn-setTgl {
      height: 22px;
      min-width: 40px;
    }
    */
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
                      <select type="text" class="form-control input-sm" name="departemen" id="departemen" required="">
                      </select>
                    </div>
                  </div>
                </div>

              </div>
              <div class="col-md-4">
                <button type="button" class="btn btn-sm btn-default" name="btn-generate" id="btn-generate" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing..." >Generate</button>
                <button type="button" class="btn btn-sm btn-default" name="btn-generate" id="btn-excel" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing..." > <i class="fa fa-file-excel-o" style="color:green"></i> Excel</button>
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
                          <tr id="atas">
                            <th class="style bb no" rowspan="2">No. </th>
                            <th class='style bb' rowspan="2" style="min-width: 100px">Mesin</th>
                            <th class='style bb' rowspan="2" style="min-width: 150px">Produk/Corak</th>
                            <th class='style bb' rowspan="2" style="width: 5px; word-wrap: break-word; text-align: center;">Standar Mtr</th>
                            <th class='style bb' rowspan="2" style="width: 5px; word-wrap: break-word; text-align: center;">Standar Kg</th>
                            <th class='style bb' rowspan="2" style="text-align: center;">RPM</th>
                            <th class='style bb' colspan="3" style="text-align: center;">Total Produksi</th>
                            <th class='style bb' rowspan="2" style="text-align: center;">Efisensi (%)</th>
                            <th class='style bb' colspan="3" style="text-align: center;">Grade</th>
                            <th class='style bb' rowspan="2">Ket</th>

                          </tr>
                          <tr id="bawah">
                            <th class='style bb' style="text-align: center;">Qty1</th>
                            <th class='style bb' style="text-align: center;">Qty2</th>
                            <th class='style bb' style="text-align: center;">Pcs</th>
                            <th class='style bb' style="text-align: center;">A</th>
                            <th class='style bb' style="text-align: center;">B</th>
                            <th class='style bb' style="text-align: center;">C</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr>
                            <td colspan="14" align="center">Tidak ada Data</td>
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

  <script type="text/javascript">

    var tgldari   = $('#tgldari').val();
    var tglsampai = $('#tglsampai').val();
 
    // set date tgldari
    $('#tgldari').datetimepicker({
      defaultDate: new Date().toLocaleString('en-US', { timeZone: 'Asia/Jakarta' }),
      format: 'D-MMMM-YYYY',
      ignoreReadonly: true,
      maxDate: new Date()
      //maxDate: xx
    });

    // set date tglsampai
    $('#tglsampai').datetimepicker({
      defaultDate: new Date().toLocaleString('en-US', { timeZone: 'Asia/Jakarta' }),
      format: 'D-MMMM-YYYY',
      ignoreReadonly: true,
      maxDate: new Date(),
      //minDate : 
      //maxDate: new Date(),
      //startDate: StartDate,
    });

    //select 2 Departementy
    $('#departemen').select2({
      allowClear: true,
      placeholder: "Select Departemen",
      ajax: {
        dataType: 'JSON',
        type: "POST",
        url: "<?php echo base_url(); ?>report/efisiensi/get_departement_select2",
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

      var tgldari   = $('#tgldari').data("DateTimePicker").date();
      var tglsampai = $('#tglsampai').data("DateTimePicker").date();
      var id_dept   = $('#departemen').val();

      var timeDiff = 0;
      if (tglsampai) {
          timeDiff = (tglsampai - tgldari) / 1000; // 000 mengubah hasil milisecond ke bentuk second
      }
      selisih = Math.floor(timeDiff/(86400)); // 1 hari = 25 jam, 1 jam=60 menit, 1 menit= 60 second , 1 hari = 86400 second
      if (id_dept == null) {
        alert_modal_warning('Departemen Harus diisi !');

      }else  if(tglsampai < tgldari){ // cek validasi tgl sampai kurang dari tgl Dari
        alert_modal_warning('Maaf, Tanggal Sampai tidak boleh kurang dari Tanggal Dari !');
        return false;

      }else if(selisih > 6 ){
        alert_modal_warning('Maaf,  Periode Tanggal tidak boleh lebih dari 7 hari !')
        return false;
      }
    });

    // btn generate
    $("#btn-generate").on('click', function() {

      var tgldari   = $('#tgldari').val();
      var tglsampai = $('#tglsampai').val();
      var id_dept   = $('#departemen').val();

      var tgldari_2   = $('#tgldari').data("DateTimePicker").date();
      var tglsampai_2 = $('#tglsampai').data("DateTimePicker").date();
      
      var timeDiff = 0;
      if (tglsampai_2) {
          timeDiff = (tglsampai_2 - tgldari_2) / 1000; // 000 mengubah hasil milisecond ke bentuk second
      }
      
      selisih = Math.floor(timeDiff/(86400)); // 1 hari = 25 jam, 1 jam=60 menit, 1 menit= 60 second , 1 hari = 86400 second

      if (tgldari == '' || tglsampai == '') {
        alert_modal_warning('Periode Tanggal Harus diisi !');

      }else if (id_dept == null) {
        alert_modal_warning('Departemen Harus diisi !');

      }else if(tglsampai_2 < tgldari_2){
        alert_modal_warning('Maaf, Tanggal Sampai tidak boleh kurang dari Tanggal Dari !');

      }else if(selisih > 6){ // jika periode tanggal lebih dari 7 hari
        alert_modal_warning('Maaf, Periode Tanggal tidak boleh lebih dari 7 hari !')

      } else {
        $("#example1_processing").css('display', ''); // show loading

        $('#btn-generate').button('loading');
        $("#example1 tbody").remove();
        $("#example1 thead tr[id='atas'] .parentTgl").remove();
        $("#example1 thead tr[id='bawah'] .childTgl").remove();
        $.ajax({
          type: "POST",
          dataType: "JSON",
          url: "<?php echo site_url('report/qualitycontrol/loadData') ?>",
          data: {
            tgldari: tgldari,
            tglsampai: tglsampai,
            id_dept: id_dept
          },
          success: function(data) {

            if (data.dataHari.length > 0) {
              var header_parent = "<th colspan='" + data.jmlHari + "' class='style parentTgl bb'> Efisiensi/Tanggal(%)</th>";
              $("#example1 thead tr[id='atas']").append(header_parent);

              $.each(data.dataHari, function(key, val) {
                var header_child = '<th class="style childTgl bb">' + val.tgl + '</th>';
                $("#example1 thead tr[id='bawah']").append(header_child);
              });
            }

            let no = 1;
            let empty = true;
            let tbody = $("<tbody />");

            $.each(data.record, function(key, value) {

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
              if (value.mrp.length > 0) {
                $.each(value.mrp, function(k, v) {
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

                  if (v.dataEfHari.length > 0) {
                    var td = '';
                    $.each(v.dataEfHari, function(a, b) {
                      //alert('tes');
                      td = '<td>' + b.efisiensi + '</td>';
                      tr.append(td);
                    });
                  }
                  tbody.append(tr);

                })
              } else {
                for (let i = 0; i < data.jmlHari; i++) {
                  td = '<td>0</td>';
                  tr.append(td);
                }
                tbody.append(tr);
                no++;
              }

              tbody.append(tr);
              $("#example1").append(tbody); // append parents

            });

            if (empty == true) {
              var tr = $("<tr>").append($("<td colspan='12' align='center'>").text('Tidak ada Data'));
              tbody.append(tr);
            }
            //$("#example1").append(tbody);

            $('#btn-generate').button('reset');
            $("#example1_processing").css('display', 'none'); // hidden loading

          },
          error: function(jqXHR, textStatus, errorThrown) {
            alert(jqXHR.responseText);
            //alert('error data');
            $("#example1_processing").css('display', 'none'); // hidden loading
            $('#btn-generate').button('reset');
          }
        });
      }
    });

    $('#btn-excel').click(function(){

      var tgldari   = $('#tgldari').val();
      var tglsampai = $('#tglsampai').val();
      var id_dept   = $('#departemen').val();

      var tgldari_2   = $('#tgldari').data("DateTimePicker").date();
      var tglsampai_2 = $('#tglsampai').data("DateTimePicker").date();

      var timeDiff = 0;
      if (tgldari == '' || tglsampai == '') {
        alert_modal_warning('Periode Tanggal Harus diisi !');
      }else if (tglsampai_2) {
          timeDiff = (tglsampai_2 - tgldari_2) / 1000; // 000 mengubah hasil milisecond ke bentuk second
      }
      selisih = Math.floor(timeDiff/(86400)); // 1 hari = 25 jam, 1 jam=60 menit, 1 menit= 60 second , 1 hari = 86400 second
      if (id_dept == null) {
        alert_modal_warning('Departemen Harus diisi !');

      }else  if(tglsampai_2 < tgldari_2){ // cek validasi tgl sampai kurang dari tgl Dari
        alert_modal_warning('Maaf, Tanggal Sampai tidak boleh kurang dari Tanggal Dari !');

      }else if(selisih > 6 ){
        alert_modal_warning('Maaf,  Periode Tanggal tidak boleh lebih dari 7 hari !')
      }else{
        $.ajax({
            "type":'POST',
            "url": "<?php echo site_url('report/Qualitycontrol/export_excel')?>",
            "dataType":'json',
            "data":  {
              tgldari: tgldari,
              tglsampai: tglsampai,
              id_dept: id_dept
            },
            beforeSend: function() {
              $('#btn-excel').button('loading');
            },error: function(){
              alert("Export Excel error");
              $('#btn-excel').button('reset');
            }
        }).done(function(data){
            if(data.status == "failed"){
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