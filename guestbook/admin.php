<?php

(defined('BASEPATH')) OR exit('No direct script access allowed');

/**
 * Image CMS
 * SМодуль "Гостевая книга"
 */
class Admin extends BaseAdminController {

	// количество записей для отображения
	private $per_page = 10;

	public function __construct() {
		parent::__construct();
		$lang = new MY_Lang();
		$lang->load('guestbook');
		$this->load->library('DX_Auth');
	}

	// список всех записей в админке
	public function index() {
		$this->load->model('guestbook_main');
		$settings = $this->get_settings();

		$offset = (int) $this->uri->segment(6);
		$row_count = (int) $settings['per_page'];

		$query = $this->guestbook_main->getAllEntries($offset, $row_count);

		if (count($query)) {
			$this->load->library('Pagination');

			$config['base_url'] = site_url('admin/components/cp/guestbook/index');
			$config['total_rows'] = $this->guestbook_main->getAllEntries()->num_rows();
			$config['per_page'] = $row_count;
			$config['uri_segment'] = $this->uri->total_segments();

			$this->pagination->num_links = 5;
			$this->pagination->initialize($config);
			$this->template->assign('paginator', $this->pagination->create_links_ajax());
			// End pagination
		}

		$this->template->assign('count', $this->guestbook_main->getAllEntries()->num_rows());

		\CMSFactory\assetManager::create()
			->setData('items',$query)
			->registerScript('admin')
			->renderAdmin('list');
	}

	// настройки модуля в админке
	public function settings() {
		\CMSFactory\assetManager::create()
				->setData('settings', $this->get_settings())
				->renderAdmin('settings');
	}

	// загружает настройки модуля из БД
	private function get_settings() {
		return $this->load->module('guestbook')->_load_settings();
	}

	// сохраняет настройки
	public function update_settings() {
		$data = array(
			'admin_email' => $this->input->post('admin_email'),
			'message_max_len' => $this->input->post('message_max_len'),
			'per_page' => (int) $this->input->post('per_page'),
			'can_guest' => (int) $this->input->post('can_guest'),
			'default_status' => (int) $this->input->post('categories'),
		);

		$this->db->where('name', 'guestbook');
		$this->db->update('components', array('settings' => serialize($data)));

		showMessage(lang("Changes have been saved", 'guestbook'));
	}

	// одобрение/отклонение записей
	public function update_status() {
		$this->db->where_in('id', $this->input->post('id'));
		$this->db->update('mod_guestbook', array('status' => $this->input->post('status')));

		showMessage(lang('Status updated', 'guestbook'), lang('Message', 'guestbook'));
		$this->load->helper('url');
		$url = '/' . str_replace(base_url(), '', $_SERVER['HTTP_REFERER']);
		pjax($url);
	}

	// удаление записей
	public function delete() {
		$id = $this->input->post('id');
		if (is_array($id)) {
			$this->db->where_in('id', $id);
			$this->db->delete('mod_guestbook');
		} else {
			$this->db->limit(1);
			$this->db->where('id', $id);
			$this->db->delete('mod_guestbook');
		}

		showMessage(lang('The record (s) removed', 'comments'));

		$this->load->helper('url');
		$url = '/' . str_replace(base_url(), '', $_SERVER['HTTP_REFERER']);
		pjax($url);
	}

	function segment($n) {
		if (array_key_exists($this->uri->segment(1), $this->langs)) {
			$n++;
			return $this->uri->segment($n);
		}

		return $this->uri->segment($n);
	}

}