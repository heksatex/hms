<?php defined('BASEPATH') or exit ('No Direct Script Acces Allowed');

/**
 * 
 */
class M_reportReproses extends CI_Model
{

    private function get_data_query_by_kode()
	{	
        if($this->input->post('kode_reproses')){
    		$this->db->like('r.kode_reproses',$this->input->post('kode_reproses'));
        }

        if($this->input->post('nama_produk')){
    		$this->db->like('mp.nama_produk',$this->input->post('nama_produk'));
        }

        if($this->input->post('lot_baru')){
    		$this->db->like('ri.lot_new',$this->input->post('lot_baru'));
        }

        if($this->input->post('sub_parent')){
    		$this->db->like('mpsp.nama_sub_parent',$this->input->post('sub_parent'));
        }

        if($this->input->post('jenis')){
    		$this->db->where('r.id_jenis',$this->input->post('jenis'));
        }


        $this->db->select("r.kode_reproses, r.tanggal, r.id_jenis, rj.nama_jenis, r.note, r.nama_user,ri.lot, ri.kode_produk, mp.nama_produk, ri.lot, ri.lot_new, ri.qty, ri.uom, ri.qty2, ri.uom2, ri.lokasi_asal,  mpsp.nama_sub_parent");
        $this->db->from("reproses r");
        $this->db->JOIN("reproses_items ri","r.kode_reproses = ri.kode_reproses", "INNER");
        $this->db->JOIN("reproses_jenis rj","r.id_jenis = rj.id","INNER");
        $this->db->JOIN("mst_produk mp","ri.kode_produk = mp.kode_produk","INNER");
        $this->db->JOIN("mst_produk_sub_parent mpsp","mp.id_sub_parent = mpsp.id", "LEFT");
        $this->db->where("r.status", 'done');
        $this->db->order_by("mpsp.nama_sub_parent",'asc');
        $this->db->order_by("r.tanggal",'asc');
	}


    function get_list_reproses_by_kode($tgldari,$tglsampai)
    {
        $this->get_data_query_by_kode();
        $this->db->where("r.tanggal >= ",$tgldari);
        $this->db->where("r.tanggal <= ",$tglsampai);
        $query = $this->db->get();
		return $query->result();

    }

    function get_count_record_reproses($tgldari,$tglsampai)
    {
        $this->get_data_query_by_kode();
        $this->db->where("r.tanggal >= ",$tgldari);
        $this->db->where("r.tanggal <= ",$tglsampai);
		$query = $this->db->get();
		return $query->num_rows();
    }

}