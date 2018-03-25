<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Bestilling extends CI_Controller {

    function __construct()
    {
        parent::__construct();
		$this->load->helper('menu');
		$this->load->helper('url');
		$this->load->helper('select_quantity');
        $this->load->library('javascript');
    }

    function index() {
        $this->jquery->script('/ressources/jquery-1.6.2.min.js', TRUE);
        $this->javascript->compile();
		$this->load->model('Memberinfo');
		$divisioninfo = $this->Memberinfo->division_info($this->session->userdata('uid'));
		$adress = $this->Memberinfo->retrieve_by_medlemsnummer($this->session->userdata('uid'));
		$mypickups = $this->Memberinfo->pickups_by_member($this->session->userdata('uid'));
		if ($this->session->userdata('active') <> 'paid')
		{
			$annualfee = $this->_get_annualfee();
		} else {
			$annualfee = '';
		}
		$bagfee = $this->_get_bagfee();
		$accountstatus = 0;
		$data = array(
               'title' => 'KBHFF bestilling',
               'heading' => $this->session->userdata('name'),
               'content' => $this->session->userdata('uid'),
			   'status' => $this->session->userdata('active'),
			   'annualfee' => $annualfee,
			   'bagfee' => $bagfee,
			   'accountstatus' => $accountstatus,
			   'mypickups' => $mypickups,
			   'divisioninfo' => $divisioninfo,
			   'bag_quantity' => select_quantity(10),
          );

		$this->load->view('v_bestilling', $data);
    }

	function _get_annualfee()
	{
		$this->db->select('id, amount', FALSE);
		$this->db->from('items');
		$this->db->from('division_members');
		$this->db->where('division_members.member', (int)$this->session->userdata('uid') ); 
		$this->db->where('ff_division_members.division','ff_items.division',FALSE); 
		$this->db->where('ff_items.producttype_id',FF_ANNUALFEE,FALSE); 
		$this->db->limit(1);
		$query = $this->db->get();
		return ($query->result_array());
	}	

	function _get_bagfee()
	{
		$this->db->select('id, amount', FALSE);
		$this->db->from('items');
		$this->db->from('division_members');
		$this->db->where('division_members.member', (int)$this->session->userdata('uid') ); 
		$this->db->where('ff_division_members.division','ff_items.division',FALSE); 
		$this->db->where('ff_items.producttype_id',FF_BAGFEE,FALSE); 
		$this->db->limit(1);
		$query = $this->db->get();
		return ($query->result_array());
	}	

} // class bestilling

/* End of file bestilling.php */
/* Location: ./application/controllers/bestilling.php */