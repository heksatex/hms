
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
                  <div class="col-xs-4"><label>Kode Adjustment </label></div>
                  <div class="col-xs-8">
                    <input type="text" class="form-control input-sm" name="kode_adjustment" id="kode_adjustment"  readonly="readonly" />                    
                  </div>                                    
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Create Date </label></div>
                  <div class="col-xs-8 col-md-8">
                     <input type='text' class="form-control input-sm " name="tgl_buat" id="tgl_buat" readonly="readonly" value="<?php echo date('Y-m-d H:i:s')?>"  />
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
                  <div class="col-xs-4"><label>Type Adjustment</label></div>
                  <div class="col-xs-8">
                    <select class="form-control input-sm" name="type_adjustment" id="type_adjustment" />
                    <option value="">Pilih Type</option>
                      <?php $disabled = '';
                            foreach ($type as $row) { 
                              if($row->view == '0'){
                                $disabled = "disabled";
                              }
                      ?>
                              <option value='<?php echo $row->id; ?>' <?php echo $disabled;?> ><?php echo $row->name_type;?></option>
                      <?php 
                              $disabled = '';
                            }
                      ?>
                    </select>
                  </div>                                    
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Lokasi Adjustment</label></div>
                  <div class="col-xs-8">
                    <select class="form-control input-sm" name="lokasi_adjustment" id="lokasi_adjustment" />
                    <option value="">Pilih Lokasi</option>
                      <?php foreach ($warehouse as $row) { ?>
                        <option value='<?php echo $row->kode; ?>'><?php echo $row->nama;?></option>
                      <?php }?>
                    </select>
                  </div>                                    
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Kode Lokasi </label></div>
                  <div class="col-xs-8">
                    <input type="text" class="form-control input-sm" name="kode_lokasi" id="kode_lokasi"  readonly="readonly" />                    
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
   <?php $this->load->view("admin/_partials/modal.php") ?>
   <?php $this->load->view("admin/_partials/footer.php") ?>
  </footer>

</div>

<?php $this->load->view("admin/_partials/js.php") ?>

<script type="text/javascript">

  //set schedule_date
  var datenow=new Date();  
  datenow.setMonth(datenow.getMonth());
  $('#tanggal').datetimepicker({
      defaultDate: datenow,
      format : 'YYYY-MM-DD HH:mm:ss',
      ignoreReadonly: true,
  });

  //pilih lokasi/departemen kode lokasi otomatis
  $("#lokasi_adjustment").change(function(){
      $.ajax({
            dataType: "JSON",
            url : '<?php echo site_url('warehouse/adjustment/get_stock_location_by_departemen') ?>',
            type: "POST",
            data: {kode_departemen   : $('#lokasi_adjustment').val()},
            success: function(data){
              $('#kode_lokasi').val(data.stock_location);
            },
            error: function (xhr, ajaxOptions, thrownError){
              alert('Error data');
              alert(xhr.responseText);
            }
      });
  });

  //klik button simpan
  $('#btn-simpan').click(function(){
    $('#btn-simpan').button('loading');
    please_wait(function(){});
    $.ajax({
       type: "POST",
       dataType: "json",
       url :'<?php echo base_url('warehouse/adjustment/simpan')?>',
       beforeSend: function(e) {
          if(e && e.overrideMimeType) {
              e.overrideMimeType("application/json;charset=UTF-8");
          }
       },
       data: {create_date       : $('#tgl_buat').val(),
              lokasi_adjustment : $('#lokasi_adjustment').val(),
              kode_lokasi       : $('#kode_lokasi').val(),
              note              : $('#note').val(),
              status            : 'draft',
              type_adjustment   : $("#type_adjustment").val(),

        },success: function(data){
          if(data.sesi == "habis"){
            //alert jika session habis
            alert_modal_warning(data.message);
            window.location.replace('index');
          }else if(data.status == "failed"){
              //jika ada form belum keiisi
              unblockUI( function() {
                setTimeout(function() { alert_notify(data.icon,data.message,data.type,function(){}); }, 1000);
              });
               document.getElementById(data.field).focus();
          }else{
           //jika berhasil disimpan/diubah
            //$('#kode_prod').val(data.isi);
            $('#kode_adjustment_encr').val(data.isi);
            unblockUI( function() {
              setTimeout(function() { 
                alert_notify(data.icon,data.message,data.type, function(){
                 
                window.location.replace('edit/'+data.isi);
              },1000); 
              });
            });
          }
          $('#btn-simpan').button('reset');

        },error: function (xhr, ajaxOptions, thrownError) {
          $('#btn-simpan').button('reset');
          alert(xhr.responseText);
          unblockUI( function(){});
        }
    });
      window.setTimeout(function() {
     $(".alert").fadeTo(500, 0).slideUp(500, function(){
      $(this).remove(); });
    }, 3000);
  });


    //klik button Batal
    $('#btn-cancel').click(function(){
       if($('#kode_prod').val() == ""){
        var message = 'Data Masih Kosong !';
        alert_modal_warning(message);
       }
    });


    //klik button print
    $('#btn-print').click(function(){
       if($('#kode_prod').val() == ""){
        var message = 'Data Masih Kosong !';
        alert_modal_warning(message);
       }
    });
   
</script>


</body>
</html>
