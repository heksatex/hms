<!DOCTYPE html>
<html lang="en">
<head>
  <?php $this->load->view("admin/_partials/head.php") ?>
  <style type="text/css">

  </style>
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
    <section class="content" >
      <!--  box content -->
      <div class="box" >
        <div class="box-header with-border">
          <h3 class="box-title"><b>KANBAN MODE</b></h3>
          <div class="image pull-right text-right">
            <?php 
              $dept = $id_dept;//parsing dari controller 
            ?>
            <a href="<?php echo base_url("manufacturing/mO/".$dept);?>"  data-toggle="tooltip" title="List Mode">
              <img src="<?php echo base_url('dist/img/list.png'); ?>" style="width: 7%; height: auto; text-align: right;" >
            </a>
          </div>
        </div>
        <div class="box-body" >
            <form class="form-horizontal" >
              <div class="form-group"> 
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-2"><label>Product</label></div>
                  <div class="col-xs-10 col-md-6">
                    <input type="text" class="form-control input-sm" name="product" id="product"/>
                  </div>                                    
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-2"><label>Tanggal </label></div>
                  <div class="col-xs-5 col-md-3">
                    <div class='input-group date' id='dari' >
                    <input type='text' class="form-control input-sm" name="dari" id="dari1" readonly="readonly"  />
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                    </span>
                    </div>
                  </div>
                  <div class="col-xs-5 col-md-3">
                    <div class='input-group date' id='sampai' >
                    <input type='text' class="form-control input-sm" name="sampai" id="sampai1" readonly="readonly" />
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                    </span>
                    </div>
                  </div>
                  <div class="col-xs-4 col-md-2">
                    <button type="button" class="btn btn-primary btn-sm" id="btn-cari" name="btn-cari">Cari</button>
                  </div>
                </div>
              </div>
              <div class="form-group">
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-2"><label></label></div>
                  <div class="col-xs-8">
                    
                  </div>                                    
                </div>
              </div>
            </form>
                       
            <div id="detail">
              <?php $this->load->view('manufacturing/v_mo_jadwal_view', array('data_mesin'=>$data_mesin, 'arr_multi' => $arr_multi));  ?>
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

<?php $this->load->view("admin/_partials/js.php") ?>

<script type="text/javascript">

  var datedari=new Date();
  datedari.setMonth(datedari.getMonth(),1);
  $('#dari').datetimepicker({
      defaultDate: datedari,
      format : 'YYYY-MM-DD HH:mm:ss',
      ignoreReadonly: true,
  });
  
  var datesampai=new Date();  
  datesampai.setMonth(datesampai.getMonth());
  $('#sampai').datetimepicker({
      defaultDate: datesampai,
      format : 'YYYY-MM-DD HH:mm:ss',
      ignoreReadonly: true,
  }); 


 $(document).ready(function(){
  $("#btn-cari").click(function(){ 
    $.ajax({
      url :'<?php echo site_url ('manufacturing/mO/search')?>',
      type: 'POST', 
      data: {product: $("#product").val(), dari: $("#dari1").val(), sampai: $("#sampai1").val(),}, 
      dataType: "json",
      beforeSend: function(e) {
        if(e && e.overrideMimeType) {
          e.overrideMimeType("application/json;charset=UTF-8");
        }
      },
      success: function(response){
        $("#detail").html(response.hasil);
      },
      error: function (xhr, ajaxOptions, thrownError) { 
        alert(xhr.responseText);
        
      }
    });
  });
});

 $('#exampple1').DataTable({
    "processing": false,
    "serverSide": false,
    "ordering"  : false,
    "searching" : false,
    "info"      : false,
    "paginate"  : false,
    "scrollY"   : false,
    "scrollX"   : true,
  });

</script>

</body>
</html>
