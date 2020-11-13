<?php


namespace JD_Tools;


class ViewBuilder
{
	public $data = [];

	private $tab = array();
	protected $active_tab;
	private $tabs = array();
	private $registry;

	public $tab_content = '';

	public function __construct($registry, &$data = []) {
		$this->registry = $registry;
		$this->data = $data;

		$this->language->load('tool/jd_tools');

		if (!empty($data['route'])) {
			$this->load->language($data['route']);
		}
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
		$this->active_tab = $this->data['active_tab'];

//		$this->tab_content = sprintf($this->language->get('method_not_exists'), $this->active_tab);

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
			'actions' => empty($this->tab['actions'])? [] : $this->tab['actions'],
			'col_content'   =>  empty($this->tab['col_content'])? '' : $this->tab['col_content'],
			'col_1'     =>  empty($this->tab['col_1'])? '' : $this->tab['col_1'],
			'col_2'     =>  empty($this->tab['col_2'])? '' : $this->tab['col_2'],
		);

		$content_method = 'tab_' . $id;
		if ($this->data['active_tab'] != $id ) {
			$this->tab['col_content'] = '<a href="' . $this->createLink('', $id ) . '">Завантажити контент вкладки</a>';
		}

		$this->tabs[$id] = $this->tab;
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
		if (is_null($active_tab)) $url .= '&active_tab=' . $this->data['active_tab'];
		elseif ('' !== $active_tab) $url = '&active_tab=' . $active_tab . $url;

		$route = ($method)? $this->data['route'] . '/' . $method : $this->data['route'];

		$link = $this->url->link( $route, 'user_token=' . $this->session->data['user_token'] . $url, true);
		return $link;
	}
	/**
	 * Додавання лінку із кнопкою на активний таб
	 *
	 * Викликається всередині функції таба Tab_XXX(),
	 * додає кнопку із лінком в нього.
	 * $method - назва функції класу імпорт, параметри не передаються
	 *
	 * @param $name
	 * @param $method
	 * @param string $btn_type
	 * @param bool $disabled
	 */
	public function addAction($name, $method = '', $params = array(), $btn_type = 'primary', $disabled = false) {

		if (!$disabled) {
			$action_url = $this->createLink($method, $this->data['active_tab'], $params);
		}
		$data = array(
			'type' => $disabled? 'default' : (is_null($btn_type)? 'primary' : $btn_type) ,
			'btn_text' => $name,
			'action_url' => isset($action_url)? $action_url : '',
			'disabled' => $disabled,
		);
		$this->tab['actions'][] = $this->load->view('tool/jd_tools/snippets/button', $data);
	}
	public function addActionSeparator() {
		$this->tab['actions'][] = "<hr>";
	}
	public function getAction() {
		if (isset($this->request->get['action'])) {
			$action = $this->request->get['action'];
			switch ($action) {

			}
		}
	}

	/*
	 * Створення форми налаштувань
	 */
	//=============================================
	public function createSettingForm($data) {
		// todo jd params
		$params = [
			'inputs'    => [
				0   =>  'view1',
				1   =>  'view2',
				// ...
			],
		];
//		print_r($data);
		$data['name'] = empty($data['name'])? false : $data['name'];

		//todo jd view
//		$this->addAction('Зберегти', '', ['action' => 'saveSettings']);
		$view = $this->load->view('tool/jd_tools/snippets/setting_form', $data);
		$this->addMessage($view, 'input', 'div', 'content');
	}
	public function createInputField($data) {
		// params
		$params = [
			'label' =>  [
				'id'    =>  '',
				'text'  =>  '',
			],
			'type'  =>  '',
			'placeholder'   =>  '',
			'value' =>  '',
		];
		if(!empty($data['help']) && empty($data['help']['id'])) $data['help']['id'] = $data['id'] . '_help';
		$this->load->model('setting/setting');
		$value = $this->model_setting_setting->getSettingValue($this->module_setting_code . '_' . $data['id']);
		$data['value'] = empty($value)? false : $value;

		// view
		$view = $this->load->view('tool/jd_tools/snippets/input_field', $data);
		return $view;
	}

	/**
	 * Додає повідомлення на активний таб
	 *
	 * Викликається всередині функції таба TsbXXX(),
	 * По-замовчуванню додає повідомлення із текстом і тегом "р" в першу колонку таба ($->tab['col_1'])
	 * якщо $message - масив, то він буде перетворений через print_r($message, 1) і тег замінений на <pre></pre>.
	 *
	 * Колонка задається цифрою, 1 або 2
	 *
	 * @param $message
	 * @param $class        - html tag attribute class
	 * @param string $tag   - html tag
	 * @param int $col_num
	 */
	public function addMessage($message, $class = null, $tag = 'p', $col_num = 1, $message_on = false) {

		if((defined('MESSAGE_ON') && MESSAGE_ON) || $message_on) {
			if (is_array($message) || is_object($message)) {
				$message = print_r($message, 1);
				if (null === $tag || 'p' == $tag) $tag = 'pre';
			}
			if (null === $tag) $tag = 'p';
			if ($class) $class = " class='" . $class . "'";


			$this->tab['col_' . $col_num] = empty($this->tab['col_' . $col_num])?
				"<" . $tag . $class . ">" . $message . "</$tag>"
				: $this->tab['col_' . $col_num] . "<" . $tag . $class . ">" . $message . "</$tag>";
		}
	}
	public function addSMessage($message, $type = 'other_msg') {
		if (!is_string($message)) {
			ob_start();
			var_dump($message);
			$message = ob_get_clean();
		}
		if (isset($this->session->data[$type]))
			$this->session->data[$type] = '<p>' . $message . '</p>' . $this->session->data[$type];
		else $this->session->data[$type] = '<p>' . $message . '</p>';
	}
	public function getSMessages(){
		if (isset($this->session->data['error'])) {
			$this->data['error_warning'] = $this->session->data['error'];

			unset($this->session->data['error']);
		} else {
			$this->data['error_warning'] = '';
		}
		if (isset($this->session->data['success'])) {
			$this->data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$this->data['success'] = '';
		}
		if (isset($this->session->data['other_msg'])) {
			$this->data['other_msg'] = $this->session->data['other_msg'];
			unset($this->session->data['other_msg']);
		} else {
			$this->data['other_msg'] = '';
		}
	}

	public function render() {
		$this->data['tabs'] = $this->getTabs();
		$this->getSMessages();

		$this->data['header'] = $this->load->controller('common/header');
		$this->data['column_left'] = $this->load->controller('common/column_left');
		$this->data['footer'] = $this->load->controller('common/footer');

		return $this->response->setOutput($this->load->view('tool/jd_tools_main', $this->data));
	}
}