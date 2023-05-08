
<!DOCTYPE html>
<html lang="en">
<head>
  <?php $this->load->view("admin/_partials/head.php") ?>

  <style>
    button[id="btn-simpan"],button[id="btn-cancel"],button[id="btn-unhold"]{/*untuk hidden button simpan/cancel di top bar MO*/
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

    table.table td .cancel {
        display: none;
        color : red;
        margin: 10 0px;
        min-width:  24px;
    }

    table.table td .add {
        display: none;
    }

    .min-width-200{
        min-width: 200px;;
    }

    .min-width-100{
        min-width: 100px;
    }

    .min-width-80{
        min-width: 80px;;
    }

    .width-btn {
      width: 54px !important;
    }

  </style>

</head>

<body class="hold-transition skin-black fixed sidebar-mini">

<style>
    <?php 
      if($list->status == 'hold'){
    ?>
      button[id="btn-edit"],button[id="btn-stok"],button[id="btn-hold"],button[id="btn-produksi"],button[id="btn-produksi-batch"],button[id="btn-consume"],button[id="btn-waste"]{
        display: none;
      }
      button[id="btn-unhold"]{
        display : inline
      }
    <?php 
    }
    ?>
</style>
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
                        <a href="#" class="view" title="Lihat detail Produk"><span class="glyphicon  glyphicon-share"></span></a>
                    </span>
                    <input type="hidden" class="form-control input-sm" name="kode_produk" id="kode_produk"  value="<?php echo $list->kode_produk;?>"  readonly="readonly"   />
                  </div>
                </div>  
              </div>
              <div class="col-md-12 col-xs-12">
                <div class="col-xs-4"><label>Qty </label></div>
                <div class="col-xs-8">
                  <input type="text" class="form-control input-sm" name="qty" id="qty"  value="<?php echo number_format($list->qty,2); echo ' '.$list->uom;?>"  readonly="readonly"   />
                </div>                                    
              </div>
              <div class="col-md-12 col-xs-12">
                <div class="col-xs-4"><label>BOM </label></div>
                <div class="col-xs-8">
                  <div class='input-group'>
                    <input type="text" class="form-control input-sm" name="bom" id="bom"  value="<?php echo htmlentities($bom['nama_bom']);?>"  readonly="readonly"   />
                    <span class="input-group-addon">
                        <a href="#" class="view-bom"  title="Lihat detail BOM"><span class="glyphicon  glyphicon-share"></span></a>
                    </span>
                  </div>                                    
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
                  <input type="text" class="form-control input-sm " name="air" id="air" value="<?php echo $list->air;?>"  onkeyup="validAngka(this)" <?php if($disable == "yes") echo 'readonly="readonly"';?>  readonly="readonly" >
                </div>                                    
              </div>
              <div class="col-md-12 col-xs-12">
                <div class="col-xs-4"><label>Berat (Kg) </label></div>
                <div class="col-xs-8">
                  <input type="text" class="form-control input-sm " name="berat" id="berat"  value="<?php echo $list->berat;?>" onkeyup="validAngka(this)" <?php if($disable == "yes") echo 'readonly="readonly"';?>  readonly="readonly" >
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
                  <input type="text" class="form-control input-sm " name="gramasi" id="gramasi"  value="<?php echo $list->gramasi;?>" onkeyup="validAngka(this)" <?php if($disable == "yes") echo 'readonly="readonly"';?>  readonly="readonly" >
                </div>                                    
              </div>
              <div class="col-md-12 col-xs-12">
                <div class="col-xs-4"><label>Program </label></div>
                <div class="col-xs-8">
                  <input type="text" class="form-control input-sm " name="program" id="program"  value="<?php echo $list->program;?>" <?php if($disable == "yes") echo 'readonly="readonly"';?>  readonly="readonly" >
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
                  <input type="text" class="form-control input-sm " name="varian_warna" id="varian_warna"  value="<?php echo $list->nama_varian;?>" <?php if($disable == "yes") echo 'readonly="readonly"';?>  readonly="readonly" >
                </div>                                    
              </div>
              <div class="col-md-12 col-xs-12">
                  <div class="col-xs-4 "> <div class="box-color" style="background-color: <?php echo $list->kode_warna;?>"></div></div>
                  <div class="col-xs-8 col-md-8" id="ta">
                      <textarea class="form-control input-sm" name="notes_varian" id="notes_varian" readonly="readonly"><?php echo $list->notes_varian; ?></textarea>
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
                    <input type='text' class="form-control input-sm" name="start" id="start"  value="<?php echo $list->start_time;?>" disabled/>
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
                    <input type='text' class="form-control input-sm" name="finish" id="finish"  value="<?php echo $list->finish_time;?>" disabled />
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
                  <input type='text' class="form-control input-sm" name="lot_prefix_waste" id="lot_prefix_waste"  readonly="readonly"   value="<?php echo $lot_prefix_waste;?>" />
                </div>                                    
              </div>

              <div class="col-md-12 col-xs-12">
                <div class="col-xs-4"><label>Alasan</label></div>
                <div class="col-xs-8">
                  <input type='text' class="form-control input-sm" name="alasan" id="alasan"  readonly="readonly"   value="<?php echo $list->alasan;?>" />
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
                                  <td style="color:<?php echo $color;?>" align="right"><?php  if(!empty($row->sum_qty) AND $row->status == 'ready')echo number_format($row->sum_qty,2); if($row->status == 'cancel' AND $row->sum_qty_cancel > 0) echo number_format($row->sum_qty_cancel,2); ?></td>
                                  <td><?php if($row->status == 'cancel') echo 'Batal';  else echo $row->status;?></td>
                                  <td><?php echo $row->reff?></td>
                                  <td><?php if($row->type == 'stockable' AND ($row->status == 'ready' or $row->status == 'draft') AND $type_mo['type_mo'] !='colouring' AND $akses_menu > 0 ){?>
                                    <a href="javascript:void(0)" onclick="tambah_quant('<?php echo $row->kode_produk ?>','<?php echo $row->move_id ?>', '<?php echo $row->origin_prod?>')" data-toggle="tooltip" title="Tambah Quant">
                                     <span class="glyphicon  glyphicon-share"></span></a>
                                   <?php }?>
                                  
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
                              <th class="style">Kg(%)</th
                            </tr>
                            <tbody>
                              <?php
                                foreach ($hasil_rm as $row) {
                              ?>
                                <tr class="num">
                                  <td></td>
                                  <td>
                                    <a href="javascript:void(0)" onclick="view_rm_hasil('<?php echo $list->kode; ?>','<?php echo ($row->kode_produk); ?>', '<?php echo htmlentities($row->nama_produk)?>','rm')"><?php echo '['.$row->kode_produk.'] '.$row->nama_produk?></a>
                                  </td>
                                  <td align="right"><?php echo number_format($row->tot_qty,2)?></td>
                                  <td><?php echo $row->uom?></td>
                                  <td align="right"><?php echo number_format($row->tot_qty2,2)?></td>
                                  <td><?php echo $row->uom2?></td>
                                  <td style="white-space: nowrap;"><?php echo ($row->persen_kg)." %"?></td>
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
                                    <td><?php echo $row->persen?></td>
                                    <td align="right"><?php echo number_format($row->qty,2)?></td>
                                    <td><?php echo $row->uom?></td>
                                    <td><?php echo $row->status?></td>
                                    <td><?php echo $row->reff_note?></td>
                                  
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
                                    <td><?php echo $row->persen?></td>
                                    <td align="right"><?php echo number_format($row->qty,2)?></td>
                                    <td><?php echo $row->uom?></td>
                                    <td><?php echo $row->status?></td>
                                    <td><?php echo $row->reff_note?></td>
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

                      <!--Additional--> 
                      <div class="col-md-12">
                        <div class="box box-primary">
                          <div class="box-header with-border">
                            <h3 class="box-title"><b>Additional</b></h3>
                            <?php 
                            if( $akses_menu >0 AND ($type_mo['type_mo'] !='colouring' or $type_mo['type_mo'] !='knitting')AND ($level == 'Super Administrator' or $level == 'Administrator' or $cek_dept == 'PPIC')){
                              if(!empty($menu)){  ?>
                                <div class=" pull-right text-right">
                                  <button class="btn btn-primary btn-sm" id="btn-request-add" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing...">Request Additional</button>
                                </div>
                              <?php 
                              }
                            }?>
                          </div>
                          <div class="box-body" id="tbody_additional">

                            <div  class="row">
                            <?php 
                            if($type_mo['type_mo']=='knitting') {//cek type_mo 
                            ?>
                              <!-- Tabel Kiri -->
                              <div class="col-md-6 table-responsive">
                                <table class="table table-condesed table-hover rlstable" width="100%" id ="table_rm_add">
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
                                  <tbody id="tbody_rm_add">
                                    <?php
                                    if(!empty($rm_add)){
                                      foreach ($rm_add as $row) {
                                        
                                        $sisa        = $row->qty-$row->sum_qty_done;
                                        $qty_rm_sisa = $sisa;
                                        if(round($row->sum_qty,2) > round($sisa,2)){$color = 'red';}elseif(round($row->sum_qty,2) < round($sisa,2)){$color = 'blue';}else{$color = '';}
                                    ?>
                                      <tr class="num">
                                        <td data-content="edit" data-id="row_order"  data-isi="<?php echo $row->row_order; ?>" data-id2="origin_prod"  data-isi2="<?php echo htmlentities($row->origin_prod); ?>"></td>
                                        <td data-content="edit" data-id="kode_produk" data-isi="<?php echo htmlentities($row->kode_produk); ?>" data-id2="nama_produk" data-isi2="<?php echo htmlentities($row->nama_produk); ?>" >
                                          <?php if($row->type == 'stockable'){?>
                                              <a href="javascript:void(0)" onclick="view_quant('<?php echo $list->kode; ?>','<?php echo htmlentities($row->origin_prod); ?>','<?php echo $row->move_id ?>','<?php echo $row->kode_produk?>','<?php echo htmlentities($row->nama_produk)?>')"><?php echo '['.$row->kode_produk.'] '.$row->nama_produk?></a>
                                            <?php }else{
                                                echo '['.$row->kode_produk.'] '.$row->nama_produk;
                                            }?>
                                        </td>
                                        <td data-content="edit" data-id="qty" data-isi="<?php echo $row->qty;?>" align="right"><?php echo number_format($qty_rm_sisa,2)?></td>
                                        <td data-content="edit" data-id="uom" data-isi="<?php echo $row->uom;?>"><?php echo $row->uom?></td>
                                        <td style="color:<?php echo $color;?>" align="right"><?php  if(!empty($row->sum_qty) AND $row->status == 'ready')echo number_format($row->sum_qty,2); if($row->status == 'cancel' AND $row->sum_qty_cancel > 0) echo number_format($row->sum_qty_cancel,2); ?></td>
                                        <td><?php echo $row->status?></td>
                                        <td data-content="edit" data-id="reff" data-isi="<?php echo htmlentities($row->reff_note);?>"><?php echo $row->reff_note?></td>
                                        <td style="text-align: center; width:50px">
                                          <?php if($row->status == 'draft' AND $row->move_id == ''){?>
                                                  <a href="javascript:void(0)" class="add" title="Simpan" data-toggle="tooltip" style="margin-left: 20px;" type_obat="rm"><i class="fa fa-save"></i></a>
                                                  <a href="javascript:void(0)" class="edit" title="Edit" data-toggle="tooltip" style="color: #FFC107;" type_obat="rm"><i class="fa fa-edit"></i></a>
                                                  <a href="javascript:void(0)"  class="delete" title="Hapus" data-toggle="tooltip" ><i class="fa fa-trash" style="color: red;"></i></a>
                                                  <a href="javascript:void(0)" class="cancel" title="Cancel" data-toggle="tooltip" style="margin-left: 20px;" type_obat="rm" ><i class="fa fa-close"></i></a>
                                          <?php }else if($row->type == 'stockable' AND ($row->status == 'ready' or $row->status == 'draft') AND $type_mo['type_mo'] !='colouring' AND $akses_menu > 0 AND $row->move_id != '' ){?>
                                                <a href="javascript:void(0)" onclick="tambah_quant('<?php echo $row->kode_produk ?>','<?php echo $row->move_id ?>', '<?php echo $row->origin_prod?>')" data-toggle="tooltip" title="Tambah Quant">
                                                <span class="glyphicon  glyphicon-share"></span></a>
                                          <?php
                                                }
                                          ?> 
                                        </td>
                                      </tr>
                                    <?php 
                                      }
                                    }
                                    ?>
                                  </tbody>
                                  <?php if(($list->status == 'draft' or $list->status =='ready' ) AND $akses_menu >0 AND ($level == 'Super Administrator' or $level == 'Administrator' or $cek_dept == 'PPIC')){ ?>
                                  <tfoot>
                                    <tr>
                                      <td colspan="6"> 
                                        <a href="javascript:void(0)" class="add-new-rm" onclick="tambah_baris_add(false,'table_rm_add','','','','','')"><i class="fa fa-plus"></i> Tambah Data</a>
                                      </td>
                                    </tr>
                                  </tfoot>
                                  <?php } ?>
                                </table>
                                <div class="example1_processing table_processing" style="display: none">
                                    Processing...
                                </div>
                              </div>
                              <!-- Tabel Kiri -->
                              <?php 
                              }
                              ?>
                                <!-- Tabel Kanan -->
                              <div class="col-md-6 table-responsiv pull-right " >
                                <table class="table table-condesed table-hover rlstable" width="100%" id ="table_rm_hasil">
                                  <label>Sudah dikonsumsi</label>
                                  <tr>
                                    <th class="style no">No.</th>
                                    <th class="style">Product</th>
                                    <th class="style" style="text-align: right;">Qty</th>
                                    <th class="style">uom</th>
                                    <th class="style" style="text-align: right;">Qty2</th>
                                    <th class="style">uom2</th>
                                    <th class="style">Kg(%)</th>
                                  </tr>
                                  <tbody>
                                    <?php
                                      foreach ($hasil_rm_add as $row) {
                                    ?>
                                      <tr class="num">
                                        <td></td>
                                        <td>
                                          <a href="javascript:void(0)" onclick="view_rm_hasil('<?php echo $list->kode; ?>','<?php echo ($row->kode_produk); ?>', '<?php echo htmlentities($row->nama_produk)?>','add')"><?php echo '['.$row->kode_produk.'] '.$row->nama_produk?></a>
                                        </td>
                                        <td align="right"><?php echo number_format($row->tot_qty,2)?></td>
                                        <td><?php echo $row->uom?></td>
                                        <td align="right"><?php echo number_format($row->tot_qty2,2)?></td>
                                        <td><?php echo $row->uom2?></td>
                                        <td style="white-space: nowrap;"><?php echo ($row->persen_kg)." %"?></td>
                                      </tr>
                                    <?php 
                                      }
                                    ?>
                                  </tbody>
                                </table>
                              </div>
                              <!--/.Tabel Kanan -->  
                              </div>
                          

                            <?php 
                            if($type_mo['type_mo']=='colouring') {//cek type_mo 
                            ?>
                              <div  class="row">
                              <!-- Obat >> -->
                              <!-- Tabel Kiri -->
                              <div class="col-md-6 table-responsive">
                                <table class="table table-condesed table-hover rlstable" width="100%" id ="table_dyest_add">
                                  <label>Dyeing Stuff</label>
                                  <tr>
                                    <th class="style no">No.</th>
                                    <th class="style">Product</th>
                                    <th class="style" style="text-align: right;">Qty</th>
                                    <th class="style">uom</th>
                                    <th class="style">Status</th>
                                    <th class="style">reff</th>
                                    <th class="style"></th>
                                  </tr>
                                  <tbody id="tbody_dye">
                                    <?php
                                    if(!empty($dystuff_add)){
                                      foreach ($dystuff_add as $row) {
                                    ?>
                                      <tr class="num">
                                        <td data-content="edit" data-id="row_order"  data-isi="<?php echo $row->row_order; ?>" data-id2="origin_prod"  data-isi2="<?php echo htmlentities($row->origin_prod); ?>"></td>
                                        <td data-content="edit" data-id="kode_produk" data-isi="<?php echo htmlentities($row->kode_produk); ?>" data-id2="nama_produk" data-isi2="<?php echo htmlentities($row->nama_produk); ?>" ><?php echo '['.$row->kode_produk.'] '.$row->nama_produk?></td>
                                        <td data-content="edit" data-id="qty" data-isi="<?php echo $row->qty;?>" align="right"><?php echo number_format($row->qty,2)?></td>
                                        <td data-content="edit" data-id="uom" data-isi="<?php echo $row->uom;?>"><?php echo $row->uom?></td>
                                        <td><?php echo $row->status?></td>
                                        <td data-content="edit" data-id="reff" data-isi="<?php echo htmlentities($row->reff_note);?>"><?php echo $row->reff_note?></td>
                                        <td style="text-align: center; width:50px">
                                          <?php if($row->status == 'draft' AND $row->move_id == ''){?>
                                                  <a href="javascript:void(0)" class="add" title="Simpan" data-toggle="tooltip" style="margin-left: 20px;" type_obat="DYE"><i class="fa fa-save"></i></a>
                                                  <a href="javascript:void(0)" class="edit" title="Edit" data-toggle="tooltip" style="color: #FFC107;" type_obat="DYE"><i class="fa fa-edit"></i></a>
                                                  <a href="javascript:void(0)"  class="delete" title="Hapus" data-toggle="tooltip" ><i class="fa fa-trash" style="color: red;"></i></a>
                                                  <a href="javascript:void(0)" class="cancel" title="Cancel" data-toggle="tooltip" style="margin-left: 20px;" type_obat="DYE" ><i class="fa fa-close"></i></a>
                                          <?php }?> 
                                        </td>
                                      </tr>
                                    <?php 
                                      }
                                    }
                                    ?>
                                  </tbody>
                                  <?php if(($list->status == 'draft' or $list->status =='ready' ) AND $akses_menu >0 AND ($level == 'Super Administrator' or $level == 'Administrator' or $cek_dept == 'PPIC')){ ?>
                                  <tfoot>
                                    <tr>
                                      <td colspan="6"> 
                                        <a href="javascript:void(0)" class="add-new-rm" onclick="tambah_baris_add(false,'table_dyest_add','','','','','')"><i class="fa fa-plus"></i> Tambah Data</a>
                                      </td>
                                    </tr>
                                  </tfoot>
                                  <?php } ?>
                                </table>
                                <div class="example1_processing table_processing" style="display: none">
                                    Processing...
                                </div>
                              </div>
                              <!-- Tabel Kiri -->

                              <!-- Tabel Kanan -->
                              <div class="col-md-6 table-responsive">
                                <table class="table table-condesed table-hover rlstable" width="100%" id ="table_aux_add">
                                  <label>Auxiliary</label>
                                  <tr>
                                    <th class="style no">No.</th>
                                    <th class="style">Product</th>
                                    <th class="style" style="text-align: right;">Qty</th>
                                    <th class="style">uom</th>
                                    <th class="style">Status</th>
                                    <th class="style">reff</th>
                                    <th class="style"></th>
                                  </tr>
                                  <tbody id="tbody_aux">
                                    <?php
                                    if(!empty($aux_add)){
                                      foreach ($aux_add as $row) {
                                    ?>
                                      <tr class="num">
                                        <td data-content="edit" data-id="row_order"  data-isi="<?php echo $row->row_order; ?>" data-id2="origin_prod"  data-isi2="<?php echo htmlentities($row->origin_prod);?>"></td>
                                        <td data-content="edit" data-id="kode_produk" data-isi="<?php echo $row->kode_produk; ?>" data-id2="nama_produk" data-isi2="<?php echo htmlentities($row->nama_produk); ?>"><?php echo '['.$row->kode_produk.'] '.$row->nama_produk?></td>
                                        <td data-content="edit" data-id="qty" data-isi="<?php echo $row->qty;?>" align="right"><?php echo number_format($row->qty,2)?></td>
                                        <td data-content="edit" data-id="uom" data-isi="<?php echo $row->uom;?>"><?php echo $row->uom?></td>
                                        <td><?php echo $row->status?></td>
                                        <td data-content="edit" data-id="reff" data-isi="<?php echo htmlentities($row->reff_note);?>"><?php echo $row->reff_note?></td>
                                        <td style="text-align: center; width:50px">
                                          <?php if($row->status == 'draft' AND $row->move_id == ''){?>
                                                  <a href="javascript:void(0)" class="add" title="Simpan" data-toggle="tooltip" style="margin-left: 20px;" type_obat="AUX"><i class="fa fa-save"></i></a>
                                                  <a href="javascript:void(0)" class="edit" title="Edit" data-toggle="tooltip" style="color: #FFC107;" type_obat="AUX"><i class="fa fa-edit"></i></a>
                                                  <a href="javascript:void(0)" class="delete" title="Hapus" data-toggle="tooltip" ><i class="fa fa-trash" style="color: red;"></i></a>
                                                  <a href="javascript:void(0)" class="cancel" title="Cancel" data-toggle="tooltip" style="margin-left: 20px;" type_obat="AUX"><i class="fa fa-close"></i></a>
                                          <?php }?> 
                                        </td>
                                      </tr>
                                    <?php 
                                      }
                                    }
                                    ?>
                                  </tbody>
                                  <?php if(($list->status == 'draft' or $list->status =='ready') AND $akses_menu >0 AND ($level == 'Super Administrator' or $level == 'Administrator' or $cek_dept == 'PPIC')){ ?>
                                  <tfoot>
                                    <tr>
                                      <td colspan="6"> 
                                        <a href="javascript:void(0)" class="add-new-rm" onclick="tambah_baris_add(false,'table_aux_add','','','','','')"><i class="fa fa-plus"></i> Tambah Data</a>
                                      </td>
                                    </tr>
                                  </tfoot>
                                  <?php } ?>
                                </table>
                                <div  class="example1_processing table_processing" style="display: none">
                                    Processing...
                                </div>
                              </div>
                              <!--/.Tabel Kanan -->
                              <!-- Obat << -->
                              </div>
                            <?php 
                            }
                            ?>

                          </div>
                        </div>
                      </div>
                      <!--/.Additional-->

                  

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
                            <th class="style"></th>
                         </tr>
                          <tbody>
                            <?php
                              foreach ($hasil_fg as $row) {
                                if($row->lot_adj != ''){
                                  $color = "style='color:red';";
                                }else{
                                  $color = "";
                                }
                            ?>
                              <tr class="num" <?php echo $color ?>>
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
                                  <input type="checkbox" class='checkPrint' value="<?php echo $row->quant_id; ?>">
                                </td>
                                <td>
                                  <?php if($akses_menu >0){?>
                                    <a href="javascript:void(0)" onclick="batal_hph(this,'<?php echo $row->quant_id ?>','<?php echo $row->lot ?>')"  title="Hapus Lot/KP" >
                                     <i class=" fa fa-trash" style="color: red"></i>
                                    </a>
                                  <?php } ?>
                                </td>
                              </tr>
                            <?php 
                              }
                            ?>
                          </tbody>
                        </table>
                        <small><b>*Jika terdapat baris yang berwarna <font color="red">MERAH</font> maka Product/Lot tersebut telah di proses ADJUSTMENT !!</b></small>
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

  <style type="text/css">
	.error{
		border:  1px solid red !important;
	}  
  </style>

  <div id="load_modal">
    <!-- Load Partial Modal -->
   <?php $this->load->view("admin/_partials/modal.php") ?>
  </div>

</div>
<!--/. Site wrapper -->
<?php $this->load->view("admin/_partials/js.php") ?>

<!-- add js -->
<script src="<?php echo base_url('dist/js/myscript.js') ?>"></script>

<script type="text/javascript">

  
  // untuk focus after select2 close
  $(document).on('focus', '.select2', function (e) {
    if (e.originalEvent) {
        var s2element = $(this).siblings('select');
        s2element.select2('open');

        // Set focus back to select2 element on closing.
        s2element.on('select2:closing', function (e) {
            s2element.select2('focus');
        });
    }
  });

  //auto height in textarea
  function textAreaAdjust(o) {
    o.style.height = "1px";
    o.style.height = (25+o.scrollHeight)+"px";
  }
  
   // show after refresh close modal
  $(document).on('click','#datetimepicker3',function (e) {
       $('#datetimepicker3').datetimepicker({
             format : 'YYYY-MM-DD HH:mm:ss',
             ignoreReadonly: true
         });     
  });
   
  $(document).on('click','#datetimepicker4',function (e) {
      $('#datetimepicker4').datetimepicker({
            format : 'YYYY-MM-DD HH:mm:ss',
            ignoreReadonly: true
        });
  });
  
  $('#start').inputmask("datetime",{
    mask: "y-2-1 h:s:s", 
    //placeholder: "yyyy-mm-dd hh:mm:ss", 
    placeholder: "yyyy-mm-dd hh:mm:ss",
    leapday: "-02-29", 
    separator: "-", 
    alias: "yyyy/mm/dd"
  });
  
  $('#finish').inputmask("datetime",{
      mask: "y-2-1 h:s:s", 
      //placeholder: "yyyy-mm-dd hh:mm:ss", 
      placeholder: "yyyy-mm-dd hh:mm:ss",
      leapday: "-02-29", 
      separator: "-", 
      alias: "yyyy/mm/dd"
  });

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
    var status = $('#status').val();
   
    if(status != "hold" ){
      $("#btn-produksi").show();
      $("#btn-stok").show();
      $("#btn-hold").show();
      $("#btn-done").show();
      $("#btn-print").show();
      $("#btn-produksi-batch").show();
      $("#btn-waste").show();
      $("#btn-consume").show();
    }else{
      $("#btn-unhold").show();
      $("#btn-edit").hide();
      $("#btn-produksi").hide();
      $("#btn-stok").hide();
      $("#btn-hold").hide();
      $("#btn-done").show();
      $("#btn-print").show();
      $("#btn-produksi-batch").hide();
      $("#btn-waste").hide();
      $("#btn-consume").hide();
    }
    
    $('#mc').attr('disabled', true);
    $("#btn-cancel-edit").attr('id','btn-cancel');
    $("#lebar_jadi_mo").attr("readonly", true);
    $("#lebar_greige_mo").attr("readonly", true);
    $('#uom_lebar_jadi_mo').attr('disabled', true);
    $('#uom_lebar_greige_mo').attr('disabled', true);
    $('#handling').attr('disabled', true);
    $('#start').attr('disabled', true);
    $('#finish').attr('disabled', true);

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
    $("#view_data .modal-dialog ").removeClass('lebar2');
    //replace id btn_request
    $("#tambah_data .modal-dialog .modal-content .modal-footer #btn_request").attr('id',"btn-tambah");
    $("#tambah_data .modal-dialog .modal-content .modal-footer #btn-tambah").text("Simpan");

    $("#tambah_data .modal-dialog .modal-content .modal-body").empty();
    // $("#tambah_data .modal-dialog .modal-content .modal-footer button[id='btn-tambah']).remove();

    readonly_textfield();
    refresh_mo();
  });

  $(document).on('hidden.bs.modal', '.bootbox.modal', function (e) {    
        if($(".modal").hasClass('in')) {
            $('body').addClass('modal-open');
        }
  });

  // ketika modal view data close maka button nya ubah btn tutup saja
  $("#view_data").on('hidden.bs.modal', function(){
    $("#view_data .modal-dialog .modal-content .modal-footer").html('<button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Tutup</button>');
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
        $('#lot_prefix_waste').val('KP/[MY]/[MC]/DEPT/COUNTER');
      }else if(type == 'Proofing'){
        $('#lot_prefix').val('PF/[MY]/[MC]/DEPT/COUNTER');
        $('#lot_prefix_waste').val('PF/[MY]/[MC]/DEPT/COUNTER');
      }else{
        $('#lot_prefix').val('');
        $('#lot_prefix_waste').val('');
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
      // $("#view_data .modal-dialog .modal-content .modal-footer").html('<button type="button" id="btn-waste-data" class="btn btn-primary btn-sm"> Habis Diproduksi</button> <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Tutup</button>');

      $('.modal-title').text('View Quant');
        $.post('<?php echo site_url()?>manufacturing/mO/view_mo_quant_modal',
          {kode:kode, move_id:move_id, deptid:deptid, origin_prod:origin_prod, kode_produk:kode_produk, nama_produk:nama_produk},
          function(html){
            setTimeout(function() {$(".view_body").html(html);  },1000);
          }   
      );
  }

  //untuk tambah details dari tabel stock quant
  function tambah_quant(kode_produk,move_id,origin_prod){   
      var status = $('#status').val();
      if(status == 'done'){
         alert_modal_warning('Maaf, Anda tidak bisa tambah Quant, Proses Produksi telah Selesai ! ');
      }else if(status == 'hold'){
         alert_modal_warning('Maaf, Anda tidak bisa tambah Quant, Proses Produksi di Hold ! ');
      }else{

        $('#btn-tambah').button('reset');
        $("#tambah_data").modal({
            show: true,
            backdrop: 'static'
        })
        $("#tambah_data .modal-dialog .modal-content .modal-body").addClass('tambah_quant');
        $("#tambah_data .modal-dialog .modal-content .modal-footer #btn-tambah").attr('disabled',true);

        $("#tambah_data .modal-dialog .modal-content .modal-footer ").html('');

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

  function view_rm_hasil(kode,kode_produk,nama_produk,tipe){
      $("#view_data").modal({
          show: true,
          backdrop: 'static'
      })
      //var deptid = "<?php echo $list->dept_id; ?>"//parsing data id dept untuk log history
      $(".view_body").html('<center><h5><img src="<?php echo base_url('dist/img/ajax-loader.gif') ?> "/><br>Please Wait...</h5></center>');
      $('.modal-title').text('View Sudah dikonsumsi');
        $.post('<?php echo site_url()?>manufacturing/mO/view_mo_rm_hasil',
          {kode:kode, kode_produk:kode_produk, nama_produk:nama_produk,tipe:tipe},
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
    $("#alasan").attr("readonly", false);
    // $("#lot_prefix_waste").attr("readonly", false);

    $('#type_production').attr('disabled', false).attr('id','type_production');

    var dept_id = "<?php echo $list->dept_id; ?>";
    if(dept_id != 'TRI' && dept_id != 'JAC'){
      $("#lot_prefix").attr("readonly", false);
      $("#lot_prefix_waste").attr("readonly", false);
    }

    $("#btn-simpan").show();//tampilkan btn-simpan
    $("#btn-edit").hide();//sembuyikan btn-edit
    $("#btn-produksi").hide();//sembuyikan btn-produksi
    $("#btn-produksi-batch").hide();//sembuyikan btn-produksi-batch
    $("#btn-waste").hide();//sembuyikan btn-waste
    $("#btn-consume").hide();// sembuyikan btn-consume
    $("#btn-stok").hide();//sembuyikan btn-produksi
    $("#btn-hold").hide();//sembuyikan btn-hide
    $("#btn-done").hide();//sembuyikan btn-done
    $("#btn-print").hide();//sembuyikan btn-print
    $("#btn-cancel").attr('id','btn-cancel-edit');// ubah id btn-cancel jadi btn-cancel-edit
    $('#mc').attr('disabled', false).attr('id', 'mc');
    $("#lebar_jadi_mo").attr("readonly", false);
    $("#lebar_greige_mo").attr("readonly", false);
    $('#uom_lebar_jadi_mo').attr('disabled', false).attr('id','uom_lebar_jadi_mo');
    $('#uom_lebar_greige_mo').attr('disabled', false).attr('id','uom_lebar_greige_mo');
    $('#handling').attr('disabled', false).attr('id','handling');
    $('#start').attr('disabled', false).attr('id','start');;
    $('#finish').attr('disabled', false).attr('id','finish');;

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
          {txtProduct      : $('#kode_produk').val() },
          function(html){
            setTimeout(function() {$(".view_body").html(html);  },1000);
          }   
       );
  });

  // modal view detail product
  $(".view-bom").unbind( "click" );
  $(document).on('click','.view-bom',function(e){
      kode_bom  = "<?php echo $list->kode_bom;?>";
      e.preventDefault();
      $("#view_data").modal({
          show: true,
          backdrop: 'static'
      });
      $(".view_body").html('<center><h5><img src="<?php echo base_url('dist/img/ajax-loader.gif') ?> "/><br>Please Wait...</h5></center>');
      $('.modal-title').text('View Data BOM');
        $.post('<?php echo site_url()?>manufacturing/mO/get_bom',
          {kode : kode_bom },
          function(html){
            setTimeout(function() {$(".view_body").html(html);  },1000);
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
    var arrStr = encodeURIComponent(JSON.stringify(checkboxBarcode));

    if(countchek == 0){
      alert_modal_warning('Silahkan Pilih Product yang akan di Print !');
    }else{
      var url = '<?php echo base_url() ?>manufacturing/mO/print_barcode';
      window.open(url+'?kode='+ kode+'&&dept_id='+ dept_id+'&&checkboxBarcode='+ arrStr,'_blank');
    }

  });


  //validasi input angka
  function validAngka(a){
    if(!/^[0-9.]+$/.test(a.value)){
      a.value = a.value.substring(0,a.value.length-1000);
    }    
  }

  //html entities javascript
  function htmlentities_script(str) {
    return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
  }

  /*
  //highlight form
  function highlight(a){
    $(a).css("border","1px solid red");
    validAngka(a);
  }
  */

  //open modal rekam cacat lot
  function rekam_cacat(deptid,quant_id,lot){ 
    var kode   = $("#kode").val();
    var status = "<?php echo $list->status;?>";
    $("#tambah_data").modal({
      show: true,
      backdrop: 'static',    
    });
    $("#tambah_data .modal-dialog .modal-content .modal-footer ").html('');
    $(".tambah_data").html('<center><h5><img src="<?php echo base_url('dist/img/ajax-loader.gif') ?> "/><br>Please Wait...</h5></center>');
    $('.modal-title').text('Rekam Cacat Lot');
    $.post('<?php echo site_url()?>manufacturing/mO/rekam_cacat_modal',
          {deptid : deptid, lot : lot, quant_id : quant_id, kode : kode, status : status },
          function(html){
            setTimeout(function() { $(".tambah_data").html(html); },1000);
          }   
    );
  }

  function batal_hph(btn,quant_id,lot){ 

      var kode   = $("#kode").val();
      var dept_id = "<?php echo $list->dept_id;?>";
      var status = $('#status').val();
      
      if(status == 'done'){
        alert_modal_warning('Maaf, Proses Produksi telah Selesai !');
      }else if(status == 'cancel'){
        alert_modal_warning('Maaf, Proses Produksi telah dibatalkan !');
      }else{

        bootbox.confirm({
          message: "Apa anda yakin ingin menghapus KP/Lot <b>"+lot+" </b> ini ?",
          title: "<i class='glyphicon glyphicon-trash' style='color: red'></i> Delete !",
          buttons: {
            confirm: {
              label: 'Yes',
              className: 'btn-primary btn-sm'
            },
            cancel: {
              label: 'No',
              className: 'btn-default btn-sm'
            },
          },
          callback: function (result) {
            if(result == true){
              var btn_load = $(btn);
              btn_load.button('loading');
              please_wait(function(){});
              $.ajax({
                  type: "POST",
                  url :'<?php echo base_url('manufacturing/mO/batal_hph')?>',
                  dataType: 'JSON',
                  data    : {kode:kode, quant_id:quant_id, deptid:dept_id, lot:lot},
                  success: function(data){
                    if(data.sesi=='habis'){
                      //alert jika session habis
                      alert_modal_warning(data.message);
                      window.location.replace('../index');
                      unblockUI( function(){});
                      btn_load.button('reset');

                    }else if(data.status == 'failed'){
                      alert_modal_warning(data.message);
                      unblockUI( function(){});
                      btn_load.button('reset');
                    }else{
                      $("#tab_2").load(location.href + " #tab_2");
                      $("#status_bar").load(location.href + " #status_bar");
                      $("#foot").load(location.href + " #foot");
                      btn_load.button('reset');
                      unblockUI( function(){
                        setTimeout(function() { alert_notify(data.icon,data.message,data.type,function(){});}, 1000);
                      });
                    
                    }

                  },error: function (xhr, ajaxOptions, thrownError) {
                    alert(xhr.responseText);
                    unblockUI( function(){});
                    btn_load.button('reset');
                  }
              });

            }
          }
        });
        
      }
  }

  $(document).on('click','#btn-hold',function(e){

      var kode    = $("#kode").val();
      var dept_id = "<?php echo $list->dept_id;?>";
      var status  = $('#status').val();

      if(status == 'done'){
        alert_modal_warning('Maaf, Proses Produksi telah Selesai !');
      }else if(status == 'cancel'){
        alert_modal_warning('Maaf, Proses Produksi telah dibatalkan !');
      }else{
        bootbox.prompt({
          message: "Apa anda yakin ingin menunda/Hold MO ini ?",
          title: "<font color='red'><i class='fa fa-warning'></i></font> Hold !",
          placeholder:" Alasan MO di Hold ",
          buttons: {
            confirm: {
              label: 'Yes',
              className: 'btn-primary btn-sm'
            },
            cancel: {
              label: 'No',
              className: 'btn-default btn-sm'
            },
          },
          callback: function (result) {
            if(result !== null){
              // alert('Masuk = '+result)
              $('#btn-hold').button('loading');
              alasan = result;
              $.ajax({
                    type: "POST",
                    url :'<?php echo base_url('manufacturing/mO/hold_mo')?>',
                    dataType: 'JSON',
                    data    : {kode:kode, deptid:dept_id, alasan:alasan},
                    success: function(data){
                      if(data.sesi=='habis'){
                        //alert jika session habis
                        alert_modal_warning(data.message);
                        window.location.replace('../index');
                        unblockUI( function(){});
                        $('#btn-hold').button('reset');

                      }else if(data.status == 'failed'){
                        alert_modal_warning(data.message);
                        unblockUI( function(){});
                        $('#btn-hold').button('reset');
                        refresh_mo();
                      }else{
                        // $("#tab_2").load(location.href + " #tab_2");
                        // $("#status_bar").load(location.href + " #status_bar");
                        // $("#foot").load(location.href + " #foot");
                        $('#btn-hold').button('reset');
                        unblockUI( function(){
                          setTimeout(function() { alert_notify(data.icon,data.message,data.type,function(){});}, 1000);
                        });
                        refresh_mo();
                        readonly_textfield();//refresh form/btn
                        $("#mo").load(location.href + " #mo>*");
                        $("#btn-edit").hide();
                        $("#btn-stok").hide();
                        $("#btn-produksi").hide();
                        $("#btn-produksi-batch").hide();
                        $("#btn-waste").hide();
                        $("#btn-consume").hide();
                        $("#btn-unhold").show();
                        $("#btn-hold").hide();
                      }

                    },error: function (xhr, ajaxOptions, thrownError) {
                      alert(xhr.responseText);
                      unblockUI( function(){});
                      $('#btn-hold').button('reset');
                    }
                });
           
            }
          }
        });
      }

  });

  $(document).on('click','#btn-unhold',function(e){

    var kode    = $("#kode").val();
    var dept_id = "<?php echo $list->dept_id;?>";
    var status  = $('#status').val();

    if(status == 'done'){
      alert_modal_warning('Maaf, Proses Produksi telah Selesai !');
    }else if(status == 'cancel'){
      alert_modal_warning('Maaf, Proses Produksi telah dibatalkan !');
    }else{
      bootbox.confirm({
        message: "Apa anda yakin ingin melanjutkan MO ini ?",
        title: "<font color='red'><i class='fa fa-warning'></i></font> unHold !",
        buttons: {
          confirm: {
            label: 'Yes',
            className: 'btn-primary btn-sm'
          },
          cancel: {
            label: 'No',
            className: 'btn-default btn-sm'
          },
        },
        callback: function (result) {
          if(result !== null){
            $('#btn-unhold').button('loading');
            alasan = result;
            $.ajax({
                  type: "POST",
                  url :'<?php echo base_url('manufacturing/mO/unhold_mo')?>',
                  dataType: 'JSON',
                  data    : {kode:kode, deptid:dept_id},
                  success: function(data){
                    if(data.sesi=='habis'){
                      //alert jika session habis
                      alert_modal_warning(data.message);
                      window.location.replace('../index');
                      unblockUI( function(){});
                      $('#btn-unhold').button('reset');

                    }else if(data.status == 'failed'){
                      alert_modal_warning(data.message);
                      unblockUI( function(){});
                      $('#btn-unhold').button('reset');
                      refresh_mo();
                      readonly_textfield();//refresh form/btn
                    }else{
                      $('#btn-unhold').button('reset');
                      $('#btn-unhold').hide();
                      unblockUI( function(){
                        setTimeout(function() { alert_notify(data.icon,data.message,data.type,function(){});}, 1000);
                      });
                      refresh_mo();
                      readonly_textfield();//refresh form/btn   
                      $("#mo").load(location.href + " #mo>*");
                      $("#btn-edit").show();
                      $("#btn-stok").show();
                      $("#btn-produksi").show();
                      $("#btn-produksi-batch").show();
                      $("#btn-waste").show();
                      $("#btn-consume").show();
                      $("#btn-unhold").hide();
                      $("#btn-hold").show();
                      $("#btn-done").show();
                      $("#btn-print").show();
   
                    }

                  },error: function (xhr, ajaxOptions, thrownError) {
                    alert(xhr.responseText);
                    unblockUI( function(){});
                    $('#btn-unhold').button('reset');
                  }
              });
          
          }
        }
      });
    }

  });

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
      //var move_id = '<?php echo $move_id_rm['move_id'];?>';
      var move_id_fg = '<?php echo $move_id_fg['move_id'];?>';
      var qty  = '<?php echo $list->qty?>';
      $("#tambah_data").modal({
          show: true,
          backdrop: 'static'
      });
      $("#tambah_data .modal-dialog .modal-content .modal-body").addClass('produksi_rm_batch');
      $("#tambah_data .modal-dialog .modal-content .modal-footer #btn-tambah").attr('disabled',true);
      $("#tambah_data .modal-dialog .modal-content .modal-footer ").html('');

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
          //move_id     : move_id, 
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
      //var move_id = '<?php echo $move_id_rm['move_id'];?>';
      var move_id_fg = '<?php echo $move_id_fg['move_id'];?>';
      var qty  = '<?php echo $list->qty?>';
      $("#tambah_data").modal({
          show: true,
          backdrop: 'static'
      });
      $("#tambah_data .modal-dialog .modal-content .modal-body").addClass('produksi_rm');
      $("#tambah_data .modal-dialog .modal-content .modal-footer #btn-tambah").attr('disabled',true);
      $("#tambah_data .modal-dialog .modal-content .modal-footer ").html('');
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

  $("#btn-waste").unbind( "click" );
  $(document).on('click','#btn-waste',function(e){

    var status = $('#status').val();
    if(status == 'done'){
      alert_modal_warning('Maaf, Proses Produksi telah Selesai !');
    }else if(status == 'cancel'){
      alert_modal_warning('Maaf, Proses Produksi telah dibatalkan !');
    }else if(status == 'draft'){
      alert_modal_warning('Maaf, Product belum ready !');
    }else{
      e.preventDefault();
      $('.modal-title').text('Waste');

      $('#btn-tambah').button('reset');
     
      var kode       = $("#kode").val();
      var deptid     = "<?php echo $list->dept_id; ?>"//parsing data id dept untuk log history
      var move_id_fg = '<?php echo $move_id_fg['move_id'];?>';
      var qty        = '<?php echo $list->qty?>';
      $("#tambah_data").modal({
          show: true,
          backdrop: 'static'
      });
      $("#tambah_data .modal-dialog .modal-content .modal-body").addClass('waste_produksi');
      $("#tambah_data .modal-dialog .modal-content .modal-footer #btn-tambah").attr('disabled',true);
      $("#tambah_data .modal-dialog .modal-content .modal-footer ").html('');
      $("#btn-produksi").prop('disabled',true);

      $(".waste_produksi").html('<center><h5><img src="<?php echo base_url('dist/img/ajax-loader.gif') ?> "/><br>Please Wait...</h5></center>');
      $.post('<?php echo site_url()?>manufacturing/mO/produksi_waste',
        { kode        : $('#kode').val(),
          kode_produk : $('#kode_produk').val(), 
          nama_produk : $('#product').val(),
          sisa_qty    : $('#total_sisa').val(), 
          uom_qty_sisa    : $('#uom_qty_sisa').val(), 
          deptid      : deptid, 
          kode        : kode,
          //move_id     : move_id, 
          move_id_fg  : move_id_fg, 
          qty         : $('#qty_prod').val(),  
          origin      : $('#origin').val(),
          // qty1_std    : $('#qty1_std').val(),
          // qty2_std    : $('#qty2_std').val(),
          // lot_prefix  : $('#lot_prefix').val(),
          lot_prefix_waste  : $('#lot_prefix_waste').val(),       
        } 
      ).done(function(html){
        setTimeout(function() {
          $(".waste_produksi").html(html)  
        },1000);
        $("#tambah_data .modal-dialog .modal-content .modal-footer #btn-tambah").attr('disabled',false);
      });
    }
  });


  $("#btn-consume").unbind( "click" );
  $(document).on('click','#btn-consume',function(e){

    var status = $('#status').val();
    if(status == 'done'){
      alert_modal_warning('Maaf, Proses Produksi telah Selesai !');
    }else if(status == 'cancel'){
      alert_modal_warning('Maaf, Proses Produksi telah dibatalkan !');
    }else if(status == 'draft'){
      alert_modal_warning('Maaf, Product belum ready !');
    }else{
      e.preventDefault();
      $('.modal-title').text('Consume');

      $('#btn-tambah').button('reset');
     
      var kode       = $("#kode").val();
      var deptid     = "<?php echo $list->dept_id; ?>"//parsing data id dept untuk log history
      var move_id_fg = "<?php echo $move_id_fg['move_id'];?>";
      var qty        = "<?php echo $list->qty?>";
      $("#tambah_data").modal({
          show: true,
          backdrop: 'static'
      });
      $("#tambah_data .modal-dialog .modal-content .modal-body").addClass('consume');
      $("#tambah_data .modal-dialog .modal-content .modal-footer #btn-tambah").attr('disabled',true);
      $("#tambah_data .modal-dialog .modal-content .modal-footer ").html('');

      $("#btn-produksi").prop('disabled',true);
      $("#btn-produksi-batch").prop('disabled',true);

      $(".consume").html('<center><h5><img src="<?php echo base_url('dist/img/ajax-loader.gif') ?> "/><br>Please Wait...</h5></center>');
      $.post('<?php echo site_url()?>manufacturing/mO/consume_mo',
        { kode        : $('#kode').val(),
          kode_produk : $('#kode_produk').val(), 
          nama_produk : $('#product').val(),
          sisa_qty    : $('#total_sisa').val(), 
          uom_qty_sisa    : $('#uom_qty_sisa').val(), 
          deptid      : deptid, 
          kode        : kode,
          move_id_fg  : move_id_fg, 
          qty         : $('#qty_prod').val(),  
          origin      : $('#origin').val(),
          lot_prefix_waste  : $('#lot_prefix_waste').val(),       
        } 
      ).done(function(html){
        setTimeout(function() {
          $(".consume").html(html)  
        },1000);
        $("#tambah_data .modal-dialog .modal-content .modal-footer #btn-tambah").attr('disabled',false);
      });
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
      $("#tambah_data .modal-dialog .modal-content .modal-footer ").html('');
      
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
    //var move_id = '<?php echo $move_id_rm['move_id'];?>';
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
         data: {kode : $('#kode').val(), deptid : deptid, origin : $('#origin').val(), lokasi : lokasi, type_mo:type_mo 
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
                alasan      : $('#alasan').val(),

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
              $("#mo").load(location.href + " #mo>*");
            }

          },error: function (xhr, ajaxOptions, thrownError) { 
            alert(xhr.responseText);
            setTimeout($.unblockUI, 1000); 
            unblockUI( function(){});
            $('#btn-simpan').button('reset');
          }
      });
    });


   //modal request resep
   $('#btn-done').click(function(){

      var status = $('#status').val();
      // if(status == 'done'){
      //   alert_modal_warning('Maaf, Proses Produksi telah Selesai !');
      // }else if(status == 'cancel'){
      //   alert_modal_warning('Maaf, Proses Produksi telah dibatalkan !');
      
      // }else if(status == 'draft'){
      //   alert_modal_warning('Maaf, Product belum ready !');
     
      // }else{

        $("#view_data").modal({
            show: true,
            backdrop: 'static'
        })
        var deptid = "<?php echo $list->dept_id; ?>"

        // $("#view_data .modal-dialog ").addClass('lebar2');

        $(".view_body").html('<center><h5><img src="<?php echo base_url('dist/img/ajax-loader.gif') ?> "/><br>Please Wait...</h5></center>');
        $('.modal-title').text('Warning !!');
        $.post('<?php echo site_url()?>manufacturing/mO/mo_done_modal',
              {kode:$('#kode').val(), deptid:deptid},
        ).done(function(html){
            setTimeout(function() {
                $(".view_body").html(html);  
              },1000);
         });

      // }

    });



  //## StartAdditional >> 

  // tambah baris
  function tambah_baris_add(data,table,kode_produk,nama_produk,qty,uom,reff_note){

    var tambah = true;
    $(".add-new-rm").hide();
    if(table == 'table_dyest_add'){
      var index  = $("#table_dyest_add tbody[id='tbody_dye'] tr:last-child").index();
      if(index== -1){
        row = 0;
      }else{
        row  = parseInt($("#table_dyest_add tbody[id='tbody_dye'] tr:last-child td .row").val());
      }

      tbody_id        = 'tbody_dye';
      link_get_list   = "lab/dti/get_list_dye";
      link_get_prod   = "lab/dti/get_prod_by_id";
      type_obat       = 'DYE';

      tbl  = "#table_dyest_add tbody[id='tbody_dye'] ";

    }else if(table == 'table_aux_add'){
      var index  = $("#table_aux_add tbody[id='tbody_aux'] tr:last-child").index();
      if(index== -1){
        row = 0;
      }else{
        row  = parseInt($("#table_aux_add tbody[id='tbody_aux'] tr:last-child td .row").val());
      }
      tbody_id        = 'tbody_aux';
      link_get_list   = "lab/dti/get_list_aux";
      link_get_prod   = "lab/dti/get_prod_by_id";
      type_obat       = 'AUX'

      tbl  = "#table_aux_add tbody[id='tbody_aux'] ";
    }else{
      var index  = $("#table_rm_add tbody[id='tbody_rm_add'] tr:last-child").index();
      if(index== -1){
        row = 0;
      }else{
        row  = parseInt($("#table_rm_add tbody[id='tbody_rm_add'] tr:last-child td .row").val());
      }
      tbody_id        = 'tbody_rm_add';
      link_get_list   = "manufacturing/mO/get_list_produk_rm_select2";
      link_get_prod   = "manufacturing/mO/get_produk_rm_by_id";
      type_obat       = 'rm'

      tbl  = "#table_rm_add tbody[id='tbody_rm_add'] ";
      
    }

    if(tambah){

        var ro           = row+1;
        var class_produk = 'kode_produk_'+ro;
        var produk       = 'nama_produk'+ro;
        var class_uom    = 'uom_'+ro;
        var row        = '<tr class="num">'
                    + '<td><input type="hidden"  name="row" class="row" value="'+ro+'"></td>'
                    + '<td  class="min-width-200">'
                        + '<select add="manual" type="text" class="form-control input-sm kode_produk '+class_produk+'" name="Product" id="kode_produk"></select>'
                        + '<input type="hidden" class="form-control input-sm nama_produk '+produk+'" name="nama_produk" id="nama_produk" value="'+nama_produk+'"></td>'
                    + '<td class="min-width-100"><input type="text" class="form-control input-sm qty" name="Qty" id="qty"  onkeyup="validAngka(this)"   value="'+qty+'"></td>'
                    + '<td class="min-width-100"><select type="text" class="form-control input-sm uom '+class_uom+'" name="Uom" id="uom"></select></td>'
                    + '<td></td>'
                    + '<td class="min-width-100"><textarea type="text" class="form-control input-sm" name="note" id="reff" >'+reff_note+'</textarea></td>'
                    + '<td class="width-50" align="center">'
                        + '<button type="button" class="btn btn-primary btn-xs add width-btn" title="Simpan" data-toggle="tooltip" type_obat="'+type_obat+'">Simpan</button>'
                        + '<a class="edit" title="Edit" data-toggle="tooltip"><i class="fa fa-edit"></i></a>'
                        + '<button type="button" class="btn btn-danger btn-xs batal width-btn" title="Batal" data-toggle="tooltip">Batal</button></td>'
                    + '</td>'
                    + '</tr>';

        $('#'+table+' tbody[id="'+tbody_id+'"] ').append(row);
        $('#'+table+' tbody[id="'+tbody_id+'"] tr ').eq(index+1).find(".add, .edit").toggle();
        $('[data-toggle="tooltip"]').tooltip();

        //n_qty[inx_n_qty+1].focus();
        
        var sel_produk = $('#'+table+' tbody[id="'+tbody_id+'"] tr .'+class_produk);
        var sel_uom    = $('#'+table+' tbody[id="'+tbody_id+'"] tr .'+class_uom);
        var produk_hide= $('#'+table+' tbody[id="'+tbody_id+'"] tr .'+produk);

        if(data==true){
            //untuk event selected select2 nama_produk
            custom_nama = '['+kode_produk+'] '+nama_produk;
            var $newOption = $("<option></option>").val(kode_produk).text(custom_nama);
            sel_produk.empty().append($newOption).trigger('change');

            var $newOption2 = $("<option></option>").val(uom).text(uom);
            sel_uom.empty().append($newOption2).trigger('change');

        }

        //select 2 product
        sel_produk.select2({
            ajax:{
                dataType: 'JSON',
                type : "POST",
                url : "<?php echo base_url();?>"+link_get_list,
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
                            text:'['+item.kode_produk+'] '+item.nama_produk
                        });
                    });
                    return {
                    results:results
                    };
                },
                error: function (xhr, ajaxOptions, thrownError){
                    console.log(xhr.responseText);
                }
            }
        });

        //jika nama produk diubah
        sel_produk.change(function(){
            
            $.ajax({
                dataType: "JSON",
                url : "<?php echo base_url();?>"+link_get_prod,
                type: "POST",
                data: {kode_produk: $(this).parents("tr").find("#kode_produk").val() },
                success: function(data){
                    produk_hide.val(data.nama_produk);
                    //$('#qty').val(data.qty);
                    //untuk event selected select2 uom
                    var $newOptionuom = $("<option></option>").val(data.uom).text(data.uom);
                    sel_uom.empty().append($newOptionuom).trigger('change');
                },
                error: function (xhr, ajaxOptions, thrownError){
                    console.log(xhr.responseText);
                }
            });
        });

        
        //select 2 uom
        sel_uom.select2({
            allowClear: true,
            placeholder: "",
            ajax:{
                    dataType: 'JSON',
                    type : "POST",
                    url : "<?php echo base_url();?>lab/dti/get_uom_select2",
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
                        //alert('Error data');
                        console.log(xhr.responseText);
                    }
            }
        });

    }

  }


    // hapus row
  $(document).on("click", ".batal", function(){
    $(".add-new-rm").show();
    $("#foot").load(location.href + " #foot");
    $("#status_head").load(location.href + " #status_head");
    $("#tbody_additional").load(location.href + " #tbody_additional");

  }); 

  $(document).on("click", ".cancel", function(){
    $(".add-new-rm").show();
    $("#foot").load(location.href + " #foot");
    $("#status_head").load(location.href + " #status_head");
    $("#tbody_additional").load(location.href + " #tbody_additional");
  });    


  $(document).on('click','.add',function(e){
    e.preventDefault();

    var empty = false;
    var input = $(this).parents("tr").find('input[type="text"]');

    var empty2 = false;
    var select = $(this).parents("tr").find('select[name="Product"]');

    var empty3 = false;
    var select2 = $(this).parents("tr").find('select[name="Uom"]');

    //validasi product tidak boleh kosong
    select.each(function(index,value){
      if(!$(this).val() && $(this).attr('name')=='Product' ){
        alert_notify('fa fa-warning',$(this).attr('name')+ ' Harus Diisi !','danger',function(){});
        $(this).parents('td').find('span span.selection span.select2-selection').addClass('error'); 
        empty2 = true;
      }else{
        $(this).parents('td').find('span span.selection span.select2-selection').removeClass('error'); 
      }
    });

    //validasi qty tidak boleh kosong
    select2.each(function(index,value){
      if(!$(this).val() && $(this).attr('name')=='Uom' ){
        alert_notify('fa fa-warning',$(this).attr('name')+ ' Harus Diisi !','danger',function(){});
        $(this).parents('td').find('span span.selection span.select2-selection').addClass('error'); 
        empty3 = true;
      }else{
        $(this).parents('td').find('span span.selection span.select2-selection').removeClass('error'); 
      }
    });


    // validasi untuk inputan textbox
    input.each(function(){
      if(!$(this).val() && $(this).attr('name')!='reff'){
        alert_notify('fa fa-warning',$(this).attr('name')+ ' Harus Diisi !','danger',function(){});
        empty = true;
        $(this).addClass('error'); 
      }else{
        $(this).removeClass('error'); 
      }
    });

    if(!empty && !empty2 && !empty3){
      var btn_loading   = $(this);
      btn_loading.button('loading');
      var type_obat     = $(this).attr('type_obat');
      var kode_produk   = $(this).parents("tr").find("#kode_produk").val();
      var produk        = $(this).parents("tr").find("#nama_produk").val();
      var qty           = $(this).parents("tr").find("#qty").val();
      var uom           = $(this).parents("tr").find("#uom").val();
      var reff          = $(this).parents("tr").find("#reff").val();
      var row_order     = $(this).parents("tr").find("#row_order").val();
      var origin_prod   = $(this).parents("tr").find("#origin_prod").val();
      $.ajax({
        dataType: "JSON",
        url :'<?php echo base_url('manufacturing/mO/simpan_rm_additional')?>',
        type: "POST",
        data: {kode       : $('#kode').val(),
              kode_produk : kode_produk,
              produk      : produk,
              qty         : qty,
              uom         : uom,
              reff        : reff,
              type_obat   : type_obat,
              origin_prod   : origin_prod,
              row_order   : row_order, },
        beforeSend: function(e) {
            $(".example1_processing").css('display','block');
        },
        success: function(data){
          if(data.sesi=='habis'){
              //alert jika session habis
              alert_modal_warning(data.message);
              window.location.replace('../index');
          }else if(data.status == 'failed2'){
              alert_modal_warning(data.message);
          }else if(data.status == 'failed'){
              alert_notify(data.icon,data.message,data.type,function(){});
              btn_loading.button('loading');
          }else{
              alert_notify(data.icon,data.message,data.type,function(){});
              btn_loading.button('loading');
          }
          $(".add-new-rm").show();                   
          $("#foot").load(location.href + " #foot");
          $("#status_head").load(location.href + " #status_head");
          $("#tbody_additional").load(location.href + " #tbody_additional");
          $(".example1_processing").css('display','none'); 
        },
        error: function (xhr, ajaxOptions, thrownError){
          btn_loading.button('loading');
          $(".example1_processing").css('display','none'); 
          alert('Error Simpan Additional');
          alert(xhr.responseText);
        }
      });
    } 
  });

    $(document).on("click", ".edit", function(){  

      var type = $(this).attr('type_obat');
      if(type =='DYE'){
        id_table = "#table_dyest_add";
        link_get_list   = "lab/dti/get_list_dye";
        link_get_data   = "lab/dti/get_data_dye";
        table           = "dye";
        tbody_id        = 'tbody_dye';
      }else if(type =='AUX'){
        id_table = "#table_aux_add";
        link_get_list   = "lab/dti/get_list_aux";
        link_get_data   = "lab/dti/get_data_aux";
        tbody_id        = 'tbody_aux';
        table           = "aux";
      }else{
        id_table        = "#table_rm_add";
        link_get_list   = "manufacturing/mO/get_list_produk_rm_select2";
        link_get_data   = "manufacturing/mO/get_produk_rm_by_id";
        tbody_id        = 'tbody_rm_add';
        table           = "rm";
      }

      $(this).parents("tr").find("td[data-content='edit']").each(function(){
        if($(this).attr('data-id')=="row_order"){
          $(this).html('<input type="hidden"  class="form-control" value="' + htmlentities_script($(this).attr('data-isi')) + '" id="'+ $(this).attr('data-id') +'"> <input type="hidden" class="form_control"  value="' + htmlentities_script($(this).attr('data-isi2')) + '" id="'+ $(this).attr('data-id2')+'"> ');
          row_order = $(this).attr('data-isi');

        }else if($(this).attr('data-id')=="kode_produk"){
          
          var kode_produk = $(this).attr('data-isi');
          var nama_produk = $(this).attr('data-isi2');

          class_sel2_prod   = table+'sel2_prod'+row_order;
          class_nama_produk = table+'nama_produk'+row_order;

          $(this).html('<select type="text"  class="form-control input-sm '+class_sel2_prod+' " id="kode_produk" name="Product" style="min-width:100px !important"></select> ' + '<input type="hidden"  class="form-control '+class_nama_produk+' " value="' + htmlentities_script($(this).attr('data-isi2')) + '" id="'+ $(this).attr('data-id2') +'"> ');

          custom_nama = '['+kode_produk+'] '+nama_produk;
          $newOption = new Option(custom_nama, kode_produk, true, true);
          $('.'+class_sel2_prod).append($newOption).trigger('change');

          $('.'+class_sel2_prod).select2({
            ajax:{
                  dataType: 'JSON',
                  type : "POST",
                  url : "<?php echo base_url();?>"+link_get_list,
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
                            text:'['+item.kode_produk+'] '+item.nama_produk
                        });
                    });
                    return {
                      results:results
                    };
                  },
                  error: function (xhr, ajaxOptions, thrownError){
                    //alert('Error data');
                    //alert(xhr.responseText);
                  }
            }
          });

          $('.'+class_sel2_prod).change(function(){
              var this1 = $(this);
              $.ajax({
                    dataType: "JSON",
                    url : "<?php echo base_url();?>"+link_get_data,
                    type: "POST",
                    data: {kode_produk: $(this).parents("tr").find("#kode_produk").val() },
                    success: function(data){
                      this1.parents('tr').find("td #nama_produk").val(data.nama_produk);
                      this1.parents('tr').find("td #uom").val(data.uom);
                      //$(this).parent('tr').find('tr td .'+class_nama_produk).val(data.nama_produk);
                      //$(id_table+' tbody[id="'+tbody_id+'"] tr .uom'+row_order).val(data.uom);
                    },
                    error: function (xhr, ajaxOptions, thrownError){
                      alert('Error data');
                      alert(xhr.responseText);
                    }
              });
          });
                
        }else if($(this).attr('data-id')=='qty'){
          $(this).html('<input type="text"  class="form-control input-sm min-width-80" value="'+ ($(this).attr('data-isi')) +'" id="'+ $(this).attr('data-id') +'" name="'+ $(this).attr('data-id') +'" onkeyup="validAngka(this)"> ');
        }else if($(this).attr('data-id')=='uom'){

          var value_option  = $(this).attr('data-isi');
          var uom           = $(this).attr('data-id')+row_order;

          $(this).html('<select type="text" class="form-control input-sm  min-width-80 '+uom+'"  id="'+ $(this).attr('data-id') +'" name="'+ $(this).attr('data-id') +'"></select>');

          var $option = $("<option selected></option>").val(value_option).text(value_option);
          $(id_table+' tbody[id="'+tbody_id+'"] tr .uom'+row_order).append($option).trigger('change');

          $(id_table+' tbody[id="'+tbody_id+'"] tr .uom'+row_order).append('<?php foreach($uom as $row){?><option value="<?php echo $row->short; ?>"><?php echo $row->short;?></option>"<?php }?>').trigger('change');

        }else if($(this).attr('data-id')=="reff"){
          $(this).html('<textarea type="text" onkeyup="textAreaAdjust(this)" class="form-control min-width-80 input-sm" id="'+ $(this).attr('data-id') +'" name="'+ $(this).attr('data-id') +'">'+ htmlentities_script($(this).attr('data-isi')) +'</textarea>');
        }

      });  

      $(this).parents("tr").find(".add, .edit").toggle();
      $(this).parents("tr").find(".cancel, .delete").toggle();
      $(".add-new-rm").hide();

    });

    // hapus rm 
    $(document).on("click", ".delete", function(){ 
      $(this).parents("tr").find("td[data-content='edit']").each(function(){
        if($(this).attr('data-id')=="row_order"){
          $(this).html('<input type="hidden" class="form-control" value="' + htmlentities_script($(this).attr('data-isi')) + '" id="'+ $(this).attr('data-id')+'"> <input type="hidden" class="form_control"  value="' + htmlentities_script($(this).attr('data-isi2')) + '" id="'+ $(this).attr('data-id2')+'"> ');
        }
      });
      var icon_loading= $(this);
      var kode        =  $("#kode").val();
      var origin_prod =  $("#origin_prod").val();
      var row_order   = $(this).parents("tr").find("#row_order").val();  
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
                      url : '<?php echo site_url('manufacturing/mO/hapus_rm') ?>',
                      type: "POST",
                      beforeSend: function(e) {
                        icon_loading.button('loading');
                      },
                      data: {kode : kode, 
                            origin_prod :origin_prod,
                            row_order : row_order  },
                      success: function(data){
                        if(data.sesi=='habis'){
                            //alert jika session habis
                            alert_modal_warning(data.message);
                            window.location.replace('../index');
                        }else if(data.status == 'failed'){
                            alert_notify(data.icon,data.message,data.type,function(){});
                        }else{
                            alert_notify(data.icon,data.message,data.type,function(){});
                        }

                        $("#foot").load(location.href + " #foot");
                        $("#status_head").load(location.href + " #status_head");
                        $("#tbody_additional").load(location.href + " #tbody_additional");
                        $(".example1_processing").css('display','none'); 
                        icon_loading.button('reset');

                      },
                      error: function (xhr, ajaxOptions, thrownError){
                        alert('Error data');
                        alert(xhr.responseText);
                        icon_loading.button('reset');
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
    });

    //request additional
    $(document).on('click',"#btn-request-add",function(e){

      var status = $('#status').val();
      if(status == 'done'){
        alert_modal_warning('Maaf, Proses Produksi telah Selesai !');
      }else if(status == 'cancel'){
        alert_modal_warning('Maaf, Proses Produksi telah dibatalkan !');

      }else{

        var deptid    = "<?php echo $list->dept_id; ?>";
        var origin_mo = "<?php echo $list->origin;?>";
        var kode = "<?php echo $list->kode;?>";
        bootbox.dialog({
          message: "Apakah Anda yakin ingin Request Additional ?" ,
          title:  "<i class='fa fa-gear'></i> Request Additional !",
          buttons: {
            danger: {
                label    : "Yes ",
                className: "btn-primary btn-sm",
                callback : function() {
                  please_wait(function(){});
                    $.ajax({
                        dataType: "JSON",
                        url : '<?php echo site_url('manufacturing/mO/request_additional') ?>',
                        type: "POST",
                        data: {kode      : kode,
                              origin_mo : origin_mo, 
                              deptid : deptid  },
                        success: function(data){
                          if(data.sesi=='habis'){
                              //alert jika session habis
                              alert_modal_warning(data.message);
                              window.location.replace('../index');
                          }else if(data.status == 'failed'){
                              unblockUI( function() {});
                              alert_modal_warning(data.message);
                              refresh_mo();
                          }else{
                              unblockUI( function() {
                                setTimeout(function() { alert_notify(data.icon,data.message,data.type,function(){}); }, 1000);
                              });
                              refresh_mo();
                          }
                        },
                        error: function (xhr, ajaxOptions, thrownError){
                          alert('Error data');
                          alert(xhr.responseText);
                          unblockUI( function() {});
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

      }
    });

    //modal mode print
    $(document).on('click','#btn-print',function(e){
      var dept_id = '<?php echo $list->dept_id?>';
      if(dept_id == 'DYE'){

        e.preventDefault();
        $(".print_data").html('<center><h5><img src="<?php echo base_url('dist/img/ajax-loader.gif') ?> "/><br>Please Wait...</h5></center>');
        $("#print_data").modal({
          show: true,
          backdrop: 'static'
        });
        $('.modal-title').text('Pilih Tipe Dokumen yang akan di Print ?');
        var deptid  = "<?php echo $list->dept_id; ?>";
        var kode    = "<?php echo $list->kode;?>";
        $.post('<?php echo site_url()?>manufacturing/mO/print_mo_modal',
        { kode:kode, deptid:deptid},
        function(html){
          setTimeout(function() {$(".print_data").html(html);  },1000);
        }   
        );
      }

    });  

  //## << Finish Additional 
</script>


</body>
</html>
