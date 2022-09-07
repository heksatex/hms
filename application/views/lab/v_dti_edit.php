
<!DOCTYPE html>
<html>
<head>
  <?php $this->load->view("admin/_partials/head.php") ?>
   <!-- color picker -->
  <link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/colorpicker/bootstrap-colorpicker.min.css') ?>">
  <style type="text/css">

    button[id="btn-simpan"],button[id="btn-active"],button[id="btn-edit"],button[id="btn-duplicate"],button[id="btn-generate"],button[id="btn-cancel"]{/*untuk hidden button di top bar */
      display: none;
    }

    table.table td .add {
        display: none;
    }

    table.table td .cancel {
        display: none;
        color : red;
        margin: 10 0px;
        min-width:  24px;
    }
    .width-btn {
      width: 54px !important;
    }

    .div1 {
      width: 100%;
      border: 1px solid;
      border-color: #d2d6de;
      padding: 50px;
      margin: 10px 0px 10px 0px;
      border-radius: 5px;
    }
    
    .bs-glyphicons {
      padding-left: 0;
      padding-bottom: 1px;
      margin-bottom: 20px;
      list-style: none;
      overflow: hidden;
    }

    .bs-glyphicons li {
      float: left;
      width: 100%;
      height: 50px;
      padding: 10px;
      margin: 0 -1px -1px 0;
      font-size: 12px;
      line-height: 1.4;
      text-align: left;
      border: 1px solid #ddd;

    }

    .bs-glyphicons .glyphicon {
      margin-top: 5px;
      margin-bottom: 10px;
      font-size: 20px;
      margin : auto;
    }

    .bs-glyphicons .glyphicon-class {
      display: inline-block;
      text-align: center;
      word-wrap: break-word; /* Help out IE10+ with class names */
    }

    .bs-glyphicons li:hover {
      background-color: rgba(86, 61, 124, .1);
    }

    @media (min-width: 768px) {
      .bs-glyphicons li {
        width: 100%;
      }
    }

    .pointer{
      cursor:pointer;
    }

    .width-60{
      width:60px;
    }

    .min-width-50{
      min-width: 50px;
    }

    .min-width-200{
        min-width: 200px;;
    }

    .min-width-100{
        min-width: 100px;
    }

    .min-width-80{
        min-width: 80px;;
    }

    .hide_btn{
      display: none;
    }

    .select2-container--focus{
		    border:  1px solid #66afe9;
    }
      
  </style>
</head>

<body class="hold-transition skin-black fixed sidebar-mini" id="block-page" onload="reloadItems('<?php echo $first_varian;?>')">
<!-- Site wrapper -->
<div class="wrapper" >

  <!-- main -header -->
  <header class="main-header">
    <?php $this->load->view("admin/_partials/main-menu.php") ?>
    <?php
      $data['deptid']     = $id_dept;
      $this->load->view("admin/_partials/topbar.php",$data)
    ?>
  </header>

  <!-- Menu Side Bar -->
  <aside class="main-sidebar">
  <?php $this->load->view("admin/_partials/sidebar.php") ?>
  </aside>

  <!-- Content Wrapper-->
  <div class="content-wrapper" >
    <!-- Content Header (Status - Bar) -->
    <section class="content-header"  >
      <div id ="status_bar">
       <?php 
         $data['jen_status'] =  $color->status;
         $this->load->view("admin/_partials/statusbar.php", $data); 
       ?>
      </div>
    </section>

    <!-- Main content -->
    <section class="content">

      <!--  box content -->
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title"><b><?php echo $color->nama_warna;?></b></h3>
        </div>
        <div class="box-body">
            <form class="form-horizontal" id="form_edit">
            <div class="form-group">                  
              <div class="col-md-12" >
                <div id="alert"></div>
              </div>
            </div>
            <div class="form-group">
              <div class="col-md-6">
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Tanggal dibuat </label></div>
                  <div class="col-xs-8 col-md-8">
                    <input type='text' class="form-control input-sm" name="tgl" id="tgl" readonly="readonly"  value="<?php echo $color->tanggal?>"/>
                    <div id="status_head">
                      <input type='hidden' class="form-control input-sm" name="status" id="status" readonly="readonly"  value="<?php echo $color->status?>"/>
                    </div>
                  </div>                                    
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Nama Warna </label></div>
                  <div class="col-xs-8">
                    <input type="text" class="form-control input-sm" name="warna" id="warna"  value="<?php echo $color->nama_warna?>"  readonly="readonly" />
                  </div>                                    
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Notes </label></div>
                  <div class="col-xs-8 ta" id="ta">
                    <textarea type="text" class="form-control input-sm" name="note" id="note"  readonly="readonly"  ><?php echo $color->notes?></textarea>
                  </div>                                    
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Marketing </label></div>
                  <div class="col-xs-8">
                    <select type="text" class="form-control input-sm" name="sales_group" id="sales_group"  style="width:100% !important" disabled> 
                      <option value="">-- Pilih Marketing --</option>
                          <?php 
                            foreach ($mst_sales_group as $val) {
                                if($val->kode_sales_group == $color->sales_group){
                                  echo "<option value='".$val->kode_sales_group."' selected>".$val->nama_sales_group."</option>";
                                }else{
                                  echo "<option value='".$val->kode_sales_group."'>".$val->nama_sales_group."</option>";
                                }
                            }
                          ?>
                    </select>
                  </div>                                    
                </div>
              </div>
              <div class="col-md-6">
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Kode Warna </label></div>
                  <div class="col-xs-8 col-md-8">
                    <div class="input-group my-colorpicker" id="my-colorpicker">
                      <input type="text" class="form-control input-sm" id="kode_warna" name="kode_warna"  value="<?php echo $color->kode_warna?>" readonly="readonly" >
                      <span class="input-group-addon" id='groupColor' >
                          <i id="wstyle"></i>
                      </span>
                    </div>
                  </div>                                    
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label></label></div>
                  <div class="col-xs-8 col-md-8">
                    <div class="div1"  id="content_colors" style="background-color:<?php echo $color->kode_warna?>">
                    </div>
                  </div>                                    
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label></label></div>
                  <div class="col-xs-8 col-md-8">
                    <ul class="bs-glyphicons">
                      <li class="pointer" onclick="cek_history_dti('<?php echo $color->id ?>', '<?php echo $color->nama_warna?>')" data-toggle="tooltip" title="Lihat History MG">
                        <span class="glyphicon glyphicon-cog"></span>            
                        <span class="glyphicon-class">History DTI List MG</span>
                      </li>                     
                      <li class="pointer" onclick="history_list_OW('<?php echo $color->id ?>')" data-toggle="tooltip" title="Lihat History MG">
                        <span class="glyphicon glyphicon-cog"></span>            
                        <span class="glyphicon-class">History DTI List OW</span>
                      </li>                      
                    </ul>
                  </div>
                </div>
                
              </div>
            </div>
            </form>

            <div class="row">
              <div class="col-md-12">
                <!-- Custom Tabs -->
                <div >
                  <ul id="tab-list" class="nav nav-tabs ">
                    <?php 
                    $loop = 1;
                    foreach($varian as $varians){ 
                          if($loop ==1 ){
                            $active = 'active';
                          }else{
                            $active = '';
                          }
                          $tab_link = 'tab_'.$varians->nama_varian;
                    ?>
                          <li class="<?php echo $active;?>"><a href="<?php echo '#'.$tab_link; ?>" data-toggle="tab" warna='<?php echo $color->id ?>' varian="<?php echo $varians->id?>"><?php echo $varians->nama_varian?></a></li>
                    <?php 
                        $loop++;
                    }
                    ?>
                    <li id="btn_tabs">
                      <?php if($color->status != 'cancel'){?>
                      <button type="button" id="add-varian" class='btn btn-primary btn-sm' title="Tambah Varian Warna"> <i class="fa fa-plus"> Tambah Varian</i></button>
                      <?php } ?>
                    </li>
                  </ul>
                  <div class="tab-content"><br>
                  
                        <div class="tab-pane active" id="tab_A">

                          <!-- Tabel Dye stuff  -->
                          <div class="col-md-6 table-responsive">
                            <table class="table table-condesed table-hover rlstable" width="100%" id="table_dyest" >
                              <label>Dyeing Stuff</label>
                              <thead>
                                <tr>
                                  <th class="style no">No.</th>
                                  <th class="style">Product</th>
                                  <th class="style">Qty (%)</th>
                                  <th class="style">UoM</th>
                                  <th class="style">reff notes</th>
                                  <th class="style min-width-50"></th>
                                </tr>
                              </thead>
                              <tbody id="tbody_dye">
                              </tbody>
                              <tfoot>
                                <tr>
                                  <td colspan="6">
                                  </td>
                                </tr>
                              </tfoot>
                            </table>
                            <div id="example1_processing" class="table_processing" style="display: none">
                              Processing...
                            </div>
                          </div>
                          <!-- Tabel Dye stuff -->

                          <!-- Tabel AUX  -->
                          <div class="col-md-6 table-responsive">
                            <table class="table table-condesed table-hover rlstable" width="100%" id="table_aux" >
                              <label>Auxiliary</label>
                              <thead>
                                <tr>
                                  <th class="style no">No.</th>
                                  <th class="style">Product</th>
                                  <th class="style">Qty (g/L)</th>
                                  <th class="style">UoM</th>
                                  <th class="style">reff notes</th>
                                  <th class="style  min-width-50"></th>
                                </tr>
                              </thead>
                              <tbody id="tbody_aux">
                              </tbody>
                              <tfoot>
                                <tr>
                                  <td colspan="6">
                                  </td>
                                </tr>
                              </tfoot>
                            </table>
                            <div id="example2_processing" class="table_processing" style="display: none">
                              Processing...
                            </div>
                          </div>
                          <!-- Tabel AUX -->
                          <!-- notes DTI Varian-->
                          <div class="col-md-8 col-xs-12">
                            <br>
                            <div class="col-xs-4"><label>Notes Varian </label></div>
                            <div class="col-md-6 col-xs-8 " id="ta">
                              <textarea type="text" class="form-control input-sm" name="note_varian" id="note_varian" readonly="readonly"  ></textarea>
                                <br>
                              </div>  
                          </div>                                  
                            
                        </div>

                  </div>
                  <!-- /.tab-content -->
                </div>
                <!-- nav-tabs-custom -->
              </div> 
              <!-- /.col -->
            </div>
           
        </div>
        <!-- /.box-body -->
      </div>
      <!-- /.box -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <footer class="main-footer">
   <?php $this->load->view("admin/_partials/modal.php") ?>
    <div id="foot">
     <?php 
        $data['kode'] =  $color->id;
        $data['mms']  =  $mms->kode;
        $this->load->view("admin/_partials/footer.php",$data) 
     ?>
    </div>
  </footer>

</div>

<style type="text/css">
	.error{
		border:  1px solid red !important;
	}  

</style>

<?php $this->load->view("admin/_partials/js.php") ?>
<!-- color picker -->
<script src="<?php echo site_url('plugins/colorpicker/bootstrap-colorpicker.min.js') ?>"></script>

<script type="text/javascript">

  $(".my-colorpicker").colorpicker();

  $('.my-colorpicker').colorpicker().on('changeColor', function (e) {
      $('#content_colors')[0].style.backgroundColor = e.color.toHex();
  });

  //untuk mengatur lebar textarea sesuai value yang ada
  $('.ta').on( 'change keyup keydown paste cut', 'textarea', function (){
    $(this).height(0).height(this.scrollHeight);
  }).find( 'textarea' ).change();

   //validasi qty
  function validAngka(a){
    if(!/^[0-9.]+$/.test(a.value))
    {
      a.value = a.value.substring(0,a.value.length-1000);
    }
  }
  
  //auto height in textarea
  function textAreaAdjust(o) {
    o.style.height = "1px";
    o.style.height = (25+o.scrollHeight)+"px";
  }

  //html entities javascript
  function htmlentities_script(str) {
    return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
  }

  // untuk focus after select2 close
  $(document).on('focus', '.select2', function (e) {
    if (e.originalEvent) {
        var s2element = $(this).siblings('select');
        s2element.select2('open');

        // Set focus back to select2 element on closing.
        s2element.on('select2:closing', function (e) {
            s2element.select2('focus');
        });
    }
  });
 
  var status = $('#status').val();
  if(status == 'cancel'){
    $("#btn-cancel").hide();
    $("#btn-active").show();
  }else{
    $("#btn-generate").show();
    $("#btn-duplicate").show();
    $("#btn-edit").show();
    $("#btn-cancel").show();
  }

  var unsaved = false;
  var unsaved2 = false;

  //untuk editable textfield 
  $(document).on('click','#btn-edit', function(e){
    
    $("#btn-simpan").show();//tampilkan btn-simpan
    $("#btn-edit").hide();//sembuyikan btn-edit
    $("#btn-generate").hide();//sembuyikan btn-generate
    $("#btn-duplicate").hide();//sembuyikan btn-duplicate
    $("#btn-print").hide();//sembuyikan btn-print
    $("#btn-active").hide();//sembuyikan btn-print

    $("#btn-cancel").attr('id','btn-cancel-edit');// ubah id btn-cancel jadi btn-cancel-edit
    $("#note").attr("readonly", false);
    $("#note_varian").attr("readonly", false);
    $('#sales_group').attr('disabled', false).attr('id', 'sales_group');
    $("#kode_warna").attr("readonly", false);

    var id_varian = $('#tab-list li.active a[data-toggle="tab"] ').attr('varian');
    var id_warna  = "<?php echo $color->id;?>";

    $("#example1_processing").css('display',''); 
    $("#example2_processing").css('display',''); 

    $.ajax({
          url : '<?php echo site_url('lab/dti/get_items_dti_for_edit') ?>',
          type: "POST",
          dataType : "JSON",
          data: {id_warna:id_warna, id_varian:id_varian},
          beforeSend: function(e) {
              $('#table_dyest tbody').remove();
              $('#table_aux tbody').remove();
          },
          success: function(data){
              unsaved  = true;
              unsaved2 =  true;
              var row = '';
              $('#table_dyest').append("<tbody id='tbody_dye'></tbody>");
              $.each(data.record1, function(key, value) {
                  tambah_baris(true,'table_dyest',value.kode_produk,value.nama_produk ,value.qty, value.uom, value.reff_note);
              });
              
              $('#table_aux').append("<tbody id='tbody_aux'></tbody>");
              $.each(data.record2, function(key, value) {
                  tambah_baris(true,'table_aux',value.kode_produk,value.nama_produk ,value.qty, value.uom, value.reff_note);
              });

              $('#tab-list li:last').remove();

              // replace tfoot
              $('#table_dyest tfoot tr').remove();
              event = "tambah_baris(false,'table_dyest','','','','','')";
              var tr ='<tr><td colspan="6"> <a href="javascript:void(0)" onclick="'+event+'"><i class="fa fa-plus"></i> Tambah Data</a></td></tr>';
              $('#table_dyest tfoot').append(tr);

              $('#table_aux tfoot tr').remove();
              event = "tambah_baris(false,'table_aux','','','','','')";
              var tr ='<tr><td colspan="6"> <a href="javascript:void(0)" onclick="'+event+'"><i class="fa fa-plus"></i> Tambah Data</a></td></tr>';
              $('#table_aux tfoot').append(tr);

              $("#example1_processing").css('display','none'); 
              $("#example2_processing").css('display','none'); 
              
          },
          error: function (xhr, ajaxOptions, thrownError){
              alert('Error data');
              alert(xhr.responseText);
          }

      });

  });

  // ketika simpan/cancel edit
  function reloadForm(id_varian){
      $("#btn-simpan").hide();//sembuyikan btn-simpan
      $("#btn-edit").show();//tampilkan btn-edit
      $("#btn-generate").show();//tampilkan btn-generate
      $("#btn-duplicate").show();//tampilkan btn-duplicate
      $("#btn-print").show();//tampilkan btn-print

      $("#btn-cancel-edit").attr('id','btn-cancel');// ubah id btn-cancel-edit jadi btn-cancel
      $("#note").attr("readonly", true);
      $("#note_varian").attr("readonly", true);
      $('#sales_group').attr('disabled', true)
      $("#kode_warna").attr("readonly", true);

      $('#tab-list').append('<li id="btn_tabs"><button type="button" id="add-varian" class="btn btn-primary btn-sm" title="Tambah Varian Warna"> <i class="fa fa-plus"> Tambah Varian</i></button></li>');
      reloadItems(id_varian);
      unsaved  = false;
      unsaved2 = false;
      $("#form_edit").load(location.href + " #form_edit>*");
      $(".my-colorpicker").colorpicker();
  }


  $(document).on('click','#btn-cancel-edit', function(e){

    var id_varian = $('#tab-list li.active a[data-toggle="tab"] ').attr('varian');
    //alert(id_varian)
    //alert(unsaved2);
    if(unsaved){
        var dialog = bootbox.dialog({
          title: "<font color='red'><i class='fa fa-warning'></i></font> Warning !",
          message: "<p>Tinggalkan perubahan yang belum anda simpan </p>",
          size: 'medium',
          buttons: {
            ok: {
              label: "Yes",
              className: 'btn-primary btn-sm',
              callback: function(){
                reloadForm(id_varian)
              }
            },
            cancel: {
                label: "No",
                className: 'btn-default btn-sm',
                callback: function(){
                }
            },
          }
        });
      }else{
        reloadItems(id_varian);
      }

  });

  /*
  $(document).on('shown.bs.tab', 'a[data-toggle="tab"]', function (e) {
    var tab = $(e.target);
    var contentId = tab.attr("href");

    var previousTab = $(e.relatedTarget);
    //alert('before'+ previousTab.attr('href'));

    //This check if the tab is active
    if (tab.parent().hasClass('active')) {
        //alert('the tab with the content id is visible');
    } else {
      //alert('the tab with the content id ' + contentId + ' is NOT visible');
    }

  });
    */
  
  // reload items dti
  function reloadItems(id_varian){

        var id_warna  = "<?php echo $color->id;?>";
        $.ajax({
          url : '<?php echo site_url('lab/dti/view_tab_body') ?>',
          type: "POST",
          data: {id_warna   : id_warna, 
                type        :'AUX',
                id_varian   : id_varian,  },
          beforeSend: function(e) {
              $('#table_aux tbody').remove();
              $("#example2_processing").css('display',''); 
          },
          success: function(html){
              $('#table_aux').append("<tbody id='tbody_aux'></tbody>");
              setTimeout(function() {$('#table_aux tbody').html(html);  });
              $("#example2_processing").css('display','none'); 
              $("#note_varian").attr("readonly", true);
              $('#table_aux tfoot tr').remove();
              //var tr ='<tr><td colspan="6"> <a href="javascript:void(0)" class="add-new" type_obat="AUX"><i class="fa fa-plus"></i> Tambah Data</a></td></tr>';
              //$('#table_aux tfoot').append(tr);
              
          },
          error: function (xhr, ajaxOptions, thrownError){
              alert('Error data');
              alert(xhr.responseText);
          }
        });

        $.ajax({
          url : '<?php echo site_url('lab/dti/view_tab_body') ?>',
          type: "POST",
          data: {id_warna   : id_warna, 
                type        :'DYE',
                id_varian   : id_varian,  },
          beforeSend: function(e) {
              $('#table_dyest tbody').remove();
              $("#example1_processing").css('display',''); 
             
          },
          success: function(html){
              $('#table_dyest').append("<tbody id='tbody_dye'></tbody>");
              setTimeout(function() {$('#table_dyest tbody').html(html);  });
              $("#example1_processing").css('display','none');

              $('#table_dyest tfoot tr').remove();
              //var tr ='<tr><td colspan="6"> <a href="javascript:void(0)" class="add-new" type_obat="DYE"><i class="fa fa-plus"></i> Tambah Data</a></td></tr>';
              //$('#table_dyest tfoot').append(tr); 
          },
          error: function (xhr, ajaxOptions, thrownError){
              alert('Error data');
              alert(xhr.responseText);
          }
        });


        $.ajax({
          url : '<?php echo site_url('lab/dti/get_note_varian') ?>',
          type: "POST",
          dataType: "json",
          data: {id_warna   : id_warna, 
                id_varian   : id_varian,  },
          beforeSend: function(e) {
              $('#note_varian').val();
          },
          success: function(data){
              setTimeout(function() {$('#note_varian').val(data.isi);  });
          },
          error: function (xhr, ajaxOptions, thrownError){
              alert('Error data');
              alert(xhr.responseText);
          }
        });

  }

  
  // show tab
  $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
      var id_varian = $(this).attr('varian');

      var activeTab   = $(e.target);
      var previousTab = $(e.relatedTarget);
      //activeTabPane   = $(activeTab.attr('href'))
      //previousTabPane = $(previousTab.attr('href'));

      //Remove class active
      activeTab.parent().removeClass('active');

      //Add Active class to clicked tab
      //var a = previousTab.parent().hasClass('active');
              
      if(unsaved){

        if(unsaved2){
          var message2 =  "<p>Tinggalkan perubahan yang belum anda simpan </p>";
        }else{
          var message2 =  "<p>Simpan terlebih dahulu Varian yang telah anda tambahkan atau tinggalkan tanpa menyimpan Varian yang telah anda tambahkan </p>";
        }

        var dialog = bootbox.dialog({
          title: "<font color='red'><i class='fa fa-warning'></i></font> Warning !",
          message: message2,
          size: 'medium',
          buttons: {
            ok: {
              label: "Yes",
              className: 'btn-primary btn-sm',
              callback: function(){
                console.log('Custom OK clicked');
                // clear varian yg telah ditambahkan
                activeTab.parent().addClass('active');
                // hapus tab list tambah varian
                tabAddVar = previousTab.attr("href");
                if(unsaved2){
                  reloadForm(id_varian);
                }else{
                  $('#tab-list li a[href="'+tabAddVar+'"]').remove();
                  reloadItems(id_varian);
                }
                $('#tab-list li:last button[type="button"]').remove();
                $('#tab-list').append('<li id="btn_tabs"><button type="button" id="add-varian" class="btn btn-primary btn-sm" title="Tambah Varian Warna"> <i class="fa fa-plus"> Tambah Varian</i></button></li>');
                $('#btn-edit').show();
                $('#btn-generate').show();
                unsaved = false;
              }
            },
            cancel: {
                label: "No",
                className: 'btn-default btn-sm',
                callback: function(){
                  // active tambah varian
                  previousTab.parent().addClass('active');
                }
            },
          }
        });
      }else{
        reloadItems(id_varian);
        activeTab.parent().addClass('active');
      }

  })

/*
  //modal tambah data Dyeing Stuff
  $(".add").unbind( "click" );
  $(document).on('click','.add',function(e){
      var id_warna = '<?php echo $color->id?>';
      e.preventDefault();
      $(".tambah_data").html('<center><h5><img src="<?php echo base_url('dist/img/ajax-loader.gif') ?> "/><br>Please Wait...</h5></center>');
      $("#tambah_data").modal({
          show: true,
          backdrop: 'static'
      });
      $('.modal-title').text('Tambah Data Dyeing Stuff');
        $.post('<?php echo site_url()?>lab/dti/tambah_dyeing_stuff_modal',
          {id_warna:id_warna, warna:$('#warna').val(), tipe_obat:'DYE'},
          function(html){
            setTimeout(function() {$(".tambah_data").html(html);  },2000);
          }   
       );
  });

  $(".add2").unbind( "click" );
  $(document).on('click','.add2',function(e){
      var id_warna = '<?php echo $color->id?>';
      e.preventDefault();
      $(".tambah_data").html('<center><h5><img src="<?php echo base_url('dist/img/ajax-loader.gif') ?> "/><br>Please Wait...</h5></center>');
      $("#tambah_data").modal({
          show: true,
          backdrop: 'static'
      });
      $('.modal-title').text('Tambah Data AUX');
        $.post('<?php echo site_url()?>lab/dti/tambah_aux_modal',
          {id_warna:id_warna, warna:$('#warna').val(), tipe_obat:'AUX'},
          function(html){
            setTimeout(function() {$(".tambah_data").html(html);  },2000);
          }   
       );
  });
  */

  // edit dyeing stuff / aux
  function edit(caption, row_order, kode_produk, nama_produk)
  {
      var id_warna = '<?php echo $color->id?>';
      var warna    = '<?php echo $color->nama_warna?>';
      $("#edit_data").modal({
          show: true,
          backdrop: 'static'
      });
      $("#edit_data .modal-dialog .modal-content .modal-footer #btn-ubah").attr('disabled',true);

      $(".edit_data").html('<center><h5><img src="<?php echo base_url('dist/img/ajax-loader.gif') ?> "/><br>Please Wait...</h5></center>');
      $('.modal-title').text('Edit '+caption);

      $.post('<?php echo site_url()?>lab/dti/edit_dye_aux_modal',
            {id_warna:id_warna, warna:warna, row_order:row_order, kode_produk:kode_produk,nama_produk:nama_produk },
      ).done(function(html){
            setTimeout(function() {
              $(".edit_data").html(html)  
            },1000);
            $("#edit_data .modal-dialog .modal-content .modal-footer #btn-ubah").attr('disabled',false);
      });
  }

  // cek hisotry DTI
  function cek_history_dti(id_warna, nama_warna){
      $("#view_data").modal({
          show: true,
          backdrop: 'static'
      })
      $(".view_body").html('<center><h5><img src="<?php echo base_url('dist/img/ajax-loader.gif') ?> "/><br>Please Wait...</h5></center>');
      $('.modal-title').text('History DTI List MG ( '+nama_warna+')' );
      $.post('<?php echo site_url()?>lab/dti/view_history_dti',
          {id_warna : id_warna},
          function(html){
            setTimeout(function() {$(".view_body").html(html);  },1000);
          }   
      );
  }

  // cek list ow yang sesuai DTI
  function history_list_OW(id_warna, nama_warna){
        $("#view_data").modal({
            show: true,
            backdrop: 'static'
        });
        $(".view_body").html('<center><h5><img src="<?php echo base_url('dist/img/ajax-loader.gif') ?> "/><br>Please Wait...</h5></center>');
        $('.modal-title').text('History DTI List OW ' );
        $.post('<?php echo site_url()?>lab/dti/view_history_ow',
            {id_warna : id_warna},
            function(html){
              setTimeout(function() {$(".view_body").html(html);  },1000);
            }   
        );
    }

  // tambah varian warna
  $(document).on("click", "#add-varian", function(e){
      var id_varian = $('#tab-list li.active a[data-toggle="tab"] ').attr('varian');
      $('#tab-list >li').removeClass('active');
      var id_warna = '<?php echo $color->id?>';
    
      $.ajax({
          url : '<?php echo site_url('lab/dti/get_items_dti') ?>',
          type: "POST",
          dataType : "JSON",
          data: {id_warna:id_warna, id_varian:id_varian},
          beforeSend: function(e) {
              $('#table_dyest tbody').remove();
              $('#table_aux tbody').remove();
              $('#add-varian').button('loading');
          },
          success: function(data){

              if(data.new_varian == ''){
                alert_modal_warning("Maaf, Varian tidak bisa ditambah lagi !");
                reloadItems(id_varian);
                $('#tab-list li a[varian="'+id_varian+'"] ').parents().addClass('active');
                $('#add-varian').button('reset');
              }else{

                unsaved = true;
                var row = '';
                // add items DYE
                // new tab varian
                ($('<li class="active"><a href="#tab' + data.new_varian + '" role="tab" data-toggle="tab">'+data.new_varian+' <button style="margin-left:5px" class="close" type="button" title="Batal Tambah Varian Baru" > × </button></a></li>')).insertBefore('#tab-list li:last');
                
                $('#table_dyest').append("<tbody id='tbody_dye'></tbody>");
                $.each(data.record1, function(key, value) {
                    tambah_baris(true,'table_dyest',value.kode_produk,value.nama_produk ,value.qty, value.uom, value.reff_note);
                });
                
                $('#table_aux').append("<tbody id='tbody_aux'></tbody>");
                $.each(data.record2, function(key, value) {
                    tambah_baris(true,'table_aux',value.kode_produk,value.nama_produk ,value.qty, value.uom, value.reff_note);
                });

                // change btn plus to save
                $('#tab-list li:last').remove();
                $('#tab-list').append('<li id="btn_tabs"><button type="button" id="save-varian" class="btn btn-primary btn-sm" title="Simpan Varian Warna"> <i class="fa fa-save"> Simpan</i></button></li>');

                // replace tfoot
                $('#table_dyest tfoot tr').remove();
                event = "tambah_baris(false,'table_dyest','','','','','')";
                var tr ='<tr><td colspan="6"> <a href="javascript:void(0)" onclick="'+event+'"><i class="fa fa-plus"></i> Tambah Data</a></td></tr>';
                $('#table_dyest tfoot').append(tr);

                $('#table_aux tfoot tr').remove();
                event = "tambah_baris(false,'table_aux','','','','','')";
                var tr ='<tr><td colspan="6"> <a href="javascript:void(0)" onclick="'+event+'"><i class="fa fa-plus"></i> Tambah Data</a></td></tr>';
                $('#table_aux tfoot').append(tr);

                // hidden btn 
                $('#btn-edit').hide();
                $('#btn-generate').hide();

              }
              
          },
          error: function (xhr, ajaxOptions, thrownError){
              alert('Error data');
              alert(xhr.responseText);
          }

      });

      $.ajax({
          url : '<?php echo site_url('lab/dti/get_note_varian') ?>',
          type: "POST",
          dataType: "json",
          data: {id_warna   : id_warna, 
                id_varian   : id_varian,  },
          beforeSend: function(e) {
              $('#note_varian').val();
          },
          success: function(data){
              $("#note_varian").attr("readonly", false);
              setTimeout(function() {$('#note_varian').val(data.isi);  });
          },
          error: function (xhr, ajaxOptions, thrownError){
              alert('Error data');
              alert(xhr.responseText);
          }
        });

  });

  // batal tambah varian baru
  $('#tab-list').on('click', '.close', function() {
        unsaved = false;
        var tabID = $(this).parents('a').attr('href');
        $(this).parents('li').remove();
        $(tabID).remove();

        //display first tab
        var tabFirst = $('#tab-list li a:first');
        tabFirst.tab('show');
        tabFirst.parent().addClass('active');

        $('#tab-list li:last').remove();
        // show btn plus
        $('#tab-list').append('<li id="btn_tabs"><button type="button" id="add-varian" class="btn btn-primary btn-sm" title="Tambah Varian Warna"> <i class="fa fa-plus"> Tambah Varian</i></button></li>');
        $('#btn-edit').show();
        $('#btn-generate').show();
  });

   // hapus row
  function delRow_dye(r){		
	  	var i = r.parentNode.parentNode.rowIndex;
		  document.getElementById("table_dyest").deleteRow(i);
	}

  function delRow_aux(r){		
	  	var i = r.parentNode.parentNode.rowIndex;
		  document.getElementById("table_aux").deleteRow(i);
	}

  // simpan varian warna
  $(document).on("click", "#save-varian", function(){

      var arr   = new Array();
      var arr2  = new Array();
      var id_warna = '<?php echo $color->id?>';
      var note_varian = $('#note_varian').val();

      var empty = false;
      var empty2 = false;
      var empty3= false;

      $("#table_dyest tbody[id='tbody_dye'] .kode_produk").each(function(index, element) {
            if ($(element).val()!=="" && $(element).val()!==null) {
              arr.push({
                //0 : no++,
                kode_produk :$(element).val(),
                nama_produk :$(element).parents("tr").find("#nama_produk").val(),
                qty 		    :$(element).parents("tr").find("#qty").val(),
                uom 		    :$(element).parents("tr").find("#uom").val(),
                reff_note 	:$(element).parents("tr").find("#reff").val(),
              });
              $(this).parents('td').find('span span.selection span.select2-selection').removeClass('error'); 
            }else{
              empty = true;
              $(this).parents('td').find('span span.selection span.select2-selection').addClass('error'); 
              alert_notify('fa fa-warning','Product Harus Diisi !','danger',function(){});
            }
      }); 

      $("#table_dyest tbody[id='tbody_dye'] .qty").each(function(index, element) {
            if ($(element).val()==""  ) {
              empty2 = true;
              alert_notify('fa fa-warning','Qty Harus Diisi !','danger',function(){});
              $(this).addClass('error'); 
            }else{
              $(this).removeClass('error'); 
            }
      }); 

      $("#table_dyest tbody[id='tbody_dye'] .uom").each(function(index, element) {
            if ($(element).val()=="" || $(element).val()==null  ) {
              empty3 = true;
              alert_notify('fa fa-warning','uom Harus Diisi !','danger',function(){});
              $(this).parents('td').find('span span.selection span.select2-selection').addClass('error'); 
            }else{
              $(this).parents('td').find('span span.selection span.select2-selection').removeClass('error'); 
            }
      }); 

      $("#table_aux tbody[id='tbody_aux'] .kode_produk").each(function(index, element) {
            if ($(element).val()!=="" && $(element).val()!==null ) {
              arr2.push({
                //0 : no++,
                kode_produk :$(element).val(),
                nama_produk :$(element).parents("tr").find("#nama_produk").val(),
                qty 		    :$(element).parents("tr").find("#qty").val(),
                uom 		    :$(element).parents("tr").find("#uom").val(),
                reff_note 	:$(element).parents("tr").find("#reff").val(),
              });
              $(this).parents('td').find('span span.selection span.select2-selection').removeClass('error'); 
            }else{
              empty = true;
              alert_notify('fa fa-warning','Product Harus Diisi !','danger',function(){});
              $(this).parents('td').find('span span.selection span.select2-selection').addClass('error'); 
            }
      });


      $("#table_aux tbody[id='tbody_aux'] .qty").each(function(index, element) {
            if ($(element).val()=="" ) {
              empty2 = true;
              alert_notify('fa fa-warning','Qty Harus Diisi !','danger',function(){});
              $(this).addClass('error'); 
            }else{
              $(this).removeClass('error'); 
            }
      }); 

      $("#table_aux tbody[id='tbody_aux'] .uom").each(function(index, element) {
            if ($(element).val()=="" || $(element).val()==null  ) {
              empty3 = true;
              alert_notify('fa fa-warning','uom Harus Diisi !','danger',function(){});
              $(this).parents('td').find('span span.selection span.select2-selection').addClass('error'); 
            }else{
              $(this).parents('td').find('span span.selection span.select2-selection').removeClass('error'); 
            }
      }); 


      if(!empty && !empty2 && !empty3 ){

        //alert(''+JSON.stringify(arr2));
        $('#save-varian').button('loading');
        $("#example2_processing").css('display',''); 
         $.ajax({
          dataType: "JSON",
          url : '<?php echo site_url('lab/dti/simpan_varian') ?>',
          type: "POST",
          data: { id_warna   : id_warna, 
                  arr_dye : JSON.stringify(arr),
                  arr_aux : JSON.stringify(arr2),
                  note_varian : note_varian
                },
          success: function(data){
            if(data.sesi=='habis'){
                //alert jika session habis
                alert_modal_warning(data.message);
                window.location.replace('../index');
            }else if(data.status == 'failed'){
                alert_modal_warning(data.message);
                //alert_notify(data.icon,data.message,data.type,function(){});
            }else{
                $(".add-new").show();                   
                $("#foot").load(location.href + " #foot");
                $("#status_head").load(location.href + " #status_head");
                alert_notify(data.icon,data.message,data.type,function(){});
                reloadItems(data.id_varian);
                // dispay btn remove varian = none
                $("#tab-list li.active a button.close").addClass('hide_btn');
                // add atribut varian dan warna
                $('ul.nav li.active a[data-toggle="tab"]').attr("varian", data.id_varian);
                $('ul.nav li.active a[data-toggle="tab"]').attr("warna", data.id_warna);

                $('#tab-list li:last').remove();
                // show btn plus
                $('#tab-list').append('<li id="btn_tabs"><button type="button" id="add-varian" class="btn btn-primary btn-sm" title="Tambah Varian Warna"> <i class="fa fa-plus"> Tambah Varian</i></button></li>');
                unsaved = true;
            }
            $('#btn-edit').show();
            $('#btn-generate').show();
            $("#example2_processing").css('display','none'); 
            $('#save-varian').button('reset');

          },
          error: function (xhr, ajaxOptions, thrownError){
            alert('Error data');
            alert(xhr.responseText);
          }
        });
      }

  });

  

  // tambah baris
  function tambah_baris(data,table,kode_produk,nama_produk,qty,uom,reff_note){
        var tambah = true;

        if(table == 'table_dyest'){
          var index  = $("#table_dyest tbody[id='tbody_dye'] tr:last-child").index();
          if(index== -1){
            row = 0;
          }else{
            row  = parseInt($("#table_dyest tbody[id='tbody_dye'] tr:last-child td .row").val());
          }
          event      = "enter(event,'table_dyest')";

          tbody_id   = 'tbody_dye';
          link_get_list   = "get_list_dye";
          delRow  = "delRow_dye(this)";

          tbl  = "#table_dyest tbody[id='tbody_dye'] ";
          row_idx = row;
          

        }else{
          var index  = $("#table_aux tbody[id='tbody_aux'] tr:last-child").index();
          tbody_id   = 'tbody_aux';
          link_get_list   = "get_list_aux";

          event      = "enter(event,'table_aux')";

          if(index== -1){
            row = 0;
          }else{
            row  = parseInt($("#table_aux tbody[id='tbody_aux'] tr:last-child td .row").val());
          }
          delRow  = "delRow_aux(this)";

          tbl  = "#table_aux tbody[id='tbody_aux'] ";
        }
        
        //var np = document.getElementsByName('nama_produk');
        var np = $(tbl+" td input[name='Product']");
        var inx_np = np.length-1;

        //var inx_np2 = $(tbl+" tr:last-child td .nama_produk").index();
        //var np1 = nm;
        //var np1 = document.getElementsByName('nama_produk');
        //var inx_np1 = np.length-1;
        //alert(inx_np1);

        //var n_qty = document.getElementsByName('Qty');
        var n_qty = $(tbl+" td input[name='Qty']");
		    var inx_n_qty = n_qty.length-1;

        var n_uom = $(tbl+" td input[name='Uom']");
		    var inx_n_uom = n_uom.length-1;

        //cek Product apa ada yg kosong
        $(tbl+' .kode_produk').each(function(index,value){
          if($(value).val()=='' || $(value).val() == null){
              alert_notify('fa fa-warning','Product Harus Diisi !','danger',function(){});
              var s2element = $(this).parents(tbl).find(np[inx_np]).siblings('select');
              //var s2element = $(this).parents('td').find('.kode_produk').siblings('select');
              s2element.select2('open');
              s2element.on('select2:closing', function (e) {
                  s2element.select2('focus');
              });
              $(this).parents('td').find('span span.selection span.select2-selection').addClass('error'); 
              tambah = false;
          }else{
              $(this).parents('td').find('span span.selection span.select2-selection').removeClass('error'); 
          }
        });
        
        //cek qty apa ada yg kosong
        $(tbl+' .qty').each(function(index,value){
          if($(value).val()==''){
              alert_notify('fa fa-warning','Qty Harus Diisi !','danger',function(){});
              $(this).parents(tbl).find(n_qty[inx_n_qty]).focus();
              $(value).addClass('error'); 
              tambah = false;
          }else{
              $(value).removeClass('error'); 
          }
        });

        //cek uom apa ada yg kosong
        $(tbl+' .uom').each(function(index,value){
          if($(value).val()=='' || $(value).val() == null){
              alert_notify('fa fa-warning','Uom Harus Diisi !','danger',function(){});
              var s2element = $(this).parents(tbl).find(uom[inx_n_uom]).siblings('select');
              s2element.select2('open');
              s2element.on('select2:closing', function (e) {
                  s2element.select2('focus');
              });
              $(this).parents('td').find('span span.selection span.select2-selection').addClass('error'); 
              tambah = false;
          }else{
              $(this).parents('td').find('span span.selection span.select2-selection').removeClass('error'); 
          }
        });

       
        if(tambah){

            var ro     = row+1;
            //alert('jml ro '+ro);
          
            var class_produk = 'kode_produk_'+ro;
            var produk       = 'nama_produk'+ro;
            var class_uom    = 'uom_'+ro;
            var row        = '<tr class="num">'
                        + '<td><input type="hidden"  name="row" class="row" value="'+ro+'"></td>'
                        + '<td  class="min-width-200">'
                            + '<select add="manual" type="text" class="form-control input-sm kode_produk '+class_produk+'" name="Product" id="kode_produk"></select>'
                            + '<input type="hidden" class="form-control input-sm nama_produk '+produk+'" name="nama_produk" id="nama_produk" value="'+nama_produk+'"></td>'
                        + '<td class="min-width-100"><input type="text" class="form-control input-sm qty" name="Qty" id="qty"  onkeyup="validAngka(this)" onkeypress="'+event+'"  value="'+qty+'"></td>'
                        + '<td class="min-width-100"><select type="text" class="form-control input-sm uom '+class_uom+'" name="Uom" id="uom"></select></td>'
                        + '<td class="min-width-100"><textarea type="text" class="form-control input-sm" name="note" id="reff" onkeypress="'+event+'"  >'+reff_note+'</textarea></td>'
                        + '<td class="width-50" align="center"><a onclick="'+delRow+'"  href="javascript:void(0)"  data-toggle="tooltip" title="Hapus Data"><i class="fa fa-trash" style="color: red"></i> </a></td>'
                        + '</tr>';

            $('#'+table+' tbody[id="'+tbody_id+'"] ').append(row);
            $('[data-toggle="tooltip"]').tooltip();

            var sel_produk = $('#'+table+' tbody[id="'+tbody_id+'"] tr .'+class_produk);
            var sel_uom    = $('#'+table+' tbody[id="'+tbody_id+'"] tr .'+class_uom);
            var produk_hide= $('#'+table+' tbody[id="'+tbody_id+'"] tr .'+produk);

            if(data==true){
                //untuk event selected select2 nama_produk
                custom_nama = '['+kode_produk+'] '+nama_produk;
                var $newOption = $("<option></option>").val(kode_produk).text(custom_nama);
                sel_produk.empty().append($newOption).trigger('change');

                var $newOption2 = $("<option></option>").val(uom).text(uom);
                sel_uom.empty().append($newOption2).trigger('change');

            }

            //select 2 product
            sel_produk.select2({
                ajax:{
                    dataType: 'JSON',
                    type : "POST",
                    url : "<?php echo base_url();?>lab/dti/"+link_get_list,
                    //delay : 250,
                    data : function(params){
                        return{
                        prod:params.term
                        };
                    }, 
                    processResults:function(data){
                        var results = [];

                        $.each(data, function(index,item){
                            results.push({
                                id:item.kode_produk,
                                text:'['+item.kode_produk+'] '+item.nama_produk
                            });
                        });
                        return {
                        results:results
                        };
                    },
                    error: function (xhr, ajaxOptions, thrownError){
                        console.log(xhr.responseText);
                    }
                }
            });
            /*
            var np = $(tbl+" tr td select[name='kode_produk']");
            var np1 = document.getElementsByName('nama_produk');
            
            var inx_np = np.length-1;
            alert(np)
            */

            if(data==false){
              //alert(np[inx_np+1]);
              // open select 2 after add ro
              //alert(tbl+' = '+inx_np);
              //alert(tbl+' = '+inx_np2);
              //var s2element = $('.nama_produk').parents('tr td').find(np[inx_np+1]).siblings('select');
              //var s2element =  $(tbl+' tr .kode_produk').parents('td').find(np2[inx_np2+1]).siblings('select');
              /*
              if(row_idx <= 0){
                aa = 0;
                alert('aa1 '+aa);
              }else{
                aa = inx_n_qty+1;
                alert('aa2 '+aa);
              }
              */
              
              //$('.qty').parents(tbl).find(n_qty[aa]).val('123');
              //var s2element =  $('.kode_produk').parents(tbl).find(np[0]).siblings('select');
              var s2element = $(this).parents(tbl).find(np[inx_np]).siblings('select');
              s2element.select2('open');
              s2element.on('select2:closing', function (e) {
                s2element.select2('focus');
              });
            }
              
            //jika nama produk diubah
            sel_produk.change(function(){
                
                $.ajax({
                    dataType: "JSON",
                    url : '<?php echo site_url('lab/dti/get_prod_by_id') ?>',
                    type: "POST",
                    data: {kode_produk: $(this).parents("tr").find("#kode_produk").val() },
                    success: function(data){
                        produk_hide.val(data.nama_produk);
                        //$('#qty').val(data.qty);
                        //untuk event selected select2 uom
                        var $newOptionuom = $("<option></option>").val(data.uom).text(data.uom);
                        sel_uom.empty().append($newOptionuom).trigger('change');
                    },
                    error: function (xhr, ajaxOptions, thrownError){
                        console.log(xhr.responseText);
                    }
                });
            });
          
            
            //select 2 uom
            sel_uom.select2({
                allowClear: true,
                placeholder: "",
                ajax:{
                        dataType: 'JSON',
                        type : "POST",
                        url : "<?php echo base_url();?>lab/dti/get_uom_select2",
                        data : function(params){

                            return{
                                prod:params.term,
                            };
                        }, 
                        processResults:function(data){
                            var results = [];
                            $.each(data, function(index,item){
                                results.push({
                                    id:item.short,
                                    text:item.short
                                });
                            });
                            return {
                                results:results
                            };
                        },
                        error: function (xhr, ajaxOptions, thrownError){
                            //alert('Error data');
                            console.log(xhr.responseText);
                        }
                }
            });

        }

    };
 
 
    //---- START DYEING STUFF / AUX  --- //
  // add new data
  $(document).on("click", ".add-new", function(){

      var type = $(this).attr('type_obat');
      if(type =='DYE'){
        id_table = "#table_dyest";
        link_get_list   = "get_list_dye";
        link_get_data   = "get_data_dye";
      }else{
        id_table = "#table_aux";
        link_get_list   = "get_list_aux";
        link_get_data   = "get_data_aux";
      }

      // hidden tambah data
      $(".add-new").hide();
      var index = $(id_table+" tbody tr:last-child").index();
      var row   ='<tr class="">'
                + '<td></td>'
                + '<td class="text-wrap"><select type="text" class="form-control input-sm  width-150 prod" name="Product" id="product"></select><input type="hidden" class="form-control input-sm prodhidd" name="prodhidd" id="prodhidd"></td>'
                + '<td class=""><input type="text" class="form-control input-sm width-80 qty" name="Qty" id="qty"  onkeyup="validAngka(this)" ></td>'
                + '<td class=""><select type="text" class="form-control input-sm width-60 uom" name="Uom" id="uom"><option value=""></option><?php foreach($uom as $row){?><option value="<?php echo $row->short; ?>"><?php echo $row->short;?></option>"<?php }?></select></td>'
                + '<td class="text-wrap"><textarea type="text" class="form-control input-sm width-80" name="reff" id="reff"></textarea></td>'
                + '<td align="center"><button type="button" class="btn btn-primary btn-xs add width-btn" title="Simpan" data-toggle="tooltip" type_obat="'+type+'">Simpan</button><a class="edit" title="Edit" data-toggle="tooltip"><i class="fa fa-edit"></i></a><button type="button" class="btn btn-danger btn-xs batal width-btn" title="Batal" data-toggle="tooltip">Batal</button></td>'
                + '<td></td>'
                + '</tr>';

      $(id_table+' tbody').append(row);
      $(id_table+" tbody tr").eq(index + 1).find(".add, .edit").toggle();
      $('[data-toggle="tooltip"]').tooltip();

      //select 2 product
      $('.prod').select2({
          ajax:{
                dataType: 'JSON',
                type : "POST",
                url : "<?php echo base_url();?>lab/dti/"+link_get_list,
                //delay : 250,
                data : function(params){
                  return{
                    prod:params.term
                  };
                }, 
                processResults:function(data){
                  var results = [];

                  $.each(data, function(index,item){
                      results.push({
                          id:item.kode_produk,
                          text:'['+item.kode_produk+'] '+item.nama_produk
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

        $(".prod").change(function(){
            $.ajax({
                  dataType: "JSON",
                  //url : '<?php echo site_url('lab/dti/get_data_dye') ?>',
                  url : "<?php echo base_url();?>lab/dti/"+link_get_data,
                  type: "POST",
                  data: {kode_produk: $(this).parents("tr").find('#product').val() },
                  success: function(data){
                    $(id_table+' tbody tr .uom').val(data.uom);
                    $(id_table+' tbody tr .prodhidd').val(data.nama_produk);
                  },
                  error: function (xhr, ajaxOptions, thrownError){
                    //alert('Error data');
                    alert(xhr.responseText);
                  }
            });
        });

  });


  //batal add row on batal button click
  $(document).on("click", ".batal", function(){
    var input = $(this).parents("tr").find('.prod');
    input.each(function(){
      $(this).parents("td").html($(this).val());
    }); 
      
    $(this).parents("tr").remove();
    $(".add-new").show();
  }); 

 

  // btn cancel edit
  $(document).on("click", ".cancel", function(){
    var id_varian  = $(this).attr('varian');
    $(".add-new").show();
    $("#foot").load(location.href + " #foot");
    $("#status_head").load(location.href + " #status_head");
    reloadItems(id_varian);
    /*
    var type = $(this).attr('type_obat');
      if(type =='DYE'){
        $("#table_dyest tbody").load(location.href + " #table_dyest tbody");
      }else{
        $("#table_aux tbody").load(location.href + " #table_aux tbody");
      }
    */
  });


  //simpan / edit row data
  $(document).on("click", ".add", function(){
      
      var varian_active  =  $('ul.nav li.active a[data-toggle="tab"]').attr('varian');
      var empty = false;
      var input = $(this).parents("tr").find('input[type="text"]');

      var empty2 = false;
      var select = $(this).parents("tr").find('select[type="text"]');

      //validasi tidak boleh kosong hanya select product saja
      select.each(function(){
        if(!$(this).val() && $(this).attr('name')=='Product' ){
          alert_notify('fa fa-warning',$(this).attr('name')+ ' Harus Diisi !','danger',function(){});
          empty2 = true;
        }

      });

      // validasi untuk inputan textbox
      input.each(function(){
        if(!$(this).val() && $(this).attr('name')!='reff'){
          alert_notify('fa fa-warning',$(this).attr('name')+ ' Harus Diisi !','danger',function(){});
          empty = true;
        }
      });

      if(!empty && !empty2){
        var tipe_obat     = $(this).attr('type_obat');
        var id_warna      = "<?php echo $color->id; ?>";
        var kode_produk   = $(this).parents("tr").find("#product").val();
        var kode_produk_before   = $(this).parents("tr").find("#prodBefore").val();
        var produk        = $(this).parents("tr").find("#prodhidd").val();
        var qty           = $(this).parents("tr").find("#qty").val();
        var uom           = $(this).parents("tr").find("#uom").val();
        var reff          = $(this).parents("tr").find("#reff").val();
        var row_order     = $(this).parents("tr").find("#row_order").val();
        $.ajax({
          dataType: "JSON",
          url : '<?php echo site_url('lab/dti/simpan_detail_dti') ?>',
          type: "POST",
          data: {id_warna   : id_warna, 
                kode_produk : kode_produk,
                kode_produk_before : kode_produk_before,
                produk      : produk,
                qty         : qty,
                uom         : uom,
                reff        : reff,
                tipe_obat   : tipe_obat,
                row_order   : row_order,
                id_varian: varian_active  },
          success: function(data){
            if(data.sesi=='habis'){
                //alert jika session habis
                alert_modal_warning(data.message);
                window.location.replace('../index');
            }else if(data.status == 'failed'){
                alert_notify(data.icon,data.message,data.type,function(){});
            }else{
                $(".add-new").show();                   
                $("#foot").load(location.href + " #foot");
                $("#status_head").load(location.href + " #status_head");
                alert_notify(data.icon,data.message,data.type,function(){});
                reloadItems(varian_active);
             
            }
          },
          error: function (xhr, ajaxOptions, thrownError){
            alert('Error data');
            alert(xhr.responseText);
          }
        });
      }   
    });

    /*
    // Edit row on edit button click di OFF kan
    $(document).on("click", ".edit", function(){  

        var type = $(this).attr('type_obat');
        if(type =='DYE'){
          id_table = "#table_dyest";
          link_get_list   = "get_list_dye";
          link_get_data   = "get_data_dye";
          table    = "dye";
        }else{
          id_table = "#table_aux";
          link_get_list   = "get_list_aux";
          link_get_data   = "get_data_aux";
          table    = "aux";
        }

        $(this).parents("tr").find("td[data-content='edit']").each(function(){
          if($(this).attr('data-id')=="row_order"){
            $(this).html('<input type="hidden"  class="form-control" value="' + htmlentities_script($(this).attr('data-isi')) + '" id="'+ $(this).attr('data-id') +'"> ');
            row_order = $(this).attr('data-isi');

          }else if($(this).attr('data-id')=="kode_produk"){

            var kode_produk = $(this).attr('data-isi');
            var nama_produk = $(this).attr('data-isi2');

            class_sel2_prod   = table+'sel2_prod'+row_order;
            class_nama_produk = table+'nama_produk'+row_order;
            kode_prd_before   = table+'kode_prod'+row_order;

            $(this).html('<select type="text"  class="form-control input-sm '+class_sel2_prod+' " id="product" name="Product" ></select> ' + '<input type="hidden"  class="form-control '+class_nama_produk+' " value="' + htmlentities_script($(this).attr('data-isi2')) + '" id="'+ $(this).attr('data-id2') +'"> ' + '<input type="hidden"  class="form-control '+kode_prd_before+' " value="' + htmlentities_script($(this).attr('data-isi')) + '" id="prodBefore"> ');

            var $newOption = $("<option></option>").val(kode_produk).text(nama_produk);
            $('.sel2_bom'+row_order).empty().append($newOption).trigger('change');

            custom_nama = '['+kode_produk+'] '+nama_produk;
            $newOption = new Option(custom_nama, kode_produk, true, true);
            $('.'+class_sel2_prod).append($newOption).trigger('change');

            $('.'+class_sel2_prod).select2({
              ajax:{
                    dataType: 'JSON',
                    type : "POST",
                    url : "<?php echo base_url();?>lab/dti/"+link_get_list,
                    //delay : 250,
                    data : function(params){
                      return{
                        prod:params.term
                      };
                    }, 
                    processResults:function(data){
                      var results = [];

                      $.each(data, function(index,item){
                          results.push({
                              id:item.kode_produk,
                              text:'['+item.kode_produk+'] '+item.nama_produk
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

            $("."+class_sel2_prod).change(function(){
            $.ajax({
                  dataType: "JSON",
                  url : "<?php echo base_url();?>lab/dti/"+link_get_data,
                  type: "POST",
                  data: {kode_produk: $(this).parents("tr").find('#product').val() },
                  success: function(data){
                    $(id_table+' tbody tr .'+class_nama_produk).val(data.nama_produk);
                    $(id_table+' tbody tr .uom'+row_order).val(data.uom);

                  },
                  error: function (xhr, ajaxOptions, thrownError){
                    alert('Error data');
                    alert(xhr.responseText);
                  }
            });
        });
                     
          }else if($(this).attr('data-id')=='qty'){
            $(this).html('<input type="text"  class="form-control input-sm width-80" value="'+ ($(this).attr('data-isi')) +'" id="'+ $(this).attr('data-id') +'" name="'+ $(this).attr('data-id') +'" onkeyup="validAngka(this)"> ');
          }else if($(this).attr('data-id')=='uom'){

            var value_option  = $(this).attr('data-isi');
            var uom           = $(this).attr('data-id')+row_order;
            
            $(this).html('<select type="text" class="form-control input-sm width-60 '+uom+'"  id="'+ $(this).attr('data-id') +'" name="'+ $(this).attr('data-id') +'"></select>');

            var $option = $("<option selected></option>").val(value_option).text(value_option);
            $(id_table+' tbody tr .uom'+row_order).append($option).trigger('change');

            $(id_table+' tbody tr .uom'+row_order).append('<?php foreach($uom as $row){?><option value="<?php echo $row->short; ?>"><?php echo $row->short;?></option>"<?php }?>').trigger('change');
       
          }else if($(this).attr('data-id')=="reff"){
            $(this).html('<textarea type="text" onkeyup="textAreaAdjust(this)" class="form-control width-80 input-sm" id="'+ $(this).attr('data-id') +'" name="'+ $(this).attr('data-id') +'">'+ htmlentities_script($(this).attr('data-isi')) +'</textarea>');
          }

        });  

        $(this).parents("tr").find(".add, .edit").toggle();
        $(this).parents("tr").find(".cancel, .delete").toggle();
        $(".add-new").hide();
     
    });
    */

  //---- FINISH DYEING STUFF / AUX --- //


  //klik button simpan
  $("#btn-simpan").unbind( "click" );
  $('#btn-simpan').click(function(){

    var id   = '<?php echo $color->id; ?>';
    var id_varian = $('#tab-list li.active a[data-toggle="tab"] ').attr('varian');
    var arr   = new Array();
    var arr2  = new Array();
    var empty = false;
    var empty2= false;
    var empty3= false;

    $("#table_dyest tbody[id='tbody_dye'] .kode_produk").each(function(index, element) {
					if ($(element).val()!=="" && $(element).val()!==null) {
						arr.push({
							//0 : no++,
							kode_produk :$(element).val(),
							nama_produk :$(element).parents("tr").find("#nama_produk").val(),
							qty 		    :$(element).parents("tr").find("#qty").val(),
							uom 		    :$(element).parents("tr").find("#uom").val(),
							reff_note 	:$(element).parents("tr").find("#reff").val(),
						});
            $(this).parents('td').find('span span.selection span.select2-selection').removeClass('error'); 
					}else{
            empty = true;
            $(this).parents('td').find('span span.selection span.select2-selection').addClass('error'); 
            alert_notify('fa fa-warning','Product Harus Diisi !','danger',function(){});
          }
		}); 

    $("#table_dyest tbody[id='tbody_dye'] .qty").each(function(index, element) {
					if ($(element).val()=="" ) {
            empty2 = true;
            alert_notify('fa fa-warning','Qty Harus Diisi !','danger',function(){});
            $(this).addClass('error'); 
					}else{
            $(this).removeClass('error'); 
          }
		}); 

    $("#table_dyest tbody[id='tbody_dye'] .uom").each(function(index, element) {
					if ($(element).val()=="" || $(element).val()==null ) {
            empty3 = true;
            alert_notify('fa fa-warning','uom Harus Diisi !','danger',function(){});
            $(this).parents('td').find('span span.selection span.select2-selection').addClass('error'); 
					}else{
            $(this).parents('td').find('span span.selection span.select2-selection').removeClass('error'); 
          }
		}); 

    $("#table_aux tbody[id='tbody_aux'] .kode_produk").each(function(index, element) {
					if ($(element).val()!=="" && $(element).val()!==null ) {
						arr2.push({
							//0 : no++,
							kode_produk :$(element).val(),
							nama_produk :$(element).parents("tr").find("#nama_produk").val(),
							qty 		    :$(element).parents("tr").find("#qty").val(),
							uom 		    :$(element).parents("tr").find("#uom").val(),
							reff_note 	:$(element).parents("tr").find("#reff").val(),
						});
            $(this).parents('td').find('span span.selection span.select2-selection').removeClass('error'); 
					}else{
            empty = true;
            alert_notify('fa fa-warning','Product Harus Diisi !','danger',function(){});
            $(this).parents('td').find('span span.selection span.select2-selection').addClass('error'); 
          }
		});
    

    $("#table_aux tbody[id='tbody_aux'] .qty").each(function(index, element) {
					if ($(element).val()=="" ) {
            empty2 = true;
            alert_notify('fa fa-warning','Qty Harus Diisi !','danger',function(){});
            $(this).addClass('error'); 
					}else{
            $(this).removeClass('error'); 
          }
		}); 

    $("#table_aux tbody[id='tbody_aux'] .uom").each(function(index, element) {
					if ($(element).val()=="" || $(element).val()==null ) {
            empty3 = true;
            alert_notify('fa fa-warning','uom Harus Diisi !','danger',function(){});
            $(this).parents('td').find('span span.selection span.select2-selection').addClass('error'); 
					}else{
            $(this).parents('td').find('span span.selection span.select2-selection').removeClass('error'); 
          }
		}); 
   

    if(!empty && !empty2 && !empty3 ){
    
      $('#btn-simpan').button('loading');
      please_wait(function(){});
      $.ajax({
         type: "POST",
         dataType: "json",
         url :'<?php echo base_url('lab/dti/simpan')?>',
         beforeSend: function(e) {
            if(e && e.overrideMimeType) {
                e.overrideMimeType("application/json;charset=UTF-8");
            }
         },
         data: {tanggal    : $('#tgl').val(),
                id         : id,
                id_varian  : id_varian,
                note       : $('#note').val(),
                warna      : $('#warna').val(),
                kode_warna : $('#kode_warna').val(),
                sales_group : $('#sales_group').val(),
                note_varian : $('#note_varian').val(),
                status     : 'edit',
                arr_dye    : JSON.stringify(arr),
                arr_aux    : JSON.stringify(arr2),

          },success: function(data){
            if(data.sesi == "habis"){
              //alert jika session habis
              alert_modal_warning(data.message);
              window.location.replace('../index');
            }else if(data.status == "failed"){
              //jika ada form belum keisi
              unblockUI( function() {
                setTimeout(function() { alert_notify(data.icon,data.message,data.type,function(){}); }, 1000);
              });
              document.getElementById(data.field).focus();//focus ke field yang belum keisi
            }else{
              //jika berhasil disimpan/diubah
              unblockUI( function() {
                  setTimeout(function() { alert_notify(data.icon,data.message,data.type,function(){}); }, 1000);
              });
              $("#foot").load(location.href + " #foot");
            }
            $("#status_head").load(location.href + " #status_head");
            $("#status_bar").load(location.href + " #status_bar");
            $('#btn-simpan').button('reset');
            reloadForm(id_varian)
          },error: function (xhr, ajaxOptions, thrownError) { 
            alert(xhr.responseText);
            setTimeout($.unblockUI, 1000); 
            $('#btn-simpan').button('reset');
            unblockUI( function(){});
          }
      });
      
    }
  });


  //klik button generate
  $("#btn-generate").unbind( "click" );
  $('#btn-generate').click(function(){
    var id_warna   = '<?php echo $color->id; ?>';
    $('#btn-generate').button('loading');
    please_wait(function(){});
      $.ajax({
         type: "POST",
         dataType: "json",
         url :'<?php echo base_url('lab/dti/generate')?>',
         beforeSend: function(e) {
            if(e && e.overrideMimeType) {
                e.overrideMimeType("application/json;charset=UTF-8");
            }
         },
         data: {id_warna : id_warna
          },success: function(data){
            if(data.sesi == "habis"){
              //alert jika session habis
              alert_modal_warning(data.message);
              window.location.replace('../index');
            }else if(data.status == "failed"){
              //jika ada form belum keisi
              unblockUI( function() {
                setTimeout(function() { alert_notify(data.icon,data.message,data.type,function(){}); }, 1000);
              });
             $('#btn-generate').button('reset');
              document.getElementById(data.field).focus();//focus ke field yang belum keisi
            }else{
              //jika berhasil disimpan/diubah
              unblockUI( function() {
                  setTimeout(function() { alert_notify(data.icon,data.message,data.type,function(){}); }, 1000);
              });
              $("#foot").load(location.href + " #foot");
              $("#status_bar").load(location.href + " #status_bar");
            }
            $("#status_head").load(location.href + " #status_head");
            $('#btn-generate').button('reset');
          },error: function (xhr, ajaxOptions, thrownError) { 
            alert(xhr.responseText);
            $('#btn-generate').button('reset');
            unblockUI( function(){});
          }
      });
    });

  //hapus dyeing stuff and aux
  function hapus(kode_produk,nama_produk,id_warna,type_obat,row_order,id_warna_varian)
  {
      var baseUrl = '<?php echo base_url(); ?>';
        bootbox.dialog({
        message: "Apakah Anda ingin menghapus data ?",
        title: "<i class='glyphicon glyphicon-trash'></i> Delete !",
        buttons: {
          danger: {
              label    : "Yes ",
              className: "btn-primary btn-sm",
              callback : function() {
                    $.ajax({
                          type: 'POST',
                          dataType: "json",
                          url : "<?php echo site_url('lab/dti/hapus_dye_aux')?>",
                          data : {kode_produk:kode_produk, nama_produk:nama_produk, id_warna:id_warna, type_obat:type_obat, row_order:row_order, id_warna_varian:id_warna_varian },
                    })
                    .done(function(response){
                      if(response.sesi == 'habis'){
                        alert_modal_warning(response.message);
                         window.location.replace(baseUrl);//replace ke halaman login
                      }else{
                        $("#foot").load(location.href + " #foot");              
                        $("#status_head").load(location.href + " #status_head");     
                        alert_notify(response.icon,response.message,response.type,function(){});
                        reloadItems(id_warna_varian)
                      }
                    })
                    .fail(function(){
                      bootbox.alert('Error....');
                    })
              }
          },
          success: {
                label    : "No",
                className: "btn-default  btn-sm",
                callback : function() {
                $('.bootbox').modal('hide');
                }
          }
        }
        });
      return false;
  }

  // duplicate DTI
  $(document).on('click','#btn-duplicate',function(e){
        e.preventDefault();
        var id_warna   = '<?php echo $color->id; ?>';
        var duplicate  = 'true';
        var varian_active  =  $('ul.nav li.active a[data-toggle="tab"]').attr('varian');

        if(id_warna == ""){
          alert_modal_warning('Id Warna Kosong !');
        }else{
          var url = '<?php echo base_url() ?>lab/dti/add';
          window.open(url+'?id_warna='+ id_warna+'&&duplicate='+duplicate+'&&id_varian='+varian_active,'_blank');
        }
  });

  // print DTI
  $(document).on('click','#btn-print',function(e){
        e.preventDefault();
        var id_warna   = '<?php echo $color->id; ?>';
        var status     = $("#status").val();
        var varian_active  =  $('ul.nav li.active a[data-toggle="tab"]').attr('varian');

        if(id_warna == ""){
          alert_modal_warning('Id Warna Kosong !');
        }else if(status == 'cancel' || status == 'draft'){
          alert_modal_warning('Print DTI Hanya bisa di Print saat statusnya Ready, Requested, Done! ');
        }else{
          var url = '<?php echo base_url() ?>lab/dti/print_dti';
          window.open(url+'?id_warna='+ id_warna+'&id_varian='+ varian_active,'_blank');
        }
  });

  function enter(e,table){
    if(e.keyCode === 13){
	        e.preventDefault(); 
	        tambah_baris(false,table,'','','','',''); //panggil fungsi tambah baris
	    }
	}


  $(document).on('click','#btn-cancel', function(e){
    var status = $('#status').val();
    if(status == 'cancel'){
      alert_modal_warning('DTI sudah dibatalkan !');
    }else{
      var id_warna   = '<?php echo $color->id; ?>';
      var dialog = bootbox.dialog({
          title : "<font color='red'><i class='fa fa-warning'></i></font> Warning !",
          message: "<p>Apakah anda yakin ingin membatalkan DTI ?</p>",
          size: 'medium',
          buttons: {
            ok: {
              label: "Yes",
              className: 'btn-primary btn-sm',
              callback: function(){
                    $("#btn-cancel").button('loading');
                    $.ajax({
                          type: 'POST',
                          dataType: "json",
                          url : "<?php echo site_url('lab/dti/cancel_dti')?>",
                          data : {id_warna:id_warna},
                    })
                    .done(function(response){
                      if(response.sesi == 'habis'){
                        alert_modal_warning(response.message);
                        window.location.replace(baseUrl);//replace ke halaman login
                      }else if(response.status =='failed'){
                        alert_modal_warning(response.message);
                      }else{
                        $("#foot").load(location.href + " #foot");              
                        $("#status_head").load(location.href + " #status_head");     
                        $("#status_bar").load(location.href + " #status_bar");     
                        $("#btn_tabs").load(location.href + " #btn_tabs");     
                        alert_notify(response.icon,response.message,response.type,function(){});
                        $("#btn-generate").hide();
                        $("#btn-duplicate").hide();
                        $("#btn-edit").hide();
                        $("#btn-cancel").hide();
                        $("#btn-active").show();
                      }
                      $("#btn-cancel").button('reset');
                    })
              }
            },
            cancel: {
                label: "No",
                className: 'btn-default btn-sm',
                callback: function(){
                }
            },
          }
      });
    }
  });


  $(document).on('click','#btn-active', function(e){
    var status = $('#status').val();
    if(status != 'cancel'){
      alert_modal_warning('DTI sudah di aktifkan !');
    }else{
      var id_warna   = '<?php echo $color->id; ?>';
      var dialog = bootbox.dialog({
          title : "<font color='red'><i class='fa fa-warning'></i></font> Warning !",
          message: "<p>Apakah anda yakin ingin mengaktifkan kembali DTI ?</p>",
          size: 'medium',
          buttons: {
            ok: {
              label: "Yes",
              className: 'btn-primary btn-sm',
              callback: function(){
                    $("#btn-active").button('loading');
                    $.ajax({
                          type: 'POST',
                          dataType: "json",
                          url : "<?php echo site_url('lab/dti/active_dti')?>",
                          data : {id_warna:id_warna},
                    })
                    .done(function(response){
                      if(response.sesi == 'habis'){
                        alert_modal_warning(response.message);
                        window.location.replace(baseUrl);//replace ke halaman login
                      }else if(response.status =='failed'){
                        alert_modal_warning(response.message);
                      }else{
                        $("#foot").load(location.href + " #foot");              
                        $("#status_head").load(location.href + " #status_head");     
                        $("#status_bar").load(location.href + " #status_bar"); 
                        $("#btn_tabs").load(location.href + " #btn_tabs");     
                        alert_notify(response.icon,response.message,response.type,function(){});
                        $("#btn-generate").show();
                        $("#btn-duplicate").show();
                        $("#btn-edit").show();
                        $("#btn-cancel").show();
                        $("#btn-active").hide();
                      }
                      $("#btn-active").button('reset');
                    })
              }
            },
            cancel: {
                label: "No",
                className: 'btn-default btn-sm',
                callback: function(){
                }
            },
          }
      });
    }
  });


</script>


</body>
</html>
