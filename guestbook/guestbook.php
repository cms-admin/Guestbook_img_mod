<?php

(defined('BASEPATH')) OR exit('No direct script access allowed');

/**
 * Image CMS
 * Модуль "Гостевая книга"
 */

class Guestbook extends MY_Controller {

	/** Подготовим необходимые свойства для класса */
	public $settings = array();
	public $username_max_len = 30;
	public $message_max_len = 600;
	public $message = '';
	protected $formErrors = array();


	public function __construct() {
		parent::__construct();
		$this->formErrors = array(
			'required' => lang('Field is required'),
			'min_length' => lang('Length is less than the minimum'),
			'valid_email' => lang('Email is not valid'),
			'max_length' => lang('Length greater than the maximum')
		);
		$lang = new MY_Lang();
		$lang->load('guestbook');
		$this->_initSettings();
	}

	function captcha_check($code) {
		if (!$this->dx_auth->captcha_check($code))
			return FALSE;
		else
			return TRUE;
	}

	function recaptcha_check() {
		$result = $this->dx_auth->is_recaptcha_match();
		if (!$result) {
			$this->form_validation->set_message('recaptcha_check', lang("Improper protection code", 'guestbook'));
		}
		return $result;
	}

	/* ------------------------------------------------------- */
	/* ------         вывод всех записей на экран       ------ */
	/* ------------------------------------------------------- */

	public function index() {
		$this->core->set_meta_tags(lang('All entries in guestbook', 'guestbook'));
		$this->load->model('guestbook_main');
		$settings = $this->_load_settings();

		$offset = (int) $this->uri->segment(3);
		$row_count = (int) $settings['per_page'];

		$query = $this->guestbook_main->getAllowedEntries($offset, $row_count);

		if (count($query)) {
			$this->load->library('Pagination');

			$config['base_url'] = site_url('guestbook/index');
			$config['total_rows'] = $this->guestbook_main->getAllowedEntries()->num_rows();
			$config['per_page'] = $row_count;
			$config['uri_segment'] = $this->uri->total_segments();

			$this->pagination->num_links = 5;
			$this->pagination->initialize($config);
			$this->template->assign('paginator', $this->pagination->create_links_ajax());
			// End pagination
		}

		$this->template->assign('count', $this->guestbook_main->getAllowedEntries()->num_rows());

		\CMSFactory\assetManager::create()
			->setData('items',$query)
			->registerStyle('frontend')
			->registerStyle('icons')
			->render('all');
	}

	/* ------------------------------------------------------- */
	/* ------     вывод формы для отправки сообщения    ------ */
	/* ------------------------------------------------------- */

	public function message() {
		$this->core->set_meta_tags(lang('Leave message in the guest book', 'guestbook'));
		$this->load->library('form_validation');
		$settings = $this->_load_settings();
		// Create captcha
		$this->dx_auth->captcha();
		$tpl_data['cap_image'] = $this->dx_auth->get_captcha_image();

		$this->template->add_array($tpl_data);

		if (count($_POST) > 0) {
			$this->form_validation->set_rules('name', lang('Your name', 'guestbook'), 'trim|required|min_length[3]|max_length[' . $this->username_max_len . ']|xss_clean');
			$this->form_validation->set_rules('email', lang('Email', 'guestbook'), 'trim|required|valid_email|xss_clean');
			$this->form_validation->set_rules('message', lang('Message', 'guestbook'), 'trim|required|max_length[' . $settings['message_max_len'] . ']|xss_clean');

			if ($this->dx_auth->use_recaptcha)
				$this->form_validation->set_rules('recaptcha_response_field', lang("Protection code", 'guestbook'), 'trim|xss_clean|required|callback_recaptcha_check');
			else
				$this->form_validation->set_rules('captcha', lang("Protection code", 'guestbook'), 'trim|required|xss_clean|callback_captcha_check');

			if ($this->form_validation->run($this) == FALSE) { // there are errors
				$this->form_validation->set_error_delimiters("", "");
				CMSFactory\assetManager::create()->setData('validation', $this->form_validation);
			} else { // form is validate
				$guestbook_data = array(
					'user_id' => $this->dx_auth->get_user_id(), // 0 if unregistered
					'user_name' => htmlspecialchars($this->input->post('name')),
					'user_mail' => $this->input->post('email'),
					'text' => strip_tags($this->input->post('message')),
					'status' => $this->settings['default_status'],
					'date' => time(),
					'rate' => $this->input->post('rate'),
				);
				$this->message = strip_tags(nl2br(
					lang('Name', 'guestbook') . ' : ' . $this->input->post('name') .
					lang('E-mail', 'guestbook') . ' : ' . $this->input->post('email') .
					lang('Message', 'guestbook') . ' : ' . $this->input->post('message')
				));
				$this->_send_message();
				$this->_save($guestbook_data);
			}
		}

		$this->template->assign('user_id', $this->dx_auth->get_user_id());
		$this->template->assign('guest_allow', $settings['can_guest']);
		$this->template->assign('message_max_len', $settings['message_max_len']);

		CMSFactory\assetManager::create()
			->registerScript('frontend')
			->registerStyle('frontend')
			->registerStyle('icons')
			->render('message');
	}

	//запись сообщения в базу данных
	private function _save($data) {
		$this->db->insert('mod_guestbook', $data);
		return $this->db->insert_id();
	}

	// Отправка сообщения админу
	private function _send_message() {
		$config['charset'] = 'UTF-8';
		$config['wordwrap'] = FALSE;

		$this->load->library('email');
		$this->email->initialize($config);

		$this->email->from($this->input->post('email'), $this->input->post('name'));
		$this->email->to($this->settings['admin_email']);

		$this->email->subject(lang('New message in Guestbook', 'guestbook'));
		$this->email->message($this->message);

		$this->email->send();

		CMSFactory\assetManager::create()->appendData('message_sent', TRUE);
	}

	/* ------------------------------------------------------- */
	/* ------       инициализация настроек модуля       ------ */
	/* ------------------------------------------------------- */

	function _initSettings(){
		$this->db->select('settings');
		$settings = $this->db->get_where('components', array('name' => 'guestbook'))->row_array();
		$settings = unserialize(implode(',',$settings));

		if (count($settings) == 5){

			$this->settings = $settings;

		} else {
			$settings = array(
				'admin_email' => 'support@cms-admin.ru',
				'message_max_len' => '600',
				'per_page' => '10',
				'can_guest' => '1',
				'default_status' => '0',
			);

			$this->settings = $settings;
			$settings = serialize($settings);

			$init_data = array('settings' => $settings);
			$this->_updateSettings($init_data);
		}

	}

	// обовляет настройки модуля
	function _updateSettings($data){
		$this->db->where('name', 'guestbook');
		$this->db->update('components', $data);
	}

	public function _load_settings() {
		$this->db->where('name', 'guestbook');
		$query = $this->db->get('components', 1)->row_array();

		return unserialize($query['settings']);
	}

	public function _install() {
		$this->load->dbforge();
		$fields = array(
			'id'				=> array('type' => 'INT', 'constraint' => 11, 'auto_increment' => TRUE,),
			'user_id'		=> array('type' => 'INT', 'constraint' => 11,),
			'user_name'	=> array('type' => 'VARCHAR', 'constraint' => 50,),
			'user_mail'	=> array('type' => 'VARCHAR', 'constraint' => 50,),
			'text'			=> array('type' => 'TEXT',),
			'date'			=> array('type' => 'INT', 'constraint' => 11,),
			'status'		=> array('type' => 'SMALLINT', 'constraint' => 1,),
			'rate'			=> array('type' => 'SMALLINT', 'constraint' => 1,)
		);

		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->add_field($fields);
		$this->dbforge->create_table('mod_guestbook', TRUE);

		$this->db->where('name', 'guestbook')->update('components', array('in_menu' => '1', 'enabled' => '1'));
		$this->_initSettings();
	}

	public function _deinstall() {
		$this->load->dbforge();
		$this->dbforge->drop_table('mod_guestbook');
	}

}

/* End of file sample_module.php */
