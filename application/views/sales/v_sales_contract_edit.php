
<!DOCTYPE html>
<html>
<head>
  <?php $this->load->view("admin/_partials/head.php") ?>
  <style type="text/css">
    table.table th:last-child {
        width: 100px;
    }
    table.table td a {
       cursor: pointer;
       display: inline-block;
       margin: 0 5px;
       min-width: 24px;
    }    
    table.table td .add {
        display: none;
    }
    .width-btn {
      width: 54px !important;
    }

    table.table td .cancel {
        display: none;
        color : red;
        margin: 10 0px;
        min-width:  24px;
    }
    hr.garis_total {
       border: 1px solid grey;
       width: 90%;
    }

    table.table td .add-color-lines {
    	display: none;
    }

    table.table td .cancel-color-lines {
        display: none;
        color : red;
        margin: 10 0px;
        min-width:  24px;
    }

    .description{
      resize: vertical;
    }

   </style>
</head>

<body class="hold-transition skin-black fixed sidebar-mini" id="block-page">
<!-- Site wrapper -->
<div class="wrapper" >

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
  <div class="content-wrapper" >
    <!-- Content Header (Status - Bar) -->
    <section class="content-header"  >
      <div id ="status_bar">
       <?php 
         $data['jen_status'] =  $salescontract->status;
         $this->load->view("admin/_partials/statusbar.php", $data); 
       ?>
      </div>
    </section>

    <!-- Main content -->
    <section class="content">

      <!--  box content -->
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title"><b><?php echo $salescontract->sales_order;?></b></h3>
            <div class="pull-right text-right" id="btn-header">
            <?php if($salescontract->status=='draft'){?>
                <button class="btn btn-primary btn-sm" id="btn-confirm" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing...">Confirm Contract</button>
            <?php }elseif($salescontract->status=='date_assigned'){?>
                 <button class="btn btn-primary btn-sm" id="btn-approve" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing...">Approve Contract</button>
            <?php }elseif($salescontract->status=='waiting_color'){?>
            	 <button class="btn btn-primary btn-sm" id="btn-create-color" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing...">Create Color</button>	
                 <button class="btn btn-primary btn-sm" id="btn-approve-color" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing...">Approve Color</button>
            <?php }elseif($salescontract->status=='product_generated'){?>
                 <button class="btn btn-primary btn-sm" id="btn-approve-order" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing...">Approve Order</button>
            <?php }
            ?>
            </div>
        </div>
        <div class="box-body">
            <form class="form-horizontal">
            <div class="form-group">                  
              <div class="col-md-12" >
                <div id="alert"></div>
              </div>
            </div>
            <div class="form-group">

              <div class="col-md-6">
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Sales Order</label></div>
                  <div class="col-xs-8">
                    <input type="text" class="form-control input-sm" name="sales_order" id="sales_order" readonly="readonly" value="<?php echo $salescontract->sales_order?>" />
                  </div>                                    
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Customer</label></div>
                  <div class="col-xs-8">                   
                    <input type="text" class="form-control input-sm" name="customer" id="customer" readonly="readonly" value="<?php echo $salescontract->customer_name?>" />
                  </div>                                    
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Invoice Address</label></div>
                  <div class="col-xs-8">
                    <textarea type="text" class="form-control input-sm"name="invoice_address" id="invoice_address"  readonly="readonly"><?php echo $salescontract->invoice_address?> </textarea>
                  </div>                                    
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Delivery Address</label></div>
                  <div class="col-xs-8">
                    <textarea type="text" class="form-control input-sm" name="delivery_address" id="delivery_address" readonly="readonly"><?php echo $salescontract->delivery_address?></textarea>
                  </div>                                    
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Buyer Code </label></div>
                  <div class="col-xs-8">
                    <input type="text" class="form-control input-sm" name="buyer_code" id="buyer_code" readonly="readonly" value="<?php echo $salescontract->buyer_code?>"  />
                  </div>                                    
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Type</label></div>
                  <div class="col-xs-8">
                    <input type="text" class="form-control input-sm" name="type" id="type" readonly="readonly" value="<?php echo $salescontract->order_type?>"  />
                  </div>                                    
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Sales Person</label></div>
                  <div class="col-xs-8">
                    <input type="text" class="form-control input-sm" name="sales_person" id="sales_person" readonly="readonly" value="<?php echo $salescontract->nama_sales_group?>"  />
                  </div>                                    
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Order Production</label></div>
                  <div class="col-xs-8">
                    <?php if($salescontract->order_production == 'true'){?>
                            <input type="checkbox" name="order_production" id="order_production" checked disabled value="true">                            
                    <?php }else{?>
                            <input type="checkbox" name="order_production" id="order_production" disabled value="true">
                    <?php  }
                    ?>
                  </div>                                    
                </div>
              </div>

              <div class="col-md-6">
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Tanggal </label></div>
                  <div class="col-xs-8 col-md-8">
                    <input type='text' class="form-control input-sm" name="tgl" id="tgl" readonly="readonly" value="<?php echo $salescontract->create_date ?>"  />
                  </div>                                    
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Reference/Description</label></div>
                  <div class="col-xs-8">
                    <input type="text" class="form-control input-sm" name="reference" id="reference" value="<?php echo $salescontract->reference?>" />
                  </div>                                    
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Warehouse</label></div>
                  <div class="col-xs-8">
                    <select class="form-control input-sm" name="warehouse" id="warehouse" />
                      <option value="">Pilih Warehouse</option>
                      <?php foreach ($warehouse as $row) {
                        if($row->kode==$salescontract->warehouse){?>
                         <option value='<?php echo $row->kode; ?>' selected><?php echo $row->nama;?></option>
                      <?php
                        }else{?>
                        <option value='<?php echo $row->kode; ?>'><?php echo $row->nama;?></option>
                      <?php  }}?>
                    </select>
                  </div>                                    
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Currency</label></div>
                  <div class="col-xs-8">
                    <select class="form-control input-sm" name="currency" id="currency" />
                     <option value="">Pilih Currency</option>
                      <?php foreach ($currency as $row) {
                        if($row->nama==$salescontract->currency_nama){?>
                         <option selected><?php echo $row->nama;?></option>
                      <?php
                        }else{?>
                        <option><?php echo $row->nama;?></option>
                      <?php  }}?>
                    </select>
                  </div>                                    
                </div>
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Delivery Date</label></div>
                  <div class="col-xs-8">
                    <input type="text" class="form-control input-sm" name="delivery_date" id="delivery_date" value="<?php echo $salescontract->delivery_date ?>" />
                  </div>                                    
                </div>
                <!--div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Time Of Shipment</label></div>
                  <div class="col-xs-8">
                    <input type="text" class="form-control input-sm" name="time_ship" id="time_ship" value="<?php echo $salescontract->time_shipment?>" />
                  </div--> 
                <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4"><label>Note</label></div>
                  <div class="col-xs-8">
                    <textarea type="text" class="form-control input-sm" name="note_head" id="note_head" ><?php echo $salescontract->note_head?></textarea>
                    <div id="ref_status">
                      <input type="hidden" name="status" id="status" value="<?php echo $salescontract->status?>">
                    </div>
                  </div>                                    
                </div>                                  
                </div>
              </div>
            </div>

            <?php 

          //nl2br(htmlspecialchars($sc->bank));
            ?>

            <div class="row">
              <div class="col-md-12">
                <!-- Custom Tabs -->
                <div class="">
                  <ul class="nav nav-tabs">
                    <li class="active"><a href="#tab_1" data-toggle="tab">Contract Lines</a></li>
                    <li><a href="#tab_2" data-toggle="tab">Color Lines</a></li>
                    <li><a href="#tab_3" data-toggle="tab">Other Information</a></li>
                  </ul>
                  <div class="tab-content"><br>
                    <div class="tab-pane active" id="tab_1">
                      <!-- Tabel  contract lines-->
                      <div class="col-md-12 table-responsive">
                      <table class="table table-condesed table-hover table-responsive rlstable" id="contract_lines" > 
                        <thead>
                          <tr>
                            <th class="style no">No.</th>
                            <th class="style" width="200px">Product</th>
                            <th class="style" width="150px">Description</th>
                            <th class="style" width="120px" style="text-align: right;">Qty</th>
                            <th class="style" width="50px" >Uom</th>
                            <!--th class="style" width="120px">Roll Info</th-->
                            <th class="style" width="120px">Unit Price</th>
                            <th class="style" width="130px">Taxes</th>
                            <th class="style" width="150">Subtotal</th>
                            <th class="style" width="100px">Due Date</th>
                            <th class="style" width="50px"></th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php 
                            $no = 1;
                            foreach ($details as $row) {
                          ?>
                            <tr class="num">
                              <td data-content="edit" data-id="row_order" data-isi="<?php echo $row->row_order;?>"></td>
                              <td class="text-wrap width-200" data-content="edit" data-id="kode_produk" data-isi="<?php echo $row->kode_produk;?>" data-id2="prodhidd" data-isi2="<?php echo htmlentities($row->nama_produk)?>"><?php echo $row->nama_produk?></td>
                              <td class="text-wrap width-150" data-content="edit" data-id="description" data-isi="<?php echo htmlentities($row->description);?>"><?php echo $row->description?></td>
                              <td class="width-80" align="right" data-content="edit" data-id="qty"  data-isi="<?php echo $row->qty;?>"><?php echo number_format($row->qty,2)?></td>
                              <td class="width-100" data-content="edit" data-id="uom"  data-isi="<?php echo $row->uom;?>"><?php echo $row->uom?></td>
                              <!--td class="text-wrap width-120" data-content="edit" data-id="roll" data-isi="<?php echo $row->roll_info;?>"><?php echo $row->roll_info?></td-->
                              <td class="width-100" align="right" data-content="edit" data-id="price" data-isi="<?php echo ($row->price);?>"><?php echo number_format($row->price,4)?></td>
                              <td data-content="edit" data-id="taxes" data-isi='<select type="text" class="form-control input-sm tax" name="taxes" id="taxes"><option value="">-Taxes-</option><?php foreach($tax as $val){if($val->id==$row->tax_id){?><option value="<?php echo $val->id; ?>" selected><?php echo $val->nama;?></option><?php }else{?> <option value="<?php echo $val->id; ?>"><?php echo $val->nama;?></option> <?php }}?>'><?php echo $row->tax_nama?>
                                
                              </td>
                              <td class="load" align="right"><?php echo number_format(($row->price*$row->qty),4); ?></td>
                              <td class="width-100"><?php echo $row->due_date?></td> 
                              <td class="width-120">
                                  <a class="add" title="Simpan" data-toggle="tooltip" row_id='tes'><i class="fa fa-save"></i></a>
                                  <a class="edit" title="Edit" data-toggle="tooltip" style="color: #FFC107;"><i class="fa fa-edit"></i></a>
                                  <a class="delete" title="Hapus" data-toggle="tooltip"><i class="fa fa-trash" style="color: red"></i></a>
                                  <a class="cancel" title="Cancel" data-toggle="tooltip" style="margin-left: 20px;"><i class="fa fa-close"></i></a>
                              </td>
                          <?php 
                            }     
                          ?>
                       
                            </tr>
                          </tbody>
                          <tfoot>
                            <tr>
                              <td colspan="11">
                                <a href="javascript:void(0)" class="add-new"><i class="fa fa-plus"></i> Tambah Data</a>
                              </td>
                            </tr>
                          <tfoot>
                        </table>
                      </div>
                      <!-- Tabel  -->
                      <!--Total-->
                       <div id="total" >
                        <div class="col-md-6">
                        </div>
                        <div class="col-md-6" class="pull-right text-right">
                          <div class="form-group"> 
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-4"><label>Untaxed Amount</label></div>
                              <div class="col-xs-1" >: </div>
                              <div class="col-xs-8 col-md-7" align="right">
                                 <?php echo $salescontract->currency_symbol." ".number_format($salescontract->untaxed_value,4); ?>
                              </div>                                    
                            </div>
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-4"><label>Taxes </label></div>
                               <div class="col-xs-1" >: </div>
                              <div class="col-xs-8 col-md-7" align="right">
                                <?php echo $salescontract->currency_symbol." ".number_format($salescontract->tax_value,4); ?>
                              </div>
                            </div>
                          
                            <div class="col-md-12 col-xs-12">  <hr class="garis_total">
                              <div class="col-xs-4"><label>Total </label></div>
                              <div class="col-xs-1" >: </div>
                              <div class="col-xs-8 col-md-7" align="right">
                                <?php echo $salescontract->currency_symbol ." ".number_format($salescontract->total_value,4); ?>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                      <!-- /.Total-->
                    </div>
                    <!-- /.tab-pane 1 -->

                    <div class="tab-pane " id="tab_2">
                      <!-- Tabel  contract lines-->
                      <div class="col-md-12 table-responsive">
	                      <table class="table table-condesed table-hover table-responsive rlstable" id="color_lines"> 
	                        <thead>
	                          <tr>
	                            <th class="style no">No.</th>
	                            <th class="style" width="150px">Product</th>
	                            <th class="style" width="150px">Description</th>
	                            <th class="style" width="150px">Color</th>
	                            <th class="style" width="150px">Color Name</th>
	                            <th class="style" width="80px" style="text-align: right;">Qty</th>
	                            <th class="style" width="150px" >Piece Info</th>
	                            <th class="style" width="50px" >Uom</th>
	                            <th class="style" width="50px"></th>
	                          </tr>
	                        </thead>
	                        <tbody>
                            <?php 
                                  $no = 1;
                                  foreach ($details_color_lines as $row) {
                                ?>
                                  <tr class="num">
                                    <td data-content="edit" data-id="row_order" data-isi="<?php echo $row->row_order;?>"></td>
                                    <td class="text-wrap width-150"><?php echo $row->nama_produk?></td>
                                    <td class="text-wrap width-150" data-content="edit" data-id="description_color" data-isi="<?php echo htmlentities($row->description);?>"><?php echo $row->description?></td>
                                    <td class="text-wrap" data-id="color"  data-isi="<?php echo $row->kode_warna;?>"><?php echo $row->kode_warna?></td>
                                    <td class="text-wrap" data-content="edit" data-id="color_name"  data-isi="<?php echo $row->color_alias_name;?>"><?php echo $row->color_alias_name?></td>
                                    <td align="right" data-content="edit" data-id="qty"  data-isi="<?php echo $row->qty;?>"><?php echo $row->qty?></td>
                                    <td class="text-wrap width-150" data-content="edit" data-id="piece_info" data-isi="<?php echo $row->piece_info;?>"><?php echo $row->piece_info?></td>
                                    <td data-content="edit" data-id="uom"  data-isi="<?php echo $row->uom;?>"><?php echo $row->uom?></td>
                                    <td>
                                        <a class="add-color-lines" title="Simpan" data-toggle="tooltip" row_id='tes'><i class="fa fa-save"></i></a>
                                        <a class="edit-color-lines" title="edit" data-toggle="tooltip" style="color: #FFC107;"><i class="fa fa-edit"></i></a>
                                        <a class="delete-color-lines" title="Hapus" data-toggle="tooltip"><i class="fa fa-trash" style="color: red"></i></a>
                                        <a class="cancel-color-lines" title="Cancel" data-toggle="tooltip" style="margin-left: 20px;"><i class="fa fa-close"></i></a>
                                    </td>
                                <?php 
                                  }     
                                ?>
                       
                          </tbody>
                          <tfoot>
                            <tr>
                              <td colspan="11">
                                <a href="javascript:void(0)" class="add-new-color-lines"><i class="fa fa-plus"></i> Tambah Data</a>
                              </td>
                            </tr>
                          <tfoot>
                        </table>
                      </div>
                      <!-- Tabel  -->
                    </div>
                    <!-- /.tab-pane 2 -->
                    
                    <div class="tab-pane" id="tab_3">
                      <form class="form-horizontal">
                        <div class="form-group">

                          <div class="col-md-6">
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-4"><label>Incoterm</label></div>
                              <div class="col-xs-8">
                                <select class="form-control input-sm" name="incoterm" id="incoterm" />
                                  <option value="">- Pilih Incoterm -</option>
                                  <?php foreach ($incoterm as $row) {
                                    if($row->id==$salescontract->incoterm_id){?>
                                     <option value='<?php echo $row->id; ?>' selected><?php echo $row->nama;?></option>
                                  <?php
                                    }else{?>
                                    <option value='<?php echo $row->id; ?>'><?php echo $row->nama;?></option>
                                  <?php  
                                    }
                                   }?>
                                </select>
                              </div>                                    
                            </div>
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-4"><label>Payment Term</label></div>
                              <div class="col-xs-8">
                                <select class="form-control input-sm" name="paymentterm" id="paymentterm" />
                                  <option value="">- Pilih Payment Term -</option>
                                  <?php foreach ($paymentterm as $row) {
                                    if($row->id==$salescontract->paymentterm_id){?>
                                     <option value='<?php echo $row->id; ?>' selected><?php echo $row->nama;?></option>
                                  <?php
                                    }else{?>
                                    <option value='<?php echo $row->id; ?>'><?php echo $row->nama;?></option>
                                  <?php  
                                    }
                                   }?>
                                </select>
                              </div>                                    
                            </div>
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-4"><label>Destination</label></div>
                              <div class="col-xs-8">
                                <input type="text" class="form-control input-sm" name="destination" id="destination" value="<?php echo $salescontract->destination?>">
                              </div>                                    
                            </div>
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-4"><label>Bank</label></div>
                              <div class="col-xs-8">
                                <textarea type="text" class="form-control input-sm" name="bank" id="bank" ><?php   echo $salescontract->bank?></textarea>
                              </div>                                    
                            </div>
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-4"><label>Clause to be Mentioned on L/C</label></div>
                              <div class="col-xs-8">
                                <textarea type="text" class="form-control input-sm" name="clause" id="clause" ><?php echo $salescontract->clause?></textarea>
                              </div>                                    
                            </div>
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-4"><label>Note</label></div>
                              <div class="col-xs-8">
                                <textarea type="text" class="form-control input-sm" name="note" id="note" ><?php echo $salescontract->note?></textarea>
                              </div>                                    
                            </div>
                          </div>

                        </div>
                      </form>
                    </div>
                    <!-- /.tab-pane 3 -->
              
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
    <div id="foot">
      <?php 
        $data['kode'] =  $salescontract->sales_order;
        $data['mms']  =  $mms->kode;
        $this->load->view("admin/_partials/footer.php",$data) 
      ?>
    </div>
  </footer>

    <!-- Load Partial Modal -->
   <?php $this->load->view("admin/_partials/modal.php") ?>

</div>

<?php $this->load->view("admin/_partials/js.php") ?>
<!--script type="text/javascript" src="<?php echo base_url('dist/js/js_sales_contract_view.js') ?>"></script-->

<script type="text/javascript">

  //html entities javascript
  function htmlentities_script(str) {
    return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
  }

  function validAngka(a){
    if(!/^[0-9.]+$/.test(a.value)){
      a.value = a.value.substring(0,a.value.length-1000);
      alert_notify('fa fa-warning','Maaf, Inputan Hanya Berupa Angka !','danger',function(){});
    }
  }

  function refresh_tab_and_div(){
    $("#tab_1").load(location.href + " #tab_1");
    $("#tab_2").load(location.href + " #tab_2");
    $("#status_bar").load(location.href + " #status_bar");
    $("#foot").load(location.href + " #foot");
    $("#total").load(location.href + " #total");
  }

  // Append table with add row form on add new button click
  $(document).on("click", ".add-new", function(){

    var status = $("#status").val();

    if(status == 'draft' || status == 'waiting_date' ){

    $(".add-new").hide();
    var index = $("#contract_lines tbody tr:last-child").index();
    var row   ='<tr class="num">'
          + '<td></td>'
          + '<td class="width-300"><select type="text" class="form-control input-sm prod" name="Product" id="product"></select></td>'
          + '<td><textarea class="form-control description" name="Description" id="description"></textarea><input type="hidden" class="form-control input-sm prodhidd" name="prodhidd" id="prodhidd"></td>'
          + '<td class="width-150"><input type="text" class="form-control input-sm" name="Qty" id="qty" onkeyup="validAngka(this)"></td>'
          + '<td class="width-120"><select type="text" class="form-control input-sm uom" name="Uom" id="uom"></select></td>'
          //+ '<td class="width-120"><input type="text" class="form-control input-sm" name="roll" id="roll"></td>'
          + '<td class="width-150"><input type="text" class="form-control input-sm" name="Unit Price" id="price" onkeyup="validAngka(this)"></td>'
          + '<td class="width-150"><select type="text" class="form-control input-sm tax" name="taxes" id="taxes"><option value="">-Taxes-</option><?php foreach($tax as $row){?><option value="<?php echo $row->id; ?>"><?php echo $row->nama;?></option>"<?php }?></select></td>'
          + '<td></td>'
          + '<td></td>'
          + '<td class="width-120"><button type="button" class="btn btn-primary btn-xs add width-btn" title="Simpan" data-toggle="tooltip">Simpan</button><a class="edit" title="Edit" data-toggle="tooltip"><i class="fa fa-edit"></i></a><button type="button" class="btn btn-danger btn-xs batal width-btn" title="Batal" data-toggle="tooltip">Batal</button></td>'
          + '</tr>';


        $('#contract_lines tbody').append(row);
        $("#contract_lines tbody tr").eq(index + 1).find(".add, .edit").toggle();
        $('[data-toggle="tooltip"]').tooltip();

        //select 2 product
        $('.prod').select2({
          allowClear: true,
          placeholder: "",
          ajax:{
                dataType: 'JSON',
                type : "POST",
                url : "<?php echo base_url();?>sales/salescontract/get_produk_select2",
                //delay : 250,
                data : function(params){
                  return{
                    prod:params.term
                  };
                }, 
                processResults:function(data){
                  var results = [];

                  $.each(data, function(index,item){
                      results.push({
                          id:item.kode_produk,
                          text:item.nama_produk
                      });
                  });
                  return {
                    results:results
                  };
                },
                error: function (xhr, ajaxOptions, thrownError){
                //  alert('Error data');
                //  alert(xhr.responseText);
                }
          }
        });

      $(".prod").change(function(){
          $.ajax({
                dataType: "JSON",
                url : '<?php echo site_url('sales/salescontract/get_prod_by_id') ?>',
                type: "POST",
                data: {kode_produk: $(this).parents("tr").find("#product").val() },
                success: function(data){
                  //alert(data.nama_produk);
                  $('.prodhidd').val(data.nama_produk);
                  $('.description').val(data.nama_produk);
                  //$('.uom').val(data.uom);

                  var $newOptionuom = $("<option></option>").val(data.uom).text(data.uom);
                  $(".uom").empty().append($newOptionuom).trigger('change');
                },
                error: function (xhr, ajaxOptions, thrownError){
                //  alert('Error data');
                //  alert(xhr.responseText);
                }
          });
      });



      //select 2 uom
      $('.uom').select2({
        allowClear: true,
        placeholder: "",
        ajax:{
              dataType: 'JSON',
              type : "POST",
              url : "<?php echo base_url();?>sales/salescontract/get_uom_select2",
              data : function(params){

                return{
                  prod:params.term,
                };
              }, 
              processResults:function(data){
                var results = [];
                $.each(data, function(index,item){
                  results.push({
                      id:item.short,
                      text:item.short
                  });
                });
                return {
                  results:results
                };
              },
              error: function (xhr, ajaxOptions, thrownError){
              //  alert('Error data');
              //  alert(xhr.responseText);
              }
        }
      });

    }else{
      alert_modal_warning('Maaf, Data items tidak bisa Ditambah !');
    }
  });


    // simpan / edit row data ke database
    $(".add").unbind( "click" );
    $(document).on("click", ".add", function(){
      var empty = false;
      var input = $(this).parents("tr").find('input[type="text"]');

      var empty2 = false;
      var select = $(this).parents("tr").find('select[type="text"]');

      //validasi tidak boleh kosong hanya select product saja
      select.each(function(){
        if(!$(this).val() && $(this).attr('name')=='Product' ){
          alert_notify('fa fa-warning',$(this).attr('name')+ ' Harus Diisi !','danger',function(){});
          empty2 = true;
        }
      });

      // validasi untuk inputan textbox
      input.each(function(){
        if(!$(this).val() && $(this).attr('name')!='roll'){
          alert_notify('fa fa-warning',$(this).attr('name')+ ' Harus Diisi !','danger',function(){});
          empty = true;
        }
      });

      if(!empty && !empty2){
        var kode  =  "<?php echo $salescontract->sales_order; ?>";
        var kode_prod  = $(this).parents("tr").find("#product").val();
        var prod  = $(this).parents("tr").find("#prodhidd").val();
        var desc  = $(this).parents("tr").find("#description").val();
        var qty   = $(this).parents("tr").find("#qty").val();
        var uom   = $(this).parents("tr").find("#uom").val();
        var roll  = '';
        //var roll  = $(this).parents("tr").find("#roll").val();
        var price = $(this).parents("tr").find("#price").val();
        var taxes = $(this).parents("tr").find("#taxes").val();
        var row_order = $(this).parents("tr").find("#row_order").val();
        var dat = $(this).parents("tr").find('input[type="text"]').val();

        $.ajax({
          dataType: "JSON",
          url : '<?php echo site_url('sales/salescontract/simpan_detail') ?>',
          type: "POST",
          data: {kode : kode, 
                kode_prod  : kode_prod,
                prod  : prod,
                desc  : desc, 
                qty   : qty,
                uom   : uom,
                roll  : roll,
                price : price,
                taxes : taxes,
                row_order : row_order  },
          success: function(data){
            if(data.sesi=='habis'){
                //alert jika session habis
                alert_modal_warning(data.message);
                window.location.replace('../index');
            }else{
                $("#tab_1").load(location.href + " #tab_1");
                $("#foot").load(location.href + " #foot");
                //$("#total").load(location.href + " #total");
                $(".add-new").show();                   
                alert_notify(data.icon,data.message,data.type,function(){});
                $("#btn-header").load(location.href + " #btn-header");
                refresh_tab_and_div();
             }
          },
          error: function (xhr, ajaxOptions, thrownError){
            alert('Error data');
            alert(xhr.responseText);
          }
        });
        
      }   
    });


    // Edit row on edit button click
    $(document).on("click", ".edit", function(){  
      var status = $("#status").val();

       if(status == 'draft' || status == 'waiting_date' || status == 'date_assigned'){

        $(this).parents("tr").find("td[data-content='edit']").each(function(){
          if($(this).attr('data-id')=="row_order"){
            $(this).html('<input type="hidden"  class="form-control" value="' + $(this).attr('data-isi') + '" id="'+ $(this).attr('data-id') +'"> ');
            row_order = $(this).attr('data-isi');
          }else if($(this).attr('data-id') == 'kode_produk'){

            var kode_produk = $(this).attr('data-isi');
            var nama_produk = $(this).attr('data-isi2');

            class_sel2_prod = 't_sel2_prod'+row_order;
            class_nama_produk = 'e_nama_produk'+row_order;

            $(this).html('<select type="text"  class="form-control input-sm '+class_sel2_prod+' " id="product" name="Product" ></select> ' + '<input type="hidden"  class="form-control '+class_nama_produk+' " value="' + htmlentities_script($(this).attr('data-isi2')) + '" id="'+ $(this).attr('data-id2') +'"> ');

            // append berdasarkan nama produk
            $newOption = new Option(nama_produk, kode_produk, true, true);
            $('.t_sel2_prod'+row_order).append($newOption).trigger('change');

             //select 2 product
            $('.t_sel2_prod'+row_order).select2({
              allowClear: true,
              placeholder: "",
              ajax:{
                    dataType: 'JSON',
                    type : "POST",
                    url : "<?php echo base_url();?>sales/salescontract/get_produk_select2",
                    //delay : 250,
                    data : function(params){
                      return{
                        prod:params.term
                      };
                    }, 
                    processResults:function(data){
                      var results = [];

                      $.each(data, function(index,item){
                          results.push({
                              id:item.kode_produk,
                              text:item.nama_produk
                          });
                      });
                      return {
                        results:results
                      };
                    },
                    error: function (xhr, ajaxOptions, thrownError){
                    //  alert('Error data');
                    //  alert(xhr.responseText);
                    }
              }
            });

            $('.t_sel2_prod'+row_order).change(function(){
              $.ajax({
                    dataType: "JSON",
                    url : '<?php echo site_url('sales/salescontract/get_prod_by_id') ?>',
                    type: "POST",
                    data: {kode_produk: $(this).parents("tr").find("#product").val() },
                    success: function(data){
                      //alert(data.nama_produk);
                      $('.e_nama_produk'+row_order).val(data.nama_produk);
                      $('.description'+row_order).val(data.nama_produk);
                      //$('.uom').val(data.uom);

                      var $newOptionuom = $("<option></option>").val(data.uom).text(data.uom);
                      $(".uom"+row_order).empty().append($newOptionuom).trigger('change');
                    },
                    error: function (xhr, ajaxOptions, thrownError){
                    //  alert('Error data');
                    //  alert(xhr.responseText);
                    }
              });
          });


          }else if($(this).attr('data-id')=='uom'){

            class_uom = 'uom'+row_order;

            $(this).html('<select type="text"  class="form-control input-sm '+class_uom+'" id="'+ $(this).attr('data-id') +'" name="Uom" ></select> ');

            var $newOptionuom = $("<option></option>").val($(this).attr('data-isi') ).text($(this).attr('data-isi') );
            $(".uom"+row_order).empty().append($newOptionuom).trigger('change');

            $('.uom'+row_order).select2({
              allowClear: true,
              placeholder: "",
              ajax:{
                    dataType: 'JSON',
                    type : "POST",
                    url : "<?php echo base_url();?>sales/salescontract/get_uom_select2",
                    data : function(params){

                      return{
                        prod:params.term,
                      };
                    }, 
                    processResults:function(data){
                      var results = [];
                      $.each(data, function(index,item){
                        results.push({
                            id:item.short,
                            text:item.short
                        });
                      });
                      return {
                        results:results
                      };
                    },
                    error: function (xhr, ajaxOptions, thrownError){
                    //  alert('Error data');
                    //  alert(xhr.responseText);
                    }
              }
            }); 
          }else if($(this).attr('data-id')=="description"){
            class_desc = 'description'+row_order;
            $(this).html('<textarea type="text" class="form-control input-sm '+class_desc+'" id="'+ $(this).attr('data-id') +'" name="'+ $(this).attr('data-id') +'">'+ htmlentities_script($(this).attr('data-isi')) +'</textarea>');
          
          }else if($(this).attr('data-id')=="taxes"){
            $(this).html($(this).attr('data-isi'));
          }else if($(this).attr('data-id')=='qty' || $(this).attr('data-id')=='price'){
            $(this).html('<input type="text"  class="form-control input-sm" value="'+ htmlentities_script($(this).attr('data-isi')) +'" id="'+ $(this).attr('data-id') +'" name="'+ $(this).attr('data-id') +'" onkeyup="validAngka(this)"> ');
          }else{
            $(this).html('<input type="text"  class="form-control input-sm" value="'+ htmlentities_script($(this).attr('data-isi')) +'" id="'+ $(this).attr('data-id') +'" name="'+ $(this).attr('data-id') +'"> ');
          }

        });  

        $(this).parents("tr").find(".add, .edit").toggle();
        $(this).parents("tr").find(".cancel, .delete").toggle();
        $(".add-new").hide();
      }else{
         alert_modal_warning('Maaf, Data tidak bisa diubah !')
      }
    });
    
    // batal add row on batal button click
    $(document).on("click", ".batal", function(){
      var input = $(this).parents("tr").find('.prod');
      input.each(function(){
       $(this).parent("td").html($(this).val());
      }); 
      
      $(this).parents("tr").remove();
      $(".add-new").show();
    });

    
    //delete row di database
    $(document).on("click", ".delete", function(){ 

     var status = $("#status").val();
     if(status == 'draft' || status == 'waiting_date'){

      $(this).parents("tr").find("td[data-content='edit']").each(function(){
        if($(this).attr('data-id')=="row_order"){
          $(this).html('<input type="hidden" class="form-control" value="' + $(this).attr('data-isi') + '" id="'+ $(this).attr('data-id') +'"> ');
        }
      });
      var kode  =  "<?php echo $salescontract->sales_order; ?>";
      var row_order = $(this).parents("tr").find("#row_order").val();  
      bootbox.dialog({
        message: "Apakah Anda ingin menghapus data ?",
        title: "<i class='glyphicon glyphicon-trash'></i> Delete !",
        buttons: {
          danger: {
              label    : "Yes ",
              className: "btn-primary btn-sm",
              callback : function() {
                  $.ajax({
                      dataType: "JSON",
                      url : '<?php echo site_url('sales/salescontract/hapus_detail') ?>',
                      type: "POST",
                      data: {kode : kode, 
                            row_order : row_order  },
                      success: function(data){
                        if(data.sesi=='habis'){
                            //alert jika session habis
                            alert_modal_warning(data.message);
                            window.location.replace('../index');
                        }else if(data.status =='failed'){
                            alert_modal_warning(data.message);
                            $("#btn-header").load(location.href + " #btn-header");
                            refresh_tab_and_div();
                        }else{
                            refresh_tab_and_div();
                            $("#btn-header").load(location.href + " #btn-header");
                            $(".add-new").show();                   
                            alert_notify(data.icon,data.message,data.type,function(){});
                         }
                      },
                      error: function (xhr, ajaxOptions, thrownError){
                        alert('Error data');
                        alert(xhr.responseText);
                      }
                    });
              }
          },
          success: {
                label    : "No",
                className: "btn-default  btn-sm",
                callback : function() {
                  $('.bootbox').modal('hide');
                }
          }
        }
        });
    }else{
      alert_modal_warning('Maaf, Data tidak bisa di Hapus !')
    }
    });

    //btn cancel edit
    $(document).on("click", ".cancel", function(){
        $("#tab_1").load(location.href + " #tab_1");
        //$("#total").load(location.href + " #total");
        $(".add-new").show();
        /*
      var input = $(this).parents("tr").find('input[type="text"]');
      input.each(function(){
        $(this).parent("td").html($(this).attr('value'));
      }); 
      $(this).parents("tr").find(".edit, .add").toggle();
      $(this).parents("tr").find(".delete, .cancel").toggle();
        */
    });



  /* START COLOR LINES */
  
  // Append table with add row form on add new button click
  $(document).on("click", ".add-new-color-lines", function(){
    
    //no SO
    var kode  =  "<?php echo $salescontract->sales_order; ?>";
    var status = $("#status").val();

    if(status == 'waiting_color' || status == 'product_generated'){

    $(".add-new-color-lines").hide();
    var index = $("#color_lines tbody tr:last-child").index();
    var row   ='<tr class="num">'
          + '<td></td>'
          + '<td><select type="text" class="form-control input-sm prod_color" name="Product" id="product"></select></td>'
          + '<td><input type="text" class="form-control input-sm description_color" name="Description" id="description_color"></select><input type="hidden" class="form-control input-sm prodhidd_color" name="prodhidd" id="prodhidd_color"></td>'
          + '<td><select type="text" class="form-control input-sm color" name="Color" id="color"></select></td>'
          + '<td><input type="text" class="form-control input-sm" name="Color Name" id="color_name"></td>'
          + '<td><input type="text" class="form-control input-sm" name="Qty" id="qty" onkeyup="validAngka(this)"></td>'
          + '<td><input type="text" class="form-control input-sm" name="Piece Info" id="piece_info"></td>'
          + '<td><input type="text" class="form-control input-sm uom_color" name="Uom" id="uom"></td>'
          + '<td><button type="button" class="btn btn-primary btn-xs add-color-lines width-btn" title="Simpan" data-toggle="tooltip">Simpan</button><a class="edit-color-lines" title="Edit" data-toggle="tooltip"><i class="fa fa-edit"></i></a><button type="button" class="btn btn-danger btn-xs batal-color-lines width-btn" title="Batal" data-toggle="tooltip">Batal</button></td>'
          + '</tr>';


        $('#color_lines tbody').append(row);
        $("#color_lines tbody tr").eq(index + 1).find(".add-color-lines, .edit-color-lines").toggle();
        $('[data-toggle="tooltip"]').tooltip();

        //select 2 product
        $('.prod_color').select2({
          allowClear: true,
          placeholder: "",
          ajax:{
                dataType: 'JSON',
                type : "POST",
                url : "<?php echo base_url();?>sales/salescontract/get_produk_color_select2",
                //delay : 250,
                data : function(params){
                  return{
                    kode : kode,
                    prod:params.term
                  };
                }, 
                processResults:function(data){
                  var results = [];

                  $.each(data, function(index,item){
                      results.push({
                          id:item.kode_produk,
                          text:item.nama_produk
                      });
                  });
                  return {
                    results:results
                  };
                },
                error: function (xhr, ajaxOptions, thrownError){
                //  alert('Error data');
                //  alert(xhr.responseText);
                }
          }
        });

        $(".prod_color").change(function(){
          $.ajax({
                dataType: "JSON",
                url : '<?php echo site_url('sales/salescontract/get_prod_by_id') ?>',
                type: "POST",
                data: {kode_produk: $(this).parents("tr").find("#product").val() },
                success: function(data){
                  //alert(data.nama_produk);
                  $('.prodhidd_color').val(data.nama_produk);
                  $('.description_color').val(data.nama_produk);
                  $('.uom_color').val(data.uom);
                },
                error: function (xhr, ajaxOptions, thrownError){
                //  alert('Error data');
                //  alert(xhr.responseText);
                }
          });
        });


          //select 2 color
        $('.color').select2({
          allowClear: true,
          placeholder: "",
          ajax:{
                dataType: 'JSON',
                type : "POST",
                url : "<?php echo base_url();?>sales/salescontract/get_color_select2",
                //delay : 250,
                data : function(params){
                  return{
                    prod:params.term
                  };
                }, 
                processResults:function(data){
                  var results = [];

                  $.each(data, function(index,item){
                      results.push({
                          id:item.kode_warna,
                          text:item.kode_warna
                      });
                  });
                  return {
                    results:results
                  };
                },
                error: function (xhr, ajaxOptions, thrownError){
                //  alert('Error data');
                //  alert(xhr.responseText);
                }
          }
        }); 

    }else{
      alert_modal_warning('Maaf, Data items tidak bisa Ditambah !');
    }
  });    


  // simpan / edit row data ke database COLOR LINES
    $(".add-color-lines").unbind( "click" );
    $(document).on("click", ".add-color-lines", function(){
      var empty = false;
      var input = $(this).parents("tr").find('input[type="text"]');

      var empty2 = false;
      var select = $(this).parents("tr").find('select[type="text"]');

      //validasi tidak boleh kosong hanya select product saja
      select.each(function(){
        if(!$(this).val() && $(this).attr('name')=='Product' ){
          alert_notify('fa fa-warning',$(this).attr('name')+ ' Harus Diisi !','danger',function(){});
          empty2 = true;
        }

        if(!$(this).val() && $(this).attr('name')=='Color'){
           alert_notify('fa fa-warning', $(this).attr('name')+ ' Harus Diisi !', 'danger',function(){});
        }
      });

      // validasi untuk inputan textbox
      input.each(function(){
        if(!$(this).val() && $(this).attr('name')!='Piece Info'){
          alert_notify('fa fa-warning',$(this).attr('name')+ ' Harus Diisi !','danger',function(){});
          empty = true;
        }
      });

      if(!empty && !empty2){
        var kode  =  "<?php echo $salescontract->sales_order; ?>";
        var kode_prod  = $(this).parents("tr").find("#product").val();
        var prod  = $(this).parents("tr").find("#prodhidd_color").val();
        var desc  = $(this).parents("tr").find("#description_color").val();
        var color  = $(this).parents("tr").find("#color").val();
        var color_name = $(this).parents("tr").find("#color_name").val();
        var qty   = $(this).parents("tr").find("#qty").val();
        var uom   = $(this).parents("tr").find("#uom").val();
        var piece_info  = $(this).parents("tr").find("#piece_info").val();
        var row_order = $(this).parents("tr").find("#row_order").val();
        //var dat = $(this).parents("tr").find('input[type="text"]').val();
              
        $.ajax({
          dataType: "JSON",
          url : '<?php echo site_url('sales/salescontract/simpan_detail_color_lines') ?>',
          type: "POST",
          data: {kode : kode, 
                kode_prod  : kode_prod,
                prod  : prod,
                color : color,
                color_name : color_name,
                desc  : desc, 
                qty   : qty,
                uom   : uom,
                piece_info: piece_info,
                row_order : row_order  },
          success: function(data){
            if(data.sesi=='habis'){
                //alert jika session habis
                alert_modal_warning(data.message);
                window.location.replace('../index');
            }else if(data.status == 'failed'){
              alert_modal_warning(data.message);
            }else{
                $("#tab_2").load(location.href + " #tab_2");
                $("#foot").load(location.href + " #foot");
                //$("#total").load(location.href + " #total");
                $(".add-new-color-lines").show();                   
                alert_notify(data.icon,data.message,data.type,function(){});
             }
          },
          error: function (xhr, ajaxOptions, thrownError){
            alert('Error data');
            alert(xhr.responseText);
          }
        });

      }   
    });

    // batal add row on batal button click COLOR LINES
    $(document).on("click", ".batal-color-lines", function(){
      var input = $(this).parents("tr").find('.prod_color');
      input.each(function(){
       $(this).parent("td").html($(this).val());
      }); 
      
      $(this).parents("tr").remove();
      $(".add-new-color-lines").show();
    });


    $(document).on("click", ".delete-color-lines", function(){ 

     var status = $("#status").val();
     if(status == 'waiting_color'){

      $(this).parents("tr").find("td[data-content='edit']").each(function(){
        if($(this).attr('data-id')=="row_order"){
          $(this).html('<input type="text" class="form-control" value="' + $(this).attr('data-isi') + '" id="'+ $(this).attr('data-id') +'"> ');
        }
      });
     

      var kode  =  "<?php echo $salescontract->sales_order; ?>";
      var row_order = $(this).parents("tr").find("#row_order").val();  

        bootbox.dialog({
          message: "Apakah Anda ingin menghapus data ?",
          title: "<i class='glyphicon glyphicon-trash'></i> Delete !",
          buttons: {
            danger: {
                label    : "Yes ",
                className: "btn-primary btn-sm",
                callback : function() {
                    $.ajax({
                        dataType: "JSON",
                        url : '<?php echo site_url('sales/salescontract/hapus_detail_color_lines') ?>',
                        type: "POST",
                        data: {kode : kode, 
                              row_order : row_order  },
                        success: function(data){
                          if(data.sesi=='habis'){
                              //alert jika session habis
                              alert_modal_warning(data.message);
                              window.location.replace('../index');
                          }else if(data.status == 'failed'){
                              $("#tab_2").load(location.href + " #tab_2");
                              $("#foot").load(location.href + " #foot");
                              $(".add-new-color-lines ").show();
                              alert_modal_warning(data.message);
                          }else{
                              $("#tab_2").load(location.href + " #tab_2");
                              $("#foot").load(location.href + " #foot");
                              $(".add-new-color-lines ").show();   
                              alert_notify(data.icon,data.message,data.type,function(){});
                           }
                        },
                        error: function (xhr, ajaxOptions, thrownError){
                          alert('Error data');
                          alert(xhr.responseText);
                        }
                      });
                }
            },
            success: {
                  label    : "No",
                  className: "btn-default  btn-sm",
                  callback : function() {
                    $('.bootbox').modal('hide');
                  }
            }
          }
          });
    }else{
      alert_modal_warning('Maaf, Data tidak bisa di Hapus !')
    }
    });


    // Edit row on edit button click COLOR LINES
    $(document).on("click", ".edit-color-lines", function(){  
      var status = $("#status").val();

       if(status == 'waiting_color'){

        $(this).parents("tr").find("td[data-content='edit']").each(function(){
          if($(this).attr('data-id')=="row_order"){
            $(this).html('<input type="hidden"  class="form-control" value="' + $(this).attr('data-isi') + '" id="'+ $(this).attr('data-id') +'"> ');
          }else if($(this).attr('data-id')=='qty'){
            $(this).html('<input type="text"  class="form-control" value="'+ htmlentities_script($(this).attr('data-isi')) +'" id="'+ $(this).attr('data-id') +'" name="'+ $(this).attr('data-id') +'" onkeyup="validAngka(this)"> ');
          }else{
            $(this).html('<input type="text"  class="form-control" value="'+ htmlentities_script($(this).attr('data-isi')) +'" id="'+ $(this).attr('data-id') +'" name="'+ $(this).attr('data-id') +'"> ');
          }

        });  

        $(this).parents("tr").find(".add-color-lines, .edit-color-lines").toggle();
        $(this).parents("tr").find(".cancel-color-lines, .delete-color-lines").toggle();
        $(".add-new-color-lines").hide();
      }else{
         alert_modal_warning('Maaf, Data tidak bisa diubah !')
      }
    });

    //btn cancel edit COLOR LINES
    $(document).on("click", ".cancel-color-lines", function(){
        $("#tab_2").load(location.href + " #tab_2");
        $(".add-new-color-lines").show();
    });


  /* END COLOR LINES */

    //klik button simpan
    $(document).on('click','#btn-simpan',function(e){
      var status = $("#status").val();

      if(status == 'draft' || status == 'waiting_date' || status == 'date_assigned'){

        var op = $("input:checked").val();
        if(op == 'true'){
          order_production = 'true';
        }else{
          order_production = 'false'
        }

        $('#btn-simpan').button('loading');
        please_wait(function(){});
        $.ajax({
           type: "POST",
           dataType: "json",
           url :'<?php echo base_url('sales/salescontract/simpan')?>',
           beforeSend: function(e) {
              if(e && e.overrideMimeType) {
                  e.overrideMimeType("application/json;charset=UTF-8");
              }
           },
           data: {sales_order: $('#sales_order').val(),
                  customer   : $('#customer').val(),
                  invoice_address  : $('#invoice_address').val(),
                  delivery_address : $('#delivery_address').val(),
                  buyer_code : $('#buyer_code').val(),
                  type       : $('#type').val(),
                  order_production : order_production,
                  tgl        : $('#tgl').val(),
                  reference  : $('#reference').val(),
                  warehouse  : $('#warehouse').val(),
                  currency   : $('#currency').val(),
                  delivery_date   : $('#delivery_date').val(),
                  time_ship  : '',
                  note_head  : $('#note_head').val(),

                  incoterm   : $('#incoterm').val(),
                  paymentterm   : $('#paymentterm').val(),
                  destination   : $('#destination').val(),
                  bank     : $('#bank').val(),
                  clause   : $('#clause').val(),
                  note     : $('#note').val(),

            },success: function(data){
              if(data.sesi == "habis"){
                //alert jika session habis
                alert_modal_warning(data.message);
                window.location.replace('index');
              }else if(data.status == "failed"){
                //jika ada form belum keiisi
                unblockUI( function() {
                  setTimeout(function() { alert_notify(data.icon,data.message,data.type,function(){}); }, 1000);
                });
                document.getElementById(data.field).focus();
              }else{
               //jika berhasil disimpan/diubah
                unblockUI( function() {
                  setTimeout(function() { 
                    alert_notify(data.icon,data.message,data.type, function(){
                  },1000); 
                  });
                });
                $("#foot").load(location.href + " #foot");
                $("#total").load(location.href + " #total");

              }
              $('#btn-simpan').button('reset');

            },error: function (xhr, ajaxOptions, thrownError) {
              alert(xhr.responseText);
              unblockUI( function(){});
              $('#btn-simpan').button('reset');
            }
        });
          window.setTimeout(function() {
         $(".alert").fadeTo(500, 0).slideUp(500, function(){
          $(this).remove(); });
        }, 3000);

      }else{
        alert_modal_warning('Maaf, Data tidak bisa diubah !')
      }
    });


  //modal mode print
  $(document).on('click','#btn-print',function(e){
      e.preventDefault();
      var kode = $('#kode').val();
      $(".print_data").html('<center><h5><img src="<?php echo base_url('dist/img/ajax-loader.gif') ?> "/><br>Please Wait...</h5></center>');
      $("#print_data").modal({
          show: true,
          backdrop: 'static'
      });
      $('.modal-title').text('Pilih Bahasa ?');
       var  so = '<?php echo $salescontract->sales_order?>';
      $.post('<?php echo site_url()?>sales/salescontract/mode_print_modal',
        { so : so},
          function(html){
            setTimeout(function() {$(".print_data").html(html);  },1000);
        }   
      );
  });

    //klik button confirm contract
    $(document).on('click','#btn-confirm',function(e){
      $('#btn-confirm').button('loading');
      please_wait(function(){});
      $.ajax({
         type: "POST",
         dataType: "json",
         url :'<?php echo base_url('sales/salescontract/confirm_contract')?>',
         beforeSend: function(e) {
            if(e && e.overrideMimeType) {
                e.overrideMimeType("application/json;charset=UTF-8");
            }
         },
         data: {sales_order: $('#sales_order').val(),
              
          },success: function(data){
            if(data.sesi == "habis"){
              //alert jika session habis
              alert_modal_warning(data.message);
              window.location.replace('../index');
            }else if(data.status == "failed"){
              //jika details masih kosong
              unblockUI( function() {
                setTimeout(function() { alert_notify(data.icon,data.message,data.type,function(){}); }, 1000);
              });
              $("#btn-header").load(location.href + " #btn-header");
              refresh_tab_and_div();
              $('#btn-confirm').button('reset');
             
            }else{
              unblockUI( function() {
                setTimeout(function() { 
                  alert_notify(data.icon,data.message,data.type, function(){
                },1000); 
                });
              });
              $("#ref_status").load(location.href + " #ref_status");
              $("#btn-header").load(location.href + " #btn-header");
              $("#status_bar").load(location.href + " #status_bar");
              $("#foot").load(location.href + " #foot");
              $("#total").load(location.href + " #total");

            }
            $('#btn-confirm').button('reset');

          },error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.responseText);
            unblockUI( function(){});
            $('#btn-confirm').button('reset');
          }
      });
        window.setTimeout(function() {
       $(".alert").fadeTo(500, 0).slideUp(500, function(){
        $(this).remove(); });
      }, 3000);
    });

    //klik button approve contract
    $(document).on('click','#btn-approve',function(e){
      $('#btn-approve').button('loading');
      please_wait(function(){});
      $.ajax({
         type: "POST",
         dataType: "json",
         url :'<?php echo base_url('sales/salescontract/approve_contract')?>',
         beforeSend: function(e) {
            if(e && e.overrideMimeType) {
                e.overrideMimeType("application/json;charset=UTF-8");
            }
         },
         data: {sales_order: $('#sales_order').val(),
              
          },success: function(data){
            if(data.sesi == "habis"){
              //alert jika session habis
              alert_modal_warning(data.message);
              window.location.replace('index');
            }else if(data.status == "failed"){
              //jika details masih kosong
              $('#btn-approve').button('reset');
              $("#btn-header").load(location.href + " #btn-header");
              $("#status_bar").load(location.href + " #status_bar");
              unblockUI( function() {
                //setTimeout(function() { alert_notify(data.icon,data.message,data.type); }, 1000);
                alert_modal_warning(data.message);
              });
            }else{
              $("#ref_status").load(location.href + " #ref_status");
              $("#btn-header").load(location.href + " #btn-header");
              unblockUI( function() {
                setTimeout(function() { 
                  alert_notify(data.icon,data.message,data.type, function(){
                },1000); 
                });
              });
              $("#status_bar").load(location.href + " #status_bar");
              $("#foot").load(location.href + " #foot");
              $("#total").load(location.href + " #total");

            }
            $('#btn-approve').button('reset');

          },error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.responseText);
            unblockUI( function(){});
            $('#btn-approve').button('reset');
          }
      });
        window.setTimeout(function() {
       $(".alert").fadeTo(500, 0).slideUp(500, function(){
        $(this).remove(); });
      }, 3000);
    });


    //klik button approve color
    $(document).on('click','#btn-approve-color',function(e){
      $('#btn-approve-color').button('loading');
      please_wait(function(){});
      $.ajax({
         type: "POST",
         dataType: "json",
         url :'<?php echo base_url('sales/salescontract/approve_color')?>',
         beforeSend: function(e) {
            if(e && e.overrideMimeType) {
                e.overrideMimeType("application/json;charset=UTF-8");
            }
         },
         data: {sales_order: $('#sales_order').val(),
              
          },success: function(data){
            if(data.sesi == "habis"){
              //alert jika session habis
              alert_modal_warning(data.message);
              window.location.replace('../index');
            }else if(data.status == "failed"){
              //jika details masih kosong
              $("#ref_status").load(location.href + " #ref_status");
              $("#btn-header").load(location.href + " #btn-header");
              $("#status_bar").load(location.href + " #status_bar");
              unblockUI( function() {
                //setTimeout(function() { alert_notify(data.icon,data.message,data.type); }, 1000);
                alert_modal_warning(data.message);
              });
             $('#btn-approve-color').button('reset');
             
            }else{
              unblockUI( function() {
                setTimeout(function() { 
                  alert_notify(data.icon,data.message,data.type, function(){
                },1000); 
                });
              });
              $("#ref_status").load(location.href + " #ref_status");
              $("#btn-header").load(location.href + " #btn-header");
              $("#status_bar").load(location.href + " #status_bar");
              $("#foot").load(location.href + " #foot");
              $("#total").load(location.href + " #total");

            }
            $('#btn-approve-color').button('reset');

          },error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.responseText);
            unblockUI( function(){});
            $('#btn-approve-color').button('reset');
          }
      });
        window.setTimeout(function() {
       $(".alert").fadeTo(500, 0).slideUp(500, function(){
        $(this).remove(); });
      }, 3000);
    });


  // modal Create Color
  $("#btn-create-color").unbind( "click" );
  $(document).on('click','#btn-create-color',function(e){
      e.preventDefault();
      $("#tambah_data").modal({
          show: true,
          backdrop: 'static'
      });
      $(".tambah_data").html('<center><h5><img src="<?php echo base_url('dist/img/ajax-loader.gif') ?> "/><br>Please Wait...</h5></center>');
      $('.modal-title').text('Create Color');
        $.post('<?php echo site_url()?>sales/salescontract/create_color_modal',
          {txtProduct      : $('#product').val() },
          function(html){
            setTimeout(function() {$(".tambah_data").html(html);  },1000);
          }   
       );
  });


    //btn simpan create color
    $('#btn-tambah').click(function(){
      $('#btn-tambah').button('loading');
      please_wait(function(){});
      
      $.ajax({
         type: "POST",
         dataType: "json",
         url :'<?php echo base_url('lab/dti/simpan')?>',
         beforeSend: function(e) {
            if(e && e.overrideMimeType) {
                e.overrideMimeType("application/json;charset=UTF-8");
            }
         },
         data: {tanggal    : $('#tgl_modal').val(),
                warna      : $('#warna').val(),
                note       : $('#notes').val(),
                status     : 'tambah'

          },success: function(data){
            if(data.sesi == "habis"){
              //alert jika session habis
              alert_modal_warning(data.message);
              window.location.replace('index');
            }else if(data.status == "failed"){
              //jika ada form belum keiisi
              unblockUI( function() {
                setTimeout(function() { alert_notify(data.icon,data.message,data.type,function(){}); }, 1000);
              });
              document.getElementById(data.field).focus();
            }else{
             //jika berhasil disimpan
              unblockUI( function() {
                setTimeout(function() { 
                  alert_notify(data.icon,data.message,data.type, function(){
                  $("#tab_2").load(location.href + " #tab_2");
                },1000); 
                });
              });
              $('#tambah_data').modal('hide');
            }
            $('#btn-tambah').button('reset');

          },error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.responseText);
            unblockUI( function(){});
            $('#btn-tambah').button('reset');
          }
      });
        window.setTimeout(function() {
       $(".alert").fadeTo(500, 0).slideUp(500, function(){
        $(this).remove(); });
      }, 3000);
      
    });

  $("#tambah_data").on("hidden.bs.modal", function () {
    ///alert('tes');
    $('#form_create_color')[0].reset();
  });
    
</script>

</body>
</html>
