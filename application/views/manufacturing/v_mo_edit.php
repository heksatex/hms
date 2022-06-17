
<!DOCTYPE html>
<html lang="en">
<head>
  <?php $this->load->view("admin/_partials/head.php") ?>

  <style>
    button[id="btn-simpan"],button[id="btn-cancel"]{/*untuk hidden button simpan/cancel di top bar MO*/
      display: none;
    }
    
    .box-color {
       width: 100%;
      border: 1px solid;
      border-color: #d2d6de;
      padding: 50px;
      margin: 10px 0px 10px 0px;
      border-radius: 5px;
    }
    .lebar2  {
        width:40% !important;
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
     $data['deptid']     = $list->dept_id;
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
    <section class="content-header" >
      <div id ="status_bar">
       <?php 
         $data['jen_status'] = $list->status;
         $data['deptid']    = $list->dept_id;
         $this->load->view("admin/_partials/statusbar.php", $data); 
       ?>
      </div>
    </section>

    <!-- Main content -->
    <section class="content">
     
      <!--  box content -->
      <div class="box">
        <div class="box-header with-border">
          <h3 class="box-title"><b><?php echo $list->kode;?></b></h3>
          <?php if($list->dept_id=='DYE' AND $akses_menu >0){
              if(!empty($menu)){  ?>
          <div class=" pull-right text-right">
            <button class="btn btn-primary btn-sm" id="btn-request" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing...">Request</button>
          </div>
        <?php }
            }?>
        </div>
        <div class="box-body">
          <form class="form-horizontal" id="mo">
            <div class="form-group">                  
              <div class="col-md-12" >
                <div id="alert"></div>
              </div>
            </div>
            <div class="col-md-6">
            <div class="form-group"> 

              <div class="col-md-12 col-xs-12">
                <div class="col-xs-4"><label>Kode</label></div>
                <div class="col-xs-8">
                  <input type="text" class="form-control input-sm" name="kode" id="kode" value="<?php echo $list->kode;?>" readonly="readonly"/>
                </div>                                    
              </div>
              <div class="col-md-12 col-xs-12">
                <div class="col-xs-4"><label>Tanggal dibuat </label></div>
                <div class="col-xs-8 col-md-8">
                  <input type='text' class="form-control input-sm" name="tgl" id="tgl" value="<?php echo $list->tanggal;?>" readonly="readonly" />
                </div>
              </div>
              <div class="col-md-12 col-xs-12">
                <div class="col-xs-4"><label>Origin </label></div>
                <div class="col-xs-8 col-md-8">
                  <input type='text' class="form-control input-sm" name="origin" id="origin" value="<?php echo $list->origin;?>" readonly="readonly" />
                </div>
              </div>
              <div class="col-md-12 col-xs-12">
                <div class="col-xs-4"><label>Product</label></div>
                <div class="col-xs-8">
                  <div class='input-group'>
                    <input type="text" class="form-control input-sm" name="product" id="product" value="<?php echo htmlentities($list->nama_produk);?>" readonly="readonly" data-toggle="tooltip" title="<?php echo htmlentities($list->nama_produk); ?>">
                    <span class="input-group-addon">
                        <a href="#" class="view"><span class="glyphicon  glyphicon-share"></span></a>
                    </span>
                    <input type="hidden" class="form-control input-sm" name="kode_produk" id="kode_produk"  value="<?php echo $list->kode_produk;?>"  readonly="readonly"   />
                  </div>
                </div>  
              </div>
              <div class="col-md-12 col-xs-12">
                <div class="col-xs-4"><label>Qty </label></div>
                <div class="col-xs-8">
                  <input type="text" class="form-control input-sm" name="qty" id="qty"  value="<?php echo $list->qty; echo ' '.$list->uom;?>"  readonly="readonly"   />
                </div>                                    
              </div>
              <div class="col-md-12 col-xs-12">
                <div class="col-xs-4"><label>BOM </label></div>
                <div class="col-xs-8">
                  <input type="text" class="form-control input-sm" name="bom" id="bom"  value="<?php echo htmlentities($bom['nama_bom']);?>"  readonly="readonly"   />
                </div>                                    
              </div>

              <?php if($show_lebar['show_lebar'] == 'true') {?>
              <div class="col-md-12 col-xs-12">
                <div class="col-xs-4"><label>Lebar Greige </label></div>
                <div class="col-xs-5">
                  <input type="text" class="form-control input-sm" name="lebar_greige_mo" id="lebar_greige_mo"  value="<?php echo $list->lebar_greige; ?>"  readonly="readonly"   />
                </div> 
                <div class="col-xs-3">
                    <select class="form-control input-sm" name="uom_lebar_greige_mo" id="uom_lebar_greige_mo" disabled="true">
                    	<option value=""></option>
                            <?php foreach ($uom as $row) {
                                    if($row->short == $list->uom_lebar_greige){
                                    	echo "<option selected value='".$row->short."'>".$row->short."</option>";
                                	}else{
                                      echo "<option value='".$row->short."'>".$row->short."</option>";
                                	}
                                }
                         ?>
                    </select>
                </div>                                   
              </div>
              <div class="col-md-12 col-xs-12">
                <div class="col-xs-4"><label>Lebar Jadi </label></div>
                <div class="col-xs-5">
                  <input type="text" class="form-control input-sm" name="lebar_jadi_mo" id="lebar_jadi_mo"  value="<?php echo $list->lebar_jadi; ?>"  readonly="readonly"  />
                </div>
                <div class="col-xs-3">
                    <select class="form-control input-sm" name="uom_lebar_jadi_mo" id="uom_lebar_jadi_mo" disabled="true">
                    	<option value=""></option>
                            <?php foreach ($uom as $row) {
                                    if($row->short == $list->uom_lebar_jadi){
                                    	echo "<option selected value='".$row->short."'>".$row->short."</option>";
                                	}else{
                                      echo "<option value='".$row->short."'>".$row->short."</option>";
                                	}
                                }
                         ?>
                    </select>
                </div>                                      
              </div>
              <?php }?>

              <?php if($type_mo['type_mo']=='colouring') {?>
              <div class="col-md-12 col-xs-12">
                <div class="col-xs-4"><label>Air (Ltr) </label></div>
                <div class="col-xs-8">
                  <input type="text" class="form-control input-sm highlight" name="air" id="air" value="<?php echo $list->air;?>"  onkeyup="highlight(this)" <?php if($disable == "yes") echo 'readonly="readonly"';?>  readonly="readonly" >
                </div>                                    
              </div>
              <div class="col-md-12 col-xs-12">
                <div class="col-xs-4"><label>Berat (Kg) </label></div>
                <div class="col-xs-8">
                  <input type="text" class="form-control input-sm highlight" name="berat" id="berat"  value="<?php echo $list->berat;?>" onkeyup="highlight(this)" <?php if($disable == "yes") echo 'readonly="readonly"';?>  readonly="readonly" >
                </div>                                    
              </div>
              <div class="col-md-12 col-xs-12">
                <div class="col-xs-4"><label>Finishing </label></div>
                <div class="col-xs-8">
                  <select class="form-control input-sm" name="handling" id="handling" disabled="true">
                    <option value="">Pilih Finishing</option>
                    <?php 
                      foreach ($handling as $row) {
                        if($list->id_handling == $row->id){?>
                          <option value="<?php echo $row->id;?>" selected><?php echo $row->nama_handling;?></option>
                    <?php 
                        }else{?>
                          <option value="<?php echo $row->id;?>"><?php echo $row->nama_handling;?></option>
                    <?php
                        }
                      }?>
                  </select>
                </div>                                    
              </div>
              <div class="col-md-12 col-xs-12">
                <div class="col-xs-4"><label>Gramasi </label></div>
                <div class="col-xs-8">
                  <input type="text" class="form-control input-sm highlight" name="gramasi" id="gramasi"  value="<?php echo $list->gramasi;?>" onkeyup="highlight(this)" <?php if($disable == "yes") echo 'readonly="readonly"';?>  readonly="readonly" >
                </div>                                    
              </div>
              <div class="col-md-12 col-xs-12">
                <div class="col-xs-4"><label>Program </label></div>
                <div class="col-xs-8">
                  <input type="text" class="form-control input-sm highlight" name="program" id="program"  value="<?php echo $list->program;?>" <?php if($disable == "yes") echo 'readonly="readonly"';?>  readonly="readonly" >
                </div>                                    
              </div>
              <div class="col-md-12 col-xs-12">
                <div class="col-xs-4"><label>Warna </label></div>
                <div class="col-xs-8">
                  <input type="hidden" class="form-control input-sm" name="id_warna" id="id_warna"  value="<?php echo $list->id_warna;?>"  readonly="readonly"   />
                  <input type="text" class="form-control input-sm" name="warna" id="warna"  value="<?php echo $list->nama_warna;?>"  readonly="readonly"   />
                </div>                                    
              </div>
              <div class="col-md-12 col-xs-12">
                <div class="col-xs-4"><label>Varian </label></div>
                <div class="col-xs-8">
                  <input type="text" class="form-control input-sm highlight" name="varian_warna" id="varian_warna"  value="<?php echo $list->nama_varian;?>" <?php if($disable == "yes") echo 'readonly="readonly"';?>  readonly="readonly" >
                </div>                                    
              </div>
              <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4 "> <div class="box-color" style="background-color: <?php echo $list->kode_warna;?>"></div></div>
                  <div class="col-xs-8 col-md-8" id="ta">
                      <textarea class="form-control input-sm" name="notes_dti" id="notes_dti" readonly="readonly"><?php echo $list->notes_dti; ?></textarea>
                  </div>                                    
              </div>

            <?php }?>
            </div>
            </div>
            <div class="col-md-6">
            <div class="form-group">   
              <div class="col-md-12 col-xs-12">
                <div class="col-xs-4"><label>Start Time </label></div>
                <div class="col-xs-8 col-md-8">
                  <div class='input-group date' id='datetimepicker3' >
                    <input type='text' class="form-control input-sm" name="start" id="start"  value="<?php echo $list->start_time;?>" />
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar" disabled="true" ></span>
                    </span>
                  </div>
                </div>                                    
              </div>
              <div class="col-md-12 col-xs-12">
                <div class="col-xs-4"><label>Finish Time </label></div>
                <div class="col-xs-8 col-md-8">
                  <div class='input-group date' id='datetimepicker4' >
                    <input type='text' class="form-control input-sm" name="finish" id="finish"  value="<?php echo $list->finish_time;?>" readonly="readonly" />
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                    </span>
                  </div>
                </div>                                    
              </div>
             
              <div class="col-md-12 col-xs-12">
                <div class="col-xs-4"><label>Tanggal Jatuh Tempo </label></div>
                <div class="col-xs-8">
                  <input type='text' class="form-control input-sm" name="tgl_jt" id="tgl_jt"  readonly="readonly"   value="<?php echo $list->tanggal_jt;?>"/>
                </div>                                    
              </div>
              <div class="col-md-12 col-xs-12">
                <div class="col-xs-4"><label>Responsible </label></div>
                <div class="col-xs-8">
                  <input type='text' class="form-control input-sm" name="responsible" id="responsible"  readonly="readonly"   value="<?php echo $list->responsible;?>"/>
                </div>                                    
              </div>
              <div class="col-md-12 col-xs-12">
                <div class="col-xs-4"><label>Reff Note PPIC </label></div>
                <div id="ta" class="col-xs-8">
                  <textarea class="form-control input-sm" name="note" id="note" readonly="readonly"><?php echo $list->reff_note; ?></textarea>
                </div>                                    
              </div>
              <div class="col-md-12 col-xs-12">
                <div class="col-xs-4"><label>No Mesin </label></div>
                <div class="col-xs-8">
                  <select class="form-control input-sm" name="mc" id="mc" disabled="true">
                    <option value="">-- Pilih No Mesin --</option>
                    <?php 
                      foreach ($mesin as $val) {
                        if($list->mc_id==$val->mc_id){
                          echo "<option selected value='".$val->mc_id."'>".$val->nama_mesin."</option>";
                        }else{
                          echo "<option value='".$val->mc_id."'>".$val->nama_mesin."</option>";
                        }
                      }
                    ?>
                  </select>                 
                </div>                                    
              </div>
              <div class="col-md-12 col-xs-12">
                <div class="col-xs-4"><label>Target Efisiensi/ Jam </label></div>
                <div class="col-xs-8">
                  <input type='text' class="form-control input-sm" name="target_efisiensi" id="target_efisiensi"  readonly="readonly"   value="<?php echo $list->target_efisiensi;?>" onkeyup="validAngka(this)"/>
                </div>                                    
              </div>
              <div class="col-md-12 col-xs-12">
                <div class="col-xs-4"><label>Qty 1 Standar </label></div>
                <div class="col-xs-8">
                  <input type='text' class="form-control input-sm" name="qty1_std" id="qty1_std"  readonly="readonly"   value="<?php echo $list->qty1_std;?>" onkeyup="validAngka(this)"/>
                </div>                                    
              </div>
              <div class="col-md-12 col-xs-12">
                <div class="col-xs-4"><label>Qty 2 Standar </label></div>
                <div class="col-xs-8">
                  <input type='text' class="form-control input-sm" name="qty2_std" id="qty2_std"  readonly="readonly"   value="<?php echo $list->qty2_std;?>" onkeyup="validAngka(this)"/>
                </div>                                    
              </div>
              <div class="col-md-12 col-xs-12">
                <div class="col-xs-4"><label>Type Production</label></div>
                <div class="col-xs-8">
                  <select class="form-control input-sm" name="type_production" id="type_production" disabled >
                  <?php $type = array('Proofing', 'Production'); ?>
                  <option value="">Pilih Type Production</option>
                  <?php 
                    foreach($type as $val){
                      if($val == $list->type_production ){
                        echo "<option selected>".$val."</option>";
                      }else{
                        echo "<option>".$val."</option>";
                      }
                    }
                  ?>
                  </select>
                </div>                                    
              </div>
              <div class="col-md-12 col-xs-12">
                <div class="col-xs-4"><label>Lot Prefix</label></div>
                <div class="col-xs-8">
                  <input type='text' class="form-control input-sm" name="lot_prefix" id="lot_prefix"  readonly="readonly"   value="<?php echo $lot_prefix;?>" />
                </div>                                    
              </div>
               <div class="col-md-12 col-xs-12">
                <div class="col-xs-4"><label>Lot Prefix Waste</label></div>
                <div class="col-xs-8">
                  <input type='text' class="form-control input-sm" name="lot_prefix_waste" id="lot_prefix_waste"  readonly="readonly"   value="<?php echo $list->lot_prefix_waste;?>" />
                </div>                                    
              </div>

            </div>
            </div>
          </form>

       
            <div class="row">
              <div class="col-md-12">
                <!-- Custom Tabs -->
                <div class="">
                  <ul class="nav nav-tabs">
                    <li class="active"><a href="#tab_1" data-toggle="tab">Bahan Baku</a></li>
                    <li><a href="#tab_2" data-toggle="tab">Barang Jadi</a></li>
                    <li><a href="#tab_3" data-toggle="tab">Waste</a></li>
                  </ul>
                  <div class="tab-content"><br>
                    <div class="tab-pane active" id="tab_1">
                       <?php //untuk hitung qty sisa fg 
                        $qty = $list->qty;
                        $qty_fg  = $total_fg->total_qty;
                        $sisa_qty = $qty - $qty_fg;
                      ?>
                       <input type="hidden" name="total_sisa" id="total_sisa" value="<?php echo $sisa_qty;?>">
                       <input type="hidden" name="uom_qty_sisa" id="uom_qty_sisa" value="<?php echo $list->uom;?>">
                       <input type="hidden" name="status" id="status" value="<?php echo $list->status?>">
                       <input type="hidden" name="qty_prod" id="qty_prod" value="<?php echo $list->qty?>">

                      <!--bahan baku-->
                      <div class="col-md-12">
                        <div class="box box-primary">
                        <div class="box-body">
                        <!-- Tabel Kiri -->
                        <div class="col-md-6 table-responsive">
                          <table class="table table-condesed table-hover rlstable" width="100%" id ="table_rm">
                            <label>Akan dikonsumsi</label>
                            <tr>
                              <th class="style no">No.</th>
                              <th class="style">Product</th>
                              <th class="style" style="text-align: right;">Qty</th>
                              <th class="style">uom</th>
                              <th class="style">Tersedia</th>
                              <th class="style">Status</th>
                              <th class="style">reff</th>
                              <th class="style"></th>
                            </tr>
                            <tbody>
                              <?php
                                $color = '';
                                foreach ($rm as $row) {

                                $sisa        = $row->qty-$row->sum_qty_done;
                                $qty_rm_sisa = $sisa;

                                if(round($row->sum_qty,2) > round($sisa,2)){$color = 'red';}elseif(round($row->sum_qty,2) < round($sisa,2)){$color = 'blue';}else{$color = '';}

                              ?>
                                <tr class="num">
                                  <td></td>
                                  <td><?php if($row->type == 'stockable'){?>
                                      <a href="javascript:void(0)" onclick="view_quant('<?php echo $list->kode; ?>','<?php echo htmlentities($row->origin_prod); ?>','<?php echo $row->move_id ?>','<?php echo $row->kode_produk?>','<?php echo htmlentities($row->nama_produk)?>')"><?php echo '['.$row->kode_produk.'] '.$row->nama_produk?></a>
                                     <?php }else{
                                        echo '['.$row->kode_produk.'] '.$row->nama_produk;
                                     }?>
                                  </td>
                                  <td align="right"><?php echo number_format($qty_rm_sisa,2)?></td>
                                  <td><?php echo $row->uom?></td>
                                  <td style="color:<?php echo $color;?>" align="right"><?php  if(!empty($row->sum_qty) AND $row->status == 'ready')echo number_format($row->sum_qty,2); if($row->status == 'cancel') echo number_format($row->sum_qty_cancel,2); ?></td>
                                  <td><?php if($row->status == 'cancel') echo 'Batal';  else echo $row->status;?></td>
                                  <td><?php echo $row->reff?></td>
                                  <td><?php if($row->type == 'stockable' AND ($row->status == 'ready' or $row->status == 'draft') AND $type_mo['type_mo'] !='colouring' AND $akses_menu > 0){?>
                                    <a href="javascript:void(0)" onclick="tambah_quant('<?php echo $row->kode_produk ?>','<?php echo $row->move_id ?>', '<?php echo $row->origin_prod?>')" data-toggle="tooltip" title="Tambah Quant">
                                     <span class="glyphicon  glyphicon-share"></span></a>
                                   <?php }?>
                                    <!--a onclick="hapus('<?php  echo $row->kode ?>', '<?php  echo ($row->row_order) ?>')"  href="javascript:void(0)"><i class="fa fa-trash" style="color: red"></i> </a-->
                                  </td>
                                </tr>
                              <?php 

                                }
                              ?>
                            </tbody>
                            <!--tr>
                              <td colspan="6">
                                 <a href="#" class="add"><i class="fa fa-plus"></i> Tambah Data</a>
                              </td>
                            </tr-->
                          </table>
                        </div>
                        <!-- Tabel Kiri -->

                         <!-- Tabel Kanan -->
                        <div class="col-md-6 table-responsive">
                          <table class="table table-condesed table-hover rlstable" width="100%" id ="table_rm_hasil">
                            <label>Sudah dikonsumsi</label>
                            <tr>
                              <th class="style no">No.</th>
                              <th class="style">Product</th>
                              <th class="style" style="text-align: right;">Qty</th>
                              <th class="style">uom</th>
                              <th class="style" style="text-align: right;">Qty2</th>
                              <th class="style">uom2</th>
                              <!--th class="style"></th-->
                            </tr>
                            <tbody>
                              <?php
                                foreach ($hasil_rm as $row) {
                              ?>
                                <tr class="num">
                                  <td></td>
                                  <td>
                                    <a href="javascript:void(0)" onclick="view_rm_hasil('<?php echo $list->kode; ?>','<?php echo ($row->kode_produk); ?>', '<?php echo htmlentities($row->nama_produk)?>')"><?php echo '['.$row->kode_produk.'] '.$row->nama_produk?></a>
                                  </td>
                                  <td align="right"><?php echo number_format($row->tot_qty,2)?></td>
                                  <td><?php echo $row->uom?></td>
                                  <td align="right"><?php echo number_format($row->tot_qty2,2)?></td>
                                  <td><?php echo $row->uom2?></td>
                                  <!--td>
                                    <a onclick="hapus('<?php  echo $row->kode ?>', '<?php  echo htmlentities($row->nama_produk) ?>')"  href="javascript:void(0)"><i class="fa fa-trash" style="color: red"></i> </a>
                                  </td-->
                                </tr>
                              <?php 
                                }
                              ?>
                            </tbody>
                          </table>
                        </div>
                         <!--/.Tabel Kanan -->
                        </div>
                        </div>
                      </div>
                      <!--/.bahan baku-->
                      <?php if($type_mo['type_mo']=='colouring') {//cek type_mo 
                      ?>
                      <!--obat--> 
                      <div class="col-md-12">
                        <div class="box box-primary">
                          <div class="box-header with-border">
                            <h3 class="box-title"><b>Obat</b></h3>
                          </div>
                        <div class="box-body">
                        <!-- Tabel Kiri -->
                        <div class="col-md-6 table-responsive">
                          <table class="table table-condesed table-hover rlstable" width="100%" id ="table_dyest">
                            <label>Dyeing Stuff</label>
                            <tr>
                              <th class="style no">No.</th>
                              <th class="style">Product</th>
                              <th class="style">%</th>
                              <th class="style" style="text-align: right;">Qty</th>
                              <th class="style">uom</th>
                              <th class="style">Status</th>
                              <th class="style">reff</th>
                            </tr>
                            <tbody>
                              <?php
                               if(!empty($dystuff)){
                                foreach ($dystuff as $row) {
                              ?>
                                <tr class="num">
                                  <td></td>
                                  <td><?php echo '['.$row->kode_produk.'] '.$row->nama_produk?></td>
                                  <td><?php echo $row->qty_asli?></td>
                                  <td align="right"><?php echo number_format($row->qty,2)?></td>
                                  <td><?php echo $row->uom?></td>
                                  <td><?php echo $row->status?></td>
                                  <td><?php echo $row->reff_note?></td>
                                  <!--td>
                                    <a onclick=""  href="javascript:void(0)"><i class="fa fa-trash" style="color: red"></i> </a>
                                  </td-->
                                </tr>
                              <?php 
                                }
                              }
                              ?>
                            </tbody>
                          </table>
                        </div>
                        <!-- Tabel Kiri -->

                        <!-- Tabel Kanan -->
                        <div class="col-md-6 table-responsive">
                          <table class="table table-condesed table-hover rlstable" width="100%" id ="table_aux">
                            <label>Auxiliary</label>
                            <tr>
                              <th class="style no">No.</th>
                              <th class="style">Product</th>
                              <th class="style">g/L</th>
                              <th class="style" style="text-align: right;">Qty</th>
                              <th class="style">uom</th>
                              <th class="style">Status</th>
                              <th class="style">reff</th>
                            </tr>
                            <tbody>
                              <?php
                                if(!empty($aux)){
                                foreach ($aux as $row) {
                              ?>
                                <tr class="num">
                                  <td></td>
                                  <td><?php echo '['.$row->kode_produk.'] '.$row->nama_produk?></td>
                                  <td><?php echo $row->qty_asli?></td>
                                  <td align="right"><?php echo number_format($row->qty,2)?></td>
                                  <td><?php echo $row->uom?></td>
                                  <td><?php echo $row->status?></td>
                                  <td><?php echo $row->reff_note?></td>
                                  <!--td>
                                    <a onclick=""  href="javascript:void(0)"><i class="fa fa-trash" style="color: red"></i> </a>
                                  </td-->
                                </tr>
                              <?php 
                                }
                              }
                              ?>
                            </tbody>
                          </table>
                        </div>
                         <!--/.Tabel Kanan -->
                        </div>
                        </div>
                      </div>
                      <!--/.obat-->
                    <?php }?>

                    </div>
                    <!-- /.tab-pane -->
                    <div class="tab-pane" id="tab_2">

                      <!-- div col-md-12 -->
                      <div class="col-md-12">                        
                    
                      <div class="box box-primary">
                      <div class="box-body">

                      <!-- Tabel Kiri -->
                      <div class="col-md-4 table-responsive">
                        <table class="table table-condesed table-hover table-responsive rlstable" id="table_fg">
                          <label>Akan diproduksi</label>
                          <tr>
                            <th class="style no">No.</th>
                            <th class="style">Product</th>
                            <th class="style" style="text-align: right;">Qty</th>
                            <th class="style">uom</th>
                         </tr>
                          <tbody>
                            <?php foreach ($fg as $row) {
                              $sisa_qty_fg = $row->qty - $row->sum_fg_hasil;

                              ?>
                              <tr class="num">
                                <td></td>
                                <td><?php echo $list->nama_produk?></td>
                                <td align="right"><?php echo number_format($sisa_qty_fg,2)?></td>
                                <td><?php echo $list->uom?></td>
                              </tr>
                          </tbody>
                          <?php 
                          }
                          ?>
                        </table>
                      </div>
                      <!--./ Tabel Kiri -->
                      
                      <!-- Tabel Kanan -->
                      <div class="col-md-8 table-responsive">
                        <table class="table table-condesed table-hover table-responsive rlstable" id="table_fg_hasil">
                          <label>Sudah diproduksi</label>
                          <div class="pull-right" data-toggle="tooltip" title="Disabled tooltip">
                            <button class="btn btn-primary btn-xs"  title="Print Barcode" id="btn-print-barcode"><i class="fa fa-print"></i> Print Barcode
                            </button>
                          </div> 

                          <tr>
                            <th class="style no">No.</th>
                            <th class="style">Product</th>
                            <th class="style">Lot</th>
                            <th class="style" style="text-align: right;">Qty</th>
                            <th class="style">uom</th>
                            <th class="style" style="text-align: right;">Qty2</th>
                            <th class="style">uom2</th>
                            <?php if($show_lebar['show_lebar'] == 'true'){?>
                              <th class="style">Lbr.Greige</th>
                              <th class="style">Lbr.Jadi</th>
                            <?php }?>
                            <th class="style">Grade</th>
                            <th class="style">Reff Note</th>
                            <th class="style">Cacat</th>
                            <th class="style">Print</th>
                         </tr>
                          <tbody>
                            <?php
                              foreach ($hasil_fg as $row) {
                            ?>
                              <tr class="num">
                                <td></td>
                                <td ><?php echo $row->nama_produk?></td>
                                <td><?php echo $row->lot?></td>
                                <td align="right"><?php echo number_format($row->qty,2)?></td>
                                <td><?php echo $row->uom?></td>
                                <td align="right"><?php echo number_format($row->qty2,2)?></td>
                                <td><?php echo $row->uom2?></td>
                                <?php if($show_lebar['show_lebar'] == 'true'){?>
                                  <td><?php echo $row->lebar_greige." ".$row->uom_lebar_greige?></td>
                                  <td><?php echo $row->lebar_jadi." ".$row->uom_lebar_jadi?></td>
                                <?php }?>
                                <td><?php echo $row->nama_grade?></td>
                                <td class="text-wrap width-200"><?php echo $row->reff_note?></td>
                                <td>
                                   <a href="javascript:void(0)" onclick="rekam_cacat('<?php echo $list->dept_id ?>', '<?php echo $row->quant_id ?>','<?php echo $row->lot ?>')" data-toggle="tooltip" title="Rekam Cacat Lot">
                                     <span class="glyphicon  glyphicon-share"></span></a>
                                </td>
                                <td>
                                  <input type="checkbox" class='checkPrint' value="<?php echo $row->lot.'^^'.$row->nama_grade.'|^'?>">

                                  <!--a href="javascript:void(0)" onclick="print_lot('<?php echo $row->lot ?>','<?php echo $row->nama_grade ?>')" data-toggle="tooltip" title="Print">
                                     <span class="fa  fa-print"></span>
                                   </a-->
                                </td>
                              </tr>
                            <?php 
                              }
                            ?>
                          </tbody>
                        </table>
                      </div>
                       <!-- /.Tabel Kanan -->
                      </div>
                      </div>
                      </div>
                      <!-- ./ div col-md-12 -->
                    </div>
                    <!-- /.tab-pane -->

                    <!-- tab-pane waste -->
                    <div class="tab-pane" id=tab_3>
                      <div class="col-md-12">
                        <div class="box box-primary">
                          <div class="box-body">
                            <div class="col-xs-12">
                              <table class="table table-condesed table-hover table-responsive rlstable" id="table_wd">
                               <label>Waste Details</label>
                                <tr>
                                  <th class="style no">No.</th>
                                  <th class="style">Product</th>
                                  <th class="style">Lot</th>
                                  <th class="style" style="text-align: right;">Qty</th>
                                  <th class="style">uom</th>
                                  <th class="style" style="text-align: right;">Qty2</th>
                                  <th class="style">uom2</th>
                                  <th class="style">Reff Note</th>
                                </tr>
                                <tbody>
                                  <?php
                                    foreach ($hasil_waste as $row) {
                                  ?>
                                    <tr class="num">
                                      <td></td>
                                      <td ><?php echo $row->nama_produk?></td>
                                      <td><?php echo $row->lot?></td>
                                      <td align="right"><?php echo number_format($row->qty,2)?></td>
                                      <td><?php echo $row->uom?></td>
                                      <td align="right"><?php echo number_format($row->qty2,2)?></td>
                                      <td><?php echo $row->uom2?></td>
                                      <td class="text-wrap width-200"><?php echo $row->reff_note?></td>
                                    </tr>
                                  <?php 
                                    }
                                  ?>
                                </tbody>
                              </table>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <!-- /tab-pane waste -->
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
     <?php $this->load->view("admin/_partials/footer.php") ?>
    </div>
  </footer>

  <div id="load_modal">
    <!-- Load Partial Modal -->
   <?php $this->load->view("admin/_partials/modal.php") ?>
  </div>

</div>
<!--/. Site wrapper -->

<?php $this->load->view("admin/_partials/js.php") ?>
<!--script src="https://rawgit.com/RobinHerbots/jquery.inputmask/3.x/dist/jquery.inputmask.bundle.js"></script-->
<script type="text/javascript">

   // show after refresh close modal
   $(document).on('click','#datetimepicker4',function (e) {
      $('#datetimepicker4').datetimepicker({
            format : 'YYYY-MM-DD HH:mm:ss',
            ignoreReadonly: true
        });
  });

  $(document).on('click','#datetimepicker3',function (e) {
      $('#datetimepicker3').datetimepicker({
            format : 'YYYY-MM-DD HH:mm:ss',
            ignoreReadonly: true
        });     
  });
/*
  $('#start').inputmask("datetime",{
    mask: "y-mm-dd h:s", 
    //placeholder: "yyyy-mm-dd hh:mm:ii", 
    //leapday: "-02-29", 
    separator: "-", 
    //alias: "dd-mm-yyyy"
  });
*/
  function refresh_mo(){
    $("#tab_1").load(location.href + " #tab_1");
    $("#tab_2").load(location.href + " #tab_2");             
    $("#tab_3").load(location.href + " #tab_3");             
    $("#foot").load(location.href + " #foot");
    $("#status_bar").load(location.href + " #status_bar");
  }

  //btn-cancel-edit ketika tidak jadi untuk edit data
  $(document).on('click','#btn-cancel-edit', function(e){
    $('#mc').select2({});
    $("#mo").load(location.href + " #mo>*");
    readonly_textfield();//refresh form/btn
    
  });


  function readonly_textfield(){
    $('#btn-simpan').button('reset');
    $('#btn-simpan').hide();
    $('#btn-edit').show();
    $("#air").attr("readonly", true);
    $("#berat").attr("readonly", true);
    $("#gramasi").attr("readonly", true);
    $("#program").attr("readonly", true);
    $("#note").attr("readonly", true);
    $("#target_efisiensi").attr("readonly", true);
    $("#qty1_std").attr("readonly", true);
    $("#qty2_std").attr("readonly", true);
    $("#type_production").attr("disabled", true);
    $("#lot_prefix").attr("readonly", true);
    $("#lot_prefix_waste").attr("readonly", true);
    $("#btn-produksi").show();
    $("#btn-stok").show();
    $("#btn-done").show();
    $("#btn-print").show();
    $("#btn-produksi-batch").show();
    $('#mc').attr('disabled', true);
    $("#btn-cancel-edit").attr('id','btn-cancel');
    $("#lebar_jadi_mo").attr("readonly", true);
    $("#lebar_greige_mo").attr("readonly", true);
    $('#uom_lebar_jadi_mo').attr('disabled', true);
    $('#uom_lebar_greige_mo').attr('disabled', true);
    $('#handling').attr('disabled', true);

  }

  //untuk reload page setelah buka modal tambah_data
  $(".modal").on('hidden.bs.modal', function(){
    //window.location.reload(true);
    $("#mo").load(location.href + " #mo>*");
    $("#tambah_data .modal-dialog .modal-content .modal-body").removeClass('produksi_rm');
    $("#tambah_data .modal-dialog .modal-content .modal-body").removeClass('produksi_rm_batch');
    $("#tambah_data .modal-dialog .modal-content .modal-body").removeClass('tambah_quant');
    $("#tambah_data .modal-dialog .modal-content .modal-body").removeClass('request_resep');
    $("#tambah_data .modal-dialog ").removeClass('lebar2');
    //replace id btn_request
    $("#tambah_data .modal-dialog .modal-content .modal-footer #btn_request").attr('id',"btn-tambah");
    $("#tambah_data .modal-dialog .modal-content .modal-footer #btn-tambah").text("Simpan");

    readonly_textfield();
    refresh_mo();
  });

  //select 2 no mesin 
  $('#mc').select2({});
  // setting lot prefix TRI< JAC
  var dept_id = "<?php echo $list->dept_id?>";
  if(dept_id == 'TRI'|| dept_id == 'JAC'){
    $('#type_production').on('change', function(){
      var type = $('#type_production').val();
      if(type == 'Production'){
        $('#lot_prefix').val('KP/[MY]/[MC]/DEPT/COUNTER');
      }else if(type == 'Proofing'){
        $('#lot_prefix').val('PF/[MY]/[MC]/DEPT/COUNTER');
      }else{
        $('#lot_prefix').val('');
      }

    });
  }

  //untuk mengatur lebar textarea sesuai value yang ada
  $('#ta').on( 'change keyup keydown paste cut', 'textarea', function (){
    $(this).height(0).height(this.scrollHeight);
  }).find( 'textarea' ).change();
  
   //modal view quant tiap produk (stock move items)
  function view_quant(kode,origin_prod,move_id,kode_produk,nama_produk){
      $("#view_data").modal({
          show: true,
          backdrop: 'static'
      })
     var deptid = "<?php echo $list->dept_id; ?>"//parsing data id dept untuk log history
      $(".view_body").html('<center><h5><img src="<?php echo base_url('dist/img/ajax-loader.gif') ?> "/><br>Please Wait...</h5></center>');
      $('.modal-title').text('View Quant');
        $.post('<?php echo site_url()?>manufacturing/mO/view_mo_quant_modal',
          {kode : kode, move_id : move_id, deptid:deptid, origin_prod : origin_prod, kode_produk:kode_produk, nama_produk:nama_produk},
          function(html){
            setTimeout(function() {$(".view_body").html(html);  },1000);
          }   
       );
  }

  //untuk tambah details dari tabel stock quant
  function tambah_quant(kode_produk,move_id,origin_prod){   
      var status = "<?php echo $list->status;?>";
      if(status == 'done'){
         alert_modal_warning('Maaf, Anda tidak bisa tambah Quant, Proses Produksi telah Selesai ! ');
      }else{

        $('#btn-tambah').button('reset');
        $("#tambah_data").modal({
            show: true,
            backdrop: 'static'
        })
        $("#tambah_data .modal-dialog .modal-content .modal-body").addClass('tambah_quant');
        $("#tambah_data .modal-dialog .modal-content .modal-footer #btn-tambah").attr('disabled',true);

       var deptid = "<?php echo $list->dept_id; ?>"//parsing data id dept untuk log history
        $(".tambah_data").html('<center><h5><img src="<?php echo base_url('dist/img/ajax-loader.gif') ?> "/><br>Please Wait...</h5></center>');
        $('.modal-title').text('Tambah Details Product Qty');
          $.post('<?php echo site_url()?>manufacturing/mO/tambah_data_details_quant_mo',
            {kode_produk : kode_produk, move_id : move_id, deptid:deptid, origin_prod : origin_prod},
          ).done(function(html){
            setTimeout(function() {
              $(".tambah_quant").html(html)  
            },1000);
            $("#tambah_data .modal-dialog .modal-content .modal-footer #btn-tambah").attr('disabled',false);
          });
      }
  }

  function view_rm_hasil(kode,kode_produk,nama_produk){
      $("#view_data").modal({
          show: true,
          backdrop: 'static'
      })
      //var deptid = "<?php echo $list->dept_id; ?>"//parsing data id dept untuk log history
      $(".view_body").html('<center><h5><img src="<?php echo base_url('dist/img/ajax-loader.gif') ?> "/><br>Please Wait...</h5></center>');
      $('.modal-title').text('View Sudah dikonsumsi');
        $.post('<?php echo site_url()?>manufacturing/mO/view_mo_rm_hasil',
          {kode : kode, kode_produk : kode_produk, nama_produk : nama_produk},
          function(html){
            setTimeout(function() {$(".view_body").html(html);  },1000);
          }   
      );
  }

  //untuk editable textfield 
  $(document).on('click','#btn-edit', function(e){
    $('#mc').select2({});
    $("#air").attr("readonly", false);
    $("#berat").attr("readonly", false);
    $("#gramasi").attr("readonly", false);
    $("#program").attr("readonly", false);
    $("#note").attr("readonly", false);
    $("#target_efisiensi").attr("readonly", false);
    $("#qty1_std").attr("readonly", false);
    $("#qty2_std").attr("readonly", false);
    $("#lot_prefix_waste").attr("readonly", false);

    $('#type_production').attr('disabled', false).attr('id','type_production');

    var dept_id = "<?php echo $list->dept_id; ?>";
    if(dept_id != 'TRI' && dept_id != 'JAC'){
      $("#lot_prefix").attr("readonly", false);
    }

    $("#btn-simpan").show();//tampilkan btn-simpan
    $("#btn-edit").hide();//sembuyikan btn-edit
    $("#btn-produksi").hide();//sembuyikan btn-produksi
    $("#btn-produksi-batch").hide();//sembuyikan btn-produksi-batch
    $("#btn-stok").hide();//sembuyikan btn-produksi
    $("#btn-done").hide();//sembuyikan btn-done
    $("#btn-print").hide();//sembuyikan btn-print
    $("#btn-cancel").attr('id','btn-cancel-edit');// ubah id btn-cancel jadi btn-cancel-edit
    $('#mc').attr('disabled', false).attr('id', 'mc');
    $("#lebar_jadi_mo").attr("readonly", false);
    $("#lebar_greige_mo").attr("readonly", false);
    $('#uom_lebar_jadi_mo').attr('disabled', false).attr('id','uom_lebar_jadi_mo');
    $('#uom_lebar_greige_mo').attr('disabled', false).attr('id','uom_lebar_greige_mo');
    $('#handling').attr('disabled', false).attr('id','handling');

  });


  // modal view detail product
  $(".view").unbind( "click" );
  $(document).on('click','.view',function(e){
      e.preventDefault();
      $("#view_data").modal({
          show: true,
          backdrop: 'static'
      });
      $(".view_body").html('<center><h5><img src="<?php echo base_url('dist/img/ajax-loader.gif') ?> "/><br>Please Wait...</h5></center>');
      $('.modal-title').text('View Data Product');
        $.post('<?php echo site_url()?>manufacturing/mO/get_product',
          {txtProduct      : $('#product').val() },
          function(html){
            setTimeout(function() {$(".view_body").html(html);  },1000);
          }   
       );
  });

  // modal tambah data
  $(".add").unbind( "click" );
  $(document).on('click','.add',function(e){
      e.preventDefault();
      var kode = $('#kode').val();
      //$('[name="kode1"]').val(kode);
      $(".tambah_data").html('<center><h5><img src="<?php echo base_url('dist/img/ajax-loader.gif') ?> "/><br>Please Wait...</h5></center>');
      $("#tambah_data").modal({
          show: true,
          backdrop: 'static'
      });
      $('.modal-title').text('Tambah Data');
      $.post('<?php echo site_url()?>manufacturing/mO/tambah_rm',
        { kode : $('#kode').val(),},
          function(html){
            setTimeout(function() {$(".tambah_data").html(html);  },1000);
        }   
      );
  });


  // klik btn print 
  $(document).on('click',"#btn-print-barcode",function(e){

    var checkboxBarcode = [];
    var dept_id = "<?php echo $list->dept_id; ?>";
    var kode    = "<?php echo $list->kode; ?>";

    // value check pust to checkboxBarcode
    $(".checkPrint:checked").each(function() {
        checkboxBarcode.push($(this).val());
    });
    countchek = checkboxBarcode.length;

    if(countchek == 0){
      alert_modal_warning('Silahkan Pilih Product yang akan di Print !');
    }else{
      var url = '<?php echo base_url() ?>manufacturing/mO/print_barcode';
      window.open(url+'?kode='+ kode+'&&dept_id='+ dept_id+'&&countchek='+ countchek+'&&checkboxBarcode='+ checkboxBarcode,'_blank');
    }

  });


  //validasi input angka
  function validAngka(a){
    if(!/^[0-9.]+$/.test(a.value)){
      a.value = a.value.substring(0,a.value.length-1000);
    }    
  }

  //highlight form
  function highlight(a){
    $(a).css("border","1px solid red");
    validAngka(a);
  }


  //open modal rekam cacat lot
  function rekam_cacat(deptid,quant_id,lot){ 
    var kode   = $("#kode").val();
    var status = "<?php echo $list->status;?>";
    $("#tambah_data").modal({
      show: true,
      backdrop: 'static',    
    });

    $(".tambah_data").html('<center><h5><img src="<?php echo base_url('dist/img/ajax-loader.gif') ?> "/><br>Please Wait...</h5></center>');
    $('.modal-title').text('Rekam Cacat Lot');
    $.post('<?php echo site_url()?>manufacturing/mO/rekam_cacat_modal',
          {deptid : deptid, lot : lot, quant_id : quant_id, kode : kode, status : status },
          function(html){
            setTimeout(function() { $(".tambah_data").html(html); },1000);
          }   
    );
  }

  // modal produksi batch
  $("#btn-produksi-batch").unbind( "click" );
  $(document).on('click','#btn-produksi-batch',function(e){

    var status = $('#status').val();
    if(status == 'done'){
      alert_modal_warning('Maaf, Proses Produksi telah Selesai !');
    }else if(status == 'cancel'){
      alert_modal_warning('Maaf, Proses Produksi telah dibatalkan !');
    }else if(status == 'draft'){
      alert_modal_warning('Maaf, Product belum ready !');
    }else{
      e.preventDefault();
      $('.modal-title').text('Produksi batch');

      $('#btn-tambah').button('reset');
     
      var kode   = $("#kode").val();
      var deptid = "<?php echo $list->dept_id; ?>"//parsing data id dept untuk log history
      var move_id = '<?php echo $move_id_rm['move_id'];?>';
      var move_id_fg = '<?php echo $move_id_fg['move_id'];?>';
      var qty  = '<?php echo $list->qty?>';
      $("#tambah_data").modal({
          show: true,
          backdrop: 'static'
      });
      $("#tambah_data .modal-dialog .modal-content .modal-body").addClass('produksi_rm_batch');
      $("#tambah_data .modal-dialog .modal-content .modal-footer #btn-tambah").attr('disabled',true);

      $("#btn-produksi").prop('disabled',true);

      $(".produksi_rm_batch").html('<center><h5><img src="<?php echo base_url('dist/img/ajax-loader.gif') ?> "/><br>Please Wait...</h5></center>');
      $.post('<?php echo site_url()?>manufacturing/mO/produksi_rm_batch',
        { kode        : $('#kode').val(),
          kode_produk : $('#kode_produk').val(), 
          nama_produk : $('#product').val(),
          sisa_qty    : $('#total_sisa').val(), 
          uom_qty_sisa    : $('#uom_qty_sisa').val(), 
          deptid      : deptid, 
          kode        : kode,
          move_id     : move_id, 
          move_id_fg  : move_id_fg, 
          qty         : $('#qty_prod').val(),  
          origin      : $('#origin').val(),
          qty1_std    : $('#qty1_std').val(),
          qty2_std    : $('#qty2_std').val(),
          lot_prefix  : $('#lot_prefix').val(),
          lot_prefix_waste  : $('#lot_prefix_waste').val(),       
        } 
      ).done(function(html){
        setTimeout(function() {
          $(".produksi_rm_batch").html(html)  
        },1000);
        $("#tambah_data .modal-dialog .modal-content .modal-footer #btn-tambah").attr('disabled',false);
      });
    }
  });

  // modal produksi 
  $("#btn-produksi").unbind( "click" );
  $(document).on('click','#btn-produksi',function(e){

    var status = $('#status').val();
    if(status == 'done'){
      alert_modal_warning('Maaf, Proses Produksi telah Selesai !');
    }else if(status == 'cancel'){
      alert_modal_warning('Maaf, Proses Produksi telah dibatalkan !');
    }else if(status == 'draft'){
      alert_modal_warning('Maaf, Product belum ready !');
    }else{
      e.preventDefault();
      $('.modal-title').text('Produksi');
      $('#btn-tambah').button('reset');
     
      var kode   = $("#kode").val();
      var deptid = "<?php echo $list->dept_id; ?>"//parsing data id dept untuk log history
      var move_id = '<?php echo $move_id_rm['move_id'];?>';
      var move_id_fg = '<?php echo $move_id_fg['move_id'];?>';
      var qty  = '<?php echo $list->qty?>';
      $("#tambah_data").modal({
          show: true,
          backdrop: 'static'
      });
      $("#tambah_data .modal-dialog .modal-content .modal-body").addClass('produksi_rm');
      $("#tambah_data .modal-dialog .modal-content .modal-footer #btn-tambah").attr('disabled',true);
      $("#btn-produksi-batch").prop('disabled',true);

      $(".produksi_rm").html('<center><h5><img src="<?php echo base_url('dist/img/ajax-loader.gif') ?> "/><br>Please Wait...</h5></center>');
      $.post('<?php echo site_url()?>manufacturing/mO/produksi_rm',
        { kode        : $('#kode').val(),
          kode_produk : $('#kode_produk').val(), 
          nama_produk : $('#product').val(),
          sisa_qty    : $('#total_sisa').val(), 
          uom_qty_sisa    : $('#uom_qty_sisa').val(), 
          deptid      : deptid, 
          kode        : kode,
          move_id     : move_id, 
          move_id_fg  : move_id_fg, 
          qty         : $('#qty_prod').val(),  
          origin      : $('#origin').val(),
          qty1_std    : $('#qty1_std').val(),
          qty2_std    : $('#qty2_std').val(),
          lot_prefix  : $('#lot_prefix').val(),
          
        }   
      ).done(function(html){
        setTimeout(function() {
              $(".produksi_rm").html(html);  
            },1000);
        $("#tambah_data .modal-dialog .modal-content .modal-footer #btn-tambah").attr('disabled',false);

      });;
    }
  });


  //hapus data bahan baku
  function hapus(kode, row_order){
      bootbox.dialog({
      message: "Apakah Anda ingin menghapus data ?",
      title: "<i class='glyphicon glyphicon-trash'></i> Delete !",
      buttons: {
        danger: {
            label    : "Yes ",
            className: "btn-primary btn-sm",
            callback : function() {
                  $.ajax({
                        type: 'POST',
                        dataType: "json",
                        url : "<?php echo site_url('manufacturing/mO/hapus_rm')?>",
                        data : {kode : kode, row_order:row_order },
                  })
                  .done(function(response){
                    $("#table_rm").load(location.href + " #table_rm");
                    alert_notify(response.icon,response.message,response.type, function(){});
                  })
                  .fail(function(){
                    bootbox.alert('Error....');
                  })
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
  }


  //modal request resep
  $('#btn-request').click(function(){

    var status = $('#status').val();
    var move_id = '<?php echo $move_id_rm['move_id'];?>';
    if(status == 'done'){
      alert_modal_warning('Maaf, Proses Produksi telah Selesai !');
    }else if(status == 'cancel'){
      alert_modal_warning('Maaf, Proses Produksi telah dibatalkan !');
    /*
    }else if(status == 'draft'){
      alert_modal_warning('Maaf, Product belum ready !');
    */
    }else{

      $("#tambah_data").modal({
          show: true,
          backdrop: 'static'
      })
     var deptid = "<?php echo $list->dept_id; ?>"

      $("#tambah_data .modal-dialog .modal-content .modal-body").addClass('request_resep');
      $("#tambah_data .modal-dialog .modal-content .modal-footer #btn-tambah").attr('disabled',true);
      
      //replace id btn tambah
      $("#tambah_data .modal-dialog .modal-content .modal-footer #btn-tambah").attr('id',"btn_request");
      $("#tambah_data .modal-dialog .modal-content .modal-footer #btn_request").text("Proses");

      $("#tambah_data .modal-dialog ").addClass('lebar2');

      $(".request_resep").html('<center><h5><img src="<?php echo base_url('dist/img/ajax-loader.gif') ?> "/><br>Please Wait...</h5></center>');
      $('.modal-title').text('Pilih Varian Warna');
        $.post('<?php echo site_url()?>manufacturing/mO/request_obat_modal',
          {id_warna:$('#id_warna').val(), kode:$('#kode').val(), deptid:deptid, origin: $('#origin').val()},
        ).done(function(html){
        setTimeout(function() {
              $(".request_resep").html(html);  
            },1000);
          $("#tambah_data .modal-dialog .modal-content .modal-footer #btn_request").attr('disabled',false);
        });

    }
  });

  //klik button cek stock
  $("#btn-stok").unbind( "click" );
  $('#btn-stok').click(function(){
    var deptid = "<?php echo $list->dept_id; ?>";//parsing data id dept untuk log history
    var lokasi = "<?php echo $list->source_location; ?>";
    var type_mo = "<?php echo $type_mo['type_mo'];?>";// untuk menentukan origin
    $('#btn-stok').button('loading');
    please_wait(function(){});
    var move_id = '<?php echo $move_id_rm['move_id'];?>';
    var baseUrl = '<?php echo base_url(); ?>';
      $.ajax({
         type: "POST",
         dataType: "json",
         url :'<?php echo base_url('manufacturing/mO/cek_stok')?>',
         beforeSend: function(e) {
            if(e && e.overrideMimeType) {
                e.overrideMimeType("application/json;charset=UTF-8");
            }
         },
         data: {move_id : move_id, kode : $('#kode').val(), deptid : deptid, origin : $('#origin').val(), lokasi : lokasi, type_mo:type_mo 
          },success: function(data){
            if(data.sesi == "habis"){
              //alert jika session habis
              alert_modal_warning(data.message);
              window.location = baseUrl;//replace ke halaman login
            }else if(data.status == "failed"){
              alert_modal_warning(data.message);
              unblockUI( function() {});              
              $("#status_bar").load(location.href + " #status_bar");
              $("#tab_1").load(location.href + " #tab_1");
              $("#tab_2").load(location.href + " #tab_2"); 
              $("#tab_3").load(location.href + " #tab_3");                 
              $("#foot").load(location.href + " #foot");
              $('#btn-stok').button('reset');
              if(data.status_kurang == "yes"){
                alert_notify(data.icon2,data.message2,data.type2,function(){});
              }
            }else{

              if(data.terpenuhi == "yes"){
                 unblockUI( function() {});                  
              }else{
                unblockUI( function() {
                    setTimeout(function() { alert_notify(data.icon,data.message,data.type,function(){}); }, 1000);
                });
              }
              $("#status_bar").load(location.href + " #status_bar");
              $("#tab_1").load(location.href + " #tab_1");
              $("#tab_2").load(location.href + " #tab_2");  
              $("#tab_3").load(location.href + " #tab_3");     
              $("#foot").load(location.href + " #foot");
              $('#btn-stok').button('reset');
            }
            $("#mo").load(location.href + " #mo>*");

          },error: function (xhr, ajaxOptions, thrownError) { 
            alert(xhr.responseText);
            setTimeout($.unblockUI, 1000); 
            unblockUI( function(){});
            $('#btn-stok').button('reset');
          }
      });
    });



  //klik button simpan
  $("#btn-simpan").unbind( "click" );
  $('#btn-simpan').click(function(){
    $('#btn-simpan').button('loading');
    var deptid  = "<?php echo $list->dept_id; ?>";//parsing data id dept untuk log history
    var type_mo = "<?php echo $type_mo['type_mo'];?>";//untuk validasi air dan berat jika type mo nya hanya colouring
    please_wait(function(){});

      $.ajax({
         type: "POST",
         dataType: "json",
         url :'<?php echo base_url('manufacturing/mO/simpan')?>',
         beforeSend: function(e) {
            if(e && e.overrideMimeType) {
                e.overrideMimeType("application/json;charset=UTF-8");
            }
         },
         data: {berat : $('#berat').val(),
                air   : $('#air').val(),
                start : $('#start').val(),
                finish: $('#finish').val(),
                reff_note   : $('#note').val(),
                mesin       : $('#mc').val(),
                kode        : $('#kode').val(),
                target_efisiensi : $('#target_efisiensi').val(),
                qty1_std    : $('#qty1_std').val(),
                qty2_std    : $('#qty2_std').val(),
                type_production  : $('#type_production').val(),
                lot_prefix  : $('#lot_prefix').val(),
                lot_prefix_waste  : $('#lot_prefix_waste').val(),
                deptid      : deptid,
                type_mo     : type_mo,
                lebar_greige     : $('#lebar_greige_mo').val(),
                uom_lebar_greige : $('#uom_lebar_greige_mo').val(),
                lebar_jadi       : $('#lebar_jadi_mo').val(),
                uom_lebar_jadi   : $('#uom_lebar_jadi_mo').val(),
                handling    : $('#handling').val(),
                gramasi     : $('#gramasi').val(),
                program     : $('#program').val(),
                origin      : $('#origin').val(),

          },success: function(data){
            if(data.sesi == "habis"){
              //alert jika session habis
              alert_modal_warning(data.message);
              window.location.replace('../index');
            }else if(data.status == "failed"){
               //jika ada form belum keisi
              unblockUI( function() {
                setTimeout(function() { alert_notify(data.icon,data.message,data.type,function(){}); }, 1000);
              });
              $('#btn-simpan').button('reset');
              document.getElementById(data.field).focus();//focus ke field yang belum keisi
              refresh_mo();
            }else{
              //jika berhasil disimpan/diubah
              unblockUI( function() {
                  setTimeout(function() { alert_notify(data.icon,data.message,data.type,function(){}); }, 1000);
              });
              $('#btn-simpan').button('reset');
              readonly_textfield();
              refresh_mo();
              $('.highlight').css("borderColor", "");//clear css highlight
            }

          },error: function (xhr, ajaxOptions, thrownError) { 
            alert(xhr.responseText);
            setTimeout($.unblockUI, 1000); 
            unblockUI( function(){});
            $('#btn-simpan').button('reset');
          }
      });
    });


  //klik button done
  $("#btn-done").unbind( "click" );
  $('#btn-done').click(function(){
    var status = $('#status').val();
    var move_id = '<?php echo $move_id_rm['move_id'];?>';
    if(status == 'done'){
      alert_modal_warning('Maaf, Proses Produksi telah Selesai !');
    }else if(status == 'cancel'){
      alert_modal_warning('Maaf, Proses Produksi telah dibatalkan !');
    /*
    }else if(status == 'draft'){
      alert_modal_warning('Maaf, Product belum ready !');
    */
    }else{

    $('#btn-done').button('loading');
    var deptid  = "<?php echo $list->dept_id; ?>";//parsing data id dept untuk log history    
    var qty_target  = "<?php echo $list->qty; ?>";
    please_wait(function(){});

        $.ajax({
           type: "POST",
           dataType: "json",
           url :'<?php echo base_url('manufacturing/mO/mo_done')?>',
           beforeSend: function(e) {
              if(e && e.overrideMimeType) {
                  e.overrideMimeType("application/json;charset=UTF-8");
              }
           },
            data: {kode   : $('#kode').val(),              
                   deptid : deptid,   
                   qty_target : qty_target,
                   move_id : move_id          
            },success: function(data){
              if(data.sesi == "habis"){
                //alert jika session habis
                alert_modal_warning(data.message);
                window.location.replace('../index');
              }else if(data.status == "failed"){
                unblockUI( function() {
                  setTimeout(function() { alert_notify(data.icon,data.message,data.type,function(){}); }, 1000);
                });
                refresh_mo(); 
                $('#btn-done').button('reset')         
              }else{
                unblockUI( function() {
                    setTimeout(function() { alert_notify(data.icon,data.message,data.type,function(){}); }, 1000);
                });
                refresh_mo();
                $('#btn-done').button('reset');           
              }

            },error: function (xhr, ajaxOptions, thrownError) { 
              alert(xhr.responseText);
              setTimeout($.unblockUI, 1000); 
              unblockUI( function(){});
              $('#btn-done').button('reset');
            }
        });
    }
  });


  $(document).on("click","#btn-tambah-produksi-batch",function(e){
    alert('tes');
  });


</script>


</body>
</html>
