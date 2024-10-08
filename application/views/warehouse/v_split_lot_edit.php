<!DOCTYPE html>
<html>
<head>
  <?php $this->load->view("admin/_partials/head.php") ?>
  <style>
    button[id="btn-generate"]{/*untuk hidden button generate*/
        display: none;
    }
    <?php if($split->dept_id != 'GJD'){ ?>
              button[id="btn-print"]{/*untuk hidden button generate*/
                display: none;
              }
    <?php } ?>
    .select2-container--focus{
	    border:  1px solid #66afe9;
    }

    .select2-container--default .select2-selection--single{
        height : 30px;
        font-size : 12px;
        padding: 5px 12px;
    }
  </style>
</head>

<body class="hold-transition skin-black fixed sidebar-mini" > 
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
     <?php $this->load->view("admin/_partials/statusbar.php") ?>
    </section>

    <!-- Main content -->
    <section class="content">

      <!--  box content -->
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title">Form Split Lot : <b><?php echo $split->kode_split;?></b> </h3>
        </div>

        <div class="box-body">
            <form class="form-horizontal">
             
                <div class="form-group">

                    <div class="col-md-6">
                        <div class="col-md-12 col-xs-12">
                            <div class="col-xs-4"><label>Kode  </label></div>
                            <div class="col-xs-8">
                                <input type="text" class="form-control input-sm" name="kode" id="kode"  readonly="readonly" value="<?php echo $split->kode_split;?>"/>                    
                            </div>                                    
                        </div>
                        <div class="col-md-12 col-xs-12">
                            <div class="col-xs-4"><label>Tanggal dibuat</label></div>
                            <div class="col-xs-8 col-md-8">
                                <input type='text' class="form-control input-sm " name="tgl_buat" id="tgl_buat" readonly="readonly" value="<?php echo $split->tanggal; ?>"  />
                            </div>                                    
                        </div>

                        <div class="col-md-12 col-xs-12" >
                            <div class="col-xs-4"><label>Departemen</label></div>
                            <div class="col-xs-8">
                                <input type='text' class="form-control input-sm " name="dapartemen" id="dapartemen" readonly="readonly" value="<?php echo $split->nama_departemen; ?>"  />
                            </div>                                    
                        </div>
                        <div class="col-md-12 col-xs-12" style="padding-bottom:10px;">
                            <div class="col-xs-4"><label>Note </label></div>
                            <div id="ta" class="col-xs-8">
                                <textarea class="form-control input-sm" name="note" id="note" ><?php echo htmlentities($split->note);?></textarea>
                            </div>                                    
                        </div>
                    </div>

                    <div class="col-md-6" >
                        <div class="col-md-12 col-xs-12">
                            <?php if($split->dept_id == 'GJD'){?>
                                    <div class="col-xs-4"><label>Marketing </label></div>
                                    <div class="col-xs-8">
                                        <input type="text" class="form-control input-sm" name="nama_sales_group" id="nama_sales_group"  value="<?php echo $split->nama_sales_group; ?>" readonly>                    
                                    </div>                                    
                             <?php } ?>
                            <div class="col-xs-4"><label>Kode Produk  </label></div>
                                <div class="col-xs-8">
                                    <input type="hidden" class="form-control input-sm" name="quant_id" id="quant_id"  value="<?php echo $split->quant_id; ?>" readonly >  
                                    <input type="text" class="form-control input-sm" name="kode_produk" id="kode_produk"  value="<?php echo $split->kode_produk; ?>" readonly >                    
                                </div>                                    
                            </div>

                            <div class="col-md-12 col-xs-12">
                                <div class="col-xs-4"><label>Nama Produk  </label></div>
                                <div class="col-xs-8">
                                    <input type="text" class="form-control input-sm" name="nama_produk" id="nama_produk"   value="<?php echo htmlentities($split->nama_produk); ?>" readonly>                    
                                </div>                                    
                            </div>
                            <?php if($split->dept_id == 'GJD'){?>
                            <div class="col-md-12 col-xs-12">
                                <div class="col-xs-4"><label>Corak Remark  </label></div>
                                <div class="col-xs-8">
                                    <input type="text" class="form-control input-sm" name="corak_remark" id="corak_remark"  value="<?php echo $split->corak_remark; ?>"  readonly >                    
                                </div>                                    
                            </div>
                            <div class="col-md-12 col-xs-12">
                                <div class="col-xs-4"><label>Warna Remark  </label></div>
                                <div class="col-xs-8">
                                    <input type="text" class="form-control input-sm" name="warna_remark" id="warna_remark"  value="<?php echo $split->warna_remark; ?>"  readonly >                    
                                </div>                                    
                            </div>
                            <?php } ?>
                            <div class="col-md-12 col-xs-12">
                                <div class="col-xs-4"><label>Barcode/Lot  </label></div>
                                <div class="col-xs-8">
                                    <input type="text" class="form-control input-sm" name="lot" id="lot"  value="<?php echo $split->lot; ?>"  readonly >                    
                                </div>                                    
                            </div>

                            <div class="col-md-12 col-xs-12">
                                <div class="col-xs-4"><label>Qty1  </label></div>
                                <div class="col-xs-5">
                                    <input type="text" class="form-control input-sm" name="qty" id="qty"  value="<?php echo $split->qty; ?>"  readonly>                    
                                </div> 
                                <div class="col-xs-3">
                                    <input type="text" class="form-control input-sm" name="uom_qty" id="uom_qty"  value="<?php echo $split->uom; ?>" readonly >                    
                                </div>                                    
                            </div>

                            <div class="col-md-12 col-xs-12">
                                <div class="col-xs-4"><label>Qty2  </label></div>
                                <div class="col-xs-5">
                                    <input type="text" class="form-control input-sm" name="qty2" id="qty2"  value="<?php echo $split->qty2; ?>" readonly>                    
                                </div> 
                                <div class="col-xs-3">
                                    <input type="text" class="form-control input-sm" name="uom_qty2" id="uom_qty2"  value="<?php echo $split->uom2; ?>" readonly>                    
                                </div>                                    
                            </div>
                            <?php if($split->dept_id == 'GJD'){?>
                            <div class="col-md-12 col-xs-12">
                                <div class="col-xs-4"><label>Qty1 Jual  </label></div>
                                <div class="col-xs-5">
                                    <input type="text" class="form-control input-sm" name="qty_jual" id="qty_jual"  value="<?php echo $split->qty_jual; ?>"  readonly>                    
                                </div> 
                                <div class="col-xs-3">
                                    <input type="text" class="form-control input-sm" name="uom_qty_jual" id="uom_qty_jual"  value="<?php echo $split->uom_jual; ?>" readonly >                    
                                </div>                                    
                            </div>

                            <div class="col-md-12 col-xs-12">
                                <div class="col-xs-4"><label>Qty2 Jual  </label></div>
                                <div class="col-xs-5">
                                    <input type="text" class="form-control input-sm" name="qty2_jual" id="qty2_jual"  value="<?php echo $split->qty2_jual; ?>" readonly>                    
                                </div> 
                                <div class="col-xs-3">
                                    <input type="text" class="form-control input-sm" name="uom_qty2_jual" id="uom_qty2_jual"  value="<?php echo $split->uom2_jual; ?>" readonly>                    
                                </div>                                    
                            </div>

                            <div class="col-md-12 col-xs-12">
                                <div class="col-xs-4"><label>Lebar Jadi  </label></div>
                                <div class="col-xs-5">
                                    <input type="text" class="form-control input-sm" name="lebar_jadi" id="lebar_jadi"  value="<?php echo $split->lebar_jadi; ?>" readonly>                    
                                </div> 
                                <div class="col-xs-3">
                                    <input type="text" class="form-control input-sm" name="uom_lebar_jadi" id="uom_lebar_jadi"  value="<?php echo $split->uom_lebar_jadi; ?>" readonly>                    
                                </div>                                    
                            </div>
                            <?php } ?>
                        </div>

                    </div>

                </div>
            
            </form>

            <div class="row">
              <div class="col-md-12">
                <!-- Custom Tabs -->
                <div class="">
                  <ul class="nav nav-tabs " >
                    <li class="active"><a href="#tab_1" data-toggle="tab">Split Items</a></li>
                  </ul>
                  <div class="tab-content over"><br>
                    <div class="tab-pane active" id="tab_1">

                      <!-- Tabel  -->
                      <div class="col-md-12 table-responsive over">
                        <table class="table table-condesed table-hover rlstable  over" width="100%" id="table_items" >
                          <thead>                          
                            <tr>
                              <th class="style no">No.</th>
                              <?php if($split->dept_id == 'GJD'){?>
                              <th class="style" style="width:120px;" >Corak Remark</th>
                              <th class="style" style="width:120px;" >Warna Remark</th>
                             <?php }?>
                              <th class="style" style="width:100px;" >Qty1</th>
                              <th class="style" width="80px">Uom</th>
                              <th class="style" style="width:100px;" >Qty2</th>
                              <th class="style" width="80px">Uom2</th>
                              <?php if($split->dept_id == 'GJD'){?>
                              <th class="style" style="width:100px;" >Qty1 Jual</th>
                              <th class="style" width="80px">Uom Jual</th>
                              <th class="style" style="width:100px;" >Qty2 Jual</th>
                              <th class="style" width="80px">Uom2 Jual</th>
                              <th class="style" width="100px">Lbr.Jadi</th>
                              <th class="style" width="100px">Uom Lbr.Jadi</th>
                              <?php }?>
                              <th class="style" width="80px">Lot Baru</th>
                              <th class="style" width="80px">
                                <?php if(($split->dept_id) == 'GJD'){?>
                                    All <input type="checkbox" name="checkQAll" id="checkQAll"></th>
                                <?php }?>
                            </tr>
                          </thead>
                          <tbody>
                            <?php
                                foreach ($split_items as $row) {
                                  ?>
                                    <tr class="num">
                                      <td></td>
                                      <?php if($split->dept_id == 'GJD'){?>
                                      <td><a href="#" class="edit_items" data-lot="<?php echo $row->lot_baru ?>"  data-title="Edit"> <?php echo !empty($row->corak_remark)? $row->corak_remark :'Edit' ?> </a></td>
                                      <td><?php echo $row->warna_remark?></td>
                                      <?php }?>
                                      <td align="right"><?php echo number_format($row->qty,2)?></td>
                                      <td><?php echo $row->uom?></td>
                                      <td align="right"><?php echo number_format($row->qty2,2)?></td>
                                      <td><?php echo $row->uom2?></td>
                                      <?php if($split->dept_id == 'GJD'){?>
                                      <td align="right"><?php echo number_format($row->qty_jual,2)?></td>
                                      <td><?php echo $row->uom_jual?></td>
                                      <td align="right"><?php echo number_format($row->qty2_jual,2)?></td>
                                      <td><?php echo $row->uom2_jual?></td>
                                      <td><?php echo $row->lebar_jadi?></td>
                                      <td><?php echo $row->uom_lebar_jadi?></td>
                                      <?php }?>
                                      <td class="text-wrap width-200"><?php echo $row->lot_baru?></td>
                                      <td>
                                        <?php if(($split->dept_id) == 'GJD'){?>
                      										<input type="checkbox" class="checkItem" value="<?php echo $row->quant_id_baru?>" data-toggle="tooltip" title="Pilih Lot">
                                        <?php }?>
                                      </td>
                                    </tr>
                                  <?php 
                                }
                            ?>
                           
                          </tbody>
                         
                        </table>
                      </div>
                      <!-- Tabel  -->
                    </div>
                    <!-- /.tab-pane -->
              
                  </div>
                  <!-- /.tab-content -->
                </div>
                <!-- nav-tabs-custom -->
              </div>
              <!-- /.col -->
            </div>

        </div>
        <!-- /.box-body -->
      </div>
      <!-- /.box -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <footer class="main-footer">
    <div id="foot">
      <?php 
        $data['kode'] =  $split->kode_split;
        $data['mms']  =  $mms->kode;
        $this->load->view("admin/_partials/footer.php",$data) 
     ?>
    </div>
  </footer>

</div>

<?php $this->load->view("admin/_partials/js.php") ?>
<?php $this->load->view("admin/_partials/modal.php") ?>

<script>

      //checked All
    $('#checkQAll').on("change", function(){
          $('.checkItem').prop("checked", $(this).prop("checked"));
      });

    $(".checkItem").on("change", function(){
      var checked = $(this).is(':checked');
          if(checked == false){
              $('#checkQAll').prop("checked", false);
          }
      checkAllSQ();
    });	

    function checkAllSQ(){

      var lengthClass = $(".checkItem").length;
      var loop        = 0;
      $('.checkItem').each(function(index,item){		
        var checked = $(this).is(':checked');

        if(checked == true){
          // alert(loop);
          loop++;
        }

        if(lengthClass == loop){
          $('#checkQAll').prop("checked", true);
        }
      });
    }

 
    $(document).on("click", "#btn-print", function(e) {

        var myCheckboxes_arr = new Array();

        $(".checkItem:checked").each(function() {
            myCheckboxes_arr.push($(this).val());
        });
     
        let kode_split      = "<?php echo $split->kode_split; ?>";
        if (myCheckboxes_arr.length === 0) {
            alert_notify('fa fa-warning', 'Pilih LOT terlebih dahulu yang akan di print !', 'danger', function() {});
        }else{
            $(".print_data").html('<center><h5><img src="<?php echo base_url('dist/img/ajax-loader.gif') ?> "/><br>Please Wait...</h5></center>');
            $("#print_data").modal({
                show: true,
                backdrop: 'static'
            });
            $("#print_data .modal-dialog .modal-content .modal-footer #btn-print-barcode").remove();
            $('.modal-title').text('Pilih Desain Barcode dan K3L ');

            $.post('<?php echo site_url()?>warehouse/splitlot/print_modal',
            { kode_split:kode_split, data_arr:myCheckboxes_arr},
                function(html){
                    setTimeout(function() {$(".print_data").html(html);  },1000);
                    $("#print_data .modal-dialog .modal-content .modal-footer").prepend('<button class="btn btn-default btn-sm" id="btn-print-barcode" name="btn-print" >Print</button>');

                }   
            );
        }
    });

    // load new page print
    function print_voucher() {
        var win = window.open();
        win.document.write($("#printed").html());
        win.document.close();
        setTimeout(function(){ win.print(); win.close();}, 200);
    }

     $(document).on("click", ".edit_items", function(e) {
        let lot = $(this).attr('data-lot');
        edit_items(lot)
    });

    function edit_items(lot){
        var kode     = "<?php echo $split->kode_split; ?>";
        
        $('#btn-tambah').button('reset');
        $("#tambah_data").modal({
                    show: true,
                    backdrop: 'static'
        })
           
        $("#tambah_data").removeClass('modal fade lebar').addClass('modal fade lebar_mode');
        $("#tambah_data .modal-dialog .modal-content .modal-body").addClass('add_batch');
        $("#tambah_data .modal-dialog .modal-content .modal-footer #btn-tambah").attr('disabled',true);
    
        $(".tambah_data").html('<center><h5><img src="<?php echo base_url('dist/img/ajax-loader.gif') ?> "/><br>Please Wait...</h5></center>');
        $('.modal-title').text('Edit Items');
        $.post('<?php echo site_url()?>warehouse/splitlot/edit_items_modal',
              {kode:kode,lot:lot},
        ).done(function(html){
              setTimeout(function() {
                        $(".add_batch").html(html)  
              },1000);
              $("#tambah_data .modal-dialog .modal-content .modal-footer #btn-tambah").attr('disabled',false);
        });
    }

    
    // untuk focus after select2 close
    $(document).on('focus', '.select2', function(e) {
        if (e.originalEvent) {
            var s2element = $(this).siblings('select');
            s2element.select2('open');
            // Set focus back to select2 element on closing.
            s2element.on('select2:closing', function(e) {
                s2element.select2('focus');
            });
        }
    });

    $(document).on('select2:opening', '.select2', function(e) {
        if ($(this).attr('readonly') == 'readonly') {
            //   console.log( 'can not open : readonly' );
            e.preventDefault();
            $(this).select2('close');
            return false;
        } else {
            //   console.log( 'can be open : free' );
        }
    });
  
</script>


</body>
</html>

