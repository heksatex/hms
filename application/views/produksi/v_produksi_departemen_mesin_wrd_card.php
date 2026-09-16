                        <?php 
                            // foreach($list_mo as $i => $mo): ?> 
                            <?php 
                            $ex = explode('|', $mo['reff_note']);
                            $mo_knitting = trim($ex[1] ?? '');
                            $mc_knitting = trim($ex[2] ?? '');
                            $gb          = trim($ex[4] ?? '');
                            $jml_beam     = trim($ex[5] ?? '');
                            $progress = 0;

                            if ($mo['qty'] > 0) {
                                $progress = min(
                                    100,
                                    ($mo['qty_produced'] / $mo['qty']) * 100
                                );
                            }
                            ?>
                            <a href="<?= site_url('manufacturing/produksi/produksihph/'.$id_dept.'/'.$mo['mc_id'].'/'.$mo['kode']) ?>" class="mo-card" data-progress="<?= round($progress); ?>">
                            <div class="kanban-card-item">
                                <div class="kanban-card-title">
                                    <span class="mo-number"> <?= $mo['kode']; ?> </span> 
                                    <span class="kanban-status-tag number-mo"> <?php echo $number; ?>  </span>
                                </div>
                                    <div class="mo-item-name"> 
                                        <?= $mo['nama_produk']; ?> 
                                    </div>
                                    <div class="mo-progress">
                                        <div class="progress-header">
                                            <span>Progress</span>
                                            <strong><?= round($progress); ?>%</strong>
                                        </div>
                                        <div class="progress-bar-bg">
                                            <div class="progress-bar-fill" style="width:<?= $progress; ?>%;"></div>
                                        </div>
                                    </div>
                                    <div class="mo-item-info">
                                        <div>
                                            <label>Target</label>
                                            <strong> <?= number_format($mo['qty']); ?> Mtr </strong>
                                        </div>
                                        <div>
                                            <label>Beam</label>
                                            <strong> <?= $gb." - ".$jml_beam  ?> </strong>
                                        </div>
                                        <div>
                                            <label>MO Knitting</label>
                                            <strong> <?= $mo_knitting ?> </strong>
                                        </div>
                                        <div>
                                            <label>Machine</label>
                                            <strong> <?= $mc_knitting; ?> </strong>
                                        </div>
                                    </div>
                            </div> 
                            </a>
                        <?php 
                        // endforeach; 
                           
                        ?> 