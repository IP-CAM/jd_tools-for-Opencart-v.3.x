<?php
$this->load->model('setting/setting');

$jd_settings = $this->model_setting_setting->getSetting('jd_tools_main');
if (empty($jd_settings)) {
	$this->model_setting_setting->editSetting(
		'jd_tools_main',
		[
			'jd_tools_main_menu_items'  =>  [
				'dashboard' => [
					'route'  => 'common/dashboard',
					'name'  =>  'Головна',
					'children'  =>  [],
				],
				'prom_ua_import'    =>  [
					'route' =>  'tool/prom_ua_import',
					'name'  =>  'Імпорт ПромУА',
					'children'  => [],
				],
			]
		]
	);
}