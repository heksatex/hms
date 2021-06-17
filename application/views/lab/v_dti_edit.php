
<!DOCTYPE html>
<html>
<head>
  <?php $this->load->view("admin/_partials/head.php") ?>
</head>

<body class="hold-transition skin-black fixed sidebar-mini" id="block-page">
<!-- Site wrapper -->
<div class="wrapper" >

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
          <h3 class="box-title"><b><?php echo $color->kode_warna;?></b></h3>
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
                  <div class="col-xs-4"><label>Tanggal </label></div>
                  <div class="col-xs-8 col-md-8">
                    <input type='text' class="form-control input-sm" name="tgl" id="tgl" readonly="readonly"  value="<?php echo $color->tanggal?>"/>
                  </div>                                    
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Warna </label></div>
                  <div class="col-xs-8">
                    <input type="text" class="form-control input-sm" name="warna" id="warna"  value="<?php echo $color->kode_warna?>"  readonly="readonly" />
                  </div>                                    
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Notes </label></div>
                  <div class="col-xs-8">
                    <textarea type="text" class="form-control input-sm" name="note" id="note"  ><?php echo $color->notes?></textarea>
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
                            <th class="style">reff Notes</th>
                            <th class="style"></th>
                          </tr>
                          <tbody>
                            <?php
                              $no = 1;
                              foreach ($dyest as $row) {
                            ?>
                              <tr class="num">
                                <td></td>
                                <td><?php echo $row->nama_produk?></td>
                                <td><?php echo $row->qty?></td>
                                <td><?php echo $row->uom?></td>
                                <td><?php echo $row->reff_note?></td>
                                <td class="no" align="center" >
                                 <a onclick="hapus('<?php  echo htmlentities($row->nama_produk) ?>','<?php  echo $row->kode_warna ?>', '<?php  echo $row->type_obat ?>', '<?php  echo $row->row_order ?>')"  href="javascript:void(0)"><i class="fa fa-trash" style="color: red"></i> 
                                 </a>
                                </td>
                              </tr>
                            <?php 
                              }
                            ?>
                          </tbody>
                          <tr>
                            <td colspan="8">
                               <a href="javascript:void(0)" class="add"><i class="fa fa-plus"></i> Tambah Data</a>
                            </td>
                          </tr>
                        </table>
                      </div>
                      <!-- Tabel Dye stuff -->

                      <!-- Tabel AUX  -->
                      <div class="col-md-6 table-responsive">
                        <table class="table table-condesed table-hover rlstable" width="100%" id="table_aux" >
                          <label>AUX</label>
                          <tr>
                            <th class="style no">No.</th>
                            <th class="style">Product</th>
                            <th class="style">qty (g/L)</th>
                            <th class="style">uom</th>
                            <th class="style">reff Notes</th>
                            <th class="style"></th>
                          </tr>
                          <tbody>
                            <?php
                              $no = 1;
                              foreach ($aux as $row) {
                            ?>
                              <tr class="num">
                                <td></td>
                                <td><?php echo $row->nama_produk?></td>
                                <td><?php echo $row->qty?></td>
                                <td><?php echo $row->uom?></td>
                                <td><?php echo $row->reff_note?></td>
                                <td class="no" align="center" >
                                 <a onclick="hapus('<?php  echo htmlentities($row->nama_produk) ?>','<?php  echo $row->kode_warna ?>', '<?php  echo $row->type_obat ?>', '<?php  echo $row->row_order ?>')"  href="javascript:void(0)"><i class="fa fa-trash" style="color: red"></i> 
                                 </a>
                                </td>
                              </tr>
                            <?php 
                              }
                            ?>
                          </tbody>
                          <tr>
                            <td colspan="8">
                               <a href="javascript:void(0)" class="add2"><i class="fa fa-plus"></i> Tambah Data</a>
                            </td>
                          </tr>
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
     <?php $this->load->view("admin/_partials/footer.php") ?>
    </div>
  </footer>

</div>

<?php $this->load->view("admin/_partials/js.php") ?>

<script type="text/javascript">

  //modal tambah data Dyeing Stuff
  $(".add").unbind( "click" );
  $(document).on('click','.add',function(e){
      e.preventDefault();
      $(".tambah_data").html('<center><h5><img src="<?php echo base_url('dist/img/ajax-loader.gif') ?> "/><br>Please Wait...</h5></center>');
      $("#tambah_data").modal({
          show: true,
          backdrop: 'static'
      });
      $('.modal-title').text('Tambah Data Dyeing Stuff');
        $.post('<?php echo site_url()?>lab/dti/tambah_dyeing_stuff_modal',
          {warna      : $('#warna').val(),tipe_obat     : 'DYE'},
          function(html){
            setTimeout(function() {$(".tambah_data").html(html);  },2000);
          }   
       );
  });

  $(".add2").unbind( "click" );
  $(document).on('click','.add2',function(e){
      e.preventDefault();
      $(".tambah_data").html('<center><h5><img src="<?php echo base_url('dist/img/ajax-loader.gif') ?> "/><br>Please Wait...</h5></center>');
      $("#tambah_data").modal({
          show: true,
          backdrop: 'static'
      });
      $('.modal-title').text('Tambah Data AUX');
        $.post('<?php echo site_url()?>lab/dti/tambah_aux_modal',
          {warna      : $('#warna').val(),tipe_obat     : 'AUX'},
          function(html){
            setTimeout(function() {$(".tambah_data").html(html);  },2000);
          }   
       );
  });


  //klik button simpan
  $("#btn-simpan").unbind( "click" );
  $('#btn-simpan').click(function(){
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
                warna      : $('#warna').val(),
                note       : $('#note').val(),
                status     : 'edit',

          },success: function(data){
            if(data.sesi == "habis"){
              //alert jika session habis
              alert_modal_warning(data.message);
              window.location.replace('../index');
            }else if(data.status == "failed"){
              //jika ada form belum keisi
              unblockUI( function() {
                setTimeout(function() { alert_notify(data.icon,data.message,data.type); }, 1000);
              });
              document.getElementById(data.field).focus();//focus ke field yang belum keisi
            }else{
              //jika berhasil disimpan/diubah
              unblockUI( function() {
                  setTimeout(function() { alert_notify(data.icon,data.message,data.type); }, 1000);
              });
              $("#foot").load(location.href + " #foot");
            }
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
         data: {warna      : $('#warna').val(),
          },success: function(data){
            if(data.sesi == "habis"){
              //alert jika session habis
              alert_modal_warning(data.message);
              window.location.replace('../index');
            }else if(data.status == "failed"){
              //jika ada form belum keisi
              unblockUI( function() {
                setTimeout(function() { alert_notify(data.icon,data.message,data.type); }, 1000);
              });
              document.getElementById(data.field).focus();//focus ke field yang belum keisi
            }else{
              //jika berhasil disimpan/diubah
              unblockUI( function() {
                  setTimeout(function() { alert_notify(data.icon,data.message,data.type); }, 1000);
              });
              $("#foot").load(location.href + " #foot");
              $("#status_bar").load(location.href + " #status_bar");
            }
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
  function hapus(nama_produk,warna,type_obat,row_order)
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
                          data : {nama_produk : nama_produk,warna : warna, type_obat:type_obat , row_order:row_order },
                    })
                    .done(function(response){
                      if(response.sesi == 'habis'){
                        alert_modal_warning(response.message);
                         window.location.replace(baseUrl);//replace ke halaman login
                      }else{
                        $("#table_aux").load(location.href + " #table_aux");
                        $("#table_dyest").load(location.href + " #table_dyest");
                        $("#foot").load(location.href + " #foot");                   
                        alert_notify(response.icon,response.message,response.type);
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
</script>


</body>
</html>
