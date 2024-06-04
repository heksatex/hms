<!DOCTYPE html>
<html lang="en">
<head>
  <?php $this->load->view("admin/_partials/head.php") ?>

  <style type="text/css">
      .limit_capt {
        padding :0px !important;
        width: 0%;
      }

      @media (min-width: 300px) {
        .btn-style-proc {
         padding-left: 30px !important;
        }
      }
  </style>

</head>

<body class="hold-transition skin-black fixed sidebar-mini">
<!-- Site wrapper -->
<div class="wrapper">
  <!-- main -header -->
  <header class="main-header">
    <?php $this->load->view("admin/_partials/main-menu.php") ?>
    <?php
      $data['deptid']     = $id_dept;
      $data['hms_top']    = 'empty';// menghilangkan top bar tulisan HMS saat mode HP
      $this->load->view("admin/_partials/topbar.php",$data)
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
            
            <form name="input" class="form-horizontal" role="form" >
                <div class="form-group"> 
                    <div class="col-md-8">
                        <div class="col-md-10 col-xs-12">
                            <div class="col-xs-12">
                            <input type="text" class="form-control input-lg" name="txtlot" id="txtlot" placeholder="Scan Barcode / Lot" />
                            </div>                                    
                        </div>
                        <div class="col-xs-4 col-md-1 btn-style-proc " >
                            <button type="button" id="btn-proses" name="submit" class="btn btn-primary btn-lg" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing..."><i class="fa fa-barcode"></i> Proses</button>
                        </div>
                    </div>
                   
                </div>
            </form>
            
            <div class="col-md-10">
                <div class="box box-danger ">
                <div class="box-header with-border">
                  <h3 class="box-title"><label>Informasi Barcode / Lot</label></h3>
                  <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                    </button>
                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i>
                    </button>
                  </div>
                </div>
                <div class="box-body" style="display: block;">
                
                <form name="input" class="form-horizontal" role="form">
                    <div class="col-md-6">
                        <div class="form-group"> 
                            <div class="col-md-12 col-xs-12">
                                <div class="col-xs-4"><label>Barcode / Lot</label></div>
                                <div class="col-xs-1 limit_capt"><label>:</label></div>
                                <div class="col-xs-8" id="lot">
                                </div>
                            </div>
                        </div> 
                        <div class="form-group"> 
                            <div class="col-md-12 col-xs-12">
                                <div class="col-xs-4"><label>Tanggal dibuat</label></div>
                                <div class="col-xs-1 limit_capt"><label>:</label></div>
                                <div class="col-xs-8" id="tgl_dibuat">
                                </div>
                            </div>
                        </div> 
                        <div class="form-group"> 
                            <div class="col-md-12 col-xs-12">
                                <div class="col-xs-4"><label>Kode Produk</label></div>
                                <div class="col-xs-1 limit_capt"><label>:</label></div>
                                <div class="col-xs-8" id="kode_produk">

                                </div>
                            </div>
                        </div> 
                        <div class="form-group"> 
                            <div class="col-md-12 col-xs-12">
                                <div class="col-xs-4"><label>Nama Produk </label></div>
                                <div class="col-xs-1 limit_capt"><label>:</label></div>
                                <div class="col-xs-8" id="nama_produk">
                                </div>
                            </div>
                        </div> 
                        <div class="form-group"> 
                            <div class="col-md-12 col-xs-12">
                                <div class="col-xs-4"><label>Corak remark </label></div>
                                <div class="col-xs-1 limit_capt"><label>:</label></div>
                                <div class="col-xs-8" id="corak_remark">
                                </div>
                            </div>
                        </div>
                        <div class="form-group"> 
                            <div class="col-md-12 col-xs-12">
                                <div class="col-xs-4"><label>Wana remark </label></div>
                                <div class="col-xs-1 limit_capt"><label>:</label></div>
                                <div class="col-xs-8" id="warna_remark">
                                </div>
                            </div>
                        </div> 
                        <div class="form-group"> 
                            <div class="col-md-12 col-xs-12">
                                <div class="col-xs-4"><label>Grade </label></div>
                                <div class="col-xs-1 limit_capt"><label>:</label></div>
                                <div class="col-xs-8" id="grade">
                                </div>
                            </div>
                        </div> 
                    </div>
                    <div class="col-md-6">
                        <div class="form-group"> 
                            <div class="col-md-12 col-xs-12">
                                <div class="col-xs-4"><label>Qty1 [HPH]</label></div>
                                <div class="col-xs-1 limit_capt"><label>:</label></div>
                                <div class="col-xs-8" id="qty">
                                </div>
                            </div>
                        </div> 
                        <div class="form-group"> 
                            <div class="col-md-12 col-xs-12">
                                <div class="col-xs-4"><label>Qty2 [HPH]</label></div>
                                <div class="col-xs-1 limit_capt"><label>:</label></div>
                                <div class="col-xs-8" id="qty2">
                                </div>
                            </div>
                        </div> 
                        <div class="form-group"> 
                            <div class="col-md-12 col-xs-12">
                                <div class="col-xs-4"><label>Qty1 [JUAL] </label></div>
                                <div class="col-xs-1 limit_capt"><label>:</label></div>
                                <div class="col-xs-8" id="qty_jual">
                                </div>
                            </div>
                        </div> 
                        <div class="form-group"> 
                            <div class="col-md-12 col-xs-12">
                                <div class="col-xs-4"><label>Qty2 [JUAL]</label></div>
                                <div class="col-xs-1 limit_capt"><label>:</label></div>
                                <div class="col-xs-8" id="qty2_jual">
                                </div>
                            </div>
                        </div> 
                        <div class="form-group"> 
                            <div class="col-md-12 col-xs-12">
                                <div class="col-xs-4"><label>Lokasi dept </label></div>
                                <div class="col-xs-1 limit_capt"><label>:</label></div>
                                <div class="col-xs-8" id="lokasi">
                                </div>
                            </div>
                        </div> 
                        <div class="form-group"> 
                            <div class="col-md-12 col-xs-12">
                                <div class="col-xs-4"><label>Lokasi Fisik </label></div>
                                <div class="col-xs-1 limit_capt"><label>:</label></div>
                                <div class="col-xs-8" id="lokasi_fisik">
                                </div>
                            </div>
                        </div> 
                        <div class="form-group"> 
                            <div class="col-md-12 col-xs-12">
                                <div class="col-xs-4"><label>Reff Note </label></div>
                                <div class="col-xs-1 limit_capt"><label>:</label></div>
                                <div class="col-xs-8" id="reff_note">
                                </div>
                            </div>
                        </div> 
                        <div class="form-group"> 
                            <div class="col-md-12 col-xs-12">
                                <div class="col-xs-4"><label>Jml Barcode </label></div>
                                <div class="col-xs-1 limit_capt"><label>:</label></div>
                                <div class="col-xs-8" id="jml">
                                </div>
                            </div>
                        </div> 
                    </div>
                    <div class="col-md-12">
                        <p style="font-size:10px; color:red">* Jika Jml Barcode > 1 Cek Informasi Barcode lebih lanjut di Stock Quant</p>
                    </div>
                </form>

                </div>
          
              </div>
            </div>
            <!-- ./box informasi barcode -->
            <div class="col-md-10">
                <div class="box box-primary ">
                <div class="box-header with-border">
                  <h3 class="box-title"><label><i class="fa fa-flag-checkered"></i> Tracking Proses</label></h3>
                  <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                    </button>
                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i>
                    </button>
                  </div>
                </div>
                <div class="box-body" style="display: block;">
                <div class="col-xs-12 table-responsive">
                    <table id="example1" class="table">
                    <thead>
                        <tr>
                        <th class='style no'>No</th>
                        <th class='style'>Tanggal</th>
                        <th class='style'>Kode</th>
                        <th class='style'>Keterangan</th>
                        <th class='style'>Status</th>
                        <th class='style'>User</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                           <td colspan="6" align="center">Tidak ada Data</td>
                        </tr>
                    </tbody>
                    </table>
                    <div id="example1_processing" class="table_processing" style="display: none">
                        Processing...
                    </div>
                </div>

                </div>
              </div>
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

    $(document).on("click", "#btn-proses", function(){
        proses();
    });

    $('#txtlot').focus();

    $('#txtlot').keydown(function(event){
        if(event.keyCode == 13) {
           event.preventDefault();
            proses();
          //return false;
        }
    });

    function proses(){
        let txtlot = $("#txtlot").val();   
        // kosongkan informasi
        $('#lot').text('');
        $('#tgl_dibuat').text('');
        $('#kode_produk').text('');
        $('#nama_produk').text('');
        $('#corak_remark').text('');
        $('#warna_remark').text('');
        $('#grade').text('');
        $('#qty').text('');
        $('#qty2').text('');
        $('#qty_jual').text('');
        $('#qty2_jual').text('');
        $('#lokasi').text('');
        $('#lokasi_fisik').text('');
        $('#reff_note').text('');
        $('#jml').text('');
        
        if(txtlot == ''){
            alert_notify('fa fa-warning','Barcode / Lot tidak boleh kosong !','danger',function(){});
            $("#example1 tbody").remove();
            $('#txtlot').focus();
            $("#example1").append("<tr><td colspan='6' align='center'>Tidak Ada Data</td></tr>");
        }else{
            $('#btn-proses').button('loading');
            $("#example1_processing").css('display','');// show loading processing in table
           
            $.ajax({
            type     : "POST",
            dataType : "json",
            url :'<?php echo base_url('warehouse/trackinglot/search_barcode')?>',
            beforeSend: function(e) {
                if(e && e.overrideMimeType) {
                    e.overrideMimeType("application/json;charset=UTF-8");
                }
                $("#example1 tbody").remove();
               
            },
            data: {txtlot : txtlot},
            success: function(data){
                if(data.sesi == "habis"){
                    //alert jika session habis
                    alert_modal_warning(data.message);
                    window.location.replace('index');
                }else{
                    empty = true;
                    $.each(data.info, function(key, value) {
                        empty  = false;
                        $('#lot').text(value.lot);
                        $('#tgl_dibuat').text(value.tgl_dibuat);
                        $('#kode_produk').text(value.kode_produk);
                        $('#nama_produk').text(value.nama_produk);
                        $('#corak_remark').text(value.corak_remark);
                        $('#warna_remark').text(value.warna_remark);
                        $('#grade').text(value.grade);
                        $('#qty').text(value.qty);
                        $('#qty2').text(value.qty2);
                        $('#qty_jual').text(value.qty_jual);
                        $('#qty2_jual').text(value.qty2_jual);
                        $('#lokasi').text(value.lokasi);
                        $('#lokasi_fisik').text(value.lokasi_fisik);
                        $('#reff_note').text(value.reff_note);
                        $('#jml').text(value.jml);
                        //alert(value.lot)
                    });


                    var tbody = $("<tbody />");
                    var no    = 1;
                    var empty = true;
                    $.each(data.record, function(key, value) {
                        empty = false;
                        link = value.kode;
                        if(value.link != ''){
                            link = '<a href="<?=base_url()?>'+value.link+'" target="_blank" data-togle="tooltip" title="Cek Details">'+value.kode+'</a>';
                        }
                        var tr = $("<tr>").append(
                          $("<td>").text(no++),
                          $("<td>").text(value.tanggal),
                          $("<td>").html(link),
                          $("<td>").text(value.keterangan),
                          $("<td>").text(value.status),
                          $("<td>").text(value.user),

                        );
                       tbody.append(tr);
                    });

                    if(empty == true){
                      var tr = $("<tr>").append($("<td colspan='6' align='center'>").text('Tidak ada Data'));
                      tbody.append(tr);
                    }

                    $("#example1").append(tbody);

                    setTimeout(function() { alert_notify(data.icon,data.message,data.type,function(){});},1000); 
                    $('#txtlot').val('');
                    $('#txtlot').focus();
                    $("#example1_processing").css('display','none');// hidden loading processing in table

                }
                $('#btn-proses').button('reset');

                },error: function (xhr, ajaxOptions, thrownError) {
                    alert(xhr.responseText);
                    $('#btn-proses').button('reset');
                    $("#example1_processing").css('display','none');// hidden loading processing in table
                }
            });

        }
    }
 
</script>

</body>
</html>
