#!/bin/bash
mysql -h localhost -u root -ptoor << EOF
use hmsdb_bak;

#CLEAR
DELETE FROM acc_mutasi_grg_detail_datar
WHERE periode_th = YEAR(SUBDATE$replace_currDate) AND periode_bln = MONTH(SUBDATE$replace_currDate);

#SALDO AWAL
INSERT INTO acc_mutasi_grg_detail_datar (periode_th, periode_bln, dept_id_mutasi, kode_produk, lot, nama_produk, s_awal_lot, s_awal_qty1, s_awal_qty1_uom, s_awal_qty2, s_awal_qty2_uom, s_awal_qty_opname, s_awal_qty_opname_uom)
SELECT YEAR(SUBDATE$replace_currDate) as periode_th, MONTH(SUBDATE$replace_currDate) as periode_bln, 
SUBSTRING_INDEX(lokasi,'/',1) as dept_id_mutasi, 
kode_produk, lot, nama_produk, 
count(lot), 
COALESCE(SUM(qty),0) as qty1, COALESCE(uom,'') as qty1_uom, 
COALESCE(SUM(qty2),0) as qty2, COALESCE(uom2,'') as qty2_uom2, 
COALESCE(SUM(qty_opname),0) as qty_opname, COALESCE(uom_opname,'') as uom_opname
FROM acc_stock_quant_eom sq
INNER JOIN departemen d ON d.stock_location=sq.lokasi
WHERE YEAR(tanggal) = YEAR(SUBDATE$replace_currDate - INTERVAL 1 MONTH)
AND MONTH(tanggal) = MONTH(SUBDATE$replace_currDate - INTERVAL 1 MONTH)
AND sq.lokasi='GRG/Stock'
GROUP BY kode_produk, lot
ORDER BY lot;

DROP PROCEDURE IF EXISTS LoopIn;
DELIMITER $$
CREATE PROCEDURE LoopIn()
BEGIN
	
	DECLARE x  INT;
	DECLARE seq_ VARCHAR(255); 
	SET x = 0;
	loop_label:  LOOP
		
		SET  x = x + 1;

		SET seq_ =  CONCAT('in',x);
		
		#IN insert ignore corak
		INSERT IGNORE INTO acc_mutasi_grg_detail_datar (periode_th, periode_bln, dept_id_mutasi, kode_produk, lot, nama_produk)
		SELECT YEAR(SUBDATE$replace_currDate) as periode_th, MONTH(SUBDATE$replace_currDate) as periode_bln,
		'GRG' as dept_id_mutasi,
		smi.kode_produk, smi.lot, smi.nama_produk
		FROM acc_stock_move_items smi 
		INNER JOIN acc_dept_mutasi dm ON smi.dept_id_mutasi=dm.dept_id AND dm.seq=seq_
		WHERE smi.dept_id_mutasi = 'GRG'
		AND smi.dept_id_dari = dm.dept_id_dari
		AND smi.dept_id_tujuan = dm.dept_id_tujuan
		AND smi.tanggal_transaksi >= CONCAT(date_add(date_add(LAST_DAY(SUBDATE$replace_currDate),interval 1 DAY),interval -1 MONTH), ' 00:00:000')
		AND smi.tanggal_transaksi <= CONCAT(SUBDATE$replace_currDate,' 23:59:59')
		GROUP BY kode_produk, lot;
		
		#IN buat temp
		INSERT INTO acc_mutasi_tmp_datar
		SELECT YEAR(SUBDATE$replace_currDate) as periode_th, MONTH(SUBDATE$replace_currDate) as periode_bln,
		'GRG' as dept_id_mutasi, smi.kode_produk, smi.lot, smi.nama_produk,
		COUNT(*) as jml_lot,
		SUM(smi.qty) as qty1, COALESCE(uom,'') as qty1_uom, 
		SUM(smi.qty2) as qty2, COALESCE(uom2,'') as qty2_uom, 
		SUM(smi.qty_opname) as qty_opname, COALESCE(uom_opname,'') as opname_uom,
		seq_ as seq,
		'' as kode_transaksi,
		0 as id_type_adjustment
		FROM acc_stock_move_items smi 
		INNER JOIN acc_dept_mutasi dm ON smi.dept_id_mutasi=dm.dept_id AND dm.seq=seq_
		WHERE smi.dept_id_mutasi = 'GRG'
		AND smi.dept_id_dari = dm.dept_id_dari
		AND smi.dept_id_tujuan = dm.dept_id_tujuan
		AND smi.tanggal_transaksi >= CONCAT(date_add(date_add(LAST_DAY(SUBDATE$replace_currDate),interval 1 DAY),interval -1 MONTH), ' 00:00:000')
		AND smi.tanggal_transaksi <= CONCAT(SUBDATE$replace_currDate,' 23:59:59')
		GROUP BY smi.kode_produk, smi.lot;

		#IN update mutasi
		IF x =1 THEN
			UPDATE acc_mutasi_grg_detail_datar a
			INNER JOIN acc_mutasi_tmp_datar b
			ON a.periode_th = b.periode_th AND a.periode_bln = b.periode_bln AND a.dept_id_mutasi = b.dept_id_mutasi AND a.kode_produk = b.kode_produk AND a.lot = b.lot
			SET
			a.in1_lot=b.jml_lot,
			a.in1_qty1=b.qty1,
			a.in1_qty1_uom=b.qty1_uom,
			a.in1_qty2=b.qty2,
			a.in1_qty2_uom=b.qty2_uom,
			a.in1_qty_opname=b.qty_opname,
			a.in1_qty_opname_uom=b.qty_opname_uom;
		ELSEIF x =2 THEN
			UPDATE acc_mutasi_grg_detail_datar a
			INNER JOIN acc_mutasi_tmp_datar b
			ON a.periode_th = b.periode_th AND a.periode_bln = b.periode_bln AND a.dept_id_mutasi = b.dept_id_mutasi AND a.kode_produk = b.kode_produk AND a.lot = b.lot
			SET
			a.in2_lot=b.jml_lot,
			a.in2_qty1=b.qty1,
			a.in2_qty1_uom=b.qty1_uom,
			a.in2_qty2=b.qty2,
			a.in2_qty2_uom=b.qty2_uom,
			a.in2_qty_opname=b.qty_opname,
			a.in2_qty_opname_uom=b.qty_opname_uom;
		ELSEIF x =3 THEN
			UPDATE acc_mutasi_grg_detail_datar a
			INNER JOIN acc_mutasi_tmp_datar b
			ON a.periode_th = b.periode_th AND a.periode_bln = b.periode_bln AND a.dept_id_mutasi = b.dept_id_mutasi AND a.kode_produk = b.kode_produk AND a.lot = b.lot
			SET
			a.in3_lot=b.jml_lot,
			a.in3_qty1=b.qty1,
			a.in3_qty1_uom=b.qty1_uom,
			a.in3_qty2=b.qty2,
			a.in3_qty2_uom=b.qty2_uom,
			a.in3_qty_opname=b.qty_opname,
			a.in3_qty_opname_uom=b.qty_opname_uom;
		ELSEIF x =4 THEN
			UPDATE acc_mutasi_grg_detail_datar a
			INNER JOIN acc_mutasi_tmp_datar b
			ON a.periode_th = b.periode_th AND a.periode_bln = b.periode_bln AND a.dept_id_mutasi = b.dept_id_mutasi AND a.kode_produk = b.kode_produk AND a.lot = b.lot
			SET
			a.in4_lot=b.jml_lot,
			a.in4_qty1=b.qty1,
			a.in4_qty1_uom=b.qty1_uom,
			a.in4_qty2=b.qty2,
			a.in4_qty2_uom=b.qty2_uom,
			a.in4_qty_opname=b.qty_opname,
			a.in4_qty_opname_uom=b.qty_opname_uom;
		ELSEIF x =5 THEN
			UPDATE acc_mutasi_grg_detail_datar a
			INNER JOIN acc_mutasi_tmp_datar b
			ON a.periode_th = b.periode_th AND a.periode_bln = b.periode_bln AND a.dept_id_mutasi = b.dept_id_mutasi AND a.kode_produk = b.kode_produk AND a.lot = b.lot
			SET
			a.in5_lot=b.jml_lot,
			a.in5_qty1=b.qty1,
			a.in5_qty1_uom=b.qty1_uom,
			a.in5_qty2=b.qty2,
			a.in5_qty2_uom=b.qty2_uom,
			a.in5_qty_opname=b.qty_opname,
			a.in5_qty_opname_uom=b.qty_opname_uom;
		ELSEIF x =6 THEN
			UPDATE acc_mutasi_grg_detail_datar a
			INNER JOIN acc_mutasi_tmp_datar b
			ON a.periode_th = b.periode_th AND a.periode_bln = b.periode_bln AND a.dept_id_mutasi = b.dept_id_mutasi AND a.kode_produk = b.kode_produk AND a.lot = b.lot
			SET
			a.in6_lot=b.jml_lot,
			a.in6_qty1=b.qty1,
			a.in6_qty1_uom=b.qty1_uom,
			a.in6_qty2=b.qty2,
			a.in6_qty2_uom=b.qty2_uom,
			a.in6_qty_opname=b.qty_opname,
			a.in6_qty_opname_uom=b.qty_opname_uom;
		ELSEIF x =7 THEN
			UPDATE acc_mutasi_grg_detail_datar a
			INNER JOIN acc_mutasi_tmp_datar b
			ON a.periode_th = b.periode_th AND a.periode_bln = b.periode_bln AND a.dept_id_mutasi = b.dept_id_mutasi AND a.kode_produk = b.kode_produk AND a.lot = b.lot
			SET
			a.in7_lot=b.jml_lot,
			a.in7_qty1=b.qty1,
			a.in7_qty1_uom=b.qty1_uom,
			a.in7_qty2=b.qty2,
			a.in7_qty2_uom=b.qty2_uom,
			a.in7_qty_opname=b.qty_opname,
			a.in7_qty_opname_uom=b.qty_opname_uom;
		END IF;
		
		#IN delete temp
		DELETE FROM acc_mutasi_tmp_datar;
		
		IF  x = 7 THEN 
			LEAVE  loop_label;
		END  IF;
		
	END LOOP;
	
END$$
DELIMITER ;
CALL LoopIn();

DROP PROCEDURE IF EXISTS LoopOut;
DELIMITER $$
CREATE PROCEDURE LoopOut()
BEGIN

	DECLARE x  INT;
	DECLARE seq_ VARCHAR(255); 
	SET x = 0;
	loop_label:  LOOP
		
		SET  x = x + 1;

		SET seq_ =  CONCAT('out',x);
		
		#OUT insert ignore corak
		INSERT IGNORE INTO acc_mutasi_grg_detail_datar (periode_th, periode_bln, dept_id_mutasi, kode_produk, lot, nama_produk)
		SELECT YEAR(SUBDATE$replace_currDate) as periode_th, MONTH(SUBDATE$replace_currDate) as periode_bln,
		'GRG' as dept_id_mutasi,
		smi.kode_produk, smi.lot, smi.nama_produk
		FROM acc_stock_move_items smi 
		INNER JOIN acc_dept_mutasi dm ON smi.dept_id_mutasi=dm.dept_id AND dm.seq=seq_
		WHERE smi.dept_id_mutasi = 'GRG'
		AND smi.dept_id_dari = dm.dept_id_dari
		AND smi.dept_id_tujuan = dm.dept_id_tujuan
		AND smi.tanggal_transaksi >= CONCAT(date_add(date_add(LAST_DAY(SUBDATE$replace_currDate),interval 1 DAY),interval -1 MONTH), ' 00:00:000')
		AND smi.tanggal_transaksi <= CONCAT(SUBDATE$replace_currDate,' 23:59:59')
		GROUP BY kode_produk, lot;
		
		#OUT buat temp
		INSERT INTO acc_mutasi_tmp_datar
		SELECT YEAR(SUBDATE$replace_currDate) as periode_th, MONTH(SUBDATE$replace_currDate) as periode_bln,
		'GRG' as dept_id_mutasi, smi.kode_produk, smi.lot, smi.nama_produk,
		COUNT(*) as jml_lot,
		SUM(smi.qty) as qty1, COALESCE(uom,'') as qty1_uom, 
		SUM(smi.qty2) as qty2, COALESCE(uom2,'') as qty2_uom, 
		SUM(smi.qty_opname) as qty_opname, COALESCE(uom_opname,'') as opname_uom,
		seq_ as seq,
		'' as kode_transaksi,
		0 as id_type_adjustment
		FROM acc_stock_move_items smi 
		INNER JOIN acc_dept_mutasi dm ON smi.dept_id_mutasi=dm.dept_id AND dm.seq=seq_
		WHERE smi.dept_id_mutasi = 'GRG'
		AND smi.dept_id_dari = dm.dept_id_dari
		AND smi.dept_id_tujuan = dm.dept_id_tujuan
		AND smi.tanggal_transaksi >= CONCAT(date_add(date_add(LAST_DAY(SUBDATE$replace_currDate),interval 1 DAY),interval -1 MONTH), ' 00:00:000')
		AND smi.tanggal_transaksi <= CONCAT(SUBDATE$replace_currDate,' 23:59:59')
		GROUP BY smi.kode_produk, smi.lot;

		#OUT update mutasi
		IF x =1 THEN
			UPDATE acc_mutasi_grg_detail_datar a
			INNER JOIN acc_mutasi_tmp_datar b
			ON a.periode_th = b.periode_th AND a.periode_bln = b.periode_bln AND a.dept_id_mutasi = b.dept_id_mutasi AND a.kode_produk = b.kode_produk AND a.lot = b.lot
			SET
			a.out1_lot=b.jml_lot,
			a.out1_qty1=b.qty1,
			a.out1_qty1_uom=b.qty1_uom,
			a.out1_qty2=b.qty2,
			a.out1_qty2_uom=b.qty2_uom,
			a.out1_qty_opname=b.qty_opname,
			a.out1_qty_opname_uom=b.qty_opname_uom;
		ELSEIF x =2 THEN
			UPDATE acc_mutasi_grg_detail_datar a
			INNER JOIN acc_mutasi_tmp_datar b
			ON a.periode_th = b.periode_th AND a.periode_bln = b.periode_bln AND a.dept_id_mutasi = b.dept_id_mutasi AND a.kode_produk = b.kode_produk AND a.lot = b.lot
			SET
			a.out2_lot=b.jml_lot,
			a.out2_qty1=b.qty1,
			a.out2_qty1_uom=b.qty1_uom,
			a.out2_qty2=b.qty2,
			a.out2_qty2_uom=b.qty2_uom,
			a.out2_qty_opname=b.qty_opname,
			a.out2_qty_opname_uom=b.qty_opname_uom;
		ELSEIF x =3 THEN
			UPDATE acc_mutasi_grg_detail_datar a
			INNER JOIN acc_mutasi_tmp_datar b
			ON a.periode_th = b.periode_th AND a.periode_bln = b.periode_bln AND a.dept_id_mutasi = b.dept_id_mutasi AND a.kode_produk = b.kode_produk AND a.lot = b.lot
			SET
			a.out3_lot=b.jml_lot,
			a.out3_qty1=b.qty1,
			a.out3_qty1_uom=b.qty1_uom,
			a.out3_qty2=b.qty2,
			a.out3_qty2_uom=b.qty2_uom,
			a.out3_qty_opname=b.qty_opname,
			a.out3_qty_opname_uom=b.qty_opname_uom;
		ELSEIF x =4 THEN
			UPDATE acc_mutasi_grg_detail_datar a
			INNER JOIN acc_mutasi_tmp_datar b
			ON a.periode_th = b.periode_th AND a.periode_bln = b.periode_bln AND a.dept_id_mutasi = b.dept_id_mutasi AND a.kode_produk = b.kode_produk AND a.lot = b.lot
			SET
			a.out4_lot=b.jml_lot,
			a.out4_qty1=b.qty1,
			a.out4_qty1_uom=b.qty1_uom,
			a.out4_qty2=b.qty2,
			a.out4_qty2_uom=b.qty2_uom,
			a.out4_qty_opname=b.qty_opname,
			a.out4_qty_opname_uom=b.qty_opname_uom;
		ELSEIF x =5 THEN
			UPDATE acc_mutasi_grg_detail_datar a
			INNER JOIN acc_mutasi_tmp_datar b
			ON a.periode_th = b.periode_th AND a.periode_bln = b.periode_bln AND a.dept_id_mutasi = b.dept_id_mutasi AND a.kode_produk = b.kode_produk AND a.lot = b.lot
			SET
			a.out5_lot=b.jml_lot,
			a.out5_qty1=b.qty1,
			a.out5_qty1_uom=b.qty1_uom,
			a.out5_qty2=b.qty2,
			a.out5_qty2_uom=b.qty2_uom,
			a.out5_qty_opname=b.qty_opname,
			a.out5_qty_opname_uom=b.qty_opname_uom;
		ELSEIF x =6 THEN
			UPDATE acc_mutasi_grg_detail_datar a
			INNER JOIN acc_mutasi_tmp_datar b
			ON a.periode_th = b.periode_th AND a.periode_bln = b.periode_bln AND a.dept_id_mutasi = b.dept_id_mutasi AND a.kode_produk = b.kode_produk AND a.lot = b.lot
			SET
			a.out6_lot=b.jml_lot,
			a.out6_qty1=b.qty1,
			a.out6_qty1_uom=b.qty1_uom,
			a.out6_qty2=b.qty2,
			a.out6_qty2_uom=b.qty2_uom,
			a.out6_qty_opname=b.qty_opname,
			a.out6_qty_opname_uom=b.qty_opname_uom;
		ELSEIF x =7 THEN
			UPDATE acc_mutasi_grg_detail_datar a
			INNER JOIN acc_mutasi_tmp_datar b
			ON a.periode_th = b.periode_th AND a.periode_bln = b.periode_bln AND a.dept_id_mutasi = b.dept_id_mutasi AND a.kode_produk = b.kode_produk AND a.lot = b.lot
			SET
			a.out7_lot=b.jml_lot,
			a.out7_qty1=b.qty1,
			a.out7_qty1_uom=b.qty1_uom,
			a.out7_qty2=b.qty2,
			a.out7_qty2_uom=b.qty2_uom,
			a.out7_qty_opname=b.qty_opname,
			a.out7_qty_opname_uom=b.qty_opname_uom;
		ELSEIF x =8 THEN
			UPDATE acc_mutasi_grg_detail_datar a
			INNER JOIN acc_mutasi_tmp_datar b
			ON a.periode_th = b.periode_th AND a.periode_bln = b.periode_bln AND a.dept_id_mutasi = b.dept_id_mutasi AND a.kode_produk = b.kode_produk AND a.lot = b.lot
			SET
			a.out8_lot=b.jml_lot,
			a.out8_qty1=b.qty1,
			a.out8_qty1_uom=b.qty1_uom,
			a.out8_qty2=b.qty2,
			a.out8_qty2_uom=b.qty2_uom,
			a.out8_qty_opname=b.qty_opname,
			a.out8_qty_opname_uom=b.qty_opname_uom;
		END IF;
		
		#IN delete temp
		DELETE FROM acc_mutasi_tmp_datar;
		
		IF  x = 8 THEN 
			LEAVE  loop_label;
		END  IF;
		
	END LOOP;
	
END$$
DELIMITER ;
CALL LoopOut();

#ADJ_IN insert ignore corak
INSERT IGNORE INTO acc_mutasi_grg_detail_datar (periode_th, periode_bln, dept_id_mutasi, kode_produk, lot, nama_produk)
SELECT YEAR(SUBDATE$replace_currDate) as periode_th, MONTH(SUBDATE$replace_currDate) as periode_bln,
'GRG' as dept_id_mutasi,
smi.kode_produk, smi.lot, smi.nama_produk
FROM acc_stock_move_items smi 
WHERE smi.dept_id_mutasi = 'GRG'
AND smi.type = 'adj_in'
AND smi.tanggal_transaksi >= CONCAT(date_add(date_add(LAST_DAY(SUBDATE$replace_currDate),interval 1 DAY),interval -1 MONTH), ' 00:00:000')
AND smi.tanggal_transaksi <= CONCAT(SUBDATE$replace_currDate,' 23:59:59')
GROUP BY kode_produk, lot;

#ADJ_IN buat temp
INSERT INTO acc_mutasi_tmp_datar
SELECT YEAR(SUBDATE$replace_currDate) as periode_th, MONTH(SUBDATE$replace_currDate) as periode_bln,
'GRG' as dept_id_mutasi, smi.kode_produk, smi.lot, smi.nama_produk,
COUNT(*) as jml_lot,
SUM(smi.qty) as qty1, COALESCE(smi.uom,'') as qty1_uom, 
SUM(smi.qty2) as qty2, COALESCE(smi.uom2,'') as qty2_uom, 
SUM(smi.qty_opname) as qty_opname, COALESCE(uom_opname,'') as opname_uom,
'ADJ_IN' as seq,
smi.kode_transaksi,
adj.id_type_adjustment
FROM acc_stock_move_items smi 
INNER JOIN adjustment adj ON smi.kode_transaksi = adj.kode_adjustment
INNER JOIN adjustment_items adji ON adj.kode_adjustment = adji.kode_adjustment AND adji.lot = smi.lot AND adji.quant_id = smi.quant_id
WHERE smi.dept_id_mutasi = 'GRG'
AND smi.type = 'adj_in'
AND smi.tanggal_transaksi >= CONCAT(date_add(date_add(LAST_DAY(SUBDATE$replace_currDate),interval 1 DAY),interval -1 MONTH), ' 00:00:000')
AND smi.tanggal_transaksi <= CONCAT(SUBDATE$replace_currDate,' 23:59:59')
AND (adji.qty_move > 0 OR adji.qty2_move > 0)
GROUP BY smi.kode_produk,smi.lot;

#ADJ_IN mutasi
UPDATE acc_mutasi_grg_detail_datar a
INNER JOIN acc_mutasi_tmp_datar b
ON a.periode_th = b.periode_th AND a.periode_bln = b.periode_bln AND a.dept_id_mutasi = b.dept_id_mutasi AND a.kode_produk = b.kode_produk AND a.lot = b.lot
SET
a.adj_in_lot=b.jml_lot,
a.adj_in_qty1=b.qty1,
a.adj_in_qty1_uom=b.qty1_uom,
a.adj_in_qty2=b.qty2,
a.adj_in_qty2_uom=b.qty2_uom,
a.adj_in_qty_opname=b.qty_opname,
a.adj_in_qty_opname_uom=b.qty_opname_uom,
a.1_in_lot = IF(b.id_type_adjustment=1, b.jml_lot, 1_in_lot),
a.1_in_qty1 = IF(b.id_type_adjustment=1, b.qty1, 1_in_qty1),
a.1_in_qty1_uom = IF(b.id_type_adjustment=1, b.qty1_uom, 1_in_qty1_uom),
a.1_in_qty2 = IF(b.id_type_adjustment=1, b.qty2, 1_in_qty2),
a.1_in_qty2_uom = IF(b.id_type_adjustment=1, b.qty2_uom, 1_in_qty2_uom),
a.1_in_qty_opname = IF(b.id_type_adjustment=1, b.qty_opname, 1_in_qty_opname),
a.1_in_qty_opname_uom = IF(b.id_type_adjustment=1, b.qty_opname_uom, 1_in_qty_opname_uom),
a.2_in_lot = IF(b.id_type_adjustment=2, b.jml_lot, 2_in_lot),
a.2_in_qty1 = IF(b.id_type_adjustment=2, b.qty1, 2_in_qty1),
a.2_in_qty1_uom = IF(b.id_type_adjustment=2, b.qty1_uom, 2_in_qty1_uom),
a.2_in_qty2 = IF(b.id_type_adjustment=2, b.qty2, 2_in_qty2),
a.2_in_qty2_uom = IF(b.id_type_adjustment=2, b.qty2_uom, 2_in_qty2_uom),
a.2_in_qty_opname = IF(b.id_type_adjustment=2, b.qty_opname, 2_in_qty_opname),
a.2_in_qty_opname_uom = IF(b.id_type_adjustment=2, b.qty_opname_uom, 2_in_qty_opname_uom),
a.3_in_lot = IF(b.id_type_adjustment=3, b.jml_lot, 3_in_lot),
a.3_in_qty1 = IF(b.id_type_adjustment=3, b.qty1, 3_in_qty1),
a.3_in_qty1_uom = IF(b.id_type_adjustment=3, b.qty1_uom, 3_in_qty1_uom),
a.3_in_qty2 = IF(b.id_type_adjustment=3, b.qty2, 3_in_qty2),
a.3_in_qty2_uom = IF(b.id_type_adjustment=3, b.qty2_uom, 3_in_qty2_uom),
a.3_in_qty_opname = IF(b.id_type_adjustment=3, b.qty_opname, 3_in_qty_opname),
a.3_in_qty_opname_uom = IF(b.id_type_adjustment=3, b.qty_opname_uom, 3_in_qty_opname_uom),
a.4_in_lot = IF(b.id_type_adjustment=4, b.jml_lot, 4_in_lot),
a.4_in_qty1 = IF(b.id_type_adjustment=4, b.qty1, 4_in_qty1),
a.4_in_qty1_uom = IF(b.id_type_adjustment=4, b.qty1_uom, 4_in_qty1_uom),
a.4_in_qty2 = IF(b.id_type_adjustment=4, b.qty2, 4_in_qty2),
a.4_in_qty2_uom = IF(b.id_type_adjustment=4, b.qty2_uom, 4_in_qty2_uom),
a.4_in_qty_opname = IF(b.id_type_adjustment=4, b.qty_opname, 4_in_qty_opname),
a.4_in_qty_opname_uom = IF(b.id_type_adjustment=4, b.qty_opname_uom, 4_in_qty_opname_uom),
a.5_in_lot = IF(b.id_type_adjustment=5, b.jml_lot, 5_in_lot),
a.5_in_qty1 = IF(b.id_type_adjustment=5, b.qty1, 5_in_qty1),
a.5_in_qty1_uom = IF(b.id_type_adjustment=5, b.qty1_uom, 5_in_qty1_uom),
a.5_in_qty2 = IF(b.id_type_adjustment=5, b.qty2, 5_in_qty2),
a.5_in_qty2_uom = IF(b.id_type_adjustment=5, b.qty2_uom, 5_in_qty2_uom),
a.5_in_qty_opname = IF(b.id_type_adjustment=5, b.qty_opname, 5_in_qty_opname),
a.5_in_qty_opname_uom = IF(b.id_type_adjustment=5, b.qty_opname_uom, 5_in_qty_opname_uom),
a.6_in_lot = IF(b.id_type_adjustment=6, b.jml_lot, 6_in_lot),
a.6_in_qty1 = IF(b.id_type_adjustment=6, b.qty1, 6_in_qty1),
a.6_in_qty1_uom = IF(b.id_type_adjustment=6, b.qty1_uom, 6_in_qty1_uom),
a.6_in_qty2 = IF(b.id_type_adjustment=6, b.qty2, 6_in_qty2),
a.6_in_qty2_uom = IF(b.id_type_adjustment=6, b.qty2_uom, 6_in_qty2_uom),
a.6_in_qty_opname = IF(b.id_type_adjustment=6, b.qty_opname, 6_in_qty_opname),
a.6_in_qty_opname_uom = IF(b.id_type_adjustment=6, b.qty_opname_uom, 6_in_qty_opname_uom),
a.7_in_lot = IF(b.id_type_adjustment=7, b.jml_lot, 7_in_lot),
a.7_in_qty1 = IF(b.id_type_adjustment=7, b.qty1, 7_in_qty1),
a.7_in_qty1_uom = IF(b.id_type_adjustment=7, b.qty1_uom, 7_in_qty1_uom),
a.7_in_qty2 = IF(b.id_type_adjustment=7, b.qty2, 7_in_qty2),
a.7_in_qty2_uom = IF(b.id_type_adjustment=7, b.qty2_uom, 7_in_qty2_uom),
a.7_in_qty_opname = IF(b.id_type_adjustment=7, b.qty_opname, 7_in_qty_opname),
a.7_in_qty_opname_uom = IF(b.id_type_adjustment=7, b.qty_opname_uom, 7_in_qty_opname_uom);

#ADJ_IN delete temp
DELETE FROM acc_mutasi_tmp_datar;

#ADJ_OUT insert ignore corak
INSERT IGNORE INTO acc_mutasi_grg_detail_datar (periode_th, periode_bln, dept_id_mutasi, kode_produk, lot, nama_produk)
SELECT YEAR(SUBDATE$replace_currDate) as periode_th, MONTH(SUBDATE$replace_currDate) as periode_bln,
'GRG' as dept_id_mutasi,
smi.kode_produk, smi.lot, smi.nama_produk
FROM acc_stock_move_items smi 
WHERE smi.dept_id_mutasi = 'GRG'
AND smi.type = 'adj_out'
AND smi.tanggal_transaksi >= CONCAT(date_add(date_add(LAST_DAY(SUBDATE$replace_currDate),interval 1 DAY),interval -1 MONTH), ' 00:00:000')
AND smi.tanggal_transaksi <= CONCAT(SUBDATE$replace_currDate,' 23:59:59')
GROUP BY kode_produk, lot;

#ADJ_OUT buat temp
INSERT INTO acc_mutasi_tmp_datar
SELECT YEAR(SUBDATE$replace_currDate) as periode_th, MONTH(SUBDATE$replace_currDate) as periode_bln,
'GRG' as dept_id_mutasi, smi.kode_produk, smi.lot, smi.nama_produk,
COUNT(*) as total_lot,
SUM(smi.qty) as qty1, COALESCE(smi.uom,'') as qty1_uom, 
SUM(smi.qty2) as qty2, COALESCE(smi.uom2,'') as qty2_uom, 
SUM(smi.qty_opname) as qty_opname, COALESCE(uom_opname,'') as opname_uom,
'ADJ_OUT' as seq,
smi.kode_transaksi,
adj.id_type_adjustment
FROM acc_stock_move_items smi 
INNER JOIN adjustment adj ON smi.kode_transaksi = adj.kode_adjustment
INNER JOIN adjustment_items adji ON adj.kode_adjustment = adji.kode_adjustment AND adji.lot = smi.lot AND adji.quant_id = smi.quant_id
WHERE smi.dept_id_mutasi = 'GRG'
AND smi.type = 'adj_out'
AND smi.tanggal_transaksi >= CONCAT(date_add(date_add(LAST_DAY(SUBDATE$replace_currDate),interval 1 DAY),interval -1 MONTH), ' 00:00:000')
AND smi.tanggal_transaksi <= CONCAT(SUBDATE$replace_currDate,' 23:59:59')
AND (adji.qty_move < 0 OR adji.qty2_move < 0)
GROUP BY smi.kode_produk,smi.lot;

#ADJ_OUT mutasi
UPDATE acc_mutasi_grg_detail_datar a
INNER JOIN acc_mutasi_tmp_datar b
ON a.periode_th = b.periode_th AND a.periode_bln = b.periode_bln AND a.dept_id_mutasi = b.dept_id_mutasi AND a.kode_produk = b.kode_produk AND a.lot = b.lot
SET
a.adj_out_lot=b.jml_lot,
a.adj_out_qty1=b.qty1,
a.adj_out_qty1_uom=b.qty1_uom,
a.adj_out_qty2=b.qty2,
a.adj_out_qty2_uom=b.qty2_uom,
a.adj_out_qty_opname=b.qty_opname,
a.adj_out_qty_opname_uom=b.qty_opname_uom,
a.1_out_lot = IF(b.id_type_adjustment=1, b.jml_lot, 1_out_lot),
a.1_out_qty1 = IF(b.id_type_adjustment=1, b.qty1, 1_out_qty1),
a.1_out_qty1_uom = IF(b.id_type_adjustment=1, b.qty1_uom, 1_out_qty1_uom),
a.1_out_qty2 = IF(b.id_type_adjustment=1, b.qty2, 1_out_qty2),
a.1_out_qty2_uom = IF(b.id_type_adjustment=1, b.qty2_uom, 1_out_qty2_uom),
a.1_out_qty_opname = IF(b.id_type_adjustment=1, b.qty_opname, 1_out_qty_opname),
a.1_out_qty_opname_uom = IF(b.id_type_adjustment=1, b.qty_opname_uom, 1_out_qty_opname_uom),
a.2_out_lot = IF(b.id_type_adjustment=2, b.jml_lot, 2_out_lot),
a.2_out_qty1 = IF(b.id_type_adjustment=2, b.qty1, 2_out_qty1),
a.2_out_qty1_uom = IF(b.id_type_adjustment=2, b.qty1_uom, 2_out_qty1_uom),
a.2_out_qty2 = IF(b.id_type_adjustment=2, b.qty2, 2_out_qty2),
a.2_out_qty2_uom = IF(b.id_type_adjustment=2, b.qty2_uom, 2_out_qty2_uom),
a.2_out_qty_opname = IF(b.id_type_adjustment=2, b.qty_opname, 2_out_qty_opname),
a.2_out_qty_opname_uom = IF(b.id_type_adjustment=2, b.qty_opname_uom, 2_out_qty_opname_uom),
a.3_out_lot = IF(b.id_type_adjustment=3, b.jml_lot, 3_out_lot),
a.3_out_qty1 = IF(b.id_type_adjustment=3, b.qty1, 3_out_qty1),
a.3_out_qty1_uom = IF(b.id_type_adjustment=3, b.qty1_uom, 3_out_qty1_uom),
a.3_out_qty2 = IF(b.id_type_adjustment=3, b.qty2, 3_out_qty2),
a.3_out_qty2_uom = IF(b.id_type_adjustment=3, b.qty2_uom, 3_out_qty2_uom),
a.3_out_qty_opname = IF(b.id_type_adjustment=3, b.qty_opname, 3_out_qty_opname),
a.3_out_qty_opname_uom = IF(b.id_type_adjustment=3, b.qty_opname_uom, 3_out_qty_opname_uom),
a.4_out_lot = IF(b.id_type_adjustment=4, b.jml_lot, 4_out_lot),
a.4_out_qty1 = IF(b.id_type_adjustment=4, b.qty1, 4_out_qty1),
a.4_out_qty1_uom = IF(b.id_type_adjustment=4, b.qty1_uom, 4_out_qty1_uom),
a.4_out_qty2 = IF(b.id_type_adjustment=4, b.qty2, 4_out_qty2),
a.4_out_qty2_uom = IF(b.id_type_adjustment=4, b.qty2_uom, 4_out_qty2_uom),
a.4_out_qty_opname = IF(b.id_type_adjustment=4, b.qty_opname, 4_out_qty_opname),
a.4_out_qty_opname_uom = IF(b.id_type_adjustment=4, b.qty_opname_uom, 4_out_qty_opname_uom),
a.5_out_lot = IF(b.id_type_adjustment=5, b.jml_lot, 5_out_lot),
a.5_out_qty1 = IF(b.id_type_adjustment=5, b.qty1, 5_out_qty1),
a.5_out_qty1_uom = IF(b.id_type_adjustment=5, b.qty1_uom, 5_out_qty1_uom),
a.5_out_qty2 = IF(b.id_type_adjustment=5, b.qty2, 5_out_qty2),
a.5_out_qty2_uom = IF(b.id_type_adjustment=5, b.qty2_uom, 5_out_qty2_uom),
a.5_out_qty_opname = IF(b.id_type_adjustment=5, b.qty_opname, 5_out_qty_opname),
a.5_out_qty_opname_uom = IF(b.id_type_adjustment=5, b.qty_opname_uom, 5_out_qty_opname_uom),
a.6_out_lot = IF(b.id_type_adjustment=6, b.jml_lot, 6_out_lot),
a.6_out_qty1 = IF(b.id_type_adjustment=6, b.qty1, 6_out_qty1),
a.6_out_qty1_uom = IF(b.id_type_adjustment=6, b.qty1_uom, 6_out_qty1_uom),
a.6_out_qty2 = IF(b.id_type_adjustment=6, b.qty2, 6_out_qty2),
a.6_out_qty2_uom = IF(b.id_type_adjustment=6, b.qty2_uom, 6_out_qty2_uom),
a.6_out_qty_opname = IF(b.id_type_adjustment=6, b.qty_opname, 6_out_qty_opname),
a.6_out_qty_opname_uom = IF(b.id_type_adjustment=6, b.qty_opname_uom, 6_out_qty_opname_uom),
a.7_out_lot = IF(b.id_type_adjustment=7, b.jml_lot, 7_out_lot),
a.7_out_qty1 = IF(b.id_type_adjustment=7, b.qty1, 7_out_qty1),
a.7_out_qty1_uom = IF(b.id_type_adjustment=7, b.qty1_uom, 7_out_qty1_uom),
a.7_out_qty2 = IF(b.id_type_adjustment=7, b.qty2, 7_out_qty2),
a.7_out_qty2_uom = IF(b.id_type_adjustment=7, b.qty2_uom, 7_out_qty2_uom),
a.7_out_qty_opname = IF(b.id_type_adjustment=7, b.qty_opname, 7_out_qty_opname),
a.7_in_qty_opname_uom = IF(b.id_type_adjustment=7, b.qty_opname_uom, 7_in_qty_opname_uom);

#ADJ_OUT delete temp
DELETE FROM acc_mutasi_tmp_datar;

#SALDO_AKHIR
UPDATE acc_mutasi_grg_detail_datar a
INNER JOIN mst_produk b ON a.kode_produk = b.kode_produk
SET
s_akhir_lot = s_awal_lot + in1_lot + in2_lot + in3_lot + in4_lot + in5_lot + in6_lot + in7_lot + adj_in_lot - (out1_lot + out2_lot + out3_lot + out4_lot + out5_lot + out6_lot + out7_lot + out8_lot + adj_out_lot),
s_akhir_qty1 = s_awal_qty1 + in1_qty1 + in2_qty1 + in3_qty1 + in4_qty1 + in5_qty1 + in6_qty1 + in7_qty1 + adj_in_qty1 - (out1_qty1 + out2_qty1 + out3_qty1 + out4_qty1 + out5_qty1 + out6_qty1 + out7_qty1 + out8_qty1 + adj_out_qty1),
s_akhir_qty1_uom = b.uom,
s_akhir_qty2 = s_awal_qty2 + in1_qty2 + in2_qty2 + in3_qty2 + in4_qty2 + in5_qty2 + in6_qty2 + in7_qty2 + adj_in_qty2 - (out1_qty2 + out2_qty2 + out3_qty2 + out4_qty2 + out5_qty2 + out6_qty2 + out7_qty2 + out8_qty2 + adj_out_qty2),
s_akhir_qty2_uom = b.uom_2,
s_akhir_qty_opname = s_awal_qty_opname + in1_qty_opname + in2_qty_opname + in3_qty_opname + in4_qty_opname + in5_qty_opname + in6_qty_opname + in7_qty_opname + adj_in_qty_opname - (out1_qty_opname + out2_qty_opname + out3_qty_opname + out4_qty_opname + out5_qty_opname + out6_qty_opname + out7_qty_opname + out8_qty_opname +  adj_out_qty_opname),
s_akhir_qty_opname_uom = 'Kg'
WHERE
periode_th = YEAR(SUBDATE$replace_currDate) AND
periode_bln = MONTH(SUBDATE$replace_currDate);

#ID_CATEGORY
UPDATE acc_mutasi_grg_detail_datar m
INNER JOIN mst_produk mp ON m.kode_produk = mp.kode_produk
LEFT JOIN mst_produk_parent mpp ON mp.id_parent = mpp.id
SET m.id_category = mp.id_category,
m.product_parent = COALESCE(mpp.nama,'')
WHERE m.periode_th = YEAR(SUBDATE$replace_currDate)
AND m.periode_bln = MONTH(SUBDATE$replace_currDate);

#JENIS KAIN
UPDATE acc_mutasi_grg_detail_datar m
INNER JOIN mst_produk mp ON m.kode_produk = mp.kode_produk
LEFT JOIN mst_jenis_kain mjk ON mp.id_jenis_kain = mjk.id
SET m.nama_jenis_kain = mjk.nama_jenis_kain
WHERE m.periode_th = YEAR(SUBDATE$replace_currDate)
AND m.periode_bln = MONTH(SUBDATE$replace_currDate);

INSERT INTO log_checksum
VALUE (now(), 'UPDATE|acc_mutasi_grg_detail_datar|Proses Koreksi Mundur');


