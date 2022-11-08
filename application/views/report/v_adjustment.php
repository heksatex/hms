
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
          <h3 class="box-title"><b>Adjustment</b></h3>
        </div>
        <div class="box-body">
           
            <form name="input" class="form-horizontal" role="form" method="POST" id="frm_periode" action="<?=base_url()?>report/adjustment/export_excel">
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
                        <select type="text" class="form-control input-sm" name="departemen" id="departemen" required=""  >
                        </select>
                      </div>                                    
                  </div>
                </div>
               
               
              </div>
              <div class="col-md-4">
                <button type="button" class="btn btn-sm btn-default" name="btn-generate" id="btn-generate" >Generate</button>
                <button type="submit" class="btn btn-sm btn-default" name="btn-generate" id="btn-excel" > <i class="fa fa-file-excel-o"></i> Excel</button>
              </div>

            </form>

            <!-- table ADJ IN  -->
            <div class="box-body">
              <div class="col-sm-12 table-responsive">
                <div class="form-group">
                    <div class="col-md-2">
                          <label>Adjustment IN</label>
                    </div>
                    <div class="col-xs-5 col-md-10" id="total_lot"><label>Total Lot : 0</label></div>
                </div>
                <div class="table_scroll">
                  <div class="table_scroll_head">
                    <div class="divListviewHead">
                        <table id="example1" class="table" border="0">
                            <thead>
                              <tr>
                                <th  class="style no"  >No. </th>
                                <th  class='style' style="min-width: 80px">Kode Adjustment</th>
                                <th  class='style' style="min-width: 80px">Tanggal</th>
                                <th  class='style' style="min-width: 150px">Nama Produk</th>
                                <th  class='style' style="min-width: 80px">Lot</th>
                                <th  class='style' >Qty Stock</th>
                                <th  class='style' >Qty Adj</th>
                                <th  class='style' >UoM</th>
                                <th  class='style' >Qty2 Stock</th>
                                <th  class='style' >Qty2 Adj</th>
                                <th  class='style' >UoM2</th>
                                <th  class='style' >Qty Move</th>
                                <th  class='style' >Qty Move2</th>
                                <th  class='style' style="min-width: 80px">User</th>
                                <th  class='style' style="min-width: 100px">Notes</th>
                              </tr>
                            </thead>
                            <tbody>
                              <tr>
                                <td colspan="15" align="center">Tidak ada Data</td>
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

             <!-- table ADJ OUT  -->
             <div class="box-body">
              <div class="col-sm-12 table-responsive">
                <div class="form-group">
                    <div class="col-md-2">
                          <label>Adjustment OUT</label>
                    </div>
                    <div class="col-xs-5 col-md-10" id="total_lot2"><label>Total Lot : 0</label></div>
                </div>
                <div class="table_scroll">
                  <div class="table_scroll_head">
                    <div class="divListviewHead">
                        <table id="example2" class="table" border="0">
                            <thead>
                              <tr>
                                <th  class="style no"  >No. </th>
                                <th  class='style' style="min-width: 80px">Kode Adjustment</th>
                                <th  class='style' style="min-width: 80px">Tanggal</th>
                                <th  class='style' style="min-width: 150px">Nama Produk</th>
                                <th  class='style' style="min-width: 80px">Lot</th>
                                <th  class='style' >Qty Stock</th>
                                <th  class='style' >Qty Adj</th>
                                <th  class='style' >UoM</th>
                                <th  class='style' >Qty2 Stock</th>
                                <th  class='style' >Qty2 Adj</th>
                                <th  class='style' >UoM2</th>
                                <th  class='style' >Qty Move</th>
                                <th  class='style' >Qty Move2</th>
                                <th  class='style' style="min-width: 80px">User</th>
                                <th  class='style' style="min-width: 100px">Notes</th>
                              </tr>
                            </thead>
                            <tbody>
                              <tr>
                                <td colspan="15" align="center">Tidak ada Data</td>
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

  // array tmp
  var tmp_filter = [];

  // set date tgldari
  $('#tgldari').datetimepicker({
      defaultDate : new Date(),
      format : 'D-MMMM-YYYY',
      ignoreReadonly: true
  });

  // set date tglsampai
  $('#tglsampai').datetimepicker({
      defaultDate : new Date(),
      format : 'D-MMMM-YYYY',
      ignoreReadonly: true
  });

  //select 2 Departementy
  $('#departemen').select2({
      allowClear: true,
      placeholder: "Select Departemen",
      ajax:{
            dataType: 'JSON',
            type : "POST",
            url : "<?php echo base_url();?>report/efisiensi/get_departement_select2",
            //delay : 250,
            data : function(params){
              return{
                nama:params.term,
              };
            }, 
            processResults:function(data){
              var results = [];
              $.each(data, function(index,item){
                results.push({
                    id:item.kode,
                    text:item.nama
                });
              });
              return {
                results:results
              };
            },
            error: function (xhr, ajaxOptions, thrownError){
              //alert('Error data');
              //alert(xhr.responseText);
            }
      }
    });

  // btn generate
  $("#btn-generate").on('click', function(){
      tmp_filter  = [];
      tgldari   = $('#tgldari').val();
      tglsampai = $('#tglsampai').val();
      id_dept   = $('#departemen').val();

      if(tgldari == '' || tglsampai == ''){
        alert_modal_warning('Periode Tanggal Harus diisi !');

      }else if(id_dept == null){
        alert_modal_warning('Departemen Harus diisi !');

      }else{  
          $("#example1_processing").css('display',''); // show loading
          $("#example2_processing").css('display',''); // show loading

          $('#btn-generate').button('loading');
          $("#example1 tbody").remove();
          $("#example2 tbody").remove();
          $.ajax({
                type: "POST",
                dataType : "JSON",
                url : "<?php echo site_url('report/adjustment/loadData')?>",
                data: {tgldari:tgldari, tglsampai:tglsampai, id_dept:id_dept, load:'header'},
                success: function(data){
                  $("#total_lot").html(data.total_lot_adj_in);;
                  $("#total_lot2").html(data.total_lot_adj_out);;
                  
                  // push data filter ke array tmp_filter
                  tmp_filter.push({'id_dept' : id_dept, 'tgldari' : tgldari, 'tglsampai' : tglsampai});
                  //alert('check arr '+JSON.stringify(tmp_filter));

                  body_grouping("example1",data.record)// ADJ IN
                  body_grouping("example2",data.record2)// ADJ OUT

                $('#btn-generate').button('reset');
                $("#example1_processing").css('display','none'); // hidden loading
                $("#example2_processing").css('display','none'); // hidden loading

                },error : function(jqXHR, textStatus, errorThrown){
                  alert(jqXHR.responseText);
                  //alert('error data');
                  $('#btn-generate').button('reset');
                }
          });
      }
  });


  $(document).on("click", ".group1", function(e){

    var kode      = '';
    var tbody_id  = '';
    var tampil    = false;
    var id_dept = '';
    var tgldari = '';
    var tglsampai = '';
    for(i = 0; i < tmp_filter.length; i++){
      id_dept   = tmp_filter[i].id_dept;
      tgldari   = tmp_filter[i].tgldari;
      tglsampai = tmp_filter[i].tglsampai;
            
    }

    // ambil data berdasarkan data-content='edit'
    $(this).parents("tr").find("td[data-content='edit']").each(function(){
        data_isi = $(this).attr('data-isi');
        tbody_id = $(this).attr('data-tbody');

        $(this).toggleClass("collapsed collapse"); // ganti collapsed, collapse
        if($(this).hasClass("collapsed") == true){
          tampil = true;
        }else if($(this).hasClass("collapsed") == false){
          tampil = false;
        }
        $(this).find(".glyphicon").toggleClass("glyphicon-plus glyphicon-minus"); // ganti icon + jadi minus

        if(tampil == true){
          $("#example1 tbody[data-parent='"+tbody_id+"']").remove();// remove child by groupby

        }else{

          this_icon = $(this);
          this_icon.html('<i class="fa fa-spinner fa-spin "></i>');
          this_icon.css('pointer-events','none');

          $.ajax({
            type : 'POST',
            dataType: 'json',
            url : "<?php echo site_url('report/adjustment/loadData')?>",
            data : {id_dept:id_dept, tgldari:tgldari, tglsampai:tglsampai, data_isi:data_isi, load:'item', view:'in'},
            success:function(data){
                
                body_items('example1',data.item,tbody_id);

                // kembalikan icon ke awal
                this_icon.css('pointer-events','');
                this_icon.html('<i class="glyphicon glyphicon-minus "></i>');

            },error : function(jqXHR, textStatus, errorThrown){
              //alert(jqXHR.responseText);
              alert('error load child');
              // kembalikan icon ke awal
              this_icon.css('pointer-events','');
              this_icon.html('<i class="glyphicon glyphicon-minus "></i>');
              
            }

          });


        }

    });

  });

  $(document).on("click", ".group2", function(e){

    var kode      = '';
    var tbody_id  = '';
    var tampil    = false;
    var id_dept = '';
    var tgldari = '';
    var tglsampai = '';
    for(i = 0; i < tmp_filter.length; i++){
      id_dept   = tmp_filter[i].id_dept;
      tgldari   = tmp_filter[i].tgldari;
      tglsampai = tmp_filter[i].tglsampai;
            
    }

    // ambil data berdasarkan data-content='edit'
    $(this).parents("tr").find("td[data-content='edit']").each(function(){
        data_isi = $(this).attr('data-isi');
        tbody_id = $(this).attr('data-tbody');

        $(this).toggleClass("collapsed collapse"); // ganti collapsed, collapse
        if($(this).hasClass("collapsed") == true){
          tampil = true;
        }else if($(this).hasClass("collapsed") == false){
          tampil = false;
        }
        $(this).find(".glyphicon").toggleClass("glyphicon-plus glyphicon-minus"); // ganti icon + jadi minus

        if(tampil == true){
          $("#example2 tbody[data-parent='"+tbody_id+"']").remove();// remove child by groupby

        }else{

          this_icon = $(this);
          this_icon.html('<i class="fa fa-spinner fa-spin "></i>');
          this_icon.css('pointer-events','none');

          $.ajax({
            type : 'POST',
            dataType: 'json',
            url : "<?php echo site_url('report/adjustment/loadData')?>",
            data : {id_dept:id_dept, tgldari:tgldari, tglsampai:tglsampai, data_isi:data_isi, load:'item', view:'out'},
            success:function(data){
                
                body_items('example2',data.item,tbody_id);

                // kembalikan icon ke awal
                this_icon.css('pointer-events','');
                this_icon.html('<i class="glyphicon glyphicon-minus "></i>');

            },error : function(jqXHR, textStatus, errorThrown){
              //alert(jqXHR.responseText);
              alert('error load child');
              // kembalikan icon ke awal
              this_icon.css('pointer-events','');
              this_icon.html('<i class="glyphicon glyphicon-minus "></i>');
              
            }

          });


        }

    });

  });

  function body_grouping(id_table,record){

                  let no    = 1;  
                  let empty = true;
                  let icon  = '';
                  let style = '';
                  var group = 'group-of-rows-';

                  if(id_table == 'example1'){
                    $group_num = 'group1';
                  }else{
                    $group_num = 'group2';
                  }
              
                  $.each(record, function(key, value){
                    empty = false;
                    group = 'group-of-rows-'+no;

                    var tr = $("<tr >").append(
                              $("<td class='show collapsed "+$group_num+"' href='#' style='cursor:pointer;'  data-content='edit' data-isi='"+value.kode_produk+"' data-tbody='"+group+"'>").html("<i class='glyphicon glyphicon-plus' ></i>"),
                               $("<td>").text(''),
                               $("<td>").text(''),
                               $("<td>").text(value.nama_produk),
                               $("<td>").text(value.tot_lot),
                               $("<td align='right'>").text(value.qty_stock),
                               $("<td align='right'>").text(value.qty),
                               $("<td>").text(value.uom),
                               $("<td align='right'>").text(value.qty2_stock),
                               $("<td align='right'>").text(value.qty2),
                               $("<td>").text(value.uom2),
                               $("<td align='right'>").text(value.qty_move),
                               $("<td align='right'>").text(value.qty_move2),
                    );
                    no++;
                    tbody = $("<tbody id='"+group+"'>").append(tr);
                    $("#"+id_table).append(tbody); // append parents

                  });

                  if(empty == true){
                    var tr = $("<tr>").append($("<td colspan='15' align='center'>").text('Tidak ada Data'));
                    tbody = $("<tbody id='"+group+"'>").append(tr);
                    $("#"+id_table).append(tbody); // append parents
                  }

  }

  function body_items(id_table,record,tbody_id){
                let tbody = $("<tbody data-parent='"+tbody_id+"' />");
                let row = '';
                let no  = 1;
                $.each(record, function(key, value) {
                          row +=  "<tr  style='background-color: #f2f2f2;' >";
                          row += "<td>"+no++ +"</td>";
                          row += "<td>"+value.kode_adjustment+"</td>";
                          row += "<td>"+value.tanggal+"</td>";
                          row += "<td></td>";
                          row += "<td>"+value.lot+"</td>";
                          row += "<td align='right' >"+value.qty_stock+"</td>";
                          row += "<td align='right' >"+value.qty+"</td>";
                          row += "<td>"+value.uom+"</td>";
                          row += "<td align='right'>"+value.qty2_stock+"</td>";
                          row += "<td align='right'>"+value.qty2+"</td>";
                          row += "<td>"+value.uom2+"</td>";
                          row += "<td align='right'>"+value.qty_move+"</td>";
                          row += "<td align='right'>"+value.qty_move2+"</td>";
                          row += "<td>"+value.user+"</td>";
                          row += "<td>"+value.note+"</td>";
                          row += "</tr>";
                });
                tbody.append(row);
                $('#'+id_table+' tbody[id='+tbody_id+']').after(tbody);
                
  }

</script>

</body>
</html>
