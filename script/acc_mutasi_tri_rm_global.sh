#!/bin/bash
mysql -h localhost -u root -ptoor << EOF
use hmsdb_bak;

#CLEAR
DELETE FROM acc_mutasi_tri_rm_global
WHERE periode_th = YEAR(SUBDATE$replace_currDate) AND periode_bln = MONTH(SUBDATE$replace_currDate);

#INSERT INTO SELECTFROM
INSERT INTO acc_mutasi_tri_rm_global
SELECT YEAR(SUBDATE$replace_currDate) as periode_th, MONTH(SUBDATE$replace_currDate) as periode_bln, m.dept_id_mutasi,mpp.nama as nama_produk,
SUM(s_awal_lot) s_awal_lot, SUM(CASE WHEN mp.uom='Mtr' THEN m.s_awal_qty1 END) "s_awal_mtr",
SUM(CASE 
	WHEN mp.uom='Kg' THEN m.s_awal_qty1
	WHEN mp.uom_2='Kg' THEN m.s_awal_qty2
END) "s_awal_kg",
SUM(m.in1_lot) in1_lot, SUM(CASE WHEN mp.uom='Mtr' THEN m.in1_qty1 END) "in1_mtr",
SUM(CASE 
	WHEN mp.uom='Kg' THEN m.in1_qty1 
	WHEN mp.uom_2='Kg' THEN m.in1_qty2
END) "in1_kg",
SUM(m.in2_lot) in2_lot, SUM(CASE WHEN mp.uom='Mtr' THEN m.in2_qty1 END) "in2_mtr",
SUM(CASE 
	WHEN mp.uom='Kg' THEN m.in2_qty1 
	WHEN mp.uom_2='Kg' THEN m.in2_qty2
END) "in2_kg",
SUM(m.in3_lot) in3_lot, SUM(CASE WHEN mp.uom='Mtr' THEN m.in3_qty1 END) "in3_mtr",
SUM(CASE 
	WHEN mp.uom='Kg' THEN m.in3_qty1 
	WHEN mp.uom_2='Kg' THEN m.in3_qty2
END) "in3_kg",
SUM(m.in4_lot) in4_lot, SUM(CASE WHEN mp.uom='Mtr' THEN m.in4_qty1 END) "in4_mtr",
SUM(CASE 
	WHEN mp.uom='Kg' THEN m.in4_qty1 
	WHEN mp.uom_2='Kg' THEN m.in4_qty2
END) "in4_kg",
SUM(m.in5_lot) in5_lot, SUM(CASE WHEN mp.uom='Mtr' THEN m.in5_qty1 END) "in5_mtr",
SUM(CASE 
	WHEN mp.uom='Kg' THEN m.in5_qty1 
	WHEN mp.uom_2='Kg' THEN m.in5_qty2
END) "in5_kg",
SUM(m.adj_in_lot) adj_in_lot, SUM(CASE WHEN mp.uom='Mtr' THEN m.adj_in_qty1 END) "adj_in_mtr",
SUM(CASE 
	WHEN mp.uom='Kg' THEN m.adj_in_qty1 
	WHEN mp.uom_2='Kg' THEN m.adj_in_qty2
END) "adj_in_kg",
SUM(m.out1_lot) out1_lot, SUM(CASE WHEN mp.uom='Mtr' THEN m.out1_qty1 END) "out1_mtr",
SUM(CASE 
	WHEN mp.uom='Kg' THEN m.out1_qty1 
	WHEN mp.uom_2='Kg' THEN m.out1_qty2
END) "out1_kg",
SUM(m.out2_lot) out2_lot, SUM(CASE WHEN mp.uom='Mtr' THEN m.out2_qty1 END) "out2_mtr",
SUM(CASE 
	WHEN mp.uom='Kg' THEN m.out2_qty1 
	WHEN mp.uom_2='Kg' THEN m.out2_qty2
END) "out2_kg",
SUM(m.out3_lot) out3_lot, SUM(CASE WHEN mp.uom='Mtr' THEN m.out3_qty1 END) "out3_mtr",
SUM(CASE 
	WHEN mp.uom='Kg' THEN m.out3_qty1 
	WHEN mp.uom_2='Kg' THEN m.out3_qty2
END) "out3_kg",
SUM(m.out4_lot) out4_lot, SUM(CASE WHEN mp.uom='Mtr' THEN m.out4_qty1 END) "out4_mtr",
SUM(CASE 
	WHEN mp.uom='Kg' THEN m.out4_qty1 
	WHEN mp.uom_2='Kg' THEN m.out4_qty2
END) "out4_kg",
SUM(m.out5_lot) out5_lot, SUM(CASE WHEN mp.uom='Mtr' THEN m.out5_qty1 END) "out5_mtr",
SUM(CASE 
	WHEN mp.uom='Kg' THEN m.out5_qty1 
	WHEN mp.uom_2='Kg' THEN m.out5_qty2
END) "out5_kg",
SUM(m.adj_out_lot) adj_out_lot, SUM(CASE WHEN mp.uom='Mtr' THEN m.adj_out_qty1 END) "adj_out_mtr",
SUM(CASE 
	WHEN mp.uom='Kg' THEN m.adj_out_qty1 
	WHEN mp.uom_2='Kg' THEN m.adj_out_qty2
END) "adj_out_kg",
SUM(s_akhir_lot) s_akhir_lot, SUM(CASE WHEN mp.uom='Mtr' THEN m.s_akhir_qty1 END) "s_akhir_mtr",
SUM(CASE 
	WHEN mp.uom='Kg' THEN m.s_akhir_qty1
	WHEN mp.uom_2='Kg' THEN m.s_akhir_qty2
END) "s_akhir_kg"
FROM acc_mutasi_tri_rm m
INNER JOIN mst_produk mp ON m.kode_produk = mp.kode_produk
INNER JOIN mst_produk_parent mpp ON mp.id_parent = mpp.id
WHERE  m.periode_th = YEAR(SUBDATE$replace_currDate)
AND m.periode_bln = MONTH(SUBDATE$replace_currDate)
GROUP BY mp.id_parent;

INSERT INTO log_checksum
VALUE (now(), 'UPDATE|acc_mutasi_tri_rm_global|Proses Koreksi Mundur');
