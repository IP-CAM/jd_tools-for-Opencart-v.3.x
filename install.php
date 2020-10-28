<?php
$this->load->model('setting/setting');

$jd_settings = $this->model_setting_setting->getSetting('jd_tools_main');
if (empty($jd_settings)) {
	$this->model_setting_setting->editSetting(
		'jd_tools_main',
		[
			'jd_tools_main_menu_items' => [
				[
					'route'  => 'common/dashboard',
					'name'  =>  'Немає елементів',
					'children'  =>  [],
				],
				[
					'route' =>  'tool/prom_ua_import',
					'name'  =>  'Імпорт ПромУА',
					'children'  => [],
				],
			]
		]
	);
}