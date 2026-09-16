                <a href="<?= site_url('manufacturing/produksi/listMO/'.$id_dept.'/'.$mc['mc_id']) ?>" class="machine-card" data-mc-id="<?= $mc['mc_id'] ?>"   data-machine="<?= htmlspecialchars($mc['nama_mesin'], ENT_QUOTES, 'UTF-8') ?>">
                     <span class="machine-mo-badge">
                        <?= (int)$mc['total_mo'] ?> MO
                    </span>
                    <i class="fa fa-cog"></i>
                    <div class="machine-name">
                        <?= strtoupper($mc['nama_mesin']) ?>
                    </div>
                </a>