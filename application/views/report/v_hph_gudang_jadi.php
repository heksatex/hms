
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

    #pagination {
        display: inline-block;
        padding-left: 0;
        border-radius: 4px;
        /*padding-top: 5px;*/

    } 

    #pagination>a, #pagination>strong {
        position: relative;
        float: left;
        padding: 4px 8px;
        margin-left: -1px;
        line-height: 1.42857143;
        color: #337ab7;
        text-decoration: none;
        background-color: #fff;
        border: 1px solid #ddd;
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
          <h3 class="box-title"><b>HPH Gudang Jadi</b></h3>
        </div>
        <div class="box-body">
           
            <form name="input" class="form-horizontal" role="form" method="POST" id="frm_periode">
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
                <div class="col-md-4">
                    <div class="pull-right text-right">
                        <div id='pagination'></div>
                    </div>
                </div>
              <br>
              <br>
              <div class="col-md-12">
                   <div class="panel panel-default" style="margin-bottom: 0px;">
                    <div id="advancedSearch" class="panel-collapse collapse" role="tabpanel" aria-labelledby="advanced" >
                      <div class="panel-body" style="padding: 5px">
                        <div class="col-md-4" >
                            <div class="form-group">
                                <div class="col-md-5">
                                    <label>No.HPH </label>
                                </div>
                                <div class="col-md-7">
                                    <input type="text" class="form-control input-sm" name="no_hph" id="no_hph" >
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-5">
                                    <label>Lot (Bahan Baku)</label>
                                </div>
                                <div class="col-md-7">
                                    <input type="text" class="form-control input-sm" name="lot_bahan_baku" id="lot_bahan_baku" >
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
                                    <label>Corak Remark</label>
                                </div>
                                <div class="col-md-7">
                                    <input type="text" class="form-control input-sm" name="corak_remark" id="corak_remark" >
                                </div>
                            </div> 
                            <div class="form-group">
                                <div class="col-md-5">
                                    <label>Warna Remark</label>
                                </div>
                                <div class="col-md-7">
                                    <input type="text" class="form-control input-sm" name="warna_remark" id="warna_remark" >
                                </div>
                            </div> 
                            <div class="form-group">
                                <div class="col-md-5">
                                    <label>Quality </label>
                                </div>
                                <div class="col-md-7">
                                    <select type="text" class="form-control input-sm select2" name="quality" id="quality"  style="width:100% !important"> 
                                        <option value="">-- Pilih Quality --</option>
                                        <?php 
                                        foreach ($quality as $val) {
                                            echo "<option value='".$val->id."'>".$val->nama."</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-5">
                                    <label>Jenis Kain </label>
                                </div>
                                <div class="col-md-7">
                                    <select type="text" class="form-control input-sm select2" name="jenis_kain" id="jenis_kain"  style="width:100% !important"> 
                                        <option value="">-- Pilih Jenis Kain --</option>
                                        <?php 
                                        foreach ($jenis_kain as $val) {
                                            echo "<option value='".$val->id."'>".$val->nama_jenis_kain."</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            
                        </div>
                        <div class="col-md-4">
                            
                            <div class="form-group">
                                <div class="col-md-5">
                                    <label>Lot (Barang Jadi)</label>
                                </div>
                                <div class="col-md-7">
                                    <input type="text" class="form-control input-sm" name="lot_barang_jadi" id="lot_barang_jadi" >
                                </div>
                            </div> 
                            <div class="form-group">
                                <div class="col-md-5">
                                    <label>Lbr.Jadi</label>
                                </div>
                                <div class="col-md-7">
                                    <input type="text" class="form-control input-sm" name="lebar_jadi" id="lebar_jadi" >
                                </div>
                            </div> 
                            <div class="form-group">
                                <div class="col-md-5">
                                    <label>Benang</label>
                                </div>
                                <div class="col-md-7">
                                    <input type="text" class="form-control input-sm" name="benang" id="benang" >
                                </div>
                            </div> 
                            <div class="form-group">
                                <div class="col-md-5">
                                    <label>No Mesin </label>
                                </div>
                                <div class="col-md-7">
                                    <select type="text" class="form-control input-sm select2" name="mc" id="mc"  style="width:100% !important"> 
                                        <option value="">-- Pilih No Mesin --</option>
                                        <?php 
                                        foreach ($mesin as $val) {
                                            echo "<option value='".$val->mc_id."'>".$val->nama_mesin."</option>";
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
                                <label>Color Order </label>
                              </div>
                              <div class="col-md-7">
                                  <input type="text" class="form-control input-sm" name="color_order" id="color_order" >
                              </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-5">
                                    <label>Grade </label>
                                </div>
                                <div class="col-md-7">
                                    <select type="text" class="form-control input-sm select2" name="grade" id="grade" style="width:100% !important">
                                    <option>All</option>
                                    <option>A</option>
                                    <option>B</option>
                                    <option>C</option>
                                    <option>F</option>
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
                                    <option>SPLIT</option>
                                    <option>JOIN</option>
                                    <option>MANUAL</option>
                                    </select>
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
                              <th  class='style bb ws' style="min-width: 80px">No. HPH</th>
                              <th  class='style bb ws'>MC GJD</th>
                              <th  class='style bb ws' style="min-width: 150px">Lot(Bahan Baku)</th>
                              <th  class='style bb ws' style="min-width: 200px">Nama Produk</th>
                              <th  class='style bb ws text-right'>Qty1 Prod</th>
                              <th  class='style bb ws text-right'>Qty2 Prod</th>
                              <th  class='style bb ws'>Quality</th>
                              <th  class='style bb ws'>Tanggal.Proses </th>
                              <th  class='style bb ws' style="min-width: 150px">Lot(Barang Jadi)</th>
                              <th  class='style bb ws'>Corak Remark</th>
                              <th  class='style bb ws'>Warna Remark</th>
                              <th  class='style bb ws text-right'>Qty1 HPH</th>
                              <th  class='style bb ws text-right'>Qty2 HPH</th>
                              <th  class='style bb ws'>Grade</th>
                              <th  class='style bb ws'>L.Jadi</th>
                              <th  class='style bb ws'>Jenis Kain</th>
                              <th  class='style bb ws'>Gramasi</th>
                              <th  class='style bb ws'>Brt/mtr/pnl</th>
                              <th  class='style bb ws'>Benang</th>
                              <th  class='style bb ws text-right'>Qty1 Jual</th>
                              <th  class='style bb ws text-right'>Qty2 Jual</th>
                              <th  class='style bb ws'>Marketing</th>
                              <th  class='style bb ws'>SC </th>
                              <th  class='style bb ws'>CO </th>
                              <th  class='style bb ws style="min-width: 100px" '>Keterangan </th>
                              <th  class='style bb ws style="min-width: 100px" '>Operator </th>
                              <th  class='style bb ws' style="min-width: 100px" >Nama User</th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr>
                              <td colspan="29" >Tidak ada Data</td>
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
  $('.select2').select2({});

  var d     = new Date();
  var month = d.getMonth();
  var day   = d.getDate();
  var year  = d.getFullYear();

  // set date tgldari
  $('#tgldari').datetimepicker({
      defaultDate : new Date(year, month, day, 00, 00, 00),
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

        if(arr_filter.length == 0){ // cek validasi tgl sampai kurang dari tgl Dari
            alert_modal_warning('Silahkan Generate terlebih dahulu !');
        }else{
            $.ajax({
                "type"    :'POST',
                "url"     : "<?php echo site_url('report/HPHgudangjadi/export_excel_hph')?>",
                "data"    : {arr_filter:JSON.stringify(arr_filter) },
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

  var arr_filter    = [];

  // btn generate
  $("#btn-generate").on('click', function(){
        arr_filter    = [];
        tgldari   = $('#tgldari').val();
        tglsampai = $('#tglsampai').val();
        no_hph     = $('#no_hph').val();
        lot_bahan_baku     = $('#lot_bahan_baku').val();
        corak              = $('#corak').val();
        corak_remark       = $('#corak_remark').val();
        warna_remark       = $('#warna_remark').val();
        quality            = $('#quality').val();
        jenis_kain         = $('#jenis_kain').val();
        lot_barang_jadi    = $('#lot_barang_jadi').val();
        benang             = $('#benang').val();
        lebar_jadi         = $('#lebar_jadi').val();
        mc                 = $('#mc').val();
        color_order        = $('#color_order').val();
        sales_order        = $('#sales_order').val();
        user               = $('#user').val();
        jenis              = $('#jenis').val();
        grade              = $('#grade').val();
        tgldari_2 = $('#tgldari').data("DateTimePicker").date();
        tglsampai_2 = $('#tglsampai').data("DateTimePicker").date();

        var timeDiff = 0;
        if (tglsampai_2) {
            timeDiff = (tglsampai_2 - tgldari_2) / 1000; // 000 mengubah hasil milisecond ke bentuk second
        }
        selisih = Math.floor(timeDiff/(86400)); // 1 hari = 25 jam, 1 jam=60 menit, 1 menit= 60 second , 1 hari = 86400 second

        if(tgldari == '' || tglsampai == ''){
            alert_modal_warning('Periode Tanggal Harus diisi !');
        }else if(tglsampai_2 < tgldari_2){ // cek validasi tgl sampai kurang dari tgl Dari
             alert_modal_warning('Maaf, Tanggal Sampai tidak boleh kurang dari Tanggal Dari !');            
        }else if(selisih > 31 ){
            alert_modal_warning('Maaf, Periode Tanggal tidak boleh lebih dari 31 hari !')
        }else{  

            arr_filter.push({
                            tgldari:tgldari,
                            tglsampai:tglsampai,
                            no_hph:no_hph,
                            lot_bahan_baku:lot_bahan_baku,
                            corak:corak,
                            corak_remark:corak_remark,
                            warna_remark:warna_remark,
                            quality:quality,
                            jenis_kain:jenis_kain,
                            lot_barang_jadi:lot_barang_jadi,
                            benang:benang,
                            lebar_jadi:lebar_jadi,
                            mc:mc,
                            color_order:color_order,
                            sales_order:sales_order,
                            user:user,
                            grade:grade,
                            jenis:jenis,
            });

            loadSearchData();
            
        }
  });

   // next pagination
  $('#pagination').on('click','a',function(e){
      
      e.preventDefault(); 
      var pageNum = $(this).attr('data-ci-pagination-page');
      //alert(pageNum);
      loadSearchData(pageNum)

  });

  function loadSearchData(pageNum = null){

            if(pageNum == null){
                pageNum = 0;
            }

            $("#example1_processing").css('display','');// show loading processing in table
            $('#btn-generate').button('loading');
            $("#example1 tbody").remove();
            $.ajax({
                type: "POST",
                dataType : "JSON",
                // url : "<?php echo site_url('report/HPHgudangjadi/loadData')?>",
                url : '<?=base_url()?>report/HPHgudangjadi/loadData/'+pageNum,
                data : {arr_filter:JSON.stringify(arr_filter) },
                success: function(data){

                  if(data.status == 'failed'){
                    $('#total_record').html('Total Data : 0');
                    alert_modal_warning(data.message);
                  }else{

                    $('#total_record').html(data.total_record);
                    $('#pagination').html(data.pagination);

                    let tbody = $("<tbody />");
                    let no    = 1;
                    let empty = true;

                    $.each(data.record, function(key, value){
                        empty = false;


                        var tr = $("<tr>").append(
                                 $("<td>").text(no++),
                                 $("<td>").text(value.no_hph),
                                 $("<td>").text(value.nama_mesin),
                                 $("<td>").text(value.lot),
                                 $("<td>").text(value.nama_produk),
                                 $("<td align='right'>").text(value.qty_prod+' '+value.uom_prod),
                                 $("<td align='right'>").text(value.qty2_prod+' '+value.uom2_prod),
                                 $("<td>").text(value.nama_quality),
                                 $("<td>").text(value.tgl_hph),
                                 $("<td>").text(value.lot2),
                                 $("<td>").text(value.corak_remark),
                                 $("<td>").text(value.warna_remark),
                                 $("<td align='right'>").text(value.qty1_hph+' '+value.uom_hph),
                                 $("<td align='right'>").text(value.qty2_hph+' '+value.uom2_hph),
                                 $("<td>").text(value.grade),
                                 $("<td>").text(value.lbr_jadi+' '+value.uom_lbr_jadi),
                                 $("<td>").text(value.jenis_kain),
                                 $("<td>").text(value.gramasi),
                                 $("<td>").text(value.berat),
                                 $("<td>").text(value.benang),
                                 $("<td align='right'>").text(value.qty1_jual+' '+value.uom_jual),
                                 $("<td align='right'>").text(value.qty2_jual+' '+value.uom2_jual),
                                 $("<td>").text(value.marketing),
                                 $("<td>").text(value.sc),
                                 $("<td>").text(value.co),
                                 $("<td>").text(value.keterangan),
                                 $("<td>").text(value.operator),
                                 $("<td>").text(value.nama_user),
                        );
                        tbody.append(tr);
                    });
                    if(empty == true){
                      var tr = $("<tr>").append($("<td colspan='29' >").text('Tidak ada Data'));
                      tbody.append(tr);
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

</script>

</body>
</html>
