<?php

defined('BASEPATH') OR exit('No direct script access allowed');
/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */

class Wa_message {

    protected $val = [];
    protected $template = '';
    protected $user;
    protected $group;
    protected $model;

    public function __construct() {
        $this->model = & get_instance();
        $this->model->load->model("m_WaSendMessage");
        $this->model->load->model("m_WaGroup");
        $this->model->load->model("m_WaTemplate");
        $this->model->load->model("m_user");
    }
    
    public function sendMessageToUser(string $templateName, array $value, string $username): bool {

        $this->val = $value;
        if (!$this->getTemplate($templateName)) {
            return false;
        }
        $this->setUser($username);
        $this->save($this->user->nama . ';' . $this->user->telepon_wa);
        return true;
    }

    public function sendMessageToUsers(string $templateName, array $value, array $username): bool {

        $this->val = $value;
        if (!$this->getTemplate($templateName)) {
            return false;
        }
        foreach ($username as $key => $value) {
            $this->setUser($value);
            $this->save($this->user->nama . ';' . $this->user->telepon_wa);
        }

        return true;
    }

    public function sendMessageToUserByDept(string $templateName, array $value, array $deptID): bool {

        $this->val = $value;
        if (!$this->getTemplate($templateName)) {
            return false;
        }
        foreach ($this->user as $key => $value) {
            $this->save($value->nama . ';' . $value->telepon_wa);
        }
        true;
    }

    protected function getTemplate($templateName): bool {
        $data = $this->model->m_WaTemplate->getDataByName($templateName);
        
        if (!is_object($data)) {
            return false;
        }
        
        $this->template = $data->template;
        return true;
    }

    protected function setToTemplate():string {
        return strtr($this->template, $this->val);
    }

    protected function setUser($username) {
        
        $this->user = $this->model->m_user->get_user_by_username($username);
    }

    protected function setUserByDept($dept) {
        $this->user = $this->model->m_user->get_user_by_dept($dept);
    }

    protected function save($value, $touser = 'touser') {
        $this->model->m_WaSendMessage->save($this->setToTemplate(), [$touser => $value]);
    }
}
