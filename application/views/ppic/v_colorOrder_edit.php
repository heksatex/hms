
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
         $data['jen_status'] =  $colororder->status;
         $this->load->view("admin/_partials/statusbar.php", $data); 
       ?>
      </div>
    </section>

    <!-- Main content -->
    <section class="content">

      <!--  box content -->
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title"><b><?php echo $colororder->kode_co;?></b></h3>
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
                  <div class="col-xs-4"><label>Kode CO </label></div>
                  <div class="col-xs-8">
                    <input type="text" class="form-control input-sm" name="kode_co" id="kode_co"  readonly="readonly" value="<?php echo $colororder->kode_co;?>"  />
                  </div>                                    
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Tanggal </label></div>
                  <div class="col-xs-8 col-md-8">
                      <input type='text' class="form-control input-sm" name="tgl" id="tgl" readonly="readonly"  value="<?php echo $colororder->tanggal;?>"/>
                  </div>                                    
                </div>
                <?php 
                 $route =$this->m_colorOrder->get_nama_route_by_kode($colororder->route)->row_array();
                ?>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Route</label></div>
                  <div class="col-xs-8">
                    <input type="text" class="form-control input-sm"  readonly="readonly" value="<?php echo $route['nama']; ;?>"  />
                    <input type="hidden" class="form-control input-sm" name="route" id="route"  readonly="readonly" value="<?php echo $colororder->route;?>"  />
                  </div>                                    
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Notes </label></div>
                  <div class="col-xs-8">
                    <textarea type="text" class="form-control input-sm" name="note" id="note"><?php echo $colororder->notes;?></textarea>
                  </div>                                    
                </div>

              </div>

              <div class="col-md-6">

                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Sales Contract</label></div>
                  <div class="col-xs-8">
                      <input type="text" class="form-control input-sm" name="kode_sc" id="kode_sc" value="<?php echo $colororder->kode_sc;?>" readonly="readonly"/>
                  </div>                                    
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Buyer Code </label></div>
                  <div class="col-xs-8">
                    <input type="text" class="form-control input-sm" name="buyer_code" id="buyer_code" value="<?php echo $colororder->buyer_code;?>"  readonly="readonly"/>
                  </div>                                    
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Handling</label></div>
                  <div class="col-xs-8">
                    <input type="text" class="form-control input-sm" name="handling" id="handling"  readonly="readonly" value="<?php echo $colororder->handling;?>"  />
                  </div>                                    
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Lebar Jadi</label></div>
                  <div class="col-xs-8">
                    <input type="text" class="form-control input-sm" name="lbr_jadi" id="lbr_jadi"  value="<?php echo $colororder->lebar_jadi;?>" readonly="readonly"/>
                  </div>                                    
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Tanggal Kirim / Surat Jalan </label></div>
                  <div class="col-xs-8">
                    <input type='text' class="form-control input-sm" name="tgl_sj" id="tgl_sj" value="<?php echo $colororder->tanggal_sj;?>" readonly="readonly"/>
                  </div>                                    
                </div>
               
              </div>
           
            </div>
      
            <div class="row">
              <div class="col-md-12">
                <!-- Custom Tabs -->
                <div class="">
                  <ul class="nav nav-tabs">
                    <li class="active"><a href="#tab_1" data-toggle="tab">Color Details</a></li>
                  </ul>
                  <div class="tab-content"><br>
                    <div class="tab-pane active" id="tab_1">

                      <!-- Tabel  -->
                      <div class="col-md-12 table-responsive">
                        <table class="table table-condesed table-hover rlstable" width="100%" id="color_detail" >
                          <label></label>
                          <tr>
                            <th class="style no">No.</th>
                            <th class="style">Product</th>
                            <th class="style">Color</th>
                            <th class="style">Qty</th>
                            <th class="style">Uom</th>
                            <th class="style">Reff Notes</th>
                            <th class="style">Status</th>
                            <th class="style"></th>
                            <th class="style" width="50px"></th>

                          </tr>
                          <tbody>
                            <?php
                              $no = 1;
                              foreach ($detail as $row) {
                            ?>
                              <tr class="num">
                                <td></td>
                                <td><a href="javascript:void(0)" onclick="edit('<?php  echo $row->kode_co ?>', '<?php  echo $row->row_order ?>', '<?php  echo $row->status ?>')"><?php echo $row->nama_produk?></a></td>
                                <td><?php echo $row->kode_warna?></td>
                                <td><?php echo $row->qty?></td>
                                <td><?php echo $row->uom?></td>
                                <td><?php echo $row->reff_notes?></td>
                                <td><?php echo $row->status?></td>
                                <td class="no" align="center" >
                                 <a title="Hapus" data-toggle="tooltip" onclick="hapus('<?php  echo $row->kode_co ?>', '<?php  echo $row->row_order ?>', '<?php  echo $row->status ?>')"  href="javascript:void(0)"><i class="fa fa-trash" style="color: red"></i> 
                                 </a>
                                </td>
                                <td>
                                  <?php if($row->status == 'draft'){?>
                                    <button type="button" class="btn btn-primary btn-xs btn-generate" title="Generate" data-toggle="tooltip" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing...">Generate</button>
                                  <?php }?>
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
                      <!-- Tabel  -->
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

  //modal tambah data tabel color detail
  $(".add").unbind( "click" );
  $(document).on('click','.add',function(e){
      e.preventDefault();
      $(".tambah_data").html('<center><h5><img src="<?php echo base_url('dist/img/ajax-loader.gif') ?> "/><br>Please Wait...</h5></center>');
      //$("#tambah_data").modal('show');
      $("#tambah_data").modal({
          show: true,
          backdrop: 'static'
      });
      $('.modal-title').text('Color Details');
        $.post('<?php echo site_url()?>ppic/colororder/color_detail_modal',
          {kode_sc      : $('#kode_sc').val(),kode_co     : $('#kode_co').val(),       },
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
         url :'<?php echo base_url('ppic/colororder/simpan')?>',
         beforeSend: function(e) {
            if(e && e.overrideMimeType) {
                e.overrideMimeType("application/json;charset=UTF-8");
            }
         },
         data: {kode_co    : $('#kode_co').val(),
                kode_sc    : $('#kode_sc').val(),
                buyer_code : $('#buyer_code').val(),
                tgl_sj     : $('#tgl_sj').val(),
                note       : $('#note').val(),
                tgl        : $('#tgl').val(),
                route      : $('#route').val(),
                lbr_jadi   : $('#lbr_jadi').val(),
                handling   : $('#handling').val(),

          },success: function(data){
            if(data.sesi == "habis"){
              //alert jika session habis
              alert_modal_warning(data.message);
              window.location.replace('../index');

            }else if(data.status == "failed")
            {
              //jika ada form belum keisi
              unblockUI( function() {
                setTimeout(function() { alert_notify(data.icon,data.message,data.type); }, 1000);
              });
              document.getElementById(data.field).focus();//focus ke field yang belum keisi
             }
             else
             {
             //jika berhasil disimpan/diubah
              unblockUI( function() {
                  setTimeout(function() { alert_notify(data.icon,data.message,data.type); }, 1000);
              });
              $("#foot").load(location.href + " #foot");
              $('#kode_co').val(data.isi);
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


  //klik button Generate
    $("#btn-generate").unbind( "click" );
    $('#btn-generate').click(function(){
      $('#btn-generate').button('loading');
      please_wait(function(){});
      var baseUrl = '<?php echo base_url(); ?>';
      var message = 'Waktu Anda Telah Habis !';
      $.ajax({
         dataType: "json",
         type: "POST",
         url :'<?php echo base_url('ppic/colororder/generate')?>',
         data: {kode_co    : $('#kode_co').val()},
         success: function(data){
          if(data.sesi=='habis'){
            //alert jika session habis
            alert_modal_warning(data.message);
            window.location = baseUrl;//replace ke halaman login

          }else if(data.status=='kosong'){
            //jika color details masih kosong
            unblockUI(function(){
               alert_modal_warning(data.message);
            });
          }else{
            unblockUI( function() {
              setTimeout(function() { alert_notify(data.icon,data.message,data.type); }, 1000);
            });
            $("#status_bar").load(location.href + " #status_bar");
            $("#foot").load(location.href + " #foot");
            $("#color_detail").load(location.href + " #color_detail");

          }
         $('#btn-generate').button('reset');

         },error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.responseText);
            unblockUI( function(){});
            $('#btn-generate').button('reset');
          }
      });
    });


  //edit color details
  function edit(kode_co, row_order, status)
  {
      //$("#edit_data").modal('show');
      $("#edit_data").modal({
          show: true,
          backdrop: 'static'
      })
      $(".edit_data").html('<center><h5><img src="<?php echo base_url('dist/img/ajax-loader.gif') ?> "/><br>Please Wait...</h5></center>');
      $("#btn-ubah").attr("disabled", false);
      $('.modal-title').text('Edit Color Detail');
        $.post('<?php echo site_url()?>ppic/colororder/edit_color_detail_modal',
          {kode_co      : kode_co, row_order  : row_order, status : status},
          function(html){
            setTimeout(function() {$(".edit_data").html(html);  },2000);
          }   
       );
  }

  //hapus color details
  function hapus(kode_co, row_order,status)
  {
      var baseUrl = '<?php echo base_url(); ?>';
      if(status=="draft"){
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
                          url : "<?php echo site_url('ppic/colororder/delete_color_detail')?>",
                          data : {kode_co : kode_co, row_order:row_order },
                    })
                    .done(function(response){
                      if(response.status == 'failed'){
                          alert_modal_warning(response.message);
                          window.location = baseUrl;//replace ke halaman login
                      }else{
                        $("#color_detail").load(location.href + " #color_detail");
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
      }else{
        var message = 'Maaf, Data Tidak Bisa Dihapus !';
        alert_modal_warning(message);
      }
      return false;
  }


  //klik button Batal
    $('#btn-cancel').click(function(){
       if($('#kode_co').val() == ""){
        swal ( "Sorry" ,  "Data tidak Bisa Dibatalkan!" ,  "error" );
       }else{
        var message = 'Sorry, Data Tidak Bisa Dibatalkan !';
        //alert_modal_warning(message);
        please_wait();
        //loading_modal();
       }
    });


    function loading_modal(callback)
    {
      $(document).ready(function(){
        $('#loading_Modal').modal('show');
      });
      callback();
    }

</script>


</body>
</html>
