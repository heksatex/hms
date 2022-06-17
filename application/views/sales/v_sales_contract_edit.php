
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

    .set_textarea{
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
                  <!--button class="btn btn-primary btn-sm" id="btn-approve-color" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing...">Approve Color</button-->
                  <button class="btn btn-primary btn-sm" id="btn-approve-order" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing...">Approve Order</button>
            <?php }elseif($salescontract->status=='product_generated'){?>
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
                  <div class="col-xs-4"><label>Sales Contract</label></div>
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
                            <th class="style" width="80px" >Uom</th>
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
                              <th class="style" width="100px">Finishing</th>
                              <th class="style" width="100px">Route CO</th>
                              <th class="style" width="80px">Gramasi</th>
	                            <th class="style" width="80px" style="text-align: right;">Qty</th>
	                            <th class="style" width="80px" >Uom</th>
	                            <th class="style" width="150px" >Piece Info</th>
	                            <th class="style" width="120px">Lebar Jadi</th>
	                            <th class="style" width="100px">Uom Lbr Jadi</th>
	                            <th class="style" width="150px">Reff Notes</th>
	                            <th class="style" width="80px" >OW</th>
	                            <th class="style" >Status</th>
	                            <th class="style" width="50px"></th>
	                          </tr>
	                        </thead>
	                        <tbody>
                            <?php 
                                  $no = 1;
                                  foreach ($details_color_lines as $row) {
                                ?>
                                  <tr class="num">
                                    <td data-content="edit" data-id="row_order" data-isi="<?php echo $row->row_order;?>" data-isi2="<?php echo $row->ow;?>"></td>
                                    <td class="text-wrap width-150"><?php echo $row->nama_produk?></td>
                                    <td class="text-wrap width-150" data-content="edit" data-name="Description" data-id="description_color" data-isi="<?php echo htmlentities($row->description);?>"><?php echo $row->description?></td>
                                    <td class="text-wrap" data-content="edit" data-id="color"   data-name="Color" data-isi="<?php echo $row->id_warna;?>" data-isi2="<?php echo $row->nama_warna;?>"><?php echo $row->nama_warna?></td>
                                    <td class="text-wrap" data-content="edit" data-name="Color Name" data-id="color_name"  data-isi="<?php echo $row->color_alias_name;?>"><?php echo htmlentities($row->color_alias_name)?></td>
                                    <td class="text-wrap" data-content="edit" data-name="Finishing" data-id="handling"  data-isi="<?php echo $row->id_handling;?>"><?php echo $row->nama_handling?></td>
                                    <td class="text-wrap" data-content="edit" data-name="Route CO" data-id="route_co"  data-isi="<?php echo $row->route_co;?>"><?php echo $row->nama_route_co?></td>
                                    <td class="text-wrap" data-content="edit" data-name="Gramasi" data-id="gramasi"  data-isi="<?php echo $row->gramasi;?>"><?php echo $row->gramasi?></td>
                                    <td align="right" data-content="edit" data-id="qty" data-name="Qty" data-isi="<?php echo $row->qty;?>"><?php echo $row->qty?></td>
                                    <td ><?php echo $row->uom?></td>
                                    <td class="text-wrap width-150" data-content="edit" data-name="Piece Info" data-id="piece_info" data-isi="<?php echo $row->piece_info;?>"><?php echo htmlentities($row->piece_info)?></td>
                                    <td class="text-wrap" data-content="edit" data-name="Lebar Jadi" data-id="lebar_jadi"  data-isi="<?php echo $row->lebar_jadi;?>"><?php echo $row->lebar_jadi?></td>
                                    <td class="text-wrap width-80"  data-content="edit" data-name="Uom Lebar Jadi" data-id="uom_lebar_jadi" data-isi="<?php echo $row->uom_lebar_jadi;?>" ><?php echo $row->uom_lebar_jadi?></td>
                                    <td class="text-wrap" data-content="edit" data-name="Reff Note" data-id="reff_notes"  data-isi="<?php echo htmlentities($row->reff_notes);?>"><?php echo $row->reff_notes?></td>
                                    <td ><?php echo $row->ow?></td>
                                    <td style="min-width:80px;" >
                                        <?php if(!empty($row->ow)){ ?>
                                        <select class="form-control input-sm status_scl" id="status_scl" name="status_scl" sc="<?php echo $row->sales_order;?>" row_order="<?php echo $row->row_order;?>" ow="<?php echo $row->ow;?>" >
                                          <?php $arr_stat = array('t','f','ng');
                                                foreach($arr_stat as $stats){
                                                  if($stats == 't'){
                                                    $status = 'Aktif';
                                                  }else if($stats == 'ng'){
                                                    $status = 'Not Good';
                                                  }else{
                                                    $status = 'Tidak Aktif';
                                                  }

                                                  if($row->status == $stats){
                                                    echo '<option value="'.$row->status.'" selected>'.$status.'</option>';
                                                  }else{
                                                    echo '<option value="'.$stats.'">'.$status.'</option>';
                                                  }
                                                }
                                          ?>
                                        </select>
                                        <?php } ?>
                                    <td>
                                      <?php if(empty($row->ow)){?>
                                        <a class="add-color-lines" title="Simpan" data-toggle="tooltip" row_id='tes'><i class="fa fa-save"></i></a>

                                        <?php if($salescontract->status =='waiting_color' ){?>
                                        <a class="ow-color-lines" title="OW" data-toggle="tooltip"><i class="fa  fa-arrow-right"></i></a>
                                        <?php }?>

                                        <a class="edit-color-lines" title="Edit" data-toggle="tooltip" style="color: #FFC107;"><i class="fa fa-edit"></i></a>
                                        <a class="delete-color-lines" title="Hapus" data-toggle="tooltip"><i class="fa fa-trash" style="color: red"></i></a>
                                        <a class="cancel-color-lines" title="Cancel" data-toggle="tooltip"><i class="fa fa-close"></i></a>
                                      <?php }?>
                                    </td>
                                  </tr>
                                <?php 
                                  }     
                                ?>
                          </tbody>
                          <tfoot>
                            <tr>
                              <td colspan="13">
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

  //auto height in textarea
  function textAreaAdjust(o) {
    o.style.height = "1px";
    o.style.height = (25+o.scrollHeight)+"px";
  }

  function refresh_tab_and_div(){
    $("#tab_1").load(location.href + " #tab_1");
    $("#tab_2").load(location.href + " #tab_2");
    $("#status_bar").load(location.href + " #status_bar");
    $("#foot").load(location.href + " #foot");
    $("#total").load(location.href + " #total");
  }

  // untuk ubah status sales color line aktif/tidak aktif
  $(document).on("change", ".status_scl", function(){

    var sales_order = $(this).attr('sc');
    var row_order   = $(this).attr('row_order');
    var value       = $(this).val();
    var ow          = $(this).attr('ow');
    $.ajax({
          dataType: "JSON",
          url : '<?php echo site_url('sales/salescontract/update_status_color_lines') ?>',
          type: "POST",
          data: {sales_order  : sales_order, 
                row_order     : row_order,
                ow            : ow,
                value         : value},
          success: function(data){
            if(data.sesi=='habis'){
                //alert jika session habis
                alert_modal_warning(data.message);
                window.location.replace('../index');
            }else{
                $("#tab_2").load(location.href + " #tab_2");
                $("#foot").load(location.href + " #foot");
                //$("#total").load(location.href + " #total");
                alert_notify(data.icon,data.message,data.type,function(){});
             }
          },
          error: function (xhr, ajaxOptions, thrownError){
            alert('Error data');
            alert(xhr.responseText);
          }
        });
        

  });

  // Append table with add row form on add new button click
  $(document).on("click", ".add-new", function(){

    var status = $("#status").val();

    if(status == 'draft' || status == 'waiting_date' ){

    $(".add-new").hide();
    var index = $("#contract_lines tbody tr:last-child").index();
    var row   ='<tr class="num">'
          + '<td></td>'
          + '<td class="width-300"><select type="text" class="form-control input-sm prod" name="Product" id="product"></select></td>'
          + '<td><textarea class="form-control description set_textarea" name="Description" id="description"></textarea><input type="hidden" class="form-control input-sm prodhidd" name="prodhidd" id="prodhidd"></td>'
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
          + '<td><select type="text" class="form-control input-sm prod_color width-150" name="Product" id="product"></select></td>'
          + '<td><textarea type="text" class="form-control input-sm description_color set_textarea  width-150" onkeyup="textAreaAdjust(this)"  name="Description" id="description_color"></textarea><input type="hidden" class="form-control input-sm prodhidd_color" name="prodhidd" id="prodhidd_color"></td>'
          + '<td><select type="text" class="form-control input-sm color width-150" name="Color" id="color"></select></td>'
          + '<td><textarea type="text" class="form-control input-sm  width-150 set_textarea" onkeyup="textAreaAdjust(this)"  name="Color Name" id="color_name"></textarea></td>'
          + '<td><select type="text" class="form-control input-sm  width-100 handling" name="Finishing" id="handling" ><option value=""></option><?php foreach($handling as $row){?><option value="<?php echo $row->id; ?>"><?php echo $row->nama_handling;?></option>"<?php }?></select></td>'
          + '<td><select type="text" class="form-control input-sm  width-100 route_co" name="Route CO" id="route_co" ><option value=""></option><?php foreach($route as $row){?><option value="<?php echo $row->kode; ?>"><?php echo $row->nama;?></option>"<?php }?></select></td>' 
          + '<td><input type="text" class="form-control input-sm width-100" name="Gramasi" id="gramasi" onkeyup="validAngka(this)"></td>'
          + '<td><input type="text" class="form-control input-sm width-100" name="Qty" id="qty" onkeyup="validAngka(this)"></td>'
          + '<td><input type="text" class="form-control input-sm uom_color width-50" name="Uom" id="uom" readonly></td>'
          + '<td><textarea type="text" class="form-control  input-sm width-100 set_textarea" onkeyup="textAreaAdjust(this)"  name="Piece Info" id="piece_info"></textarea></td>'
          + '<td class=""><input type="text" class="form-control input-sm width-100 lebar_jadi" name="Lebar Jadi" id="lebar_jadi" ></td>'
          + '<td class=""><select type="text" class="form-control input-sm width-80 uom_lebar_jadi" name="Uom Lebar Jadi" id="uom_lebar_jadi"><option value=""></option><?php 
          foreach($list_uom as $row){?><option value="<?php echo $row->short; ?>"><?php echo $row->short;?></option>"<?php }?></select></td>'
          + '<td><textarea type="text" class="form-control  input-sm width-100 set_textarea" onkeyup="textAreaAdjust(this)"  name="Reff Notes" id="reff_notes"></textarea></td>'
          + '<td></td>'
          + '<td><button type="button" class="btn btn-primary btn-xs add-color-lines width-btn" title="Simpan" data-toggle="tooltip">Simpan</button><a class="edit-color-lines" title="Edit" data-toggle="tooltip"><i class="fa fa-edit"></i></a><button type="button" class="btn btn-danger btn-xs batal-color-lines width-btn" title="Batal" data-toggle="tooltip">Batal</button></td>'
          + '</tr>';

        $('#color_lines tbody').append(row);
        $("#color_lines tbody tr").eq(index + 1).find(".add-color-lines, .edit-color-lines").toggle();
        $('[data-toggle="tooltip"]').tooltip();

        // select 2 handling/finishing
        $('.handling').select2({});

        // select 2 handling/finishing
         $('.uom_lebar_jadi').select2({});

        // select 2 route co
         $('.route_co').select2({});

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
                  $('.lebar_jadi').val(data.lebar_jadi);
                  $('.uom_lebar_jadi').val(data.uom_lebar_jadi);
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
                url  : "<?php echo base_url();?>sales/salescontract/get_color_select2",
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
                          id:item.id,
                          text:item.nama_warna
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
          empty2 = true;
        }

        if(!$(this).val() && $(this).attr('name')=='Finishing'){
          alert_notify('fa fa-warning', $(this).attr('name')+ ' Harus Diisi !', 'danger',function(){});
          empty2 = true;
        }

        if(!$(this).val() && $(this).attr('name')=='Route CO'){
          alert_notify('fa fa-warning', $(this).attr('name')+ ' Harus Diisi !', 'danger',function(){});
          empty2 = true;
        }

        if(!$(this).val() && $(this).attr('name')=='Gramasi'){
          alert_notify('fa fa-warning', $(this).attr('name')+ ' Harus Diisi !', 'danger',function(){});
          empty2 = true;
        }

        if(!$(this).val() && $(this).attr('name')=='Uom Lebar Jadi'){
          alert_notify('fa fa-warning', $(this).attr('name')+ ' Harus Diisi !', 'danger',function(){});
          empty2 = true;
        }
      });

      // validasi untuk inputan textbox
      input.each(function(){
        if(!$(this).val() && $(this).attr('name')!='Piece Info' || !$(this).val() && $(this).attr('name')!='Reff Notes'){
          alert_notify('fa fa-warning',$(this).attr('name')+ ' Harus Diisi !','danger',function(){});
          empty = true;
          //break;
        }
      });

      if(!empty && !empty2){
        var kode        =  "<?php echo $salescontract->sales_order; ?>";
        var kode_prod   = $(this).parents("tr").find("#product").val();
        var prod        = $(this).parents("tr").find("#prodhidd_color").val();
        var desc        = $(this).parents("tr").find("#description_color").val();
        var color       = $(this).parents("tr").find("#color").val();
        var color_name  = $(this).parents("tr").find("#color_name").val();
        var handling    = $(this).parents("tr").find("#handling").val();
        var route_co    = $(this).parents("tr").find("#route_co").val();
        var gramasi     = $(this).parents("tr").find("#gramasi").val();
        var qty         = $(this).parents("tr").find("#qty").val();
        var uom         = $(this).parents("tr").find("#uom").val();
        var piece_info  = $(this).parents("tr").find("#piece_info").val();
        var lebar_jadi  = $(this).parents("tr").find("#lebar_jadi").val();
        var uom_lebar_jadi  = $(this).parents("tr").find("#uom_lebar_jadi").val();
        var reff_note   = $(this).parents("tr").find("#reff_notes").val();
        var row_order   = $(this).parents("tr").find("#row_order").val();
        //var dat = $(this).parents("tr").find('input[type="text"]').val();
              
        $.ajax({
          dataType: "JSON",
          url : '<?php echo site_url('sales/salescontract/simpan_detail_color_lines') ?>',
          type: "POST",
          data: {kode       : kode, 
                kode_prod   : kode_prod,
                prod        : prod,
                color       : color,
                color_name  : color_name,
                handling    : handling,
                route_co    : route_co,
                gramasi     : gramasi,
                desc        : desc, 
                qty         : qty,
                uom         : uom,
                piece_info  : piece_info,
                lebar_jadi  : lebar_jadi,
                uom_lebar_jadi  : uom_lebar_jadi,
                reff_note   : reff_note,
                row_order   : row_order  },
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
                $(".add-new-color-lines").show();                   
                alert_notify(data.icon,data.message,data.type,function(){});
            }
            $("#ref_status").load(location.href + " #ref_status");
            $("#btn-header").load(location.href + " #btn-header");
            refresh_tab_and_div();

          },
          error: function (xhr, ajaxOptions, thrownError){
            alert('Error data');
            alert(xhr.responseText);
            refresh_tab_and_div();
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
      var ow     = '';
      $(this).parents("tr").find("td[data-content='edit']").each(function(){
        if($(this).attr('data-id')=="row_order"){
          ow = $(this).attr('data-isi2');
        }
      });

       if(ow == ''){

        $(this).parents("tr").find("td[data-content='edit']").each(function(){
          if($(this).attr('data-id')=="row_order"){
            $(this).html('<input type="hidden"  class="form-control" value="' + $(this).attr('data-isi') + '" id="'+ $(this).attr('data-id') +'"> ');
            row_order = $(this).attr('data-isi');

          }else if($(this).attr('data-id')=='qty' || $(this).attr('data-id')=='gramasi'){
            $(this).html('<input type="text"  class="form-control width-100" value="'+ htmlentities_script($(this).attr('data-isi')) +'" id="'+ $(this).attr('data-id') +'" name="'+ $(this).attr('data-name') +'" onkeyup="validAngka(this)"> ');
          
          }else if($(this).attr('data-id')=='lebar_jadi'){
            $(this).html('<input type="text"  class="form-control width-100" value="'+ htmlentities_script($(this).attr('data-isi')) +'" id="'+ $(this).attr('data-id') +'" name="'+ $(this).attr('data-name') +'" > ');
          
          }else if($(this).attr('data-id') == 'color'){

            var id_warna    = $(this).attr('data-isi');
            var nama_warna  = $(this).attr('data-isi2');

            class_sel2_color = 'sel2_color'+row_order;
                       
            //select 2 bom by kode-produk
            $(this).html('<select type="text" class="form-control input-sm width-150 '+class_sel2_color+'" id="color" name="Color" ></select> ');

            var $newOption = $("<option></option>").val(id_warna).text(nama_warna);
            $('.sel2_color'+row_order).empty().append($newOption).trigger('change');

            $('.sel2_color'+row_order).select2({
              allowClear: true,
              placeholder: "",
              ajax:{
                    dataType: 'JSON',
                    type : "POST",
                    url  : "<?php echo base_url();?>sales/salescontract/get_color_select2",
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
                              id:item.id,
                              text:item.nama_warna
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

           
          }else if($(this).attr('data-id') == 'handling'){

            var obj_handling = new Array();
            <?php 
              foreach($handling as $key ){
            ?>
                  obj_handling.push({id:"<?php echo $key->id?>", nama_handling:"<?php echo $key->nama_handling;?>"});
            <?php 
              }
            ?>

            var value_option  = $(this).attr('data-isi');
            handling_row      = $(this).attr('data-id')+row_order;

            var option = '<option value=""></option>';
            $.each(obj_handling, function(index,val){
                  if(obj_handling[index].id == value_option ){
                    option += "<option selected value='"+obj_handling[index].id+"'>"+obj_handling[index].nama_handling+"</option>";
                  }else{
                    option += "<option value='"+obj_handling[index].id+"'>"+obj_handling[index].nama_handling+"</option>";
                  }
            });

            $(this).html('<select type="text" class="form-control input-sm width-80 '+handling_row+'"  id="'+ $(this).attr('data-id') +'" name="'+ $(this).attr('data-name') +'"></select>');
            $('.'+handling_row).append(option).trigger('change');

            $('.'+handling_row).select2({});

          }else if($(this).attr('data-id') == 'route_co'){

            var obj_route = new Array();
            <?php 
              foreach($route as $key ){
            ?>
                  obj_route.push({id:"<?php echo $key->kode?>", nama:"<?php echo $key->nama;?>"});
            <?php 
              }
            ?>

            var value_option  = $(this).attr('data-isi');
            route_co_row      = $(this).attr('data-id')+row_order;

            var option = '<option value=""></option>';
            $.each(obj_route, function(index,val){
                  if(obj_route[index].id == value_option ){
                    option += "<option selected value='"+obj_route[index].id+"'>"+obj_route[index].nama+"</option>";
                  }else{
                    option += "<option value='"+obj_route[index].id+"'>"+obj_route[index].nama+"</option>";
                  }
            });

            $(this).html('<select type="text" class="form-control input-sm width-100 '+route_co_row+'"  id="'+ $(this).attr('data-id') +'" name="'+ $(this).attr('data-name') +'"></select>');
            $('.'+route_co_row).append(option).trigger('change');

            $('.'+route_co_row).select2({});

          }else if($(this).attr('data-id') == 'uom_lebar_jadi'){

            var value_option  = $(this).attr('data-isi');
            uom_lbr_row       = $(this).attr('data-id')+row_order;

            var obj_uom = new Array();
            <?php 
              foreach($list_uom as $key ){
            ?>
                  obj_uom.push({id:"<?php echo $key->short?>", nama:"<?php echo $key->short;?>"});
            <?php 
              }
            ?>

            var option = '<option value=""></option>';
            $.each(obj_uom, function(index,val){
                  if(obj_uom[index].id == value_option ){
                    option += "<option selected value='"+obj_uom[index].id+"'>"+obj_uom[index].nama+"</option>";
                  }else{
                    option += "<option value='"+obj_uom[index].id+"'>"+obj_uom[index].nama+"</option>";
                  }
            });
            
            $(this).html('<select type="text" class="form-control input-sm width-80 '+uom_lbr_row+'"  id="'+ $(this).attr('data-id') +'" name="'+ $(this).attr('data-name') +'"></select>');

            $('.'+uom_lbr_row).append(option).trigger('change');
            $('.'+uom_lbr_row).select2({});

          }else if($(this).attr('data-id')=='description_color' || $(this).attr('data-id') == 'color_name' || $(this).attr('data-id') =='piece_info' || $(this).attr('data-id') =='reff_notes' ){
            
            $(this).html('<textarea type="text" onkeyup="textAreaAdjust(this)" class="form-control input-sm set_textarea width-150" id="'+ $(this).attr('data-id') +'" name="'+ $(this).attr('data-id') +'">'+ htmlentities_script($(this).attr('data-isi')) +'</textarea>');

          }else{
            $(this).html('<input type="text"  class="form-control" value="'+ htmlentities_script($(this).attr('data-isi')) +'" id="'+ $(this).attr('data-id') +'" name="'+ $(this).attr('data-id') +'"> ');
          }

        });  

        $(this).parents("tr").find(".add-color-lines, .edit-color-lines").toggle();
        $(this).parents("tr").find(".cancel-color-lines, .delete-color-lines").toggle();
        $(this).parents("tr").find(".ow-color-lines").toggle();
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

     // create OW / kilik panah kanan untuk OW
    $(document).on("click", ".ow-color-lines", function(){ 

        var status = $("#status").val();
        if(status == 'waiting_color'){

          $(this).parents("tr").find("td[data-content='edit']").each(function(){
            if($(this).attr('data-id')=="row_order"){
              $(this).html('<input type="hidden" class="form-control" value="' + $(this).attr('data-isi') + '" id="'+ $(this).attr('data-id') +'"> ');
            }
          });

          var kode      =  "<?php echo $salescontract->sales_order; ?>";
          var row_order = $(this).parents("tr").find("#row_order").val();

          bootbox.dialog({
            message: "Apakah Anda ingin membuat OW ?",
            title: "<i class='fa fa-gear'></i> Create OW !",
            buttons: {
              danger: {
                  label    : "Yes ",
                  className: "btn-primary btn-sm",
                  callback : function() {
                      please_wait(function(){});
                      $.ajax({
                          dataType: "JSON",
                          url : '<?php echo site_url('sales/salescontract/create_OW') ?>',
                          type: "POST",
                          data: {kode:kode,  row_order:row_order },
                          success: function(data){
                            if(data.sesi=='habis'){
                                //alert jika session habis
                                alert_modal_warning(data.message);
                                window.location.replace('../index');
                            }else if(data.status == 'failed'){
                                alert_modal_warning(data.message);
                                unblockUI( function(){});
                                $("#tab_2").load(location.href + " #tab_2");
                                $("#foot").load(location.href + " #foot");
                                $("#ref_status").load(location.href + " #ref_status");
                            }else{
                                unblockUI( function() {
                                  setTimeout(function() { 
                                    alert_notify(data.icon,data.message,data.type, function(){
                                  },1000); 
                                  });
                                });
                                $("#ref_status").load(location.href + " #ref_status");
                                $("#tab_2").load(location.href + " #tab_2");
                                $("#foot").load(location.href + " #foot");
                              }
                              refresh_tab_and_div();
                          },
                          error: function (xhr, ajaxOptions, thrownError){
                            alert('Error Create OW');
                            //alert(xhr.responseText);
                            unblockUI( function(){});
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
          alert_modal_warning('Maaf, Tidak Bisa membuat OW !');
        }
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
              refresh_tab_and_div();
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
            }
            $("#ref_status").load(location.href + " #ref_status");
            $("#btn-header").load(location.href + " #btn-header");
            $("#status_bar").load(location.href + " #status_bar");
            $("#foot").load(location.href + " #foot");
            $("#tab_2").load(location.href + " #tab_2");
            
            $('#btn-approve-color').button('reset');

          },error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.responseText);
            unblockUI( function(){});
            $('#btn-approve-color').button('reset');
          }
      });
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
