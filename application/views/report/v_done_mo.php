
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
      max-height: calc( 100vh - 250px );
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
          <h3 class="box-title"><b>Done MO</b></h3>
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
                    <div class="col-md-4">
                      <label>
                          <div id='total_record'>Total Data : 0</div>
                      </label>
                    </div>
                   
                  </div>
                </div>
              </div>
              <div class="col-md-4">
                <button type="button" class="btn btn-sm btn-default" name="btn-generate" id="btn-generate" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing..."> Generate</button>
                <button type="button" class="btn btn-sm btn-default" name="btn-excel" id="btn-excel" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing..."> <i class="fa fa-file-excel-o " style="color:green"></i> Excel</button>
              </div>
            
            </form>

            <!-- table -->
            <div class="box-body">
              <div class="col-sm-12 table-responsive">
                  <div class="divListviewHead">
                    <div role="region" aria-labelledby="HeadersCol" tabindex="0" class="rowheaders">
                      <table id="example1" class="table table-condesed table-hover" border="1">
                          <thead>
                            <tr>
                              <th  class="style bb no" rowspan="3">No. </th>
                              <th  class='style bb' rowspan="3">MO</th>
                              <th  class='style bb' style="min-width: 80px" rowspan="3">Tgl.MO</th>
                              <th  class='style bb' rowspan="3">Departemen</th>
                              <th  class='style bb nowrap text-center' rowspan="2" colspan = "2" >Consume Bahan Baku</th>
                              <th  class='style bb nowrap text-center' colspan = "10">Barang Jadi</th>
                              <th  class='style bb' rowspan="3">Status</th>
                            </tr>
                            <tr>
                                <th  class="style bb nowrap text-center" colspan = "2">Produce</th>
                                <th  class="style bb nowrap text-center" colspan = "2">Waste</th>
                                <th  class="style bb nowrap text-center" colspan = "2">Adjustment</th>
                                <th  class="style bb nowrap text-center" colspan = "2">Total</th>
                                <th  class="style bb nowrap text-center" colspan = "2">Adjustment Terbaru</th>
                            </tr>
                            <tr>  
                                <th  class="style bb nowrap">Mtr</th>
                                <th  class="style bb nowrap">Kg</th>
                                <th  class="style bb nowrap">Mtr</th>
                                <th  class="style bb nowrap">Kg</th>
                                <th  class="style bb nowrap">Mtr</th>
                                <th  class="style bb nowrap">Kg</th>
                                <th  class="style bb nowrap">Mtr</th>
                                <th  class="style bb nowrap">Kg</th>
                                <th  class="style bb nowrap">Mtr</th>
                                <th  class="style bb nowrap">Kg</th>
                                <th  class="style bb nowrap">Mtr</th>
                                <th  class="style bb nowrap">Kg</th>
                            </<tr>
                          </thead>
                          <tbody>
                            <tr>
                              <td colspan="17" align="left">Tidak ada Data</td>
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
        
        defaultDate : new Date(year, month, day, 00, 00, 00),
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


    // btn excel
    $('#btn-excel').click(function(){

        tgldari    = $('#tgldari').val();
        tglsampai  = $('#tglsampai').val();
        departemen = $('#departemen').val();
        tgldari_2     = $('#tgldari').data("DateTimePicker").date();
        tglsampai_2   = $('#tglsampai').data("DateTimePicker").date();

       
        if(tglsampai_2 < tgldari_2){ // cek validasi tgl sampai kurang dari tgl Dari
            alert_modal_warning('Maaf, Tanggal Sampai tidak boleh kurang dari Tanggal Dari !');
        }else if (departemen == null) {
            alert_modal_warning('Departemen Harus diisi !');
        }else{
        $.ajax({
            "type":'POST',
            "url" : "<?php echo site_url('report/doneMO/export_excel')?>",
            "data": {tgldari:tgldari, tglsampai:tglsampai, departemen:departemen },
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
        tgldari_2     = $('#tgldari').data("DateTimePicker").date();
        tglsampai_2   = $('#tglsampai').data("DateTimePicker").date();

        if(tgldari == '' || tglsampai == ''){
            alert_modal_warning('Periode Tanggal Harus diisi !');

        }else if (departemen == null) {
            alert_modal_warning('Departemen Harus diisi !');

        }else if(tglsampai_2 < tgldari_2){ // cek validasi tgl sampai kurang dari tgl Dari
            alert_modal_warning('Maaf, Tanggal Sampai tidak boleh kurang dari Tanggal Dari !');

        }else{  
            $("#example1_processing").css('display',''); // show loading

            $('#btn-generate').button('loading');
            $("#example1 tbody").remove();
            $.ajax({
                    type: "POST",
                    dataType : "JSON",
                    url : "<?php echo site_url('report/doneMO/loadData')?>",
                    data: {tgldari:tgldari, tglsampai:tglsampai, departemen:departemen},
                    success: function(data){

                    if(data.status == 'failed'){
                        $('#total_record').html('Total Data : 0');
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
                                    $("<td>").text(value.kode),
                                    $("<td>").text(value.tanggal),
                                    $("<td>").text(value.departemen),
                                    $("<td align='right'>").text(value.cons_qty1),
                                    $("<td align='right'>").text(value.cons_qty2),
                                    $("<td align='right'>").text(value.prod_qty1),
                                    $("<td align='right'>").text(value.prod_qty2),
                                    $("<td align='right'>").text(value.waste_qty1),
                                    $("<td align='right'>").text(value.waste_qty2),
                                    $("<td align='right'>").text(value.adj_qty1),
                                    $("<td align='right'>").text(value.adj_qty2),
                                    $("<td align='right'>").text(value.total_qty1),
                                    $("<td align='right'>").text(value.total_qty2),
                                    $("<td align='right'>").text(value.adj_ril_qty1),
                                    $("<td align='right'>").text(value.adj_ril_qty2),
                                    $("<td>").text(value.status),
                            );
                            tbody.append(tr);
                        });
                        if(empty == true){
                            var tr = $("<tr>").append($("<td colspan='17' align='left'>").text('Tidak ada Data'));
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
                        ("#example1_processing").css('display','none'); // hidden loading
                    }
            });
        }
    });

</script>

</body>
</html>
