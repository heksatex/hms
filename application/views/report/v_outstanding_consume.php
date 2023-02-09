
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
          <h3 class="box-title"><b>Outstanding Consume</b></h3>
        </div>
        <div class="box-body">
           
            <form name="input" class="form-horizontal" role="form" method="POST" id="frm_periode" action="#">
              <div class="col-md-6">
                <div class="form-group">
                    <div class="col-md-12"> 
                        <div class="col-md-5">
                            <label>Departemen </label>
                        </div>
                        <div class="col-md-7">
                            <select type="text" class="form-control input-sm" name="departemen" id="departemen"  style="width:100% !important">
                                <option value="">-- Pilih Departemen --</option>
                                    <?php 
                                        foreach ($warehouse as $val) {
                                            echo "<option value='".$val->kode."'>".$val->nama."</option>";
                                        }
                                    ?>
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
                      <input type="radio" id="view" name="view[]" value="Global" checked="checked" >
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
                <button type="button" class="btn btn-sm btn-default" name="btn-excel" id="btn-excel" > <i class="fa fa-file-excel-o" style="color:green"></i> Excel</button>
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
                                <label>Kode</label>
                              </div>
                              <div class="col-md-7">
                                <input type="text" class="form-control input-sm" name="kode" id="kode">
                              </div>
                            </div>
                            <div class="form-group">
                              <div class="col-md-5">
                                <label>Nama Produk </label>
                              </div>
                              <div class="col-md-7">
                                <input type="text" class="form-control input-sm" name="corak" id="corak" placeholder="Corak / Nama Produk">
                              </div>
                            </div>
                          </div>
                          <div class="col-md-5">
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
                                    <th  class='style bb' style="min-width: 80px">Kode</th>
                                    <th  class='style bb'>Origin</th>
                                    <th  class='style bb nowrap'>Kode Produk</th>
                                    <th  class='style bb' style="min-width: 150px">Nama Produk</th>
                                    <th  class='style bb' style="min-width: 80px">Target Qty</th>
                                    <th  class='style bb nowrap' id="head_lot"> Total Lot</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                    <td colspan="7" align="center">Tidak ada Data</td>
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

  // disable enter
  $(window).keydown(function(event){
    if(event.keyCode == 13) {
      event.preventDefault();
      return false;
    }
  });

  $('#departemen').select2({});
  $('#dept_dari').select2({});

  // cek selisih saatu submit excel
  $('#frm_periode').submit(function(){

    var departemen = $('#departemen').val();

    if(departemen == '') {
        alert_modal_warning('Departemen Harus Dipilih !');
        return false;
    }

  });

  // btn generate
  $("#btn-generate").on('click', function(){

      var departemen = $('#departemen').val();
      var kode       = $('#kode').val();
      var corak      = $('#corak').val();
      var lot        = $('#lot').val();

      var radio_view= false;
      var radio_arr = new Array(); 

      var radio_arr = $('input[name="view[]"]').map(function(e, i) {
            if(this.checked == true){
                radio_view = true;
                return i.value;
            }

      }).get();

      if(departemen == '') {
        alert_modal_warning('Departemen Harus Dipilih !');
      }else if(radio_view == '') {
        alert_modal_warning('View Harus Dipilih !');

      }else{  
          $("#example1_processing").css('display',''); // show loading

          $('#btn-generate').button('loading');
          $("#example1 tbody").remove();
          $.ajax({
                type: "POST",
                dataType : "JSON",
                url : "<?php echo site_url('report/outstandingconsume/loadData')?>",
                data: {departemen:departemen, kode:kode, corak:corak, lot:lot, view_arr:radio_arr},
                success: function(data){

                  if(data.status == 'failed'){
                    $('#total_record').html('Total Data : 0');
                    alert_modal_warning(data.message);

                  }else{

                    $('#total_record').html(data.total_record);
                    if(data.view == 'Global'){
                      

                        $("#example1 thead tr ").remove();

                        var thead_tr   = '<tr>';
                            thead_tr  += '<th class="style bb no">No</th>';
                            thead_tr  += '<th class="style bb"  style="min-width: 80px">Kode</th>';
                            thead_tr  += '<th class="style bb">Origin</th>';
                            thead_tr  += '<th class="style bb nowrap">Kode Produk</th>';
                            thead_tr  += '<th class="style bb" style="min-width: 150px">Nama Produk</th>';
                            thead_tr  += '<th class="style bb" style="min-width: 80px">Target Qty</th>';
                            thead_tr  += '<th class="style bb nowrap" id="head_lot" >Total Lot</th>';
                            thead_tr  += '</tr>';


                        $("#example1 thead ").append(thead_tr);

                        $('#head_lot').html('Total Lot');
                        width_lot = "style='min-width: 50px !important; text-align:right;' ";

                    }else{
                        $('#head_lot').html('Lot');;
                        width_lot = "style='min-width: 150px !important'";

                        var thead     = '';
                        var thead_tr   = '<th class="style bb" style="min-width: 80px">Qty1</th>';
                            thead_tr  += '<th class="style bb" style="min-width: 80px">Qty2</th>';
                            thead_tr  += '<th class="style bb" style="min-width: 80px">Grade</th>';
                            thead_tr  += '<th class="style bb" style="min-width: 80px">Reff Note</th>';

                        $("#example1 thead tr:last").append(thead_tr);
                    }

                    let tbody = $("<tbody />");
                    let no    = 1;
                    let empty = true;
                    let link  = '';

                    if(radio_arr == "Global"){

                        $.each(data.record, function(key, value){
                            empty = false;
                            link = '<a href="<?=base_url()?>manufacturing/mO/edit/'+value.kode_enc+'" data-toggle="tooltip" title="Lihat MO" target="_blank">'+value.kode+'</a>'
                            var tr = $("<tr>").append(
                                    $("<td>").text(no++),
                                    $("<td>").html(link),
                                    $("<td>").text(value.origin),
                                    $("<td>").text(value.kode_produk),
                                    $("<td>").text(value.nama_produk),
                                    $("<td>").text(value.qty),
                                    $("<td class='nowrap' "+width_lot+">").text(value.lot),
                            );
                            tbody.append(tr);
                        });

                    }else{

                        $.each(data.record, function(key, value){
                            empty = false;
                            link = '<a href="<?=base_url()?>manufacturing/mO/edit/'+value.kode_enc+'" data-toggle="tooltip" title="Lihat MO" target="_blank">'+value.kode+'</a>'
                            var tr = $("<tr>").append(
                                    $("<td>").text(no++),
                                    $("<td>").html(link),
                                    $("<td>").text(value.origin),
                                    $("<td>").text(value.kode_produk),
                                    $("<td>").text(value.nama_produk),
                                    $("<td>").text(value.qty),
                                    $("<td class='nowrap' "+width_lot+">").text(value.lot),
                                    $("<td align='right'>").text(value.qty1),
                                    $("<td align='right'>").text(value.qty2),
                                    $("<td>").text(value.grade),
                                    $("<td>").text(value.reff_note),
                            );
                            tbody.append(tr);
                        });

                    }


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
                  $("#example1_processing").css('display','none'); // hidden loading
                }
          });
      }
  });

  $('#btn-excel').click(function(){

      var departemen = $('#departemen').val();
      var kode       = $('#kode').val();
      var corak      = $('#corak').val();

      var radio_view= false;
      var radio_arr = new Array(); 

      var radio_arr = $('input[name="view[]"]').map(function(e, i) {
            if(this.checked == true){
                radio_view = true;
                return i.value;
            }

      }).get();

      if(departemen == '') {
        alert_modal_warning('Departemen Harus Dipilih !');
      }else if(radio_view == '') {
        alert_modal_warning('View Harus Dipilih !');
      }else{

        $.ajax({
            "type":'POST',
            "url": "<?php echo site_url('report/outstandingconsume/export_excel')?>",
            "data": {departemen:departemen, kode:kode, corak:corak,view_arr:radio_arr},
            "dataType":'json',
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