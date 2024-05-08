
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

    /* > CSS advanced search */
    .li-adv{
      display: inline-block;
      cursor: pointer;
      position: relative;
      margin-right: 8px;
    }

    .li-adv:hover{
       background-color: #f2dede;
        
    }
  
    /* < CSS advanced search */


    /* > CSS sort table*/

    thead a:link, a:visited .column_sort {
      color: black;
      text-decoration: none;
    }

    thead a:hover, a:active, .column_sort{
      color: black;
      text-decoration: underline;
    }

    /* < CSS sort table*/
    .example1 table  {
      display: block;
      height: calc( 100vh - 200px );
      overflow-x: auto;
    }

   .nowrap{
      white-space: nowrap;
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
    /* set tampilan textfield table filter */
    @media only screen and (max-width: 600px) {
      .width-input{
        width: 100px;
      }
      .width-date{
        width: 150px;
      }
    }

    .min-width-200{
      min-width:200px;
    }

    .min-width-130{
      min-width: 130px;
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
  <?php
      $this->load->view("admin/_partials/sidebar.php"); 
   ?>
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

                          <?php 
                            if(!empty($user_filter)){
                          ?>
                          <!--div class="form-group" id="divFavorite">
                            <div class="col-12 col-sm-4 col-md-4">
                                <label><i class="fa fa-star"></i> Favorite </label>
                            </div> 
                            <div class="col-12 col-sm-8 col-md-8">
                            <?php 
                              foreach ($user_filter as $val) {
                                $data_filter = str_replace('"', '\"', $val['data_filter']);
                                $data_grouping = $val['data_grouping'];

                                $nama_filter = $val['nama_filter'];
                                $id_replace  = str_replace(" ", "-", $val['nama_filter']);
                                $id          = 'fav'.$id_replace;
                                ?>
                                <a onclick='favorite("<?php echo $nama_filter;?>","<?php echo $data_filter;?>","<?php echo $data_grouping; ?>","<?php echo $id;?>")' class="badge" id="<?php echo $id;?>">
                                  <?php echo $val['nama_filter'];?>
                                </a>
                                <span aria-hidden="true" onclick="deleteFavorite('<?php echo $nama_filter;?>')"><i class="fa fa-remove" style="cursor: pointer;" data-toggle='tooltip' title="delete favorite"></i></span>
                                                             
                             <?php 
                              }
                              ?>
                            </div>
                          </div-->
                          <?php
                            }//end if empty user_filter
                          ?>
                          <div class="form-group">
                            <div class="col-12 col-sm-3 col-md-3">
                              <span class="fa fa-database"></span> <label>Group By</label>
                            </div>
                            <div class="col-12 col-sm-8 col-md-8" id="groupBy">
                              <li onclick="groupBy('kode_produk','Group By Kode Produk',0)" class="li-adv" data-index="0">Kode Produk</li>
                              <li onclick="groupBy('nama_produk','Group By Nama Produk',1)" class="li-adv" data-index="1" >Nama Produk</li>
                              <li onclick="groupBy('lot','Group By Lot',2)" class="li-adv" data-index="2">Lot</li>
                              <li onclick="groupBy('lokasi','Group By Lokasi',3)" class="li-adv" data-index="3">Lokasi</li>
                              <li onclick="groupBy('lokasi_fisik','Group By Lokasi Fisik',4)" class="li-adv" data-index="4">Lokasi Fisik</li>
                            </div>
                          </div>

                          <!--div class="form-group">
                            <div class="col-12 col-sm-4 col-md-4">
                              <label><i class="fa fa-database"></i> Group by </label>
                            </div>
                            <div class="col-12 col-sm-8 col-md-8">
                              <a onclick="groupBy('nama_produk','Group By Nama Produk')" class="badge" id="nama_produk">Nama Produk</a>
                              <a onclick="groupBy('lokasi','Group By Lokasi')" class="badge" id="lokasi">Lokasi</a>
                            </div>
                          </div-->

                        </div><!-- /.col-md group caption-->

                        <div class="col-12 col-sm-12 col-md-6">

                          <!--div data-toggle="collapse" href="#saveFilter" aria-expanded="false" aria-controls="saveFilter" class="collapsed" style="cursor: pointer;"><i class="saveFilter glyphicon glyphicon-triangle-bottom"></i> Save Filter 
                          </div>
                          <div id="saveFilter" class="collapse">
                            <div class="panel-body ">
                              <div class="col-md-12">
                                <input type="text" name="nama_filter" id="nama_filter" class="form-control input-sm" placeholder="Nama Filter">
                              </div>
                              <div class="col-md-12">
                                <input type="checkbox" name="check_default" id="check_default">
                                <label>use by default</label>
                              </div>
                              <div class="col-md-12">
                                <button type="button" id="btn-simpan-filter" class="btn btn-default btn-sm">Save</button>
                              </div>
                            </div>
                          </div-->

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
                                    <select class="form-control input-sm element width-input" name="cmbElement" id="cmbElement" onchange="get_condition(this);" >
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
            <div class="col-xs-12 table-responsive example1 divListview">
              <div role="region" aria-labelledby="HeadersCol" tabindex="0" class="rowheaders">
                <table id="example1" class="table table-condesed table-hover" border="0">
                  <thead>
                    <tr>
                      <th  class="style bb no">No. </th>
                      <th  class='style bb nowrap'  width='80px'><!--a class="column_sort" id="kode_produk" data-order="desc" href="javascript:void(0)"-->Kode Produk</a></th>
                      <th  class='style bb nowrap'  width='300px'><!--a class="column_sort" id="nama_produk" data-order="desc" href="javascript:void(0)"-->Nama Produk</a></th>
                      <th  class='style bb nowrap'  width='100px'><!--a class="column_sort" id="create_date" data-order="desc" href="javascript:void(0)"-->Tanggal  Dibuat</a></th>
                      <th  class='style bb nowrap'  width='100px'><!--a class="column_sort" id="move_date" data-order="desc" href="javascript:void(0)"-->Tanggal  Diterima</a></th>
                      <th  class='style bb nowrap'  width='200px'><!--a class="column_sort" id="lot" data-order="desc" href="javascript:void(0)"-->Lot</a></th>
                      <th  class='style bb nowrap'  width='200px'><!--a class="column_sort" id="corak_remark" data-order="desc" href="javascript:void(0)"-->Corak Remark</a></th>
                      <th  class='style bb nowrap'  width='200px'><!--a class="column_sort" id="warna_remark" data-order="desc" href="javascript:void(0)"-->Warna Remark</a></th>
                      <th  class='style bb nowrap'  width='50px'><!--a class="column_sort" id="nama_grade" data-order="desc" href="javascript:void(0)"-->Grade</a></th>
                      <th  class='style bb nowrap'  width='100px'><!--a class="column_sort" id="qty" data-order="desc" href="javascript:void(0)"-->Qty</a></th>
                      <th  class='style bb nowrap'  width='50px'><!--a class="column_sort" id="uom" data-order="desc" href="javascript:void(0)"-->Uom</a></th>
                      <th  class='style bb nowrap'  width='100px'><!--a class="column_sort" id="qty2" data-order="desc" href="javascript:void(0)"-->Qty2</a></th>
                      <th  class='style bb nowrap'  width='50px'><!--a class="column_sort" id="uom2" data-order="desc" href="javascript:void(0)"-->Uom2</a></th>
                      <th  class='style bb nowrap'  width='100px'><!--a class="column_sort" id="qty_jual" data-order="desc" href="javascript:void(0)"-->Qty Jual</a></th>
                      <th  class='style bb nowrap'  width='50px'><!--a class="column_sort" id="uom_jual" data-order="desc" href="javascript:void(0)"-->Uom Jual</a></th>
                      <th  class='style bb nowrap'  width='100px'><!--a class="column_sort" id="qty2_jual" data-order="desc" href="javascript:void(0)"-->Qty2 Jual</a></th>
                      <th  class='style bb nowrap'  width='50px'><!--a class="column_sort" id="uom2_jual" data-order="desc" href="javascript:void(0)"-->Uom2 Jual</a></th>
                      <th  class='style bb nowrap'  width='100px'><!--a class="column_sort" id="qty_opname" data-order="desc" href="javascript:void(0)"-->Qty Opname</a></th>
                      <th  class='style bb nowrap'  width='100px'><!--a class="column_sort" id="lebar_greige data-order="desc" href="javascript:void(0)"-->Lbr.Greige</a></th>
                      <th  class='style bb nowrap'  width='100px'><!--a class="column_sort" id="lebar_jadi" data-order="desc" href="javascript:void(0)"-->Lbr.Jadi</a></th>
                      <th  class='style bb nowrap'  width='200px'><!--a class="column_sort" id="lokasi" data-order="desc" href="javascript:void(0)"-->Lokasi</a></th>
                      <th  class='style bb nowrap'  width='100px'><!--a class="column_sort" id="lokasi" data-order="desc" href="javascript:void(0)"-->Lokasi Fisik</a></th>
                      <th  class='style bb nowrap'  width='200px'><!--a class="column_sort" id="reff_note" data-order="desc" href="javascript:void(0)"-->Reff Note</a></th>
                      <th  class='style bb nowrap'  width='80px'><!--a class="column_sort" id="reserve_move" data-order="desc" href="javascript:void(0)"-->Reserve Move</a></th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td colspan="18" align="left">Tidak ada Data</td>
                    </tr>
                  </tbody>
                </table>
                <div id="example1_processing" class="table_processing" style="display: none; z-index:5;">
                  Processing...
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
<!-- /.Site wrapper -->

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

    //* Show collapse save Filter
    $('#saveFilter').on('shown.bs.collapse', function () {
       $(".saveFilter").removeClass("glyphicon-triangle-bottom").addClass("glyphicon-triangle-top");
    });

    //* Hide collapse save Filter
    $('#saveFilter').on('hidden.bs.collapse', function () {
       $(".saveFilter").removeClass("glyphicon-triangle-top").addClass("glyphicon-triangle-bottom");
    });

    //show / hide collapse child in tabel
    $(document).on("hide.bs.collapse show.bs.collapse", ".child", function (event) {
        $(this).prev().find(".glyphicon").toggleClass("glyphicon-plus glyphicon-minus");
        event.stopPropagation();
    });


    var arr_filter   = [];// arr_filter menampung array filter textfile, filtertable
    var arr_grouping = [];// arr_grouping menampung array group
  
    $(document).ready(function(){
      $('[data-role="tags-input"]').tagsinput({ });
    });

    // body onload
    function get_default(){
      $("#tags").tagsinput('removeAll');
      $("#cmbElement").prop('selectedIndex',0);

    }

    // function grouping table from onlick caption
    function groupBy(groupBy,caption,dataIndex){
      var check_arr_group = false;
      var indexKe = '';

        $.each(arr_grouping, function(index,isi){
          if(arr_grouping[index].caption == caption){
            check_arr_group = true;
            indexKe = index;
          }
        });

        if(check_arr_group == false){
          if(arr_grouping.length  == 2 ){
            alert_modal_warning('Maaf, Pilihan Group By Tidak Bisa Lebih dari 2 ')
          }else{
            arr_grouping.push({favorite:'No', nama_field:groupBy, caption:caption, data_index:dataIndex});//add array grouping
            $('#groupBy [data-index="'+dataIndex+'"]').addClass('badge bg-red');//add class badge bg-red to 
            $('[data-role="tags-input"]').tagsinput('add', caption);// add caption to textbox
          }

        }else{
          arr_grouping.splice(indexKe,1);//remove caption in array grouping
          $('[data-role="tags-input"]').tagsinput('remove', caption);//remove caption in textbox
          $('#groupBy [data-index="'+dataIndex+'"]').removeClass('badge bg-red');//remove class badge bg red
        }
         
    }

   

    // event jika caption ditambahkan di texbox
    $('#tags').on('itemAdded', function(event){
     // alert('added belum '+JSON.stringify(arr_filter));
  
     //please_wait(function(){});
      var check_arr_filter = false;
      var check_arr_group  = false;
      var check_favorite_arr_filter   = false;
      var check_favorite_arr_grouping = false;
      
      // cek jika ada favorite di arr_filter
      $.each(arr_filter, function(index,isi){
        if(arr_filter[index].favorite == 'Yes' && arr_filter[index].caption == event.item){

          check_favorite_arr_filter = true;
        }
      });

      // cek jika ada favorite di array group
      $.each(arr_grouping, function(index,isi){
       if(arr_grouping[index].favorite == 'Yes'){
        check_favorite_arr_grouping = true;
       }
      });
      

      // jika tidak ada favorite
      if(check_favorite_arr_filter == false){

        // chek item yang ditambahkan berdasarkan caption
        $.each(arr_filter, function(index,isi){
          if(arr_filter[index].caption == event.item){
            check_arr_filter = true;
          }
        });

        if(check_arr_filter == false){

          // cek jika caption ada di arr_grouping
          $.each(arr_grouping, function(index,isi){
            if(arr_grouping[index].caption == event.item){
              check_arr_group = true;
            }
          });

          if(check_arr_group == false ){
            //alert('masuk arr_filter push');
            //insert event.item ke arr_filter s
            arr_filter.push({favorite:'No', caption:event.item, nama_field:'nama_produk', operator:'LIKE', isi:event.item, condition:'AND', type:'textfield' });
          }

        }

      }

      //alert(' group  '+JSON.stringify(arr_grouping));
      // cek jika caption ada di arr_grouping
      $.each(arr_grouping, function(index,isi){
          //alert('tidak ada group ')
          if(arr_grouping[index].caption == event.item){
            check_arr_group = true;
            //alert('ada group ')

          }
      });

      // jika arr_filter terisi
      //if(arr_filter.length > 0 ){
      if(arr_filter.length > 0 && check_arr_filter == false  && check_arr_group == false && arr_grouping.length == 0){
        //alert('createPagination')
        createPagination(0);
      } 

      if(arr_grouping.length > 0 ){
        //alert('creationGroup');
        creationGroup();

      }

      //alert('added sudah '+JSON.stringify(arr_filter));
      //unblockUI( function() {});
      
    });


    //klik button apply 
    $(document).on("click","#btn-filter",function(e) {
  
        var filter = false;
        var empty_value = false;
        var arr    = [];
        var id_dept ='<?php  echo $id_dept;?>';
      
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

          isi = $(value).parents("tr").find("#value").val()
          if(isi == ''){
            empty_value = true;
          }

        }); 

        if(filter == true && empty_value == false){
          $("#example1 tbody").remove();
          $('#btn-filter').button('loading');
          $("#example1_processing").css('display','');// show loading processing in table
          please_wait(function(){});
          
          $.ajax({
              type: "POST",
              dataType: "JSON",
              url : '<?php echo site_url('warehouse/stockquants/loadData/0') ?>',
              data : {data_filter_table:arr, data_filter:arr_filter, data_grouping:arr_grouping, type_filter:'table', id_dept:id_dept},
              success : function(data){
                    $('#pagination').html(data.pagination);
                    $('#total_record').html(data.total_record);
                    var empty = true;

                    if(arr_grouping.length > 0 ){//jika arr_grouping ny terisi
                      $("#example1").append(data.record);
                    }else{
                      var tbody = $("<tbody />");
                      var no    = 1;
                      $.each(data.record, function(key, value) {
                        empty = false;
                        /*
                        var tr = $("<tr class='' id='"+key+"' />")
                        $.each(value, function(k, v) {
                          //alert('append')
                          tr.append(
                            $("<td />", {
                                html: v
                              })[0].outerHTML
                          );
                           tbody.append(tr);
                        })
                        */
                        var tr = $("<tr>").append(
                          $("<td>").text(no++),
                          $("<td>").text(value.kode_produk),
                          $("<td class='min-width-200'>").html('<a href="<?=base_url()?>warehouse/stockquants/edit/'+value.quant_id+'" target="_blank">'+value.nama_produk+'</a>'),
                          $("<td>").text(value.create_date),
                          $("<td>").text(value.move_date),
                          $("<td class='min-width-130'>").text(value.lot),
                          $("<td>").text(value.corak_remark),
                          $("<td>").text(value.warna_remark),
                          $("<td>").text(value.grade),
                          $("<td align='right'>").text(value.qty),
                          $("<td>").text(value.uom),
                          $("<td align='right'>").text(value.qty2),
                          $("<td>").text(value.uom2),
                          $("<td align='right'>").text(value.qty_jual),
                          $("<td>").text(value.uom_jual),
                          $("<td align='right'>").text(value.qty2_jual),
                          $("<td>").text(value.uom2_jual),
                          $("<td align='right'>").text(value.qty_opname),
                          $("<td align='right'>").text(value.lebar_greige),
                          $("<td align='right'>").text(value.lebar_jadi),
                          $("<td>").text(value.lokasi),
                          $("<td>").text(value.lokasi_fisik),
                          $("<td>").text(value.reff_note),
                          $("<td>").text(value.reserve_move),
                        );
                       tbody.append(tr);
                      });

                      $("#example1").append(tbody);
                    }
                    $("#example1_processing").css('display','none');// hidden loading processing in table

                    $('#btn-filter').button('reset');

                    if(empty == true && arr_grouping.length == 0){
                      var tr = $("<tr>").append($("<td colspan='18' align='left'>").text('Tidak ada Data'));
                      tbody.append(tr);
                      $("#example1").append(tbody);
                    }

                    $.each(data.dataArr, function(key, val) {
                      
                      arr_filter.push({favorite:'No', caption:val.caption, nama_field : val.nama_field, operator:val.operator, isi:val.isi, condition:val.condition, type:'table', jml_field :val.jml_field});
                      $('[data-role="tags-input"]').tagsinput("add", val.caption);
                    });

                    unblockUI( function() {});

              },error: function (jqXHR, textStatus, errorThrown){
                //alert(jqXHR.responseText);
                alert('Error Filter Tabel');
                $("#example1_processing").css('display','none');// hidden loading processing in table
                $('#btn-filter').button('reset');
                unblockUI( function() {});
              }
          });
        }else if(empty_value == true){
          alert_modal_warning('Value Filter tidak boleh kosong !');

        }else if(filter== false){
          alert_modal_warning('Maaf, Advanced Filter Kosong !');
        }

    });



    //event if item caption removed in textbox
    $('#tags').on('itemRemoved', function(event){
      //please_wait(function(){});
      var caption = event.item;//item removed
      removeArray(caption,'remove');
      //unblockUI( function() {});
    });


    function removeArray(caption,action){

      // alert('masuk removeArray ' +caption);
      if(action == 'remove'){
          var tmp_index_filter = [];// untuk menampung index yang akan di hapus
          var tmp_index_grouping = []; //untuk menampung index array grouping yang akan di hapus
          var tmp_fav_data_index = '';
          var favorite_group     = false;
          var favorite_arr       = false;
          var check_favorite_no  = false;
          var check_arr          = false;

          
          //looping arr_filter untuk di masukan ke array tmp_index_filter
          $.each(arr_filter, function(index,isi){
            if(arr_filter[index].caption == caption && arr_filter[index].favorite == 'No'){
              tmp_index_filter.push(index);
              //alert('push '+index);
            }

            if(arr_filter[index].caption == caption && arr_filter[index].favorite == 'Yes' ){
              tmp_index_filter.push(index);
              tmp_fav_data_index = arr_filter[index].data_index;
              favorite_arr = true;
            }

          });

          //alert('before '+JSON.stringify(arr_filter));
          //alert(tmp_index_filter.length +' '+ arr_filter.length);

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
             //alert('hapus sebagian arr')
             tmp_index_filter.reverse().forEach(function(index) {
                arr_filter.splice(index, 1);
                //alert('Hapus index '+index );
             });

          }
          //alert('after '+JSON.stringify(arr_filter));

          
          //looping arr_grouping untuk di masukan ke array tmp_index_grouping
          $.each(arr_grouping, function(index,isi){
            if(arr_grouping[index].caption == caption && arr_grouping[index].favorite == 'No'){
              tmp_index_grouping.push(index);
              //$('#'+id).removeClass('bg-red');//remove class bg red by id
              $('#groupBy [data-index="'+arr_grouping[index].data_index+'"]').removeClass('badge bg-red');//remove class badge bg red
            }

            if(arr_grouping[index].caption == caption && arr_grouping[index].favorite == 'Yes'){
              favorite_group = true;
              tmp_index_grouping.push(index);
              tmp_fav_data_index = arr_grouping[index].data_index;
            }

          });



          // remove arr_grouping jika array lengthnya sama
          if(tmp_index_grouping.length == arr_grouping.length){
            arr_grouping = [];
          }else if(tmp_index_grouping.length == 1){
            for(row in tmp_index_grouping){
              indexKe = tmp_index_grouping[row];
              arr_grouping.splice(indexKe,1); // splice(index ke-, jml yg dhapus)
            } 
          }else{
            tmp_index_grouping.sort(function(a, b) { return b - a; }).forEach(function(index) {
              arr_grouping.splice(index, 1);
            });
          }

          //remove class badge bg-red
          if(favorite_group == true || favorite_arr == true){
            $('#fav [data-index="'+tmp_fav_data_index+'"]').removeClass('badge bg-red');//remove class badge bg red

          }

          // cek apa ada favorite di arr_grouping, arr_filter
          if(favorite_group == true || favorite_arr == true){

             $.each(arr_grouping, function(index,isi){
              check_arr = true;
              if( arr_grouping[index].favorite == 'No'){
                  check_favorite_no = true
              }
            });

            $.each(arr_filter, function(index,isi){
              check_arr = true;
              if( arr_filter[index].favorite == 'No'){
                  check_favorite_no = true
              }
            });
          }
      }

      // jika tidak ada favorite di arr_filter , arr_grouping
      if((favorite_group == false && favorite_arr == false) || (check_favorite_no == true )){
        //alert('load ulang')
        if(arr_grouping.length > 0){
          creationGroup();
        }else{
          createPagination(0);
        }

      }

    }

    var tmp_arr_group = []; // untuk menampung item grouping
    var tmp_arr_group1= [];

    // load creationGroup
    function creationGroup(){

      please_wait(function(){});
      $("#example1_processing").css('display','');// show loading processing in table

      tmp_arr_group =[];

      var id_dept ='<?php  echo $id_dept;?>';
      var html    = '';
      $.ajax({
        type : 'POST',
        dataType: 'json',
        url  : '<?=base_url()?>warehouse/stockquants/loadData/0',
        data : {data_filter : arr_filter, data_grouping : arr_grouping,  id_dept : id_dept},
        success: function(data){
          $('#pagination').html(data.pagination);
          $('#total_record').html(data.total_record);
          $("#example1 tbody").remove();
          $("#example1").append(data.record);
          tmp_arr_group1.push(data.tmp_arr_group);
          tmp_arr_group.push({'group_ke': data.group_ke, 'record':data.tmp_arr_group});
          tmp_arr_group1 = [];
          $("#example1_processing").css('display','none');// hidden loading processing in table
          unblockUI( function() {});

          if(data.record == ''){
            var tr = $("<tr>").append($("<td colspan='18' align='left'>").text('Tidak ada Data'));
            tbody.append(tr);
            $("#example1").append(tbody);
          }

          //alert('check arr '+JSON.stringify(tmp_arr_group));
        },error: function (jqXHR, textStatus, errorThrown){
          //alert(jqXHR.responseText);
          alert('Error Create Group');
          $("#example1_processing").css('display','none');// hidden loading processing in table
          unblockUI( function() {});

        }
      });

    }

   
    //jika next page ,isi akan rubah lagi
    //createPagination(0);
    $('#pagination').on('click','a',function(e){
      
      e.preventDefault(); 
      var pageNum = $(this).attr('data-ci-pagination-page');
      //alert(pageNum);
      createPagination(pageNum)

    });

    function createPagination(pageNum){
      $("#example1 tbody").remove();
      $("#example1_processing").css('display','');// show loading processing in table
      // alert('tes');
      var page = '';
      // alert('check arr '+JSON.stringify(arr_filter));
      var id_dept ='<?php  echo $id_dept;?>';
      please_wait(function(){});

      $.ajax({
        type : 'POST',
        dataType: 'json',
        url  : '<?=base_url()?>warehouse/stockquants/loadData/'+pageNum,
        data : {data_filter : arr_filter, id_dept : id_dept},
        success: function(data){
          $('#pagination').html(data.pagination);
          $('#total_record').html(data.total_record);
          ///alert('berhasil2')
          var tbody = $("<tbody id='0'/>");
          var no    = 1;
          var empty = true;
          $.each(data.record, function(key, value) {
            //var tr = $("<tr class='' id='"+key+"' />")
            empty = false;
            var tr = $("<tr>").append(
                      $("<td>").text(no++),
                      $("<td>").text(value.kode_produk),
                      $("<td class='min-width-200'>").html('<a href="<?=base_url()?>warehouse/stockquants/edit/'+value.quant_id+'" target="_blank">'+value.nama_produk+'</a>'),
                      $("<td>").text(value.create_date),
                      $("<td>").text(value.move_date),
                      $("<td class='min-width-130'>").text(value.lot),
                      $("<td>").text(value.corak_remark),
                      $("<td>").text(value.warna_remark),
                      $("<td>").text(value.grade),
                      $("<td  align='right'>").text(value.qty),
                      $("<td>").text(value.uom),
                      $("<td  align='right'>").text(value.qty2),
                      $("<td>").text(value.uom2),
                      $("<td align='right'>").text(value.qty_jual),
                      $("<td>").text(value.uom_jual),
                      $("<td align='right'>").text(value.qty2_jual),
                      $("<td>").text(value.uom2_jual),
                      $("<td align='right'>").text(value.qty_opname),
                      $("<td  align='right'>").text(value.lebar_greige),
                      $("<td  align='right'>").text(value.lebar_jadi),
                      $("<td>").text(value.lokasi),
                      $("<td>").text(value.lokasi_fisik),
                      $("<td>").text(value.reff_note),
                      $("<td>").text(value.reserve_move),
              );
              tbody.append(tr);
          });
          if(empty){
            var tr = $("<tr>").append($("<td colspan='18' align='left'>").text('Tidak ada Data'));
            tbody.append(tr);
          }
         $("#example1_processing").css('display','none');// hidden loading processing in table

         $("#example1").append(tbody);
         unblockUI( function() {});

          //alert('berhasil');
        },error: function (jqXHR, textStatus, errorThrown){
          alert('Error Load Items');
          $("#example1_processing").css('display','none');// hidden loading processing in table
          unblockUI( function() {});
          //alert(jqXHR.responseText);
        }
      });
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
        url : '<?php echo site_url('warehouse/stockquants/conditionFilter') ?>',
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
      var condition = "<select class='form-control input-sm condition width-input' name='cmbCondition' id='cmbCondition' >";
          condition +="<option>LIKE</option>";
          condition +="<option>NOT LIKE</option>";
          condition += "</select>";
      var value = "<input type='text' class='form-control input-sm value width-input' name='txtValue' id='value' >";

    }else if(type_condition == 'datetime' ){
      var condition = "<select class='form-control input-sm condition width-input' name='cmbCondition' id='cmbCondition'  >";
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
      var condition = "<select class='form-control input-sm condition width-input' name='cmbCondition' id='cmbCondition'  >";
          condition += "<option>=</option>";
          condition += "<option>>=</option>";
          condition += "<option><=</option>";
          condition += "<option>></option>";
          condition += "<option><</option>";
          condition += "<option>!=</option>";
          condition += "</select>";
      var value = "<input type='text' class='form-control input-sm value width-input' name='txtValue' id='value' >";

    }else if(type_condition == 'status'){
      var condition = "<select class='form-control input-sm condition width-input' name='cmbCondition' id='cmbCondition'  >";
          condition += "<option>=</option>";
          condition += "</select>";
      var value = "<select class='form-control input-sm value width-input' name='cmbValue' id='value'  >";
          value += "<option>draft</option>";
          value += "<option>ready</option>";
          value += "<option>done</option>";
          value += "<option>cancel</option>";
          value += "</select>";

    }else if(type_condition == 'opname'){
      var condition = "<select class='form-control input-sm condition width-input' name='cmbCondition' id='cmbCondition'  >";
          condition += "<option>=</option>";
          condition += "</select>";
      var value = "<select class='form-control input-sm value width-input' name='cmbValue' id='value'  >";
          value += "<option>draft</option>";
          value += "<option>done</option>";
          value += "</select>";
    }

    $('#filterAdvanced tr:nth-child('+rowIndex+') td:nth-child(2)').html(condition);//set cmbCondition
    $('#filterAdvanced tr:nth-child('+rowIndex+') td:nth-child(3)').html(value);//set value
  }

  // < Tabel Filter

  /*

  $(document).on('click', '.column_sort', function(){
      $("#example1 tbody").remove();
      var nama_kolom = $(this).attr("id");
      var order = $(this).attr("data-order");
      var id_dept ='<?php  echo $id_dept;?>';
      var arrow = '';

      if(order == 'desc'){
        //$(this).addClass('fa fa-arrow-down');
        $('.fa').remove('');
        arrow = ' <span class="fa fa-sort-amount-desc"></span>';

      } else {
        $('.fa').remove('');
        arrow = ' <span class="fa fa-sort-amount-asc"></span>';
      }
        
      $.ajax({
          type : 'POST',
          dataType: 'json',
          url  : '<?=base_url()?>warehouse/stockquants/loadData/0',
          data:{nama_kolom:nama_kolom, order:order,data_filter : arr_filter, data_grouping : arr_grouping,  id_dept : id_dept},
          success:function(data){
            //alert('berhasil');
            if(order == 'desc'){
              $('#'+nama_kolom+'').attr('data-order','asc');
            }else{
              $('#'+nama_kolom+'').attr('data-order','desc');
            }

            $('#'+nama_kolom+'').append(arrow);

            if(arr_grouping.length > 0 ){//jika arr_grouping ny terisi
              $("#example1").append(data.record);

            }else{
              var tbody = $("<tbody />");
              var no    = 1;
              $.each(data.record, function(key, value) {
                 
              var tr = $("<tr>").append(
                       $("<td>").text(no++),
                       $("<td>").text(value.kode_produk),
                       $("<td>").html('<a href="<?=base_url()?>warehouse/stockquants/edit/'+value.quant_id+'" target="_blank">'+value.nama_produk+'</a>'),
                       $("<td>").text(value.create_date),
                       $("<td>").text(value.lot),
                       $("<td>").text(value.grade),
                       $("<td>").text(value.qty),
                       $("<td>").text(value.uom),
                       $("<td>").text(value.qty2),
                       $("<td>").text(value.uom2),
                       $("<td>").text(value.qty_opname),
                       $("<td>").text(value.lebar_greige),
                       $("<td>").text(value.lebar_jadi),
                       $("<td>").text(value.lokasi),
                       $("<td>").text(value.lokasi_fisik),
                       $("<td>").text(value.reff_note),
                       $("<td>").text(value.reserve_move),
                  );
                 tbody.append(tr);
              });
              $("#example1").append(tbody);
              //alert($('.column_sort').attr('data-order'));
            }
          },error: function (jqXHR, textStatus, errorThrown){
            //alert(jqXHR.responseText);
            alert('Error Data');
          }
      })
         
  }); 

  */


  $(document).on("click", ".group1", function(e){

    var kode      = '';
    var tbody_id  = '';
    var tampil    = false;
    var html      = '';
    var info_page = '';
    var page_next = '';
    var page_prev = '';
    var this_icon = '';
    var id_dept   = '<?php  echo $id_dept;?>';

    // ambil data berdasarkan data-content='edit'
    $(this).parents("tr").find("td[data-content='edit']").each(function(){
        kode = $(this).attr('data-isi');
        group_by = $(this).attr('data-group');
        tbody_id = $(this).attr('data-tbody');
        root     = $(this).attr('data-root');
        group_ke = $(this).attr('group-ke');
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


    if(tampil == true){
      $("#example1 tbody[data-parent='"+tbody_id+"']").remove();// remove child by groupby
      if(node_root == 'Yes'){
        $("#example1 tbody[data-root='"+root+"']").remove();// remove child by root
      }
      $("#example1 tbody[id='"+tbody_id+"'] tr" ).find('td.list_pagination').text('');// remove btn pagination by tbody_id

    }else{

      this_icon.html('<i class="fa fa-spinner fa-spin "></i>');
      this_icon.css('pointer-events','none');

      $.ajax({
            type : 'POST',
            dataType: 'json',
            url  : '<?=base_url()?>warehouse/stockquants/loadChild',
            data : { kode:kode, group_by:group_by, tbody_id:tbody_id, record:'0', data_grouping:arr_grouping, data_filter:arr_filter,id_dept :id_dept, group_ke:group_ke, root:root, tmp_arr_group:tmp_arr_group},
            success:function(data){

              if(data.list_group != ''){
                $('#example1 tbody[id='+data.tbody_id+']').after(data.list_group);
               
              }else{

                let tbody = $("<tbody data-root='"+data.root+"' data-parent='"+tbody_id+"' />");
                let row = '';
                let no  = 1;
                $.each(data.record, function(key, value) {

                          row +=  "<tr  style='background-color: #f2f2f2;' >";
                          row += "<td>"+no+++"</td>";
                          row += "<td>"+value.kode_produk+"</td>";
                          row += "<td class='min-width-200'><a href='<?=base_url()?>warehouse/stockquants/edit/"+value.id_encr+"' target='_blank'>"+value.nama_produk+"</a></td>";
                          row += "<td>"+value.create_date+"</td>";
                          row += "<td>"+value.move_date+"</td>";
                          row += "<td class='min-width-130'>"+value.lot+"</td>";
                          row += "<td>"+value.nama_grade+"</td>";
                          row += "<td>"+value.corak_remark+"</td>";
                          row += "<td>"+value.warna_remark+"</td>";
                          row += "<td align='right'>"+value.qty+"</td>";
                          row += "<td>"+value.uom+"</td>";
                          row += "<td align='right'>"+value.qty2+"</td>";
                          row += "<td>"+value.uom2+"</td>";
                          row += "<td align='right'>"+value.qty_jual+"</td>";
                          row += "<td>"+value.uom_jual+"</td>";
                          row += "<td align='right'>"+value.qty2_jual+"</td>";
                          row += "<td>"+value.uom2_jual+"</td>";
                          row += "<td align='right'>"+value.qty_opname+"</td>";
                          row += "<td align='right'>"+value.lebar_greige+"</td>";
                          row += "<td align='right'>"+value.lebar_jadi+"</td>";
                          row += "<td>"+value.lokasi+"</td>";
                          row += "<td>"+value.lokasi_fisik+"</td>";
                          row += "<td>"+value.reff_note+"</td>";
                          row += "<td>"+value.reserve_move+"</td>";
                          row += "</tr>";
                });
                tbody.append(row);
               
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

              // kembalikan icon ke awal
              this_icon.css('pointer-events','');
              this_icon.html('<i class="glyphicon glyphicon-minus "></i>');

            },error: function (jqXHR, textStatus, errorThrown){
              alert(jqXHR.responseText);
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
    
    var id_dept   = '<?php  echo $id_dept;?>';
    $(this).parents("tr").find("td[data-content='edit']").each(function(){
        kode = $(this).attr('data-isi');
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
    loadPageChild(this_icon,kode,group_by,group_ke,tbody_id,page,action,id_dept,root)
    this_icon.html('<i class="fa fa-spinner fa-spin"></i>');
    this_icon.css('pointer-events','none');
  });


  // klik button next
  $(document).on("click", "button[data-pager-action='next']", function(e){
    
    var id_dept   = '<?php  echo $id_dept;?>';
    $(this).parents("tr").find("td[data-content='edit']").each(function(){
        kode = $(this).attr('data-isi');
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
    loadPageChild(this_icon,kode,group_by,group_ke,tbody_id,page,action,id_dept,root)
    this_icon.html('<i class="fa fa-spinner fa-spin"></i>');
    this_icon.css('pointer-events','none');
  });

  
  // untuk meload child dari next/prev button
  function loadPageChild(this_icon,kode,group_by,group_ke,tbody_id,page,action,id_dept,root){

           $.ajax({
            type : 'POST',
            dataType: 'json',
            url  : '<?=base_url()?>warehouse/stockquants/loadChild',
            data : { kode:kode, group_by:group_by, tbody_id:tbody_id, record:page, data_grouping:arr_grouping, data_filter:arr_filter,id_dept :id_dept, group_ke:group_ke, root:root, tmp_arr_group:tmp_arr_group},
            success:function(data){
            
                $("#example1 tbody[data-parent='"+tbody_id+"']").remove();// remove child by data-parent

                let tbody = $("<tbody data-root='"+data.root+"' data-parent='"+tbody_id+"' />");
                let row = '';
                let no  = 1;
                $.each(data.record, function(key, value) {
                          row +=  "<tr  style='background-color: #f2f2f2;' >";
                          row += "<td>"+no+++"</td>";
                          row += "<td>"+value.kode_produk+"</td>";
                          row += "<td class='min-width-200'><a href='<?=base_url()?>warehouse/stockquants/edit/"+value.id_encr+"' target='_blank'>"+value.nama_produk+"</a></td>";
                          row += "<td>"+value.create_date+"</td>";
                          row += "<td>"+value.move_date+"</td>";
                          row += "<td class='min-width-130'>"+value.lot+"</td>";
                          row += "<td>"+value.corak_remark+"</td>";
                          row += "<td>"+value.warna_remark+"</td>";
                          row += "<td>"+value.nama_grade+"</td>";
                          row += "<td align='right' >"+value.qty+"</td>";
                          row += "<td>"+value.uom+"</td>";
                          row += "<td align='right'>"+value.qty2+"</td>";
                          row += "<td>"+value.uom2+"</td>";
                          row += "<td align='right'>"+value.qty_jual+"</td>";
                          row += "<td>"+value.uom_jual+"</td>";
                          row += "<td align='right'>"+value.qty2_jual+"</td>";
                          row += "<td>"+value.uom2_jual+"</td>";
                          row += "<td>"+value.qty_opname+"</td>";
                          row += "<td>"+value.lebar_greige+"</td>";
                          row += "<td>"+value.lebar_jadi+"</td>";
                          row += "<td>"+value.lokasi+"</td>";
                          row += "<td>"+value.lokasi_fisik+"</td>";
                          row += "<td>"+value.reff_note+"</td>";
                          row += "<td>"+value.reserve_move+"</td>";
                          row += "</tr>";
                });
                tbody.append(row);
               
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

</script>


</body>
</html>
