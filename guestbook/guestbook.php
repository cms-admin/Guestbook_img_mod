<?php

(defined('BASEPATH')) OR exit('No direct script access allowed');

/**
 * Image CMS
 * Модуль "Гостевая книга"
 */

class Guestbook extends MY_Controller {

	/** Подготовим необходимые свойства для класса */
	private $settings = array();
	public $username_max_len = 30;
	public $message_max_len = 600;
	public $admin_mail = 'admin@localhost';
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
		$this->initSettings();
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

	public function index() {
	
		$this->core->set_meta_tags(lang('Guestbook', 'guestbook'));
		
		$this->load->library('form_validation');
		
		$this->load->model('base');
		
		// Create captcha
		$this->dx_auth->captcha();
		$tpl_data['cap_image'] = $this->dx_auth->get_captcha_image();

		$this->template->add_array($tpl_data);
		
		if (count($_POST) > 0) {
			$this->form_validation->set_rules('name', lang('Your name', 'guestbook'), 'trim|required|min_length[3]|max_length[' . $this->username_max_len . ']|xss_clean');
			$this->form_validation->set_rules('email', lang('Email', 'guestbook'), 'trim|required|valid_email|xss_clean');
			$this->form_validation->set_rules('message', lang('Message', 'guestbook'), 'trim|required|max_length[' . $this->message_max_len . ']|xss_clean');

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
				$this->save($guestbook_data);
			}
		}
		$data['user_id'] = $this->dx_auth->get_user_id();
		$data['guest_allow'] = $this->settings['can_guest'];

		list  ($data['all_items'], $data['all_total']) = $this->base->get_all_gb();
		$data['all_pages'] = $this->base->pagination($data['all_total'], 2);

		$data['pos_items'] = $this->base->get_pos_gb();
		$data['neg_items'] = $this->base->get_neg_gb();


		CMSFactory\assetManager::create()
			->setData($data)
			->registerScript('session')
			->registerScript('frontend')
			->registerStyle('frontend')
			->render('guestbook');
	}
	
	//запись сообщения в базу данных
	private function save($data) {
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


	public function autoload() {
	
	}
	
	private function initSettings(){
		$this->db->select('settings');
		$settings = $this->db->get_where('components', array('name' => 'guestbook'))->row_array();
		$settings = unserialize(implode(',',$settings));
		
		if (count($settings) == 3){
			
			$this->settings = $settings;
			
		} else {

			$settings['admin_email'] = "support@cms-admin.ru";
			$settings['can_guest'] = "1";
			$settings['default_status'] = "0";
			
			$this->settings = $settings;
			$settings = serialize($settings);

			$init_data = array('settings' => $settings);
			$this->updateSettings($init_data);
		}

	}
	
	public function updateSettings($data){
		$this->db->where('name', 'guestbook');
		$this->db->update('components', $data);
	}

	public function _load_settings() {
		$this->db->where('name', 'guestbook');
		$query = $this->db->get('components', 1)->row_array();

		return unserialize($query['settings']);
	}
	
	public function _install() {
	
		/** Подключаем класс Database Forge содержащий функции,
		 *  которые помогут вам управлять базой данных.
		 *  http://ellislab.com/codeigniter/user-guide/database/forge.html */
		
		$this->load->dbforge();

		/** Создаем массив полей и их атрибутов для БД */
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

		/** Указываем на поле, которое будет с ключом Primary */
		$this->dbforge->add_key('id', TRUE);
		/** Добавим поля в таблицу */
		$this->dbforge->add_field($fields);
		/** Запускаем запрос к базе данных на создание таблицы */
		$this->dbforge->create_table('mod_guestbook', TRUE);

		/** Обновим метаданные модуля, включим автозагрузку модуля и доступ по URL */
		$this->db->where('name', 'guestbook')->update('components', array('autoload' => '1', 'enabled' => '1'));
	}

	/**
	 * Метод относиться  к стандартным методам ImageCMS.
	 * Будет вызван при удалении модуля пользователем
	 */
	public function _deinstall() {
		$this->load->dbforge();
		$this->dbforge->drop_table('mod_guestbook');
	}

}

/* End of file sample_module.php */