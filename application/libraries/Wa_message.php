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
    protected $to = [];
    protected $has_mention = null;
    protected $status = true;
    protected $footer = "";
    protected $pesan = "";

    public function __construct() {
        $this->model = & get_instance();
        $this->model->load->model("m_WaSendMessage");
        $this->model->load->model("m_WaGroup");
        $this->model->load->model("m_WaTemplate");
        $this->model->load->model("m_user");
    }

    /**
     * 
     * @param string $templateName
     * @param array $valueForTemplate
     * @param string $username
     * @return bool|$this
     */
    public function sendMessageToUser(string $templateName, array $valueForTemplate, string $username) {

        $this->val = $valueForTemplate;
        if (!$this->getTemplate($templateName)) {
            $this->status = false;
        }
        $this->setUser($username);
        if (!array_key_exists('nama', $this->user)) {
            $this->status = false;
        }
        if ($this->status) {
            $this->to[] = ['touser' => $this->user->nama . ';' . $this->user->telepon_wa];
        }
//        $this->save($this->user->nama . ';' . $this->user->telepon_wa);

        return $this;
    }

    /**
     * 
     * @param string $templateName
     * @param array $valueForTemplate
     * @param array $username
     * @return bool|$this
     */
    public function sendMessageToUsers(string $templateName, array $valueForTemplate, array $username) {

        $this->val = $valueForTemplate;
        if (!$this->getTemplate($templateName)) {
            $this->status = false;
        }
        foreach ($username as $key => $value) {
            $this->setUser($value);
            if (!array_key_exists('nama', $this->user)) {
                $this->status = false;
                continue;
            }

            if ($this->status) {
                $this->to[] = ['touser' => $this->user->nama . ';' . $this->user->telepon_wa];
                $this->status = true;
            }
//            $this->save($this->user->nama . ';' . $this->user->telepon_wa);
        }

        return $this;
    }

    /**
     * 
     * @param string $templateName
     * @param array $valueForTemplate
     * @param array $deptID
     * @return bool|$this
     */
    public function sendMessageToUserByDept(string $templateName, array $valueForTemplate, array $deptID) {

        $this->val = $valueForTemplate;
        if (!$this->getTemplate($templateName)) {
            $this->status = false;
        }
        $this->setUserByDept($deptID);
        foreach ($this->user as $key => $value) {
            if (!array_key_exists('nama', $value)) {
                $this->status = false;
                continue;
            }
            if ($this->status) {
                $this->to[] = ['touser' => $value->nama . ';' . $value->telepon_wa];
                $this->status = true;
            }
//            $this->save($value->nama . ';' . $value->telepon_wa);
        }
        return $this;
    }

    /**
     * 
     * @param string $templateName
     * @param array $valueForTemplate
     * @param array $group
     * @return bool|$this
     */
    public function sendMessageToGroup(string $templateName, array $valueForTemplate, array $group) {

        $this->val = $valueForTemplate;
        if (!$this->getTemplate($templateName)) {
            $this->status = false;
        }
        foreach ($group as $key => $value) {
            $this->setGroup($value);
            if (!array_key_exists('wa_group', $this->group)) {
                $this->status = false;
                continue;
            }
            if ($this->status) {
                $this->to[] = ['togroup' => $this->group->wa_group];
                $this->status = true;
            }
//            $this->save($this->group->wa_group, 'togroup');
        }

        return $this;
    }

    /**
     * 
     * @param string $templateName
     * @param array $valueForTemplate
     * @param array $depthkode
     * @return bool|$this
     */
    public function sendMessageToGroupByDepth(string $templateName, array $valueForTemplate, array $depthkode) {

        $this->val = $valueForTemplate;
        if (!$this->getTemplate($templateName)) {
            return false;
        }
        $this->setGroupByDept($depthkode);
        foreach ($this->group as $key => $value) {
//            $this->save($value->wa_group, 'togroup');
            if ($this->status) {
                $this->to [] = ['togroup' => $value->wa_group];
            }
        }

        return $this;
    }

    /**
     * 
     * @param string $pesan
     * @return $this
     */
    public function setMessageNoTemplate(string $pesan) {
        $this->pesan = $pesan;
        return $this;
    }

    /**
     * 
     * @param array $phoneNumber
     * @return $this
     */
    public function setMentions(array $phoneNumber) {
        if (count($phoneNumber) > 0) {
            $this->has_mention = json_encode($phoneNumber);
        }
        return $this;
    }

    /**
     * 
     * @param string $footer_name
     * @return $this
     */
    public function setFooter(string $footer_name) {
        $data = $this->model->m_WaTemplate->getFooter($footer_name);

        if (!is_object($data)) {
            $this->footer = "";
        } else {
            $this->footer = $data->template;
        }

        return $this;
    }

    /**
     * KSave Database
     */
    public function send() {
        foreach ($this->to as $value) {
            foreach ($value as $key => $values) {
                $this->save($values, $key);
            }
        }
    }

    protected function getTemplate($templateName): bool {
        if ($this->pesan !== "" || trim($this->pesan) !== "") {
            $this->template = $this->pesan;
            return true;
        }
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
        $this->model->m_WaSendMessage->save($this->setToTemplate(), [$touser => $value, 'has_mention' => $this->has_mention, 'footer' => $this->footer]);
    }
}
