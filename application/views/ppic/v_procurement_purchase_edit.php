<!DOCTYPE html>
<html>

<head>
  <?php $this->load->view("admin/_partials/head.php") ?>
  <style type="text/css">
    table.table td .add {
      display: none;
    }

    .width-btn {
      width: 54px !important;
    }

    table.table td .cancel {
      display: none;
      color: red;
      margin: 10 0px;
      min-width: 24px;
    }

    @media screen and (min-width: 768px) {
/*      .over {
        overflow-x: visible !important;
      }*/
    }

    .min-width-full {
      min-width: 100%;
    }

    .min-width-200 {
      min-width: 200px;
      ;
    }

    .min-width-100 {
      min-width: 100px;
    }

    .min-width-80 {
      min-width: 80px;
      ;
    }

    .tbl-catatan {
      line-height: 0.1 !important;
      font-size: 11px
    }

    /*
    @media screen and (max-width: 767px) {
      .over {
       overflow-y: scroll !important; 
      }
    }
    */
  </style>
</head>

<body class="hold-transition skin-black fixed sidebar-mini" id="block-page">
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
        <div id="status_bar">
          <?php
          $data['deptid'] = $id_dept;
          $data['jen_status'] =  $procurementpurchase->status;
          $this->load->view("admin/_partials/statusbar.php", $data);
          ?>
        </div>
      </section>

      <!-- Main content -->
      <section class="content">

        <!--  box content -->
        <div class="box">
          <div class="box-header with-border">
            <h3 class="box-title"><b><?php echo $procurementpurchase->kode_pp; ?></b></h3>
          </div>
          <div class="box-body">
            <form class="form-horizontal">
              <div class="form-group">
                <div class="col-md-12">
                  <div id="alert"></div>
                </div>
              </div>
              <div class="form-group">

                <div class="col-md-6">
                  <div class="col-md-12 col-xs-12">
                    <div class="col-xs-4"><label>Procurement Purchase</label></div>
                    <div class="col-xs-8">
                      <input type="text" class="form-control input-sm" name="kode_pp" id="kode_pp" readonly="readonly" value="<?php echo $procurementpurchase->kode_pp ?>" />
                    </div>
                  </div>
                  <div class="col-md-12 col-xs-12">
                    <div class="col-xs-4"><label>Create Date </label></div>
                    <div class="col-xs-8 col-md-8">
                      <input type='text' class="form-control input-sm" name="tgl_buat" id="tgl_buat" readonly="readonly" value="<?php echo $procurementpurchase->create_date ?>" />
                    </div>
                  </div>

                  <div class="col-md-12 col-xs-12">
                    <div class="col-xs-4"><label>Reff Notes </label></div>
                    <div class="col-xs-8">
                      <textarea type="text" class="form-control input-sm" name="note" id="note"><?php echo $procurementpurchase->notes ?></textarea>
                    </div>
                  </div>
                  <div class="col-md-12 col-xs-12">
                    <div class="col-xs-4"><label>Type </label></div>
                    <div class="col-xs-8">
                      <div class="col-xs-12 col-sm-12 col-md-12">
                        <?php 
                          $checked_type = "";
                          if($procurementpurchase->type == 'mto'){  
                            $checked_type = "checked";
                          }
                        ?>
                        <input type="radio" id="mto" name="type[]" value="mto"  <?php echo $checked_type;?> disabled >
                        <label for="mto">Make to Order</label>
                      </div>
                      <div class="col-xs-12 col-sm-12 col-md-12">
                        <?php 
                          $checked_type3 = "";
                          if($procurementpurchase->type == 'pengiriman'){  
                            $checked_type3 = "checked";
                          }
                        ?>
                        <input type="radio" id="pengiriman" name="type[]" value="pengiriman" <?php echo $checked_type3;?> disabled >
                        <label for="pengiriman">Pengiriman</label>
                      </div>
                    </div>                                    
                  </div>
                  <div class="col-md-12 col-xs-12">
                    <div class="col-xs-4"><label>Sales Order </label></div>
                    <div class="col-xs-8">
                      <div class="col-xs-6 col-sm-4 col-md-4">
                        <?php
                        $checked = "";
                        if ($procurementpurchase->show_sales_order == 'yes') {
                          $checked = "checked";
                        }
                        ?>
                        <input type="radio" id="sc_true" name="sc[]" value="yes" <?php echo $checked; ?> disabled>
                        <label for="yes">Yes</label>
                      </div>
                      <div class="col-xs-6 col-sm-4 col-md-4">
                        <?php
                        $checked2 = "";
                        if ($procurementpurchase->show_sales_order == 'no') {
                          $checked2 = "checked";
                        }
                        ?>
                        <input type="radio" id="sc_false" name="sc[]" value="no" <?php echo $checked2; ?> disabled>
                        <label for="no">No</label>
                      </div>
                    </div>
                  </div>

                </div>

                <div class="col-md-6">
                  <div class="col-md-12 col-xs-12">
                    <div class="col-xs-4"><label>Schedule Date </label></div>
                    <div class="col-xs-8 col-md-8">
                      <input type='text' class="form-control input-sm" name="tgl" id="tgl" readonly="readonly" value="<?php echo $procurementpurchase->schedule_date ?>" />
                    </div>
                  </div>
                  <?php if ($procurementpurchase->show_sales_order == 'yes') { ?>
                    <div class="col-md-12 col-xs-12">
                      <div class="col-xs-4"><label>Production Order</label></div>
                      <div class="col-xs-8">
                        <input type="text" class="form-control input-sm" name="kode_prod" id="kode_prod" readonly="readonly" value="<?php echo $procurementpurchase->kode_prod ?>" />
                      </div>
                    </div>
                    <div class="col-md-12 col-xs-12">
                      <div class="col-xs-4"><label>Sales Order</label></div>
                      <div class="col-xs-8">
                        <input type="text" class="form-control input-sm" name="sales_order" id="sales_order" readonly="readonly" value="<?php echo $procurementpurchase->sales_order ?>" />
                      </div>
                    </div>
                  <?php } ?>
                  <div class="col-md-12 col-xs-12">
                    <div class="col-xs-4"><label>Departement Tujuan</label></div>
                    <div class="col-xs-8">
                      <input type="hidden" class="form-control input-sm" name="warehouse" id="warehouse" readonly="readonly" value="<?php echo $procurementpurchase->warehouse ?>" />
                      <input type="text" class="form-control input-sm" name="nama_dept" id="nama_dept" readonly="readonly" value="<?php echo $procurementpurchase->nama_departemen ?>" />
                    </div>
                  </div>
                  <div class="col-md-12 col-xs-12">
                    <div class="col-xs-4"><label>Priority </label></div>
                    <div class="col-xs-8">
                      <select class="form-control input-sm" name="priority" id="priority" />
                      <option value="">Pilih Priority</option>
                      <?php
                      $val = array('Normal', 'Urgent');
                      for ($i = 0; $i <= 1; $i++) {
                        if ($val[$i] == $procurementpurchase->priority) { ?>
                          <option selected><?php echo $val[$i]; ?></option>
                        <?php
                        } else { ?>
                          <option><?php echo $val[$i]; ?></option>
                      <?php  }
                      } ?>
                      </select>
                    </div>
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-12">
                  <!-- Custom Tabs -->
                  <div class="">
                    <ul class="nav nav-tabs ">
                      <li class="active"><a href="#tab_1" data-toggle="tab">Procurements Lines</a></li>
                    </ul>
                    <div class="tab-content over"><br>
                      <div class="tab-pane active" id="tab_1">

                        <!-- Tabel  -->
                        <div class="col-md-12 table-responsive over">
                          <table class="table table-condesed table-hover rlstable  over" width="100%" id="procurements">
                            <thead>
                              <tr>
                                <th class="style no">No.</th>
                                <th class="style" width="200px">Product</th>
                                <th class="style" width="150px">Schedule Date</th>
                                <th class="style" style="width:100px; text-align: right;">Qty Uom Beli</th>
                                <th class="style" width="80px">Uom Beli</th>
                                <th class="style" style="width:100px; text-align: right;">Qty</th>
                                <th class="style" width="80px">Uom</th>
                                <th class="style" width="200px">Notes</th>
                                <th class="style" width="60px">Status</th>
                                <th class="style" width="60px">kode CFB</th>
                                <th class="style" style="width: 80px; text-align: center;">
                                  <?php
                                  if ($procurementpurchase->status == 'done' or $procurementpurchase->status == 'cancel') {
                                  ?>
                                    <a href="javascript:void(0)" data-toggle="tooltip" title="Details" onclick="view_detail('<?php echo $procurementpurchase->kode_pp; ?>','<?php echo $procurementpurchase->kode_prod; ?>','<?php echo $procurementpurchase->sales_order; ?>')"><span class="glyphicon  glyphicon-share"></span></a>
                                  <?php
                                  }
                                  ?>

                                </th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php
                              $no = 1;
                              foreach ($details as $row) {
                              ?>
                                <tr class="">
                                  <td data-content="edit" data-id="row_order" data-isi="<?php echo $row->row_order; ?>"><?php echo $no.".";?></td>
                                  <td data-content="edit" data-id="kode_produk" data-isi="<?php echo $row->kode_produk; ?>" data-id2="prodhidd" data-isi2="<?php echo htmlentities($row->nama_produk) ?>"><?php echo '[' . $row->kode_produk . '] ' . $row->nama_produk; ?></a></td>
                                  <td data-content="edit" data-id="schedule_date" data-isi="<?php echo $row->schedule_date; ?>"><?php echo $row->schedule_date ?></td>
                                  <td data-content="edit" data-id="qty_beli" data-name="Qty Beli" data-isi="<?php echo $row->qty_beli; ?>" align="right"><?php echo number_format($row->qty_beli, 2) ?></td>
                                  <td data-content="edit" data-id="uom_beli" data-name="Uom Beli" data-isi="<?php echo $row->id; ?>" data-isi2="<?php echo $row->dari; ?>" data-nilai="<?php echo $row->cat_beli; ?>"><?php echo $row->dari ?> <p><small id="uom_beli_note" class="form-text text-muted"><?= $row->cat_beli; ?></small></p></td>
                                  <td data-content="edit" data-id="qty" data-name="Qty" data-isi="<?php echo $row->qty; ?>" align="right"><?php echo number_format($row->qty, 2) ?></td>
                                  <td data-content="edit" data-id="uom" data-name="Uom" data-isi="<?php echo $row->uom; ?>"><?php echo $row->uom ?></td>
                                  <td data-content="edit" data-id="reff" data-isi="<?php echo htmlentities($row->reff_notes); ?>" class="text-wrap width-200"> <?php echo $row->reff_notes ?></td>
                                  <td><?php echo $row->nama_status; ?></td>
                                  <td><?php echo $row->kode_cfb ?></td>
                                  <td align="center">
                                    <?php if ($row->status == 'draft') { ?>
                                      <a href="javascript:void(0)" class="add" title="Simpan" data-toggle="tooltip"><i class="fa fa-save"></i></a>
                                      <a href="javascript:void(0)" class="edit" title="Edit" data-toggle="tooltip" style="color: #FFC107;   margin-right: 24px;"><i class="fa fa-edit"></i></a>
                                      <a href="javascript:void(0)" class="delete" title="Hapus" data-toggle="tooltip"><i class="fa fa-trash" style="color: red"></i></a>
                                      <a href="javascript:void(0)" class="cancel" title="Cancel" data-toggle="tooltip" style="margin-left: 20px;"><i class="fa fa-close"></i></a>
                                    <?php } ?>
                                  </td>
                                </tr>
                                <?php
                                if (!empty($row->catatan)) {
                                  $catatan = explode("#", $row->catatan);
                                  foreach ($catatan as $keys => $catt) {
                                ?>
                                    <tr>
                                      <td class="text-right tbl-catatan"><?= $no . "." . ($keys + 1) ?></td>
                                      <td class="tbl-catatan" colspan="8" style="vertical-align: top; color:red;">
                                        <?= $catt ?>
                                      </td>
                                    </tr>
                              <?php
                                  }
                                }
                                $no++;
                              }
                              ?>
                            </tbody>
                            <tfoot>
                              <?php
                              if ($procurementpurchase->status == 'draft') { ?>
                                <tr>
                                  <td colspan="8">
                                    <a href="javascript:void(0)" class="add-new"><i class="fa fa-plus"></i> Tambah Data</a>
                                  </td>
                                </tr>
                              <?php } ?>
                              <tfoot>
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

            </form>
          </div>
          <!-- /.box-body -->
        </div>
        <!-- /.box -->
      </section>
      <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

    <footer class="main-footer">
      <?php $this->load->view("admin/_partials/modal.php") ?>
      <div id="foot">
        <?php $this->load->view("admin/_partials/footer.php") ?>
      </div>
    </footer>

  </div>

  <?php $this->load->view("admin/_partials/js.php") ?>

  <script type="text/javascript">
    function validAngka(a) {
      if (!/^[0-9.]+$/.test(a.value)) {
        a.value = a.value.substring(0, a.value.length - 1000);
        alert_notify('fa fa-warning', 'Maaf, Inputan Qty Hanya Berupa Angka !', 'danger');
      }
    }

    //html entities javascript
    function htmlentities_script(str) {
      return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    //modal view move items
    function view_detail(kode_pp, kode_prod, sales_order) {
      $("#view_data").modal({
        show: true,
        backdrop: 'static'
      })
      $(".view_body").html('<center><h5><img src="<?php echo base_url('dist/img/ajax-loader.gif') ?> "/><br>Please Wait...</h5></center>');
      $('.modal-title').text('Detail Items');
      $.post('<?php echo site_url() ?>ppic/procurementpurchase/view_detail_items', {
          kode_pp: kode_pp,
          kode_prod: kode_prod,
          sales_order: sales_order
        },
        function(html) {
          setTimeout(function() {
            $(".view_body").html(html);
          });
        }
      );
    }

    // Append table with add row form on add new button click
    $(document).on("click", ".add-new", function() {

      $(".add-new").hide();
      var index = $("#procurements tbody tr:last-child").index();
      var row = '<tr class="">' +
        '<td></td>' +
        '<td class="min-width-200">' +
        '<select type="text" class="form-control input-sm prod min-width-full" name="Product" id="product"></select>' +
        '<input type="hidden" class="form-control input-sm prodhidd" name="prodhidd" id="prodhidd"></td>' +
        '<td><div class="input-group width-150 date" id="sch_date" ><input type="text" class="form-control input-sm" name="schedule_date" id="schedule_date" readonly="readonly"  /><span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span></div></td>' +
        '<td class="min-width-100"><input type="text" class="form-control input-sm width-100 qty_beli" name="Qty Beli" id="qty_beli"  onkeyup="validAngka(this)" ></td>' +
        '<td class="min-width-100"><select type="text" class="form-control input-sm uom_beli" name="Uom Beli" id="uom_beli"></select> <small id="uom_beli_note" class="form-text text-muted uom_beli_note"></small></td>' +
        '<td class="min-width-100"><input type="text" class="form-control input-sm width-100 qty" name="Qty" id="qty"  onkeyup="validAngka(this)" ></td>' +
        '<td class="min-width-100"><select type="text" class="form-control input-sm uom" name="Uom" id="uom"></select></td>' +
        '<td class="min-width-50"><textarea type="text" class="form-control input-sm width-150" name="reff" id="reff"></textarea></td>' +
        '<td></td>' +
        '<td></td>' +
        '<td align="center"><button type="button" class="btn btn-primary btn-xs add width-btn" title="Simpan" data-toggle="tooltip">Simpan</button><a class="edit" title="Edit" data-toggle="tooltip"><i class="fa fa-edit"></i></a><button type="button" class="btn btn-danger btn-xs batal width-btn" title="Batal" data-toggle="tooltip">Batal</button></td>' +
        '</tr>';

      $('#procurements tbody').append(row);
      $("#procurements tbody tr").eq(index + 1).find(".add, .edit").toggle();
      $('[data-toggle="tooltip"]').tooltip();

      //set schedule date
      var datetomorrow = new Date();
      datetomorrow.setDate(datetomorrow.getDate() + 1);
      var datetomorrow3 = new Date();
      datetomorrow3.setDate(datetomorrow3.getDate() + 3);
      $('#sch_date').datetimepicker({
            // useCurrent: false,
            minDate :  moment().startOf('day').add(0, 'd'),
            defaultDate: datetomorrow3,
            format: 'YYYY-MM-DD HH:mm:ss',
            ignoreReadonly: true,
      }).on('dp.show', function() {
          $(this).closest('.table-responsive').removeClass('table-responsive').addClass('temp');
        }).on('dp.hide', function() {
          $(this).closest('.temp').addClass('table-responsive').removeClass('temp')
      });


      //select 2 product
      $('.prod').select2({
        allowClear: true,
        placeholder: "",
        ajax: {
          dataType: 'JSON',
          type: "POST",
          url: "<?php echo base_url(); ?>ppic/procurementpurchase/get_produk_procurement_purchase_select2",
          //delay : 250,
          data: function(params) {
            return {
              prod: params.term,
            };
          },
          processResults: function(data) {
            var results = [];

            $.each(data, function(index, item) {
              results.push({
                id: item.kode_produk,
                text: '[' + item.kode_produk + '] ' + item.nama_produk
              });
            });
            results.push({
              id: "add_search",
              text: "search.."
            })
            return {
              results: results
            };
            // text:"<a href='#' onclick='test()' >Search More ..</a>"

          },
          error: function(xhr, ajaxOptions, thrownError) {
            //alert(xhr.responseText);
            //alert('Error data');
          }
        }
      });

      $(".prod").change(function() {
        $.ajax({
          dataType: "JSON",
          url: '<?php echo site_url('ppic/procurementpurchase/get_prod_by_id') ?>',
          type: "POST",
          data: {
            kode_produk: $(this).parents("tr").find("#product").val()
          },
          success: function(data) {
            $('.prodhidd').val(data.nama_produk);
            // $('.uom').val(data.uom);
            var $newOptionuom = $("<option></option>").val(data.uom).text(data.uom);
            $('.uom').empty().append($newOptionuom).trigger('change');
          },
          error: function(xhr, ajaxOptions, thrownError) {
            // alert('Error data');
            // alert(xhr.responseText);
          }
        });
      });


      $(".uom").select2({
                allowClear: true,
                placeholder: "",
                ajax:{
                        dataType: 'JSON',
                        type : "POST",
                        url : "<?php echo base_url();?>ppic/procurementpurchase/get_list_uom_select2",
                        data : function(params){
                            return{
                                prod:params.term,
                                kode_produk: $(this).parents("tr").find("#product").val()
                            };
                        }, 
                        processResults:function(data){
                            var results = [];
                            $.each(data, function(index,item){
                                results.push({
                                    id:item.uom,
                                    text:item.uom
                                });
                            });
                            return {
                                results:results
                            };
                        },
                        error: function (xhr, ajaxOptions, thrownError){
                            // alert('Error data');
                            // alert(xhr.responseText);
                        }
                }
      });

      $('.uom_beli').select2({
                allowClear: true,
                placeholder: "",
                ajax: {
                    url : "<?php echo base_url();?>ppic/procurementpurchase/get_list_uom_beli_select2",
                    delay: 250,
                    type: "POST",
                    data: function (params) {
                      return{
                                prod:params.term,
                                kode_produk:$(this).parents("tr").find("#product").val()
                            };
                    },
                    processResults: function (data) {
                        return {
                            results: $.map(JSON.parse(data), function (obj) {
                                return {
                                    id: obj.id,
                                    text: obj.uom,
                                    catatan: obj.catatan,
                                    nilai:obj.nilai
                                };
                            })
                        };
                    }
                }
           
      });

      $('.uom_beli').on('select2:select', function (e) {
          var gt_cata_uom_beli = $('#procurements tbody tr .uom_beli :selected').data().data.catatan;
          $('.uom_beli_note').html(gt_cata_uom_beli);
      });

    });


    //batal add row on batal button click
    $(document).on("click", ".batal", function() {
      var input = $(this).parents("tr").find('.prod');
      input.each(function() {
        $(this).parent("td").html($(this).val());
      });

      $(this).parents("tr").remove();
      $(".add-new").show();
    });

    //refresh procurement purchase
    function refresh_procurement() {
      $("#tab_1").load(location.href + " #tab_1");
      $("#foot").load(location.href + " #foot");
      $("#status_bar").load(location.href + " #status_bar");
    }


    //untuk reload page setelah modal ditutup
    $(".modal").on('hidden.bs.modal', function() {
      refresh_procurement();
    });


    //simpan / edit row data ke database
    $(document).on("click", ".add", function() {
      var empty = false;
      var input = $(this).parents("tr").find('input[type="text"]');

      var empty2 = false;
      var select = $(this).parents("tr").find('select[type="text"]');

      //validasi tidak boleh kosong hanya select product saja
      select.each(function() {
        if (!$(this).val() && $(this).attr('name') == 'Product') {
          alert_notify('fa fa-warning', $(this).attr('name') + ' Harus Diisi !', 'danger', function() {});
          empty2 = true;
        }
        if (!$(this).val() && $(this).attr('name') == 'Uom') {
          alert_notify('fa fa-warning', $(this).attr('name') + ' Harus Diisi !', 'danger', function() {});
          empty2 = true;
        }
      });

      // validasi untuk qty = 0
      input.each(function() {
        if ($(this).attr('name') == 'Qty') {
          qty_val = parseFloat($(this).val());
          if (qty_val == false) {
            alert_notify('fa fa-warning', $(this).attr('name') + ' tidak boleh 0 !', 'danger', function() {});
            empty = true;
          }
        }
      });

      // validasi untuk inputan textbox
      input.each(function() {
        if (!$(this).val() && ($(this).attr('name') != 'reff') && ($(this).attr('id') != 'qty_beli') ) {
          alert_notify('fa fa-warning', $(this).attr('name') + ' Harus Diisi !', 'danger', function() {});
          empty = true;
        }
      });


      if (!empty && !empty2) {
        var kode = "<?php echo $procurementpurchase->kode_pp ?>";
        var kode_produk = $(this).parents("tr").find("#product").val();
        var produk = $(this).parents("tr").find("#prodhidd").val();
        var schedule_date = $(this).parents("tr").find("#schedule_date").val();
        var qty_beli = $(this).parents("tr").find("#qty_beli").val();
        var uom_beli = $(this).parents("tr").find("#uom_beli").val();
        var qty = $(this).parents("tr").find("#qty").val();
        var uom = $(this).parents("tr").find("#uom").val();
        var reff = $(this).parents("tr").find("#reff").val();
        var row_order = $(this).parents("tr").find("#row_order").val();

        var btn_load = $(this);
        btn_load.button('loading');

        $.ajax({
          dataType: "JSON",
          url: '<?php echo site_url('ppic/procurementpurchase/simpan_detail_procurement_purchase') ?>',
          type: "POST",
          data: {
            kode: kode,
            kode_produk: kode_produk,
            produk: produk,
            tgl: schedule_date,
            qty: qty,
            uom: uom,
            qty_beli: qty_beli,
            uom_beli: uom_beli,
            reff: reff,
            row_order: row_order
          },
          success: function(data) {
            if (data.sesi == 'habis') {
              //alert jika session habis
              alert_modal_warning(data.message);
              window.location.replace('../index');
              btn_load.button('reset');
            } else {
              refresh_procurement();
              $(".add-new").show();
              alert_notify(data.icon, data.message, data.type, function() {});
              btn_load.button('reset');
            }
          },
          error: function(xhr, ajaxOptions, thrownError) {
            alert('Error data');
            alert(xhr.responseText);
            btn_load.button('reset');
          }
        });

      }
    });


    // Edit row on edit button click
    $(document).on("click", ".edit", function() {
      $(this).parents("tr").find("td[data-content='edit']").each(function() {

        if ($(this).attr('data-id') == "row_order") {
          $(this).html('<input type="hidden"  class="form-control input-sm" value="' + $(this).attr('data-isi') + '" id="' + $(this).attr('data-id') + '"> ');
          row_order = $(this).attr('data-isi');
          // }else  if($(this).attr('data-id')=="row_order"){
          //   $(this).html('<input type="hidden"  class="form-control" value="' + $(this).attr('data-isi') + '" id="'+ $(this).attr('data-id') +'"> ');
        } else if ($(this).attr('data-id') == 'kode_produk') {

          var kode_produk = $(this).attr('data-isi');
          var nama_produk = $(this).attr('data-isi2');

          class_sel2_prod = 't_sel2_prod' + row_order;
          class_nama_produk = 'e_nama_produk' + row_order;

          $(this).html('<select type="text"  class="form-control input-sm ' + class_sel2_prod + ' min-width-full " id="product" name="Product" ></select> ' + '<input type="hidden"  class="form-control ' + class_nama_produk + ' " value="' + htmlentities_script($(this).attr('data-isi2')) + '" id="' + $(this).attr('data-id2') + '"> ');

          // append berdasarkan nama produk
          name_opt    = "["+kode_produk+"] "+nama_produk;
          $newOption = new Option(name_opt, kode_produk, true, true);
          $('.t_sel2_prod' + row_order).append($newOption).trigger('change');

          //select 2 product
          $('.t_sel2_prod' + row_order).select2({
            allowClear: true,
            placeholder: "",
            ajax: {
              dataType: 'JSON',
              type: "POST",
              url: "<?php echo base_url(); ?>ppic/procurementpurchase/get_produk_procurement_purchase_select2",
              //delay : 250,
              data: function(params) {
                return {
                  prod: params.term
                };
              },
              processResults: function(data) {
                var results = [];

                $.each(data, function(index, item) {
                  results.push({
                    id: item.kode_produk,
                    text: '[' + item.kode_produk + '] ' + item.nama_produk
                  });
                });
                return {
                  results: results
                };
              },
              error: function(xhr, ajaxOptions, thrownError) {
                //  alert('Error data');
                //  alert(xhr.responseText);
              }
            }
          });

          $('.t_sel2_prod' + row_order).change(function() {
            $.ajax({
              dataType: "JSON",
              url: '<?php echo site_url('sales/salescontract/get_prod_by_id') ?>',
              type: "POST",
              data: {
                kode_produk: $(this).parents("tr").find("#product").val()
              },
              success: function(data) {
                //alert(data.nama_produk);
                $('.e_nama_produk' + row_order).val(data.nama_produk);
                $('.description' + row_order).val(data.nama_produk);
                // $(".uom" + row_order).val(data.uom);

                var $newOptionuom = $("<option></option>").val(data.uom).text(data.uom);
                $(".uom" + row_order).empty().append($newOptionuom).trigger('change');
              },
              error: function(xhr, ajaxOptions, thrownError) {
                //  alert('Error data');
                //  alert(xhr.responseText);
              }
            });
          });

        } else if ($(this).attr('data-id') == "schedule_date") {
          $(this).html('<div class="input-group date" id="sch_date2" ><input type="text" class="form-control input-sm " value="' + htmlentities_script($(this).attr('data-isi')) + '" id="' + $(this).attr('data-id') + '" name="' + $(this).attr('data-id') + '" readonly="readonly"  /><span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span></div> ');
          var datetomorrow = new Date();
          datetomorrow.setDate(datetomorrow.getDate() + 1);
          var datetomorrow3 = new Date();
          datetomorrow3.setDate(datetomorrow3.getDate() + 1);
          $('#sch_date2').datetimepicker({
            // useCurrent: false,
            minDate :  moment().startOf('day').add(0, 'd'),
            defaultDate: datetomorrow3,
            format: 'YYYY-MM-DD HH:mm:ss',
            ignoreReadonly: true,
          }).on('dp.show', function() {
              $(this).closest('.table-responsive').removeClass('table-responsive').addClass('temp');
          }).on('dp.hide', function() {
              $(this).closest('.temp').addClass('table-responsive').removeClass('temp')
          });
        } else if ($(this).attr('data-id') == 'qty_beli') {
          $(this).html('<input type="text"  class="form-control input-sm qty_beli" value="' + htmlentities_script($(this).attr('data-isi')) + '" id="' + $(this).attr('data-id') + '" name="' + $(this).attr('data-name') + '" onkeyup="validAngka(this)"> ');
        } else if ($(this).attr('data-id') == 'uom_beli') {

          class_uom_beli = 'uom_beli' + row_order;
          class_cata_uom_beli = 'uom_beli_note' + row_order;

          $(this).html('<select type="text"  class="form-control input-sm ' + class_uom_beli + ' min-width-full " id="' + $(this).attr('data-id') + '" name="' + $(this).attr('data-name') + '" ></select> <small id="uom_beli_note" class="form-text text-muted '+class_cata_uom_beli+'"></small>');


            $('.' + class_uom_beli).select2({
                allowClear: true,
                placeholder: "",
                ajax: {
                    url : "<?php echo base_url();?>ppic/procurementpurchase/get_list_uom_beli_select2",
                    delay: 250,
                    type: "POST",
                    data: function (params) {
                      return{
                                prod:params.term,
                                kode_produk:$(this).parents("tr").find("#product").val()
                            };
                    },
                    processResults: function (data) {
                        return {
                            results: $.map(JSON.parse(data), function (obj) {
                                return {
                                    id: obj.id,
                                    text: obj.uom,
                                    catatan: obj.catatan,
                                    nilai:obj.nilai
                                };
                            })
                        };
                    }
                }
           
            });

            $('.' + class_uom_beli).on('select2:select', function (e) {
              var gt_cata_uom_beli = $('#procurements tbody tr .'+class_uom_beli+' :selected').data().data.catatan;
              $('.'+class_cata_uom_beli).html(gt_cata_uom_beli);
            });

            // $newOption = new Option($(this).attr('data-isi2'), $(this).attr('data-isi'), true, true);
            // $('.' + class_uom_beli).append($newOption).trigger('change');
          

        } else if ($(this).attr('data-id') == 'qty') {
          $(this).html('<input type="text"  class="form-control input-sm" value="' + htmlentities_script($(this).attr('data-isi')) + '" id="' + $(this).attr('data-id') + '" name="' + $(this).attr('data-name') + '" onkeyup="validAngka(this)"> ');
        } else if ($(this).attr('data-id') == 'uom') {
          class_uom = 'uom' + row_order;
          // $(this).html('<input type="text"  class="form-control input-sm ' + class_uom + ' " value="' + htmlentities_script($(this).attr('data-isi')) + '" id="' + $(this).attr('data-id') + '" name="' + $(this).attr('data-name') + '" readonly> ');

          $(this).html('<select type="text"  class="form-control input-sm ' + class_uom + ' min-width-full " id="' + $(this).attr('data-id') + '" name="' + $(this).attr('data-name') + '" ></select> ');

          var $newOptionuom = $("<option></option>").val($(this).attr('data-isi')).text($(this).attr('data-isi'));
          $(".uom" + row_order).empty().append($newOptionuom).trigger('change');

          $('.' + class_uom).select2({
                allowClear: true,
                placeholder: "",
                ajax:{
                        dataType: 'JSON',
                        type : "POST",
                        url : "<?php echo base_url();?>ppic/procurementpurchase/get_list_uom_select2",
                        data : function(params){
                            return{
                                prod:params.term,
                                kode_produk: $(this).parents("tr").find("#product").val()
                            };
                        }, 
                        processResults:function(data){
                            var results = [];
                            $.each(data, function(index,item){
                                results.push({
                                    id:item.uom,
                                    text:item.uom
                                });
                            });
                            return {
                                results:results
                            };
                        },
                        error: function (xhr, ajaxOptions, thrownError){
                            // alert('Error data');
                            // alert(xhr.responseText);
                        }
                }
          });

        } else if ($(this).attr('data-id') == "reff") {
          $(this).html('<textarea type="text" class="form-control input-sm" id="' + $(this).attr('data-id') + '" name="' + $(this).attr('data-id') + '">' + htmlentities_script($(this).attr('data-isi')) + '</textarea>');
        }

      });

      $(this).parents("tr").find(".add, .edit").toggle();
      $(this).parents("tr").find(".cancel, .delete").toggle();
      $(".add-new").hide();
    });


    $(document).on("keyup", ".qty_beli", function(){
        let qty_beli = $(this).val();
        let uom_bei  = $(this).parents("tr").find("#uom_beli").val(); // id nilai konversi uom
        let get_nilai = $(this).parents("tr").find("#uom_beli").find(':selected').data().data.nilai;
        result    = qty_beli*get_nilai;
        $(this).parents("tr").find("#qty").val(result);
    });

    // batal add row on batal button click
    $(document).on("click", ".batal", function() {
      var input = $(this).parents("tr").find('.prod');
      input.each(function() {
        $(this).parent("td").html($(this).val());
      });

      $(this).parents("tr").remove();
      $(".add-new").show();
    });

    //btn cancel edit
    $(document).on("click", ".cancel", function() {
      $("#tab_1").load(location.href + " #tab_1");
      $(".add-new").show();
    });

    //delete row di database
    $(document).on("click", ".delete", function() {
      $(this).parents("tr").find("td[data-content='edit']").each(function() {
        if ($(this).attr('data-id') == "row_order") {
          $(this).html('<input type="hidden" class="form-control" value="' + htmlentities_script($(this).attr('data-isi')) + '" id="' + $(this).attr('data-id') + '"> ');
        }
      });
      var kode = "<?php echo $procurementpurchase->kode_pp; ?>";
      var row_order = $(this).parents("tr").find("#row_order").val();
      var btn_load = $(this);
      bootbox.dialog({
        message: "Apakah Anda ingin menghapus data ?",
        title: "<i class='glyphicon glyphicon-trash'></i> Delete !",
        buttons: {
          danger: {
            label: "Yes ",
            className: "btn-primary btn-sm",
            callback: function() {
              $.ajax({
                dataType: "JSON",
                url: '<?php echo site_url('ppic/procurementpurchase/hapus_procurement_purchase_items') ?>',
                type: "POST",
                data: {
                  kode: kode,
                  row_order: row_order
                },
                beforeSend: function() {
                  btn_load.button('loading');
                },
                success: function(data) {
                  if (data.sesi == 'habis') {
                    //alert jika session habis
                    alert_modal_warning(data.message);
                    window.location.replace('../index');
                  } else if (data.status == 'failed') {
                    alert_modal_warning(data.message);
                    refresh_procurement();
                  } else {
                    refresh_procurement();
                    $(".add-new").show();
                    alert_notify(data.icon, data.message, data.type, function() {});
                  }
                  btn_load.button('reset');
                },
                error: function(xhr, ajaxOptions, thrownError) {
                  alert('Error data');
                  alert(xhr.responseText);
                  btn_load.button('reset');
                }
              });
            }
          },
          success: {
            label: "No",
            className: "btn-default  btn-sm",
            callback: function() {
              $('.bootbox').modal('hide');
            }
          }
        }
      });
    });

    $(document).on("click", "#btn-generate", function() {

      var kode = "<?php echo $procurementpurchase->kode_pp; ?>";
      var status_head = "<?php echo $procurementpurchase->status ?>";

      if (status_head == 'cancel') {
        alert_modal_warning('Maaf, Procurement Purchase Sudah dibatalkan !');

      } else if (status_head == 'done') {
        alert_modal_warning('Maaf, Product Sudah Generated !');
      } else {

        bootbox.dialog({
          message: "Apakah Anda ingin Generate Data ?",
          title: "<i class='fa fa-gear'></i> Generate Data !",
          buttons: {
            danger: {
              label: "Yes ",
              className: "btn-primary btn-sm",
              callback: function() {
                please_wait(function() {});
                $('#btn-generate').button('loading');
                $.ajax({
                  dataType: "JSON",
                  url: '<?php echo site_url('ppic/procurementpurchase/generate_procurement_purchase') ?>',
                  type: "POST",
                  data: {
                    kode: kode
                  },
                  success: function(data) {
                    if (data.sesi == 'habis') {
                      //alert jika session habis
                      alert_modal_warning(data.message);
                      window.location.replace('../index');
                    } else if (data.status == 'failed') {
                      alert_modal_warning(data.message);
                      refresh_procurement();
                      unblockUI(function() {});
                      $('#btn-generate').button('reset');
                    } else {
                      refresh_procurement();
                      unblockUI(function() {
                        setTimeout(function() {
                          alert_notify(data.icon, data.message, data.type, function() {});
                        }, 1000);
                      });
                      $('#btn-generate').button('reset');

                    }
                  },
                  error: function(xhr, ajaxOptions, thrownError) {
                    alert('Error data');
                    alert(xhr.responseText);
                    unblockUI(function() {});
                    $('#btn-generate').button('reset');
                  }
                });
              }
            },
            success: {
              label: "No",
              className: "btn-default  btn-sm",
              callback: function() {
                $('.bootbox').modal('hide');
              }
            }
          }
        });
      }

    });


    //batal procurement purchase
    $(document).on("click", "#btn-cancel", function() {

      var kode = "<?php echo $procurementpurchase->kode_pp; ?>";
      var kode_prod = "<?php echo $procurementpurchase->kode_prod; ?>";
      var sales_order = "<?php echo $procurementpurchase->sales_order; ?>";

      var status = "<?php echo $procurementpurchase->status; ?>";

      if (status == 'cancel') {
        var message = 'Maaf, Procurement Purchase Sudah dibatalkan !';
        alert_modal_warning(message);
        // }else if(status == 'draft'){
        //    var message = 'Maaf, Status Procurement Purchase Masih draft !';
        //   alert_modal_warning(message);
      } else {
        bootbox.dialog({
          message: "Apakah Anda ingin membatalkan Procurement Purchase ini ?",
          title: "<i class='fa fa-warning'></i> Batal Procurements Purchase !",
          buttons: {
            danger: {
              label: "Yes ",
              className: "btn-primary btn-sm",
              callback: function() {
                please_wait(function() {});
                $.ajax({
                  dataType: "JSON",
                  url: '<?php echo site_url('ppic/procurementpurchase/batal_procurement_purchase') ?>',
                  type: "POST",
                  data: {
                    kode: kode,
                    kode_prod: kode_prod,
                    sales_order: sales_order
                  },
                  success: function(data) {
                    if (data.sesi == 'habis') {
                      //alert jika session habis
                      alert_modal_warning(data.message);
                      window.location.replace('../index');
                    } else if (data.status == 'failed') {
                      unblockUI(function() {});
                      //alert(data.message);
                      alert_modal_warning(data.message);
                      refresh_procurement();
                    } else {
                      refresh_procurement();
                      unblockUI(function() {
                        setTimeout(function() {
                          alert_notify(data.icon, data.message, data.type, function() {});
                        }, 1000);
                      });
                    }
                  },
                  error: function(xhr, ajaxOptions, thrownError) {
                    alert('Error data');
                    alert(xhr.responseText);
                    refresh_procurement();
                    unblockUI(function() {});
                  }
                });
              }
            },
            success: {
              label: "No",
              className: "btn-default  btn-sm",
              callback: function() {
                $('.bootbox').modal('hide');
                refresh_procurement();
              }
            }
          }
        });

      }

    });


    //klik button simpan
    $('#btn-simpan').click(function() {
      $('#btn-simpan').button('loading');
      please_wait(function() {});
      $.ajax({
        type: "POST",
        dataType: "json",
        url: '<?php echo base_url('ppic/procurementpurchase/simpan') ?>',
        beforeSend: function(e) {
          if (e && e.overrideMimeType) {
            e.overrideMimeType("application/json;charset=UTF-8");
          }
        },
        data: {
          kode_pp: $('#kode_pp').val(),
          kode_prod: $('#kode_prod').val(),
          tgl: $('#tgl').val(),
          note: $('#note').val(),
          sales_order: $('#sales_order').val(),
          priority: $('#priority').val(),
          warehouse: $('#warehouse').val(),

        },
        success: function(data) {
          if (data.sesi == "habis") {
            //alert jika session habis
            alert_modal_warning(data.message);
            window.location.replace('../index');
          } else if (data.status == "failed") {
            //jika ada form belum keiisi
            refresh_procurement();
            $('#btn-simpan').button('reset');
            unblockUI(function() {
              setTimeout(function() {
                alert_notify(data.icon, data.message, data.type, function() {});
              }, 1000);
            });
            document.getElementById(data.field).focus();
          } else {
            //jika berhasil disimpan/diubah
            refresh_procurement();
            unblockUI(function() {
              setTimeout(function() {
                alert_notify(data.icon, data.message, data.type, function() {});
              }, 1000);
            });
            $('#btn-simpan').button('reset');
          }

        },
        error: function(xhr, ajaxOptions, thrownError) {
          alert(xhr.responseText);
          unblockUI(function() {});
          $('#btn-simpan').button('reset');

        }
      });
      window.setTimeout(function() {
        $(".alert").fadeTo(500, 0).slideUp(500, function() {
          $(this).remove();
        });
      }, 3000);
    });

    //klik button Batal
    $('#btn-cancel').click(function() {
      $("#ref_warehouse").load(location.href + " #ref_warehouse");
    });

    // duplicate DTI
    $(document).on('click', '#btn-duplicate', function(e) {
      e.preventDefault();
      var kode_pp = $('#kode_pp').val();
      var duplicate = 'true';

      if (kode_pp == "") {
        alert_modal_warning('Kode Procurement Purchase Kosong!');
      } else {
        var url = '<?php echo base_url() ?>ppic/procurementpurchase/add';
        window.open(url + '?kode_pp=' + kode_pp + '&&duplicate=' + duplicate, '_blank');
      }
    });
  </script>


</body>

</html>