
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

    .white-space-nowrap {
      white-space: nowrap !important;
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

    #pagination2 {
        display: inline-block;
        padding-left: 0;
        border-radius: 4px;
        /*padding-top: 5px;*/

    } 

    #pagination2>a, #pagination2>strong {
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
          <h3 class="box-title"><b>Mutasi</b></h3>
        </div>
        <div class="box-body">
           
            <form name="input" class="form-horizontal" role="form" method="POST" id="frm_mutasi" action="<?=base_url()?>report/mutasi/export_excel_mutasi">
              <div class="col-md-8">
                <div class="form-group" >
                    <div class="col-md-12"> 
                        <div class="col-md-2">
                            <label>Periode </label>
                        </div>
                        <div class="col-md-4">
                            <div class='input-group'>
                                <input type="text" class="form-control input-sm" name="tanggal" id="tanggal" required="">
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span>    
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label>Departemen </label>
                        </div>
                        <div class="col-md-4">
                            <select type="text" class="form-control input-sm" name="departemen" id="departemen" required="">
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-group" >
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
              </div>
              <div class="col-md-4">
                <button type="button" class="btn btn-sm btn-default" name="btn-generate" id="btn-generate" >Generate</button>
                <button type="submit" class="btn btn-sm btn-default" name="btn-excel" id="btn-excel" > <i class="fa fa-file-excel-o" style="color:green"></i> Excel</button>
                <button type="button" class="btn btn-sm btn-default" name="btn-pdf" id="print-bap" > <i class="fa fa-file-pdf-o" style="color:red"></i> BAP Mutasi</button>
              </div>
              <div class="col-md-12">
                <div class="form-group" style="margin-bottom: 0px;">
                  <div class="col-md-12">
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
              <div class="col-md-12">
                   <div class="panel panel-default" style="margin-bottom: 0px;">
                    <div id="advancedSearch" class="panel-collapse collapse" role="tabpanel" aria-labelledby="advanced" >
                      <div class="panel-body" style="padding: 5px">
                        <div class="form-group col-md-12" style="margin-bottom:0px">
                          <div class="col-md-4" >
                            <div class="form-group">
                              <div class="col-md-5">
                                <label>Kode Produk </label>
                              </div>
                              <div class="col-md-7">
                                  <input type="text" class="form-control input-sm" name="kode_produk" id="kode_produk" >
                              </div>
                            </div>
                            <div class="form-group">
                              <div class="col-md-5">
                                <label>Nama Produk </label>
                              </div>
                              <div class="col-md-7">
                                  <input type="text" class="form-control input-sm" name="nama_produk" id="nama_produk" >
                              </div>
                            </div> 
                           
                          </div>
                          <div class="col-md-4">
                            <div class="form-group">
                              <div class="col-md-5">
                                <label>Kode Transaksi </label>
                              </div>
                              <div class="col-md-7">
                                  <input type="text" class="form-control input-sm" name="kode_transaksi" id="kode_transaksi" placeholder="">
                              </div>
                            </div>
                            <div class="form-group">
                              <div class="col-md-5">
                                <label>Lot </label>
                              </div>
                              <div class="col-md-7">
                                  <input type="text" class="form-control input-sm" name="lot" id="lot" >
                              </div>
                            </div>
                          </div>
                          <div class="col-md-4">
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
                <div class="form-group">
                    <div class="col-md-12">
                        <label>
                            <div id='info_table1'></div>
                        </label>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-6">
                        <label>
                            <div id='total_record'>Total Data : 0</div>
                        </label>
                    </div>
                    <div class="col-md-6">
                        <div class="pull-right text-right">
                          <div id='pagination'></div>
                        </div>
                    </div>
                </div>
                <div class="table_scroll">
                  <div class="table_scroll_head">
                    <div class="divListviewHead">
                        <table id="example1" class="table table-condesed table-hover" border="0">
                            <thead>
                              <tr>
                                <th  class="style no" >No. </th>
                                <th  class='style' style="min-width: 80px">Kode Produk</th>
                                <th  class='style' style="min-width: 80px">Nama Produk</th>
                                <th  class='style' style="min-width: 150px">Saldo Awal</th>
                                <th  class='style' style="min-width: 150px">Saldo Akhir</th>
                              </tr>
                            </thead>
                            <tbody>
                              <tr>
                                <td colspan="5" align="center">Tidak ada Data</td>
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

              <div class="col-sm-12 table-responsive" style="display: none;" id="table_2">
                <div class="form-group">
                      <div class="col-md-12">
                          <label>
                              <div id='info_table2'></div>
                          </label>
                      </div>
                  </div>
                  <div class="form-group">
                      <div class="col-md-6">
                          <label>
                              <div id='total_record2'>Total Data : 0</div>
                          </label>
                      </div>
                      <div class="col-md-6">
                          <div class="pull-right text-right">
                            <div id='pagination2'></div>
                          </div>
                    </div>
                </div>
                <div class="table_scroll">
                  <div class="table_scroll_head">
                    <div class="divListviewHead">
                        <table id="example2" class="table table-condesed table-hover" border="0" >
                            <thead>
                              <tr>
                                <th  class="style no" >No. </th>
                                <th  class='style' style="min-width: 80px">Kode Produk</th>
                                <th  class='style' style="min-width: 80px">Nama Produk</th>
                                <th  class='style' style="min-width: 150px">Saldo Awal</th>
                                <th  class='style' style="min-width: 150px">Saldo Akhir</th>
                              </tr>
                            </thead>
                            <tbody>
                              <tr>
                                <td colspan="5" align="center">Tidak ada Data</td>
                              </tr>
                            </tbody>
                        </table>
                        <div id="example2_processing" class="table_processing" style="display: none">
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

  //* Show collapse advanced search
  $('#advancedSearch').on('shown.bs.collapse', function () {
      $(".showAdvanced").removeClass("glyphicon-triangle-bottom").addClass("glyphicon-triangle-top");
  });

  //* Hide collapse advanced search
  $('#advancedSearch').on('hidden.bs.collapse', function () {
    $(".showAdvanced").removeClass("glyphicon-triangle-top").addClass("glyphicon-triangle-bottom");
  });

  var d     = new Date();
  var month = d.getMonth()-1;
  var day   = d.getDate();
  var day_1 = d.getDate()-1;
  var year  = d.getFullYear();

  // set date tgldari
  $('#tanggal').datetimepicker({
      
    //   defaultDate : new Date(year, month, day_1, 07, 00, 00),
    defaultDate : new Date(year, month),
    format : 'MMMM YYYY',
    ignoreReadonly: true,
    //maxDate: new Date(year, month),
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



  // cek selisih saatu submit excel
  $('#frm_mutasi').submit(function(){

    tanggal    = $('#tanggal').val();
    departemen = $('#departemen').val();

    if(tanggal == ''){ 
      alert_modal_warning('Periode Tanggal Harus diisi!');
      return false;
    }else if (departemen == null) {
      alert_modal_warning('Departemen Harus diisi !');
      return false;
    }

  });

  // next pagination
  $('#pagination').on('click','a',function(e){
      
      e.preventDefault(); 
      var pageNum = $(this).attr('data-ci-pagination-page');
      //alert(pageNum);
      $("#example1 tbody").remove();
      loadSearchData(pageNum,'rm')

  });

   // next pagination 2
   $('#pagination2').on('click','a',function(e){
      
      e.preventDefault(); 
      var pageNum = $(this).attr('data-ci-pagination-page');
      //alert(pageNum);
      $("#example2 tbody").remove();
      loadSearchData(pageNum,'fg')

  });

  $("#btn-generate").on('click', function(){
    loadSearchData();
    $("#example1 tbody").remove();
    $("#example2 tbody").remove();
       
  });


  var arr_filter = [];

  // load Data
  function loadSearchData(pageNum=null,table=null){

      if(pageNum == null){
          pageNum = 0;
      }
      if(table == null){
        table  = "all";
      }

      arr_filter = [];

      tanggal         = $('#tanggal').val();
      departemen      = $('#departemen').val();
      kode_produk     = $('#kode_produk').val();
      nama_produk     = $('#nama_produk').val();
      kode_transaksi  = $('#kode_transaksi').val();
      lot             = $('#lot').val();

      var radio_view= false;
      var radio_arr = new Array(); 

      var radio_arr = $('input[name="view[]"]').map(function(e, i) {
            if(this.checked == true){
                check_status = true;
              return i.value;
            }

      }).get();

      if(tanggal == '' ){
        alert_modal_warning('Periode Tanggal Harus diisi !');

      }else if (departemen == null) {
        alert_modal_warning('Departemen Harus diisi !');

      }else{
          $("#example1_processing").css('display',''); // show loading
          if(table == 'all' || table =='fg'){
            $("#example2_processing").css('display',''); // show loading
          }

          $('#btn-generate').button('loading');
          //$("#example1 tbody").remove();
          //$("#example2 tbody").remove();
       
          // push array
          arr_filter.push({tanggal:tanggal, departemen:departemen, kode_produk:kode_produk, nama_produk:nama_produk, kode_transaksi:kode_transaksi, lot:lot, view_arr:radio_arr});
          $.ajax({
                type: "POST",
                dataType : "JSON",
                url : '<?=base_url()?>report/mutasi/loadData/'+pageNum,
                data: {arr_filter:arr_filter, table:table },
                success: function(data){

                  if(data.view == 'Global'){
                    $('#pagination').html('');
                    $('#pagination2').html('');
                  }

                  if(data.status == 'failed'){
                    alert_modal_warning(data.message);
                    let tbody = $("<tbody />");
                    $('#info_table1').html('');
                    $('#info_table2').html('');
                    $('#total_record').html('Total Data : 0');
                    $('#total_record2').html('Total Data : 0');
                    row = $("<tr>").append($("<td colspan='12' align='center'>").text('Tidak ada Data'));
                    tbody.append(row);
                    $('#example1').append(tbody);
                    $('#example2').append(tbody);
                  }else{
                    if(table == 'all' || table == 'rm'){
                      $("#example1 thead").remove();
                    }
                    if(table == 'all' || table == 'fg'){
                      $("#example2 thead").remove();
                    }
                    $.each(data.result, function(key, value){ 
                      if(value.table_1 == "Yes"){
                        if(data.view == "Global"){
                          create_table('example1', value.head_table1,value.head_table2,value.record,value.count_in,value.count_out);
                        }else{
                          create_table_detail('example1', value.head_table,value.record);
                        }
                        $('#info_table1').html('');
                        $('#total_record').html('Total Data : '+value.count_record);
                        $('#pagination').html(value.pagination);
                      }

                      if(value.table_2 == "Yes"){
                        if(data.view == "Global"){
                          create_table('example2',value.head_table1,value.head_table2,value.record,value.count_in,value.count_out);
                        }else{
                          create_table_detail('example2', value.head_table,value.record);
                        }

                        $('#table_2').css('display','');
                        $('#info_table1').html('Mutasi Bahan Baku');
                        $('#info_table2').html('Mutasi Barang Jadi');
                        $('#total_record2').html('Total Data : '+value.count_record);
                        $('#pagination2').html(value.pagination);
                      }else if(value.table_2 == "No"){
                        $('#info_table1').html('');
                        $('#info_table2').html('');
                        $('#table_2').css('display','none');
                        $('#total_record2').html('Total Data : '+value.count_record);
                        $('#pagination2').html(value.pagination);
                      }
                    });
                  }
                    
                $('#btn-generate').button('reset');
                $("#example1_processing").css('display','none'); // hidden loading
                $("#example2_processing").css('display','none'); // show loading

                },error : function(jqXHR, textStatus, errorThrown){
                  alert(jqXHR.responseText);
                  //alert('error data');
                  $("#example1_processing").css('display','none'); // hidden loading
                  $("#example2_processing").css('display','none'); // show loading
                  $('#btn-generate').button('reset');
                }
          });
      }
  }

  function create_table_detail(tableId,head_table,body){
                    let thead = $("<thead />");
                    let row   = '';

                    for ( var i = 0, l = head_table.length; i < l; i++ ) {
                        row += "<th class='style white-space-nowrap' >";
                        row += head_table[i];
                        row += "</th>";
                    }
                    tr = $("<tr>").append(row);
                    thead.append(tr)
                    $('#'+tableId).append(thead);

                    let no    = 1;
                    let row2  = '';
                    let tbody = $("<tbody />");
                    $.each(body, function(key, value){ 

                        if(value.type == 'in'){
                          dept  = value.dept_id_dari;
                        }else{ 
                          dept  = value.dept_id_tujuan;
                        }

                        row2 += "<tr>";
                        row2 += "<td>"+ (no++) +"</td>";
                        row2 += "<td class='white-space-nowrap'>"+value.posisi_mutasi+"</td>";
                        row2 += "<td class='white-space-nowrap'>"+dept+"</td>";
                        row2 += "<td class='white-space-nowrap'>"+value.type+"</td>";
                        row2 += "<td class='white-space-nowrap'>"+value.kode_transaksi+"</td>";
                        row2 += "<td class='white-space-nowrap'>"+value.tanggal_transaksi+"</td>";
                        row2 += "<td class='white-space-nowrap'>"+value.kode_produk+"</td>";
                        row2 += "<td class='white-space-nowrap'>"+value.nama_produk+"</td>";
                        row2 += "<td class='white-space-nowrap'>"+value.nama_category+"</td>";
                        row2 += "<td class='white-space-nowrap'>"+value.lot+"</td>";
                        row2 += "<td class='white-space-nowrap' align='right'>"+formatNumber(value.qty)+" "+value.uom+"</td>";
                        row2 += "<td class='white-space-nowrap' align='right'>"+formatNumber(value.qty2)+" "+value.uom2+"</td>";
                        row2 += "<td class='white-space-nowrap' align='right'>"+formatNumber(value.qty_opname)+" "+value.uom_opname+"</td>";
                        row2 += "<td class='white-space-nowrap'>"+value.origin+"</td>";
                        row2 += "<td class='white-space-nowrap'>"+value.method+"</td>";
                        row2 += "<td class='white-space-nowrap'>"+value.sc+"</td>";
                        row2 += "<td class='white-space-nowrap'>"+value.nama_sales_group+"</td>";

                        row2 += "</tr>";
                    });
                    if(body.length == 0){
                      row2 = $("<tr>").append($("<td colspan='12' align='center'>").text('Tidak ada Data'));
                    }

                    tbody.append(row2);
                    $('#'+tableId).append(tbody);
          return

  }

  function create_table(tableId,head_table1,head_table2,body,count_in,count_out){
                    let thead = $("<thead />");
                    let row   = '';
                    let row2  = '';

                    $.each(head_table1, function(key, value){ // loop
                        $.each(value, function(a, b){
                            if(a == 'info'){
                                for ( var i = 0, l = b.length; i < l; i++ ) {
                                    $.each(b[i], function(c, d){
                                        row += "<th class='style no white-space-nowrap'  rowspan='2'>";
                                        row += d;
                                        row += "</th>";
                                    });
                                }
                            }else if(a == 'awal' || a == 'akhir' || a == 'in' || a== 'out' || a == 'adj_in' || a == 'adj_out' || a == 'count_in' || a == 'count_out'){
                              
                                for ( var i = 0, l = b.length; i < l; i++ ) {
                                    $.each(b[i], function(c, d){
                                        row += "<th class='style no text-center'  colspan='4' >";
                                        row += d;
                                        row += "</th>";
                                    });
                                    /*
                                    if(a == 'in'  ){
                                        var x = data.count_in;
                                    }else if( a == 'out'){
                                        var x = data.count_out;
                                    }else{
                                        var x = 1;
                                    }
                                    for ( var z = 0; z < x; z++) {
                                        $.each(b[i], function(c, d){
                                            row += "<th class='style no text-center'  colspan='4' >";
                                            row += d;
                                            row += "</th>";
                                        });
                                    } 
                                    */
                                }
                            }

                        });
                    });
                    tr = $("<tr>").append(row);
                    thead.append(tr)
                    $.each(head_table2, function(key, value){ //loop head table
                        $.each(value, function(a, b){ // loop jenis awal, in, out, adj, akhir
                                for ( var i = 0, l = b.length; i < l; i++ ) {
                                    $.each(b[i], function(c, d){
                                        row2 += "<th class='style white-space-nowrap'>";
                                        row2 += d;
                                        row2 += "</th>";
                                    });
                                }

                        });
                    });
                    tr2  = $("<tr>").append(row2);
                    thead.append(tr2)
                    $('#'+tableId).append(thead);

                    let tbody = $("<tbody />");
                    let row3  = '';
                    let no    = 1;
                  
                    $.each(body, function(key, value){ // loop body

                          // in 
                          let d = 0;
                          let no_in = 1;
                          let in_total_lot         = 0
                          let in_total_qty1        = 0;
                          let in_total_qty2        = 0;
                          let in_total_qty_opname  = 0;
                          let qty1_uom             = '';
                          let qty2_uom             = '';
                          let qty_opname_uom       = '';
                          let in_empty             = true;

                          let x = 0;
                          let no_out= 1;
                          let out_empty    = true;
                          let out_total_lot   = 0
                          let out_total_qty1        = 0;
                          let out_total_qty2        = 0;
                          let out_total_qty_opname  = 0;

                          row3 += "<tr>";
                          row3 += "<td>"+ (no++) +"</td>";
                          row3 += "<td class='white-space-nowrap'>"+value.kode_produk+"</td>";
                          row3 += "<td class='white-space-nowrap'>"+value.nama_produk+"</td>";
                          // saldo awal
                          row3 += "<td class='white-space-nowrap' align='right'>"+formatNumber(value.s_awal_lot)+"</td>";
                          row3 += "<td class='white-space-nowrap' align='right'>"+formatNumber(value.s_awal_qty1)+" "+value.s_awal_qty1_uom+"</td>";
                          row3 += "<td class='white-space-nowrap' align='right'>"+formatNumber(value.s_awal_qty2)+" "+value.s_awal_qty2_uom+"</td>";
                          row3 += "<td class='white-space-nowrap' align='right'>"+formatNumber(value.s_awal_qty_opname)+" "+value.s_awal_qty_opname_uom+"</td>";

                          // info uom in 1
                          if(value.s_awal_qty1_uom != ''){
                            qty1_uom        = value.s_awal_qty1_uom;
                          }else{
                            qty1_uom        = '';
                          }
                          if(value.s_awal_qty2_uom != ''){
                            qty2_uom        = value.s_awal_qty2_uom;
                          }else{
                            qty2_uom        = '';
                          }
                          if( value.s_awal_qty_opname_uom != ''){
                            qty_opname_uom  = value.s_awal_qty_opname_uom;
                          }else{
                            qty_opname_uom  = '';
                          }

                          for (  n = count_in; d < n; d++ ) {
                            in_lot         = 'in'+no_in+'_lot';
                            in_qty1        = 'in'+no_in+'_qty1';
                            in_qty1_uom    = 'in'+no_in+'_qty1_uom';
                            in_qty2        = 'in'+no_in+'_qty2';
                            in_qty2_uom    = 'in'+no_in+'_qty2_uom';
                            in_opname      = 'in'+no_in+'_qty_opname';
                            in_opname_uom  = 'in'+no_in+'_qty_opname_uom';
                            row3 += "<td class='white-space-nowrap' align='right'>"+formatNumber(value[in_lot])+"</td>";
                            row3 += "<td class='white-space-nowrap' align='right'>"+formatNumber(value[in_qty1])+" "+value[in_qty1_uom]+"</td>";
                            row3 += "<td class='white-space-nowrap' align='right'>"+formatNumber(value[in_qty2])+" "+value[in_qty2_uom]+"</td>";
                            row3 += "<td class='white-space-nowrap' align='right'>"+formatNumber(value[in_opname])+" "+value[in_opname_uom]+"</td>";
                            no_in++;
                            // info total++ in
                            in_total_lot         = in_total_lot+parseInt(value[in_lot]);
                            in_total_qty1        = in_total_qty1+parseFloat(value[in_qty1]);
                            in_total_qty2        = in_total_qty2+parseFloat(value[in_qty2]);
                            in_total_qty_opname  = in_total_qty_opname+parseFloat(value[in_opname]);

                            //info uom
                            if(value[in_qty1_uom] != ''){
                              qty1_uom  = value[in_qty1_uom];
                            }
                            if(value[in_qty2_uom] != ''){
                              qty2_uom  = value[in_qty2_uom];
                            }
                            if(value[in_opname_uom] != ''){
                              qty_opname_uom = value[in_opname_uom];
                            }
                           
                            in_empty        = true;
                          }

                          // adj in
                          row3 += "<td class='white-space-nowrap' align='right'>"+formatNumber(value.adj_in_lot)+"</td>";
                          row3 += "<td class='white-space-nowrap' align='right'>"+formatNumber(value.adj_in_qty1)+" "+value.adj_in_qty1_uom+"</td>";
                          row3 += "<td class='white-space-nowrap' align='right'>"+formatNumber(value.adj_in_qty2)+" "+value.adj_in_qty2_uom+"</td>";
                          row3 += "<td class='white-space-nowrap' align='right'>"+formatNumber(value.adj_in_qty_opname)+" "+value.adj_in_qty_opname_uom+"</td>";

                          in_total_lot         = in_total_lot+parseInt(value.adj_in_lot);
                          in_total_qty1        = in_total_qty1+parseFloat(value.adj_in_qty1);
                          in_total_qty2        = in_total_qty2+parseFloat(value.adj_in_qty2);
                          in_total_qty_opname  = in_total_qty_opname+parseFloat(value.adj_in_qty_opname);

                          // info uom
                          if(value.adj_in_qty1_uom != ''){
                            qty1_uom  = value.adj_in_qty1_uom;
                          }
                          if(value.adj_in_qty2_uom != ''){
                            qty2_uom  = value.adj_in_qty2_uom;
                          }
                          if(value.adj_in_qty_opname_uom != ''){
                            qty_opname_uom  = value.adj_in_qty_opname_uom;
                          }

                          // total in 
                          if(in_empty == true){
                            row3 += "<td class='white-space-nowrap' align='right'>"+formatNumber(in_total_lot)+"</td>";
                            row3 += "<td class='white-space-nowrap' align='right'>"+formatNumber(in_total_qty1)+" "+qty1_uom+"</td>";
                            row3 += "<td class='white-space-nowrap' align='right'>"+formatNumber(in_total_qty2)+" "+qty2_uom+"</td>";
                            row3 += "<td class='white-space-nowrap' align='right'>"+formatNumber(in_total_qty_opname)+" "+qty_opname_uom+"</td>";
                          }

                          for (  s = count_out; x < s; x++ ) {
                            out_lot         = 'out'+no_out+'_lot';
                            out_qty1        = 'out'+no_out+'_qty1';
                            out_qty1_uom    = 'out'+no_out+'_qty1_uom';
                            out_qty2        = 'out'+no_out+'_qty2';
                            out_qty2_uom    = 'out'+no_out+'_qty2_uom';
                            out_opname      = 'out'+no_out+'_qty_opname';
                            out_opname_uom  = 'out'+no_out+'_qty_opname_uom';
                            row3 += "<td class='white-space-nowrap' align='right'>"+formatNumber(value[out_lot])+"</td>";
                            row3 += "<td class='white-space-nowrap' align='right'>"+formatNumber(value[out_qty1])+" "+value[out_qty1_uom]+"</td>";
                            row3 += "<td class='white-space-nowrap' align='right'>"+formatNumber(value[out_qty2])+" "+value[out_qty2_uom]+"</td>";
                            row3 += "<td class='white-space-nowrap' align='right'>"+formatNumber(value[out_opname])+" "+value[out_opname_uom]+"</td>";
                            no_out++;

                            // info total++ out
                            out_total_lot       = out_total_lot+parseInt(value[out_lot]);
                            out_total_qty1      = out_total_qty1+parseFloat(value[out_qty1]);
                            out_total_qty2      = out_total_qty2+parseFloat(value[out_qty2]);
                            out_total_qty_opname = out_total_qty_opname+parseFloat(value[out_opname]);

                            // info uom 
                            if(value[out_qty1_uom] != ''){
                              qty1_uom  = value[out_qty1_uom];
                            }
                            if(value[out_qty2_uom] != ''){
                              qty2_uom  = value[out_qty2_uom];
                            }
                            if(value[out_opname_uom] != ''){
                              qty_opname_uom = value[out_opname_uom];
                            }
                           
                            out_empty        = true;
                          }

                          // adj out
                          row3 += "<td class='white-space-nowrap' align='right'>"+formatNumber(value.adj_out_lot)+"</td>";
                          row3 += "<td class='white-space-nowrap' align='right'>"+formatNumber(value.adj_out_qty1)+" "+value.adj_out_qty1_uom+"</td>";
                          row3 += "<td class='white-space-nowrap' align='right'>"+formatNumber(value.adj_out_qty2)+" "+value.adj_out_qty2_uom+"</td>";
                          row3 += "<td class='white-space-nowrap' align='right'>"+formatNumber(value.adj_out_qty_opname)+" "+value.adj_out_qty_opname_uom+"</td>";

                          out_total_lot         = out_total_lot+parseInt(value.adj_out_lot);
                          out_total_qty1        = out_total_qty1+parseFloat(value.adj_out_qty1);
                          out_total_qty2        = out_total_qty2+parseFloat(value.adj_out_qty2);
                          out_total_qty_opname  = out_total_qty_opname+parseFloat(value.adj_out_qty_opname);

                          // info uom
                          if(value.adj_out_qty1_uom != ''){
                            qty1_uom  = value.adj_out_qty1_uom;
                          }
                          if(value.adj_out_qty2_uom != ''){
                            qty2_uom  = value.adj_out_qty2_uom;
                          }
                          if(value.adj_out_qty_opname_uom != ''){
                            qty_opname_uom  = value.adj_out_qty_opname_uom;
                          }


                          // total out
                          if(out_empty == true){
                            row3 += "<td class='white-space-nowrap' align='right'>"+formatNumber(out_total_lot)+"</td>";
                            row3 += "<td class='white-space-nowrap' align='right'>"+formatNumber(out_total_qty1)+" "+qty1_uom+"</td>";
                            row3 += "<td class='white-space-nowrap' align='right'>"+formatNumber(out_total_qty2)+" "+qty2_uom+"</td>";
                            row3 += "<td class='white-space-nowrap'  align='right'>"+formatNumber(out_total_qty_opname)+" "+qty_opname_uom+"</td>";
                          }

                          // saldo Akhir
                          row3 += "<td class='white-space-nowrap' align='right'>"+formatNumber(value.s_akhir_lot)+"</td>";
                          row3 += "<td class='white-space-nowrap' align='right'>"+formatNumber(value.s_akhir_qty1)+" "+value.s_akhir_qty1_uom+"</td>";
                          row3 += "<td class='white-space-nowrap' align='right'>"+formatNumber(value.s_akhir_qty2)+" "+value.s_akhir_qty2_uom+"</td>";
                          row3 += "<td class='white-space-nowrap' align='right'>"+formatNumber(value.s_akhir_qty_opname)+" "+value.s_akhir_qty_opname_uom+"</td>";


                          row3 += "</tr>";
                            
                    });

                    if(body.length == 0){
                      row3 = $("<tr>").append($("<td colspan='20' align='center'>").text('Tidak ada Data'));
                    }

                    tbody.append(row3);
                    $('#'+tableId).append(tbody);


            return;
  }

  $('#print-bap').click(function(e){

    e.preventDefault();
    tanggal    = encodeURIComponent($('#tanggal').val());
    departemen = $('#departemen').val();

    if(tanggal == ''){ 
      alert_modal_warning('Periode Tanggal Harus diisi!');
    }else if (departemen == null) {
      alert_modal_warning('Departemen Harus diisi !');
    }else{

      var url = '<?php echo base_url() ?>report/mutasi/print_bap_mutasi';
      window.open(url+'?tanggal='+tanggal+'&&departemen='+departemen,'_blank');
    }
  });  
  
  function formatNumber(n) {
    return new Intl.NumberFormat('en-US').format(n);
  }

</script>

</body>
</html>
