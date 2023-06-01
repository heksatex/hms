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
            <h3 class="box-title"><b>Downtime</b></h3>
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
                        <input type="text" class="form-control input-sm" name="tglsampai" id="tglsampai" required="">
                        
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
                <div class="form-group">
                  <div class="col-md-12">
                    <div class="col-md-2"><label>Shortcut</label></div>
                    <div class="col-md-10">
                        <button type="button" class="btn btn-xs btn-default" name="btn-1" id="btn-1" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing..." onclick="btn_shortcut('btn-1','now')">Saat Ini</button>
                        <button type="button" class="btn btn-xs btn-default" name="btn-2" id="btn-2" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing..." onclick="btn_shortcut('btn-2','1shift-before')">1 Shift Sebelum</button>
                        <button type="button" class="btn btn-xs btn-default" name="btn-3" id="btn-3" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing..." onclick="btn_shortcut('btn-3','24hours-before')">24 Jam Sebelum</button>
                        <button type="button" class="btn btn-xs btn-default" name="btn-4" id="btn-4" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing..." onclick="btn_shortcut('btn-4','7days-before')">7 Hari Sebelum</button>
                        <button type="button" class="btn btn-xs btn-default" name="btn-5" id="btn-5" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing..."onclick="btn_shortcut('btn-5','30day-before')">30 Hari Sebelum</button>
                    </div>
                  </div>
                </div>

              </div>
              <div class="col-md-4">
                <!-- <div class="form-group">
                        <button type="button" class="btn btn-xs btn-default" name="btn-1" id="btn-1">Saat Ini</button>
                        <button type="button" class="btn btn-xs btn-default" name="btn-2" id="btn-2">1 Shift Sebelum</button>
                        <button type="button" class="btn btn-xs btn-default" name="btn-3" id="btn-4">24 Jam Sebelum</button>
                        <button type="button" class="btn btn-xs btn-default" name="btn-4" id="btn-5">7 Hari Sebelum</button>
                        <button type="button" class="btn btn-xs btn-default" name="btn-5" id="btn-6">30 Sebelum</button>
                </div> -->
                <div class="form-group">
                    <button type="button" class="btn btn-sm btn-default" name="btn-generate" id="btn-generate" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing..." > Generate</button>
                    <button type="button" class="btn btn-sm btn-default" name="btn-generate" id="btn-excel" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing..." > <i class="fa fa-file-excel-o" style="color:green"></i> Excel</button>
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
                            <th class="style bb no">No. </th>
                            <th class='style bb' style="min-width: 200px">Nama Mesin</th>
                            <th class='style bb' style="min-width: 100px; text-align:right">Downtime (min)</th>
                            <th class='style bb' style="min-width: 100px; text-align:right">Downtime (%)</th>
                            <th class='style bb' style="min-width: 10px; text-align:right">Uptime (min)</th>
                            <th class='style bb' style="min-width: 10px; text-align:right">Uptime (%)</th>
                            <th class='style bb' style="min-width: 100px; text-align:right">dc</th>
                            <th class='style bb' style="min-width: 100px; text-align:right">dct</th>
                            <th class='style bb' style="min-width: 100px; text-align:right">dcr</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr>
                            <td colspan="9" align="center">Tidak ada Data</td>
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

  <div id="load_modal">
    <!-- Load Partial Modal -->
    <?php $this->load->view("admin/_partials/modal.php") ?>
  </div>

  <script type="text/javascript">
 
    // set date tgldari
    $('#tgldari').datetimepicker({
      defaultDate: new Date().toLocaleString('en-US', { timeZone: 'Asia/Jakarta' }),
      format : 'D-MMMM-YYYY HH:mm:ss',
      ignoreReadonly: true,
      // maxDate: new Date()
    });

    // set date tglsampai
    $('#tglsampai').datetimepicker({
      defaultDate: new Date().toLocaleString('en-US', { timeZone: 'Asia/Jakarta' }),
      format : 'D-MMMM-YYYY HH:mm:ss',
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
        url: "<?php echo base_url(); ?>report/qualitycontrol/get_departement_select2",
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

    var arr_filter = [];

    
    // btn generate
    $("#btn-generate").on('click', function() {

      var tgldari   = $('#tgldari').val();
      var tglsampai = $('#tglsampai').val();
      var id_dept   = $('#departemen').val();
      var this_btn  = $(this);

      var tgldari_2   = $('#tgldari').data("DateTimePicker").date();
      var tglsampai_2 = $('#tglsampai').data("DateTimePicker").date();
      
      if (tgldari == '' || tglsampai == '') {
        alert_modal_warning('Periode Tanggal Harus diisi !');

      }else if (id_dept == null) {
        alert_modal_warning('Departemen Harus diisi !');

      }else if(tglsampai_2 < tgldari_2){
        alert_modal_warning('Maaf, Tanggal Sampai tidak boleh kurang dari Tanggal Dari !');

      } else {
        arr_filter = [];
        process_downtime(this_btn);
       
      }
    });

    
    function btn_shortcut(id,btn)
    { 
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

        } else {
          var this_btn   =  $('#'+id);
          arr_filter  = [];
          $.ajax({
              type: "POST",
              dataType: "JSON",
              url: "<?php echo site_url('report/downtime/shortcut_get') ?>",
              data: {
                shortcut: btn,
              },
              success: function(data) {
                  // alert('berhasil');
                  $('#tgldari').data('DateTimePicker').date(new Date(new Date(data.tgl_dari)));
                  $('#tglsampai').data('DateTimePicker').date(new Date(new Date(data.tgl_sampai)));
                  process_downtime(this_btn);
                  // $('#tgldari').data("DateTimePicker").date(data.tgl_dari);
                  // $('#tglsampai').data("DateTimePicker").date(data.tgl_sampai);
                  // $('#tglsampai').data("DateTimePicker").date(data.tgl_sampai).format('D-MMMM-YYYY HH:mm:ss');
              },
              error: function(jqXHR, textStatus, errorThrown) {
                alert(jqXHR.responseText);
              }
          });
        }

    }

    function process_downtime(this_btn)
    { 
        var tgldari   = $('#tgldari').val();
        var tglsampai = $('#tglsampai').val();
        var id_dept   = $('#departemen').val();

        $("#example1_processing").css('display', ''); // show loading
        this_btn.button('loading');
        $("#example1 tbody").remove();
        $.ajax({
          type: "POST",
          dataType: "JSON",
          url: "<?php echo site_url('report/downtime/loadData') ?>",
          data: {
            tgldari: tgldari,
            tglsampai: tglsampai,
            id_dept: id_dept
          },
          success: function(data) {

            let no = 1;
            let empty = true;
            let tbody = $("<tbody />");

            arr_filter.push({tgldari:tgldari, tglsampai:tglsampai, id_dept:id_dept});

            $.each(data.record, function(key, value) {

              empty = false;
              func  = "detailDowntime('"+value.mc_id+"')";
              var tr = $("<tr>").append(
                $("<td>").html(no),
                $("<td>").html('<a href="javascript:void(0)" onclick="'+func+'">'+value.nama_mesin+'</a>'),
                $("<td align='right'>").text(value.downtime),
                $("<td align='right'>").text(value.downtime_2),
                $("<td align='right'>").text(value.uptime),
                $("<td align='right'>").text(value.uptime_2),
                $("<td align='right'>").text(value.dc),
                $("<td align='right'>").text(value.dct),
                $("<td align='right'>").text(value.dcr),

              );
              tbody.append(tr);
              no++;
            });

            if(empty == true) {
              var tr = $("<tr>").append($("<td colspan='9' align='center'>").text('Tidak ada Data'));
              tbody.append(tr);
            }

            $("#example1").append(tbody); // append parents

            this_btn.button('reset');
            $("#example1_processing").css('display', 'none'); // hidden loading

          },
          error: function(jqXHR, textStatus, errorThrown) {
            alert(jqXHR.responseText);
            //alert('error data');
            $("#example1_processing").css('display', 'none'); // hidden loading
            this_btn.button('reset');
          }
        });

    }

    function detailDowntime(id)
    {
        var tgldari   = $('#tgldari').val();
        var tglsampai = $('#tglsampai').val();
        var id_dept   = $('#departemen').val();

        $("#view_data").modal({
            show: true,
            backdrop: 'static'
        });
        $(".view_body").html('<center><h5><img src="<?php echo base_url('dist/img/ajax-loader.gif') ?> "/><br>Please Wait...</h5></center>');
        $('.modal-title').text('View Detail Downtime' );
        $.post('<?php echo site_url()?>report/downtime/view_detail_downtime',
            {id:id, tgldari:tgldari, tglsampai:tglsampai, id_dept:id_dept},
            function(html){
              setTimeout(function() {$(".view_body").html(html);  },1000);
            }   
         ); 
    }

    $('#btn-excel').click(function(){

      var tgldari   = $('#tgldari').val();
      var tglsampai = $('#tglsampai').val();
      var id_dept   = $('#departemen').val();

      var tgldari_2   = $('#tgldari').data("DateTimePicker").date();
      var tglsampai_2 = $('#tglsampai').data("DateTimePicker").date();

      if (tgldari == '' || tglsampai == '') {
        alert_modal_warning('Periode Tanggal Harus diisi !');
      }else if (id_dept == null) {
        alert_modal_warning('Departemen Harus diisi !');

      }else  if(tglsampai_2 < tgldari_2){ // cek validasi tgl sampai kurang dari tgl Dari
        alert_modal_warning('Maaf, Tanggal Sampai tidak boleh kurang dari Tanggal Dari !');

      }else{
        $.ajax({
            "type":'POST',
            "url": "<?php echo site_url('report/downtime/export_excel')?>",
            "dataType":'json',
            "data":  {
              arr_filter: arr_filter,
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