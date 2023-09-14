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
        if (!array_key_exists('nama', $this->user)) {
            return false;
        }
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
            if (!array_key_exists('nama', $this->user)) {
                return false;
            }
            $this->save($this->user->nama . ';' . $this->user->telepon_wa);
        }

        return true;
    }

    public function sendMessageToUserByDept(string $templateName, array $value, array $deptID): bool {

        $this->val = $value;
        if (!$this->getTemplate($templateName)) {
            return false;
        }
        $this->setUserByDept($deptID);
        foreach ($this->user as $key => $value) {
            if (!array_key_exists('nama', $value)) {
                return false;
            }
            $this->save($value->nama . ';' . $value->telepon_wa);
        }
        return true;
    }

    public function sendMessageToGroup(string $templateName, array $value, array $group): bool {

        $this->val = $value;
        if (!$this->getTemplate($templateName)) {
            return false;
        }
        foreach ($group as $key => $value) {
            $this->setGroup($value);
            if (!array_key_exists('wa_group', $this->group)) {
                return false;
            }
            $this->save($this->group->wa_group, 'togroup');
        }

        return true;
    }

    public function sendMessageToGroupByDepth(string $templateName, array $value, array $depthkode): bool {

        $this->val = $value;
        if (!$this->getTemplate($templateName)) {
            return false;
        }
        $this->setGroupByDept($depthkode);
        foreach ($this->group as $key => $value) {
            $this->save($value->wa_group, 'togroup');
        }

        return true;
    }

    protected function getTemplate($templateName): bool {
        $data = $this->model->m_WaTemplate->getDataByName($templateName);

        if (!is_object($data)) {
            return false;
        }

        $this->template = $data->template;
        return true;
    }

    protected function setToTemplate(): string {
        return strtr($this->template, $this->val);
    }

    protected function setUser($username) {

        $this->user = $this->model->m_user->get_user_by_username($username) ?? [];
    }

    protected function setGroup($group) {
        $this->group = $this->model->m_WaGroup->getDataByNama($group) ?? [];
    }

    protected function setGroupByDept(array $depth) {
        $this->group = $this->model->m_WaGroup->getDataByDepth($depth) ?? [];
    }

    protected function setUserByDept($dept) {
        $this->user = $this->model->m_user->get_user_by_dept($dept) ?? [];
    }

    protected function save($value, $touser = 'touser') {
        $this->model->m_WaSendMessage->save($this->setToTemplate(), [$touser => $value]);
    }
}
