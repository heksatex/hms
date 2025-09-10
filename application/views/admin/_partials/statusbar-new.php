<style>
    .np {
        text-decoration: none;
        display: inline-block;
        padding: 8px 16px;
    }

    .np:hover {
        background-color: #04AA6D;
        color: black;
    }

    .previous .next {
        background-color: #f1f1f1;
        color: black;
    }

    .round {
        border-radius: 25%;
    }
</style>
<?php
//mengambil status bar aktif atau tidak
if (!$bar = $this->cache->get("status:bar:{$this->uri->segment(2)}")) {
    $bar = $this->m_menu->status_bar($this->uri->segment(2))->row_array();
    $this->cache->save("status:bar:{$this->uri->segment(2)}", $bar, 300);
}
if ($bar['status_bar'] == '1') {
    ?>
    <div class="box" style="background: #C0C0C0;">
        <div class="wizard1">
            <?php
            if (!$list = $this->cache->get("status:list:{$this->uri->segment(2)}")) {
                if (!empty($deptid)) {//$deptid di dapat dari parsing view yang view nya sama aja (manufacturing/warehouse)
                    $list = $this->m_menu->jenis_status_bar_deptid($this->uri->segment(2), $deptid); //untuk menampilkan jenis status bar
                } else {
                    $list = $this->m_menu->jenis_status_bar($this->uri->segment(2)); //untuk menampilkan jenis status bar
                }
                $this->cache->save("status:list:{$this->uri->segment(2)}", $list, 300);
            }

            if (!empty($jen_status)) {//cek apakah ada status berdasarkan kode  co, jen_status[status] didapat dari view
                $jenis_status = $jen_status;
            } else {
                $jenis_status = '';
            }
            foreach ($list as $row) {
                if ($row->jenis_status == $jenis_status) {
                    $a = "current";
                    $active = "badge badge-inverse  current";
                } else {
                    $a = "hidden-xs";
                    $active = "badge  hidden-xs";
                }
                ?>
                <a class="<?php echo $a; ?>"><span class="<?php echo $active; ?>"><?php echo $row->nama_status ?></span></a>
                <!--a class="current "><span class="badge badge-inverse  current">Produksi</span> </a-->
                <?php
            }

            if (isset($navigation_page) && $navigation_page) {
                ?>

                <div class="pull-right text-right" >
                    <span title="Previous" data-placement="bottom" data-toggle="tooltip" class="np previous round btn-sm" data-url="<?= $prev_page ?>">
                        <i class="fa fa-arrow-left"></i>
                    </span>
                    &nbsp;&nbsp;
                    <span title="Next" data-placement="bottom" data-toggle="tooltip" class="np next round btn-sm" data-url="<?= $next_page ?>">
                        <i class="fa fa-arrow-right"></i>
                    </span>
                </div>
                <?php
            }
            ?>
        </div>
    </div>
    <?php
}
?>