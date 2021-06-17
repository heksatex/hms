
<!DOCTYPE html>
<html>
<head>
  <?php $this->load->view("admin/_partials/head.php") ?>
  <style>
    
    .bs-glyphicons {
      padding-left: 0;
      padding-bottom: 1px;
      margin-bottom: 20px;
      list-style: none;
      overflow: hidden;
    }

    .bs-glyphicons li {
      float: left;
      width: 25%;
      height: 50px;
      padding: 10px;
      margin: 0 -1px -1px 0;
      font-size: 15px;
      line-height: 1.4;
      text-align: elft;
      border: 1px solid #ddd;
    }

    .bs-glyphicons .glyphicon {
      margin-top: 5px;
      margin-bottom: 10px;
      font-size: 20px;
    }

    .bs-glyphicons .glyphicon-class {
      display: block;
      text-align: left;
      word-wrap: break-word; /* Help out IE10+ with class names */
    }

    .bs-glyphicons li:hover {
      background-color: rgba(86, 61, 124, .1);
    }

    @media (min-width: 768px) {
      .bs-glyphicons li {
        width: 50%;
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
          <h3 class="box-title"><b>Form Edit - <?php echo $user->username;?></b></h3>
        </div>
        <div class="box-body">
          <form class="form-horizontal">
            
            <div class="form-group">                  
              <div class="col-md-12" >
                <div id="alert"></div>
              </div>
            </div>

            <div class="form-group">
              <div class="col-md-12">
                <div class="col-md-12 col-xs-12">                  
                  <div class="col-md-12 col-xs-12">
                    <div class="col-xs-2"><label>Nama User </label></div>
                    <div class="col-xs-6">
                      <input type="text" class="form-control input-sm" name="namauser" id="namauser" value="<?php echo htmlentities($user->nama) ?>"/>                      
                    </div>                                                        
                  </div>
                  <div class="col-md-12 col-xs-12">
                    <div class="col-xs-2"><label>Login </label></div>
                    <div class="col-xs-6">
                      <input type="text" class="form-control input-sm" name="login" id="login" value="<?php echo htmlentities($user->username) ?>" disabled>
                    </div>
                  </div>
                  <div class="col-md-12 col-xs-12">
                    <div class="col-xs-2"><label>Tanggal Dibuat </label></div>
                    <div class="col-xs-3">
                      <div class='input-group date' id='tanggaldibuat' >
                        <input type='text' class="form-control input-sm" name="tgldibuat" id="tgldibuat" readonly="readonly" value="<?php echo $user->create_date?>"/>
                        <span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar"></span>
                        </span>
                      </div>
                    </div>
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
                  <li class="active"><a href="#tab_1" data-toggle="tab">Hak Akses</a></li>                  
                </ul>             
                <div class="tab-content"><br>

                  <!-- tab1 Hak Akses -->
                  <div class="tab-pane active" id="tab_1">
                    <div class="col-md-12">
                      <form class="form-horizontal">

                        <!-- sales -->
                        <div class="col-md-12">
                          <p class="text-light-blue"><strong>Sales</strong></p>
                        </div>
                        <!-- kiri -->
                        <div class="col-md-6">
                          <div class="form-group">
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-8">Sales Contract</div>
                              <div class="col-xs-4">                                                             
                              <?php if (strpos($priv, 'mms37,') == true){ ?>
                                      <input type="checkbox" name="chk[]" value="mms37" checked="checked">
                              <?php }else{ ?>
                                      <input type="checkbox" name="chk[]" value="mms37">
                              <?php } ?>
                              </div>               
                            </div>                            
                          </div>
                        </div>
                        <!-- kanan -->
                        <div class="col-md-6">
                          <div class="form-group">
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-8">Customer</div>
                              <div class="col-xs-4">                                                             
                              <?php if (strpos($priv, 'mms57,') == true){ ?>
                                      <input type="checkbox" name="chk[]" value="mms57" checked="checked">
                              <?php }else{ ?>
                                      <input type="checkbox" name="chk[]" value="mms57">
                              <?php } ?>
                              </div>               
                            </div>   
                          </div>
                        </div>
                        
                        <!-- ppic -->
                        <div class="col-md-12">
                          <p class="text-light-blue"><strong>PPIC</strong></p>
                        </div>
                        <!-- kiri -->
                        <div class="col-md-6">
                          <div class="form-group">
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-8">Order Planning</div>
                              <div class="col-xs-4">                                
                                <?php if (strpos($priv, 'mms38,') == true){ ?>
                                        <input type="checkbox" name="chk[]" value="mms38" checked="checked">
                                <?php }else{ ?>
                                        <input type="checkbox" name="chk[]" value="mms38">
                                <?php } ?>
                              </div>               
                            </div>
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-8">Procurement Purchase</div>
                              <div class="col-xs-4">
                                <?php if (strpos($priv, 'mms50,') == true){ ?>
                                        <input type="checkbox" name="chk[]" value="mms50" checked="checked">
                                <?php }else{ ?>
                                        <input type="checkbox" name="chk[]" value="mms50">
                                <?php } ?>
                              </div>               
                            </div>                            
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-8">BoM</div>
                              <div class="col-xs-4">
                                <?php if (strpos($priv, 'mms73,') == true){ ?>
                                        <input type="checkbox" name="chk[]" value="mms73" checked="checked">
                                <?php }else{ ?>
                                        <input type="checkbox" name="chk[]" value="mms73">
                                <?php } ?>
                              </div>               
                            </div>                            
                          </div>
                        </div>
                        <!-- kanan -->
                        <div class="col-md-6">
                          <div class="form-group">
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-8">Procurement Order</div>
                              <div class="col-xs-4">                                
                                <?php if (strpos($priv, 'mms39,') == true){ ?>
                                        <input type="checkbox" name="chk[]" value="mms39" checked="checked">
                                <?php }else{ ?>
                                        <input type="checkbox" name="chk[]" value="mms39">
                                <?php } ?>
                              </div>               
                            </div>
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-8">Production Order</div>
                              <div class="col-xs-4">                                
                                <?php if (strpos($priv, 'mms17,') == true){ ?>
                                        <input type="checkbox" name="chk[]" value="mms17" checked="checked">
                                <?php }else{ ?>
                                        <input type="checkbox" name="chk[]" value="mms17">
                                <?php } ?>
                              </div>               
                            </div>
                          </div>
                        </div>

                        <!-- manufacturing -->
                        <div class="col-md-12">
                          <p class="text-light-blue"><strong>Manufacturing</strong></p>
                        </div>
                        <!-- kiri -->
                        <div class="col-md-6">
                          <div class="form-group">
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-8">MO Cutting Shearing</div>
                              <div class="col-xs-4">                                                                
                                <?php if (strpos($priv, 'mms7,') == true){ ?>
                                        <input type="checkbox" name="chk[]" value="mms7" checked="checked">
                                <?php }else{ ?>
                                        <input type="checkbox" name="chk[]" value="mms7">
                                <?php } ?>
                              </div>               
                            </div>
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-8">MO Jacquard</div>
                              <div class="col-xs-4">
                                <?php if (strpos($priv, 'mms4,') == true){ ?>
                                        <input type="checkbox" name="chk[]" value="mms4" checked="checked">
                                <?php }else{ ?>
                                        <input type="checkbox" name="chk[]" value="mms4">
                                <?php } ?>
                              </div>               
                            </div>
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-8">MO Tricot</div>
                              <div class="col-xs-4">                                
                                <?php if (strpos($priv, 'mms5,') == true){ ?>
                                        <input type="checkbox" name="chk[]" value="mms5" checked="checked">
                                <?php }else{ ?>
                                        <input type="checkbox" name="chk[]" value="mms5">
                                <?php } ?>
                              </div>               
                            </div>
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-8">MO Warping Dasar</div>
                              <div class="col-xs-4">                                
                                <?php if (strpos($priv, 'mms2,') == true){ ?>
                                        <input type="checkbox" name="chk[]" value="mms2" checked="checked">
                                <?php }else{ ?>
                                        <input type="checkbox" name="chk[]" value="mms2">
                                <?php } ?>
                              </div>               
                            </div>
                          </div>
                        </div>
                        <!-- kanan -->
                        <div class="col-md-6">
                          <div class="form-group">
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-8">MO Inspecting 1</div>
                              <div class="col-xs-4">                                
                                <?php if (strpos($priv, 'mms8,') == true){ ?>
                                        <input type="checkbox" name="chk[]" value="mms8" checked="checked">
                                <?php }else{ ?>
                                        <input type="checkbox" name="chk[]" value="mms8">
                                <?php } ?>
                              </div>               
                            </div>
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-8">MO Raschel</div>
                              <div class="col-xs-4">                                
                                <?php if (strpos($priv, 'mms6,') == true){ ?>
                                        <input type="checkbox" name="chk[]" value="mms6" checked="checked">
                                <?php }else{ ?>
                                        <input type="checkbox" name="chk[]" value="mms6">
                                <?php } ?>
                              </div>               
                            </div>                            
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-8">MO Twisting</div>
                              <div class="col-xs-4">                                                                
                                <?php if (strpos($priv, 'mms1,') == true){ ?>
                                        <input type="checkbox" name="chk[]" value="mms1" checked="checked">
                                <?php }else{ ?>
                                        <input type="checkbox" name="chk[]" value="mms1">
                                <?php } ?>
                              </div>               
                            </div>                            
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-8">MO Warping Panjang</div>
                              <div class="col-xs-4">                                                                
                                <?php if (strpos($priv, 'mms3,') == true){ ?>
                                        <input type="checkbox" name="chk[]" value="mms3" checked="checked">
                                <?php }else{ ?>
                                        <input type="checkbox" name="chk[]" value="mms3">
                                <?php } ?>
                              </div>               
                            </div>                            
                          </div>
                        </div>

                        <!-- warehouse -->
                        <div class="col-md-12">
                          <p class="text-light-blue"><strong>Warehouse</strong></p>
                        </div>
                        <!-- kiri -->
                        <div class="col-md-6">
                          <div class="form-group">
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-8">Cutting Shearing</div>
                              <div class="col-xs-4">                                
                                <?php if (strpos($priv, 'mms40,') == true or strpos($priv, 'mms41,') == true){ ?>
                                        <input type="checkbox" name="chk[]" value="mms40,mms41" checked="checked">
                                <?php }else{ ?>
                                        <input type="checkbox" name="chk[]" value="mms40,mms41">
                                <?php } ?>
                              </div>               
                            </div>
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-8">Gudang Greige</div>
                              <div class="col-xs-4">                                
                                <?php if (strpos($priv, 'mms33,') == true or strpos($priv, 'mms42,') == true){ ?>
                                        <input type="checkbox" name="chk[]" value="mms33,mms42" checked="checked">
                                <?php }else{ ?>
                                        <input type="checkbox" name="chk[]" value="mms33,mms42">
                                <?php } ?>
                              </div>               
                            </div>
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-8">Jacquard</div>
                              <div class="col-xs-4">                                
                                <?php if (strpos($priv, 'mms18,') == true or strpos($priv, 'mms19,') == true){ ?>
                                        <input type="checkbox" name="chk[]" value="mms18,mms19" checked="checked">
                                <?php }else{ ?>
                                        <input type="checkbox" name="chk[]" value="mms18,mms19">
                                <?php } ?>
                              </div>               
                            </div>
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-8">Receiving</div>
                              <div class="col-xs-4">                                
                                <?php if (strpos($priv, 'mms53,') == true or strpos($priv, 'mms54,') == true or strpos($priv, 'mms71') == true){ ?>
                                        <input type="checkbox" name="chk[]" value="mms53,mms54,mms71" checked="checked">
                                <?php }else{ ?>
                                        <input type="checkbox" name="chk[]" value="mms53,mms54,mms71">
                                <?php } ?>
                              </div>               
                            </div>
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-8">Stock Quants</div>
                              <div class="col-xs-4">                                
                                <?php if (strpos($priv, 'mms52,') == true){ ?>
                                        <input type="checkbox" name="chk[]" value="mms52" checked="checked">
                                <?php }else{ ?>
                                        <input type="checkbox" name="chk[]" value="mms52">
                                <?php } ?>
                              </div>               
                            </div>
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-8">Twisting</div>
                              <div class="col-xs-4">                                
                                <?php if (strpos($priv, 'mms43,') == true or strpos($priv, 'mms44,') == true){ ?>
                                        <input type="checkbox" name="chk[]" value="mms43,mms44" checked="checked">
                                <?php }else{ ?>
                                        <input type="checkbox" name="chk[]" value="mms43,mms44">
                                <?php } ?>
                              </div>               
                            </div>
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-8">Warping Panjang</div>
                              <div class="col-xs-4">                                
                                <?php if (strpos($priv, 'mms47,') == true or strpos($priv, 'mms48,') == true){ ?>
                                        <input type="checkbox" name="chk[]" value="mms47,mms48" checked="checked">
                                <?php }else{ ?>
                                        <input type="checkbox" name="chk[]" value="mms47,mms48">
                                <?php } ?>
                              </div>               
                            </div>
                          </div>
                        </div>
                        <!-- kanan -->
                        <div class="col-md-6">
                          <div class="form-group">
                            <div class="col-md-12 col-xs-12">                              
                              <div class="col-xs-8">Gudang Benang</div>
                              <div class="col-xs-4">                                
                                <?php if (strpos($priv, 'mms49,') == true or strpos($priv, 'mms51,') == true){ ?>
                                        <input type="checkbox" name="chk[]" value="mms49,mms51" checked="checked">
                                <?php }else{ ?>
                                        <input type="checkbox" name="chk[]" value="mms49,mms51">
                                <?php } ?>
                              </div>               
                            </div>                            
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-8">Inspecting 1</div>
                              <div class="col-xs-4">                                
                                <?php if (strpos($priv, 'mms23,') == true or strpos($priv, 'mms24,') == true){ ?>
                                        <input type="checkbox" name="chk[]" value="mms23,mms24" checked="checked">
                                <?php }else{ ?>
                                        <input type="checkbox" name="chk[]" value="mms23,mms24">
                                <?php } ?>
                              </div>               
                            </div>
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-8">Produk</div>
                              <div class="col-xs-4">                                
                                <?php if (strpos($priv, 'mms56,') == true){ ?>
                                        <input type="checkbox" name="chk[]" value="mms56" checked="checked">
                                <?php }else{ ?>
                                        <input type="checkbox" name="chk[]" value="mms56">
                                <?php } ?>
                              </div>               
                            </div>                            
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-8">Stock Moves</div>
                              <div class="col-xs-4">                                
                                <?php if (strpos($priv, 'mms55,') == true){ ?>
                                        <input type="checkbox" name="chk[]" value="mms55" checked="checked">
                                <?php }else{ ?>
                                        <input type="checkbox" name="chk[]" value="mms55">
                                <?php } ?>
                              </div>               
                            </div>                            
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-8">Tricot</div>
                              <div class="col-xs-4">                                
                                <?php if (strpos($priv, 'mms14,') == true or strpos($priv, 'mms15,') == true){ ?>
                                        <input type="checkbox" name="chk[]" value="mms14,mms15" checked="checked">
                                <?php }else{ ?>
                                        <input type="checkbox" name="chk[]" value="mms14,mms15">
                                <?php } ?>
                              </div>               
                            </div>
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-8">Warping Dasar</div>
                              <div class="col-xs-4">                                
                                <?php if (strpos($priv, 'mms45,') == true or strpos($priv, 'mms46,') == true){ ?>
                                        <input type="checkbox" name="chk[]" value="mms45,mms46" checked="checked">
                                <?php }else{ ?>
                                        <input type="checkbox" name="chk[]" value="mms45,mms46">
                                <?php } ?>
                              </div>               
                            </div>
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-8">Adjustment</div>
                              <div class="col-xs-4">                                
                                <?php if (strpos($priv, 'mms72,') == true){ ?>
                                        <input type="checkbox" name="chk[]" value="mms72" checked="checked">
                                <?php }else{ ?>
                                        <input type="checkbox" name="chk[]" value="mms72">
                                <?php } ?>
                              </div>               
                            </div>      
                          </div>
                        </div>

                         <!-- report -->
                        <div class="col-md-12">
                          <p class="text-light-blue"><strong>Report</strong></p>
                        </div>
                        <!-- kiri -->
                         <div class="col-md-6">
                          <div class="form-group">
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-8">Print MO</div>
                              <div class="col-xs-4">
                                <?php if (strpos($priv, 'mms74,') == true){ ?>
                                        <input type="checkbox" name="chk[]" value="mms74" checked="checked">
                                <?php }else{ ?>
                                        <input type="checkbox" name="chk[]" value="mms74">
                                <?php } ?>
                              </div>               
                            </div>
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-8">Cacat</div>
                              <div class="col-xs-4">
                                <?php if (strpos($priv, 'mms75,') == true){ ?>
                                        <input type="checkbox" name="chk[]" value="mms75" checked="checked">
                                <?php }else{ ?>
                                        <input type="checkbox" name="chk[]" value="mms75">
                                <?php } ?>
                              </div>               
                            </div>
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-8">Efisiensi</div>
                              <div class="col-xs-4">
                                <?php if (strpos($priv, 'mms80,') == true){ ?>
                                        <input type="checkbox" name="chk[]" value="mms80" checked="checked">
                                <?php }else{ ?>
                                        <input type="checkbox" name="chk[]" value="mms80">
                                <?php } ?>
                              </div>               
                            </div>
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-8">Rekap Cacat</div>
                              <div class="col-xs-4">
                                <?php if (strpos($priv, 'mms81,') == true){ ?>
                                        <input type="checkbox" name="chk[]" value="mms81" checked="checked">
                                <?php }else{ ?>
                                        <input type="checkbox" name="chk[]" value="mms81">
                                <?php } ?>
                              </div>               
                            </div>  
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-8">Adjustment</div>
                              <div class="col-xs-4">
                                <?php if (strpos($priv, 'mms86,') == true){ ?>
                                        <input type="checkbox" name="chk[]" value="mms86" checked="checked">
                                <?php }else{ ?>
                                        <input type="checkbox" name="chk[]" value="mms86">
                                <?php } ?>
                              </div>               
                            </div>                          
                          </div>
                        </div>

                        <!-- kanan -->
                        <div class="col-md-6">
                          <div class="form-group">
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-8">Produksi Warping Dasar</div>
                              <div class="col-xs-4">
                                <?php if (strpos($priv, 'mms76,') == true){ ?>
                                        <input type="checkbox" name="chk[]" value="mms76" checked="checked">
                                <?php }else{ ?>
                                        <input type="checkbox" name="chk[]" value="mms76">
                                <?php } ?>
                              </div>               
                            </div>
                            <div class="col-md-12 col-xs-12">
                            <div class="col-xs-8">HPH Warping Dasar</div>
                              <div class="col-xs-4">
                                <?php if (strpos($priv, 'mms77,') == true){ ?>
                                        <input type="checkbox" name="chk[]" value="mms77" checked="checked">
                                <?php }else{ ?>
                                        <input type="checkbox" name="chk[]" value="mms77">
                                <?php } ?>
                              </div>               
                            </div>                                
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-8">Produksi Tricot</div>
                              <div class="col-xs-4">
                                <?php if (strpos($priv, 'mms78,') == true){ ?>
                                        <input type="checkbox" name="chk[]" value="mms78" checked="checked">
                                <?php }else{ ?>
                                        <input type="checkbox" name="chk[]" value="mms78">
                                <?php } ?>
                              </div>               
                            </div>
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-8">HPH Tricot</div>
                              <div class="col-xs-4">
                                <?php if (strpos($priv, 'mms79,') == true){ ?>
                                        <input type="checkbox" name="chk[]" value="mms79" checked="checked">
                                <?php }else{ ?>
                                        <input type="checkbox" name="chk[]" value="mms79">
                                <?php } ?>
                              </div>               
                            </div>
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-8">Produksi Warping Panjang</div>
                              <div class="col-xs-4">
                                <?php if (strpos($priv, 'mms82,') == true){ ?>
                                        <input type="checkbox" name="chk[]" value="mms82" checked="checked">
                                <?php }else{ ?>
                                        <input type="checkbox" name="chk[]" value="mms82">
                                <?php } ?>
                              </div>               
                            </div>
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-8">HPH Warping Panjang</div>
                              <div class="col-xs-4">
                                <?php if (strpos($priv, 'mms83,') == true){ ?>
                                        <input type="checkbox" name="chk[]" value="mms83" checked="checked">
                                <?php }else{ ?>
                                        <input type="checkbox" name="chk[]" value="mms83">
                                <?php } ?>
                              </div>               
                            </div>
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-8">Produksi Jacquard</div>
                              <div class="col-xs-4">
                                <?php if (strpos($priv, 'mms84,') == true){ ?>
                                        <input type="checkbox" name="chk[]" value="mms84" checked="checked">
                                <?php }else{ ?>
                                        <input type="checkbox" name="chk[]" value="mms84">
                                <?php } ?>
                              </div>               
                            </div>
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-8">HPH Jacquard</div>
                              <div class="col-xs-4">
                                <?php if (strpos($priv, 'mms85,') == true){ ?>
                                        <input type="checkbox" name="chk[]" value="mms85" checked="checked">
                                <?php }else{ ?>
                                        <input type="checkbox" name="chk[]" value="mms85">
                                <?php } ?>
                              </div>               
                            </div>
                          </div>                                
                        </div>
                       

                        <!-- setting -->
                        <div class="col-md-12">
                          <p class="text-light-blue"><strong>Setting</strong></p>
                        </div>
                        <!-- kiri -->
                        <div class="col-md-6">
                          <div class="form-group">
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-8">User Manajemen</div>
                              <div class="col-xs-4">
                                <?php if (strpos($priv, 'mms90,') == true){ ?>
                                        <input type="checkbox" name="chk[]" value="mms90" checked="checked">
                                <?php }else{ ?>
                                        <input type="checkbox" name="chk[]" value="mms90">
                                <?php } ?>
                              </div>               
                            </div>                            
                          </div>
                        </div>
                        <!-- kanan -->
                        <div class="col-md-6">
                          <div class="form-group">
                            <div class="col-md-12 col-xs-12">
                              <div class="col-xs-8">Ganti Password</div>
                              <div class="col-xs-4">
                                <?php if (strpos($priv, 'mms91,') == true){ ?>
                                        <input type="checkbox" name="chk[]" value="mms91" checked="checked">
                                <?php }else{ ?>
                                        <input type="checkbox" name="chk[]" value="mms91">
                                <?php } ?>
                              </div>               
                            </div>                            
                          </div>
                        </div>
                        <div>
                            <!--button type='button' class="btn btn-sm btn-default"  id="btn-update" >HELP</button-->
                        </div>
                      </form>

                    </div>
                  </div>
                  <!-- tab1 Info Produk -->

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

  <footer class="main-footer">
   <?php $this->load->view("admin/_partials/modal.php") ?>
   <?php $this->load->view("admin/_partials/footer.php") ?>
  </footer>

</div>

<?php $this->load->view("admin/_partials/js.php") ?>

<script type="text/javascript">

  window.onload = function(){//hidden button
    $('#btn-generate').hide();
    $('#btn-cancel').hide();
    $('#btn-print').hide();
  }

  //set tgl buat
  /*
  var datenow=new Date();  
  datenow.setMonth(datenow.getMonth());
  $('#tanggal').datetimepicker({
      defaultDate: datenow,
      format : 'YYYY-MM-DD HH:mm:ss',
      ignoreReadonly: true,
  });
  */

  //generate chk yg checked apa saja
  function gen_chk_akses(){
    var arr = $.map($('input:checkbox:checked'), function(e, i) {
      return e.value;
    });
    return arr;
  }

  //klik button simpan
    $('#btn-simpan').click(function(){
      $('#btn-simpan').button('loading');

      var arr_chk_akses = gen_chk_akses();

      arr_chk_akses = arr_chk_akses.join(',');

      please_wait(function(){});
      $.ajax({
         type: "POST",
         dataType: "json",
         url :'<?php echo base_url('setting/user/simpan')?>',
         beforeSend: function(e) {
            if(e && e.overrideMimeType) {
                e.overrideMimeType("application/json;charset=UTF-8");
            }
         },
         data: {namauser        : $('#namauser').val(),
                login           : $('#login').val(),
                tanggaldibuat   : $('#tgldibuat').val(),
                arrchkakses     : arr_chk_akses,                
                status          : 'edit',

          },success: function(data){
            if(data.sesi == "habis"){
              //alert jika session habis
              alert_modal_warning(data.message);
              window.location.replace('index');
            }else if(data.status == "failed"){
              //jika ada form belum keiisi
              $('#btn-simpan').button('reset');
              unblockUI( function() {
                setTimeout(function() { alert_notify(data.icon,data.message,data.type); }, 1000);
              });
              document.getElementById(data.field).focus();

            }else{
             //jika berhasil disimpan/diubah
              unblockUI( function() {                
                setTimeout(function() { alert_notify(data.icon,data.message,data.type); }, 1000);
                });
              $('#btn-simpan').button('reset');
            }

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
    });


    $('#btn-update').click(function(){
      alert('masuk');
      please_wait(function(){});

      $.ajax({
         type: "POST",
         dataType: "json",
         url :'<?php echo base_url('setting/user/help_mo_done')?>',
         data :{},
         success: function(data){
          unblockUI( function() {
                setTimeout(function() { alert_notify(data.icon,data.message,data.type); }, 1000);
          });
         },error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.responseText);
            unblockUI( function() {});
         }
      
      });
    });
   
</script>


</body>
</html>
