<?php
foreach ($list as $row) {
    ?>
    <div class="box-comment">
        <img src="<?= base_url('dist/img/profile_default.png') ?>" class="user-image" alt="User Image">
        <div class="comment-text" style="word-wrap:break-word;">
            <span class="username">
                <?= $row->nama_user; ?>  <?= "| " . $row->jenis_log; ?>  
                <span class="text-muted pull-right"><?= date("d M Y H:i:s", strtotime($row->datelog)); ?></span>
            </span>
            <?= nl2br($row->note); ?>
        </div>
    </div>
    <?php
}
?>