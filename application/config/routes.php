<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'login';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

$route['admin']			= 'admin/overview';
$route['manufacturing'] = 'manufacturing/manufacturing';
$route['warehouse']     = 'warehouse/warehouse';
$route['ppic']			= 'ppic/ppic';
$route['lab']			= 'lab/lab';
$route['sales']			= 'sales/sales';
$route['setting']	 	= 'setting/setting';
$route['report']	 	= 'report/report';
//WA Group
$route['setting/wa_group']['GET'] = 'setting/WaGroup/index';
$route['setting/wa_group/add']['GET'] = 'setting/WaGroup/add';
$route['setting/wa_group/edit/([a-zA-Z0-9]+)']['GET'] = 'setting/WaGroup/edit/$1';
$route['setting/wa_group/update']['POST'] = 'setting/WaGroup/update';
$route['setting/wa_group/simpan']['POST'] = 'setting/WaGroup/simpan';
$route['setting/wa_group/get_data']['POST'] = 'setting/WaGroup/getData';
//WA Template
$route['setting/wa_template']['GET'] = 'setting/WaTemplate/index';
$route['setting/wa_template/add']['GET'] = 'setting/WaTemplate/add';
$route['setting/wa_template/edit/([a-zA-Z0-9]+)']['GET'] = 'setting/WaTemplate/edit/$1';
$route['setting/wa_template/get_data']['POST'] = 'setting/WaTemplate/getData';
$route['setting/wa_template/simpan']['POST'] = 'setting/WaTemplate/simpan';
$route['setting/wa_template/update']['POST'] = 'setting/WaTemplate/update';
//WA Send Message
$route['setting/wa_send_message']['GET'] = 'setting/WaSendMessage/index';
$route['setting/wa_send_message/get_data']['POST'] = 'setting/WaSendMessage/getData';
//WA Schedule Message
$route['setting/wa_schedule']['GET'] = 'setting/WaScheduleMessage/index';
$route['setting/wa_schedule/add']['GET'] = 'setting/WaScheduleMessage/add';
$route['setting/wa_schedule/edit/([a-zA-Z0-9]+)']['GET'] = 'setting/WaScheduleMessage/edit/$1';
$route['setting/wa_schedule/get_data']['POST'] = 'setting/WaScheduleMessage/getData';
$route['setting/wa_schedule/simpan']['POST'] = 'setting/WaScheduleMessage/simpan';
$route['setting/wa_schedule/update']['POST'] = 'setting/WaScheduleMessage/update';