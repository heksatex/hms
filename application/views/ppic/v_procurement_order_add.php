
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
                  <div class="col-xs-4"><label>Procurement Order </label></div>
                  <div class="col-xs-8">
                    <input type="text" class="form-control input-sm" name="kode_proc" id="kode_proc"  readonly="readonly" />
                    <input type="hidden" class="form-control input-sm" name="kode_proc_en" id="kode_proc_en"  readonly="readonly" />
                  </div>                                    
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Create Date </label></div>
                  <div class="col-xs-8 col-md-8">
                     <input type='text' class="form-control input-sm" name="tgl_buat" id="tgl_buat" readonly="readonly" value="<?php echo date('Y-m-d H:i:s')?>"  />
                  </div>                                    
                </div>

                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Notes </label></div>
                  <div class="col-xs-8">
                    <textarea type="text" class="form-control input-sm" name="note" id="note"></textarea>
                  </div>                                    
                </div>

                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Type </label></div>
                  <div class="col-xs-8">
                    <div class="col-xs-12 col-sm-12 col-md-12">
                      <input type="radio" id="mto" name="type[]" value="mto">
                      <label for="mto">Make to Order</label>
                    </div>
                    <div class="col-xs-12 col-sm-3 col-md-12">
                      <input type="radio" id="mts" name="type[]" value="mts">
                      <label for="mst">Make to Stock </label>
                    </div>
                    <div class="col-xs-12 col-sm-12 col-md-12">
                      <input type="radio" id="pengiriman" name="type[]" value="pengiriman">
                      <label for="pengiriman">Pengiriman</label>
                    </div>
                  </div>                                    
                </div>

                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Sales Order </label></div>
                  <div class="col-xs-8">
                    <div class="col-xs-6 col-sm-4 col-md-4">
                      <input type="radio" id="sc_true" name="sc[]" value="yes">
                      <label for="yes">Yes</label>
                    </div>
                    <div class="col-xs-6 col-sm-4 col-md-4">
                      <input type="radio" id="sc_false" name="sc[]" value="no">
                      <label for="no">No</label>
                    </div>
                  </div>                                    
                </div>

              </div>

              <div class="col-md-6">

                <span id="show_type" style="display: none;">
                  <div class="col-md-12 col-xs-12">
                    <div class="col-xs-4"><label>Schedule Date </label></div>
                    <div class="col-xs-8 col-md-8">
                      <div class='input-group date' id='tanggal' >
                        <input type='text' class="form-control input-sm" name="tgl" id="tgl" readonly="readonly"  />
                        <span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar"></span>
                        </span>
                      </div>
                    </div>                                    
                  </div>

                  <span id="show_sc" style="display: inline;">
                    <div class="col-md-12 col-xs-12">
                      <div class="col-xs-4"><label>Production Order </label></div>
                      <div class="col-xs-8">
                        <div class='input-group'>
                          <input type="text" class="form-control input-sm" name="kode_prod" id="kode_prod" readonly="readonly" />
                          <span class="input-group-addon">
                              <a href="#" class="sc"><span class="glyphicon  glyphicon-share"></span></a>
                          </span>
                        </div>
                      </div>                                    
                    </div>
                    <div class="col-md-12 col-xs-12">
                      <div class="col-xs-4"><label>Sales Order </label></div>
                      <div class="col-xs-8 col-md-8">
                          <input type="text" class="form-control input-sm" name="sales_order" id="sales_order" readonly="readonly" />
                      </div>                                    
                    </div>
                  </span>

                  <div class="col-md-12 col-xs-12">
                    <div class="col-xs-4"><label id="label_dept">Departement Tujuan</label></div>
                    <div class="col-xs-8">
                      <select class="form-control input-sm" name="warehouse" id="warehouse" />
                      <option value="">Pilih Warehouse</option>
                        <?php foreach ($warehouse as $row) {?>
                          <option value='<?php echo $row->kode; ?>'><?php echo $row->nama;?></option>
                        <?php  }?>
                      </select>
                    </div>                                    
                  </div>
                  <div class="col-md-12 col-xs-12">
                    <div class="col-xs-4"><label>Priority </label></div>
                    <div class="col-xs-8">
                      <select class="form-control input-sm" name="priority" id="priority" />
                      <option value="">Pilih Priority</option>
                      <option>Not Urgent</option>
                      <option>Normal</option>
                      <option>Urgent</option>
                      <option>Very Urgent</option>
                      </select>
                    </div>                                    
                  </div>
                </div>
              </span>

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

  // modal view production order
  $(document).on('click','.sc',function(e){
      e.preventDefault();
      $("#view_data").modal('show');
      $(".view_body").html('<center><h5><img src="<?php echo base_url('dist/img/ajax-loader.gif') ?> "/><br>Please Wait...</h5></center>');
      $('.modal-title').text('View Production Order');
        $.post('<?php echo site_url()?>ppic/procurementorder/list_production_order_modal',
          function(html){
            setTimeout(function() {$(".view_body").html(html);  },1000);
          }   
       );
  });

//pilih data pada modal view production order
  $(document).on('click', '.pilih', function (e) {
      document.getElementById("kode_prod").value = $(this).attr('kode_prod');
      document.getElementById("sales_order").value = $(this).attr('sales_order');
      $('#view_data').modal('hide');
  });

  $(document).on("change", "input[name='type[]']", function(){
        checkTampil('type');
  });

  $(document).on("change", "input[name='sc[]']", function(){
        checkTampil('sc');
  });

  function checkTampil(show){
      if(show == 'type'){
      //var radio_arr = new Array(); 
      var radio_type = $('input[name="type[]"]').map(function(e, i) {
            if(this.checked == true){
                return i.value;
            }
      }).get();

      if(radio_type == 'mts'){
        $('input[name="sc[]"]').prop("checked", false);
        $('#sc_true').prop("disabled", false).attr('id','sc_true');
        $('#sc_false').prop("disabled", false).attr('id','sc_false');
        $('#show_type').hide();
        $('#label_dept').html('Departemen');
        
      }else if(radio_type == "pengiriman"){
        $('input[name="sc[]"]').prop("checked", false);
        $('#sc_true').prop("disabled", false).attr('id','sc_true');
        $('#sc_false').prop("disabled", false).attr('id','sc_false');
        $('#show_type').hide();
        $('#label_dept').html('Departemen Tujuan');

      }else if(radio_type == "mto"){ // Make to Stock
        $('#sc_true').prop("checked", true);
        $('input[name="sc[]"]').prop("disabled", true);
        $('#show_type').show();
        $('#show_sc').show();
        $('#label_dept').html('Departemen Tujuan');
      }
      $('#kode_prod').val('');
      $('#sales_order').val('');

    }else if(show == 'sc'){

      var radio_type_2 = $('input[name="sc[]"]').map(function(e, i) {
            if(this.checked == true){
                return i.value;
            }
      }).get();
      
      if(radio_type_2 == 'yes'){
        $('#show_type').show();
        $('#show_sc').show();

      }else if(radio_type_2 == 'no'){
        $('#show_type').show();
        $('#show_sc').hide();
        $('#kode_prod').val('');
        $('#sales_order').val('');
      }
    }
  }


  //klik button simpan
  $('#btn-simpan').click(function(){

      var radio_type = $('input[name="type[]"]').map(function(e, i) {
            if(this.checked == true){
                return i.value;
            }
      }).get();

      var radio_type_2 = $('input[name="sc[]"]').map(function(e, i) {
            if(this.checked == true){
                return i.value;
            }
      }).get();


      $('#btn-simpan').button('loading');
      please_wait(function(){});
      $.ajax({
         type: "POST",
         dataType: "json",
         url :'<?php echo base_url('ppic/procurementorder/simpan')?>',
         beforeSend: function(e) {
            if(e && e.overrideMimeType) {
                e.overrideMimeType("application/json;charset=UTF-8");
            }
         },
         data: {kode_prod   : $('#kode_prod').val(),
                kode_proc   : $('#kode_proc').val(),
                tgl         : $('#tgl').val(),
                note        : $('#note').val(),
                sales_order : $('#sales_order').val(),
                priority    : $('#priority').val(),
                warehouse   : $('#warehouse').val(),
                type        : radio_type,
                show_sc     : radio_type_2,

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
              $('#kode_proc').val(data.isi);
              $('#kode_proc_en').val(data.kode_encrypt);
              unblockUI( function() {
                setTimeout(function() { 
                  alert_notify(data.icon,data.message,data.type, function(){
                  window.location.replace('edit/'+$('#kode_proc_en').val());
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
       if($('#kode_proc').val() == ""){
        var message = 'Data Masih Kosong !';
        alert_modal_warning(message);
       }
    });


    //klik button print
    $('#btn-print').click(function(){
       if($('#kode_proc').val() == ""){
        var message = 'Data Masih Kosong !';
        alert_modal_warning(message);
       }
    });
   
</script>


</body>
</html>
