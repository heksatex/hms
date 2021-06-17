
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
    <section class="content">
      <!--  box content -->
      <div class="box">
        <div class="box-body">

  <!--div class="accordion" id="accordionExample">  
   <div class="card">
    <div class="card-header" id="headingTwo">
      <h5 class="mb-0">
        <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
          Collapsible Group Item #2
        </button>
      </h5>
    </div>
    <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionExample">
      <div class="card-body">
        Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them accusamus labore sustainable VHS.
      </div>
    </div>
  </div>

</div-->


    <!--div class="panel panel-default">
        <div class="panel-heading" role="tab" id="questionOne">
        <h5 class="panel-title">
        <a data-toggle="collapse" data-parent="#faq" href="#answerOne" aria-expanded="false" aria-controls="answerOne">
        Question 1
        </a>
        </h5>
        </div>
        <div id="answerOne" class="panel-collapse collapse" role="tabpanel" aria-labelledby="questionOne">
        <div class="panel-body">
        Answer 1...
        </div>
        </div>
     </div-->

          <form name="input" class="form-horizontal" role="form" method="POST">
              <div class="col-md-6">
                <div class="form-group"> 
                  <div class="col-md-12 col-xs-12">
                    <div class="col-xs-4"><label>Kode</label></div>
                     <div class="col-xs-8">
                        <input type="text" class="form-control input-sm" name="kode" id="kode"  />
                      </div>                                    
                  </div>
                 
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <div class="col-md-12 col-xs-12">
                    <div class="col-xs-4"><label>Status </label></div>
                    <div class="col-xs-8 col-md-8">
                      <select class="form-control input-sm" name="status" id="status">
                          <option value="">All</option>
                          <option value="draft">Draft</option>
                          <option value="ready">Ready</option>
                          <option value="done">Done</option>
                          <option value="cancel">Cancel</option>
                      </select>
                    </div>
                  </div>                
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <div class="col-md-12 col-xs-12">
                    <div class="col-xs-4"><label></label></div>
                     <div class="col-xs-8">
                        <button type="button" id="btn-filter" name="submit" class="btn btn-primary btn-sm" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing...">Proses</button>
                      </div>                                    
                  </div>
                </div>
              </div>
            </form>
            <div class="col-xs-12 table-responsive">
              <table id="example1" class="table table-striped">
                <thead>
                  <tr>
                    <th>Tanggal</th>
                    <th>Stock Move</th>
                    <th>Origin</th>
                    <th>Lokasi dari</th>
                    <th>Lokasi Tujuan</th>
                    <th>Picking</th>
                    <th>Kode Produk</th>
                    <th>Nama Produk</th>
                    <th>Lot</th>
                    <th>Qty</th>
                    <th>Uom</th>
                    <th>Qty2</th>
                    <th>UOm2</th>
                    <th>Status</th>
                  </tr>
                </thead>
              </table>
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
<!-- /.Site wrapper -->

<?php $this->load->view("admin/_partials/js.php") ?>

<script type="text/javascript">
    var table;
    $(document).ready(function() {
 
        //datatables
        table = $('#example1').DataTable({ 
            "dom": "<'row'<'col-sm-4'l><'col-sm-5'i><'col-sm-3'f>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-5'><'col-sm-7'p>>",

            "aLengthMenu": [[100, 1000, 5000, -1], [100, 1000, 5000, "All"]],
            "iDisplayLength": 1000,
            "processing": true, 
            "serverSide": true, 
            "scrollY": 400,
            "scrollX": true,
            "order": [], 

            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
             
            "ajax": {
                "url": "<?php echo site_url('warehouse/stockmoves/get_data')?>",
                "type": "POST",
                /*
                 "data": function ( data ) {
                    data.id_dept = "<?php echo $id_dept;?>"
                    data.kode = $('#kode').val();
                    data.status = $('#status').val();
                    data.reff = $('#reff').val();
                    data.reff_picking = $('#reff_picking').val();
                },*/
                "data":{"id_dept" : "<?php echo $id_dept;?>"}
            },
            "columnDefs": [
            
              {
                "targets" : [ 3 ],
                 render: function (data, type, full, meta) {
                        return "<div class='text-wrap width-140'>" + data + "</div>";
                }
              },
              {
                "targets" : [ 4 ],
                 render: function (data, type, full, meta) {
                        return "<div class='text-wrap width-140'>" + data + "</div>";
                }
              },
              {
                "targets" : 5,
                 render: function (data, type, full, meta) {
                        return "<div class='text-wrap width-150'>" + data + "</div>";
                }
              },
              {
                "targets" : 7,
                 render: function (data, type, full, meta) {
                        return "<div class='text-wrap width-150'>" + data + "</div>";
                }
              },
              {
                "targets" : 8,
                 render: function (data, type, full, meta) {
                        return "<div class='text-wrap width-150'>" + data + "</div>";
                }
              },

            ],
          
        });

        $('#btn-filter').click(function(){ //button filter event click
          $('#btn-filter').button('loading');
          table.ajax.reload( function(){
            $('#btn-filter').button('reset');
          });  //just reload table
        });
 
    });
 
</script>


</body>
</html>
