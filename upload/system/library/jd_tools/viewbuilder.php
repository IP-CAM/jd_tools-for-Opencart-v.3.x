<?php


namespace Library\JD_Tools;


class ViewBuilder
{
	private $data = [];

	private $tab = array();
	private $tabs = array();

	public function __construct($registry, &$data = []) {
		$this->document = $registry->get('document');
		$this->language = $registry->get('language');
		$this->url = $registry->get('url');

		$this->data = $data;
	}

	private function getEnvironment(){
		$this->loadScriptsEnvironment();
		$this->document->addStyle( 'view/stylesheet/jd_tools/main.css');

		$this->document->setTitle($this->language->get('heading_title'));

//		$this->data['breadcrumbs'] = array();
		$this->data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);
		$this->data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('tool/prom_ua_import', 'user_token=' . $this->session->data['user_token'], true)
		);

		$this->data['heading_title'] = $this->language->get('heading_title');
	}

	private function loadScriptsEnvironment() {
		if (isset($this->request->get['active_tab'])) {
			$this->data['active_tab'] = $this->request->get['active_tab'];
		} elseif (isset($this->session->data['active_tab'])) {
			$this->data['active_tab'] = $this->session->data['active_tab'];
			unset($this->session->data['active_tab']);
		} else {
			$this->data['active_tab'] = 'common';
		}

		define('MESSAGE_ON', true);
//		$this->load->language('tool/prom_ua_import');
//
//		library('jd_tools/source');
//		$this->load->model('setting/setting');
//		$filename = $this->model_setting_setting->getSettingValue('prom_ua_import_filename');
//		if($filename) $this->source_config['files']['filename'] = $filename;
//		$this->source = new Library\JD_Tools\Source($this->source_config);
//
//		library('jd_tools/target');
//		$this->target = new Library\JD_Tools\Target($this->target_config, $this->registry);
//
//		if(!defined('DIR_EXPORT_IMAGES'))define('DIR_EXPORT_IMAGES', DIR_UPLOAD . $this->source_config['from'] . '/' . 'images' . '/');
//		define('DIR_IMPORT_IMAGES', DIR_IMAGE . $this->target_config['dir_image']);

	}


}