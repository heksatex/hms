
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
                  <div class="col-xs-4"><label>Sales Contract</label></div>
                  <div class="col-xs-8">
                    <input type="text" class="form-control input-sm" name="sales_order" id="sales_order" readonly="readonly" />
                    <input type="hidden" class="form-control input-sm" name="sales_order_en" id="sales_order_en" readonly="readonly" />
                  </div>                                    
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Customer</label></div>
                  <div class="col-xs-8">
                    <div class='input-group'>
                      <input type="hidden" class="form-control input-sm" name="cust_id" id="cust_id" readonly="readonly" />
                      <input type="text" class="form-control input-sm" name="customer" id="customer" readonly="readonly" />
                       <span class="input-group-addon">
                          <a href="#" class="cust"  data-toggle="tooltip" title="Cari Customer"><span class="glyphicon  glyphicon-share"></span></a>
                      </span>
                    </div>
                  </div>                                    
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Invoice Address</label></div>
                  <div class="col-xs-8">
                    <textarea type="text" class="form-control input-sm"name="invoice_address" id="invoice_address"  readonly="readonly"></textarea>
                  </div>                                    
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Delivery Address</label></div>
                  <div class="col-xs-8">
                    <textarea type="text" class="form-control input-sm" name="delivery_address" id="delivery_address" readonly="readonly"></textarea>
                  </div>                                    
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Buyer Code </label></div>
                  <div class="col-xs-8">
                    <input type="text" class="form-control input-sm" name="buyer_code" id="buyer_code" readonly="readonly" />
                  </div>                                    
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Type</label></div>
                  <div class="col-xs-8">
                    <select class="form-control input-sm" name="type" id="type" />
                     <option>With Contract</option>
                    </select>
                  </div>                                    
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Sales Person</label></div>
                  <div class="col-xs-8">
                    <input type="text" class="form-control input-sm" name="sales_person" id="sales_person" readonly="readonly"/>
                  </div>                                    
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Order Production</label></div>
                  <div class="col-xs-8">
                    <input type="checkbox" name="order_production" id="order_production" value="true">
                  </div>                                    
                </div>
              </div>

              <div class="col-md-6">
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Tanggal </label></div>
                  <div class="col-xs-8 col-md-8">
                    <input type='text' class="form-control input-sm" name="tgl" id="tgl" readonly="readonly" value="<?php echo date('Y-m-d H:i:s')?>"  />
                  </div>                                    
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Reference/Description</label></div>
                  <div class="col-xs-8">
                    <input type="text" class="form-control input-sm" name="reference" id="reference" />
                  </div>                                    
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Warehouse</label></div>
                  <div class="col-xs-8">
                    <select class="form-control input-sm" name="warehouse" id="warehouse" />
                      <option value="">Pilih Warehouse</option>
                      <?php foreach ($warehouse as $row) {
                        if($row->nama == 'Gudang Jadi') {?>
                         <option value='<?php echo $row->kode; ?>' selected><?php echo $row->nama;?></option>
                      <?php
                        }else{
                          ?>
                         <option value='<?php echo $row->kode; ?>'><?php echo $row->nama;?></option>
                      <?php }
                          }?>
                    </select>
                  </div>                                    
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Currency</label></div>
                  <div class="col-xs-8">
                    <select class="form-control input-sm" name="currency" id="currency" />
                     <option value="">Pilih Currency</option>
                      <?php foreach ($currency as $row) {
                          if($row->nama == 'IDR'){ ?>
                            <option selected=""><?php echo $row->nama;?></option>

                      <?php  }else{?>
                            <option><?php echo $row->nama;?></option>
                      <?php 
                          }
                      ?>
                      <?php }?>
                    </select>
                  </div>                                    
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Delivery Date</label></div>
                  <div class="col-xs-8">
                    <input type="text" class="form-control input-sm" name="delivery_date" id="delivery_date" />
                  </div>                                    
                </div>
                <!--div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Time Of Shipment</label></div>
                  <div class="col-xs-8">
                    <input type="text" class="form-control input-sm" name="time_ship" id="time_ship" />
                  </div>                                    
                </div-->
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Note</label></div>
                  <div class="col-xs-8">
                    <textarea type="text" class="form-control input-sm" name="note_head" id="note_head" ></textarea>
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

  // modal view customer
  $(document).on('click','.cust',function(e){
      e.preventDefault();
      $("#view_data").modal('show');
      $(".view_body").html('<center><h5><img src="<?php echo base_url('dist/img/ajax-loader.gif') ?> "/><br>Please Wait...</h5></center>');
      $('.modal-title').text('View Customer');
        $.post('<?php echo site_url()?>sales/salescontract/list_customer_modal',
          function(html){
            setTimeout(function() {$(".view_body").html(html);  },1000);
          }   
       );
  });

  //pilih data pada modal view Customer
  $(document).on('click', '.pilih', function (e) {
      document.getElementById("cust_id").value = $(this).attr('id');
      document.getElementById("customer").value = $(this).attr('name');
      document.getElementById("invoice_address").value = $(this).attr('invoice-address');
      document.getElementById("delivery_address").value = $(this).attr('delivery-address');
      document.getElementById("buyer_code").value = $(this).attr('buyer-code');
      $('#view_data').modal('hide');
  });

    //klik button simpan
    $('#btn-simpan').click(function(){

      //op = $('#order_production').val();
     
      var op = $("input:checked").val();
      if(op == 'true'){
        order_production = 'true';
      }else{
        order_production = 'false'
      }
      //alert(order_production);

      $('#btn-simpan').button('loading');
      please_wait(function(){});
      $.ajax({
         type: "POST",
         dataType: "json",
         url :'<?php echo base_url('sales/salescontract/simpan')?>',
         beforeSend: function(e) {
            if(e && e.overrideMimeType) {
                e.overrideMimeType("application/json;charset=UTF-8");
            }
         },
         data: {sales_order: $('#sales_order').val(),
                cust_id    : $('#cust_id').val(),
                customer   : $('#customer').val(),
                invoice_address  : $('#invoice_address').val(),
                delivery_address : $('#delivery_address').val(),
                buyer_code : $('#buyer_code').val(),
                type       : $('#type').val(),
                order_production : order_production,
                tgl        : $('#tgl').val(),
                reference  : $('#reference').val(),
                warehouse  : $('#warehouse').val(),
                currency   : $('#currency').val(),
                delivery_date   : $('#delivery_date').val(),
                time_ship   : '',
                note_head  : $('#note_head').val(),

          },success: function(data){
            if(data.sesi == "habis"){
              //alert jika session habis
              alert_modal_warning(data.message);
              window.location.replace('index');
            }else if(data.status == "failed"){
              //jika ada form belum keiisi
              unblockUI( function() {
                setTimeout(function() { alert_notify(data.icon,data.message,data.type); }, 1000);
              });
              document.getElementById(data.field).focus();
            }else{
             //jika berhasil disimpan/diubah
              $('#sales_order').val(data.isi);
              $('#sales_order_en').val(data.kode_encrypt);
              unblockUI( function() {
                setTimeout(function() { 
                  alert_notify(data.icon,data.message,data.type, function(){
                   
                  window.location.replace('edit/'+$('#sales_order_en').val());
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
       if($('#sales_order').val() == ""){
        var message = 'Data Masih Kosong !';
        alert_modal_warning(message);
       }
    });

    //klik button Batal
    $('#btn-generate').click(function(){
       if($('#sales_order').val() == ""){
        var message = 'Data Masih Kosong !';
        alert_modal_warning(message);
       }
    });

    //klik button Batal
    $('#btn-print').click(function(){
       if($('#sales_order').val() == ""){
        var message = 'Data Masih Kosong !';
        alert_modal_warning(message);
       }
    });

</script>