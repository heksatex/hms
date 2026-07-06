<!DOCTYPE html>
<html lang="en">

<head>
  <?php $this->load->view("admin/_partials/head.php") ?>
  <style type="text/css">
    h3 {
      display: block !important;
      text-align: center !important;
    }

    @media (max-width: 767px) {
      .top-bar {
        display: none !important;
      }

      .content-wrapper {
        padding-top: 50px !important;
        margin-top: 10px !important;
      }
    }

    .ws {
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
        <div class="box ">
          <div class="box-header with-border">
            <h3 class="box-title"><b>Stock History (GJD)</b></h3>
          </div>
          <div class="box-body ">

            <form name="input" action="#" role="form" method="get">
              <div class="col-md-8" style="padding-right: 0px !important;">
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
              </div>
              <div class="col-md-4">
                <!-- <button type="button" class="btn btn-sm btn-default" name="btn-generate" id="btn-generate" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing..."> Generate</button> -->
                <button type="button" name="submit" id="btn-proses" class="btn btn-primary btn-sm">Proses</button>
                <button type="button" class="btn btn-sm btn-default" name="btn-excel-table" id="btn-excel-table" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing..."> <i class="fa fa-file-excel-o" style="color:green"></i> Excel</button>
              </div>
            </form>
            &nbsp
            <div class="row" style="padding:10px;">
              <div class="col-md-12">
                <div class="box box-danger">
                  <div class="box-header with-border">
                    <h4 class="box-title">Grafik Stock History</h4>

                    <div class="box-tools pull-right">
                      <button type="button" class="btn btn-box-tool" data-widget="collapse">
                      </button>
                      <button type="button" class="btn btn-box-tool" data-widget="remove"></button>
                    </div>
                  </div>
                  <div class="box-body">
                    <div class="chart">
                      <div id="view" style="min-width: 200px; margin : 0 auto;"></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="row" style="padding:10px;">
              <div class="col-md-12">
                <div class="box box-danger">
                  <div class="box-header with-border">
                    <h4 class="box-title">Table Stock History </h4>

                    <div class="box-tools pull-right">
                      <button type="button" class="btn btn-box-tool" data-widget="collapse">
                      </button>
                      <button type="button" class="btn btn-box-tool" data-widget="remove"></button>
                    </div>
                  </div>
                  <div class="box-body">
                    <div class="table-responsive">
                      <div class="col-md-12 table-responsive over">
                        <table class="table table-condesed table-hover rlstable over" width="100%" id="table_history">
                          <thead>
                            <tr>
                              <th class="style width-50">No.</th>
                              <th class="style ws">Tanggal</th>
                              <th class="style ws">NMBB</th>
                              <th class="style ws">NMBL</th>
                              <th class="style ws">TMBX</th>
                              <th class="style ws">TMBL</th>
                              <th class="style ws">All</th>
                            </tr>
                          </thead>
                        </table>
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

  <script type="text/javascript" src="<?php echo site_url('dist/js_line/highcharts.js') ?>"></script>
  <script type="text/javascript" src="<?php echo site_url('dist/js_line/exporting.js') ?>"></script>
  <script src="<?php echo site_url('dist/js_line/export-data.js') ?>"></script>
  <script src="<?php echo site_url('dist/js_line/accessibility.js') ?>"></script>
  <script src="<?php echo site_url('dist/js_line/offline-exporting.js') ?>"></script>
  <!-- <script src="https://code.highcharts.com/highcharts.js"></script> -->

  <script type="text/javascript">
    var d = new Date();
    var month = d.getMonth();
    var day = d.getDate();
    var day_30 = d.getDate() - 30;
    var year = d.getFullYear();

    // set date tgldari
    $('#tgldari').datetimepicker({
      defaultDate: new Date(year, month, 1),
      format: 'D-MMMM-YYYY',
      ignoreReadonly: true,
      maxDate: new Date

    });

    // set date tglsampai
    $('#tglsampai').datetimepicker({
      defaultDate: new Date(),
      format: 'D-MMMM-YYYY',
      ignoreReadonly: true,
      maxDate: new Date
    });


    $('#viesw').highcharts({
      title: {
        text: 'Stock History',
        x: -20 //center
      },
      subtitle: {
        text: '',
        x: -20
      },
      xAxis: {
        title: {
          // text: '20 March s/d 31 March';
          text: '<?php echo "March 31"; ?>',
        },
        categories: ['28 March', '29 March', '30 March', '31 March'],
      },
      yAxis: {
        title: {
          text: 'Jumlah'
        },
        plotLines: [{
          value: 0,
          width: 1,
          color: '#808080'
        }]
      },
      tooltip: {
        valueSuffix: ''
      },
      legend: {
        layout: 'vertical',
        align: 'right',
        verticalAlign: 'middle',
        borderWidth: 0
      },
      series: [

        {
          name: 'NMBB',
          data: [100, 200, 20, 200],
        },

        // {
        // name: 'NMBB',
        // data:["100"]
        // },
        // {
        // name: 'NMBB',
        // data:["100"]
        // },
        // {
        // name: 'NMBB',
        // data:["100",]
        // },
      ]
    });

    var table;
    $(document).ready(function() {
      $('#table_history').dataTable({});
    });

    function fetch_data(tgldari = '', tglsampai = '') {
      // alert('tes');
      //datatables
      table = $('#table_history').DataTable({
        "processing": true,
        "serverSide": true,
        "order": [],

        "paging": true,
        "lengthChange": true,
        "searching": true,
        "ordering": true,
        "info": true,
        "autoWidth": false,

        "ajax": {
          "url": "<?php echo site_url('report/marketing/get_dataTable_stock_history') ?>",
          "type": "POST",
          "data": {
            tgldari: tgldari,
            tglsampai: tglsampai
          },
        },

        "columnDefs": [{
            "targets": [0],
            "orderable": false,
          },
          {
            "targets": [1],
            // "className":"nowrap",
          },
        ],
      });
    }


    var arr_data = [];
    // var bio = JSON.parse('{"NMBB":"[20,30,10,50]" } ');
    // arr_data.push({name : "NMBB", data : "[20,30,10,50]"});

    var options = {
      chart: {
        type: 'line',
        renderTo: 'view'
      },
      title: {
        text: 'Stock History GJD'
      },
      // subtitle: {
      //   text: 'Receita atribuida por midia de acordo com os filtros aplicados'
      // },
      yAxis: {
        title: {
          text: 'Jumlah',
          style: {
            fontSize: '2rem'
          }
        },
        labels: {
          style: {
            fontSize: '1rem'
          }
        },
        plotLines: [{
          value: 0,
          width: 1,
          color: '#808080'
        }]
      },
      xAxis: {
        title: {
          // text: '20 March s/d 31 March';
          // text: '<?php echo "Periode Tanggal"; ?>',
          text: '',
          style: {
            fontSize: '1.5rem'
          }
        },
        labels: {
          style: {
            fontSize: '1rem'
          }
        },
        categories: []
      },
      legend: {
        layout: 'vertical',
        align: 'right',
        verticalAlign: 'middle',
        style: {
          fontSize: '2em'
        },
        title: {
          text: 'Marketing<br/><span style="font-size: 9px; color: #666; ' +
            'font-weight: normal">(Click to hide)</span>',
          style: {
            fontSize: '1.5rem'
          }
        },
      },
      // plotOptions: {
      //   series: {
      //     label: {
      //       connectorAllowed: false
      //     },
      //     // pointStart: 1
      //   }
      // },
      tooltip: {
        style: {
          fontWeight: 'bold',
          fontSize: '1em'
        }
      },
      responsive: {
        rules: [{
          condition: {
            maxWidth: 500
          },
          chartOptions: {
            legend: {
              layout: 'horizontal',
              align: 'center',
              verticalAlign: 'bottom'
            }
          }
        }]
      },
      // 🔥 Bagian penting untuk offline export
      exporting: {
        enabled: true,
        fallbackToExportServer: false, // jangan kirim ke https://export.highcharts.com/
        filename: 'Stock_History',
        buttons: {
          contextButton: {
            menuItems: [
              'viewFullscreen',   // 🟢 Fullscreen
              'printChart',       // 🟢 Print
              'downloadPNG',
              'downloadJPEG',
              'downloadPDF',
              'downloadSVG',
              'separator',
              'downloadCSV',
              'downloadXLS'
            ]
          }
        }
      },
      series: [{}],
    };

    var series = [];
    var categories = [];
    // var chart = new Highcharts.Chart(options.series);
    var chart = new Highcharts.Chart(options);
    $('#btn-proses').on('click', function(e) {
      var tgl_dari = $("#tgldari").val();
      var tgl_sampai = $("#tglsampai").val();

      $("#table_history").dataTable().fnDestroy();
      fetch_data(tgl_dari, tgl_sampai);

      $("#view").html('t');
      // chart.destroy();
      var series = [];
      var categories = [];
      $.ajax({
          type: 'POST',
          dataType: 'json',
          url: '<?= base_url() ?>report/marketing/get_data_stock_history',
          data: {
            tgl_dari: tgl_dari,
            tgl_sampai: tgl_sampai
          },
          success: function(data) {

            options.xAxis.categories = data.periode; // langsung array dari PHP

            var arr_data = data.result;
            var series = [];

            arr_data.forEach(function(el) {
              series.push({
                name: el.name,
                data: el.data // langsung array, gak perlu eval()
              });
            });

            options.series = series;
            options.xAxis.title.text = tgl_dari + ' s/d ' + tgl_sampai;
            new Highcharts.Chart(options);
          },
         error: function(jqXHR, textStatus, errorThrown) {
          //alert(jqXHR.responseText);
          alert('Error Get Data');
        }
      });
    });


    // btn excel
    $('#btn-excel-table').click(function() {

      var tgldari = $("#tgldari").val();
      var tglsampai = $("#tglsampai").val();

      if (tgldari.length == 0 || tglsampai.length == 0) {
        alert_modal_warning('Periode Tanggal Harus diisi !');
        // return false;
      } else {
        $.ajax({
          "type": 'POST',
          "url": "<?php echo site_url('report/marketing/export_excel_stock_history') ?>",
          "data": {
            tgldari: tgldari,
            tglsampai: tglsampai
          },
          "dataType": 'json',
          beforeSend: function() {
            $('#btn-excel-table').button('loading');
          },
          error: function() {
            alert('Error Export Excel');
            $('#btn-excel-table').button('reset');
          }
        }).done(function(data) {
          if (data.status == "failed") {
            alert_modal_warning(data.message);
          } else {
            var $a = $("<a>");
            $a.attr("href", data.file);
            $("body").append($a);
            $a.attr("download", data.filename);
            $a[0].click();
            $a.remove();
          }
          $('#btn-excel-table').button('reset');
        });
      }

    });
  </script>

</body>

</html>