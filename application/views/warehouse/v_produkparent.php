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
                                <div class="col-xs-5"><label>Nama Parent</label></div>
                                <div class="col-xs-7">
                                  <input type="text" class="form-control input-sm" name="nama_parent" id="nama_parent" >
                                </div>                                    
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-group"> 
                                <div class="col-xs-5"><label>Status</label></div>
                                <div class="col-xs-7">
                                  <select class="form-control input-sm" name="status" id="status">
                                      <?php 
                                          $arr_status = array(array('value' => '', 'text' => ''),array('value' => 't', 'text' => 'Aktif'), array( 'value'=> 'f', 'text' => 'Tidak Aktif'));
                                          foreach ($arr_status as $val) {
                                           ?>
                                              <option value="<?php echo $val['value']; ?>" ><?php echo $val['text'];?></option>
                                            <?php  
                                          }
                                      ?>
                                  </select>  
                                </div>                                    
                              </div>
                            </div>
                            <div class="col-md-6" >
                              <div class="form-group" >
                                  <div class="col-xs-8" style="padding-top:0px">
                                      <button type="button" id="btn-filter" name="submit" class="btn btn-primary btn-sm" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing...">Proses</button>
                                      <button type="button" id="btn-excel" name="excel" class="btn btn-success btn-sm" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing...">
                                        <i class="fa fa-file-excel-o"></i>
                                      Excel</button>
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
            <table id="example1" class="table table-striped">
              <thead>
                <tr>
                  <th class="no">No</th>
                  <th>Nama</th>
                  <th>Tanggal dibuat</th>
                  <th>Jumlah Child</th>
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
            "aLengthMenu": [[50, 100, 1000, -1], [50, 100, 1000, "All"]],
            "iDisplayLength": 50,
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
                "url": "<?php echo site_url('warehouse/produkparent/get_data')?>",
                "type": "POST",
                "data": function ( data ) {
                    data.nama_parent  = $('#nama_parent').val();
                    data.status       = $('#status').val();
                },error: function() {
                    // Message also does not show here
                    alert("error Load");
                    $('#btn-filter').button('reset');
                },
            },
            "columnDefs": [
                { 
                    "targets": [ 0 ], 
                    "orderable": false, 
                },
                { 
                  "targets": [ 2 ], 
                  "width" : 150,
                },
                { 
                  "targets": [ 3 ], 
                  "width" : 150,
                },
            ],
        });
 
    });

    $('#btn-filter').click(function(){ //button filter event click
        $('#btn-filter').button('loading');
        table.ajax.reload( function(){
          $('#btn-filter').button('reset');
        });  //just reload table
      });

      $('#nama_parent').keydown(function(event){
          if(event.keyCode == 13) {
          event.preventDefault();
          $('#btn-filter').button('loading');
              table.ajax.reload( function(){
                $('#btn-filter').button('reset');
           });
          }
     });

     $('#btn-tambah').on("click",function(e) {
       
        $("#tambah_data").modal({
            show: true,
            backdrop: 'static'
        });
        $("#tambah_data .modal-dialog .modal-content .modal-footer #btn-tambah").attr('id',"btn-tambah-parent");
        $(".tambah_data").html('<center><h5><img src="<?php echo base_url('dist/img/ajax-loader.gif') ?> "/><br>Please Wait...</h5></center>');
        $('.modal-title').text('Add Product Parent' );
        $.post('<?php echo site_url()?>warehouse/produkparent/add_parent_produk',
            function(html){
              setTimeout(function() {$(".tambah_data").html(html); },1000);
            }   
         ); 
    });

    function view_parent(id)
    {
        $("#edit_data2").modal({
            show: true,
            backdrop: 'static'
        });
        $(".edit_data2").html('<center><h5><img src="<?php echo base_url('dist/img/ajax-loader.gif') ?> "/><br>Please Wait...</h5></center>');
        $('.modal-title').text('View Product Parent' );
        $.post('<?php echo site_url()?>warehouse/produkparent/view_parent_produk',
            {id:id},
            function(html){
              setTimeout(function() {$(".edit_data2").html(html);  },1000);
            }   
         ); 
    }

    $(".modal").on('hidden.bs.modal', function(){
        $("#tambah_data .modal-dialog .modal-content .modal-footer #btn-tambah-parent").attr('id',"btn-tambah");
        //$("#tambah_data .modal-dialog .modal-content .tambah_data").html('');
        table.ajax.reload( function(){});
    });


    $('#btn-excel').click(function(){
      $.ajax({
          "type":'POST',
          "url": "<?php echo site_url('warehouse/produkparent/export_excel_parent')?>",
          "data": {nama:$('#nama_parent').val(), status:$('#status').val(), filter:$('input[type="search"]').val(),},
          "dataType":'json',
          beforeSend: function() {
            $('#btn-excel').button('loading');
          },error: function(){
            $('#btn-excel').button('reset');
          }
      }).done(function(data){
          var $a = $("<a>");
          $a.attr("href",data.file);
          $("body").append($a);
          $a.attr("download",data.filename);
          $a[0].click();
          $a.remove();
          $('#btn-excel').button('reset');
      });
    });
 
</script>

</body>
</html>
