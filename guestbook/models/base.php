<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Base extends CI_Model {

	public function __construct() {
		parent::__construct();
	}

  //получает все записи из гостевой книги
	public function get_all_gb() {
		$all_items = array();
		$this->db->select('mod_guestbook.*');
		$this->db->where('status !=', 0);
		$this->db->order_by('date', 'desc'); 
		$query = $this->db->get('mod_guestbook');
		
		if ($query->num_rows() > 0) {
			$all_items = $query->result_array();
			$total_all = count($all_items);
			for ($i = 0; $i < $total_all; $i++) {
				$all_items[$i]['text'] = htmlspecialchars_decode($all_items[$i]['text']);
			}
			return array ($all_items, $total_all);
		} else {
			return FALSE;
		}
	}
	
	public function get_pos_gb() {
		$pos_items = array();
		$this->db->select('mod_guestbook.*');
		$this->db->where('status !=', 0);
		$this->db->where('rate =', 1);
		$this->db->order_by('date', 'desc'); 
		$query = $this->db->get('mod_guestbook');
		
		if ($query->num_rows() > 0) {
			$pos_items = $query->result_array();
			$cnt = count($pos_items);
			for ($i = 0; $i < $cnt; $i++) {
				$pos_items[$i]['text'] = htmlspecialchars_decode($pos_items[$i]['text']);
			}
		}
		return ($pos_items ? $pos_items : FALSE);
	}
	
	public function get_neg_gb() {
		$neg_items = array();
		$this->db->select('mod_guestbook.*');
		$this->db->where('status !=', 0);
		$this->db->where('rate =', 0);
		$this->db->order_by('date', 'desc'); 
		$query = $this->db->get('mod_guestbook');
		
		if ($query->num_rows() > 0) {
			$neg_items = $query->result_array();
			$cnt = count($neg_items);
			for ($i = 0; $i < $cnt; $i++) {
				$neg_items[$i]['text'] = htmlspecialchars_decode($neg_items[$i]['text']);
			}
		}
		return ($neg_items ? $neg_items : FALSE);
	}

	public function pagination($total, $maxrow){
		if ($total > $maxrow) {

			$this->load->library('Pagination');

			$config['base_url'] = site_url('guestbook/');
			$config['total_rows'] = $total;
			$config['per_page'] = $maxrow;
			$config['uri_segment'] = 3;

			$config['first_link'] = lang('First link', 'guestbook');
			$config['last_link'] = lang('Last link', 'guestbook');

			$config['cur_tag_open'] = '<li class="btn-primary active"><span>';
			$config['cur_tag_close'] = '</span></li>';

			$this->pagination->num_links = 2;
			$this->pagination->initialize($config);

			$pagination = $this->pagination->create_links_ajax();
		}
		return ($pagination ? $pagination : FALSE);
	}

}