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
      $data['deptid']     = $id_dept;
      $this->load->view("admin/_partials/topbar.php", $data)
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
      </section>

      <!-- Main content -->
      <section class="content">
        <!--  box content -->
        <div class="box">
          <div class="box-body">
            <div class="col-md-12">
              <div class="col-md-12 panel-heading" role="tab" id="advanced" style="padding:0px 0px 0px 15px;cursor:pointer;">
                <div data-toggle="collapse" href="#advancedSearch" aria-expanded="false" aria-controls="advancedSearch" class='collapsed'>
                  <label>
                    <i class="showAdvanced glyphicon glyphicon-triangle-bottom">&nbsp;</i>Filter
                  </label>
                </div>
              </div>

            </div>
            <br>
            <div class="col-md-12">
              <div class="panel panel-default" style="margin-bottom: 0px;">
                <div id="advancedSearch" class="panel-collapse collapse" role="tabpanel" aria-labelledby="advanced">
                  <div class="panel-body" style="padding: 5px">
                    <div class="col-md-6 col-xs-12">
                      <div class="form-group">
                        <div class="col-md-12 col-xs-12">
                          <div class="col-xs-4">
                            <label class="form-label">Nama</label>
                          </div>
                          <div class="col-xs-8 col-md-8">
                            <input type="text" class="form-control input-sm" name="partner" id="partner">
                          </div>
                        </div>
                      </div>
                      <div class="form-group">
                        <div class="col-md-12 col-xs-12">
                          <div class="col-xs-4">
                            <label class="form-label">Partner</label>
                          </div>
                          <div class="col-xs-8 col-md-8">
                            <select name="type" class="form-control select2 input-sm" id="type" style="width: 100%">
                              <option value="all"></option>
                              <option value="customer">Customer</option>
                              <option value="supplier">Supplier</option>
                            </select>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-2 col-xs-12">
                      <button type="button" class="btn btn-sm btn-default" name="btn-generate" id="search" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing..."> Filter </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-xs-12 table-responsive">
              <table id="example1" class="table table-striped">
                <thead>
                  <tr>
                    <th class="no">No</th>
                    <th>Name</th>
                    <th>Buyer Code</th>
                    <th>Invoice Street</th>
                    <th>Invoice City</th>
                    <th>Invoice State</th>
                    <th>Invoice Country</th>
                    <th>Invoice Zip</th>
                    <th>Partner</th>
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

    <?php $this->load->view("admin/_partials/modal.php") ?>
  </div>

  <?php $this->load->view("admin/_partials/js.php") ?>

  <script type="text/javascript">
    var table;
    $(document).ready(function() {
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
          "url": "<?php echo site_url('sales/partner/get_data') ?>",
          "type": "POST",
          "data": function(d) {
            d.partner = $("#partner").val();
            d.type = $("#type").val();
          }
        },

        "columnDefs": [{
          "targets": [0, 8],
          "orderable": false,
        }, ],
      });
      $("#search").on("click", function() {
        table.ajax.reload();
      });
    });
  </script>

</body>

</html>