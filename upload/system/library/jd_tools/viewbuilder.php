<?php


namespace JD_Tools;


class ViewBuilder
{
	private $data = [];

	private $tab = array();
	private $tabs = array();
	private $registry;

	public function __construct($registry, &$data = []) {
		$this->registry = $registry;

		$this->data = $data;
		$this->getEnvironment();
	}

	public function __get($key) {
		return $this->registry->get($key);
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

	public function addTab($id, $name) {
		$this->tab = array(
			'id'    =>  $id,
			'name'  =>  $name,
			'actions' => array(),
			'col_content'   =>  '',
			'col_1'     =>  '',
			'col_2'     =>  '',
		);

		$content_method = 'Tab' . $id;
		if ($this->active_tab == $id ) {
			if (method_exists($this, $content_method)) {
				/*
				 * Виклик функції таба
				 *
				 * очевидним був би виклик через $this->>$content_method
				 * але він запускає виклик через parent::__get() і виходить хуйня.
				 *
				 * Метод через call_user_func_array працює нормально.
				*/
				call_user_func_array(array($this, $content_method), array());
			} else {
				$this->tab['col_content'] = '<p>Функція контенту для таба ' . $id . ' не існує</p>';
			}
		} else {
			$this->tab['col_content'] = '<a href="' . $this->createLink('', $id ) . '">Завантажити контент вкладки</a>';
		}

		$this->tabs[] = $this->tab;
	}
	public function getTabs() {
		foreach ($this->tabs as &$tab) {
			$tab['content'] = $this->load->view('tool/jd_tools/snippets/tab', $tab);
		}
		return $this->tabs;
	}

	public function createLink($method = '', $active_tab = null, $params = array()) {
		if ($params !== array() && $params !== null) {
			$url = '';
			foreach ($params as $key => $value) {
				$url .= "&" . $key . "=" . $value;
			}
		} else $url = '';
		if (is_null($active_tab)) $url .= '&active_tab=' . $this->tab['id'];
		elseif ('' !== $active_tab) $url = '&active_tab=' . $active_tab . $url;

		$route = ($method)? $this->path . $method : substr($this->path, 0, -1);

		$link = $this->url->link( $route, 'user_token=' . $this->session->data['user_token'] . $url, true);
		return $link;
	}

	public function render() {
		$this->data['tabs'] = $this->getTabs();

		$this->data['header'] = $this->load->controller('common/header');
		$this->data['column_left'] = $this->load->controller('common/column_left');
		$this->data['footer'] = $this->load->controller('common/footer');

		return $this->response->setOutput($this->load->view('tool/jd_tools_main', $this->data));
	}
}