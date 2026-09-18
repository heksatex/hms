<!DOCTYPE html>
<html>
  <head>
        <?php $this->load->view("produksi/hmi/head") ?>
        <!-- <link rel="stylesheet" href="<?=base_url()?>dist/css/hmi/page-mo.css"> -->
         <link rel="stylesheet" href="<?= base_url() ?>dist/css/hmi/page-mo.css?v=<?= filemtime(FCPATH . 'dist/css/hmi/page-mo.css') ?>">
  </head>
  <body> 

    <?php $this->load->view('produksi/hmi/header', $header); ?> 
    <div class="container-fluid hmi-body">
        <div class="page-header">
            <div class="page-header-top">
                <a href="<?= site_url('manufacturing/produksi/produksiMesin/'.$id_dept) ?>" class="btn-back">
                    <i class="fa fa-arrow-left"></i>
                </a>
                <div class="page-title">
                    <h2>
                        <i class="fa fa-list-alt"></i>ANTRIAN MANUFACTURING ORDER (MO) 
                        <small>
                            Departemen <?= $nama_departemen ?>
                        </small>
                        <small>
                            <?= $nama_mesin['nama_mesin']?>
                        </small>
                    </h2>
                </div>
                <div class="page-header-actions">
                    <button
                        type="button"
                        id="btnUnsetDefault"
                        class="btn-unset-default">
                        <i class="fa fa-star"></i>
                        Unset Default
                    </button>

                    <div class="mo-search">
                        <i class="fa fa-search"></i>

                        <input 
                            type="text" 
                            id="searchMO"
                            placeholder="Cari MO / Produk / MO-MC Knitting ..."
                            autocomplete="off"
                        >

                        <button type="button" id="clearSearch" title="Clear">
                            <i class="fa fa-times"></i>
                        </button>
                    </div>

                    <span class="page-total" id="totalMO">
                        <?= count($list_mo). " / ".$mesin['total_mo']; ?> Total MO
                    </span>
                    <div class="mo-show-all">
                        <label>
                            <input type="checkbox" id="showAllMO" title="Tampilkan semua">
                            <span>Tampilkan semua</span>
                        </label>
                    </div>

                </div>
            </div>
        </div>
        <div class="row"> 
            <div id="tab-mo" class="tab-panel active">
                <div class="kanban-tab-view">
                    <div class="kanban-grid-layout" id="mo-list">
                        <?php 
                            $number = 1;
                            foreach ($list_mo as $mo): ?>
                            <?php
                            $this->load->view(
                                'produksi/v_produksi_departemen_mesin_wrd_card',
                                [
                                    'mo'      => $mo,
                                    'id_dept' => $id_dept,
                                    'number'  => $number++
                                ]
                            );
                            ?>

                        <?php endforeach; ?>
                    </div>
                    <div id="mo-empty" class="empty-data" style="<?= empty($list_mo) ? 'display:block' : 'display:none' ?>">
                        Data Tidak ditemukan
                    </div>
                </div>
            </div>
            <div id="no_mo">
            <?php 
             if((int) $mesin['total_mo'] == 0) 
                echo '<span class="empty-data"> Tidak ada MO '.$nama_departemen.' di Mesin '.$nama_mesin["nama_mesin"] .'</span>';

            ?>
            </div>
        </div>
    </div> 
    
    <?php $this->load->view('produksi/hmi/footer'); ?> 
    <?php $this->load->view("produksi/hmi/js") ?>
    <!-- bootbox -->
    <script src="<?php echo base_url('dist/bootbox/bootbox.min.js') ?>"></script>
    <script>
        $(document).ready(function() {
                const $searchBox = $('.mo-search');
                const $search = $('#searchMO');
                const $clear = $('#clearSearch');
                const $cards = $('.mo-card');
                const $total = $('#totalMO');
                
                /* =========================
                SEARCH ICON
                ========================= */
                $searchBox.on('click', '> i', function(e) {
                    // hanya berlaku untuk <= 990px
                    if ($(window).width() <= 990) {
                        e.stopPropagation();
                        $searchBox.addClass('active');
                        setTimeout(function() {
                            $search.focus();
                        }, 100);
                    }
                });

                /* =========================
                SEARCH
                ========================= */
                $search.on('input', function() {
                    const keyword = $.trim($(this).val().toLowerCase());
                    let visibleCount = 0;
                    $cards.each(function() {
                        const searchText = ($(this).attr('data-search') || '').toLowerCase();
                        if (keyword === '' || searchText.indexOf(keyword) !== -1) {
                            $(this).show();
                            visibleCount++;
                        } else {
                            $(this).hide();
                        }
                    });
                    $total.text(visibleCount + '  / ' + visibleCount + ' Total MO');
                    /* tampilkan clear */
                    if (keyword !== '') {
                        $clear.show();
                    } else {
                        $clear.hide();
                    }
                });

                /* =========================
                CLEAR
                ========================= */
                $clear.on('click', function(e) {
                    e.stopPropagation();
                    $search.val('').trigger('input').focus();
                });

                /* =========================
                CLICK OUTSIDE
                ========================= */
                $(document).on('click', function(e) {
                    if (!$(e.target).closest('.mo-search').length) {
                        // hanya collapse pada <= 990
                        if ($(window).width() <= 990 && $search.val().trim() === '') {
                            $searchBox.removeClass('active');
                        }
                    }
                });
                
                /* =========================
                ESC
                ========================= */
                $search.on('keydown', function(e) {
                    if (e.key === 'Escape') {
                        $search.val('').trigger('input');
                        if ($(window).width() <= 990) {
                            $searchBox.removeClass('active');
                        }
                        $search.blur();
                    }
                });

                /* =========================
                RESPONSIVE
                ========================= */
                $(window).on('resize', function() {
                    if ($(window).width() >= 991) {
                        // desktop/tablet besar → selalu tampil
                        $searchBox.addClass('active');
                    } else {
                        // tablet kecil → icon saja kalau kosong
                        if ($search.val().trim() === '') {
                            $searchBox.removeClass('active');
                        }
                    }
                });

                let searchTimer;
                $('#searchMO').on('input', function() {
                    const showAll = $('#showAllMO').is(':checked');
                    let keyword = $(this).val().trim();
                    clearTimeout(searchTimer);
                    searchTimer = setTimeout(function() {
                        $.ajax({
                            url: '<?= site_url("manufacturing/produksi/searchMO") ?>',
                            type: 'POST',
                            dataType: 'json',
                            data: {
                                keyword: keyword,
                                id_dept: '<?= $id_dept ?>',
                                mc_id: '<?= $mc_id ?>',
                                showAll:showAll
                            },
                            success: function(response) {
                                $('#mo-list').html(response.html);
                                $('#totalMO').html(response.total+' / '+response.total_all+ ' Total MO');
                                if (response.total == 0) {
                                    $('#mo-empty').show();
                                } else {
                                    $('#mo-empty').hide();
                                }
                            }
                        });
                    }, 300);
                });

                

                $('#showAllMO').on('change', function() {
                    const showAll = $('#showAllMO').is(':checked');
                    // applyMOFilter();
                    $.ajax({
                            url: '<?= site_url("manufacturing/produksi/searchMO") ?>',
                            type: 'POST',
                            dataType: 'json',
                            data: {
                                keyword: $("#searchMO").val(),
                                id_dept: '<?= $id_dept ?>',
                                mc_id: '<?= $mc_id ?>',
                                showAll:showAll
                            },
                            success: function(response) {
                                $('#mo-list').html(response.html);
                                $('#totalMO').html(response.total+' / '+response.total_all+ ' Total MO');
                                if (response.total == 0) {
                                    $('#mo-empty').show();
                                } else {
                                    $('#mo-empty').hide();
                                }
                            }
                        });
                });



                const deptId = '<?= $id_dept ?>';
                const mcId   = '<?= $mc_id ?>';

                const defaultMachine = localStorage.getItem('default_machine');

                if (defaultMachine) {

                    try {

                        const defaults = JSON.parse(defaultMachine);

                        if (Array.isArray(defaults)) {

                            const currentDept = String('<?= $id_dept ?>');
                            const currentMc   = String('<?= $mc_id ?>');

                            const isDefault = defaults.some(function(item) {

                                return String(item.dept_id) === currentDept &&
                                    String(item.mc_id) === currentMc;

                            });

                            if (isDefault) {
                                $('#btnUnsetDefault').addClass('is-visible');
                            }
                        }

                    } catch (e) {

                        localStorage.removeItem('default_machine');

                    }
                }



                function removeDefaultMachine(deptId, mcId) {
                    let defaults = JSON.parse(localStorage.getItem('default_machine') || '[]');
                    defaults = defaults.filter(function(item) {
                        return !(String(item.dept_id) === String(deptId) && String(item.mc_id) === String(mcId));
                    });
                    if (defaults.length > 0) {
                        localStorage.setItem('default_machine', JSON.stringify(defaults));
                    } else {
                        localStorage.removeItem('default_machine');
                    }
                }


                $('#btnUnsetDefault').on('click', function() {
                    const deptId = '<?= $id_dept ?>';
                    const mcId = '<?= $mc_id ?>';
                    bootbox.confirm({
                        title: '<i class="fa fa-star-o"></i> Unset Default',
                        message: 'Hapus mesin ini dari <strong>default machine</strong>?',
                        buttons: {
                            cancel: {
                                label: '<i class="fa fa-times"></i> Batal',
                                className: 'btn-default'
                            },
                            confirm: {
                                label: '<i class="fa fa-trash"></i> Hapus',
                                className: 'btn-danger'
                            }
                        },
                        callback: function(result) {
                            if (!result) {
                                return;
                            }
                            removeDefaultMachine(deptId, mcId);
                            $('#btnUnsetDefault').hide();
                        }
                    });
                });
                        

        });
    </script>
  </body>
</html>