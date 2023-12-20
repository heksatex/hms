<!DOCTYPE html>
<html lang="en">

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
      $data['deptid'] = $id_dept;
      $this->load->view("admin/_partials/topbar.php", $data)
        ?>
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
            <form name="input" class="form-horizontal" role="form" method="POST">
                <div class="form-group">
                    <div class="col-md-12">
                      <div class="col-md-4 panel-heading" role="tab" id="advanced" style="padding:0px 0px 0px 15px;  ">
                          <div data-toggle="collapse" href="#advancedSearch" aria-expanded="false" aria-controls="advancedSearch" class='collapsed'>
                            <label style="cursor:pointer;">
                              <i class="showAdvanced glyphicon glyphicon-triangle-bottom"></i>
                              Advanced  Search
                            </label>
                          </div>
                      </div>
                    </div>
                  </div>
                <div class="col-md-12">
                    <div class="panel panel-default" style="margin-bottom: 0px;">
                      <div id="advancedSearch" class="panel-collapse collapse" role="tabpanel" aria-labelledby="advanced" >
                        <div class="panel-body" style="padding: 5px">
                          <div class="form-group col-md-12" style="margin-bottom:0px">
                              <div class="col-md-6">
                                <div class="form-group"> 
                                  <div class="col-xs-5"><label>No. PB</label></div>
                                  <div class="col-xs-7">
                                    <input type="text" class="form-control input-sm" name="no_pb" id="no_pb" >
                                  </div>  
                                  <div class="col-xs-5"><label>Lot GJD</label></div>
                                  <div class="col-xs-7">
                                    <input type="text" class="form-control input-sm" name="lot" id="lot" >
                                  </div>  
                                </div>
                              </div>
                              <div class="col-md-6">
                                <div class="form-group"> 
                                  <div class="col-xs-5"><label>Marketing</label></div>
                                  <div class="col-xs-7">
                                      <select class="form-control input-sm" name="marketing" id="marketing" >
                                        <option value=""></option>
                                        <?php foreach ($sales_group as $row) {
                                                echo "<option value='".$row->kode_sales_group."'>".$row->nama_sales_group."</option>";
                                              }
                                        ?>
                                      </select>
                                  </div>  
                                  <div class="col-xs-5"><label>Status</label></div>
                                  <div class="col-xs-7">
                                      <select class="form-control input-sm" name="status" id="status" >
                                        <option value=""></option>
                                        <?php foreach ($list_status as $row) {
                                                echo "<option value='".$row->jenis_status."'>".$row->nama_status."</option>";
                                              }
                                        ?>
                                      </select>
                                  </div>  
                                </div>
                              </div>
                              <div class="col-md-12" >
                                <div class="form-group" >
                                    <div class="col-xs-8" style="padding-top:0px">
                                        <button type="button" id="btn-filter" name="submit" class="btn btn-primary btn-sm" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing...">Proses</button>
                                    </div>                                    
                                </div>
                              </div>
                          </div>
                        </div>
                      </div>
                    </div>
                </div>
            </form>
            <br>
            <div class="col-xs-12 table-responsive">
              <table id="example1" class="table table-striped table-condesed table-hover">
                <thead>
                  <tr>
                    <th class="no">No</th>
                    <th>No. PB</th>
                    <th>Tgl. buat</th>
                    <th>Tgl. Transaksi</th>
                    <th>Marketing</th>
                    <th>Alasan</th>
                    <th>Total Batch</th>
                    <th>No. Adjustment</th>
                    <th>Notes</th>
                    <th>Nama user</th>
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

  <?php $this->load->view("admin/_partials/js.php") ?>

  <script type="text/javascript">
    var table;
    $(function () {

        //datatables
        table = $('#example1').DataTable({
            "stateSave": true,
            "dom": "<'row'<'col-sm-4'l><'col-sm-5'i><'col-sm-3'f>>" +
            "<'row'<'col-sm-12'tr>>" +
            "<'row'<'col-sm-5'><'col-sm-7'p>>",
            "processing": true,
            "serverSide": true,
            "order": [],

            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,

            "ajax": {
              "url": "<?php echo site_url('manufacturing/barcodemanual/get_data') ?>",
              "type": "POST",
              "data": function ( data ) {
                          data.pb          = $('#no_pb').val();
                          data.sales_group = $('#marketing').val();
                          data.status      = $('#status').val();
                          data.lot      = $('#lot').val();
              },"error": function(xhr) {
                  // Message also does not show here
                  alert("error Load");
                  $('#btn-filter').button('reset');
              },
            },

            "columnDefs": [
              {
                  "targets": [0],
                  "orderable": false,
              },
              {
                  "targets": 1,
                  render: function (data, type, full, meta) {
                  return "<div class='text-wrap width-90'>" + data + "</div>";
                  }
              },
            ],
        });

        $('#no_pb').keydown(function(event){
            if(event.keyCode == 13) {
              event.preventDefault();
              $('#btn-filter').button('loading');
                  table.ajax.reload( function(){
                    $('#btn-filter').button('reset');
              });
            }
        });

        $('#lot').keydown(function(event){
            if(event.keyCode == 13) {
              event.preventDefault();
              $('#btn-filter').button('loading');
                  table.ajax.reload( function(){
                    $('#btn-filter').button('reset');
              });
            }
        });

        $('#btn-filter').click(function(){ //button filter event click
            $('#btn-filter').button('loading');
            table.ajax.reload( function(){
            $('#btn-filter').button('reset');
            });
        });

        // function reload
        function reload_table(event){
            if(event.keyCode == 13) {
            $('#btn-filter').button('loading');
            table.ajax.reload( function(){
                $('#btn-filter').button('reset');
            });
            }
        }
  

    });

  </script>

</body>

</html>