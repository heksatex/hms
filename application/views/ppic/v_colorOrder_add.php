
<!DOCTYPE html>
<html>
<head>
  <?php $this->load->view("admin/_partials/head.php") ?>
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
     <?php $this->load->view("admin/_partials/statusbar.php") ?>
    </section>

    <!-- Main content -->
    <section class="content">

      <!--  box content -->
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title">Form Tambah</h3>
          
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
                    <input type="text" class="form-control input-sm" name="kode_co" id="kode_co"  readonly="readonly" />
                    <input type="hidden" class="form-control input-sm" name="kode_co_en" id="kode_co_en"  readonly="readonly" />
                  </div>                                    
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Tanggal </label></div>
                  <div class="col-xs-8 col-md-8">
                    <input type='text' class="form-control input-sm" name="tgl" id="tgl" readonly="readonly" value="<?php echo date('Y-m-d H:i:s')?>"  />
                  </div>                                    
                </div>

                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Route</label></div>
                  <div class="col-xs-8">
                    <select class="form-control input-sm" name="route" id="route" />
                     <?php foreach ($route as $row) {?>
                       <option value="<?php echo $row->kode;?>"><?php echo $row->nama;?></option>
                     <?php }?>
                    </select>
                  </div>                                    
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Notes </label></div>
                  <div class="col-xs-8">
                    <textarea type="text" class="form-control input-sm" name="note" id="note"></textarea>
                  </div>                                    
                </div>

              </div>

              <div class="col-md-6">

                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Sales Contract</label></div>
                  <div class="col-xs-8">
                    <div class='input-group'>
                      <input type="text" class="form-control input-sm" name="kode_sc" id="kode_sc" readonly="readonly" />
                       <span class="input-group-addon">
                          <a href="#" class="sc"><span class="glyphicon  glyphicon-share"></span></a>
                      </span>
                    </div>
                  </div>                                    
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Buyer Code </label></div>
                  <div class="col-xs-8">
                    <input type="text" class="form-control input-sm" name="buyer_code" id="buyer_code" readonly="readonly" />
                  </div>                                    
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Handling</label></div>
                  <div class="col-xs-8">
                    <select class="form-control input-sm" name="handling" id="handling" />
                      <?php foreach ($handling as $row) {?>
                         <option><?php echo $row->nama;?></option>
                      <?php }?>
                    </select>
                  </div>                                    
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Lebar Jadi</label></div>
                  <div class="col-xs-8">
                    <input type="text" class="form-control input-sm" name="lbr_jadi" id="lbr_jadi" />
                  </div>                                    
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Tanggal Kirim / Surat Jalan </label></div>
                  <div class="col-xs-8">
                    <div class='input-group date' id='tanggal_sj'>
                      <input type='text' class="form-control input-sm" name="tgl_sj" id="tgl_sj" readonly="readonly"/>
                      <span class="input-group-addon">
                          <span class="glyphicon glyphicon-calendar"></span>
                      </span>
                  </div>
                  </div>                                    
                </div>
               
              </div>
           
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
   <?php $this->load->view("admin/_partials/footer.php") ?>
  </footer>
   <!-- Load Partial Modal -->
   <?php $this->load->view("admin/_partials/modal.php") ?>

</div>

<?php $this->load->view("admin/_partials/js.php") ?>

<script type="text/javascript">

  //set tgl buat
  var datenow=new Date();  
  datenow.setMonth(datenow.getMonth());
  $('#tanggal').datetimepicker({
      defaultDate: datenow,
      format : 'YYYY-MM-DD HH:mm:ss',
      ignoreReadonly: true,
  });

  //set tgl kirim
  var datesampai=new Date();
  datesampai.setDate(datesampai.getDate() + 7); 
  $('#tanggal_sj').datetimepicker({
      defaultDate: datesampai,
      format : 'YYYY-MM-DD HH:mm:ss',
      ignoreReadonly: true,
      widgetPositioning:{
                          horizontal: 'auto',
                          vertical: 'bottom'
                        }
  });

  // modal view sale Contract
  $(document).on('click','.sc',function(e){
      e.preventDefault();
      $("#view_data").modal('show');
      $(".view_body").html('<center><h5><img src="<?php echo base_url('dist/img/ajax-loader.gif') ?> "/><br>Please Wait...</h5></center>');
      $('.modal-title').text('View Sales Contract');
        $.post('<?php echo site_url()?>ppic/colororder/list_sales_contract',
          function(html){
            setTimeout(function() {$(".view_body").html(html);  },1000);
          }   
       );
  });

//pilih data pada modal view sale contract
  $(document).on('click', '.pilih', function (e) {
      document.getElementById("kode_sc").value = $(this).attr('sale_contract');
      document.getElementById("buyer_code").value = $(this).attr('buyer-code');
      $('#view_data').modal('hide');
  });

  //klik button simpan
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
              window.location.replace('index');
            }else if(data.status == "failed")
            {
                //jika ada form belum keiisi
                unblockUI( function() {
                  setTimeout(function() { alert_notify(data.icon,data.message,data.type); }, 1000);
                });
                 document.getElementById(data.field).focus();
            }
            else
            {
             //jika berhasil disimpan/diubah
              $('#kode_co').val(data.isi);
              $('#kode_co_en').val(data.kode_encrypt);
              unblockUI( function() {
                setTimeout(function() { 
                  alert_notify(data.icon,data.message,data.type, function(){
                   
                  window.location.replace('edit/'+$('#kode_co_en').val());
                },1000); 
                });
              });
            }
            $('#btn-simpan').button('reset');

          },error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.responseText);
            unblockUI( function(){});
            $('#btn-simpan').button('reset');

          }
      });
        window.setTimeout(function() {
       $(".alert").fadeTo(500, 0).slideUp(500, function(){
        $(this).remove(); });
      }, 3000);
    });


    //klik button Batal
    $('#btn-cancel').click(function(){
       if($('#kode_co').val() == ""){
        var message = 'Data Masih Kosong !';
        alert_modal_warning(message);
       }
    });

    //klik button Batal
    $('#btn-generate').click(function(){
       if($('#kode_co').val() == ""){
        var message = 'Data Masih Kosong !';
        alert_modal_warning(message);
       }
    });

    //klik button Batal
    $('#btn-print').click(function(){
       if($('#kode_co').val() == ""){
        var message = 'Data Masih Kosong !';
        alert_modal_warning(message);
       }
    });
   
</script>


</body>
</html>
