#!/bin/bash
mysql -h localhost -u root -ptoor << EOF
use hmsdb_bak;

#CLEAR
DELETE FROM acc_mutasi_tws_fg_detail
WHERE periode_th = YEAR(SUBDATE$replace_currDate) AND periode_bln = MONTH(SUBDATE$replace_currDate);

#SALDO AWAL
INSERT INTO acc_mutasi_tws_fg_detail (periode_th, periode_bln, posisi_mutasi, dept_id_mutasi, dept_id_dari, dept_id_tujuan, type, kode_transaksi, tanggal_transaksi, kode_produk, nama_produk, id_category, lot, qty, uom, qty2, uom2, qty_opname, uom_opname, origin, source_move, method, reff_note, sc, sales_group, mo)
SELECT YEAR(SUBDATE$replace_currDate) as periode_th, MONTH(SUBDATE$replace_currDate) as periode_bln,
'1.SALDO AWAL' as posisi_mutasi, 'TWS' as dept_id_mutasi, 'TWS' as dept_id_dari, 'TWS' as dept_id_tujuan, '' as type, '' as kode_transaksi, sq.tanggal as tanggal_transaksi,
sq.kode_produk, sq.nama_produk, mp.id_category, sq.lot, sq.qty, sq.uom, sq.qty2, sq.uom2, sq.qty_opname, sq.uom_opname, sq.reserve_origin, sq.reserve_move, '', sq.reff_note, sq.sales_order, sq.sales_group, ''
FROM acc_stock_quant_eom sq
INNER JOIN departemen d ON d.stock_location=sq.lokasi
INNER JOIN mst_produk mp ON sq.kode_produk = mp.kode_produk
WHERE YEAR(tanggal) = YEAR(SUBDATE$replace_currDate - INTERVAL 1 MONTH)
AND MONTH(tanggal) = MONTH(SUBDATE$replace_currDate - INTERVAL 1 MONTH)
AND mp.id_category IN (SELECT id FROM mst_category WHERE dept_id='TWS')
AND sq.lokasi='TWS/Stock';

#IN & ADJ_IN
INSERT INTO acc_mutasi_tws_fg_detail (periode_th, periode_bln, posisi_mutasi, dept_id_mutasi, dept_id_dari, dept_id_tujuan, type, kode_transaksi, tanggal_transaksi, kode_produk, nama_produk, id_category, lot, qty, uom, qty2, uom2, qty_opname, uom_opname, origin, source_move, method, reff_picking, sc, sales_group, mo, reff_note)
SELECT YEAR(SUBDATE$replace_currDate) as periode_th, MONTH(SUBDATE$replace_currDate) as periode_bln,
'2.IN' as posisi_mutasi, smi.dept_id_mutasi, smi.dept_id_dari, smi.dept_id_tujuan, smi.type, smi.kode_transaksi, smi.tanggal_transaksi, smi.kode_produk, smi.nama_produk, smi.id_category, smi.lot,
smi.qty, smi.uom, smi.qty2, smi.uom2, smi.qty_opname, smi.uom_opname, smi.origin, smi.source_move, smi.method, smi.reff_picking, smi.sc, smi.sales_group, smi.mo, smi.reff_note
FROM acc_stock_move_items smi 
WHERE smi.dept_id_mutasi = 'TWS'
AND smi.id_category IN (SELECT id FROM mst_category WHERE dept_id='TWS')
AND smi.type IN ('prod', 'in', 'adj_in')
AND smi.tanggal_transaksi >= CONCAT(date_add(date_add(LAST_DAY(SUBDATE$replace_currDate),interval 1 DAY),interval -1 MONTH), ' 00:00:000')
AND smi.tanggal_transaksi <= CONCAT(SUBDATE$replace_currDate,' 23:59:59');

#OUT & ADJ_OUT
INSERT INTO acc_mutasi_tws_fg_detail (periode_th, periode_bln, posisi_mutasi, dept_id_mutasi, dept_id_dari, dept_id_tujuan, type, kode_transaksi, tanggal_transaksi, kode_produk, nama_produk, id_category, lot, qty, uom, qty2, uom2, qty_opname, uom_opname, origin, source_move, method, reff_picking, sc, sales_group, mo, reff_note)
SELECT YEAR(SUBDATE$replace_currDate) as periode_th, MONTH(SUBDATE$replace_currDate) as periode_bln,
'3.OUT' as posisi_mutasi, smi.dept_id_mutasi, smi.dept_id_dari, smi.dept_id_tujuan, smi.type, smi.kode_transaksi, smi.tanggal_transaksi, smi.kode_produk, smi.nama_produk, smi.id_category, smi.lot,
smi.qty, smi.uom, smi.qty2, smi.uom2, smi.qty_opname, smi.uom_opname, smi.origin, smi.source_move, smi.method, smi.reff_picking, smi.sc, smi.sales_group, smi.mo, smi.reff_note
FROM acc_stock_move_items smi 
WHERE smi.dept_id_mutasi = 'TWS'
AND smi.id_category IN (SELECT id FROM mst_category WHERE dept_id='TWS')
AND smi.type IN ('out', 'adj_out')
AND smi.tanggal_transaksi >= CONCAT(date_add(date_add(LAST_DAY(SUBDATE$replace_currDate),interval 1 DAY),interval -1 MONTH), ' 00:00:000')
AND smi.tanggal_transaksi <= CONCAT(SUBDATE$replace_currDate,' 23:59:59');

#OUT WASTE
INSERT INTO acc_mutasi_tws_fg_detail (periode_th, periode_bln, posisi_mutasi, dept_id_mutasi, dept_id_dari, dept_id_tujuan, type, kode_transaksi, tanggal_transaksi, kode_produk, nama_produk, id_category, lot, qty, uom, qty2, uom2, qty_opname, uom_opname, origin, source_move, method, reff_picking, sc, sales_group, mo, reff_note)
SELECT YEAR(SUBDATE$replace_currDate) as periode_th, MONTH(SUBDATE$replace_currDate) as periode_bln,
'3.OUT' as posisi_mutasi, smi.dept_id_mutasi, smi.dept_id_dari, smi.dept_id_tujuan, smi.type, smi.kode_transaksi, smi.tanggal_transaksi, smi.kode_produk, smi.nama_produk, smi.id_category, smi.lot,
smi.qty, smi.uom, smi.qty2, smi.uom2, smi.qty_opname, smi.uom_opname, smi.origin, smi.source_move, smi.method, smi.reff_picking, smi.sc, smi.sales_group, smi.mo, smi.reff_note
FROM acc_stock_move_items smi 
WHERE smi.dept_id_mutasi = 'TWS'
AND smi.id_category IN (SELECT id FROM mst_category WHERE dept_id='TWS')
AND smi.type IN ('prod')
AND smi.dept_id_tujuan LIKE '%waste%'
AND smi.tanggal_transaksi >= CONCAT(date_add(date_add(LAST_DAY(SUBDATE$replace_currDate),interval 1 DAY),interval -1 MONTH), ' 00:00:000')
AND smi.tanggal_transaksi <= CONCAT(SUBDATE$replace_currDate,' 23:59:59');

INSERT INTO log_checksum
VALUE (now(), 'INSERT|acc_mutasi_tws_fg_detail|Proses Koreksi Mundur');
