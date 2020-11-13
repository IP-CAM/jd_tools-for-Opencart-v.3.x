<?php
namespace JD_Tools;

class Source {
	private $config;
	private $reader;
	private $modules;
	private $cache;

	public $module;
	public $data;

	public function __construct($config) {
		$this->config = $config;

		$cache = 'Cache\\File';
		if (class_exists($cache)) {
			$this->cache = new $cache(360000);
		} else {
			throw new \Exception('Error: Could not load cache adaptor ' . $adaptor . ' cache!');
		}
		define('DIR_EXPORT_IMAGES', DIR_UPLOAD . $config['files']['dir'] . DIRECTORY_SEPARATOR . 'images/');
//		define('DIR_IMPORT_IMAGES', DIR_IMAGE);

		if ((version_compare(PHP_VERSION, '7.0.0') >= 0)) {
			// todo Підключати рідери і підкласи як в system/library/cache.php
			$reader_name = 'jd_tools/readers/' . $this->config['from'] . '_' . $this->config['type'] . '_reader';
			library($reader_name);
			$this->reader = new \Library\JD_Tools\Readers\Reader($this->config['files'], $this->cache);
		}

	}

	public function parse_data($tablename) {
		$data = json_decode($this->cache->get('jd_tools_' . str_replace('.', '_', $tablename), true));
		if (!$data) {
			if (!isset($this->reader)) {
				// todo Підключати рідери і підкласи як в system/library/cache.php
				$reader_name = 'jd_tools/readers/' . $this->config['from'] . '_' . $this->config['type'] . '_reader';
				library($reader_name);
				$this->reader = new \Library\JD_Tools\Readers\Reader($this->config['files'], $this->cache);
			}
			if (file_exists(DIR_EXPORT . $tablename)) {
				$data = $this->reader->get($tablename);
			} else return ['error' => 'File ' . $tablename . ' not found'];
		}
		return $data;
	}

	public function parse_data_set($start, $count = 10){
		$data = json_decode($this->cache->get("jd_tools_" . str_replace('.', '_', $tablename) ."_{$count}_from_{$start}"), true);
		if(!$data) {

		}
		return $data;
	}


	public function get_source_info($filename) {
		$data = $this->cache->get('jd_tools_' . str_replace('.', '_', $filename) . '_info');
		if (!$data) {
			if (!isset($this->reader)) {
				// todo Підключати рідери і підкласи як в system/library/cache.php
				$reader_name = 'jd_tools/readers/' . $this->config['from'] . '_' . $this->config['type'] . '_reader';
				library($reader_name);
				$this->reader = new \Library\JD_Tools\Readers\Reader($this->config['files'], $this->cache);
			}
			if (file_exists(DIR_EXPORT . $filename)) {
				$data = $this->reader->getInfo($filename);
			} else return ['error' => 'File ' . $filename . ' not found'];
			$this->cache->set('jd_tools_' . str_replace('.', '_', $filename) . '_info', $data);
		}
		return $data;
	}
	public function get_data_set($filename, $page, $start_row, $chunk_size) {
		$data = $this->cache->get('jd_tools_' . str_replace('.', '_', $filename) .  '_' . str_replace(' ', '_' , $page) . '_from_' . $start_row . '_' . ($chunk_size + 1));
		if(!$data) {
			if (!isset($this->reader)) {
				// todo Підключати рідери і підкласи як в system/library/cache.php
				$reader_name = 'jd_tools/readers/' . $this->config['from'] . '_' . $this->config['type'] . '_reader';
				library($reader_name);
				$this->reader = new \Library\JD_Tools\Readers\Reader($this->config['files'], $this->cache);
			}
			if (file_exists(DIR_EXPORT . $filename)) {
				$data = $this->reader->getDataSet($filename, $page, $start_row, $chunk_size);
			} else return ['error'  => 'File not exists'];
			$this->cache->set('jd_tools_' . str_replace('.', '_', $filename) . '_' . str_replace(' ', '_' , $page) . '_from_' . $start_row . '_' . ($chunk_size + 1), $data);
		}
		return $data;
	}
	public function get_adata_set($filename, $page, $start_row, $chunk_size) {
		$data = $this->cache->get('jd_tools_' . 'adata' . '_from_' . $start_row . '_' . ($chunk_size + 1));
		if(!$data) {
			return false;
		}
		return $data;
	}
	public function save_adata_set($data, $start_row, $chunk_size) {
		$this->cache->set('jd_tools_' . 'adata' . '_from_' . $start_row . '_' . ($chunk_size + 1), $data);
	}
	public function item_adapter($item){
		if (!isset($this->reader)) {
			// todo Підключати рідери і підкласи як в system/library/cache.php
			$reader_name = 'jd_tools/readers/' . $this->config['from'] . '_' . $this->config['type'] . '_reader';
			library($reader_name);
			$this->reader = new \Library\JD_Tools\Readers\Reader($this->config['files'], $this->cache);
		}
		return $this->reader->item_adapter($item);
	}

	public function getColNames($fileName, $pageName) {
		if (!isset($this->reader)) {
			// todo Підключати рідери і підкласи як в system/library/cache.php
			$reader_name = 'jd_tools/readers/' . $this->config['from'] . '_' . $this->config['type'] . '_reader';
			library($reader_name);
			$this->reader = new \Library\JD_Tools\Readers\Reader($this->config['files'], $this->cache);
		}
		return $this->reader->getColNames($fileName, $pageName);
	}
}