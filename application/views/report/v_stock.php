
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

    .result{
      display: inline;
    }

    .breadcumb{
      margin: 0 .4em;
      display: inline-block;
      width: auto;
    }

    .breadcumb-field{
      color:white;
      background-color: #222d32;
      font-weight: bold;
      border-top-left-radius: 10px;
      border-bottom-left-radius: 10px;
    }

    .breadcumb-value{
      color:white;
      background-color: #dd4b39;
      font-weight: bold;
    }

    .ul-pl-02{
      padding-left: .2em!important;
    }

    .breadcumb-close{
      cursor: pointer;
      font-size: 1em;
      padding: 2px;
      background: #fff;
      color: #a9a9a9;
    }

    /* >> CSS advanced search */    

    /* > GROUP BY TAGS */
    .li-adv{
    display: inline-block;
    cursor: pointer;
    position: relative;
    margin-right: 8px;
    }

    .li-adv:hover{
    background-color: #f2dede;
    }
    /* > GROUP BY TAGS */

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

    /* set tampilan textfield table filter */
    @media only screen and (max-width: 600px) {
      .width-input{
        width: 100px;
      }
      .width-date{
        width: 150px;
      }
    }

        /*over show tampilan dekstop*/
    @media screen and (min-width: 768px) {
      .over {
        overflow-x: visible !important;
      }

      div.divListview {
         width: 100%;
      }

      .listView {
         /*display: table;*/
         table-layout: fixed;
         width: 100%;
      }

    }
  
    /* << CSS advanced search */

    /* > CSS sort table*/

    thead a:link, a:visited .column_sort {
      color: #333;
      text-decoration: none;
    }

    thead a:hover, a:active, .column_sort{
      color: #333;
      text-decoration: underline;
    }

    /* < CSS sort table*/
    
    .min-width-50{
      min-width: 50px;
    }

    .min-width-80{
      min-width: 50px;
    }

    .min-width-100{
      min-width: 100px;
    }
    
    .min-width-120{
      min-width: 120px;
    }

    .min-width-130{
      min-width: 130px;
    }

    .min-width-140{
      min-width: 140px;
    }

    .min-width-150{
      min-width: 150px;
    }


  </style>
</head>

<body class="hold-transition skin-black fixed sidebar-mini" onload="get_default()">
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
          <h3 class="box-title"><b>Stock</b></h3>
        </div>
        <div class="box-body">

          <form name="input" class="form-horizontal" role="form" id="form_filter" action="<?=base_url()?>report/stock/export_excel_stock" method="POST">
              <div class="col-md-12">
                <div class="col-md-8">
                  <div class="col-md-3">
                    <select class="form-control input-sm" name="cmbSearch" id="cmbSearch">
                      <option value="umur">Umur</option>
                      <option value="nama_produk">Nama Produk</option>
                      <option value="lokasi">Lokasi</option>
                      <option value="lokasi_fisik">Lokasi Rak</option>
                    </select>
                  </div>
                  <div id='f_search'>
                    <div class="col-md-3" >
                      <select class="form-control input-sm" name="cmbOperator" id="cmbOperator">
                        <option value=">">Older than</option>
                        <option value="<">Newer than</option>
                      </select>
                    </div>
                    <div class="col-md-3">
                      <input type="number" class="form-control input-sm" id="search" name="search"placeholder="Day" onkeypress="return isNumberKey(event)" >
                    </div>
                  </div>
                  <div class="col-md-3">
                    <button type="button" class="btn btn-sm btn-default btn-flat" id="btn-search" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing..."> <span class="fa fa-search" ></span> Proses</button>
                  </div>
                </div>   

                <div class="col-md-4">
                    <!--div class="panel-heading" role="tab" id="advanced">
                        <h5 class="panel-title">
                          <div data-toggle="collapse" href="#advancedSearch" aria-expanded="false" aria-controls="advancedSearch" class='collapsed' style='cursor:pointer;'><i class="showAdvanced glyphicon glyphicon-triangle-bottom"></i> Advanced  </div>
                        </h5>
                    </div-->
                      <div class="pull-right text-right">
                        <div id='pagination'></div>
                      </div>
                </div>
              </div>
              <br>

              <!-- Panel Result -->
              <div class="col-md-12 col-xs-12" style="padding-top: 8px;">
                <div class="panel panel-default">
                    <div class="panel-collapse collapsed" role="tabpanel" aria-labelledby="result" >
                      <div class="panel-body" style="padding: 5px">
                        <!-- kiri -->
                        <div class="col-12 col-sm-12 col-md-6">
                            <div class="form-group" style="margin-bottom: 0px;">
                              <div class="col-md-12">
                               <label><div id='total_record'>Total Data : 0</div></label>
                               <label class="pull-right text-right"><input type="checkbox" name="transit[]" value="Transit Location"> Transit Location </label>
                              </div>
                            </div>
                            <div class="form-group" style="margin-bottom: 0px;">
                                <div class="col-sm-3 col-md-3 col-xs-4">
                                  <span class="fa fa-download"></span> <label>Export</label>
                                </div>
                                <div class="col-12 col-sm-8 col-md-8">
                                  <input type="hidden" name="sql" id="sql">
                                  <button type="submit" id="btn-excel" name="btn-excel" class="btn btn-default btn-sm">
                                    <i class="fa fa-file-excel-o"></i>  Excel
                                  </button>
                                </div>
                            </div>
                            <div class="form-group" style="margin-bottom: 0px;">
                            <div class="col-12 col-sm-3 col-md-3">
                              <span class="fa fa-database"></span> <label>Group By</label>
                            </div>
                            <div class="col-12 col-sm-8 col-md-8" id="groupBy">
                              <li onclick="groupBy('nama_produk','Nama Produk',0)" class="li-adv" data-index="0" >Nama Produk</li>
                              <li onclick="groupBy('lokasi','Lokasi',1)" class="li-adv" data-index="1">Lokasi</li>
                              <li onclick="groupBy('lokasi_fisik','Lokasi Fisik',2)" class="li-adv" data-index="2">Lokasi Fisik</li>
                              <li onclick="groupBy('nama_grade','Grade',3)" class="li-adv" data-index="3">Grade</li>
                            </div>
                          </div>

                        </div>
                        <!-- /. kiri -->
                        <!-- kanan -->
                        <div class="col-12 col-sm-12 col-md-6">
                            <div class="form-group">
                                <div class="col-12 col-sm-12 col-md-12">
                                  <span class="fa fa-filter"></span> 
                                  <label>Result :</label>
                                  <span class="result" id="result">
                                  </span>
                                </div>
                            </div>
                        </div>
                        <!-- /. kanan -->
                      </div>
                  </div>
                </div>
              </div>
              <!-- /.Panel Result -->

              <!-- Panel Advance -->
              <!--div class="col-md-12 col-xs-12 ">
                <div class="panel panel-default" style="margin-bottom: 0px;">
                    <div id="advancedSearch" class="panel-collapse collapse" role="tabpanel" aria-labelledby="advanced" >
                      <div class="panel-body">
                        <div class="col-12 col-sm-12 col-md-6" >

                          <div class="form-group">
                            <div class="col-12 col-sm-3 col-md-3">
                              <span class="fa fa-database"></span> <label>Group By</label>
                            </div>
                            <div class="col-12 col-sm-8 col-md-8" id="groupBy">
                              <li onclick="groupBy('nama_produk','Nama Produk',0)" class="li-adv" data-index="0" >Nama Produk</li>
                              <li onclick="groupBy('lot','Lot',1)" class="li-adv" data-index="1">Lot</li>
                              <li onclick="groupBy('lokasi','Lokasi',2)" class="li-adv" data-index="2">Lokasi</li>
                              <li onclick="groupBy('lokasi_fisik','Lokasi Rak',3)" class="li-adv" data-index="3">Lokasi Rak</li>
                              <li onclick="groupBy('umur_produk','Umur Produk',4)" class="li-adv" data-index="4">Umur Produk</li>
                            </div>
                          </div>

                        </div><!-- /.col-md group caption>

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
                        </div><!-- /.col-md-6 tabel filter>

                      </div><!-- /.panel body >
                    </div><!-- /.advance collapse >
                </div>
              <!-- /. Panel Advance -->

          <div class="box-body">
            <div class="col-sm-12 table-responsive">
              <div class="table_scroll">
                <div class="table_scroll_head">
                  <div class="divListviewHead">
                      <table id="example1" class="table" border="0">
                          <thead>
                            <tr>
                              <th  class="style no"  >No. </th>
                              <th  class='style' ><a class="column_sort" id="lot" data-order="desc" href="javascript:void(0)">Lot</a></th>
                              <th  class='style min-width-80' ><a class="column_sort" id="nama_grade" data-order="desc" href="javascript:void(0)">Grade</a></th>
                              <th  class='style min-width-100' ><a class="column_sort" id="move_date" data-order="desc" href="javascript:void(0)">Tgl diterima</a></th>
                              <th  class='style' ><a class="column_sort" id="lokasi" data-order="desc" href="javascript:void(0)">Lokasi</a></th>
                              <th  class='style min-width-120' ><a class="column_sort" id="lokasi_fisik" data-order="desc" href="javascript:void(0)">Lokasi Fisik</a></th>
                              <th  class='style min-width-80'  ><a class="column_sort" id="kode_produk" data-order="desc" href="javascript:void(0)">kode Produk</a></th>
                              <th  class='style min-width-140' ><a class="column_sort" id="nama_produk" data-order="desc" href="javascript:void(0)">Nama Produk</a></th>
                              <th  class='style min-width-100' ><a class="column_sort" id="qty" data-order="desc" href="javascript:void(0)">Qty1</a></th>
                              <th  class='style min-width-100' ><a class="column_sort" id="qty2" data-order="desc" href="javascript:void(0)">Qty2</a></th>
                              <th  class='style min-width-100' ><a class="column_sort" id="lebar_greige" data-order="desc" href="javascript:void(0)">Lbr Greige</a></th>
                              <th  class='style min-width-100' ><a class="column_sort" id="lebar_jadi" data-order="desc" href="javascript:void(0)">Lbr Jadi</th>
                              <th  class='style' >Umur (Hari)</th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr>
                              <td colspan="16" align="center">Tidak ada Data</td>
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
          <!-- /.box-body -->
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

  $(document).ready(function() {
    $('#sql').val('');
  });

  // disable enter
  $(window).keydown(function(event){
    if(event.keyCode == 13) {
      event.preventDefault();
      return false;
    }
  });
  
  /*
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

  */

  //show / hide collapse child in tabel
   $(document).on("hide.bs.collapse show.bs.collapse", ".child", function (event) {
      //alert('tes');
      $(this).prev().find(".glyphicon").toggleClass("glyphicon-plus glyphicon-minus");
      event.stopPropagation();
  });

  function isNumberKey(evt){
    var charCode = (evt.which) ? evt.which : evt.keyCode
    if (charCode > 31 && (charCode < 48 || charCode > 57))
        return false;
    return true;
}

   //html entities javascript
  function htmlentities_script(str) {
    return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
  }


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

  var arr_filter    = [];
  var arr_group     = [];
  var tmp_arr_group = [];
  var arr_order     = [];
  
  // set default body onload page
  function get_default(){

    $("#cmbSearch").prop('selectedIndex',0);

    var groupBy     = 'lokasi';
    var id          = 'group-'+groupBy;
    var caption     = 'Lokasi';
    var dataIndex   = 1;

    arr_group.push({type:'group', nama_field:groupBy, id:id,  index_group:dataIndex});
    
    // add tags to result                
    var span = '<span class="breadcumb "><span class="breadcumb-field">Group By = </span><span class="breadcumb-value"><span class="ul-pl-02">'+caption+'</span><i class="fa fa-times" data-type="group" data-search="'+groupBy+'" id="'+htmlentities_script(id)+'"></i></span></span>';
  
    $('#result').append(span);
    $('.breadcumb .breadcumb-value').find('i').addClass("breadcumb-close");
  
    $('#groupBy [data-index="'+dataIndex+'"]').addClass('badge bg-red');//add class badge bg red
  
    loadSearchData();
  }


  // translate field cmb
  function translate_cmb(field){
    if(field == 'lokasi'){
      field = 'Lokasi';
    }else if(field == 'lokasi_fisik'){
      field = 'Lokasi Rak';
    }else if(field == 'kode_produk'){
      field = 'Kode Produk';
    }else if(field == 'nama_produk'){
      field = 'Nama Produk';
    }else if(field == 'nama_grade'){
      field = 'Grade';
    }else if(field == 'create_date'){
      field = 'Tgl Dibuat';
    }else if(field == 'umur'){
      field = 'Umur';
    }
    return field;
  }

  // change combobx filter
  $('#cmbSearch').on('change', function(e){
    value = $(this).val();
    if(value == 'lokasi'){
      var value = '<div class="col-md-6"> ';
          value += "<select class='form-control input-sm value width-input' name='search' id='search'  >";
          value += "<option value=''>Pilih Lokasi Stock</option>";
          value += "<?php foreach($warehouse as $row){ echo "<option>".$row->stock_location."</option>";} ?>";
          value += "</select>";
          value += '</div>';
          
          $('#f_search').html(value);
    }else if( value == 'umur'){
      var value = '<div class="col-md-3"> ';
          value +=  '<select class="form-control input-sm" name="cmbOperator" id="cmbOperator">';
          value +=  '<option value=">">Older than</option>'
          value +=  '<option value="<">Newer than</option>'
          value += "</select>";
          value += '</div>';
          value += '<div class="col-md-3">';
          value += '<input type="number" class="form-control input-sm" id="search" name="search" placeholder="Day" onkeypress="return isNumberKey(event)">';
          value += '</div>';
          $('#f_search').html(value);
    }else{
      var value = '<div class="col-md-6"> ';
          value += ' <input type="text" class="form-control input-sm" id="search" name="search" >';
          value += '</div>';
          $('#f_search').html(value);
    }

  }); 


  // klik btn search
  $('#btn-search').on('click', function(e){
    //alert('masuk');
    var search   = $('#search').val();
    var cmbSearch = $('#cmbSearch').val();
    var cmbOperator = $('#cmbOperator').val();

    var id      = 'id-'+search;
    var caption_field = translate_cmb(cmbSearch);// translate cmbsearch

    if(search == 'kosong'){
      alert_modal_warning('Filter tidak boleh Kosong !');
    }else{

      // show loading
      $("#example1_processing").css('display',''); 

      if(search != ''){
        // add to array 
        arr_filter.push({type:'search', nama_field:cmbSearch, operator:cmbOperator,  value:search, id:id});

        if(cmbSearch == 'umur'){
          if(cmbOperator == '<'){
            caption_sparate ='Newer Than';
          }else if(cmbOperator == '>'){
            caption_sparate ='Older Than';
          }
        }else if(cmbSearch == 'lokasi' || cmbSearch =='lokasi_fisik'){
          caption_sparate = '=';
        }else{
          caption_sparate = 'LIKE';
        }
        
        // add tags to result                
        var span = ' <span class="breadcumb "> <span class="breadcumb-field"> '+caption_field+' '+caption_sparate+' </span><span class="breadcumb-value"><span class="ul-pl-02">'+search+'</span> <i class="fa fa-times" data-type="search" data-search="'+cmbSearch+'" id="'+htmlentities_script(id)+'" data-togle="tooltip" title="Delete Filter"></i></span></span>';
        
        $('#result').append(span);
        $('.breadcumb .breadcumb-value').find('i').addClass("breadcumb-close");
      }

      $('#btn-search').button('loading');

      // loaddata
      loadSearchData();

    }
         
  });


  // remove tags result
  $("body").delegate(".breadcumb-close", "click", function(){

    var id         = $(this).attr('id');
    var nama_field = $(this).attr('data-search');
    var data_type  = $(this).attr('data-type'); // type search nya advance / biasa

    // remove array_filter
    $.each(arr_filter, function(index,isi){
      //alert('array '+JSON.stringify(arr_filter));

        if(arr_filter[index].type == data_type){

          if(arr_filter[index].nama_field == nama_field && arr_filter[index].id == id){
            arr_filter.splice(index, 1);
            return false;
          }
        }
    });

    // remove array_group
    removeGroup(data_type,nama_field,id);

    // loadSearchData
    loadSearchData();
    //alert('close');
    $(this).parents(".breadcumb").remove();

  });

  // next pagination
  $('#pagination').on('click','a',function(e){
      
      e.preventDefault(); 
      var pageNum = $(this).attr('data-ci-pagination-page');
      //alert(pageNum);
      loadSearchData(pageNum)

  });

  function loadSearchData(pageNum=null){

     $("#example1_processing").css('display','');// show loading processing in table

     if(pageNum == null){
        pageNum = 0;
     }

    var check_transit  = false;
    var checkboxes_arr =  new Array(); 

    var checkboxes_arr = $('input[name="transit[]"]').map(function(e, i) {
            if(this.checked == true){
              check_transit = true;
              return i.value;
            }
    }).get();

     $("#example1 tbody").remove();
     please_wait(function(){});

     $.ajax({
                type: "POST",
                dataType : "JSON",
                url : '<?=base_url()?>report/stock/loadData/'+pageNum,
                data: {arr_filter:JSON.stringify(arr_filter), arr_group:JSON.stringify(arr_group), arr_oder:arr_order, transit:check_transit},
                success: function(data){

                  $('#pagination').html(data.pagination);
                  $('#total_record').html(data.total_record);
                  $('#sql').val(data.sql);

                  let tbody = $("<tbody />");
                  let no    = 0;
                  let empty = true;
                  if(data.group == true){
                    empty = false;

                    let $ro    = 1;
                    let $row   = '';

                    $.each(data.record, function(key, value){
                        let $group = 'group-of-rows-'+$ro;

                        $row  += "<tbody id="+$group+">";
                        $row  += "<tr  class='oe_group_header'>";
                        $row  += "<td class='show collapsed group1' href='#' style='cursor:pointer;'  data-content='edit' data-isi='"+value.nama_field+"' data-group='"+value.by+"' data-tbody='"+$group+"'data-root='"+$group+"' node-root='Yes' group-ke='1'><i class='glyphicon glyphicon-plus' ></i></td>";
                        $row += "<td colspan='5'>"+value.grouping+"</td>";
                        $row += "<td align='right'>"+value.jml+"</td>";
                        $row += "<td colspan='3' class='list_pagination'></td>";
                        $row += "<td colspan='2' ></td>";
                        $row += "</tr>";
                        $row += "</tbody>";
                        $ro++;
                    });

                    $("#example1").append($row);

                  }else{

                      //if(data.sql != ''){
                      // buat list record
                      tbody = loadRecord(data.record);
                      if(data.record.length == 0){
                        var tr = $("<tr>").append($("<td colspan='16' align='center'>").text('Tidak ada Data'));
                        tbody.append(tr);
                      }
                      $("#example1").append(tbody);
                      //}
                  }
                    $('#btn-search').button('reset');

                    $('#search').val('');// kosongkan search
                    $("#example1_processing").css('display','none'); // hidden loading
                    unblockUI( function() {});
                },error : function(jqXHR, textStatus, errorThrown){
                  //alert(jqXHR.responseText);
                  alert('error data '+jqXHR.responseText);
                  $("#example1_processing").css('display','none'); // hidden loading
                  $('#btn-search').button('reset');
                  unblockUI( function() {});
                }
     });
  }

    // disable enter input text
    $(document).ready(function() {
      $('input[type="text"]').keydown(function(event){
        if(event.keyCode == 13) {
          event.preventDefault();
          return false;
        }
      });
    });

    // cek sql saat klik btn excel
    $("#form_filter").submit(function(){

        var sql = $('#sql').val();
        if(sql == ''){
          alert('Silahkan Filter Terlebih Dahulu !');
          return false;
        }else{
          
        }

    });

    // remove aray group
    function removeGroup(data_type,nama_field,id){
      
      $.each(arr_group,  function(index,isi){

        if(arr_group[index].type == data_type){
            if(arr_group[index].nama_field == nama_field && arr_group[index].id == id){
              //alert('ketemu group');
              dataIndex =arr_group[index].index_group
              arr_group.splice(index, 1);
              //alert(dataIndex);
              $('#groupBy [data-index="'+dataIndex+'"]').removeClass('badge bg-red');//remove class badge bg red
              return false;
            }
        }

      });

      return true;
    }

    // klik tags group by
    function groupBy(groupBy,caption,dataIndex){

      var check_arr_group = false;
      var indexKe     = '';
      var id          = 'group-'+groupBy;
      var empty       = true;

      $.each(arr_group,  function(index,isi){
          if(arr_group[index].nama_field == groupBy && arr_group[index].index_group == dataIndex){
            //alert('ada');
            empty = false;
          }
      });

      if(empty == false){
        removeGroup('group',groupBy,id);
        $('#'+id+'').parents(".breadcumb").remove();

      }else{

        if(arr_group.length == 2){
          alert_modal_warning('Maaf, Pilihan Group By Tidak Bisa Lebih dari 2 ')
        }else{

          // add to array 
          arr_group.push({type:'group', nama_field:groupBy, id:id,  index_group:dataIndex});

          // add tags to result                
          var span = '<span class="breadcumb "><span class="breadcumb-field">Group By = </span><span class="breadcumb-value"><span class="ul-pl-02">'+caption+'</span><i class="fa fa-times" data-type="group" data-search="'+groupBy+'" id="'+htmlentities_script(id)+'"></i></span></span>';

          $('#result').append(span);
          $('.breadcumb .breadcumb-value').find('i').addClass("breadcumb-close");

          $('#groupBy [data-index="'+dataIndex+'"]').addClass('badge bg-red');//add class badge bg red
        }

      }

      loadSearchData();
    }


  $(document).on("click", ".group1", function(e){
  
    var kode      = '';
    var tbody_id  = '';
    var tampil    = false;
    var html      = ''; 
    var info_page = '';
    var page_next = '';
    var page_prev = '';
    //var id_dept   = '<?php  echo $id_dept;?>';

    // ambil data berdasarkan data-content='edit'
    $(this).parents("tr").find("td[data-content='edit']").each(function(){
        kode     = $(this).attr('data-isi');
        group_by = $(this).attr('data-group');
        tbody_id = $(this).attr('data-tbody');
        group_ke = $(this).attr('group-ke');
        root     = $(this).attr('data-root');
        node_root = $(this).attr('node-root');

        $(this).toggleClass("collapsed collapse"); // ganti collapsed, collapse
        if($(this).hasClass("collapsed") == true){
          tampil = true;
        }else if($(this).hasClass("collapsed") == false){
          tampil = false;
        }

        $(this).find(".glyphicon").toggleClass("glyphicon-plus glyphicon-minus"); // ganti icon + jadi minus
    });
    //alert('array loadchild');


    if(tampil == true){
      $("#example1 tbody[data-parent='"+tbody_id+"']").remove();// remove child by groupby
      if(node_root == 'Yes'){
        $("#example1 tbody[data-root='"+root+"']").remove();// remove child by root
        if(tmp_arr_group.length > 0){ // berlaku untuk group by 2
          // hapus tmp_arr_group
          $.each(tmp_arr_group,  function(index,isi){
            if(tmp_arr_group[index].tbody_id == tbody_id){
              tmp_arr_group.splice(index,1); 
              return false;
            }

          });
        }
      }
      $("#example1 tbody[id='"+tbody_id+"'] tr" ).find('td.list_pagination').text('');// remove btn pagination by tbody_id

    }else{

        var check_transit  = false;
        var checkboxes_arr =  new Array(); 

        var checkboxes_arr = $('input[name="transit[]"]').map(function(e, i) {
                if(this.checked == true){
                  check_transit = true;
                  return i.value;
                }
        }).get();
     
       $.ajax({
            type      : 'POST',
            dataType  : 'json',
            url       : '<?=base_url()?>report/stock/loadChild',
            data      : {kode:kode, group_by:group_by, tbody_id:tbody_id, group_ke:group_ke, record:'0', arr_filter:JSON.stringify(arr_filter), arr_group:JSON.stringify(arr_group), root:root, tmp_arr_group:JSON.stringify(tmp_arr_group), arr_order:arr_order, transit:check_transit},
            success:function(data){

              if(data.list_group != ''){
                $('#example1 tbody[id='+data.tbody_id+']').after(data.list_group);
                tmp_arr_group.push(data.tmp_arr_group);
              }else{
                let tbody = $("<tbody data-root='"+data.root+"' data-parent='"+tbody_id+"' />");
                let row   = '';
                let no    = 1;

                $.each(data.record, function(key, value) {

                      var tr = $("<tr style='background-color: #f2f2f2;'>").append(
                                $("<td>").text(no),
                                $("<td >").text(value.lot),
                                $("<td>").text(value.grade),
                                $("<td>").text(value.tgl_diterima),
                                $("<td>").text(value.lokasi),
                                $("<td>").text(value.lokasi_fisik),
                                $("<td>").text(value.kode_produk),
                                $("<td>").text(value.nama_produk),
                                $("<td align='right'>").text(value.qty),
                                $("<td align='right'>").text(value.qty2),
                                $("<td align='right'>").text(value.lebar_greige),
                                $("<td align='right'>").text(value.lebar_jadi),
                                $("<td>").text(value.umur_produk),
                      );
                      tbody.append(tr);
                      no++;
                });
               
                $('#example1 tbody[id='+data.tbody_id+']').after(tbody);

                  // buat pagination jika data lebih dari 10
                if(data.total_record > data.limit){
                  info_page = data.page_now+'/'+data.all_page;
                  page_prev = data.page_now;
                  page_next = data.page_now+1;
                  $('#example1 tbody[id='+data.tbody_id+'] tr' ).find("td.list_pagination").each(function(){
                    html  += '<button type="button" class="btn btn-xs btn-default" data-pager-action="previous" info-page-now='+page_prev+' style="visibility: hidden;"><</button>';
                    html  += ' <span class="list_page_state"> '+info_page+' </span>';
                    html  += ' <button type="button" class="btn btn-xs btn-default" data-pager-action="next" info-page-now='+page_next+'>></button>' ;
                    $(this).html(html);
                  });
                }

              }

            },error: function (jqXHR, textStatus, errorThrown){
              alert(jqXHR.responseText);
              alert('Error Load Child Root');
            }
      });

    }
    
  });

  // klik button previous
  $(document).on("click", "button[data-pager-action='previous']", function(e){
    
    $(this).parents("tr").find("td[data-content='edit']").each(function(){
        kode     = $(this).attr('data-isi');
        group_by = $(this).attr('data-group');
        tbody_id = $(this).attr('data-tbody');
        group_ke = $(this).attr('group-ke');
        root     = $(this).attr('data-root');

    });

    $(this).each(function(){
        page = $(this).attr('info-page-now');
    });

    action = 'prev';
    loadPageChild(kode,group_by,group_ke,tbody_id,page,action,root)
  });



 // klik button next
  $(document).on("click", "button[data-pager-action='next']", function(e){
    
    $(this).parents("tr").find("td[data-content='edit']").each(function(){
        kode     = $(this).attr('data-isi');
        group_by = $(this).attr('data-group');
        tbody_id = $(this).attr('data-tbody');
        group_ke = $(this).attr('group-ke');
        root     = $(this).attr('data-root');

    });

    $(this).each(function(){
        page = $(this).attr('info-page-now');
    });
    action = 'next';
    loadPageChild(kode,group_by,group_ke,tbody_id,page,action,root)
  });


  // untuk meload child dari next/prev button
  function loadPageChild(kode,group_by,group_ke,tbody_id,page,action,root){

            var check_transit  = false;
            var checkboxes_arr =  new Array(); 

            var checkboxes_arr = $('input[name="transit[]"]').map(function(e, i) {
                    if(this.checked == true){
                      check_transit = true;
                      return i.value;
                    }

            }).get();

           $.ajax({
            type : 'POST',
            dataType: 'json',
            url  : '<?=base_url()?>report/stock/loadChild',
            data : {  kode:kode,  record:page, arr_filter:JSON.stringify(arr_filter), arr_group:JSON.stringify(arr_group), group_by:group_by, tbody_id:tbody_id, group_ke:group_ke, root:root, tmp_arr_group:JSON.stringify(tmp_arr_group), arr_order:arr_order, transit:check_transit},
            success:function(data){
            
                $("#example1 tbody[data-parent='"+tbody_id+"']").remove();// remove child by data-parent

                let tbody = $("<tbody data-root='"+data.root+"' data-parent='"+tbody_id+"' />");
                let no  = 1;

                $.each(data.record, function(key, value) {

                      var tr = $("<tr style='background-color: #f2f2f2;'>").append(
                                  $("<td>").text(no),
                                  $("<td >").text(value.lot),
                                  $("<td>").text(value.grade),
                                  $("<td>").text(value.tgl_diterima),
                                  $("<td>").text(value.lokasi),
                                  $("<td>").text(value.lokasi_fisik),
                                  $("<td>").text(value.kode_produk),
                                  $("<td>").text(value.nama_produk),
                                  $("<td align='right'>").text(value.qty),
                                  $("<td align='right'>").text(value.qty2),
                                  $("<td align='right'>").text(value.lebar_greige),
                                  $("<td align='right'>").text(value.lebar_jadi),
                                  $("<td>").text(value.umur_produk),
                       );
                      tbody.append(tr);
                      no++;
                });
               
                $('#example1 tbody[id='+data.tbody_id+']').after(tbody);

                if(action == 'prev' && data.page_now == 2){// jika saat previus dan page ==2 atau maka hidden btn prev
                  page_prev_start = 1;
                }else{
                  page_prev_start = '';
                }
                page_now  = data.page_now;
                info_page = data.page_now-1+'/'+data.all_page;// info untuk page yg dibuka dari all page

                $("#example1 tbody[id='"+tbody_id+"'] tr ").find('td.list_pagination span.list_page_state').text(info_page);

                if(page_prev_start == 1){
                  $('#example1 tbody[id='+data.tbody_id+'] tr td.list_pagination').find("button[data-pager-action='next']").css("visibility","");
                  $('#example1 tbody[id='+data.tbody_id+'] tr td.list_pagination').find("button[data-pager-action='previous']").css("visibility","hidden");
                }

                if(page_now > 2 ){
                  $('#example1 tbody[id='+data.tbody_id+'] tr td.list_pagination').find("button[data-pager-action='next']").css("visibility","");
                  $('#example1 tbody[id='+data.tbody_id+'] tr td.list_pagination').find("button[data-pager-action='previous']").css("visibility","");
                }


                if(page_now > data.all_page){
                  $('#example1 tbody[id='+data.tbody_id+'] tr td.list_pagination').find("button[data-pager-action='next']").css("visibility","hidden");
                  $('#example1 tbody[id='+data.tbody_id+'] tr td.list_pagination').find("button[data-pager-action='previous']").css("visibility","");
                }

                page_prev = page_now-2;
                page_next = page_now;
                $("#example1 tbody[id='"+tbody_id+"'] tr ").find("td.list_pagination button[data-pager-action='previous'] ").attr('info-page-now',page_prev);

                $("#example1 tbody[id='"+tbody_id+"'] tr ").find("td.list_pagination button[data-pager-action='next'] ").attr('info-page-now',page_next);
                //alert(action);

            },error: function (jqXHR, textStatus, errorThrown){
              //alert(jqXHR.responseText);
              alert('Error Load Page Child');
            }
      });

    return;
  }


  // klik column sort 
  $(document).on('click', '.column_sort', function(){

      $("#example1_processing").css('display','');// show loading processing in table
      please_wait(function(){});
    
      $("#example1 tbody").remove();
      var nama_kolom  = $(this).attr("id");
      var order       = $(this).attr("data-order");
      var arrow       = '';
      var same        = false;
      var index_ke    = '';
      var hapus       = false;
      arr_order   = [];

      arr_order.push({column:nama_kolom, sort:order });

      //let index = arr_order.indexOf();
      /*
      // remove array_filter
      $.each(arr_order, function(index,isi){
        //alert('array '+JSON.stringify(arr_filter));
          if(arr_order[index].column == nama_kolom){
            same = true;
            index_ke = index;
          }
      });
      if(index_ke >= 0 && same == true){
        arr_order.splice(index_ke,1);
        hapus  = true;
      }

      if(arr_order.length == 0 || hapus  == true || same == false){
      }
      */

      //alert('akhir 2 '+JSON.stringify(arr_order));

      if(order == 'desc'){
        //$(this).addClass('fa fa-arrow-down');
        $('.column_sort .fa').remove('');
        arrow = ' <span class="fa fa-sort-amount-desc"></span>';

      } else {
        $('.column_sort .fa').remove('');
        arrow = ' <span class="fa fa-sort-amount-asc"></span>';
      }

      var check_transit  = false;
      var checkboxes_arr =  new Array(); 
      var checkboxes_arr = $('input[name="transit[]"]').map(function(e, i) {
                if(this.checked == true){
                  check_transit = true;
                  return i.value;
                }
      }).get();
      
      $.ajax({
          type : 'POST',
          dataType: 'json',
          url : '<?=base_url()?>report/stock/loadData/0',
          data:{arr_order:arr_order, arr_filter:JSON.stringify(arr_filter), arr_group:JSON.stringify(arr_group), transit:check_transit},
          beforeSend: function() {
            if(order == 'desc'){
              $('#'+nama_kolom+'').attr('data-order','asc');
            }else{
              $('#'+nama_kolom+'').attr('data-order','desc');
            }
  
            $('#'+nama_kolom+'').append(arrow);
          },
          success:function(data){
            //alert('berhasil');

            if(data.group == true){
                  empty = false;

                  let $ro    = 1;
                  let $row   = '';

                  $.each(data.record, function(key, value){
                        let $group = 'group-of-rows-'+$ro;

                        $row  += "<tbody id="+$group+">";
                        $row  += "<tr  class='oe_group_header'>";
                        $row  += "<td class='show collapsed group1' href='#' style='cursor:pointer;'  data-content='edit' data-isi='"+value.nama_field+"' data-group='"+value.by+"' data-tbody='"+$group+"'data-root='"+$group+"' node-root='Yes' group-ke='1'><i class='glyphicon glyphicon-plus' ></i></td>";
                        $row += "<td colspan='5'>"+value.grouping+"</td>";
                        $row += "<td align='right'>"+value.jml+"</td>";
                        $row += "<td colspan='3' class='list_pagination'></td>";
                        $row += "<td colspan='2' ></td>";
                        $row += "</tr>";
                        $row += "</tbody>";
                        $ro++;
                  });

                  $("#example1").append($row);

            }else{

              // buat list record
              tbody = loadRecord(data.record);
            
              $("#example1").append(tbody);
            }

            $("#example1_processing").css('display','none');// hidden loading processing in table
            unblockUI( function() {});

          },error: function (jqXHR, textStatus, errorThrown){
            alert(jqXHR.responseText);
            //alert('Error Data');
            $("#example1_processing").css('display','none');// hidden loading processing in table
            unblockUI( function() {});
          }
      })
      
  }); 


  // load record items
  function loadRecord(record){

      var tbody = $("<tbody />");
      var no    = 1;
      $.each(record, function(key, value) {
                 
            var tr = $("<tr style='background-color: #f2f2f2;'>").append(
                      $("<td>").text(no++),
                      $("<td >").text(value.lot),
                      $("<td>").text(value.grade),
                      $("<td>").text(value.tgl_diterima),
                      $("<td>").text(value.lokasi),
                      $("<td>").text(value.lokasi_fisik),
                      $("<td>").text(value.kode_produk),
                      $("<td>").text(value.nama_produk),
                      $("<td align='right'>").text(value.qty),
                      $("<td align='right'>").text(value.qty2),
                      $("<td align='right'>").text(value.lebar_greige),
                      $("<td align='right'>").text(value.lebar_jadi),
                      $("<td>").text(value.umur_produk),
            );    
            tbody.append(tr);
      });

      return tbody;

  }

</script>

</body>
</html>