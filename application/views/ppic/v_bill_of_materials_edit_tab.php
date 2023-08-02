                              <?php
                                $no = 1;
                                foreach ($items as $row) {
                              ?>
                                 <tr>
                                  <td data-content="edit" data-id="row_order" data-isi="<?php echo $row->row_order; ?>"><?php echo $no++;?></td>
                                  <td class="text-wrap width-400" data-content="edit" data-id="kode_produk" data-isi="<?php echo $row->kode_produk?>" ><?php echo '['.$row->kode_produk.'] '.$row->nama_produk;?></td>
                                  <td class="width-100" data-content="edit" data-id="qty" data-isi="<?php echo $row->qty ?>"><?php echo number_format($row->qty,2);?></td>
                                  <td class="width-100" data-content="edit" data-id="uom" data-isi="<?php echo $row->uom ?>"><?php echo $row->uom;?></td>
                                  <td class="width-100" data-content="edit" data-id="qty2" data-isi="<?php echo $row->qty2 ?>"><?php echo number_format($row->qty2,2);?></td>
                                  <td class="width-100" data-content="edit" data-id="uom2" data-isi="<?php echo $row->uom2 ?>"><?php echo $row->uom;?></td>
                                  <td class="text-wrap width-300" data-content="edit" data-id="note" data-isi="<?php echo htmlentities($row->note) ?>"><?php echo $row->note;?></td>
                                  <td class="width-200" align="center"> </a>
                                  </td>
                                </tr>

                              <?php 
                                }
                              ?>