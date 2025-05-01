<?php

defined('BASEPATH') or exit('No Direct Script Acces Allowed');
/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */

class M_Token extends CI_Model {

    protected $modul = '';
    protected $periode = '';
    protected $prefix = '';
    protected $format = '';
    protected $table = 'token_increment';

    public function exists(array $where) {
        $this->db->from($this->table);
        $this->db->where($where);
        $data = $this->db->select('*')->get()->row();
        return $data;
    }

    protected function checkExist(): int {
        $this->db->from($this->table);
        $this->db->where(['modul' => $this->modul, 'periode' => $this->periode]);
        $data = $this->db->select('increment')->get()->row();
        $db_error = $this->db->error();
        if ($db_error['code'] > 0) {
            throw new Exception($db_error['message']);
        }

        return $data->increment ?? 0;
    }

    protected function createModulPeriode() {
        $this->db->insert($this->table, [
            'id' => null,
            'modul' => $this->modul,
            'periode' => $this->periode,
            'increment' => 1,
            'prefix' => $this->prefix,
            'format' => $this->format
        ]);
        $db_error = $this->db->error();
        if ($db_error['code'] > 0) {
            throw new Exception($db_error['message']);
        }
    }

    protected function updateIncr(int $incr) {
        $this->db->where(['modul' => $this->modul, 'periode' => $this->periode]);
        $incrs = $incr + 1;
        $this->db->update($this->table, ['increment' =>$incrs ]);
        $db_error = $this->db->error();
        if ($db_error['code'] > 0) {
            throw new Exception($db_error['message']);
        }
    }

    public function getToken(string $modul, string $periode, string $prefix = "", string $format = "", bool $incrs = false) {
        $this->prefix = $prefix;
        $this->periode = $periode;
        $this->modul = $modul;
        $this->format = $format;
        try {
            $incr = 1;
            $exist = $this->checkExist();
            if ($exist < 1) {
                $this->createModulPeriode();
            } else {
                $incr = $exist;
            }
            if ($incrs) {
                $this->updateIncr($incr);
            }
            return $incr;
        } catch (Exception $ex) {
            log_message('error', $ex->getMessage());
            return false;
        }
    }
}
