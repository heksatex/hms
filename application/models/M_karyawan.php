<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_karyawan extends CI_Model
{
    private $table = 'karyawan';
    private $db_hr;

    public function __construct()
    {
        parent::__construct();

        $this->db_hr = $this->load->database('hr', TRUE);
    }

    public function count_all()
    {
        return $this->db_hr->count_all($this->table);
    }

    public function count_filtered($keyword = '')
    {
        $this->db_hr->from($this->table);

        if ($keyword != '') {

            $this->db_hr->group_start();

            $this->db_hr->like('nrp', $keyword);
            $this->db_hr->or_like('nama', $keyword);
            $this->db_hr->or_like('bagian', $keyword);
            $this->db_hr->or_like('jabatan', $keyword);

            $this->db_hr->group_end();
        }

        return $this->db_hr->count_all_results();
    }

    public function get_karyawan(
        $keyword = '',
        $limit = 10,
        $offset = 0
    ) {
        // Hanya data yang diperlukan untuk list
        $this->db_hr->select('
            id,
            nrp,
            nama,
            bagian,
            jabatan,
            golongan,
            tgl_masuk,
            status
        ');

        $this->db_hr->from($this->table);

        if ($keyword != '') {

            $this->db_hr->group_start();

            $this->db_hr->like('nrp', $keyword);
            $this->db_hr->or_like('nama', $keyword);
            $this->db_hr->or_like('bagian', $keyword);
            $this->db_hr->or_like('jabatan', $keyword);

            $this->db_hr->group_end();
        }

        $this->db_hr->order_by('bagian', 'ASC');
        $this->db_hr->order_by('nama', 'ASC');

        $this->db_hr->limit($limit, $offset);

        return $this->db_hr
            ->get()
            ->result_array();
    }

    public function get_by_id($nrp)
    {
        $this->db_hr->select('
            id,
            nrp,
            nama,
            bagian,
            jabatan,
            golongan,
            tgl_masuk,
            awal_kontrak,
            akhir_kontrak,
            jenis_shift,
            group_shift,
            shift,
            jenis_id,
            no_id,
            tgl_lahir,
            tmp_lahir,
            alamat,
            kota,
            kode_pos,
            pendidikan,
            jk,
            agama,
            kawin,
            jml_anak,
            jamsostek,
            spsi,
            kematian,
            bpjs,
            um,
            nisp AS norek_bca,
            fp_4100 AS finger_id,
            status_gaji,
            t_jabatan,
            t_masakerja,
            t_fungsional,
            status,
            foto
        ');

        return $this->db_hr
            ->where('nrp', $nrp)
            ->get($this->table)
            ->row_array();
    }

    public function get_foto_by_id($nrp)
    {
        return $this->db_hr
            ->select('foto, nrp')
            ->where('nrp', $nrp)
            ->get($this->table)
            ->row_array();
    }
}