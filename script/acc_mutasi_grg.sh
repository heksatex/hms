#!/bin/bash
mysql -h localhost -u root -ptoor << EOF
use hmsdb_bak;

#CLEAR
DELETE FROM acc_mutasi_grg
WHERE periode_th = YEAR(SUBDATE$replace_currDate) AND periode_bln = MONTH(SUBDATE$replace_currDate);

#SALDO AWAL
INSERT INTO acc_mutasi_grg (periode_th, periode_bln, dept_id_mutasi, kode_produk, nama_produk, s_awal_lot, s_awal_qty1, s_awal_qty1_uom, s_awal_qty2, s_awal_qty2_uom, s_awal_qty_opname, s_awal_qty_opname_uom)
SELECT YEAR(SUBDATE$replace_currDate) as periode_th, MONTH(SUBDATE$replace_currDate) as periode_bln, 
SUBSTRING_INDEX(lokasi,'/',1) as dept_id_mutasi, 
kode_produk, nama_produk, 
COUNT(*) as total_lot,
COALESCE(SUM(qty),0) as qty1, COALESCE(uom,'') as qty1_uom, 
COALESCE(SUM(qty2),0) as qty2, COALESCE(uom2,'') as qty2_uom2, 
COALESCE(SUM(qty_opname),0) as qty_opname, COALESCE(uom_opname,'') as uom_opname
FROM acc_stock_quant_eom sq
INNER JOIN departemen d ON d.stock_location=sq.lokasi
WHERE YEAR(tanggal) = YEAR(SUBDATE$replace_currDate - INTERVAL 1 MONTH)
AND MONTH(tanggal) = MONTH(SUBDATE$replace_currDate - INTERVAL 1 MONTH)
AND sq.lokasi='GRG/Stock'
GROUP BY kode_produk
ORDER BY nama_produk;

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
		INSERT IGNORE INTO acc_mutasi_grg (periode_th, periode_bln, dept_id_mutasi, kode_produk, nama_produk)
		SELECT YEAR(SUBDATE$replace_currDate) as periode_th, MONTH(SUBDATE$replace_currDate) as periode_bln,
		'GRG' as dept_id_mutasi,
		smi.kode_produk, smi.nama_produk
		FROM acc_stock_move_items smi 
		INNER JOIN acc_dept_mutasi dm ON smi.dept_id_mutasi=dm.dept_id AND dm.seq=seq_
		WHERE smi.dept_id_mutasi = 'GRG'
		AND smi.dept_id_dari = dm.dept_id_dari
		AND smi.dept_id_tujuan = dm.dept_id_tujuan
		AND smi.tanggal_transaksi >= CONCAT(date_add(date_add(LAST_DAY(SUBDATE$replace_currDate),interval 1 DAY),interval -1 MONTH), ' 00:00:000')
		AND smi.tanggal_transaksi <= CONCAT(SUBDATE$replace_currDate,' 23:59:59')
		GROUP BY kode_produk;
		
		#IN buat temp
		INSERT INTO acc_mutasi_tmp
		SELECT YEAR(SUBDATE$replace_currDate) as periode_th, MONTH(SUBDATE$replace_currDate) as periode_bln,
		'GRG' as dept_id_mutasi, smi.kode_produk, smi.nama_produk,
		COUNT(*) as total_lot,
		SUM(smi.qty) as qty1, COALESCE(uom,'') as qty1_uom, 
		SUM(smi.qty2) as qty2, COALESCE(uom2,'') as qty2_uom, 
		SUM(smi.qty_opname) as qty_opname, COALESCE(uom_opname,'') as opname_uom,
		seq_ as seq
		FROM acc_stock_move_items smi 
		INNER JOIN acc_dept_mutasi dm ON smi.dept_id_mutasi=dm.dept_id AND dm.seq=seq_
		WHERE smi.dept_id_mutasi = 'GRG'
		AND smi.dept_id_dari = dm.dept_id_dari
		AND smi.dept_id_tujuan = dm.dept_id_tujuan
		AND smi.tanggal_transaksi >= CONCAT(date_add(date_add(LAST_DAY(SUBDATE$replace_currDate),interval 1 DAY),interval -1 MONTH), ' 00:00:000')
		AND smi.tanggal_transaksi <= CONCAT(SUBDATE$replace_currDate,' 23:59:59')
		GROUP BY smi.kode_produk;
	
		#IN update mutasi
		IF x =1 THEN
			UPDATE acc_mutasi_grg a
			INNER JOIN acc_mutasi_tmp b
			ON a.periode_th = b.periode_th AND a.periode_bln = b.periode_bln AND a.dept_id_mutasi = b.dept_id_mutasi AND a.kode_produk = b.kode_produk
			SET
			a.in1_lot=b.lot,
			a.in1_qty1=b.qty1,
			a.in1_qty1_uom=b.qty1_uom,
			a.in1_qty2=b.qty2,
			a.in1_qty2_uom=b.qty2_uom,
			a.in1_qty_opname=b.qty_opname,
			a.in1_qty_opname_uom=b.qty_opname_uom;
		ELSEIF x =2 THEN
			UPDATE acc_mutasi_grg a
			INNER JOIN acc_mutasi_tmp b
			ON a.periode_th = b.periode_th AND a.periode_bln = b.periode_bln AND a.dept_id_mutasi = b.dept_id_mutasi AND a.kode_produk = b.kode_produk
			SET
			a.in2_lot=b.lot,
			a.in2_qty1=b.qty1,
			a.in2_qty1_uom=b.qty1_uom,
			a.in2_qty2=b.qty2,
			a.in2_qty2_uom=b.qty2_uom,
			a.in2_qty_opname=b.qty_opname,
			a.in2_qty_opname_uom=b.qty_opname_uom;
		ELSEIF x =3 THEN
			UPDATE acc_mutasi_grg a
			INNER JOIN acc_mutasi_tmp b
			ON a.periode_th = b.periode_th AND a.periode_bln = b.periode_bln AND a.dept_id_mutasi = b.dept_id_mutasi AND a.kode_produk = b.kode_produk
			SET
			a.in3_lot=b.lot,
			a.in3_qty1=b.qty1,
			a.in3_qty1_uom=b.qty1_uom,
			a.in3_qty2=b.qty2,
			a.in3_qty2_uom=b.qty2_uom,
			a.in3_qty_opname=b.qty_opname,
			a.in3_qty_opname_uom=b.qty_opname_uom;
		ELSEIF x =4 THEN
			UPDATE acc_mutasi_grg a
			INNER JOIN acc_mutasi_tmp b
			ON a.periode_th = b.periode_th AND a.periode_bln = b.periode_bln AND a.dept_id_mutasi = b.dept_id_mutasi AND a.kode_produk = b.kode_produk
			SET
			a.in4_lot=b.lot,
			a.in4_qty1=b.qty1,
			a.in4_qty1_uom=b.qty1_uom,
			a.in4_qty2=b.qty2,
			a.in4_qty2_uom=b.qty2_uom,
			a.in4_qty_opname=b.qty_opname,
			a.in4_qty_opname_uom=b.qty_opname_uom;
		ELSEIF x =5 THEN
			UPDATE acc_mutasi_grg a
			INNER JOIN acc_mutasi_tmp b
			ON a.periode_th = b.periode_th AND a.periode_bln = b.periode_bln AND a.dept_id_mutasi = b.dept_id_mutasi AND a.kode_produk = b.kode_produk
			SET
			a.in5_lot=b.lot,
			a.in5_qty1=b.qty1,
			a.in5_qty1_uom=b.qty1_uom,
			a.in5_qty2=b.qty2,
			a.in5_qty2_uom=b.qty2_uom,
			a.in5_qty_opname=b.qty_opname,
			a.in5_qty_opname_uom=b.qty_opname_uom;
		ELSEIF x =6 THEN
			UPDATE acc_mutasi_grg a
			INNER JOIN acc_mutasi_tmp b
			ON a.periode_th = b.periode_th AND a.periode_bln = b.periode_bln AND a.dept_id_mutasi = b.dept_id_mutasi AND a.kode_produk = b.kode_produk
			SET
			a.in6_lot=b.lot,
			a.in6_qty1=b.qty1,
			a.in6_qty1_uom=b.qty1_uom,
			a.in6_qty2=b.qty2,
			a.in6_qty2_uom=b.qty2_uom,
			a.in6_qty_opname=b.qty_opname,
			a.in6_qty_opname_uom=b.qty_opname_uom;
		ELSEIF x =7 THEN
			UPDATE acc_mutasi_grg a
			INNER JOIN acc_mutasi_tmp b
			ON a.periode_th = b.periode_th AND a.periode_bln = b.periode_bln AND a.dept_id_mutasi = b.dept_id_mutasi AND a.kode_produk = b.kode_produk
			SET
			a.in7_lot=b.lot,
			a.in7_qty1=b.qty1,
			a.in7_qty1_uom=b.qty1_uom,
			a.in7_qty2=b.qty2,
			a.in7_qty2_uom=b.qty2_uom,
			a.in7_qty_opname=b.qty_opname,
			a.in7_qty_opname_uom=b.qty_opname_uom;
		END IF;
		
		#IN delete temp
		DELETE FROM acc_mutasi_tmp;
		
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
		INSERT IGNORE INTO acc_mutasi_grg (periode_th, periode_bln, dept_id_mutasi, kode_produk, nama_produk)
		SELECT YEAR(SUBDATE$replace_currDate) as periode_th, MONTH(SUBDATE$replace_currDate) as periode_bln,
		'GRG' as dept_id_mutasi,
		smi.kode_produk, smi.nama_produk
		FROM acc_stock_move_items smi 
		INNER JOIN acc_dept_mutasi dm ON smi.dept_id_mutasi=dm.dept_id AND dm.seq=seq_
		WHERE smi.dept_id_mutasi = 'GRG'
		AND smi.dept_id_dari = dm.dept_id_dari
		AND smi.dept_id_tujuan = dm.dept_id_tujuan
		AND smi.tanggal_transaksi >= CONCAT(date_add(date_add(LAST_DAY(SUBDATE$replace_currDate),interval 1 DAY),interval -1 MONTH), ' 00:00:000')
		AND smi.tanggal_transaksi <= CONCAT(SUBDATE$replace_currDate,' 23:59:59')
		GROUP BY kode_produk;
		
		#OUT buat temp
		INSERT INTO acc_mutasi_tmp
		SELECT YEAR(SUBDATE$replace_currDate) as periode_th, MONTH(SUBDATE$replace_currDate) as periode_bln,
		'GRG' as dept_id_mutasi, smi.kode_produk, smi.nama_produk,
		COUNT(*) as total_lot,
		SUM(smi.qty) as qty1, COALESCE(uom,'') as qty1_uom, 
		SUM(smi.qty2) as qty2, COALESCE(uom2,'') as qty2_uom, 
		SUM(smi.qty_opname) as qty_opname, COALESCE(uom_opname,'') as opname_uom,
		seq_ as seq
		FROM acc_stock_move_items smi 
		INNER JOIN acc_dept_mutasi dm ON smi.dept_id_mutasi=dm.dept_id AND dm.seq=seq_
		WHERE smi.dept_id_mutasi = 'GRG'
		AND smi.dept_id_dari = dm.dept_id_dari
		AND smi.dept_id_tujuan = dm.dept_id_tujuan
		AND smi.tanggal_transaksi >= CONCAT(date_add(date_add(LAST_DAY(SUBDATE$replace_currDate),interval 1 DAY),interval -1 MONTH), ' 00:00:000')
		AND smi.tanggal_transaksi <= CONCAT(SUBDATE$replace_currDate,' 23:59:59')
		GROUP BY smi.kode_produk;
	
		#OUT update mutasi
		IF x =1 THEN
			UPDATE acc_mutasi_grg a
			INNER JOIN acc_mutasi_tmp b
			ON a.periode_th = b.periode_th AND a.periode_bln = b.periode_bln AND a.dept_id_mutasi = b.dept_id_mutasi AND a.kode_produk = b.kode_produk
			SET
			a.out1_lot=b.lot,
			a.out1_qty1=b.qty1,
			a.out1_qty1_uom=b.qty1_uom,
			a.out1_qty2=b.qty2,
			a.out1_qty2_uom=b.qty2_uom,
			a.out1_qty_opname=b.qty_opname,
			a.out1_qty_opname_uom=b.qty_opname_uom;
		ELSEIF x =2 THEN
			UPDATE acc_mutasi_grg a
			INNER JOIN acc_mutasi_tmp b
			ON a.periode_th = b.periode_th AND a.periode_bln = b.periode_bln AND a.dept_id_mutasi = b.dept_id_mutasi AND a.kode_produk = b.kode_produk
			SET
			a.out2_lot=b.lot,
			a.out2_qty1=b.qty1,
			a.out2_qty1_uom=b.qty1_uom,
			a.out2_qty2=b.qty2,
			a.out2_qty2_uom=b.qty2_uom,
			a.out2_qty_opname=b.qty_opname,
			a.out2_qty_opname_uom=b.qty_opname_uom;
		ELSEIF x =3 THEN
			UPDATE acc_mutasi_grg a
			INNER JOIN acc_mutasi_tmp b
			ON a.periode_th = b.periode_th AND a.periode_bln = b.periode_bln AND a.dept_id_mutasi = b.dept_id_mutasi AND a.kode_produk = b.kode_produk
			SET
			a.out3_lot=b.lot,
			a.out3_qty1=b.qty1,
			a.out3_qty1_uom=b.qty1_uom,
			a.out3_qty2=b.qty2,
			a.out3_qty2_uom=b.qty2_uom,
			a.out3_qty_opname=b.qty_opname,
			a.out3_qty_opname_uom=b.qty_opname_uom;
		ELSEIF x =4 THEN
			UPDATE acc_mutasi_grg a
			INNER JOIN acc_mutasi_tmp b
			ON a.periode_th = b.periode_th AND a.periode_bln = b.periode_bln AND a.dept_id_mutasi = b.dept_id_mutasi AND a.kode_produk = b.kode_produk
			SET
			a.out4_lot=b.lot,
			a.out4_qty1=b.qty1,
			a.out4_qty1_uom=b.qty1_uom,
			a.out4_qty2=b.qty2,
			a.out4_qty2_uom=b.qty2_uom,
			a.out4_qty_opname=b.qty_opname,
			a.out4_qty_opname_uom=b.qty_opname_uom;
		ELSEIF x =5 THEN
			UPDATE acc_mutasi_grg a
			INNER JOIN acc_mutasi_tmp b
			ON a.periode_th = b.periode_th AND a.periode_bln = b.periode_bln AND a.dept_id_mutasi = b.dept_id_mutasi AND a.kode_produk = b.kode_produk
			SET
			a.out5_lot=b.lot,
			a.out5_qty1=b.qty1,
			a.out5_qty1_uom=b.qty1_uom,
			a.out5_qty2=b.qty2,
			a.out5_qty2_uom=b.qty2_uom,
			a.out5_qty_opname=b.qty_opname,
			a.out5_qty_opname_uom=b.qty_opname_uom;
		ELSEIF x =6 THEN
			UPDATE acc_mutasi_grg a
			INNER JOIN acc_mutasi_tmp b
			ON a.periode_th = b.periode_th AND a.periode_bln = b.periode_bln AND a.dept_id_mutasi = b.dept_id_mutasi AND a.kode_produk = b.kode_produk
			SET
			a.out6_lot=b.lot,
			a.out6_qty1=b.qty1,
			a.out6_qty1_uom=b.qty1_uom,
			a.out6_qty2=b.qty2,
			a.out6_qty2_uom=b.qty2_uom,
			a.out6_qty_opname=b.qty_opname,
			a.out6_qty_opname_uom=b.qty_opname_uom;
		ELSEIF x =7 THEN
			UPDATE acc_mutasi_grg a
			INNER JOIN acc_mutasi_tmp b
			ON a.periode_th = b.periode_th AND a.periode_bln = b.periode_bln AND a.dept_id_mutasi = b.dept_id_mutasi AND a.kode_produk = b.kode_produk
			SET
			a.out7_lot=b.lot,
			a.out7_qty1=b.qty1,
			a.out7_qty1_uom=b.qty1_uom,
			a.out7_qty2=b.qty2,
			a.out7_qty2_uom=b.qty2_uom,
			a.out7_qty_opname=b.qty_opname,
			a.out7_qty_opname_uom=b.qty_opname_uom;
		ELSEIF x =8 THEN
			UPDATE acc_mutasi_grg a
			INNER JOIN acc_mutasi_tmp b
			ON a.periode_th = b.periode_th AND a.periode_bln = b.periode_bln AND a.dept_id_mutasi = b.dept_id_mutasi AND a.kode_produk = b.kode_produk
			SET
			a.out8_lot=b.lot,
			a.out8_qty1=b.qty1,
			a.out8_qty1_uom=b.qty1_uom,
			a.out8_qty2=b.qty2,
			a.out8_qty2_uom=b.qty2_uom,
			a.out8_qty_opname=b.qty_opname,
			a.out8_qty_opname_uom=b.qty_opname_uom;
		END IF;
		
		#IN delete temp
		DELETE FROM acc_mutasi_tmp;
		
		IF  x = 8 THEN 
			LEAVE  loop_label;
		END  IF;
		
	END LOOP;
	
END$$
DELIMITER ;
CALL LoopOut();

#ADJ_IN insert ignore corak
INSERT IGNORE INTO acc_mutasi_grg (periode_th, periode_bln, dept_id_mutasi, kode_produk, nama_produk)
SELECT YEAR(SUBDATE$replace_currDate) as periode_th, MONTH(SUBDATE$replace_currDate) as periode_bln,
'GRG' as dept_id_mutasi,
smi.kode_produk, smi.nama_produk
FROM acc_stock_move_items smi 
WHERE smi.dept_id_mutasi = 'GRG'
AND smi.type = 'adj_in'
AND smi.tanggal_transaksi >= CONCAT(date_add(date_add(LAST_DAY(SUBDATE$replace_currDate),interval 1 DAY),interval -1 MONTH), ' 00:00:000')
AND smi.tanggal_transaksi <= CONCAT(SUBDATE$replace_currDate,' 23:59:59')
GROUP BY kode_produk;

#ADJ_IN buat temp
INSERT INTO acc_mutasi_tmp
SELECT YEAR(SUBDATE$replace_currDate) as periode_th, MONTH(SUBDATE$replace_currDate) as periode_bln,
'GRG' as dept_id_mutasi, smi.kode_produk, smi.nama_produk,
COUNT(*) as total_lot,
SUM(smi.qty) as qty1, COALESCE(uom,'') as qty1_uom, 
SUM(smi.qty2) as qty2, COALESCE(uom2,'') as qty2_uom, 
SUM(smi.qty_opname) as qty_opname, COALESCE(uom_opname,'') as opname_uom,
'ADJ_IN' as seq
FROM acc_stock_move_items smi 
WHERE smi.dept_id_mutasi = 'GRG'
AND smi.type = 'adj_in'
AND smi.tanggal_transaksi >= CONCAT(date_add(date_add(LAST_DAY(SUBDATE$replace_currDate),interval 1 DAY),interval -1 MONTH), ' 00:00:000')
AND smi.tanggal_transaksi <= CONCAT(SUBDATE$replace_currDate,' 23:59:59')
GROUP BY smi.kode_produk;

#ADJ_IN mutasi
UPDATE acc_mutasi_grg a
INNER JOIN acc_mutasi_tmp b
ON a.periode_th = b.periode_th AND a.periode_bln = b.periode_bln AND a.dept_id_mutasi = b.dept_id_mutasi AND a.kode_produk = b.kode_produk
SET
a.adj_in_lot=b.lot,
a.adj_in_qty1=b.qty1,
a.adj_in_qty1_uom=b.qty1_uom,
a.adj_in_qty2=b.qty2,
a.adj_in_qty2_uom=b.qty2_uom,
a.adj_in_qty_opname=b.qty_opname,
a.adj_in_qty_opname_uom=b.qty_opname_uom;

#ADJ_IN delete temp
DELETE FROM acc_mutasi_tmp;

#ADJ_OUT insert ignore corak
INSERT IGNORE INTO acc_mutasi_grg (periode_th, periode_bln, dept_id_mutasi, kode_produk, nama_produk)
SELECT YEAR(SUBDATE$replace_currDate) as periode_th, MONTH(SUBDATE$replace_currDate) as periode_bln,
'GRG' as dept_id_mutasi,
smi.kode_produk, smi.nama_produk
FROM acc_stock_move_items smi 
WHERE smi.dept_id_mutasi = 'GRG'
AND smi.type = 'adj_out'
AND smi.tanggal_transaksi >= CONCAT(date_add(date_add(LAST_DAY(SUBDATE$replace_currDate),interval 1 DAY),interval -1 MONTH), ' 00:00:000')
AND smi.tanggal_transaksi <= CONCAT(SUBDATE$replace_currDate,' 23:59:59')
GROUP BY kode_produk;

#ADJ_OUT buat temp
INSERT INTO acc_mutasi_tmp
SELECT YEAR(SUBDATE$replace_currDate) as periode_th, MONTH(SUBDATE$replace_currDate) as periode_bln,
'GRG' as dept_id_mutasi, smi.kode_produk, smi.nama_produk,
COUNT(*) as total_lot,
SUM(smi.qty) as qty1, COALESCE(uom,'') as qty1_uom, 
SUM(smi.qty2) as qty2, COALESCE(uom2,'') as qty2_uom, 
SUM(smi.qty_opname) as qty_opname, COALESCE(uom_opname,'') as opname_uom,
'ADJ_OUT' as seq
FROM acc_stock_move_items smi 
WHERE smi.dept_id_mutasi = 'GRG'
AND smi.type = 'adj_out'
AND smi.tanggal_transaksi >= CONCAT(date_add(date_add(LAST_DAY(SUBDATE$replace_currDate),interval 1 DAY),interval -1 MONTH), ' 00:00:000')
AND smi.tanggal_transaksi <= CONCAT(SUBDATE$replace_currDate,' 23:59:59')
GROUP BY smi.kode_produk;

#ADJ_OUT mutasi
UPDATE acc_mutasi_grg a
INNER JOIN acc_mutasi_tmp b
ON a.periode_th = b.periode_th AND a.periode_bln = b.periode_bln AND a.dept_id_mutasi = b.dept_id_mutasi AND a.kode_produk = b.kode_produk
SET
a.adj_out_lot=b.lot,
a.adj_out_qty1=b.qty1,
a.adj_out_qty1_uom=b.qty1_uom,
a.adj_out_qty2=b.qty2,
a.adj_out_qty2_uom=b.qty2_uom,
a.adj_out_qty_opname=b.qty_opname,
a.adj_out_qty_opname_uom=b.qty_opname_uom;

#ADJ_OUT delete temp
DELETE FROM acc_mutasi_tmp;

#SALDO_AKHIR
UPDATE acc_mutasi_grg a
INNER JOIN mst_produk b ON a.kode_produk = b.kode_produk
SET
s_akhir_lot = s_awal_lot + in1_lot + in2_lot + in3_lot + in4_lot + in5_lot + in6_lot + in7_lot + adj_in_lot - (out1_lot + out2_lot + out3_lot + out4_lot + out5_lot + out6_lot + out7_lot + out8_lot + adj_out_lot),
s_akhir_qty1 = s_awal_qty1 + in1_qty1 + in2_qty1 + in3_qty1 + in4_qty1 + in5_qty1 + in6_qty1 + in7_qty1 + adj_in_qty1 - (out1_qty1 + out2_qty1 + out3_qty1 + out4_qty1 + out5_qty1 + out6_qty1 + out7_qty1 + out8_qty1 + adj_out_qty1),
s_akhir_qty1_uom = b.uom,
s_akhir_qty2 = s_awal_qty2 + in1_qty2 + in2_qty2 + in3_qty2 + in4_qty2 + in5_qty2 + in6_qty2 + in7_qty2 + adj_in_qty2 - (out1_qty2 + out2_qty2 + out3_qty2 + out4_qty2 + out5_qty2 + out6_qty2 + out7_qty2 + out8_qty2 + adj_out_qty2),
s_akhir_qty2_uom = b.uom_2,
s_akhir_qty_opname = s_awal_qty_opname + in1_qty_opname + in2_qty_opname + in3_qty_opname + in4_qty_opname + in5_qty_opname + in6_qty_opname + in7_qty_opname + adj_in_qty_opname - (out1_qty_opname + out2_qty_opname + out3_qty_opname + out4_qty_opname + out5_qty_opname + out6_qty_opname + out7_qty_opname + out8_qty_opname + adj_out_qty_opname),
s_akhir_qty_opname_uom = 'Kg'
WHERE
periode_th = YEAR(SUBDATE$replace_currDate) AND
periode_bln = MONTH(SUBDATE$replace_currDate);

#ID_CATEGORY
UPDATE acc_mutasi_grg m
INNER JOIN mst_produk mp ON m.kode_produk = mp.kode_produk
LEFT JOIN mst_produk_parent mpp ON mp.id_parent = mpp.id
SET m.id_category = mp.id_category,
m.product_parent = COALESCE(mpp.nama,'')
WHERE m.periode_th = YEAR(SUBDATE$replace_currDate)
AND m.periode_bln = MONTH(SUBDATE$replace_currDate);

INSERT INTO log_checksum
VALUE (now(), 'UPDATE|acc_mutasi_grg|Proses Koreksi Mundur');


