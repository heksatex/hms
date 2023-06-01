<?php defined('BASEPATH') or exit ('No Direct Script Acces Allowed');

/**
 * 
 */
class M_downtime extends CI_Model
{
   function get_down_up_time($id_dept,$tgl_dari,$tgl_sampai)
   {
        return $this->db->query("SELECT mst.mc_id, mst.nama_mesin, devid_ard, port_ard, 
                                COUNT(log.state) as dc, 
                                SUM(log.state=1) as down, 
                                SUM(log.state=0) as up, 
                                ROUND(100/COUNT(log.state)*SUM(log.state=1),2) as downtime, 
                                ROUND(100/COUNT(log.state)*SUM(log.state=0),2) as uptime,
                                ROUND(TIMESTAMPDIFF(MINUTE, '$tgl_dari', '$tgl_sampai'),0) AS dct,
                                ROUND(100/(TIMESTAMPDIFF(MINUTE, '$tgl_dari', '$tgl_sampai'))*COUNT(log.state),2) AS dcr
                                FROM mesin mst
                                INNER JOIN log_mc log ON mst.devid_ard=log.devid AND mst.port_ard=log.port
                                WHERE NOT ISNULL(devid_ard) 
                                AND mst.dept_id = '$id_dept' 
                                AND log.timelog BETWEEN '$tgl_dari' AND '$tgl_sampai'
                                GROUP BY mst.devid_ard, mst.port_ard
                                ORDER BY nama_mesin asc")->result();

   }

   function get_shift_now($jam)
   {
      $result =  $this->db->query("SELECT shift FROM z_shift_time where '$jam' BETWEEN jam_dari AND jam_sampai ")->row();
      return $result->shift;
   }

   function get_log_by_mc($id_dept,$tgl_dari,$tgl_sampai,$mc_id)
   {
      $result = $this->db->query("SELECT log.timelog, state
                                 FROM mesin mst
                                 INNER JOIN log_mc log ON mst.devid_ard=log.devid AND mst.port_ard=log.port
                                 WHERE NOT ISNULL(devid_ard)
                                 AND log.timelog BETWEEN '$tgl_dari' AND '$tgl_sampai' AND mst.mc_id = '$mc_id' AND mst.dept_id = '$id_dept'
                                 order by log.timelog desc")->result();
      return $result;
   }


   public function get_nama_mesin_by_kode($mc_id)
	{
		return $this->db->query("SELECT nama_mesin FROM mesin where mc_id = '$mc_id' ");
	}

}