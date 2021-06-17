
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
         $data['jen_status'] =  $salescontract->status;
         $this->load->view("admin/_partials/statusbar.php", $data); 
       ?>
      </div>
    </section>

    <!-- Main content -->
    <section class="content">

      <!--  box content -->
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title"><b><?php echo $salescontract->sales_order;?></b></h3>
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
                  <div class="col-xs-4"><label>Buyer Code</label></div>
                  <div class="col-xs-8">
                    <input type="text" class="form-control input-sm" name="buyer_code" id="buyer_code" readonly="readonly" value="<?php echo $salescontract->buyer_code?>" />
                    <input type="hidden" class="form-control input-sm" name="sales_order" id="sales_order" readonly="readonly" value="<?php echo $salescontract->sales_order?>" />
                  </div>                                    
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Delivery Date</label></div>
                  <div class="col-xs-8">
                    <input type="text" class="form-control input-sm" name="delivery_date" id="delivery_date" value="<?php echo $salescontract->delivery_date ?>" readonly="readonly" />
                  </div>                                    
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Order Production</label></div>
                  <div class="col-xs-8">
                    <?php if($salescontract->order_production == 'true'){?>
                            <input type="checkbox" name="order_production" id="order_production" value="true" checked="" disabled>                            
                    <?php }else{?>
                            <input type="checkbox" name="order_production" id="order_production" value="true" disabled>
                    <?php  }
                    ?>
                  </div>                                    
                </div>
              </div>

              <div class="col-md-6">
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Tanggal </label></div>
                  <div class="col-xs-8 col-md-8">
                    <input type='text' class="form-control input-sm" name="tgl" id="tgl" readonly="readonly" value="<?php echo $salescontract->create_date ?>"  />
                  </div>                                    
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Reference/Description</label></div>
                  <div class="col-xs-8">
                    <input type="text" class="form-control input-sm" name="reference" id="reference" value="<?php echo $salescontract->reference?>" readonly="readonly" />
                  </div>                                    
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Sales Person</label></div>
                  <div class="col-xs-8">
                    <input type="text" class="form-control input-sm" name="salesperson" id="salesperson" value="<?php echo $salescontract->sales_group?>" readonly="readonly" />
                  </div>
                  <div id="confirm">
                    <input type="hidden" class="form-control input-sm" name="status" id="status" readonly="readonly" value="<?php echo $salescontract->status?>" />
                  </div>                                    
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Note</label></div>
                  <div class="col-xs-8">
                    <textarea type="text" class="form-control input-sm" name="note_head" id="note_head" readonly="readonly"><?php echo $salescontract->note_head?></textarea>
                  </div>                                    
                </div> 
              </div>
            </div>
          
            <div class="row">
              <div class="col-md-12">
                <!-- Custom Tabs -->
                <div class="">
                  <ul class="nav nav-tabs">
                    <li class="active"><a href="#tab_1" data-toggle="tab">Contract Lines</a></li>
                  </ul>
                  <div class="tab-content"><br>
                    <div class="tab-pane active" id="tab_1">
                      <!-- Tabel  contract lines-->
                      <div class="col-md-12 table-responsive">
                      <table class="table table-condesed table-hover table-responsive rlstable" id="contract_lines" > 
                        <thead>
                          <tr>
                            <th class="style no">No.</th>
                            <th class="style text-wrap width-150">Product</th>
                            <th class="style text-wrap width-150">Description</th>
                            <th class="style" width="80px" style="text-align: right;" >Qty</th>
                            <th class="style" width="50px" >Uom</th>
                            <th class="style" width="150px">Due Date</th>
                            <th class="style " width="30"></th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php 
                            $no = 1;
                            foreach ($details as $row) {
                          ?>
                          <tr class="num">
                              <td></td>
                              <td><?php echo $row->nama_produk?></td>
                              <td><?php echo $row->description?></td>
                              <td align="right"><?php echo $row->qty?></td>
                              <td><?php echo $row->uom?></td>
                              <td><?php echo $row->due_date?></td>
                              <td><a href="#" onclick="edit('<?php  echo $row->sales_order ?>', '<?php  echo $row->row_order ?>')" title="Edit" data-toggle="tooltip" style="color: #FFC107;" ddata-isi="<?php echo $row->row_order;?>"><i class="fa fa-edit"></i></a> </td>
                          <?php 
                            }     
                          ?>
                            </tr>
                          </tbody>
                        </table>
                      </div>
                      <!-- Tabel  -->
                    </div>
                    <!-- /.tab-pane 1 -->
                               
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
        $data['kode'] =  $salescontract->sales_order;
        $data['mms']  =  $mms->kode;
        $this->load->view("admin/_partials/footer.php",$data) 
     ?>
    </div>
  </footer>

</div>

<?php $this->load->view("admin/_partials/js.php") ?>

<style type="text/css">
  /*css untuk atur lebar dan tinggi edit due date*/
   @media screen and (min-width: 768px) {
     .lebar2 .modal-dialog  {width: 50%;}

    }
  
</style>

<script type="text/javascript">
  
  //modal mode print
  function edit(sales_order, row_order){

    var status = $('#status').val();
    $("#edit_data").removeClass("lebar");
    $("#edit_data").addClass("lebar2");

    if(status == 'waiting_date'){
      //var kode = $('#kode').val();
      $(".edit_data").html('<center><h5><img src="<?php echo base_url('dist/img/ajax-loader.gif') ?> "/><br>Please Wait...</h5></center>');
      $("#edit_data").modal({
          show: true,
          backdrop: 'static'
      });
      $('.modal-title').text('Edit Contract Lines');
      $.post('<?php echo site_url()?>ppic/orderplanning/edit_details_modal',
        { sales_order : sales_order,row_order : row_order },
          function(html){
            setTimeout(function() {$(".edit_data").html(html);  },1000);
        }   
      );
    }else{
      alert_modal_warning('Maaf, Data Tidak bisa Edit !');
    }
  }

   //klik button confirm date   
  $(document).on('click','#btn-confirm-date',function(){
    var status = $('#status').val();
    if(status == 'waiting_date'){

      $('#btn-confirm-date').button('loading');
      please_wait(function(){});
      $.ajax({
         type: "POST",
         dataType: "json",
         url :'<?php echo base_url('ppic/orderplanning/confirm_date')?>',
         beforeSend: function(e) {
            if(e && e.overrideMimeType) {
                e.overrideMimeType("application/json;charset=UTF-8");
            }
         },
         data: {sales_order: $('#sales_order').val(),
              
          },success: function(data){
            if(data.sesi == "habis"){
              //alert jika session habis
              alert_modal_warning(data.message);
              window.location.replace('../index');
            }else if(data.status == "failed"){
              //jika details masih kosong
              unblockUI( function() {
                setTimeout(function() { alert_notify(data.icon,data.message,data.type); }, 1000);
              });
              $("#tab_1").load(location.href + " #tab_1");
              $("#status_bar").load(location.href + " #status_bar");
              $('#btn-confirm-date').button('reset');
             
            }else{
              unblockUI( function() {
                setTimeout(function() { 
                  alert_notify(data.icon,data.message,data.type, function(){
                },1000); 
                });
              });
              $("#confirm").load(location.href + " #confirm");
              $("#status_bar").load(location.href + " #status_bar");
              $("#foot").load(location.href + " #foot");
            }
            $('#btn-confirm-date').button('reset');

          },error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.responseText);
            unblockUI( function(){});
            $('#btn-confirm-date').button('reset');
          }
      });
        window.setTimeout(function() {
       $(".alert").fadeTo(500, 0).slideUp(500, function(){
        $(this).remove(); });
      }, 3000);
    }else if(status == 'draft'){
      alert_modal_warning('Maaf, Data belum Ready !');
    }else if(status != 'waiting_date'){
      alert_modal_warning('Maaf, Confirm Date sudah Dilakukan !');
    }
  });



</script>


</body>
</html>
