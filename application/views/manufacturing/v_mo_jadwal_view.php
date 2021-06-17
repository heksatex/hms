             <div class="col-xs-12 table-responsive" >
              <table id="example1" class="table table-striped" border="1" >
                <thead>
                  <tr>
                    <?php 
                     //$i = 0;
                     //$leng = $mesin['jml'];
                    foreach ($data_mesin as $id) {  ?>
                      <th >
                        <?php echo $id->nama_mesin?>
                      </th>
                    <?php 
                      }
                      ?>
                  </tr>
                </thead>
                <tbody >                  
                  <tr  >
                    <?php foreach ($data_mesin as $id) {?>
                       <td>

                        <table border="0" width="150px" >

                           <?php 
                               
                              foreach ( $arr_multi[$id->mc_id]  as $list) {
                                ?>
                                <tr>
                                  <td>
                                    <a href="<?php echo 
                                    base_url('manufacturing/mO/edit/'.encrypt_url($list->kode)) ?>">
                                      <div class="box box-success box-solid">
                                        <div class="box-header with-border">
                                          <h3 class="box-title"><?php echo $list->kode;?></h3>
                                          <div class="box-tools pull-right">
                                          </div>
                                        </div>
                                        <div class="box-body">
                                            <?php echo $list->nama_produk."<br>";?>
                                            <?php echo $list->tanggal."<br>";?>
                                            <?php echo $list->qty." ".$list->uom."<br>";?>
                                        </div>
                                      </div>
                                    </a>
                                  </td>
                                </tr>
                                <?php
                              }
                             ?>

                        </table>

                       </td>
                    <?php }?>
                  </tr>
                </tbody>
              </table>
            </div> 