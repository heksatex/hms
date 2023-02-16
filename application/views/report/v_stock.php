
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

    .nowrap{
      white-space: nowrap;
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
            <form name="input" class="form-horizontal" role="form">
              <div class="col-md-12">
                <div class="col-md-8">
                  <div class="col-md-3">
                    <select class="form-control input-sm" name="cmbSearch" id="cmbSearch">
                      <option value="umur">Umur</option>
                      <option value="kode_produk">Kode Produk</option>
                      <option value="nama_produk">Nama Produk</option>
                      <option value="category">Kategori</option>
                      <option value="lot">Lot</option>
                      <option value="nama_grade">Grade</option>
                      <option value="lokasi">Lokasi</option>
                      <option value="lokasi_fisik">Lokasi Fisik</option>
                      <option value="sales_order">Sales Contract [SC]</option>
                      <option value="sales_group">Marketing</option>
                      <option value="opname">Status Opname</option>
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
                      <input type="number" class="form-control input-sm" id="search" name="search"placeholder="Day" onkeypress="return isNumberKey(event)" onkeydown=" event_input(event)" >
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
            </form>
            <br>
              <!-- Panel Result -->
              <div class="col-md-12 col-xs-12" style="padding-top: 8px;">
                <div class="panel panel-default">
                    <div class="panel-collapse collapsed" role="tabpanel" aria-labelledby="result" >
                      <div class="panel-body" style="padding: 5px">
                        <form name="input" class="form-horizontal" role="form" id="form_export"  action="<?=base_url()?>report/stock/export_excel_stock" method="POST">
                          <!-- kiri -->
                          <div class="col-12 col-sm-12 col-md-6">
                              <div class="form-group" style="margin-bottom: 0px;">
                                <div class="col-md-12">
                                <label><div id='total_record'>Total Data : 0</div></label>
                                </div>
                              </div>
                              <div class="form-group" style="margin-bottom: 0px;">
                                  <div class="col-sm-3 col-md-3 col-xs-4">
                                    <span class="fa fa-download"></span> <label>Export</label>
                                  </div>
                                  <div class="col-12 col-sm-8 col-md-8">
                                  
                                      <input type="hidden" name="sql" id="sql">
                                      <button type="submit" id="btn-excel" name="btn-excel" class="btn btn-default btn-sm">
                                        <i class="fa fa-file-excel-o" style="color:green"></i>  Excel
                                      </button>
                                  
                                  </div>
                              </div>
                              <div class="form-group" style="margin-bottom: 0px;">
                                <div class="col-12 col-sm-3 col-md-3">
                                  <span class="fa fa-database"></span> <label>Group By</label>
                                </div>
                                <div class="col-12 col-sm-8 col-md-8" id="groupBy">
                                  <li onclick="groupBy('category','Kategori',0)" class="li-adv" data-index="0">Kategori</li>
                                  <li onclick="groupBy('nama_produk','Nama Produk',1)" class="li-adv" data-index="1" >Nama Produk</li>
                                  <li onclick="groupBy('lokasi','Lokasi',2)" class="li-adv" data-index="2">Lokasi</li>
                                  <li onclick="groupBy('lokasi_fisik','Lokasi Fisik',3)" class="li-adv" data-index="3">Lokasi Fisik</li>
                                  <li onclick="groupBy('nama_grade','Grade',4)" class="li-adv" data-index="4">Grade</li>
                                </div>
                              </div>
                              <div class="form-group" style="margin-bottom: 0px;">
                                <div class="col-12 col-sm-3 col-md-3">
                                  <label><i class="fa fa-star"></i> Favorite </label>
                                </div>
                                <div class="col-12 col-sm-8 col-md-8" id="favorites">
                                  <?php 
                                      foreach ($user_filter as $val) {
                                        $id_filter = $val['id'];
                                        $id_span   = "span-".$val['id'];
                                  ?>
                                        <a onclick='favorite("<?php echo $id_filter;?>")' class="badge" id="<?php echo $id_filter; ?>">
                                            <?php echo $val['nama_filter'];?>
                                        </a>
                                        <span id="<?php echo $id_span;?>" aria-hidden="true" onclick="deleteFavorite('<?php echo $id_filter;?>')">
                                            <i class="fa fa-remove" style="cursor: pointer;" data-toggle='tooltip' title="delete favorite"></i>
                                        </span>
                                  <?php
                                      }
                                  ?>
                                </div>
                              </div>

                          </div>
                          <!-- /. kiri -->
                          <!-- kanan -->
                          <div class="col-12 col-sm-12 col-md-6">
                              <div class="form-group">
                                <div class="col-12 col-sm-12 col-md-12">
                                  <div data-toggle="collapse" href="#saveFilter" aria-expanded="false" aria-controls="saveFilter" class="collapsed" style="cursor: pointer;">
                                  <i class="saveFilter glyphicon glyphicon-triangle-bottom"></i><strong>Save Filter </strong>
                                  </div>
                                  <div id="saveFilter" class="collapse">
                                    <div class="panel-body ">
                                      <div class="col-md-12">
                                        <input type="text" name="nama_filter" id="nama_filter" class="form-control input-sm" placeholder="Name Filter">
                                      </div>
                                      <div class="col-md-12">
                                        <input type="checkbox" name="check_default" id="check_default">
                                        <label>use by default</label>
                                      </div>
                                      <div class="col-md-12">
                                        <button type="button" id="btn-simpan-filter" class="btn btn-default btn-sm">Save</button>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                              <div class="form-group">
                                  <div class="col-12 col-sm-12 col-md-12">
                                    <span class="fa fa-filter"></span> 
                                    <label> Result :</label>
                                    <span class="result" id="result">
                                    </span>
                                  </div>
                              </div>
                          </div>
                          <!-- /. kanan -->
                        </form>
                      </div>
                  </div>
                </div>
              </div>
              <!-- /.Panel Result -->
            
             
            <div class="box-body">
              <div class="col-sm-12 table-responsive">
                  <div class="divListviewHead">
                      <div role="region" aria-labelledby="HeadersCol" tabindex="0" class="rowheaders">
                        <table id="example1" class="table table-condesed table-hover" border="0">
                          <thead>
                            <tr>
                              <th  class="style bb no"  >No. </th>
                              <th  class='style bb nowrap' ><a class="column_sort" id="lot" data-order="desc" href="javascript:void(0)">Lot</a></th>
                              <th  class='style bb min-width-80 nowrap' ><a class="column_sort" id="nama_grade" data-order="desc" href="javascript:void(0)"> Grade</a></th>
                              <th  class='style bb min-width-100 nowrap' ><a class="column_sort" id="move_date" data-order="desc" href="javascript:void(0)">Tgl diterima</a></th>
                              <th  class='style bb nowrap' ><a class="column_sort" id="lokasi" data-order="desc" href="javascript:void(0)">Lokasi</a></th>
                              <th  class='style bb min-width-120 nowrap' ><a class="column_sort" id="lokasi_fisik" data-order="desc" href="javascript:void(0)">Lokasi Fisik</a></th>
                              <th  class='style bb min-width-80 nowrap'  ><a class="column_sort" id="kode_produk" data-order="desc" href="javascript:void(0)">kode Produk</a></th>
                              <th  class='style bb min-width-140 nowrap' ><a class="column_sort" id="nama_produk" data-order="desc" href="javascript:void(0)">Nama Produk</a></th>
                              <th  class='style bb min-width-140 nowrap' ><a class="column_sort" id="category" data-order="desc" href="javascript:void(0)">Kategori</a></th>
                              <th  class='style bb min-width-100 nowrap' ><a class="column_sort" id="qty" data-order="desc" href="javascript:void(0)">Qty1</a></th>
                              <th  class='style bb min-width-100 nowrap' ><a class="column_sort" id="qty2" data-order="desc" href="javascript:void(0)">Qty2</a></th>
                              <th  class='style bb min-width-100 nowrap' ><a class="column_sort" id="lebar_greige" data-order="desc" href="javascript:void(0)">Lbr Greige</a></th>
                              <th  class='style bb min-width-100 nowrap' ><a class="column_sort" id="lebar_jadi" data-order="desc" href="javascript:void(0)">Lbr Jadi</th>
                              <th  class='style bb min-width-80 nowrap' ><a class="column_sort" id="sales_order" data-order="desc" href="javascript:void(0)">SC</th>
                              <th  class='style bb min-width-100 nowrap' ><a class="column_sort" id="sales_group" data-order="desc" href="javascript:void(0)">Marketing</th>
                              <th  class='style bb min-width-100 nowrap' ><a class="column_sort" id="qty_opname" data-order="desc" href="javascript:void(0)">Qty Opname</th>
                              <th  class='style bb' >Umur (Hari)</th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr>
                              <td colspan="18" align="center">Tidak ada Data</td>
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

  function event_input(event)
  { 
    if(event.keyCode == 13) {
        event.preventDefault();
          btn_search();
    }
  }

  // disable enter
  $('input[type="checkbox"]').keydown(function(event){
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

  // untuk buat array pilihan mst sales group
  var obj_sales = new Array();
    <?php 
      foreach($mst_sales_group as $key ){
    ?>
          obj_sales.push({kode:"<?php echo $key->kode_sales_group?>", name:"<?php echo $key->nama_sales_group;?>"});
    <?php 
      }
    ?>

  var obj_category = new Array();
    <?php 
      foreach($category as $key ){
    ?>
          obj_category.push({kode:"<?php echo $key->id?>", name:"<?php echo $key->nama_category;?>"});
    <?php 
      }
    ?>

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


  var arr_filter    = [];
  var arr_group     = [];
  var tmp_arr_group = [];
  var arr_order     = [];
  
  // set default body onload page
  function get_default(){

    $("#cmbSearch").prop('selectedIndex',0);

    var filter_id   = '<?php echo $filter_default;?>';

    if(filter_id != ''){
      // get favorit default
      $.ajax({
            type      : 'POST',
            dataType  : 'json',
            url       : '<?=base_url()?>report/stock/get_default',
            data      : {filter_id : filter_id},
            success:function(data){
              $.each(data.filter, function(index,value){
                arr_filter.push({type:'search', nama_field:value.name_field, operator:value.operator, value:value.value_filter, id:'id-'+value.value_filter, fav:'Yes',fav_id:filter_id});
              });
              $.each(data.group, function(index,value){
                id = 'group-'+value.name_field;
                arr_group.push({type:'group', nama_field:value.name_field, id:id,  index_group:value.data_index, fav:'Yes',fav_id:filter_id}); 
              });

              $.each(data.sort, function(index,value){
                arr_order.push({column:value.name_field, sort:value.sort, fav:'Yes',fav_id:filter_id });
              });
            },error: function (jqXHR, textStatus, errorThrown){
              alert('Error Load Default');
            },complete: function (data) {
              loadSearchData();
            }
      });

      $('#favorites #'+filter_id).addClass('bg-red');// css 
      
    }else{

      var groupBy     = 'lokasi';
      var id          = 'group-'+groupBy;
      var caption     = 'Lokasi';
      var dataIndex   = 2;

      arr_group.push({type:'group', nama_field:groupBy, id:id,  index_group:dataIndex, fav:'No'});
      // add tags to result                
      var span = '<span class="breadcumb "><span class="breadcumb-field">Group By = </span><span class="breadcumb-value"><span class="ul-pl-02">'+caption+'</span><i class="fa fa-times" data-type="group" data-search="'+groupBy+'" id="'+htmlentities_script(id)+'"></i></span></span>';
      $('#result').append(span);
      $('.breadcumb .breadcumb-value').find('i').addClass("breadcumb-close");
      $('#groupBy [data-index="'+dataIndex+'"]').addClass('badge bg-red');//add class badge bg red
      loadSearchData();
    }
  }

  // onlick Favorite
  function favorite(id){

    var id_same = false;
    var remove_arr_group  = [];
    var remove_arr_filter = [];
    var remove_arr_order  = [];

    // cek group fav
    $.each(arr_group,  function(index,isi){
        if(arr_group[index].fav =='Yes'){
            remove_arr_group.push(index);
            if(arr_group[index].fav_id == id){
              id_same = true;
            }
        }
    });
    // cek filter fav
    $.each(arr_filter,  function(index,isi){
        if(arr_filter[index].fav =='Yes'){
            remove_arr_filter.push(index);
            if(arr_filter[index].fav_id == id){
              id_same = true;
            }
        }
    });

    // cek order fav
    $.each(arr_order,  function(index,isi){
        if(arr_order[index].fav =='Yes'){
            remove_arr_order.push(index);
            if(arr_order[index].fav_id == id){
              id_same = true;
            }
        }
    });

    if(id_same == true){
        remove_arr_group.reverse().forEach(function(index) {
          arr_group.splice(index, 1);
          $('#favorites #'+id).removeClass('bg-red');// css 
        });

        remove_arr_filter.reverse().forEach(function(index) {
          arr_filter.splice(index, 1);
          $('#favorites #'+id).removeClass('bg-red');// css 
        });

        remove_arr_order.reverse().forEach(function(index) {
          arr_order.splice(index, 1);
          $('#favorites #'+id).removeClass('bg-red');// css 
        });
        loadSearchData();
    }else{
        remove_arr_group.reverse().forEach(function(index) {
          arr_group.splice(index, 1);
          $('#favorites a').removeClass('bg-red');// css 
        });

        remove_arr_filter.reverse().forEach(function(index) {
          arr_filter.splice(index, 1);
          $('#favorites a').removeClass('bg-red');// css 
        });

        remove_arr_order.reverse().forEach(function(index) {
          arr_order.splice(index, 1);
          $('#favorites #'+id).removeClass('bg-red');// css 
        });
        
        // get favorit default
        $.ajax({
              type      : 'POST',
              dataType  : 'json',
              url       : '<?=base_url()?>report/stock/get_default',
              data      : {filter_id : id},
              success:function(data){
                jml_group = parseInt(arr_group.length) +  parseInt(data.group.length);
                if(jml_group > 2 ){
                  alert_modal_warning('Maaf, Favorite tidak bisa di pilih karena terdapat Group By yang lebih dari 2');
                }else{
                  $.each(data.filter, function(index,value){
                    arr_filter.push({type:'search', nama_field:value.name_field, operator:value.operator, value:value.value_filter, id:'id-'+value.value_filter, fav:'Yes',fav_id:id});
                  });
                  $.each(data.group, function(index,value){
                    id_group = 'group-'+value.name_field;
                    arr_group.push({type:'group', nama_field:value.name_field, id:id_group,  index_group:value.data_index, fav:'Yes',fav_id:id}); 
                  });
                  $.each(data.sort, function(index,value){
                    arr_order.push({column:value.name_field, sort:value.sort, fav:'Yes',fav_id:id });
                  });
                  $('#favorites #'+id).addClass('bg-red');// css 
                }
              },error: function (jqXHR, textStatus, errorThrown){
                alert('Error Load Default');
              },complete: function (data) {
                loadSearchData();
              }
        });

    }
  }

  // delete favorite
  function deleteFavorite(id){
        
        // apa badge yg di hapus tu hapus
        var active     = $("#favorites a[id='"+id+"'] ").hasClass('badge bg-red');
        var remove_arr_group  = [];
        var remove_arr_filter = [];
        var remove_arr_order  = [];
        var id_same    = false;
         // cek group fav
        $.each(arr_group,  function(index,isi){
            if(arr_group[index].fav =='Yes'){
              if(arr_group[index].fav_id == id){
                  remove_arr_group.push(index);
                  id_same = true;
                }
            }
        });
        // cek filter fav
        $.each(arr_filter,  function(index,isi){
            if(arr_filter[index].fav =='Yes'){
              if(arr_filter[index].fav_id == id){
                  remove_arr_filter.push(index);
                  id_same = true;
                }
            }
        });

        // cek order fav
        $.each(arr_order,  function(index,isi){
            if(arr_order[index].fav =='Yes'){
                remove_arr_order.push(index);
                if(arr_order[index].fav_id == id){
                  id_same = true;
                }
            }
        });

        if(id == ''){
          alert_modal_warning('Favorite Kosong ! ');
        }else{
          please_wait(function(){});
          $.ajax({
                  type: "POST",
                  dataType : "JSON",
                  url : '<?=base_url()?>report/stock/delete_favorite',
                  data: {filter_id:id,},
                  success: function(data){
                    if(data.sesi == 'habis'){
                      alert_modal_warning(data.message);
                      var baseUrl = '<?php echo base_url(); ?>';
                      window.location = baseUrl;//replace ke halaman login  
                    }else {
                      unblockUI( function() {
                        setTimeout(function() { 
                            alert_notify(data.icon,data.message,data.type,function(){});
                            if(active == true || id_same == true){
                                // remove filter
                                remove_arr_group.reverse().forEach(function(index) {
                                  arr_group.splice(index, 1);
                                });

                                remove_arr_filter.reverse().forEach(function(index) {
                                  arr_filter.splice(index, 1);
                                });

                                remove_arr_order.reverse().forEach(function(index) {
                                  arr_order.splice(index, 1);
                                });
                                loadSearchData();
                            }
                         }, 1000);
                      });
                    }
                   $("#favorites a[id='"+id+"'] ").remove();
                   $("#favorites span[id='span-"+id+"'] ").remove();

                  
                   
                  },error : function(jqXHR, textStatus, errorThrown){
                    alert('error delete Favorite');
                    unblockUI( function() {});
                  }
          }); 
        }
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
    }else if(field == 'lot'){
      field = 'Lot';
    }else if(field == 'sales_order'){
      field = 'Sales Contract';
    }else if(field == 'sales_group'){
      field = 'Marketing';
    }else if(field == 'opname'){
      field  = 'Status Opname';
    }else if(field == 'category'){
      field  = 'Kategori';
    }
    return field;
  }

  // change combobx filter
  $('#cmbSearch').on('change', function(e){
    value = $(this).val();
    if(value == 'lokasi'){
      var value = '<div class="col-md-6"> ';
          value += "<select class='form-control input-sm value width-input' name='search' id='search'  >";
          value += "<option value='kosong'>-- Pilih Lokasi  --</option>";
          value += "<?php foreach($warehouse as $row){ echo "<option>".$row['lokasi']."</option>";} ?>";
          value += "</select>";
          value += '</div>';
          
          $('#f_search').html(value);
    }else if(value == 'nama_grade'){
      var value = '<div class="col-md-6"> ';
          value += "<select class='form-control input-sm value width-input' name='search' id='search'  >";
          value += "<option value='kosong'>Pilih Grade</option>";
          value += "<?php foreach($list_grade as $row){ echo "<option>".$row->nama_grade."</option>";} ?>";
          value += "</select>";
          value += '</div>';
          $('#f_search').html(value);
    }else if(value == 'category'){
      var value = '<div class="col-md-6"> ';
          value += "<select class='form-control input-sm value width-input' name='search' id='search'  >";
          value += "<option value='kosong'>Pilih Kategori</option>";
          value += "<?php foreach($category as $row){ echo "<option >".$row->nama_category."</option>";} ?>";
          value += "</select>";
          value += '</div>';
          $('#f_search').html(value);
    }else if(value == 'sales_group'){
      var value = '<div class="col-md-6"> ';
          value += "<select class='form-control input-sm value width-input' name='search' id='search'  >";
          value += "<option value='kosong'>-- Pilih Marketing --</option>";
          value += "<?php foreach($mst_sales_group as $row){ echo "<option value='".$row->kode_sales_group."'>".$row->nama_sales_group."</option>";} ?>";
          value += "</select>";
          value += '</div>';
          $('#f_search').html(value);
    }else if(value == 'opname'){
      var value = '<div class="col-md-6"> ';
          value += "<select class='form-control input-sm value width-input' name='search' id='search'  >";
          value += "<option value='kosong'>-- Status Opname --</option>";
          value += "<option value='done'>Sudah Opname</option>";
          value += "<option value='draft'>Belum Opname</option>";
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
          value += '<input type="number" class="form-control input-sm" id="search" name="search" placeholder="Day" onkeypress="return isNumberKey(event)" onkeydown="event_input(event)" >';
          value += '</div>';
          $('#f_search').html(value);
    }else{
      var value = '<div class="col-md-3"> ';
          value +=  '<select class="form-control input-sm" name="cmbOperator" id="cmbOperator">';
          value +=  '<option value="LIKE">LIKE</option>'
          value +=  '<option value="NOT LIKE">NOT LIKE</option>'
          value +=  '<option value="=">=</option>'
          value +=  '<option value="!=">!=</option>'
          value += "</select>";
          value += '</div>';
          value += '<div class="col-md-3">';
          value += '<input type="text" class="form-control input-sm" id="search" name="search" onkeydown="event_input(event)" >';
          value += '</div>';
          $('#f_search').html(value);
    }

  }); 

  // klik btn search
  $('#btn-simpan-filter').on('click', function(e){
        var nama_filter   = $('#nama_filter').val();
        var check_default  = false;
        var checkboxes_default =  new Array(); 
        checkboxes_default = $('input[name="check_default"]').map(function(e, i) {
                if(this.checked == true){
                  check_default = true;
                  return i.value;
                }
        }).get();

        var favorite = false;
         // cek array group dan filter fav=no
        $.each(arr_group,  function(index,isi){
            if(arr_group[index].fav =='Yes'){
              favorite = true;
            }
        });
        // cek filter fav
        $.each(arr_filter,  function(index,isi){
            if(arr_filter[index].fav =='Yes'){
              favorite = true;
            }
        });

        if(nama_filter == ''){
          alert_modal_warning('Nama Flter Harus diisi !');
        }else if(favorite == true){
          alert_modal_warning('Filter tidak bisa disimpan, karena terdapat Favorite yang aktif !');
        }else{
          please_wait(function(){});
          $.ajax({
                  type: "POST",
                  dataType : "JSON",
                  url : '<?=base_url()?>report/stock/save_filter',
                  data: {nama_filter:nama_filter, default:check_default, arr_filter:JSON.stringify(arr_filter), arr_group:JSON.stringify(arr_group),arr_order:JSON.stringify(arr_order) },
                  success: function(data){

                    if(data.sesi == 'habis'){
                      alert_modal_warning(data.message);
                      var baseUrl = '<?php echo base_url(); ?>';
                      window.location = baseUrl;//replace ke halaman login  
                    }else {
                      unblockUI( function() {
                        setTimeout(function() { alert_notify(data.icon,data.message,data.type,function(){}); }, 1000);
                      });
                    }
                
                    $('#btn-simpan-filter').button('reset');
                    $("#favorites").load(location.href + " #favorites>*");
                    $('#nama_filter').val('');
                  },error : function(jqXHR, textStatus, errorThrown){
                    alert('error simpan filter');
                    $('#btn-simpan-filter').button('reset');
                    unblockUI( function() {});
                  }
          });
        }

  });

  // klik btn search
  $('#btn-search').on('click', function(e){
    btn_search();
  });

  function btn_search(){
    //alert('masuk');
    var search      = $('#search').val();
    var cmbSearch   = $('#cmbSearch').val();
    var cmbOperator = $('#cmbOperator').val();

    var id      = 'id-'+search;
    var caption_field = translate_cmb(cmbSearch);// translate cmbsearch

    if(search == 'kosong'){
      alert_modal_warning('Filter tidak boleh Kosong !');
    }else{

      // show loading
      $("#example1_processing").css('display',''); 

      if(search != ''){
        
        if(cmbSearch == 'umur'){
          if(cmbOperator == '<'){
            caption_sparate ='Newer Than';
          }else if(cmbOperator == '>'){
            caption_sparate ='Older Than';
          }
        }else if(cmbSearch == 'lokasi' || cmbSearch == 'nama_grade' || cmbSearch == 'sales_group' || cmbSearch == 'opname' || cmbSearch == 'category'){
          caption_sparate = '=';
          cmbOperator     = '=';
        }else{
          caption_sparate = cmbOperator;// ex LIKE, NOT LIKE, =, !=
        }

        // add to array 
        arr_filter.push({type:'search', nama_field:cmbSearch, operator:cmbOperator,  value:search, id:id, fav:'No'});
        
        if(cmbSearch == 'sales_group'){
          $.each(obj_sales, function(index,isi){
            if(obj_sales[index].kode == search){
              name_sales_group = obj_sales[index].name;
            }
          });
          caption_value = name_sales_group;
      
        }else if(cmbSearch == 'opname'){
          if(search == 'done'){
            caption_value = 'Sudah Opname';
          }else{
            caption_value = 'Belum Opname';
          }
        }else{
          caption_value = search;
        }

        // add tags to result                
        var span = ' <span class="breadcumb "> <span class="breadcumb-field"> '+caption_field+' '+caption_sparate+' </span><span class="breadcumb-value"><span class="ul-pl-02">'+caption_value+'</span> <i class="fa fa-times" data-type="search" data-search="'+cmbSearch+'" id="'+htmlentities_script(id)+'" data-togle="tooltip" title="Delete Filter"></i></span></span>';
        
        $('#result').append(span);
        $('.breadcumb .breadcumb-value').find('i').addClass("breadcumb-close");
      }

      $('#btn-search').button('loading');

      // loaddata
      loadSearchData();

    }
         
  }


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

    // var check_transit  = false;
    // var checkboxes_arr =  new Array(); 

    // var checkboxes_arr = $('input[name="transit[]"]').map(function(e, i) {
    //         if(this.checked == true){
    //           check_transit = true;
    //           return i.value;
    //         }
    // }).get();

     $("#example1 tbody").remove();
     please_wait(function(){});

     $.ajax({
                type: "POST",
                dataType : "JSON",
                url : '<?=base_url()?>report/stock/loadData/'+pageNum,
                data: {arr_filter:JSON.stringify(arr_filter), arr_group:JSON.stringify(arr_group), arr_order:arr_order},
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
                        $row += "<td colspan='4'>"+value.grouping+"</td>";
                        $row += "<td align='right' colspan='2'>"+value.qty+"</td>";
                        $row += "<td align='right'  colspan='2'>"+value.qty2+"</td>";
                        $row += "<td colspan='3' class='list_pagination'></td>";
                        $row += "</tr>";
                        $row += "</tbody>";
                        $ro++;
                    });

                    $("#example1").append($row);

                  }else{
                      tbody = loadRecord(data.record);
                      $("#example1").append(tbody);
                  }

                  if(data.record.length == 0){
                      let tbody = $("<tbody />");
                      let tr    = $("<tr>").append($("<td colspan='16' align='center'>").text('Tidak ada Data'));
                      tbody.append(tr)
                      $('#example1').append(tbody);
                  }

                  $('#btn-search').button('reset');
                  if($('#search').is('select')){
                      $('#search').prop('selectedIndex',0);
                  }else{
                      $('#search').val('');// kosongkan search
                  }
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

    

    // cek sql saat klik btn excel
    $("#form_export").submit(function(event){

        var sql = $('#sql').val();
        if(event.keyCode == 13){
          event.preventDefault();
          return false;
        }else if(sql == ''){
          alert('Silahkan Filter Terlebih Dahulu !');
          return false;
        }else{

        }
    });

    // remove array group
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
      var favorite    = true;

      $.each(arr_group,  function(index,isi){
          if(arr_group[index].nama_field == groupBy && arr_group[index].index_group == dataIndex){
            //alert('ada');
            empty = false;
            if(arr_group[index].fav == 'No'){
              favorite  = false;
            }
          }
      });

      if(empty == false){
        if(favorite == false){
          removeGroup('group',groupBy,id);
          $('#'+id+'').parents(".breadcumb").remove();
        }
      }else{

        if(arr_group.length == 2){
          alert_modal_warning('Maaf, Pilihan Group By Tidak Bisa Lebih dari 2 ')
        }else{

          // add to array 
          arr_group.push({type:'group', nama_field:groupBy, id:id,  index_group:dataIndex, fav:'No'});

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
    var this_icon = '';

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
        this_icon = $(this);
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

        // var check_transit  = false;
        // var checkboxes_arr =  new Array(); 

        // var checkboxes_arr = $('input[name="transit[]"]').map(function(e, i) {
        //         if(this.checked == true){
        //           check_transit = true;
        //           return i.value;
        //         }
        // }).get();

       this_icon.html('<i class="fa fa-spinner fa-spin "></i>');
       this_icon.css('pointer-events','none');
     
       $.ajax({
            type      : 'POST',
            dataType  : 'json',
            url       : '<?=base_url()?>report/stock/loadChild',
            data      : {kode:kode, group_by:group_by, tbody_id:tbody_id, group_ke:group_ke, record:'0', arr_filter:JSON.stringify(arr_filter), arr_group:JSON.stringify(arr_group), root:root, tmp_arr_group:JSON.stringify(tmp_arr_group), arr_order:arr_order},
            success:function(data){

              if(data.list_group != ''){
                $('#example1 tbody[id='+data.tbody_id+']').after(data.list_group);
                tmp_arr_group.push(data.tmp_arr_group);

                empty = false;

                let $ro    = 1;
                let $row   = '';
                      //let $group_ke_next = 0;
                let $group   = data.tbody_id;
                let $group_ke_next = parseInt(data.group_ke) + 1;

                $.each(data.list_group, function(key, value){
                      $id      = $group+'-'+$ro;
                      $row  += "<tbody data-root='"+$group+"' data-parent='"+$group+"' id='"+$id+"'>";
                      $row  += "<tr  class='oe_group_header'>";
                      $row  += "<td></td>";
                      $row  += "<td class='show collapsed group1' href='#' style='cursor:pointer;'  data-content='edit' data-isi='"+value.nama_field+"' data-group='"+data.group_by+"' data-tbody='"+$id+"'data-root='"+$group+"' node-root='No' group-ke='"+$group_ke_next+"'><i class='glyphicon glyphicon-plus' ></i></td>";
                      $row  += "<td colspan='4' style='min-width:120px'>"+value.grouping+"</td>";
                      $row  += "<td align='right' colspan='2'>"+value.qty+"</td>";
                      $row  += "<td align='right' colspan='2' style='min-width:100px'>"+value.qty2+"</td>";
                      $row  += "<td class='list_pagination' colspan='3' style='min-width:80px'></td>";
                      $row  += "</tr>";
                      $row  += "</tbody>";
                      $ro++;
                });
                $('#example1 tbody[id='+data.tbody_id+']').after($row);

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
                                $("<td>").text(value.kategori),
                                $("<td align='right'>").text(value.qty),
                                $("<td align='right'>").text(value.qty2),
                                $("<td align='right'>").text(value.lebar_greige),
                                $("<td align='right'>").text(value.lebar_jadi),
                                $("<td>").text(value.sales_order),
                                $("<td>").text(value.sales_group),
                                $("<td>").text(value.qty_opname),
                                $("<td>").text(value.umur_produk),
                      );
                      tbody.append(tr);
                      no++;
                });
               
                $('#example1 tbody[id='+data.tbody_id+']').after(tbody);
              }
              
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

              // kembalikan icon ke awal
              this_icon.css('pointer-events','');
              this_icon.html('<i class="glyphicon glyphicon-minus "></i>');
             
            },error: function (jqXHR, textStatus, errorThrown){
              alert('Error Load Child Root');
              // kembalikan icon ke awal
              this_icon.css('pointer-events','');
              this_icon.html('<i class="glyphicon glyphicon-minus "></i>');
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
    var this_icon =  $(this);
    loadPageChild(this_icon,kode,group_by,group_ke,tbody_id,page,action,root);
    this_icon.html('<i class="fa fa-spinner fa-spin"></i>');
    this_icon.css('pointer-events','none');
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
    var this_icon =  $(this);
    loadPageChild(this_icon,kode,group_by,group_ke,tbody_id,page,action,root);
    this_icon.html('<i class="fa fa-spinner fa-spin"></i>');
    this_icon.css('pointer-events','none');
  });


  // untuk meload child dari next/prev button
  function loadPageChild(this_icon,kode,group_by,group_ke,tbody_id,page,action,root){

            // var check_transit  = false;
            // var checkboxes_arr =  new Array(); 

            // var checkboxes_arr = $('input[name="transit[]"]').map(function(e, i) {
            //         if(this.checked == true){
            //           check_transit = true;
            //           return i.value;
            //         }

            // }).get();

           $.ajax({
            type : 'POST',
            dataType: 'json',
            url  : '<?=base_url()?>report/stock/loadChild',
            data : {  kode:kode,  record:page, arr_filter:JSON.stringify(arr_filter), arr_group:JSON.stringify(arr_group), group_by:group_by, tbody_id:tbody_id, group_ke:group_ke, root:root, tmp_arr_group:JSON.stringify(tmp_arr_group), arr_order:arr_order},
            success:function(data){
            
                $("#example1 tbody[data-parent='"+tbody_id+"']").remove();// remove child by data-parent

                if(data.list_group != ''){
                    empty = false;

                    let $ro    = 1;
                    let $row   = '';
                          //let $group_ke_next = 0;
                    let $group   = data.tbody_id;
                    let $group_ke_next = parseInt(data.group_ke) + 1;

                    $.each(data.list_group, function(key, value){
                          $id      = $group+'-'+$ro;
                          $row  += "<tbody data-root='"+$group+"' data-parent='"+$group+"' id='"+$id+"'>";
                          $row  += "<tr  class='oe_group_header'>";
                          $row  += "<td></td>";
                          $row  += "<td class='show collapsed group1' href='#' style='cursor:pointer;'  data-content='edit' data-isi='"+value.nama_field+"' data-group='"+data.group_by+"' data-tbody='"+$id+"'data-root='"+$group+"' node-root='No' group-ke='"+$group_ke_next+"'><i class='glyphicon glyphicon-plus' ></i></td>";
                          $row  += "<td colspan='4' style='min-width:120px'>"+value.grouping+"</td>";
                          $row  += "<td align='right' colspan='2'>"+value.qty+"</td>";
                          $row  += "<td align='right' colspan='2' style='min-width:100px'>"+value.qty2+"</td>";
                          $row  += "<td class='list_pagination' colspan='3' style='min-width:80px'></td>";
                          $row  += "</tr>";
                          $row  += "</tbody>";
                          $ro++;
                    });
                    $('#example1 tbody[id='+data.tbody_id+']').after($row);
                }else{

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
                                    $("<td>").text(value.kategori),
                                    $("<td align='right'>").text(value.qty),
                                    $("<td align='right'>").text(value.qty2),
                                    $("<td align='right'>").text(value.lebar_greige),
                                    $("<td align='right'>").text(value.lebar_jadi),
                                    $("<td>").text(value.sales_order),
                                    $("<td>").text(value.sales_group),
                                    $("<td>").text(value.qty_opname),
                                    $("<td>").text(value.umur_produk),
                         );
                        tbody.append(tr);
                        no++;
                  });
                 
                  $('#example1 tbody[id='+data.tbody_id+']').after(tbody);
                }


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
                if(action == 'next'){
                  icon = '>';
                }else{
                  icon = '<';
                }
                this_icon.html(icon);
                this_icon.css('pointer-events','');   

            },error: function (jqXHR, textStatus, errorThrown){
              //alert(jqXHR.responseText);
              alert('Error Load Page Child');
              //alert(action);
              if(action == 'next'){
                  icon = '>';
                }else{
                  icon = '<';
              }
              this_icon.html(icon);
              this_icon.css('pointer-events','');   
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

      arr_order.push({column:nama_kolom, sort:order, fav:'No' });

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

      // var check_transit  = false;
      // var checkboxes_arr =  new Array(); 
      // var checkboxes_arr = $('input[name="transit[]"]').map(function(e, i) {
      //           if(this.checked == true){
      //             check_transit = true;
      //             return i.value;
      //           }
      // }).get();
      
      $.ajax({
          type : 'POST',
          dataType: 'json',
          url : '<?=base_url()?>report/stock/loadData/0',
          data:{arr_order:arr_order, arr_filter:JSON.stringify(arr_filter), arr_group:JSON.stringify(arr_group)},
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
            $('#pagination').html(data.pagination);
            
            if(data.group == true){
                  empty = false;

                  let $ro    = 1;
                  let $row   = '';

                  $.each(data.record, function(key, value){
                        let $group = 'group-of-rows-'+$ro;

                        $row  += "<tbody id="+$group+">";
                        $row  += "<tr  class='oe_group_header'>";
                        $row  += "<td class='show collapsed group1' href='#' style='cursor:pointer;'  data-content='edit' data-isi='"+value.nama_field+"' data-group='"+value.by+"' data-tbody='"+$group+"'data-root='"+$group+"' node-root='Yes' group-ke='1'><i class='glyphicon glyphicon-plus' ></i></td>";
                        $row += "<td colspan='4'>"+value.grouping+"</td>";
                        $row += "<td align='right' colspan='2'>"+value.qty+"</td>";
                        $row += "<td align='right' >"+value.qty2+"</td>";
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
                      $("<td>").text(value.kategori),
                      $("<td align='right'>").text(value.qty),
                      $("<td align='right'>").text(value.qty2),
                      $("<td align='right'>").text(value.lebar_greige),
                      $("<td align='right'>").text(value.lebar_jadi),
                      $("<td>").text(value.sales_order),
                      $("<td>").text(value.sales_group),
                      $("<td>").text(value.qty_opname),
                      $("<td>").text(value.umur_produk),
            );    
            tbody.append(tr);
      });

      return tbody;

  }

</script>

</body>
</html>
