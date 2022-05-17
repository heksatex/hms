
<!DOCTYPE html>
<html>
<head>
  <?php $this->load->view("admin/_partials/head.php") ?>
   <!-- color picker -->
  <link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/colorpicker/bootstrap-colorpicker.min.css') ?>">
  <style type="text/css">
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

  </style>
</head>

<body class="hold-transition skin-black fixed sidebar-mini" id="block-page">
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
            <form class="form-horizontal">
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
                  <div class="col-xs-8">
                    <textarea type="text" class="form-control input-sm" name="note" id="note"  ><?php echo $color->notes?></textarea>
                  </div>                                    
                </div>
              </div>
              <div class="col-md-6">
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Kode Warna </label></div>
                  <div class="col-xs-8 col-md-8">
                    <div class="input-group my-colorpicker" id="my-colorpicker">
                      <input type="text" class="form-control input-sm" id="kode_warna" name="kode_warna"  value="<?php echo $color->kode_warna?>">
                      <span class="input-group-addon" id='groupColor'>
                           <i id="wstyle" ></i>
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
                      <li class="pointer" onclick="cek_history_dti('<?php echo $color->id ?>', '<?php echo $color->nama_warna?>')" data-toggle="tooltip" title="Lihat History DTI">
                        <span class="glyphicon glyphicon-cog"></span>            
                        <span class="glyphicon-class">History DTI</span>
                      </li>                        
                    </ul>
                  </div>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-12">
                <!-- Custom Tabs -->
                <div class="">
                  <ul class="nav nav-tabs">
                    <li class="active"><a href="#tab_1" data-toggle="tab">Details</a></li>
                  </ul>
                  <div class="tab-content"><br>
                    <div class="tab-pane active" id="tab_1">

                      <!-- Tabel Dye stuff  -->
                      <div class="col-md-6 table-responsive">
                        <table class="table table-condesed table-hover rlstable" width="100%" id="table_dyest" >
                          <label>Dyeing Stuff</label>
                          <tr>
                            <th class="style no">No.</th>
                            <th class="style">Product</th>
                            <th class="style">qty (%)</th>
                            <th class="style">uom</th>
                            <th class="style">reff notes</th>
                            <th class="style"></th>
                          </tr>
                          <tbody>
                            <?php
                              $no = 1;
                              foreach ($dyest as $row) {
                            ?>
                              <tr class="num">
                                <td></td>
                                <td><?php echo '['.$row->kode_produk.'] '.$row->nama_produk?></td>
                                <td><?php echo $row->qty?></td>
                                <td><?php echo $row->uom?></td>
                                <td><?php echo $row->reff_note?></td>
                                <td class="no" align="center" >
                                 <a onclick="hapus('<?php  echo htmlentities($row->nama_produk) ?>','<?php  echo $row->id_warna ?>', '<?php  echo $row->type_obat ?>', '<?php  echo $row->row_order ?>')"  href="javascript:void(0)"><i class="fa fa-trash" style="color: red"></i> 
                                 </a>
                                </td>
                              </tr>
                            <?php 
                              }
                            ?>
                          </tbody>
                          <tfoot>
                            <tr>
                              <td colspan="8">
                                 <a href="javascript:void(0)" class="add"><i class="fa fa-plus"></i> Tambah Data</a>
                              </td>
                            </tr>
                          </tfoot>
                        </table>
                      </div>
                      <!-- Tabel Dye stuff -->

                      <!-- Tabel AUX  -->
                      <div class="col-md-6 table-responsive">
                        <table class="table table-condesed table-hover rlstable" width="100%" id="table_aux" >
                          <label>Auxiliary</label>
                          <tr>
                            <th class="style no">No.</th>
                            <th class="style">Product</th>
                            <th class="style">qty (g/L)</th>
                            <th class="style">uom</th>
                            <th class="style">reff notes</th>
                            <th class="style"></th>
                          </tr>
                          <tbody>
                            <?php
                              $no = 1;
                              foreach ($aux as $row) {
                            ?>
                              <tr class="num">
                                <td></td>
                                <td><?php echo '['.$row->kode_produk.'] '.$row->nama_produk?></td>
                                <td><?php echo $row->qty?></td>
                                <td><?php echo $row->uom?></td>
                                <td><?php echo $row->reff_note?></td>
                                <td class="no" align="center" >
                                 <a onclick="hapus('<?php  echo htmlentities($row->nama_produk) ?>','<?php  echo $row->id_warna ?>', '<?php  echo $row->type_obat ?>', '<?php  echo $row->row_order ?>')"  href="javascript:void(0)"><i class="fa fa-trash" style="color: red"></i> 
                                 </a>
                                </td>
                              </tr>
                            <?php 
                              }
                            ?>
                          </tbody>
                          <tfoot>
                            <tr>
                              <td colspan="8">
                                 <a href="javascript:void(0)" class="add2"><i class="fa fa-plus"></i> Tambah Data</a>
                              </td>
                            </tr>
                          </tfoot>
                        </table>
                      </div>
                      <!-- Tabel AUX -->
                    </div>
                    <!-- /.tab-pane -->
              
                  </div>
                  <!-- /.tab-content -->
                </div>
                <!-- nav-tabs-custom -->
              </div>
              <!-- /.col -->
            </div>
           
          </form>
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

<?php $this->load->view("admin/_partials/js.php") ?>
<!-- color picker -->
<script src="<?php echo site_url('plugins/colorpicker/bootstrap-colorpicker.min.js') ?>"></script>

<script type="text/javascript">

  $(".my-colorpicker").colorpicker();

  $('.my-colorpicker').colorpicker().on('changeColor', function (e) {
      $('#content_colors')[0].style.backgroundColor = e.color.toHex();
  });


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

  // cek hisotry DTI
  function cek_history_dti(id_warna, nama_warna){
      $("#view_data").modal({
          show: true,
          backdrop: 'static'
      })
      $(".view_body").html('<center><h5><img src="<?php echo base_url('dist/img/ajax-loader.gif') ?> "/><br>Please Wait...</h5></center>');
      $('.modal-title').text('History DTI ( '+nama_warna+')' );
      $.post('<?php echo site_url()?>lab/dti/view_history_dti',
          {id_warna : id_warna},
          function(html){
            setTimeout(function() {$(".view_body").html(html);  },1000);
          }   
      );
    }


  //klik button simpan
  $("#btn-simpan").unbind( "click" );
  $('#btn-simpan').click(function(){
    $('#btn-simpan').button('loading');
    var id   = '<?php echo $color->id; ?>';
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
                note       : $('#note').val(),
                warna      : $('#warna').val(),
                kode_warna : $('#kode_warna').val(),
                status     : 'edit',

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
             $('#btn-simpan').button('reset');
          },error: function (xhr, ajaxOptions, thrownError) { 
            alert(xhr.responseText);
            setTimeout($.unblockUI, 1000); 
            $('#btn-simpan').button('reset');
            unblockUI( function(){});
          }
      });
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
            setTimeout($.unblockUI, 1000); 
            $('#btn-generate').button('reset');
            unblockUI( function(){});
          }
      });
    });

  //hapus dyeing stuff and aux
  function hapus(nama_produk,id_warna,type_obat,row_order)
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
                          data : {nama_produk:nama_produk, id_warna:id_warna, type_obat:type_obat, row_order:row_order },
                    })
                    .done(function(response){
                      if(response.sesi == 'habis'){
                        alert_modal_warning(response.message);
                         window.location.replace(baseUrl);//replace ke halaman login
                      }else{
                        $("#table_aux").load(location.href + " #table_aux");
                        $("#table_dyest").load(location.href + " #table_dyest");
                        $("#foot").load(location.href + " #foot");              
                        $("#status_head").load(location.href + " #status_head");     
                        alert_notify(response.icon,response.message,response.type,function(){});
                        parent.fadeOut('slow');
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

  //modal mode print
  $(document).on('click','#btn-print',function(e){
        e.preventDefault();
        var id_warna   = '<?php echo $color->id; ?>';
        var status     = $("#status").val();

        if(id_warna == ""){
          alert_modal_warning('Id Warna Kosong !');
        }else if(status == 'cancel' || status == 'draft'){
          alert_modal_warning('Print DTI Hanya bisa di Print saat statusnya Ready, Requested, Done! ');
        }else{
          var url = '<?php echo base_url() ?>lab/dti/print_dti';
          window.open(url+'?id_warna='+ id_warna,'_blank');
        }
  });


</script>


</body>
</html>
