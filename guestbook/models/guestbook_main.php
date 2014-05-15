<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Guestbook_main extends CI_Model {
	
	public function __construct() {
		parent::__construct();
	}

  //получает все записи из гостевой книги
	public function getAllEntries($offset = 0, $row_count = 0) {
		if ($offset >= 0 AND $row_count > 0) {
			$query = $this->db->get('mod_guestbook', $row_count, $offset)->result_array();
		} else {
			$query = $this->db->get('mod_guestbook');
		}
		return $query;
	}
	
	//получает одобренные записи из гостевой книги
	public function getAllowedEntries($offset = 0, $row_count = 0) {
		$this->db->where('status !=', '0');
		if ($offset >= 0 AND $row_count > 0) {
			$query = $this->db->get('mod_guestbook', $row_count, $offset)->result_array();
		} else {
			$query = $this->db->get('mod_guestbook');
		}
		return $query;
	}
	
}