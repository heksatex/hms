
<!DOCTYPE html>
<html lang="en">
<head>
    <?php $this->load->view("admin/_partials/head.php") ?>
    <link rel="stylesheet" href="<?=base_url()?>dist/css/menu.css">
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
  <?php
      $this->load->view("admin/_partials/sidebar.php"); 
   ?>
  </aside>

  <!-- Content Wrapper-->
  <div class="content-wrapper">
    <!-- Content Header (Status - Bar) -->
    <section class="content-header">
    </section>

    <!-- Main content -->
    <section class="content">
      <!--  box content -->
      <div class="box">

        <div class="menu-management">

            <div class="menu-header">
                <div>
                    <h3>
                        <i class="fa fa-bars"></i>
                        Management Menu
                    </h3>
                    <small>
                        Kelola menu utama, submenu dan child menu
                    </small>
                </div>
                <button type="button" class="btn btn-primary" id="btnAddMain"> 
                    <i class="fa fa-plus"></i>
                    Menu Utama
                </button>
            </div>

            <div class="menu-filter-panel">
                <div class="row">
                    <div class="col-sm-7">
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-search"></i>
                            </span>

                            <input type="text"
                                id="searchMenu"
                                class="form-control"
                                placeholder="Cari nama atau kode menu...">

                            <span class="input-group-btn">
                                <button type="button"
                                        id="btnResetMenuFilter"
                                        class="btn btn-default"
                                        title="Reset filter">
                                    <i class="fa fa-refresh"></i>
                                </button>
                            </span>
                        </div>
                    </div>

                    <div class="col-sm-5">
                        <select id="filterJenisMenu" class="form-control">
                            <option value="">Semua Jenis Menu</option>
                            <option value="main">Menu Utama</option>
                            <option value="sub">Submenu</option>
                            <option value="child">Child</option>
                        </select>
                    </div>
                </div>

                <div id="menuFilterInfo" class="text-muted">
                    Menampilkan semua menu
                </div>
            </div>

            <div class="menu-tree menu-main-list">

                <?php foreach ($menu_tree as $main): ?>

                    <div class="menu-main menu-sortable-item" data-kode="<?= html_escape($main['kode']) ?>" data-jenis="main">

                        <div class="menu-main-header menu-drag-handle" >

                            <div class="menu-title">
                                <i class="fa fa-folder"></i>
                                <strong>
                                    <?= html_escape($main['nama']) ?>
                                </strong>
                                <span class="menu-code">
                                    <?= html_escape($main['kode']) ?>
                                </span>
                            </div>

                            <div class="menu-actions">
                                <button class="btn btn-xs btn-default btn-edit-main" data-kode="<?= html_escape($main['kode']) ?>">
                                    <i class="fa fa-pencil"></i>
                                </button>

                                <button class="btn btn-xs btn-success btn-add-sub" data-main="<?= html_escape($main['kode']) ?>">
                                    <i class="fa fa-plus"></i>
                                    Submenu
                                </button>

                                <button class="btn btn-xs btn-danger btn-delete-main" data-kode="<?= html_escape($main['kode']) ?>">
                                    <i class="fa fa-trash"></i>
                                </button>

                                <button type="button" class="btn btn-xs btn-default btn-toggle-main" title="Buka/Tutup Menu">
                                    <i class="fa fa-chevron-up"></i>
                                </button>
                            </div>
                        </div>
                        <div class="menu-main-body">
                        <?php if (!empty($main['sub_menu'])): ?>

                            <div    class="menu-sub-list menu-sub-sortable"    data-main="<?= html_escape($main['kode']) ?>">

                                <?php foreach ($main['sub_menu'] as $sub): ?>

                                    <div class="menu-sub menu-sub-item" data-kode="<?= html_escape($sub['kode']) ?>" data-jenis="sub"> 
                                        
                                        <div class="menu-sub-header menu-sub-drag-handle">

                                            <div class="menu-info">
                                                <i class="<?= html_escape($sub['ikon']) ?>"></i>
                                                <strong>
                                                    <?= html_escape($sub['nama']) ?>
                                                </strong>

                                                <small class="text-muted">
                                                    (<?= html_escape($sub['kode']) ?>)
                                                </small>

                                                <?php if (!empty($sub['parent']) && $sub['parent'] === 'true'): ?>

                                                    <span class="label label-primary">
                                                        Parent
                                                    </span>

                                                <?php endif; ?>

                                            </div>

                                            <div class="menu-actions">

                                                    <!-- <span class="label label-info">
                                                        <i class="fa fa-cog"></i>
                                                        <?= (int)$sub['button_count'] ?>
                                                    </span>

                                                    <span class="label label-warning">
                                                        <i class="fa fa-tags"></i>
                                                        <?= (int)$sub['status_count'] ?>
                                                    </span> -->

                                                    <button class="btn btn-xs btn-success btn-add-child" data-parent="<?= html_escape($sub['kode']) ?>" data-main="<?= html_escape($main['kode']) ?>" title="Tambah Child">
                                                        <i class="fa fa-plus"></i>
                                                        Child
                                                    </button>
                                                    <button type="button" class="btn btn-xs btn-info btn-manage-button" data-kode="<?= html_escape($sub['kode']) ?>" data-nama="<?= html_escape($sub['nama']) ?> "title="Kelola Button">
                                                        <i class="fa fa-cog"></i> Button
                                                         <span class="badge">
                                                            <?= (int)$sub['button_count'] ?>
                                                        </span>
                                                    </button>
                                                    <button type="button" class="btn btn-xs btn-warning btn-manage-status" data-kode="<?= html_escape($sub['kode']) ?>" data-nama="<?= html_escape($sub['nama']) ?>">
                                                        <i class="fa fa-tags"></i> Status
                                                        <span class="badge">
                                                            <?= (int)$sub['status_count'] ?>
                                                        </span>
                                                    </button>

                                                    <button type="button" class="btn btn-xs btn-primary btn-manage-user-access" data-kode="<?= html_escape($sub['kode']) ?>" data-nama="<?= html_escape($sub['nama']) ?>">
                                                        <i class="fa fa-users"></i>
                                                            User Akses
                                                        <span class="badge">
                                                            <?= (int) $sub['user_access_count'] ?>
                                                        </span>

                                                    </button>

                                                    <button class="btn btn-xs btn-default btn-edit-sub" data-kode="<?= html_escape($sub['kode']) ?>">
                                                        <i class="fa fa-pencil"></i>
                                                    </button>

                                                    <button class="btn btn-xs btn-danger btn-delete-sub" data-kode="<?= html_escape($sub['kode']) ?>"  data-nama="<?= html_escape($sub['nama']) ?>">
                                                        <i class="fa fa-trash"></i>
                                                    </button>

                                            </div>

                                        </div>


                                        <?php if (!empty($sub['child_menu'])): ?>

                                            <button type="button" class="btn btn-xs btn-default btn-toggle-child" title="Buka/Tutup Child">
                                                <i class="fa fa-chevron-up"></i>
                                            </button>

                                            <div class="menu-child-list menu-child-sortable" data-parent="<?= html_escape($sub['kode']) ?>">

                                                <?php foreach ($sub['child_menu'] as $child): ?>
                                                    <div class="menu-child menu-child-item" data-kode="<?= html_escape($child['kode']) ?>" data-jenis="child">

                                                        <div class="menu-child-drag-handle">
                                                            <!-- <i class="fa fa-arrows"></i> -->
                                                            <i class="<?= html_escape($child['ikon']) ?>"></i>
                                                            <span>
                                                                <?= html_escape($child['nama']) ?>
                                                            </span>
                                                            <small>
                                                                <?= html_escape($child['kode']) ?>
                                                            </small>
                                                        </div>


                                                        <div class="menu-actions">

                                                            <!-- <span class="label label-info">
                                                                <i class="fa fa-cog"></i>
                                                                <?= (int)$child['button_count'] ?>
                                                            </span>

                                                            <span class="label label-warning">
                                                                <i class="fa fa-tags"></i>
                                                                <?= (int)$child['status_count'] ?>
                                                            </span> -->

                                                            <button type="button" class="btn btn-xs btn-info btn-manage-button" data-kode="<?= html_escape($child['kode']) ?>" data-nama="<?= html_escape($child['nama']) ?>">
                                                                <i class="fa fa-cog"></i> Button
                                                                <span class="badge">
                                                                    <?= (int)$child['button_count'] ?>
                                                                </span>
                                                            </button>

                                                            <button type="button" class="btn btn-xs btn-warning btn-manage-status" data-kode="<?= html_escape($child['kode']) ?>"data-nama="<?= html_escape($child['nama']) ?>"title="Kelola Status">
                                                                <i class="fa fa-tags"></i> Status
                                                                <span class="badge">
                                                                    <?= (int)$child['status_count'] ?>
                                                                </span>
                                                            </button>

                                                            <button type="button" class="btn btn-xs btn-primary btn-manage-user-access" data-kode="<?= html_escape($sub['kode']) ?>" data-nama="<?= html_escape($sub['nama']) ?>">
                                                                <i class="fa fa-users"></i>
                                                                User Akses

                                                                <span class="badge">
                                                                    <?= (int) $sub['user_access_count'] ?>
                                                                </span>
                                                            </button>

                                                            <button class="btn btn-xs btn-default btn-edit-sub" data-kode="<?= html_escape($child['kode']) ?>">
                                                                <i class="fa fa-pencil"></i>
                                                            </button>

                                                            <button class="btn btn-xs btn-danger btn-delete-sub" data-kode="<?= html_escape($child['kode']) ?>"  data-nama="<?= html_escape($sub['nama']) ?>">
                                                                <i class="fa fa-trash"></i>
                                                            </button>

                                                        </div>
                                                    </div>

                                                <?php endforeach; ?>

                                            </div>

                                        <?php endif; ?>

                                    </div>

                                <?php endforeach; ?>

                            </div>

                        <?php endif; ?>

                        </div>
                    </div>

                <?php endforeach; ?>

            </div>

        </div>
       
      </div>
      <!-- /.box -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->


</div>
<!-- /.Site wrapper -->

<?php $this->load->view("admin/_partials/js.php") ?>

<div id="load_modal">
    <!-- Load Partial Modal -->
</div>

<!-- =========================================================
     MODAL MAIN MENU
========================================================= -->

<div class="modal fade" id="modalMainMenu" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">
					<span>&times;</span>
				</button>
				<h4 class="modal-title" id="modalMainTitle"> Tambah Menu Utama </h4>
			</div>
			<div class="modal-body">
				<form id="formMainMenu">
					<input type="hidden" name="mode" id="mainMode" value="insert">
					<div class="form-group">
						<label> Kode Menu </label>
						<input type="text" name="kode" id="mainKode" class="form-control" placeholder="Contoh: mm_production">
					</div>
					<div class="form-group">
						<label> Nama Menu </label>
						<input type="text" name="nama" id="mainNama" class="form-control" placeholder="Contoh: Production">
					</div>
					<div class="form-group">
						<label> Inisial Class </label>
						<input type="text" name="inisial_class" id="mainClass" class="form-control" placeholder="Contoh: production">
					</div>
					<div class="form-group">
						<label> Urutan </label>
						<input type="number" name="row_order" id="mainOrder" class="form-control" value="0">
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal"> Batal </button>
				<button type="button" class="btn btn-primary" id="btnSaveMain">
					<i class="fa fa-save"></i> Simpan </button>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="modalSubMenu" tabindex="-1">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"> &times; </button>
				<h4 class="modal-title" id="modalSubTitle"> Tambah Submenu </h4>
			</div>
			<form id="formSubMenu">
				<input type="hidden" name="mode" id="subMode" value="insert">
				<div class="modal-body">
					<div class="row">
						<!-- MAIN MENU -->
						<div class="col-md-4">
							<div class="form-group">
								<label> Menu Utama <span class="text-danger">*</span>
								</label>
								<select name="main_menu_kode" id="subMainMenu" class="form-control">
									<option value=""> - Pilih Menu Utama - </option> <?php foreach ($menu_tree as $main): ?> <option value="<?= html_escape($main['kode']) ?>"> <?= html_escape($main['nama']) ?> </option> <?php endforeach; ?>
								</select>
							</div>
						</div>
						<!-- PARENT -->
						<div class="col-md-4">
							<div class="form-group">
								<label> Parent </label>
								<select name="is_menu_sub" id="subParent" class="form-control">
									<option value=""> - Tidak ada parent - </option>
								</select>
								<small class="text-muted"> Kosong = submenu level 1. </small>
							</div>
						</div>
                        <div class="col-md-4">
                            <div class="form-group" id="parentField">
                                <label for="parent">Jadikan Parent?</label>

                                <select name="parent" id="parent" class="form-control">
                                    <option value="">Tidak</option>
                                    <option value="true">Ya, bisa memiliki Child</option>
                                </select>

                                <small class="text-muted">
                                    Pilih Ya jika submenu ini akan memiliki submenu turunan (Child).
                                </small>
                            </div>
                        </div>
					</div>
					<div class="row">
						<!-- KODE -->
						<div class="col-md-4">
							<div class="form-group">
								<label> Kode <span class="text-danger">*</span>
								</label>
								<input type="text" name="kode" id="subKode" class="form-control"  readonly>   
                                <small class="text-muted">
                                    Kode dibuat otomatis.
                                </small>
							</div>
						</div>
						<!-- NAMA -->
						<div class="col-md-8">
							<div class="form-group">
								<label> Nama <span class="text-danger">*</span>
								</label>
								<input type="text" name="nama" id="subNama" class="form-control" autocomplete="off">
							</div>
						</div>
					</div>
					<div class="row">
						<!-- ICON -->
						<div class="col-md-4">
							<div class="form-group">
								<label>Icon</label>
								<input type="text" name="ikon" id="subIkon" class="form-control" placeholder="fa fa-file-text-o">
							</div>
						</div>
						<!-- LINK -->
						<div class="col-md-8">
							<div class="form-group">
								<label>Link Menu</label>
								<input type="text" name="link_menu" id="subLink" class="form-control" placeholder="report/joblist">
							</div>
						</div>
					</div>
					<div class="row">
						<!-- CLASS -->
						<div class="col-md-4">
							<div class="form-group">
								<label>Inisial Class</label>
								<input type="text" name="inisial_class" id="subClass" class="form-control">
							</div>
						</div>
						<!-- ORDER -->
						<div class="col-md-2">
							<div class="form-group">
								<label>Order</label>
								<input type="number" name="row_order" id="subOrder" class="form-control" value="0">
							</div>
						</div>
						<!-- STATUS BAR -->
						<div class="col-md-3">
							<div class="form-group">
								<label>Status Bar</label>
								<select name="status_bar" id="subStatusBar" class="form-control">
									<option value="0">Tidak</option>
									<option value="1">Ya</option>
								</select>
							</div>
						</div>
						<!-- DEPT -->
						<div class="col-md-3">
							<div class="form-group">
								<label>Dept ID</label>
								<input type="text" name="dept_id" id="subDept" class="form-control">
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal"> Batal </button>
					<button type="button" class="btn btn-primary" id="btnSaveSub">
						<i class="fa fa-save"></i> Simpan </button>
				</div>
			</form>
		</div>
	</div>
</div>


<div class="modal fade" id="modalButton" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"> &times; </button>
                <h4 class="modal-title">
                    <i class="fa fa-cog"></i> Kelola Button </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="alert alert-info">
                            <strong id="buttonMenuNama"> - </strong>
                            <br>
                            <small> Kode: <span id="buttonMenuKode"> - </span>
                            </small>
                        </div>
                    </div>
                </div>
                <div class="text-right" style="margin-bottom:10px;">
                    <button type="button" class="btn btn-success" id="btnAddButton">
                        <i class="fa fa-plus"></i> Tambah Button </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="tableButton">
                        <thead>
                            <tr>
                                <th width="50">No</th>
                                <th>Kategori</th>
                                <th>Jenis</th>
                                <th>Caption</th>
                                <th>Icon</th>
                                <th>Link</th>
                                <th width="100">Urutan</th>
                                <th width="110">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="8" class="text-center text-muted"> Belum ada button. </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="modalButtonForm" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"> &times; </button>
                <h4 class="modal-title" id="buttonFormTitle"> Tambah Button </h4>
            </div>
            <div class="modal-body">
                <form id="formButton">
                    <input type="hidden" name="main_menu_sub_kode" id="buttonSubKode">
                    <input type="hidden" name="old_kategori_button" id="oldKategoriButton">
                    <input type="hidden" name="old_jenis_button" id="oldJenisButton">
                    <div class="form-group">
                        <label> Kategori Button </label>
                        <input type="text" name="kategori_button" id="kategoriButton" class="form-control" placeholder="view_button / form_button">
                    </div>
                    <div class="form-group">
                        <label> Jenis Button </label>
                        <input type="text" name="jenis_button" id="jenisButton" class="form-control" placeholder="Contoh: tambah / simpan / print / duplicate ">
                    </div>
                    <div class="form-group">
                        <label> Caption </label>
                        <input type="text" name="caption" id="buttonCaption" class="form-control" placeholder="Contoh: Tambah">
                    </div>
                    <div class="form-group">
                        <label> Icon </label>
                        <input type="text" name="ikon" id="buttonIkon" class="form-control" placeholder="Contoh: fa fa-plus">
                    </div>
                    <div class="form-group">
                        <label> Link Button </label>
                        <input type="text" name="link_button" id="buttonLink" class="form-control" placeholder="Contoh: transaksi/tambah">
                    </div>
                    <div class="form-group">
                        <label> Class Button </label>
                        <input type="text" name="class_button" id="buttonClass" class="form-control" placeholder="Contoh: btn btn-primary">
                    </div>
                    <div class="form-group">
                        <label> ID Button </label>
                        <input type="text" name="id_button" id="buttonId" class="form-control" placeholder="Contoh: btnTambah">
                    </div>
                    <div class="form-group">
                        <label> Row Order </label>
                        <input type="number" name="row_order" id="buttonOrder" class="form-control" value="0">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"> Batal </button>
                <button type="button" class="btn btn-primary" id="btnSaveButton">
                    <i class="fa fa-save"></i> Simpan </button>
            </div>
        </div>
    </div>
</div>



<div class="modal fade" id="modalStatus" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"> &times; </button>
                <h4 class="modal-title">
                    <i class="fa fa-tags"></i> Kelola Status </h4>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <strong id="statusMenuNama"> - </strong>
                    <br>
                    <small> Kode: <span id="statusMenuKode"> - </span>
                    </small>
                </div>
                <div class="text-right" style="margin-bottom:10px;">
                    <button type="button" class="btn btn-success" id="btnAddStatus">
                        <i class="fa fa-plus"></i> Tambah Status </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="tableStatus">
                        <thead>
                            <tr>
                                <th width="50"> No </th>
                                <th> Jenis Status </th>
                                <th> Nama Status </th>
                                <th width="70"> Urutan </th>
                                <th width="90"> Aksi </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="5" class="text-center text-muted"> Belum ada status. </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>



<div class="modal fade" id="modalStatusForm" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"> &times; </button>
                <h4 class="modal-title" id="statusFormTitle"> Tambah Status </h4>
            </div>
            <div class="modal-body">
                <form id="formStatus">
                    <input type="hidden" name="main_menu_sub_kode" id="statusSubKode">
                    <input type="hidden" name="old_jenis_status" id="oldJenisStatus">
                    <div class="form-group">
                        <label> Jenis Status </label>
                        <input type="text" name="jenis_status" id="jenisStatus" class="form-control" placeholder="Contoh: draft">
                    </div>
                    <div class="form-group">
                        <label> Nama Status </label>
                        <input type="text" name="nama_status" id="namaStatus" class="form-control" placeholder="Contoh: Draft">
                    </div>
                    <div class="form-group">
                        <label> Row Order </label>
                        <input type="number" name="row_order" id="statusOrder" class="form-control" value="0" readonly>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"> Batal </button>
                <button type="button" class="btn btn-primary" id="btnSaveStatus">
                    <i class="fa fa-save"></i> Simpan </button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="modalUserAccess" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"> &times; </button>
        <h4 class="modal-title">
          <i class="fa fa-users"></i> Akses User
        </h4>
        <p class="text-muted" id="userAccessSubName"></p>
      </div>
      <div class="modal-body">
        <input type="hidden" id="userAccessSubKode">
        <div class="table-responsive">
          <table class="table table-bordered table-hover ">
            <thead>
              <tr>
                <th>No</th>
                <th>Username</th>
                <th>Nama User</th>
                <th>Departemen</th>
                <th width="130">Aksi</th>
              </tr>
            </thead>
            <tbody id="userAccessTableBody">
              <tr>
                <td colspan="4" class="text-center"> Memuat data... </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"> Tutup </button>
      </div>
    </div>
  </div>
</div>

<script>
    $(document).ready(function() {
    /*
     * URL Controller
     */
    var menuUrl = '<?= site_url("setting/menu") ?>';
    /* =====================================================
     * TAMBAH MENU UTAMA
     * ===================================================== */
    $('#btnAddMain').on('click', function() {
        resetMainForm();
        $('#mainMode').val('insert');
        $('#modalMainTitle').text('Tambah Menu Utama');
        $('#mainKode').prop('readonly', false);
        $('#modalMainMenu').modal('show');
    });
    /* =====================================================
     * EDIT MENU UTAMA
     * ===================================================== */
    $(document).on('click', '.btn-edit-main', function() {
        var kode = $(this).data('kode');
        $.ajax({
            url: menuUrl + '/get_main_menu',
            type: 'POST',
            dataType: 'json',
            data: {
                kode: kode
            },
            success: function(res) {
                if (!res.status) {
                    alert('Data menu tidak ditemukan.');
                    return;
                }
                var data = res.data;
                $('#mainMode').val('update');
                $('#mainKode').val(data.kode);
                $('#mainNama').val(data.nama);
                $('#mainClass').val(data.inisial_class);
                $('#mainOrder').val(data.row_order);
                $('#mainKode').prop('readonly', true);
                $('#modalMainTitle').text('Edit Menu Utama');
                $('#modalMainMenu').modal('show');
            },
            error: function() {
                alert('Terjadi kesalahan saat mengambil data.');
            }
        });
    });
    /* =====================================================
     * SAVE MENU UTAMA
     * ===================================================== */
    $('#btnSaveMain').on('click', function() {
        var mode = $('#mainMode').val();
        var url = mode === 'insert' ? menuUrl + '/save_main' : menuUrl + '/update_main';
        var kode = $.trim($('#mainKode').val());
        var nama = $.trim($('#mainNama').val());
        if (kode === '') {
            alert('Kode menu wajib diisi.');
            $('#mainKode').focus();
            return;
        }
        if (nama === '') {
            alert('Nama menu wajib diisi.');
            $('#mainNama').focus();
            return;
        }
        var btn = $(this);
        btn.prop('disabled', true);
        btn.html('<i class="fa fa-spinner fa-spin"></i> Menyimpan...');
        $.ajax({
            url: url,
            type: 'POST',
            dataType: 'json',
            data: $('#formMainMenu').serialize(),
            success: function(res) {
                if (res.status) {
                    $('#modalMainMenu').modal('hide');
                    location.reload();
                } else {
                    alert(res.message);
                }
            },
            error: function() {
                alert('Terjadi kesalahan pada server.');
            },
            complete: function() {
                btn.prop('disabled', false);
                btn.html('<i class="fa fa-save"></i> Simpan');
            }
        });
    });
    /* =====================================================
     * DELETE MENU UTAMA
     * ===================================================== */
    $(document).on('click', '.btn-delete-main', function() {
        var kode = $(this).data('kode');
        // var nama = $(this).data('nama');
        var nama = $(this).closest('.menu-main').find('.menu-title strong').first().text().trim();
        if (!confirm('Hapus menu "' + nama + '"?')) {
            return;
        }
        $.ajax({
            url: menuUrl + '/delete_main',
            type: 'POST',
            dataType: 'json',
            data: {
                kode: kode
            },
            success: function(res) {
                if (res.status) {
                    location.reload();
                } else {
                    alert(res.message);
                }
            },
            error: function() {
                alert('Terjadi kesalahan pada server.');
            }
        });
    });
    /* =====================================================
     * RESET FORM
     * ===================================================== */
    function resetMainForm() {
        $('#formMainMenu')[0].reset();
        $('#mainMode').val('insert');
        $('#mainKode').prop('readonly', false);
        $('#mainOrder').val(0);
    }

    // ========================================
    // TAMBAH SUBMENU
    // ========================================

    $(document).on('click', '.btn-add-sub', function () {

        var mainKode = $(this).data('main');

        resetSubForm();

        $('#subMode').val('insert');
        $('#subMainMenu').val(mainKode);

        $('#modalSubTitle').text('Tambah Submenu');

        loadParentMenu(mainKode, '', '');

        // $('#subKode').prop('readonly', false);

        $('#modalSubMenu').modal('show');
    });

    function loadParentMenu(mainKode, selectedParent, currentKode) {

        var select = $('#subParent');

        select.html(
            '<option value="">- Tidak ada parent -</option>'
        );

        if (!mainKode) {
            return;
        }

        $.ajax({
            url: menuUrl + '/get_parent_menu',
            type: 'POST',
            dataType: 'json',

            data: {
                main_menu_kode: mainKode
            },

            success: function (res) {

                if (!res.status) {
                    return;
                }

                $.each(res.data, function (i, item) {

                    // Jangan tampilkan dirinya sendiri
                    if (currentKode && item.kode == currentKode) {
                        return;
                    }

                    select.append(
                        $('<option>', {
                            value: item.kode,
                            text: item.nama + ' (' + item.kode + ')'
                        })
                    );

                });

                if (selectedParent) {
                    select.val(selectedParent);
                }

            },

            error: function () {

                alert(
                    'Gagal mengambil daftar parent.'
                );

            }
        });
    }

    // ========================================
    // EDIT SUBMENU
    // ========================================

    $(document).on('click', '.btn-edit-sub', function () {

        var kode = $(this).data('kode');

        $.ajax({
            url: menuUrl + '/get_sub_detail',
            type: 'POST',
            dataType: 'json',

            data: {
                kode: kode
            },

            success: function (res) {

                if (!res.status) {
                    alert(res.message);
                    return;
                }

                var data = res.data;

                $('#subMode').val('update');

                $('#subKode').val(data.kode);
                $('#subNama').val(data.nama);
                $('#subIkon').val(data.ikon);
                $('#subLink').val(data.link_menu);
                $('#subClass').val(data.inisial_class);
                $('#subOrder').val(data.row_order);
                $('#subStatusBar').val(data.status_bar);
                $('#subDept').val(data.dept_id);
                $('#subMainMenu').val(data.main_menu_kode);
                // $('#parent').val(data.parent);

                var isChild = String(data.is_menu_sub || '') !== '';

                setParentOption(isChild);

                if (isChild) {
                    // Child tidak boleh menjadi Parent
                    $('#parent').val('');
                } else {
                    // Submenu level pertama: ambil nilai dari database
                    $('#parent').val(
                        String(data.parent || '') === 'true' ? 'true' : ''
                    );
                }

                loadParentMenu(
                    data.main_menu_kode,
                    data.is_menu_sub,
                    data.kode
                );

                $('#subKode').prop('readonly', true);

                $('#modalSubTitle').text(
                    'Edit Submenu'
                );

                $('#modalSubMenu').modal('show');
            },

            error: function () {

                alert(
                    'Terjadi kesalahan saat mengambil data submenu.'
                );

            }
        });
    });

    // ========================================
    // SIMPAN SUBMENU
    // ========================================

    $('#btnSaveSub').on('click', function() {
      var mode = $('#subMode').val();
      var url = mode === 'insert' ? menuUrl + '/save_sub' : menuUrl + '/update_sub';
      var mainKode = $.trim($('#subMainMenu').val());
      var kode = $.trim($('#subKode').val());
      var nama = $.trim($('#subNama').val());
      if (mainKode === '') {
          alert('Menu utama wajib dipilih.');
          $('#subMainMenu').focus();
          return;
      }
      if (kode === '') {
          alert('Kode submenu wajib diisi.');
          $('#subKode').focus();
          return;
      }
      if (nama === '') {
          alert('Nama submenu wajib diisi.');
          $('#subNama').focus();
          return;
      }
      var btn = $(this);
      btn.prop('disabled', true);
      btn.html('<i class="fa fa-spinner fa-spin"></i> Menyimpan...');
      $.ajax({
          url: url,
          type: 'POST',
          dataType: 'json',
          data: $('#formSubMenu').serialize(),
          success: function(res) {
              if (res.status) {
                  $('#modalSubMenu').modal('hide');
                  location.reload();
              } else {
                  alert(res.message);
              }
          },
          error: function() {
              alert('Terjadi kesalahan pada server.');
          },
          complete: function() {
              btn.prop('disabled', false);
              btn.html('<i class="fa fa-save"></i> Simpan');
          }
      });
    });

    function resetSubForm() {

        $('#formSubMenu')[0].reset();

        $('#subMode').val('insert');

        // $('#subKode').prop('readonly', false);

        $('#subOrder').val(0);

        $('#subStatusBar').val(0);

        $('#subParent').html(
            '<option value="">- Tidak ada parent -</option>'
        );

    }
    
    $(document).on('click', '.btn-delete-sub', function() {
        var kode = $(this).data('kode');
        var nama = $(this).data('nama');
        // var nama = $(this).closest('.menu-sub, .menu-child').find('span').first().text().trim();
        if (!confirm('Hapus menu "' + nama + '"?')) {
            return;
        }
        var btn = $(this);
        btn.prop('disabled', true);
        $.ajax({
            url: menuUrl + '/delete_sub',
            type: 'POST',
            dataType: 'json',
            data: {
                kode: kode
            },
            success: function(res) {
                if (res.status) {
                    location.reload();
                } else {
                    alert(res.message);
                    btn.prop('disabled', false);
                }
            },
            error: function() {
                alert('Terjadi kesalahan pada server.');
                btn.prop('disabled', false);
            }
        });
    });
        
    $(document).on('click', '.btn-add-child', function() {
        var mainKode = $(this).data('main');
        var parentKode = $(this).data('parent');
        resetSubForm();
        setParentOption(true);
        $('#subMode').val('insert');
        $('#subMainMenu').val(mainKode);
        $('#modalSubTitle').text('Tambah Child Menu');
        loadParentMenu(mainKode, parentKode, '');
        $('#subKode').prop('readonly', true);
        $('#modalSubMenu').modal('show');
        getNextSubCode();
    });


    $(document).on('click', '.btn-add-sub', function() {
        var mainKode = $(this).data('main');
        resetSubForm();
        setParentOption(false);
        $('#subMode').val('insert');
        $('#subMainMenu').val(mainKode);
        $('#modalSubTitle').text('Tambah Submenu');
        loadParentMenu(mainKode, '', '');
        $('#subKode').prop('readonly', true);
        $('#modalSubMenu').modal('show');
        getNextSubCode();
    });

    function getNextSubCode() {
        $('#subKode').val('Mengambil kode...');
        $.ajax({
            url: menuUrl + '/get_next_sub_code',
            type: 'POST',
            dataType: 'json',
            success: function(res) {
                if (res.status) {
                    $('#subKode').val(res.kode);
                } else {
                    $('#subKode').val('');
                    alert('Gagal membuat kode submenu.');
                }
            },
            error: function() {
                $('#subKode').val('');
                alert('Gagal mengambil kode submenu.');
            }
        });
    }
        
    var currentButtonSubKode = '';
    $(document).on('click', '.btn-manage-button', function() {
        currentButtonSubKode = $(this).data('kode');
        var nama = $(this).data('nama');
        $('#buttonMenuKode').text(currentButtonSubKode);
        $('#buttonMenuNama').text(nama);
        loadButtons(currentButtonSubKode);
        $('#modalButton').modal('show');
    });

    function loadButtons(subKode) {
        var tbody = $('#tableButton tbody');
        tbody.html('<tr>' + '<td colspan="8" class="text-center">' + '<i class="fa fa-spinner fa-spin"></i> ' + 'Mengambil data...' + '</td>' + '</tr>');
        $.ajax({
            url: menuUrl + '/get_buttons',
            type: 'POST',
            dataType: 'json',
            data: {
                main_menu_sub_kode: subKode
            },
            success: function(res) {
                if (!res.status) {
                    tbody.html('<tr>' + '<td colspan="8" class="text-center text-danger">' + res.message + '</td>' + '</tr>');
                    return;
                }
                renderButtons(res.data);
            },
            error: function(xhr) {
                console.log(xhr.responseText);
                tbody.html('<tr>' + '<td colspan="8" class="text-center text-danger">' + 'Gagal mengambil data button.' + '</td>' + '</tr>');
            }
        });
    }

    function renderButtons(data) {
        var tbody = $('#tableButton tbody');
        tbody.empty();
        if (!data || data.length === 0) {
            tbody.html('<tr>' + '<td colspan="8" class="text-center text-muted">' + 'Belum ada button.' + '</td>' + '</tr>');
            return;
        }
        $.each(data, function(i, item) {
            var row = $('<tr>');
            row.append($('<td>').text(i + 1));
            row.append($('<td>').text(item.kategori_button));
            row.append($('<td>').text(item.jenis_button));
            row.append($('<td>').text(item.caption));
            row.append($('<td>').html('<i class="' + escapeHtml(item.ikon) + '"></i> ' + escapeHtml(item.ikon)));
            row.append($('<td>').text(item.link_button));
            row.append($('<td>').addClass('text-center').text(item.row_order));
            var actions = $('<td>').addClass('text-center');
            actions.append($('<button>').attr('type', 'button').addClass('btn btn-xs btn-default btn-edit-button').attr('data-kategori', item.kategori_button).attr('data-jenis', item.jenis_button).html('<i class="fa fa-pencil"></i>'));
            actions.append(' ');
            actions.append($('<button>').attr('type', 'button').addClass('btn btn-xs btn-danger btn-delete-button').attr('data-kategori', item.kategori_button).attr('data-jenis', item.jenis_button).html('<i class="fa fa-trash"></i>'));
            row.append(actions);
            tbody.append(row);
        });
    }

    function escapeHtml(text)
    {
        return $('<div>')
            .text(text == null ? '' : text)
            .html();
    }

    $(document).on('click', '#btnAddButton', function() {
        $('#formButton')[0].reset();
        $('#buttonSubKode').val(currentButtonSubKode);
        $('#oldKategoriButton').val('');
        $('#oldJenisButton').val('');
        $('#buttonOrder').val(0);
        $('#buttonFormTitle').text('Tambah Button');
        $('#btnSaveButton').data('mode', 'insert');
        $('#modalButtonForm').modal('show');
    });

    $(document).on('click', '#btnSaveButton', function() {
        var btn = $(this);
        var subKode = $.trim($('#buttonSubKode').val());
        var kategori = $.trim($('#kategoriButton').val());
        var jenis = $.trim($('#jenisButton').val());
        var caption = $.trim($('#buttonCaption').val());
        if (subKode === '') {
            alert('Kode submenu tidak ditemukan.');
            return;
        }
        if (kategori === '') {
            alert('Kategori button wajib diisi.');
            $('#kategoriButton').focus();
            return;
        }
        if (jenis === '') {
            alert('Jenis button wajib diisi.');
            $('#jenisButton').focus();
            return;
        }
        if (caption === '') {
            alert('Caption button wajib diisi.');
            $('#buttonCaption').focus();
            return;
        }
        var mode = btn.data('mode');
        var url = mode === 'update' ? menuUrl + '/update_button' : menuUrl + '/save_button';
        btn.prop('disabled', true);
        btn.html('<i class="fa fa-spinner fa-spin"></i> Menyimpan...');
        $.ajax({
            url: url,
            type: 'POST',
            dataType: 'json',
            data: $('#formButton').serialize(),
            success: function(res) {
                if (res.status) {
                    $('#modalButtonForm').modal('hide');
                    loadButtons(currentButtonSubKode);
                } else {
                    alert(res.message);
                }
            },
            error: function(xhr) {
                console.log(xhr.responseText);
                alert('Terjadi kesalahan pada server.');
            },
            complete: function() {
                btn.prop('disabled', false);
                btn.html('<i class="fa fa-save"></i> Simpan');
            }
        });
    });

    $(document).on('click', '.btn-delete-button', function() {
        var btn = $(this);
        var kategori = btn.data('kategori');
        var jenis = btn.data('jenis');
        if (!currentButtonSubKode) {
            alert('Kode submenu tidak ditemukan.');
            return;
        }
        if (!confirm('Hapus button "' + kategori + ' - ' + jenis + '"?')) {
            return;
        }
        btn.prop('disabled', true);
        $.ajax({
            url: menuUrl + '/delete_button',
            type: 'POST',
            dataType: 'json',
            data: {
                main_menu_sub_kode: currentButtonSubKode,
                kategori_button: kategori,
                jenis_button: jenis
            },
            success: function(res) {
                if (res.status) {
                    loadButtons(currentButtonSubKode);
                } else {
                    alert(res.message);
                    btn.prop('disabled', false);
                }
            },
            error: function(xhr) {
                console.log(xhr.responseText);
                alert('Terjadi kesalahan saat menghapus button.');
                btn.prop('disabled', false);
            }
        });
    });

    $(document).on('click', '.btn-edit-button', function() {
        var btn = $(this);
        var kategori = btn.data('kategori');
        var jenis = btn.data('jenis');
        if (!currentButtonSubKode) {
            alert('Kode submenu tidak ditemukan.');
            return;
        }
        $.ajax({
            url: menuUrl + '/get_button_detail',
            type: 'POST',
            dataType: 'json',
            data: {
                main_menu_sub_kode: currentButtonSubKode,
                kategori_button: kategori,
                jenis_button: jenis
            },
            success: function(res) {
                if (!res.status) {
                    alert(res.message);
                    return;
                }
                var data = res.data;
                /*
                * Simpan primary key LAMA.
                * Dibutuhkan ketika kategori/jenis diubah.
                */
                $('#oldKategoriButton').val(data.kategori_button);
                $('#oldJenisButton').val(data.jenis_button);
                $('#buttonSubKode').val(data.main_menu_sub_kode);
                $('#kategoriButton').val(data.kategori_button);
                $('#jenisButton').val(data.jenis_button);
                $('#buttonCaption').val(data.caption);
                $('#buttonIkon').val(data.ikon);
                $('#buttonLink').val(data.link_button);
                $('#buttonClass').val(data.class_button);
                $('#buttonId').val(data.id_button);
                $('#buttonOrder').val(data.row_order);
                $('#buttonFormTitle').text('Edit Button');
                $('#btnSaveButton').data('mode', 'update');
                $('#modalButtonForm').modal('show');
            },
            error: function(xhr) {
                console.log(xhr.responseText);
                alert('Terjadi kesalahan saat mengambil data button.');
            }
        });
    });


    var currentStatusSubKode = '';
    $(document).on('click', '.btn-manage-status', function() {
        currentStatusSubKode = $(this).data('kode');
        var nama = $(this).data('nama');
        $('#statusMenuKode').text(currentStatusSubKode);
        $('#statusMenuNama').text(nama);
        loadStatuses(currentStatusSubKode);
        $('#modalStatus').modal('show');
    });

    function loadStatuses(subKode) {
        var tbody = $('#tableStatus tbody');
        tbody.html('<tr>' + '<td colspan="5" class="text-center">' + '<i class="fa fa-spinner fa-spin"></i> ' + 'Mengambil data...' + '</td>' + '</tr>');
        $.ajax({
            url: menuUrl + '/get_status',
            type: 'POST',
            dataType: 'json',
            data: {
                main_menu_sub_kode: subKode
            },
            success: function(res) {
                if (!res.status) {
                    tbody.html('<tr>' + '<td colspan="5" class="text-center text-danger">' + res.message + '</td>' + '</tr>');
                    return;
                }
                renderStatuses(res.data);
            },
            error: function(xhr) {
                console.log(xhr.responseText);
                tbody.html('<tr>' + '<td colspan="5" class="text-center text-danger">' + 'Gagal mengambil data status.' + '</td>' + '</tr>');
            }
        });
    }

    function renderStatuses(data) {
        var tbody = $('#tableStatus tbody');
        tbody.empty();
        if (!data || data.length === 0) {
            tbody.html('<tr>' + '<td colspan="5" class="text-center text-muted">' + 'Belum ada status.' + '</td>' + '</tr>');
            return;
        }
        $.each(data, function(i, item) {
            var row = $('<tr>');
            row.append($('<td>').text(i + 1));
            row.append($('<td>').text(item.jenis_status));
            row.append($('<td>').text(item.nama_status));
            row.append($('<td>').addClass('text-center').text(item.row_order));
            var actions = $('<td>').addClass('text-center');
            actions.append($('<button>').attr('type', 'button').addClass('btn btn-xs btn-default btn-edit-status').attr('data-jenis', item.jenis_status).html('<i class="fa fa-pencil"></i>'));
            actions.append(' ');
            actions.append($('<button>').attr('type', 'button').addClass('btn btn-xs btn-danger btn-delete-status').attr('data-jenis', item.jenis_status).html('<i class="fa fa-trash"></i>'));
            row.append(actions);
            tbody.append(row);
        });
    }

    $(document).on('click', '#btnAddStatus', function() {
        $('#formStatus')[0].reset();
        $('#statusSubKode').val(currentStatusSubKode);
        $('#oldJenisStatus').val('');
        $('#statusOrder').val(0);
        $('#statusFormTitle').text('Tambah Status');
        $('#btnSaveStatus').data('mode', 'insert');
        $('#modalStatusForm').modal('show');
    });

    $(document).on('click', '#btnSaveStatus', function() {
        var btn = $(this);
        var subKode = $.trim($('#statusSubKode').val());
        var jenis = $.trim($('#jenisStatus').val());
        var nama = $.trim($('#namaStatus').val());
        if (subKode === '') {
            alert('Kode submenu tidak ditemukan.');
            return;
        }
        if (jenis === '') {
            alert('Jenis status wajib diisi.');
            $('#jenisStatus').focus();
            return;
        }
        if (nama === '') {
            alert('Nama status wajib diisi.');
            $('#namaStatus').focus();
            return;
        }
        var mode = btn.data('mode');
        var url = mode === 'update' ? menuUrl + '/update_status' : menuUrl + '/save_status';
        btn.prop('disabled', true);
        btn.html('<i class="fa fa-spinner fa-spin"></i> Menyimpan...');
        $.ajax({
            url: url,
            type: 'POST',
            dataType: 'json',
            data: $('#formStatus').serialize(),
            success: function(res) {
                if (res.status) {
                    $('#modalStatusForm').modal('hide');
                    loadStatuses(currentStatusSubKode);
                } else {
                    alert(res.message);
                }
            },
            error: function(xhr) {
                console.log(xhr.responseText);
                alert('Terjadi kesalahan pada server.');
            },
            complete: function() {
                btn.prop('disabled', false);
                btn.html('<i class="fa fa-save"></i> Simpan');
            }
        });
    });

    $(document).on('click', '.btn-edit-status', function() {
        var jenis = $(this).data('jenis');
        if (!currentStatusSubKode) {
            alert('Kode submenu tidak ditemukan.');
            return;
        }
        $.ajax({
            url: menuUrl + '/get_status_detail',
            type: 'POST',
            dataType: 'json',
            data: {
                main_menu_sub_kode: currentStatusSubKode,
                jenis_status: jenis
            },
            success: function(res) {
                if (!res.status) {
                    alert(res.message);
                    return;
                }
                var data = res.data;
                /*
                * Simpan jenis_status lama
                * karena merupakan bagian PK.
                */
                $('#oldJenisStatus').val(data.jenis_status);
                $('#statusSubKode').val(data.main_menu_sub_kode);
                $('#jenisStatus').val(data.jenis_status);
                $('#namaStatus').val(data.nama_status);
                $('#statusOrder').val(data.row_order);
                $('#statusFormTitle').text('Edit Status');
                $('#btnSaveStatus').data('mode', 'update');
                $('#modalStatusForm').modal('show');
            },
            error: function(xhr) {
                console.log(xhr.responseText);
                alert('Gagal mengambil data status.');
            }
        });
    });

    $(document).on('click', '.btn-delete-status', function() {
        var btn = $(this);
        var jenis = btn.data('jenis');
        if (!currentStatusSubKode) {
            alert('Kode submenu tidak ditemukan.');
            return;
        }
        if (!confirm('Hapus status "' + jenis + '"?')) {
            return;
        }
        btn.prop('disabled', true);
        $.ajax({
            url: menuUrl + '/delete_status',
            type: 'POST',
            dataType: 'json',
            data: {
                main_menu_sub_kode: currentStatusSubKode,
                jenis_status: jenis
            },
            success: function(res) {
                if (res.status) {
                    loadStatuses(currentStatusSubKode);
                } else {
                    alert(res.message);
                    btn.prop('disabled', false);
                }
            },
            error: function(xhr) {
                console.log(xhr.responseText);
                alert('Terjadi kesalahan saat menghapus status.');
                btn.prop('disabled', false);
            }
        });
    });

    $(document).on('click', '.btn-manage-button, .btn-manage-status', function() {
        $('.menu-sub-header, .menu-child').removeClass('selected');
        $(this).closest('.menu-sub-header, .menu-child').addClass('selected');
    });

    $('#modalButton').on('hidden.bs.modal', function() {
        $('.menu-sub-header, .menu-child').removeClass('selected');
    });

    $('#modalStatus').on('hidden.bs.modal', function() {
        $('.menu-sub-header, .menu-child').removeClass('selected');
    });

    $('.menu-main-list').sortable({
        items: '.menu-sortable-item',
        handle: '.menu-drag-handle',
        axis: 'y',
        tolerance: 'pointer'
    });


    $('.menu-sub-sortable').sortable({
        items: '.menu-sub-item',
        handle: '.menu-sub-drag-handle',
        axis: 'y',
        tolerance: 'pointer',
        update: function() {
            var orders = [];
            $(this).children('.menu-sub-item').each(function(index) {
                orders.push({
                    kode: $(this).data('kode'),
                    row_order: index + 1
                });
            });
            console.log('SUBMENU ORDERS:', orders);
            $.ajax({
                url: menuUrl + '/save_sub_order',
                type: 'POST',
                dataType: 'json',
                data: {
                    orders: orders
                },
                success: function(res) {
                    if (res.status) {
                        console.log('Urutan submenu berhasil disimpan.');
                    } else {
                        alert(res.message);
                    }
                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                    alert('Gagal menyimpan urutan submenu.');
                }
            });
        }
    });


    $('.menu-child-sortable').sortable({
        items: '.menu-child-item',
        handle: '.menu-child-drag-handle',
        axis: 'y',
        tolerance: 'pointer',
        update: function() {
            var orders = [];
            var parentKode = $(this).data('parent');
            $(this).children('.menu-child-item').each(function(index) {
                orders.push({
                    kode: $(this).data('kode'),
                    row_order: index + 1
                });
            });
            console.log('CHILD PARENT:', parentKode);
            console.log('CHILD ORDERS:', orders);
            $.ajax({
                url: menuUrl + '/save_child_order',
                type: 'POST',
                dataType: 'json',
                data: {
                    parent_kode: parentKode,
                    orders: orders
                },
                success: function(res) {
                    if (res.status) {
                        console.log('Urutan child berhasil disimpan.');
                    } else {
                        alert(res.message);
                    }
                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                    alert('Gagal menyimpan urutan child.');
                }
            });
        }
    });

    $(document).on('click', '.btn-toggle-main', function (e) {

        e.preventDefault();
        e.stopPropagation();

        var $button = $(this);
        var $main = $button.closest('.menu-main');
        var $body = $main.children('.menu-main-body');
        var $icon = $button.find('i');

        $body.stop(true, true).slideToggle(200);

        if ($icon.hasClass('fa-chevron-up')) {
            $icon.removeClass('fa-chevron-up')
                .addClass('fa-chevron-down');
        } else {
            $icon.removeClass('fa-chevron-down')
                .addClass('fa-chevron-up');
        }

    });


    $(document).on('click', '.btn-toggle-child', function (e) {

        e.preventDefault();
        e.stopPropagation();

        var $button = $(this);
        var $sub = $button.closest('.menu-sub-item');
        var $list = $sub.children('.menu-child-list');
        var $icon = $button.find('i');

        $list.stop(true, true).slideToggle(200);

        if ($icon.hasClass('fa-chevron-up')) {
            $icon.removeClass('fa-chevron-up')
                .addClass('fa-chevron-down');
        } else {
            $icon.removeClass('fa-chevron-down')
                .addClass('fa-chevron-up');
        }

    });

    function loadSubUserAccess(kode) {
        $('#userAccessTableBody').html('<tr><td colspan="4" class="text-center">' + 'Memuat data...</td></tr>');
        $.ajax({
            url: menuUrl + '/get_sub_user_access',
            type: 'POST',
            dataType: 'json',
            data: {
                kode: kode
            },
            success: function(res) {
                if (!res.status) {
                    $('#userAccessTableBody').html('<tr><td colspan="4" class="text-center">' + escapeHtml(res.message) + '</td></tr>');
                    return;
                }
                var html = '';
                if (!res.data || res.data.length === 0) {
                    html = '<tr><td colspan="5" class="text-center text-muted">' + 'Belum ada user yang memiliki akses.' + '</td></tr>';
                } else {
                    var num = 1;
                    $.each(res.data, function(i, user) {
                        html += '<tr>';
                        html += '<td>' + num++ +'</td>';
                        html += '<td>' + escapeHtml(user.username) + '</td>';
                        html += '<td>' + escapeHtml(user.nama) + '</td>';
                        html += '<td>' + escapeHtml(user.dept || '-') + '</td>';
                        html += '<td>';
                        html += '<button type="button" ' + 'class="btn btn-xs btn-danger btn-revoke-user-access" ' + 'data-username="' + escapeHtml(user.username) + '" ' + 'data-main="' + escapeHtml(user.main_menu_kode) + '">';
                        html += '<i class="fa fa-unlock"></i> Cabut Akses';
                        html += '</button>';
                        html += '</td>';
                        html += '</tr>';
                    });
                }
                $('#userAccessTableBody').html(html);
            },
            error: function(xhr) {
                console.log(xhr.responseText);
                $('#userAccessTableBody').html('<tr><td colspan="5" class="text-center text-danger text-muted">' + 'Gagal memuat daftar user.' + '</td></tr>');
            }
        });
    }
    // Buka modal akses user
    $(document).on('click', '.btn-manage-user-access', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var kode = $(this).data('kode');
        var nama = $(this).data('nama');
        $('#userAccessSubKode').val(kode);
        $('#userAccessSubName').text(nama + ' (' + kode + ')');
        $('#modalUserAccess').modal('show');
        loadSubUserAccess(kode);
    });
    // Cabut akses user
    $(document).on('click', '.btn-revoke-user-access', function() {
        var username = $(this).data('username');
        var mainKode = $(this).data('main');
        var subKode = $('#userAccessSubKode').val();
        if (!confirm('Cabut akses submenu ini dari user ' + username + '?')) {
            return;
        }
        var $button = $(this);
        $button.prop('disabled', true);
        $.ajax({
            url: menuUrl + '/delete_sub_user_access',
            type: 'POST',
            dataType: 'json',
            data: {
                username: username,
                main_menu_kode: mainKode,
                kode: subKode
            },
            success: function(res) {
                if (res.status) {
                    loadSubUserAccess(subKode);
                } else {
                    alert(res.message);
                    $button.prop('disabled', false);
                }
            },
            error: function(xhr) {
                console.log(xhr.responseText);
                alert('Gagal mencabut akses user.');
                $button.prop('disabled', false);
            }
        });
    });

    function setParentOption(isChild) {

        if (isChild) {
            $('#parentField').hide();
            $('#parent').val('');
        } else {
            $('#parentField').show();
        }

    }

    // ========================================
    // SEARCH & FILTER MENU
    // ========================================
    function applyMenuFilter() {
        var keyword = $.trim($('#searchMenu').val()).toLowerCase();
        var jenis = $('#filterJenisMenu').val();
        var filtering = keyword !== '' || jenis !== '';
        var totalMain = 0;
        var totalSub = 0;
        var totalChild = 0;

        function matches(text) {
            return String(text || '').toLowerCase().indexOf(keyword) !== -1;
        }

        function hideElement($element) {
            $element.addClass('menu-filter-hidden');
        }

        function showElement($element) {
            $element.removeClass('menu-filter-hidden');
        }
        $('.menu-main-list > .menu-main').each(function() {
            var $main = $(this);
            var $mainBody = $main.children('.menu-main-body');
            var mainText = [
                $main.data('kode'),
                $main.find('.menu-title').first().text()
            ].join(' ');
            var mainMatch = (jenis === '' || jenis === 'main') && matches(mainText);
            var mainVisible = mainMatch;
            var visibleSubCount = 0;
            $main.find('.menu-sub-list > .menu-sub').each(function() {
                var $sub = $(this);
                var subText = [
                    $sub.data('kode'),
                    $sub.children('.menu-sub-header').text()
                ].join(' ');
                var subMatch = (jenis === '' || jenis === 'sub') && matches(subText);
                var visibleChildCount = 0;
                $sub.children('.menu-child-list').children('.menu-child').each(function() {
                    var $child = $(this);
                    var childText = [
                        $child.data('kode'),
                        $child.children('.menu-child-drag-handle').text()
                    ].join(' ');
                    var childMatch = (jenis === '' || jenis === 'child') && matches(childText);
                    var childVisible = !filtering || childMatch || (jenis === '' && (mainMatch || subMatch));
                    if (childVisible) {
                        showElement($child);
                        visibleChildCount++;
                        totalChild++;
                    } else {
                        hideElement($child);
                    }
                });
                var subVisible = !filtering || subMatch || visibleChildCount > 0 || (jenis === '' && mainMatch);
                if (subVisible) {
                    showElement($sub);
                    visibleSubCount++;
                    totalSub++;
                } else {
                    hideElement($sub);
                }
                // Saat filter aktif, buka child list yang relevan.
                if (filtering && visibleChildCount > 0) {
                    $sub.children('.menu-child-list').show();
                    $sub.children('.btn-toggle-child').find('i').removeClass('fa-chevron-down').addClass('fa-chevron-up');
                }
            });
            if (jenis === 'main' && filtering) {
                visibleSubCount = 0;
            }
            if (jenis === 'main' && filtering) {
                $main.find('.menu-sub').addClass('menu-filter-hidden');
            }
            mainVisible = !filtering || mainMatch || visibleSubCount > 0;
            if (mainVisible) {
                showElement($main);
                totalMain++;
                // Buka induk agar hasil pencarian terlihat.
                if (filtering) {
                    $mainBody.show();
                    $main.children('.menu-main-header').find('.btn-toggle-main i').removeClass('fa-chevron-down').addClass('fa-chevron-up');
                }
            } else {
                hideElement($main);
            }
        });
        var info = 'Menu utama: ' + totalMain + ' | Submenu: ' + totalSub + ' | Child: ' + totalChild;
        if (filtering && totalMain === 0) {
            info = 'Data tidak ditemukan.';
        } else if (!filtering) {
            info = 'Menampilkan semua menu';
        }
        $('#menuFilterInfo').text(info);
    }
    // Ketik untuk mencari
    $('#searchMenu').on('input', function() {
        applyMenuFilter();
    });
    // Filter jenis menu
    $('#filterJenisMenu').on('change', function() {
        applyMenuFilter();
    });
    // Reset pencarian dan filter
    $('#btnResetMenuFilter').on('click', function() {
        $('#searchMenu').val('');
        $('#filterJenisMenu').val('');
        $('.menu-filter-hidden').removeClass('menu-filter-hidden');
        // Pulihkan semua daftar agar seluruh menu terlihat.
        $('.menu-main-body, .menu-child-list').show();
        $('.btn-toggle-main i, .btn-toggle-child i').removeClass('fa-chevron-down').addClass('fa-chevron-up');
        applyMenuFilter();
    });

    

    $(function () {

        console.log(
            'MAIN LIST:',
            $('.menu-main-list').length
        );

        console.log(
            'MAIN ITEM:',
            $('.menu-sortable-item').length
        );

        console.log(
            'DRAG HANDLE:',
            $('.menu-drag-handle').length
        );

        $('.menu-main-list').sortable({
            items: '.menu-sortable-item',
            axis: 'y',
            tolerance: 'pointer'
        });

    });


    

});
</script>

</body>
</html>
