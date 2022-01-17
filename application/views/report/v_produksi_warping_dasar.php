
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
          <h3 class="box-title"><b>Jadwal Produksi Warping Dasar</b></h3>
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
                              <form id="frm_excel" action="<?=base_url()?>report/produksiwarpingdasar/export_excel" method="POST">
                                <input type="hidden" name="query" id="query">
                                <button type="submit" class="btn btn-default btn-sm" id="btn-excel" name="btn-excel">
                                  <i class="fa fa-file-excel-o"></i>  Excel
                                </button>
                              </form>
                            </div>
                          </div>

                        </div><!-- /.col-md group caption-->

                        <div class="col-12 col-sm-12 col-md-6">
                          <div class="table-responsive over">
                            <table id="filterAdvanced" class="table over">
                              <thead>
                                <th width="150px">element</th>
                                <th width="100px">condition</th>
                                <th width="200px">value</th>
                                <th width="10px"></th>
                                <th>delete</th>
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

                            <button type="button" id="btn-filter" name="btn-filter" class="btn btn-default btn-sm">Apply</button>

                          </div>
                        </div><!-- /.col-md-6 tabel filter-->

                      </div><!-- /.panel body -->
                    </div><!-- /.advance collapse -->
                </div><!-- /.panel default -->
            </div>
            
            <!-- table -->
            <div class="box-body">
            <div class="col-sm-12 table-responsive">
              <div class="table_scroll">
                <div class="table_scroll_head">
                  <div class="divListviewHead">
                      <table id="example1" class="table" border="0">
                          <thead>
                            <tr>
                              <th  class="style no" >No. </th>
                              <th  class='style'>MO</th>
                              <th  class='style' style="min-width: 80px">Tgl.MO</th>
                              <th  class='style'>MC</th>
                              <th  class='style' style="min-width: 150px">Product</th>
                              <th  class='style'>Sales Contract</th>
                              <th  class='style'>MO Knitting</th>
                              <th  class='style'>MC Knitting</th>
                              <th  class='style'>Corak</th>
                              <th  class='style'>GB</th>
                              <th  class='style'>Jml Beam</th>
                              <th  class='style'>Lembar</th>
                              <th  class='style'>Pjg Benang/Beam</th>
                              <th  class='style'>Kelipatan Pembuatan Benang </th>
                              <th  class='style'>Target</th>
                              <th  class='style'>HPH/Qty1</th>
                              <th  class='style'>HPH/Qty2</th>
                              <th  class='style'>Sisa</th>
                              <th  class='style' >Status</th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr>
                              <td colspan="19" align="center">Tidak ada Data</td>
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


/*
    $('#btn-load').on('click', function(){
      $('#btn-load').button('loading');
      createBody(0);
      $('#btn-load').button('reset');

    });
    $('#example1').on('scroll', function() {
       if($(this).scrollTop() + $(this).innerHeight() > $(this)[0].scrollHeight){
       //if($(this).scrollTop() + $(this).innerHeight() >= $(this)[0].scrollHeight) {
          // alert('end reached');

       }
    });
    var last_no = [];
*/  

     //* Show collapse advanced search
    $('#advancedSearch').on('shown.bs.collapse', function () {
       $(".showAdvanced").removeClass("glyphicon-triangle-bottom").addClass("glyphicon-triangle-top");
    });

    //* Hide collapse advanced search
    $('#advancedSearch').on('hidden.bs.collapse', function () {
       $(".showAdvanced").removeClass("glyphicon-triangle-top").addClass("glyphicon-triangle-bottom");
    });


    var arr_filter   = [];// arr_filter menampung array filter textfile, filtertable

     $(document).ready(function(){
      $('[data-role="tags-input"]').tagsinput({ });
    });


    // event jika caption ditambahkan di texbox
    $('#tags').on('itemAdded', function(event){

      //alert('added belum '+JSON.stringify(arr_filter));
      please_wait(function(){});
      var check_arr_filter = false;
       
      // check item yang ditambahkan berdasarkan caption
      $.each(arr_filter, function(index,isi){
        if(arr_filter[index].caption == event.item){
          check_arr_filter = true;
        }
      });

      // jika arr_filter terisi
      if(check_arr_filter == false ){
        //alert('createPagination')
        arr_filter.push({caption:event.item, nama_field:'nama_produk', operator:'LIKE', isi:event.item, condition:'AND', type:'textfield' });
        //alert('tes');
        createBody(0);
      } 

      //alert('added sudah '+JSON.stringify(arr_filter));
      unblockUI( function() {});
      
    });


    createBody(0);
    var dataRecord = [];

    function createBody(pageNum){
      $("#example1_processing").css('display',''); // show loading

      $("#example1 tbody").remove();
      // alert('tes');
      $.ajax({
        type : 'POST',
        dataType: 'json',
        url  : '<?=base_url()?>report/produksiwarpingdasar/loadData/'+pageNum,
        data : {id_dept : 'WRD', data_filter : arr_filter},
        success: function(data){
          //$('#pagination').html(data.pagination);
          $('#total_record').html(data.total_record);

          var tbody = $("<tbody id='0'/>");
          var no    = 1;
          var empty = true;
          $.each(data.record, function(key, value) {
            empty = false;
            var tr = $("<tr>").append(
                      $("<td>").text(no++),
                      $("<td>").text(value.kode),
                      $("<td>").text(value.tgl_mo),
                      $("<td>").text(value.mc),
                      $("<td>").text(value.product),
                      $("<td>").text(value.sales_contract),
                      $("<td>").text(value.mo_knitting),
                      $("<td>").text(value.mc_knitting),
                      $("<td>").text(value.corak),
                      $("<td>").text(value.GB),
                      $("<td>").text(value.jml_beam),
                      $("<td>").text(value.lembar),
                      $("<td>").text(value.pjg),
                      $("<td>").text(''),
                      $("<td align='right'>").text(value.target),
                      $("<td align='right'>").text(value.qty1),
                      $("<td>").text(value.qty2),
                      $("<td align='right'>").text(value.sisa),
                      $("<td>").text(value.status),
              );
              tbody.append(tr);
          });

          if(empty == true){
              var tr = $("<tr>").append($("<td colspan='19' align='center'>").text('Tidak ada Data'));
              tbody.append(tr);
          }
          dataRecord.push(data.record);
          //last_no_new = parseInt(last_no) + no;
          //last_no     = [];
          //last_no.push(last_no_new);
          //alert(last_no_new);
         $("#example1").append(tbody);
         $("#query").val(data.query);
         $("#example1_processing").css('display','none');

        },error: function (jqXHR, textStatus, errorThrown){
          alert(jqXHR.responseText);
        }
      });
    }


    //klik button apply 
    $(document).on("click","#btn-filter",function(e) {
        
        var filter = false;
        var arr    = [];
        var id_dept ='WRD';
      
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
          $("#example1_processing").css('display',''); // show loading

          $("#example1 tbody").remove();
          $('#btn-filter').button('loading');
          $.ajax({
              type: "POST",
              dataType: "JSON",
              url : '<?php echo site_url('report/produksiwarpingdasar/loadData/0') ?>',
              data : {data_filter_table:arr, data_filter:arr_filter, id_dept:id_dept},
              success : function(data){
                    $('#total_record').html(data.total_record);

                      var tbody = $("<tbody />");
                      var no    = 1;
                      var empty = true;
                      $.each(data.record, function(key, value) {
                        empty =false;
                        var tr = $("<tr>").append(
                                  $("<td>").text(no++),
                                  $("<td>").text(value.kode),
                                  $("<td>").text(value.tgl_mo),
                                  $("<td>").text(value.mc),
                                  $("<td>").text(value.product),
                                  $("<td>").text(value.sales_contract),
                                  $("<td>").text(value.mo_knitting),
                                  $("<td>").text(value.mc_knitting),
                                  $("<td>").text(value.corak),
                                  $("<td>").text(value.GB),
                                  $("<td>").text(value.jml_beam),
                                  $("<td>").text(value.lembar),
                                  $("<td>").text(value.pjg),
                                  $("<td>").text(''),
                                  $("<td>").text(value.target),
                                  $("<td>").text(value.qty1),
                                  $("<td>").text(value.qty2),
                                  $("<td>").text(value.sisa),
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

              },error: function (jqXHR, textStatus, errorThrown){
                alert(jqXHR.responseText);
                $('#btn-filter').button('reset');
              }
          });

        }else{
          alert_modal_warning('Maaf, Advanced Filter Kosong !');
        }

    });


    //event if item caption removed in textbox
    $('#tags').on('itemRemoved', function(event){
      please_wait(function(){});
      var caption = event.item;//item removed
      removeArray(caption,'remove');
      unblockUI( function() {});
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
      var condition = "<select class='form-control input-sm condition' name='cmbCondition' id='cmbCondition'>";
          condition +="<option>LIKE</option>";
          condition += "</select>";
      var value = "<input type='text' class='form-control input-sm value' name='txtValue' id='value'>";

    }else if(type_condition == 'datetime' ){
      var condition = "<select class='form-control input-sm condition' name='cmbCondition' id='cmbCondition'>";
          condition += "<option>=</option>";
          condition += "<option>>=</option>";
          condition += "<option><=</option>";
          condition += "<option>></option>";
          condition += "<option><</option>";
          condition += "<option>!=</option>";
          condition += "</select>";
      //var value  = "<input type='text' class='form-control dt' value='2016-04-12'/>";
      
      var value  = "<div class='input-group date dt' id='datetimepicker1' >";
          value  += "<input type='text' class='form-control input-sm value' name='txtValue' id='value' value='<?php echo date('Y-m-d H:i:s')?>' readonly='readonly' />";
          value  += "<span class='input-group-addon'>";
          value  += "<span class='glyphicon glyphicon-calendar'></span>";
          value  += "</span>";
          value  += "</div>";

    }else if(type_condition == 'value'){
      var condition = "<select class='form-control input-sm condition' name='cmbCondition' id='cmbCondition'>";
          condition += "<option>=</option>";
          condition += "<option>>=</option>";
          condition += "<option><=</option>";
          condition += "<option>></option>";
          condition += "<option><</option>";
          condition += "<option>!=</option>";
          condition += "</select>";
      var value = "<input type='text' class='form-control input-sm value' name='txtValue' id='value' >";

    }else if(type_condition == 'status'){
      var condition = "<select class='form-control input-sm condition' name='cmbCondition' id='cmbCondition'>";
          condition += "<option>=</option>";
          condition += "</select>";
      var value = "<select class='form-control input-sm value' name='cmbValue' id='value'>";
          value += "<option>draft</option>";
          value += "<option>ready</option>";
          value += "<option>done</option>";
          value += "<option>cancel</option>";
          value += "</select>";
    }

    $('#filterAdvanced tr:nth-child('+rowIndex+') td:nth-child(2)').html(condition);//set cmbCondition
    $('#filterAdvanced tr:nth-child('+rowIndex+') td:nth-child(3)').html(value);//set value
  }

  // < Tabel Filter
     
</script>

</body>
</html>
