
<!DOCTYPE html>
<html lang="en">
<head>
  <?php $this->load->view("admin/_partials/head.php") ?>
  <style>
    .btn-machine{
      margin:2px;
      /* border-radius:20px; */
      padding:5px 15px;
      transition:.2s;
    }
    .btn-machine.active{
        background:#337ab7;
        color:#FFF;
        border-color:#337ab7;
    }
    .btn-machine.active,
    .btn-machine.active:hover,
    .btn-machine.active:focus,
    .btn-machine.active:active{
        background:#337ab7;
        color:#fff;
        border-color:#337ab7;
    }
    .machine-wrapper{
        overflow-x:auto;
        white-space:nowrap;
        padding-bottom:5px;
    }
    .machine-wrapper::-webkit-scrollbar{
        height:6px;
    }
    .machine-wrapper::-webkit-scrollbar-thumb{
        background:#CCC;
        border-radius:20px;
    }

    .btn-machine:focus,
    .btn-machine.focus{
        background:#fff;
        color:#333;
        border-color:#ccc;
        outline:none;
        box-shadow:none;
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
        <div class="box-header with-border">
          <h3 class="box-title"><b>LIST MODE</b></h3>
          <div class="image pull-right text-right">
              <?php 
                $dept = "jadwal_".$this->uri->segment(3); 
              ?>
              <!--a href="<?php echo base_url("manufacturing/mO/".$dept);?>"  data-toggle="tooltip" title="Kanban Mode">
                <img src="<?php echo base_url('dist/img/kanban.png'); ?>" style="width: 7%; height: auto; text-align: right;" >
              </a-->
          </div>
        </div>
        <div class="box-body">
          <div class="panel panel-default" style="margin-bottom:10px;  margin-left:10px; margin-right:10px;">
              <div class="panel-body" style="padding:10px;">
                  <label style="margin-right:15px;">
                      <b>Mesin :</b>
                  </label>
                  <div class="machine-wrapper">
                    <div id="machine-filter">
                      <div id="machine-filter" style="display:inline-block;">
                        <button
                            class="btn btn-default btn-xs btn-machine active"
                            data-machine="">
                            Semua
                        </button>
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
                  <th>Kode</th>
                  <th>Tanggal</th>
                  <th>Origin</th>
                  <th>Product</th>
                  <th>qty</th>
                  <th>uom</th>
                  <th>Mesin</th>
                  <th>reff note PPIC</th>
                  <th>responsible</th>
                  <th>status</th>
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
    var machine_selected = "";
    var id_dept = "<?php echo $id_dept;?>";

    var MachineStorage = {
          key : "selected_machine_" + id_dept,
          get : function(){
              return localStorage.getItem(this.key) || "";
          },
          set : function(value){
              localStorage.setItem(this.key, value);
          },
          clear : function(){
              localStorage.removeItem(this.key);
          }

    };
    var storageKey = "selected_machine_" + id_dept;
    machine_selected = MachineStorage.get();
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
                "url": "<?php echo site_url('manufacturing/mO/get_data')?>",
                "type": "POST",
                data: function(d){
                    d.id_dept = "<?php echo $id_dept;?>";
                    d.mc_id   = machine_selected;
                }
            },
 
            "columnDefs": [
              { 
                  "targets": [ 0 ], 
                  "orderable": false, 
              },
              {
                "className" : "text-right",
                 render: $.fn.dataTable.render.number(',', '.', 2, ''),
                "targets"   : [5]
              },
              {
                "targets" : 8,
                 render: function (data, type, full, meta) {
                        return "<div class='text-wrap width-300'>" + data + "</div>";
                }
              },
            ], 
        });

      
 
    });

    loadMachineButton();

      function loadMachineButton() {
          $.ajax({
              data: {
                  "id_dept": "<?php echo $id_dept;?>"
              },
              url: "<?php echo site_url('manufacturing/mO/getMachine')?>",
              dataType: 'json',
              success: function(res) {
                  // reset semua active
                  $(".btn-machine").removeClass("active");
                  // kalau tidak ada yang dipilih, aktifkan "Semua"
                  if (machine_selected == "" || machine_selected == null) {
                      $(".btn-machine[data-machine='']").addClass("active");
                  }
                  $("#machine-filter").append(
                      '<button class="btn btn-default btn-xs btn-machine" data-machine="EMPTY">Mesin Kosong</button>'
                  );
                  $.each(res, function(i, row) {
                      var active = "";
                      if (row.mc_id == machine_selected) {
                          active = "active";
                      }
                      $("#machine-filter").append('<button class="btn btn-default btn-xs btn-machine ' + active + '" data-machine="' + row.mc_id + '">' + row.nama_mesin + '</button>');
                  });
                  table.ajax.reload();
              }
          });
      }

      $(document).on("click", ".btn-machine", function() {
          $(".btn-machine").removeClass("active");
          $(this).addClass("active");
          machine_selected = $(this).data("machine");
          MachineStorage.set(machine_selected);
          table.ajax.reload( function(){});  //just reload table
      });

   

 
</script>

</body>
</html>
