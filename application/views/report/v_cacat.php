
<!DOCTYPE html>
<html lang="en">
<head>
  <?php $this->load->view("admin/_partials/head.php") ?>
  <style type="text/css">
    
    h3{
      display: block !important;
      text-align: center !important;
    }
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
  <?php $this->load->view("admin/_partials/sidebar.php") ?>
  </aside>

  <!-- Content Wrapper-->
  <div class="content-wrapper">
    <!-- Content Header (Status - Bar) -->
    <section class="content-header">
    </section>

    <!-- Main content -->
    <section class="content">
      <!--  box content -->
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title"><b>Report Cacat</b></h3>
        </div>
        <div class="box-body">

            <form name="input" class="form-horizontal" role="form" method="POST">
              <div class="col-md-6">
                <div class="form-group"> 
                  <div class="col-md-12 col-xs-12">
                    <div class="col-xs-4"><label>Departemen</label></div>
                     <div class="col-xs-8">
                        <select type="text" class="form-control input-sm" name="departemen" id="departemen"  >
                        </select>
                      </div>                                    
                  </div>
                  <div class="col-md-12 col-xs-12">
                    <div class="col-xs-4"><label>No MO </label></div>
                    <div class="col-xs-8 col-md-8">
                      <select class="form-control input-sm" name="mo" id="mo">  </select>
                    </div>
                  </div>
                  <div class="col-md-12 col-xs-12">
                    <div class="col-xs-4"><label>LOT </label></div>
                    <div class="col-xs-8 col-md-8">
                      <select class="form-control input-sm" name="lot" id="lot" multiple=""></select>                      
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <div class="col-md-12 col-xs-12">
                    <div class="col-xs-4"><label> </label></div>
                    <div class="col-xs-8 col-md-8">
                      <button type="button" class="btn btn-sm btn-default" id="btn-generate">Generate</button>                     
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


  <?php $this->load->view("admin/_partials/modal.php") ?>
</div>

<?php $this->load->view("admin/_partials/js.php") ?>

<script type="text/javascript">
      
    

    //select 2 Departementy
    $('#departemen').select2({
      allowClear: true,
      placeholder: "Select Departemen",
      ajax:{
            dataType: 'JSON',
            type : "POST",
            url : "<?php echo base_url();?>report/cacat/get_departement_select2",
            //delay : 250,
            data : function(params){
              return{
                nama:params.term,
              };
            }, 
            processResults:function(data){
              var results = [];
              $.each(data, function(index,item){
                results.push({
                    id:item.kode,
                    text:item.nama
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


    $("#mo").select2({
      placeholder : "Select MO"
    });


    $("#departemen").change(function(){
      $("#mo").html('');
      $("#lot").html('');
      //select 2 MO by departemen
      $('#mo').select2({
        allowClear: true,
        placeholder: "Select MO",
        ajax:{
              dataType: 'JSON',
              type : "POST",
              url : "<?php echo base_url();?>report/cacat/get_mrp_select2",
              //delay : 250,
              data : function(params){
                return{
                  mo:params.term,
                  dept_id:$("#departemen").val(),
                };
              }, 
              processResults:function(data){
                var results = [];
                $.each(data, function(index,item){
                  results.push({
                      id:item.kode,
                      text:item.kode
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
    });

    $("#lot").select2({
      placeholder : "Select Lot"
    })

    $("#mo").change(function(){
      $("#lot").html('');
      $("#lot").select2({
          //closeOnSelect: false,
          placeholder : "Select Lot",
          allowClear: true,
        
          ajax:{
                dataType : 'JSON',
                type     : 'POST',
                url      : '<?php echo base_url();?>report/cacat/get_lot_select2',
                data : function(params){
                  return{
                    lot:params.term,
                    mo:$("#mo").val(),
                  };
                }, 
                processResults:function(data){

                  var results = [];
                  $.each(data, function(index,item){
                      results.push({
                        id:item.quant_id,
                        text:item.lot
                      });
                  });

                  return {
                    results : results
                  }
                },
                error : function(xhr, ajaxOptions, thrownError){
                 // alert(xhr.responseText);
                }
          }
      });
    });


  //klik button generate
  $(document).on('click',"#btn-generate",function(e){

    var departemen = $("#departemen").val();
    var mo          = $("#mo").val();
    var lot         = $("#lot").val();

    if(departemen == null){
      alert_notify('fa fa-warning','departemen Harus diisi !','danger');
    }else if(mo == null){
      alert_notify('fa fa-warning','No MO Harus diisi !','danger');
    }else if(lot == null){
      alert_notify('fa fa-warning','LOT Harus diisi !','danger');
    }else if(lot.length > 10 && departemen =='WRD'){
      alert_notify('fa fa-warning','Lot yang dipilih Maksimal 10 !','danger');
    }else{
      var url = '<?php echo base_url() ?>report/cacat/report_cacat';
      window.open(url+'?departemen='+ departemen+'&&mo='+ mo+'&&lot='+ lot,'_blank');
    }

  });

 
</script>

</body>
</html>
