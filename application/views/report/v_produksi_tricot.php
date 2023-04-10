
<!DOCTYPE html>
<html lang="en">
<head>
  <?php $this->load->view("admin/_partials/head.php") ?>
  <link rel="stylesheet" type="text/css" href="<?php echo base_url('dist/css/tableScroll.css') ?>">
  <style type="text/css">

    #pagination {
      display: inline-block;
      padding-left: 0;
      border-radius: 4px;
      padding-top: 5px;

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

    .example1 table  {
      display: block;
      height: calc( 100vh - 250px );
      /* height: calc( 100vh - 200px ); */
      overflow-x: auto;
    }
    
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

    /* set tampilan textfield table filter */
    @media only screen and (max-width: 600px) {
      .width-input{
        width: 100px;
      }
      .width-date{
        width: 150px;
      }
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
          <h3 class="box-title"><b>Jadwal Produksi Tricot</b></h3>
        </div>
        <div class="box-body">
           
            <form name="input" class="form-horizontal" role="form" method="POST">
              <div class="col-md-12">
                <div class="col-md-6">
                  <div class="col-md-4">
                    <label>
                      <div id='total_record'>Total Data : 0</div>
                    </label>
                  </div>
                  <div class="col-md-8">
                    <input type="text" class="bootstrap-tagsinput" id="tags" data-role="tags-input"  placeholder="Search Product" />
                  </div>
                </div>   

                <div class="col-md-6">
                  <div class="col-12 col-sm-6 col-md-5">
                    <div class="panel-heading" role="tab" id="advanced">
                        <h5 class="panel-title">
                          <div data-toggle="collapse" href="#advancedSearch" aria-expanded="false" aria-controls="advancedSearch" class='collapsed' style='cursor:pointer;'><i class="showAdvanced glyphicon glyphicon-triangle-bottom"></i> Advanced  </div>
                        </h5>
                    </div>
                  </div>
                  <div class="col-12 col-sm-6 col-md-7">
                    <div class="pull-right text-right">
                      <div id='pagination'></div>
                    </div>
                  </div>
                </div>
              </div>
            </form>

            <div class="col-md-12 col-xs-12">
                <div class="panel panel-default">
                    <div id="advancedSearch" class="panel-collapse collapse" role="tabpanel" aria-labelledby="advanced" >
                      <div class="panel-body">
                        <div class="col-12 col-sm-12 col-md-6" >

                          <div class="form-group">
                            <div class="col-12 col-sm-3 col-md-3">
                              <span class="fa fa-download"></span> <label>Export</label>
                            </div>
                            <div class="col-12 col-sm-8 col-md-8">
                              <form id="frm_excel" method="POST">
                                <input type="hidden" name="query" id="query">
                                <button type="button" class="btn btn-default btn-sm" id="btn-excel" name="btn-excel" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing...">
                                  <i class="fa fa-file-excel-o"  style="color:green"></i>  Excel
                                </button>
                              </form>
                            </div>
                          </div>

                        </div><!-- /.col-md group caption-->

                        <div class="col-12 col-sm-12 col-md-6">
                          <div class="table-responsive over">
                            <table id="filterAdvanced" class="table over">
                              <thead>
                                <th class="bb" width="150px">element</th>
                                <th class="bb" width="100px">condition</th>
                                <th class="bb" width="200px">value</th>
                                <th class="bb" width="10px"></th>
                                <th class="bb">delete</th>
                              </thead>
                              <tbody>
                                  <td>
                                    <select class="form-control input-sm element" name="cmbElement" id="cmbElement" onchange="get_condition(this);">
                                      <?php 
                                          foreach($mstFilter as $row) {
                                              echo "<option value='".$row->kode_element."'>".$row->nama_element."</option>";
                                           }
                                      ?>
                                    </select>
                                  </td>
                                  <td></td>
                                  <td></td>
                                  <td>or</td>
                                  <td></td>
                              </tbody>
                               <tfoot>
                                  <tr>
                                    <td colspan="4">
                                      <a href="#" onclick="addFilter()"><i class="fa fa-plus"></i> Add filter</a>
                                    </td>
                                  </tr>
                              </tfoot>
                            </table>

                            <button type="button" id="btn-filter" name="btn-filter" class="btn btn-default btn-sm"  data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing..."> Apply</button>

                          </div>
                        </div><!-- /.col-md-6 tabel filter-->

                      </div><!-- /.panel body -->
                    </div><!-- /.advance collapse -->
                </div><!-- /.panel default -->
            </div>
            
            <!-- table -->
            <div class="box-body">
              <div class="col-xs-12 table-responsive example1 divListview">
                <div role="region" aria-labelledby="HeadersCol" tabindex="0" class="rowheaders">
                      <table id="example1" class="table table-condesed table-hover" border="0">
                          <thead>
                            <tr>
                              <th  class="style bb ws no" rowspan="2">No. </th>
                              <th  class='style bb ws' rowspan="2">MO</th>
                              <th  class='style bb ws' rowspan="2"style="min-width: 80px">Tgl.MO</th>
                              <th  class='style bb ws' rowspan="2"style="min-width: 130px">MC</th>
                              <th  class='style bb ws' rowspan="2">SC</th>
                              <th  class='style bb ws' rowspan="2">PD</th>
                              <th  class='style bb ws' rowspan="2">Marketing</th>
                              <th  class='style bb ws' rowspan="2" style="min-width: 150px">Corak</th>
                              <th  class='style bb ws' rowspan="2">L.Greige</th>
                              <th  class='style bb ws' rowspan="2">L.Jadi</th>
                              <th  class='style bb ws' rowspan="2"style="min-width: 80px">Start Produksi</th>
                              <th  class='style bb ws' rowspan="2" style="min-width: 80px">Finish Produksi</th>
                              <th  class='style bb ws' colspan="3" style="text-align: center;">Total Order</th>
                              <th  class='style bb ws' rowspan="2">Pcs</th>
                              <th  class='style bb ws' rowspan="2">Gauge</th>
                              <th  class='style bb ws' rowspan="2">Stitch/Cm</th>
                              <th  class='style bb ws' rowspan="2">Courses</th>
                              <th  class='style bb ws' rowspan="2">RPM</th>
                              <th  class='style bb ws' rowspan="2">GB</th>
                              <th  class='style bb ws' rowspan="2" style="min-width: 150px">BD</th>
                              <th  class='style bb ws' rowspan="2">Target Qty</th>
                              <th  class='style bb ws' rowspan="2">RUN IN</th>
                              <th  class='style bb ws' rowspan="2">Target Produksi (Mtr)</th>
                              <th  class='style bb ws' colspan="3" style="text-align: center;">HPH</th>
                              <th  class='style bb ws' rowspan="2">Sisa Qty1</th>
                              <th  class='style bb ws' rowspan="2">Keterangan</th>
                              <th  class='style bb ws' rowspan="2">TC</th>
                              <th  class='style bb ws' rowspan="2">Status</th>
                            </tr>
                            <tr>
                              <th  class='style bb ws' style="text-align: center;">Mtr</th>
                              <th  class='style bb ws' style="text-align: center;">Gl</th>
                              <th  class='style bb ws' style="text-align: center;">Mtr/Gl</th>
                              <th  class='style bb ws' style="text-align: center;">Qty1</th>
                              <th  class='style bb ws' style="text-align: center;">Qty2</th>
                              <th  class='style bb ws' style="text-align: center;">Gl (Lot)</th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr>
                              <td colspan="19" align="center">Tidak ada Data</td>
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


    var arr_filter   = [];// arr_filter menampung array filter textfile, filtertable
    var dataRecord   = [];

    $(document).ready(function(){
      $('[data-role="tags-input"]').tagsinput({ });
    });

    //filter default
    $('#tags').tagsinput('add', { id: 1, text: 'Status = draft OR Status = ready OR MO is Empty' });
    $('[data-role="tags-input"]').tagsinput('add','Status = draft OR Status = ready OR MO is Empty' );
    arr_filter.push({caption:"Status = draft OR Status = ready OR MO is Empty", nama_field : "status^-|=^-|draft^-|,status^-|=^-|ready^-|,kode^-|is^-|Empty", operator:"kosong", isi:"kosong", condition:"OR", type:'table'});
    
    createBody(0);

    // event jika caption ditambahkan di texbox
    $('#tags').on('itemAdded', function(event){

      var check_arr_filter = false;
       
      // check item yang ditambahkan berdasarkan caption
      $.each(arr_filter, function(index,isi){
        if(arr_filter[index].caption == event.item){
          check_arr_filter = true;
        }
      });

      // jika arr_filter terisi
      if(check_arr_filter == false ){
        arr_filter.push({caption:event.item, nama_field:'nama_produk', operator:'LIKE', isi:event.item, condition:'AND', type:'textfield' });
        createBody(0);
      } 

    });
    

    //jika next page ,isi akan rubah lagi
    $('#pagination').on('click','a',function(e){
      
      e.preventDefault(); 
      var pageNum = $(this).attr('data-ci-pagination-page');
      createBody(pageNum)
    });
    
    function createBody(pageNum){
      please_wait(function(){});
      $("#example1_processing").css('display',''); // show loading
      $("#example1 tbody").remove();
      var id_dept ='<?php  echo $id_dept;?>';
      $.ajax({
        type : 'POST',
        dataType: 'json',
        url  : '<?=base_url()?>report/produksitricot/loadData/'+pageNum,
        data : {id_dept : 'TRI', data_filter : arr_filter},
        success: function(data){
          $('#pagination').html(data.pagination);
          $('#total_record').html(data.total_record);

          var tbody = $("<tbody id='0'/>");
          var no    = 0;
          var empty = true;
          $.each(data.record, function(key, value) {
            empty = false;
            if(value.kode == ''){
              num = '';
            }else{
              no=no+1;
              num = no;
            }
            var tr = $("<tr>").append(
                      $("<td>").text(num),
                      $("<td>").text(value.kode),
                      $("<td>").text(value.tgl_mo),
                      $("<td>").text(value.mc),
                      $("<td>").text(value.sc),
                      $("<td>").text(value.pd),
                      $("<td>").text(value.marketing),
                      $("<td>").text(value.corak),
                      $("<td>").text(value.lbr_greige),
                      $("<td>").text(value.lbr_jadi),
                      $("<td>").text(value.start_produksi),
                      $("<td>").text(value.finish_produksi),
                      $("<td align='right'>").text(value.target_pd),
                      $("<td>").text(value.gulung),
                      $("<td align='right'>").text(value.mtr_gl),
                      $("<td>").text(value.pcs),
                      $("<td>").text(value.gauge),
                      $("<td>").text(value.stitch),
                      $("<td>").text(value.courses),
                      $("<td>").text(value.rpm),
                      $("<td>").text(value.gb),
                      $("<td>").text(value.bd),
                      $("<td align='right'>").text(value.target_qty),
                      $("<td>").text(value.run_in),
                      $("<td align='right'>").text(value.target_pd),
                      $("<td align='right'>").text(value.qty1),
                      $("<td align='right'>").text(value.qty2),
                      $("<td align='right'>").text(value.h_gulung),
                      $("<td align='right'>").text(value.sisa),
                      $("<td>").text(value.ket),
                      $("<td>").text(value.tc),
                      $("<td>").text(value.status),             
              );
              tbody.append(tr);

          });

          if(empty == true){
              var tr = $("<tr>").append($("<td colspan='30' align='center'>").text('Tidak ada Data'));
              tbody.append(tr);
          }
          dataRecord.push(data.record);
          $("#example1").append(tbody);
          $("#query").val(data.query);
          $("#example1_processing").css('display','none');
         unblockUI( function() {});
        },error: function (jqXHR, textStatus, errorThrown){
          //alert(jqXHR.responseText);
          alert('error load items');
          $("#example1_processing").css('display','none');
          unblockUI( function() {});
        }
      });
    }


    //klik button apply 
    $(document).on("click","#btn-filter",function(e) {
  
        
        var filter = false;
        var arr    = [];
        var id_dept ='TRI';
      
        $(".element").each(function(index, value) {
          if ($(value).val()!=="") {
            arr.push({
              nama_field :$(value).val(),
              operator   :$(value).parents("tr").find("#cmbCondition").val(),
              isi        :$(value).parents("tr").find("#value").val(),
              type       :'table'
            });
            filter = true;
          }
        }); 

        if(filter == true){
          please_wait(function(){});
          $("#example1_processing").css('display','');
          $("#example1 tbody").remove();
          $('#btn-filter').button('loading');
          $.ajax({
              type: "POST",
              dataType: "JSON",
              url : '<?php echo site_url('report/produksitricot/loadData') ?>',
              data : {data_filter_table:arr, data_filter:arr_filter, id_dept:id_dept},
              success : function(data){
                    $('#total_record').html(data.total_record);
                    $('#pagination').html(data.pagination);

                      var tbody = $("<tbody />");
                      var no    = 0;
                      var empty = true;
                      $.each(data.record, function(key, value) {
                        empty =false;
                        if(value.kode == ''){
                          num = '';
                        }else{
                          no=no+1;
                          num = no;
                        }
                        var tr = $("<tr>").append(
                                  $("<td>").text(num),
                                  $("<td>").text(value.kode),
                                  $("<td>").text(value.tgl_mo),
                                  $("<td>").text(value.mc),
                                  $("<td>").text(value.sc),
                                  $("<td>").text(value.pd),
                                  $("<td>").text(value.marketing),
                                  $("<td>").text(value.corak),
                                  $("<td>").text(value.lbr_greige),
                                  $("<td>").text(value.lbr_jadi),
                                  $("<td>").text(value.start_produksi),
                                  $("<td>").text(value.finish_produksi),
                                  $("<td align='right'>").text(value.target_pd),
                                  $("<td>").text(value.gulung),
                                  $("<td>").text(value.mtr_gl),
                                  $("<td>").text(value.pcs),
                                  $("<td>").text(value.gauge),
                                  $("<td>").text(value.stitch),
                                  $("<td>").text(value.courses),
                                  $("<td>").text(value.rpm),
                                  $("<td>").text(value.gb),
                                  $("<td>").text(value.bd),
                                  $("<td align='right'>").text(value.target_qty),
                                  $("<td>").text(value.run_in),
                                  $("<td align='right'>").text(value.target_pd),
                                  $("<td align='right'>").text(value.qty1),
                                  $("<td align='right'>").text(value.qty2),
                                  $("<td align='right'>").text(value.h_gulung),
                                  $("<td align='right'>").text(value.sisa),
                                  $("<td>").text(value.ket),
                                  $("<td>").text(value.tc),
                                  $("<td>").text(value.status),
                                    );
                       tbody.append(tr);
                      });

                    if(empty == true){
                        var tr = $("<tr>").append($("<td colspan='19' align='center'>").text('Tidak ada Data'));
                        tbody.append(tr);
                    }
                    $("#example1").append(tbody);
                    
                    $('#btn-filter').button('reset');
                    $("#query").val(data.query);
                    $("#example1_processing").css('display','none');
                    $.each(data.dataArr, function(key, val) {
                      
                    arr_filter.push({caption:val.caption, nama_field : val.nama_field, operator:val.operator, isi:val.isi, condition:val.condition, type:'table'});
                      $('[data-role="tags-input"]').tagsinput("add", val.caption);
                    });
                    unblockUI( function() {});

              },error: function (jqXHR, textStatus, errorThrown){
                alert(jqXHR.responseText);
                alert('error filter advanced');
                $('#btn-filter').button('reset');
                $("#example1_processing").css('display','none');
                unblockUI( function() {});
              }
          });

        }else{
          alert_modal_warning('Maaf, Advanced Filter Kosong !');
        }

    });


    //event if item caption removed in textbox
    $('#tags').on('itemRemoved', function(event){
      var caption = event.item;//item removed
      removeArray(caption,'remove');
    });


    function removeArray(caption,action){

      if(action == 'remove'){
          var tmp_index_filter = [];// untuk menampung index yang akan di hapus
          var check_arr          = false;
          
          //looping arr_filter untuk di masukan ke array tmp_index_filter
          $.each(arr_filter, function(index,isi){
            if(arr_filter[index].caption == caption){
              tmp_index_filter.push(index);
            }

          });

           // remove arr_filter jika array lengthnya sama
          if(tmp_index_filter.length == arr_filter.length){
            arr_filter = [];
            //alert('length arr sama ')
          }else if(tmp_index_filter.length == 1){
             //alert('length arr satu');
            for(row in tmp_index_filter){
              indexKe = tmp_index_filter[row];
              arr_filter.splice(indexKe,1); // splice(index ke-, jml yg dhapus)
            } 
          }else{
             tmp_index_filter.reverse().forEach(function(index) {
                arr_filter.splice(index, 1);
             });

          }
          createBody(0);
      }

    }

    $('#btn-excel').click(function(){
        filter = $('#query').val();
        $.ajax({
            "type":'POST',
            "url": "<?php echo site_url('report/produksitricot/export_excel')?>",
            "data": {filter:filter},
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

    });




  // > Tabel Filter

  function addFilter(){
    
    var html = "<tr>";
        html += "<td><select class='form-control input-sm element' name='cmbElement'  id='cmbElement' onchange='get_condition(this);'><?php foreach($mstFilter as $row) { echo "<option value='".$row->kode_element."'>".$row->nama_element."</option>";}?></select></td>";
        html += "<td></td>";
        html += "<td></td>";
        html += "<td>or</td>";
        html += "<td><a onclick='deleteFilter(this);'  href='javascript:void(0)' data-toggle='tooltip' title='delete row' ><i class='fa fa-trash' style='color: red'></i></a></td>"
        html += "</tr>";
    $("#filterAdvanced tbody").append(html);
    var type_condition = '<?php echo $type_condition;?>';
    var table  = document.getElementById("filterAdvanced");
    var lastRowIndex = table.rows.length-2;//get last row
    cmbCondition(type_condition,lastRowIndex);
    $('.dt').datetimepicker({format: 'YYYY-MM-DD',format : 'YYYY-MM-DD HH:mm:ss', ignoreReadonly: true});  
  }
  
     
  function deleteFilter(r){   
    var i = r.parentNode.parentNode.rowIndex;
    document.getElementById("filterAdvanced").deleteRow(i);
  }


  //when to change element
  function get_condition(row){
    var id_dept ='<?php  echo $id_dept;?>';
    var i       = row.parentNode.parentNode.rowIndex;
    var element = $(row).parents("tr").find('#cmbElement').val();
    //alert(element);
    $.ajax({
        type : 'POST',
        dataType: 'json',
        url : '<?php echo site_url('report/produksiwarpingdasar/conditionFilter') ?>',
        data : {"element" : element, "id_dept" : id_dept},
        success: function(response){
          //alert('tes');
          cmbCondition(response.type_condition,i);
          $('.dt').datetimepicker({format: 'YYYY-MM-DD',format : 'YYYY-MM-DD HH:mm:ss', ignoreReadonly: true});  
        }
    });
  }

  //kondisi awal saat load page
  var type_condition = '<?php echo $type_condition;?>';
  var i = 1;
  cmbCondition(type_condition,i);


  //change filter condition/value
  function cmbCondition(type_condition,rowIndex){
    var id_dept   = '<?php  echo $id_dept;?>';
    if(type_condition == 'text'){
      func = 'cmbValue(this,'+rowIndex+')';
      var condition = "<select class='form-control input-sm condition width-input' name='cmbCondition' id='cmbCondition' onchange='"+func+"'>";
          condition +="<option>LIKE</option>";
          condition +="<option>NOT LIKE</option>";
          condition +="<option>=</option>";
          condition +="<option>!=</option>";
          condition +="<option>is</option>";
          condition += "</select>";
      var value = "<input type='text' class='form-control input-sm value width-input' name='txtValue' id='value'>";

    }else if(type_condition == 'datetime' ){
      var condition = "<select class='form-control input-sm condition width-input' name='cmbCondition' id='cmbCondition'>";
          condition += "<option>=</option>";
          condition += "<option>>=</option>";
          condition += "<option><=</option>";
          condition += "<option>></option>";
          condition += "<option><</option>";
          condition += "<option>!=</option>";
          condition += "</select>";
      //var value  = "<input type='text' class='form-control dt' value='2016-04-12'/>";
      
      var value  = "<div class='input-group date dt width-date' id='datetimepicker1' >";
          value  += "<input type='text' class='form-control input-sm value ' name='txtValue' id='value' value='<?php echo date('Y-m-d H:i:s')?>' readonly='readonly'  />";
          value  += "<span class='input-group-addon'>";
          value  += "<span class='glyphicon glyphicon-calendar'></span>";
          value  += "</span>";
          value  += "</div>";

    }else if(type_condition == 'value'){
      var condition = "<select class='form-control input-sm condition width-input' name='cmbCondition' id='cmbCondition'>";
          condition += "<option>=</option>";
          condition += "<option>>=</option>";
          condition += "<option><=</option>";
          condition += "<option>></option>";
          condition += "<option><</option>";
          condition += "<option>!=</option>";
          condition += "</select>";
      var value = "<input type='text' class='form-control input-sm value' name='txtValue' id='value' >";

    }else if(type_condition == 'status'){
      var condition = "<select class='form-control input-sm condition width-input' name='cmbCondition' id='cmbCondition'>";
          condition += "<option>=</option>";
          condition += "</select>";
      var value = "<select class='form-control input-sm value width-input' name='cmbValue' id='value'>";
          value += "<option>draft</option>";
          value += "<option>ready</option>";
          value += "<option>done</option>";
          value += "<option>cancel</option>";
          value += "</select>";
    }

    $('#filterAdvanced tr:nth-child('+rowIndex+') td:nth-child(2)').html(condition);//set cmbCondition
    $('#filterAdvanced tr:nth-child('+rowIndex+') td:nth-child(3)').html(value);//set value
  }

  function cmbValue(condition,rowIndex){
    
    valCondition = $(condition).val();
    if(valCondition == "is"){
      var value = "<select class='form-control input-sm value width-input' name='cmbValue' id='value'>";
          value += "<option>Empty</option>";
          value += "<option>Not Empty</option>";
          value += "</select>";
    }else{
      var value = "<input type='text' class='form-control input-sm value width-input' name='txtValue' id='value'>";
    }

    // return value;
    $('#filterAdvanced tr:nth-child('+rowIndex+') td:nth-child(3)').html(value);//set value

  }

  // < Tabel Filter
     
</script>

</body>
</html>
