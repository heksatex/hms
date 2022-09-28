
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
          <h3 class="box-title"><b>Mutasi</b></h3>
        </div>
        <div class="box-body">
           
            <form name="input" class="form-horizontal" role="form" method="POST" id="frm_mutasi" action="">
              <div class="col-md-8">
                <div class="form-group">
                    <div class="col-md-12"> 
                        <div class="col-md-2">
                            <label>Tanggal </label>
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
                <div class="form-group">
                    <div class="col-md-12">
                        <label>
                            <div id='info_table1'></div>
                        </label>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-12">
                        <label>
                            <div id='total_record'>Total Data : 0</div>
                        </label>
                    </div>
                </div>
                <div class="table_scroll">
                  <div class="table_scroll_head">
                    <div class="divListviewHead">
                        <table id="example1" class="table" border="0">
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
                      <div class="col-md-12">
                          <label>
                              <div id='total_record2'>Total Data : 0</div>
                          </label>
                      </div>
                </div>
                <div class="table_scroll">
                  <div class="table_scroll_head">
                    <div class="divListviewHead">
                        <table id="example2" class="table" border="0" >
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
    maxDate: new Date(year, month),
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



 /*  // cek selisih saatu submit excel
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

  }); */

  // btn generate
  $("#btn-generate").on('click', function(){

      tanggal    = $('#tanggal').val();
      departemen = $('#departemen').val();

      if(tanggal == '' ){
        alert_modal_warning('Periode Tanggal Harus diisi !');

      }else if (departemen == null) {
        alert_modal_warning('Departemen Harus diisi !');

      }else{  
          $("#example1_processing").css('display',''); // show loading

          $('#btn-generate').button('loading');
          $("#example1 tbody").remove();
          $.ajax({
                type: "POST",
                dataType : "JSON",
                url : "<?php echo site_url('report/mutasi/loadData')?>",
                data: {tanggal:tanggal,departemen:departemen},
                success: function(data){
                    $("#example1 thead").remove();
                    $("#example1 tbody").remove();
                    $("#example2 thead").remove();
                    $("#example2 tbody").remove();

                    $.each(data, function(key, value){ 
                      if(value.table_1 == "Yes"){
                        create_table('example1', value.head_table1,value.head_table2,value.record,value.count_in,value.count_out);
                        $('#info_table1').html('');
                        $('#total_record').html('Total Data : '+value.count_record);
                      }

                      if(value.table_2 == "Yes"){
                        create_table('example2',value.head_table1,value.head_table2,value.record,value.count_in,value.count_out);
                        $('#table_2').css('display','');
                        $('#info_table1').html('Mutasi Bahan Baku');
                        $('#info_table2').html('Mutasi Barang Jadi');
                        $('#total_record2').html('Total Data : '+value.count_record);
                      }else{
                        $('#info_table1').html('');
                        $('#info_table2').html('');
                        $('#table_2').css('display','none');
                        $('#total_record2').html('Total Data : '+value.count_record);
                      }
                    });
                    
                $('#btn-generate').button('reset');
                $("#example1_processing").css('display','none'); // hidden loading

                },error : function(jqXHR, textStatus, errorThrown){
                  alert(jqXHR.responseText);
                  //alert('error data');
                  $("#example1_processing").css('display','none'); // hidden loading
                  $('#btn-generate').button('reset');
                }
          });
      }
  });

  function create_table(tableId,head_table1,head_table2,body,count_in,count_out){
                    let thead = $("<thead />");
                    let row   = '';
                    let row2  = '';

                    $.each(head_table1, function(key, value){ // loop
                        $.each(value, function(a, b){
                            if(a == 'info'){
                                for ( var i = 0, l = b.length; i < l; i++ ) {
                                    $.each(b[i], function(c, d){
                                        row += "<th class='style no'  rowspan='2'>";
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
                                        row2 += "<th>";
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
                          row3 += "<td>"+value.kode_produk+"</td>";
                          row3 += "<td style='width:100% !important;'>"+value.nama_produk+"</td>";
                          // saldo awal
                          row3 += "<td align='right'>"+value.s_awal_lot+"</td>";
                          row3 += "<td align='right'>"+value.s_awal_qty1+" "+value.s_awal_qty1_uom+"</td>";
                          row3 += "<td align='right'>"+value.s_awal_qty2+" "+value.s_awal_qty2_uom+"</td>";
                          row3 += "<td align='right'>"+value.s_awal_qty_opname+" "+value.s_awal_qty_opname_uom+"</td>";

                          // info uom in 1
                          qty1_uom        = value.s_awal_qty1_uom;
                          qty2_uom        = value.s_awal_qty2_uom;
                          qty_opname_uom  = value.s_awal_qty_opname_uom;

                          for (  n = count_in; d < n; d++ ) {
                            in_lot         = 'in'+no_in+'_lot';
                            in_qty1        = 'in'+no_in+'_qty1';
                            in_qty1_uom    = 'in'+no_in+'_qty1_uom';
                            in_qty2        = 'in'+no_in+'_qty2';
                            in_qty2_uom    = 'in'+no_in+'_qty2_uom';
                            in_opname      = 'in'+no_in+'_qty_opname';
                            in_opname_uom  = 'in'+no_in+'_qty_opname_uom';
                            row3 += "<td align='right'>"+value[in_lot]+"</td>";
                            row3 += "<td align='right'>"+value[in_qty1]+" "+value[in_qty1_uom]+"</td>";
                            row3 += "<td align='right'>"+value[in_qty2]+" "+value[in_qty2_uom]+"</td>";
                            row3 += "<td align='right'>"+value[in_opname]+" "+value[in_opname_uom]+"</td>";
                            no_in++;
                            // info total++ in
                            in_total_lot         = in_total_lot+parseInt(value[in_lot]);
                            in_total_qty1        = in_total_qty1+parseFloat(value[in_qty1]);
                            in_total_qty2        = in_total_qty2+parseFloat(value[in_qty2]);
                            in_total_qty_opname  = in_total_qty_opname+parseFloat(value[in_opname]);
                           
                            in_empty        = true;
                          }

                          // adj in
                          row3 += "<td align='right'>"+value.adj_in_lot+"</td>";
                          row3 += "<td align='right'>"+value.adj_in_qty1+" "+value.adj_in_qty1_uom+"</td>";
                          row3 += "<td align='right'>"+value.adj_in_qty2+" "+value.adj_in_qty2_uom+"</td>";
                          row3 += "<td align='right'>"+value.adj_in_qty_opname+" "+value.adj_in_qty_opname_uom+"</td>";

                          in_total_lot         = in_total_lot+parseInt(value.adj_in_lot);
                          in_total_qty1        = in_total_qty1+parseFloat(value.adj_in_qty1);
                          in_total_qty2        = in_total_qty2+parseFloat(value.adj_in_qty2);
                          in_total_qty_opname  = in_total_qty_opname+parseFloat(value.adj_in_qty_opname);

                          // total in 
                          if(in_empty == true){
                            row3 += "<td align='right'>"+in_total_lot+"</td>";
                            row3 += "<td align='right'>"+in_total_qty1.toFixed(2)+" "+qty1_uom+"</td>";
                            row3 += "<td align='right'>"+in_total_qty2.toFixed(2)+" "+qty2_uom+"</td>";
                            row3 += "<td align='right'>"+in_total_qty_opname+" "+qty_opname_uom+"</td>";
                          }

                          for (  s = count_out; x < s; x++ ) {
                            out_lot         = 'out'+no_out+'_lot';
                            out_qty1        = 'out'+no_out+'_qty1';
                            out_qty1_uom    = 'out'+no_out+'_qty1_uom';
                            out_qty2        = 'out'+no_out+'_qty2';
                            out_qty2_uom    = 'out'+no_out+'_qty2_uom';
                            out_opname      = 'out'+no_out+'_qty_opname';
                            out_opname_uom  = 'out'+no_out+'_qty_opname_uom';
                            row3 += "<td align='right'>"+value[out_lot]+"</td>";
                            row3 += "<td align='right'>"+value[out_qty1]+" "+value[out_qty1_uom]+"</td>";
                            row3 += "<td align='right'>"+value[out_qty2]+" "+value[out_qty2_uom]+"</td>";
                            row3 += "<td align='right'>"+value[out_opname]+" "+value[out_opname_uom]+"</td>";
                            no_out++;

                            // info total++ out
                            out_total_lot       = out_total_lot+parseInt(value[out_lot]);
                            out_total_qty1      = out_total_qty1+parseFloat(value[out_qty1]);
                            out_total_qty1      = out_total_qty1+parseFloat(value[out_qty2]);
                            out_total_qty_opname = out_total_qty_opname+parseFloat(value[out_opname]);
                           
                            out_empty        = true;
                          }

                          // adj out
                          row3 += "<td align='right'>"+value.adj_out_lot+"</td>";
                          row3 += "<td align='right'>"+value.adj_out_qty1+" "+value.adj_out_qty1_uom+"</td>";
                          row3 += "<td align='right'>"+value.adj_out_qty2+" "+value.adj_out_qty2_uom+"</td>";
                          row3 += "<td align='right'>"+value.adj_out_qty_opname+" "+value.adj_out_qty_opname_uom+"</td>";

                          out_total_lot         = out_total_lot+parseInt(value.adj_out_lot);
                          out_total_qty1        = out_total_qty1+parseFloat(value.adj_out_qty1);
                          out_total_qty2        = out_total_qty2+parseFloat(value.adj_out_qty2);
                          out_total_qty_opname  = out_total_qty_opname+parseFloat(value.adj_out_qty_opname);

                          // total out
                          if(out_empty == true){
                            row3 += "<td align='right'>"+out_total_lot+"</td>";
                            row3 += "<td align='right'>"+out_total_qty1.toFixed(2)+" "+qty1_uom+"</td>";
                            row3 += "<td align='right'>"+out_total_qty2.toFixed(2)+" "+qty2_uom+"</td>";
                            row3 += "<td align='right'>"+out_total_qty_opname+" "+qty_opname_uom+"</td>";
                          }

                          // saldo Akhir
                          row3 += "<td align='right'>"+value.s_akhir_lot+"</td>";
                          row3 += "<td align='right'>"+value.s_akhir_qty1+" "+value.s_akhir_qty1_uom+"</td>";
                          row3 += "<td align='right'>"+value.s_akhir_qty2+" "+value.s_akhir_qty2_uom+"</td>";
                          row3 += "<td align='right'>"+value.s_akhir_qty_opname+" "+value.s_akhir_qty_opname_uom+"</td>";


                          row3 += "</tr>";
                            
                    }); 

                    tbody.append(row3);
                    $('#'+tableId).append(tbody);


            return;
  }

</script>

</body>
</html>
