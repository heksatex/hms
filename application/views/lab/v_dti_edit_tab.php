                              <?php
                                $no = 1;
                                foreach ($items as $row) {
                              ?>
                                <tr class="num">
                                  <td data-content="edit" data-id="row_order"  data-isi="<?php echo $row->row_order; ?>"></td>
                                  <td data-content="edit" data-id="kode_produk" data-isi="<?php echo $row->kode_produk; ?>" data-id2="prodhidd" data-isi2="<?php echo htmlentities($row->nama_produk); ?>"><?php echo '['.$row->kode_produk.'] '.$row->nama_produk?></td>
                                  <td data-content="edit" data-id="qty" data-isi="<?php echo $row->qty;?>" align="right"><?php echo $row->qty?></td>
                                  <td data-content="edit" data-id="uom" data-isi="<?php echo $row->uom;?>"><?php echo $row->uom?></td>
                                  <td data-content="edit" data-id="reff" data-isi="<?php echo htmlentities($row->reff_note);?>"><?php echo $row->reff_note?></td>
                                  <td class="min-width-50" align="center" >
                                    <a href="javascript:void(0)" class="add" title="Simpan" data-toggle="tooltip" style="margin-right: 5px;" type_obat="<?php echo $type_obat;?>"><i class="fa fa-save"></i></a>
                                    <a href="javascript:void(0)" class="edit" title="Edit" data-toggle="tooltip" style="color: #FFC107;   margin-right: 5px;" type_obat="<?php echo $type_obat;?>"><i class="fa fa-edit"></i></a>
                                    <a class="delete" onclick="hapus('<?php  echo htmlentities($row->kode_produk) ?>','<?php  echo htmlentities($row->nama_produk) ?>','<?php  echo $row->id_warna ?>', '<?php  echo $row->type_obat ?>', '<?php  echo $row->row_order ?>','<?php echo $row->id_warna_varian?>')"  href="javascript:void(0)" data-togle="tooltip" title="Hapus Product"><i class="fa fa-trash" style="color: red"></i> 
                                    </a>
                                    <a href="javascript:void(0)" class="cancel" title="Cancel" data-toggle="tooltip" type_obat="<?php echo $type_obat;?>" varian="<?php echo $row->id_warna_varian;?>"><i class="fa fa-close"></i></a>
                                  </td>
                                </tr>
                              <?php 
                                }
                              ?>