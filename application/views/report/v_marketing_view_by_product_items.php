
<!DOCTYPE html>
<html lang="en">
<head>
  <?php $this->load->view("admin/_partials/head.php") ?>
  <link href="<?= base_url('dist/css/light-box.css') ?>" rel="stylesheet">
  <style type="text/css">
    
    h3{
      display: block !important;
      text-align: center !important;
    }

    .ws{
      white-space: nowrap;
    }

    @media (max-width: 767px) {
      .top-bar{
        display:none !important;
      }
      .empty_mb{
        margin-bottom : 0px;
      }
      .content-wrapper{
        padding-top : 50px !important;
        margin-top  : 10px !important;
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
      <div class="box ">
        <div class="box-header with-border">
          <h3 class="box-title"><b>View By Product</b></h3>
        </div>
        <div class="box-body ">
              <form name="input" class="form-horizontal" role="form">
                      <div class="col-md-12">
                        <div class="row col-md-6">
                          <div class="form-group empty_mb"> 
                              <div class="col-md-12 col-xs-12">
                                  <div class="col-xs-4"><label>Product / Corak</label></div>
                                  <div class="col-xs-8"><label>:</label> <?php echo $product; ?></div>
                              </div>
                              <div class="col-md-12 col-xs-12">
                                  <div class="col-xs-4"><label>Warna</label></div>
                                  <div class="col-xs-8"><label>:</label> <?php echo $color; ?></div>
                              </div>
                              <div class="col-md-12 col-xs-12">
                                  <div class="col-xs-4"><label>Marketing</label></div>
                                  <div class="col-xs-8"><label>:</label> <?php echo $nama_mkt; ?></div>
                              </div>
                          </div> 
                        </div>
                        <div class="row col-md-6">
                          <div class="form-group"> 
                              <div class="col-md-12 col-xs-12">
                                  <div class="col-xs-4"><label>Lebar Jadi</label></div>
                                  <div class="col-xs-8"><label>:</label> <?php echo $lebar_jadi; ?></div>
                              </div>
                              <div class="col-md-12 col-xs-12">
                                  <div class="col-xs-4"><label>Uom</label></div>
                                  <div class="col-xs-8"><label>:</label> <?php echo $uom_jual; ?></div>
                              </div>
                              <div class="col-md-12 col-xs-12">
                                  <div class="col-xs-4"><label>Lot/KP Asal</label></div>
                                  <div class="col-xs-8"><label></label> <input type="checkbox" name="lot_asal" id="cek_asal" ></div>
                              </div>
                          </div>
                        </div>
                      </div>
                      <div class="col-md-12">
                        <div class=" row col-md-6">
                          <div class="form-group">
                              <div class="col-md-12 col-xs-12">
                                  <div class="col-xs-4"><label>Total Lot</label></div>
                                  <div class="col-xs-8"  id="total_items"><label>:</label> 0 Lot </div>
                              </div>
                          </div>
                        </div>
                        <div class=" col-md-6">
                          <div class="form-group">
                              <div class="col-md-12 col-xs-12">
                                 <button type="button" class="btn btn-sm btn-default" name="btn-excel" id="btn-excel" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing..."> <i class="fa fa-file-excel-o" style="color:green"></i> Excel</button>
                              </div>
                          </div>
                        </div>
                      </div>
                
                </form>

                <div class="row">
                    <div class="col-md-12">
                        <!-- Tabel  -->
                        <div class="col-md-12 table-responsive over">
                            <table class="table table-condesed table-hover rlstable over" width="100%" id="table_items" >
                                <thead>                          
                                    <tr>
                                        <th class="style width-50">No.</th>
                                        <th class="style">Gambar</th>
                                        <th class="style ">Kode Produk</th>
                                        <th class="style ">Lot</th>
                                        <th class="style ">Corak</th>
                                        <th class="style ">Warna</th>
                                        <th class="style ws">Lebar Jadi</th>
                                        <th class="style text-right">Qty1 [JUAL]</th>
                                        <th class="style text-right">Qty2 [JUAL]</th>
                                        <th class="style ws">Lokasi Fisik / Rak</th>
                                        <th class="style ws">Lot/KP</th>
                                        <th class="style ws">SO/SC</th>
                                        <th class="style ws">Picklist (PL)</th>
                                        <th class="style ws">Umur (Hari)</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                    <!-- /.col -->
                </div>
                <!-- /.row -->
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
<script src="<?= base_url('dist/js/light-box.min.js') ?>"></script>

<script type="text/javascript">
    var table;
    $(document).ready(function() {

        var zoom_percent = "100";

        //datatables
        table = $('#table_items').DataTable({ 
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
                "url": "<?php echo site_url('report/marketing/get_data_stock_by_product_items')?>",
                "type": "POST",
                "data": function (d) {
                        d.product = "<?php echo $product;?>";
                        d.color   = "<?php echo $color; ?>";
                        d.marketing = "<?php echo $mkt?>";
                        d.lebar_jadi =  "<?php echo $lebar_jadi;?>";
                        d.uom_jual = "<?php echo $uom_jual?>";
                        d.lot_asal =  $("#cek_asal").is(':checked')
                }
            },
           
            "columnDefs": [
              { 
                "targets": [0,1,13], 
                "orderable": false, 
              },
              {
                "targets" : [2],
                "visible" : false
              },
              { 
                "targets": [7,8], 
                "className":"text-right nowrap",
              },
              { 
                "targets": [4,5], 
                "className":"nowrap",
              },
              { 
                "targets": [1], 
                // "data": "img",
                "render" : function ( url, type, img) {
                    var baseUrl = img[1];
                    var default_val = 'false';
                    if(baseUrl.includes('default') == true){
                      default_val = 'true';
                    }
                    // link = 
                    data = '<a class="image-popup" href="'+img[1]+'" title="'+img[4]+' - '+img[5]+'" data-produk ="'+img[2]+'" default="'+default_val+'"><img height="50px" width="50px" src="'+img[1]+'"/></a>';
                    // return '<img height="30%" width="30%" src="'+img[1]+'"/>';
                    // return img[1];
                    return data;
                }
              },
            ],
            "drawCallback": function( settings, start, end, max, total, pre ) {  
                // console.log(this.fnSettings()); /* for json response you can use it also*/ 
                let total_record = this.fnSettings().json.recordsTotal;
                $('#total_items').html('<label>:</label> '+ formatNumber(total_record) + ' Lot' )

                $('.image-popup').magnificPopup({
                    type: 'image',
                    removalDelay: 300,
                    mainClass: 'mfp-fade',
                    gallery: {
                        enabled: false
                    },
                    image: {
                        verticalFit: true,
                        titleSrc: function(item) {
                          
                          var caption = item.el.attr('title');
                          var produk  = item.el.attr('data-produk');
                          var default_val = item.el.attr('default');
                          if(default_val == 'false'){
                            return caption + ' &middot; <button type="button" class="btn btn-xs btn-default btn-download" id="btn-download" data-produk="'+produk+'" data-title="'+caption+'">download me</button>';
                          }else{
                            return caption + ' &middot';
                          }
                          
                        },
                       
			              },
                    zoom: {
                        enabled: true,
                        duration: 300,
                        easing: 'ease-in-out',
                        opener: function (openerElement) {
                        return openerElement.is('img') ? openerElement : openerElement.find('img');
                        }
                    },
                    callbacks: {
                      open: function(item) {
                        $(".mfp-figure figure .mfp-img").css("cursor", "zoom-in");
                        zoom(zoom_percent);
                        $(".btn-download").unbind( "click" );
                        this.wrap.on('click.pinhandler', '.btn-download', function(e) {
                          console.log($(this).attr('data-produk'));
                          const produk = $(this).attr('data-produk');
                          const title  = $(this).attr('data-title');

                          $.ajax({
                              "type":'POST',
                              "url": "<?php echo site_url('report/Marketing/download_image')?>",
                              //"dataType":'json',
                              "data"  : {"produk":produk, "caption" : title},
                              xhrFields: {
                                  responseType: 'blob'
                              },error: function(){
                                alert('Error');
                              }
                          }).done(function(data){
                              if(data.status =="failed"){
                                alert_modal_warning(data.message);
                              }else{

                                  var url = window.URL.createObjectURL( data )
                                  var anchorElem = document.createElement( "a" );
                                  anchorElem.style.display = "none";
                                  anchorElem.href = url;
                                  anchorElem.download = title+".jpg";
                                  $("body").append( anchorElem );
                                  anchorElem.click();
                                  // clean-up
                                  window.URL.revokeObjectURL( url );
                              }
                              $('#btn-excel').button('reset');
                          });
                        });
                      },
                      beforeClose: function() {
                        //this.wrap.off('click.pinhandler');
                      }
                    },
                });
            },
        });

        function zoom(zoom_percent){
            $(".mfp-figure figure .mfp-img").click(function(){
                switch(zoom_percent){
                    case "100":
                        zoom_percent = "120";
                        break;
                    case "120":
                        zoom_percent = "150";
                        break;
                    case "150":
                        zoom_percent = "200";
                        $(".mfp-figure figure .mfp-img").css("cursor", "zoom-out");
                        break;
                    case "200":
                        zoom_percent = "100";
                        $(".mfp-figure figure .mfp-img").css("cursor", "zoom-in");
                        break;
                }
                $(this).css("zoom", zoom_percent+"%");
            });
        }
        
        $('#cek_asal').change(function(){ //button filter event click
          table.ajax.reload( function(){ });  //just reload table
        });
 
    });

    function formatNumber(n) {
      return new Intl.NumberFormat('en-US').format(n);
    }

    // button excel
    $('#btn-excel').click(function(){
        $.ajax({
            "type":'POST',
            "url": "<?php echo site_url('report/Marketing/export_excel_view_by_product')?>",
            "data":  {"product": "<?php echo $product;?>", "color":"<?php echo $color; ?>", "marketing":"<?php echo $mkt?>", "lebar_jadi" : "<?php echo $lebar_jadi;?>", "uom_jual":"<?php echo $uom_jual?>", "lot_asal" : $("#cek_asal").is(':checked')},
            "dataType":'json',
            beforeSend: function() {
              $('#btn-excel').button('loading');
            },error: function(){
              alert('Error Export Excel');
              $('#btn-excel').button('reset');
            }
        }).done(function(data){
            if(data.status =="failed"){
              alert_modal_warning(data.message);
            }else{
              var $a = $("<a>");
              $a.attr("href",data.file);
              $("body").append($a);
              $a.attr("download",data.filename);
              $a[0].click();
              $a.remove();
            }
            $('#btn-excel').button('reset');
        });
    });

</script>

</body>
</html>
