<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class kontantordrer extends CI_Controller {

	protected $viewdata;

    function __construct()
    {
        parent::__construct();
		$this->load->helper('url');
		$this->load->helper('menu');
		$this->load->model('account');
		$this->load->library('session');
		$this->load->model('permission');
		$this->load->helper('danish_date');
		$this->load->helper('date');
		$this->load->helper('select_quantity');
        $this->load->library('javascript');
		$this->load->model('Memberinfo');
		if (! intval($this->session->userdata('uid')) > 0)
			redirect(base_url().'index.php/');
	}
	
	function index($division = 0)
	{
		//We detect a repost - discard
		if ($this->session->userdata('timestamp') > 0 && $this->session->userdata('timestamp') == $this->input->post('timestamp'))
		{
			redirect(base_url().'index.php/kontantordrer');	
			exit();
		}
		
		$permissions = $this->session->userdata('permissions');
		$p_administrator = $this->Memberinfo->checkpermission($permissions, 'Administrator', $division);
		$p_kassemester   = $this->Memberinfo->checkpermission($permissions, 'Kassemester', $division);
		$p_infovagt   	= $this->Memberinfo->checkpermission($permissions, 'Info + lukkevagt', $division);

		if (!(($p_administrator) || ($p_kassemester)|| ($p_infovagt)))
			redirect("/login/logoff");
		if ($this->input->post('items')> 0)
		{
				if ($this->input->post('mobilepay'))
				{
					$method = 'mobilepay';
				} else {
					$method = 'kontant';
				}
				$this->_debit_order($this->input->post('puid'),$this->input->post('pickupday'), $this->input->post('item'), $this->input->post('quant'),$this->input->post('items'), $method);
		}
		
		$this->viewdata['name'] = $this->input->post('name');
		
		if ($this->input->post('name') != '')
		{
			$this->viewdata['members'] = $this->Memberinfo->search_member($this->input->post('name'), $division);
		}	
/*
		else
		{
			$this->viewdata['members'] = $this->Memberinfo->get_members($division);
		}
*/
		
		$this->jquery->script('/ressources/jquery-1.6.2.min.js', TRUE);
		$js =$this->jquery->corner('#tt');
		$this->javascript->compile();
		$this->javascript->output($js);
		$this->viewdata['title'] = 'Kontant/mobilepay ordrer';
		$this->viewdata['pickups'] = $this->Memberinfo->pickup_uid_by_division($division);
		$this->viewdata['annualfee'] = $this->Memberinfo->annualfee_uid_by_division($division);
		$this->viewdata['bagfee'] = $this->Memberinfo->bagfee_uid_by_division($division);
		$this->viewdata['division'] = $division;
		$this->viewdata['divisionname'] = $this->_divisionname($division);
		$this->viewdata['bag_quantity'] = select_quantity(10);

		$this->load->view('v_kontantordrer', $this->viewdata);

	}
	
	function annuller($orderno = 0)
	{

		if ($this->uri->segment(3) > 0)
		{
			$orderno = $this->uri->segment(3);
		} else {
			$orderno = $this->input->post('orderno');
		}
		$division = $this->_get_division_from_orderno($orderno);
		$permissions = $this->session->userdata('permissions');
		$p_administrator = $this->Memberinfo->checkpermission($permissions, 'Administrator', $division);
		$p_kassemester   = $this->Memberinfo->checkpermission($permissions, 'Kassemester', $division);

		if (! (($p_administrator) || ($p_kassemester)))
		{
			$this->viewdata['content'] = 'Du har ikke rettigheder til at redigere ordre ' . $orderno . ' (afdeling ' . (int)$division . ').';
		} else {
			if ($this->_check_kontant_order($orderno))
			{
				$this->_nullify_order($orderno);
				$this->viewdata['content'] = 'Ordre ' . $orderno . ' er annulleret.';
			} else {
				$this->viewdata['content'] = 'Ordre ' . $orderno . ' kan ikke annulleres - det er ikke en kontant/mobilepay ordre eller den er allerede annulleret.';
			}
		}
		$this->viewdata['heading'] = 'Annullering af kontant/mobilepay ordre';
		$this->jquery->script('/ressources/jquery-1.6.2.min.js', TRUE);
//		$js =$this->jquery->corner('#tt_del');
		$this->javascript->compile();
		$this->javascript->output($js);
		$this->load->view('page', $this->viewdata);

	}


	function _debit_order($puid,$pickupday, $item, $quant, $items, $method = 'ukendt')
	{

		$qsum = 0;
		while (list($key, $q) = each($quant)) 
		{
			$qsum += $q;
		}
		if (($puid> 0)&&($qsum >0))
		{
			$neworder = $this->_createorder($puid,$method,$pickupday, $item, $quant);
			$this->viewdata['kvit'] = $neworder['kvit'];
			$this->viewdata['kvitol'] = $neworder['kvitol'];
			$this->session->set_userdata('timestamp', $this->input->post('timestamp'));
			$member = $this->Memberinfo->retrieve_by_medlemsnummer($puid);
			$this->viewdata['message'] .= 'Medlem ' . $puid . ', ' . $member['firstname'] . ' ' . $member['middlename'] . ' ' . $member['lastname'] . '<br>';
			$this->viewdata['message'] .= 'Registreret ordre ' . $neworder['orderno'] . ', ' . $neworder['amount'] . ' kr. (' . $method . ') :';
			$this->viewdata['message'] .= ' <a href="/kontantordrer/annuller/' . $neworder['orderno'] . '">Fortryd</a>';
		} else {
			$this->viewdata['errors'] = 'Du skal vælge mindst ét medlem fra listen med antal p&aring; mindst 1. Intet foretaget.';
		}			
		return;		
	}


	function _createorder($puid,$method,$pickupday, $item, $quant)
	{
		$neworder = createuniqueorderno($puid, $method);
		// Create orderlines
		$orderno = $neworder['orderno'];
		$orderkey = $neworder['orderkey'];
		$counter = 0;
		$amount = 0;
		while (list($key, $date) = each($pickupday)) 
		{
		    $price = $this->_getitemprice($this->input->post('division'), $item[$counter]) ;
			if ($price['error'] == '')
			{
				$status3 = 'OK';
			} else {
				$status3 = 'priceerror';
			}
			if ($quant[$counter] > 0)
			{
				$lineamount = $price['price'] * $quant[$counter];
				$amount += $lineamount;
			    $this->_insertorderline($orderno, $orderkey, $puid, $item[$counter], $quant[$counter], $lineamount, $vat_amount,$this->session->userdata('uid'), $date, $status3,$this->input->post('division')) ;
			}
			$counter++;
		}

	    $this->_updateorderhead($orderno, $orderkey, $amount, $vat_amount,$status3, $method, $created_by) ;
		$this->_update_kontant_transactions($orderno, $puid, $amount, $method);
		if ($this->_getannualfeeorders($orderno)>0)
		{
			$data = array(
               'active' => 'paid'
            );
			$this->db->where('uid', $puid);
			$this->db->limit(1);
			$this->db->update('persons', $data);
		} 

		$kvit = kvitgetorderhead($orderno, $orderkey, '', '');
		$emailkvittering = kvitgetorderlines($orderno, $orderkey, $kvit['kvittering']);
		sendreceipt($emailkvittering, $orderno, $kvit['email'], $kvit['firstname'], $kvit['middlename'], $kvit['lastname']);
		$kvitol = kvitgetorderlines($orderno, $orderkey, '');
		$return = array();
		$return['orderno'] = $orderno;
		$return['amount'] = $amount;
		$return['kvit'] = $kvit;
		$return['kvitol'] = nl2br(htmlentities($kvitol));
		return ($return);
	}

	
	
    function _insertorderline($orderno, $orderkey, $puid, $item, $quant, $amount, $vat_amount,$created_by, $iteminfo, $status3, $division) 
	{
		$itemid = $this->_getitemid($item, $iteminfo, $division);
		$this->db->set('orderno', $orderno);
		$this->db->set('orderkey', $orderkey);
		$this->db->set('puid', $puid);
		$this->db->set('item', $itemid);
		$this->db->set('iteminfo', $iteminfo);
		$this->db->set('quant', $quant);
		$this->db->set('amount', $amount);
		$this->db->set('vat_amount', $vat_amount);
		$this->db->set('status1', $created_by);
		$this->db->set('status3', $status3 .'');
		$this->db->insert('orderlines');
	}

function _update_kontant_transactions($orderno, $puid, $amount, $method = 'ukendt')
{
	$amount = doubleval($amount);
	$orderno = doubleval($orderno);
	$puid = doubleval($puid);

		$this->db->set('puid', $puid);
		$this->db->set('amount', $amount);
		$this->db->set('authorized_by', $this->session->userdata('uid'));
		$this->db->set('orderno', $orderno);
		$this->db->set('method', $method);
		$this->db->set('trans_id', '0');
		$this->db->set('item', 0);
		$this->db->set('created', 'now()', FALSE);
		$this->db->insert('transactions');

} // _update_kontant_transactions


    function _updateorderhead($orderno, $orderkey, $amount, $vat_amount,$status3, $method, $created_by) 
	{
		$data = array(
		               'cc_trans_amount' => $amount,
		               'cc_trans_vat_amount' => $vat_amount,
		               'cc_trans_no' => 0,
		               'status1' => $method,
		               'status2' => $created_by,
		               'status3' => $status3
		            );
		
		$this->db->set('cc_trans_date', 'now()', FALSE);
		$this->db->set('changed', 'now()', FALSE);
		$this->db->where('orderno', $orderno);
		$this->db->where('orderkey', $orderkey);
		$this->db->update('orderhead', $data);
	}

	function _getitemprice($division, $item)
	{
		$this->db->select('amount')->from('items')->where('division', $division)->where('producttype_id', $item)->limit(1);
		$query = $this->db->get();
		if ($query->num_rows() > 0)
		{
			$row = $query->row();
			$return['price'] = $row->amount;
			$return['error'] = '';
		} else {
			$return['price'] = 0;
			$return['error'] = 'Varen findes ikke!';
		}
		return $return;
	}

	private function _divisionname($division)
	{
			$this->db->protect_identifiers('divisions', TRUE);		
			$this->db->select('name');
			$this->db->from('divisions');
			$this->db->where('uid', (int)$division); 
			$query = $this->db->get();
			$row = $query->row();
			return $row->name;
	}

    function _getitemid($producttype_id, $itemday, $division) 
	{
		if ($itemday == 0) // eq not a bagorder
		{
			$this->db->select('id')
			->from('ff_items')
			;
			$where = 'ff_items.producttype_id = ' . (int)$producttype_id . ' and ff_items.division = ' . (int)$division ;
			$this->db->where($where, NULL, FALSE);
			$query = $this->db->get();
			$row = $query->row();
			return $row->id;
		} else { // bagorder
		
			$this->db->select('id')
			->from('ff_items')
			->from('ff_pickupdates')
			;
			$where = 'ff_items.division = ff_pickupdates.division and ff_pickupdates.uid = ' . (int)$itemday . ' and ff_items.producttype_id = ' . (int)$producttype_id  ;
			$this->db->where($where, NULL, FALSE);
			$query = $this->db->get();
			$row = $query->row();
			return $row->id;
		}
	}

    function _getday($dateid) 
	{
		$this->db->select('pickupdate')
		->from('ff_pickupdates')
		;
		$where = 'ff_pickupdates.uid = ' . (int)$dateid;
		$this->db->where($where, NULL, FALSE);
		$query = $this->db->get();
		$row = $query->row();
		return $row->pickupdate;
	}

    function _check_kontant_order($orderno) 
	{
		$this->db->select('status1')
		->from('ff_orderhead')
		;
		$where = 'ff_orderhead.orderno = ' . (int)$orderno;
		$this->db->where($where, NULL, FALSE);
		$query = $this->db->get();
		$row = $query->row();
		if (($row->status1 == 'kontant')||($row->status1 == 'mobilepay'))
		{
			return TRUE;
		} else {
			return FALSE;
		}
	}

	function _get_division_from_orderno($orderno)	
	{
		$this->db->select('division')
		->from('ff_pickupdates')
		->from('ff_orderlines')
		;
		$where = 'ff_orderlines.orderno = ' . (int)$orderno . ' and ff_orderlines.iteminfo = ff_pickupdates.uid' ;
		$this->db->where($where, NULL, FALSE);
		$query = $this->db->get();
		$row = $query->row();
		return $row->division;
	}

	function _nullify_order($orderno)
	{
		$data = array(
		               'status1' => 'annulleret',
					   'status2' => $this->session->userdata('uid'),
		            );
		$this->db->set('changed', 'now()', FALSE);
		$this->db->where('orderno', $orderno);
		$this->db->update('orderhead', $data);
	}
	
    function _getannualfeeorders($orderno) 
	{
		$this->db->select('ff_orderlines.item')
		->from('ff_orderlines')
		->from('ff_items')
		->from('ff_producttypes')
		;
		$where = "ff_orderlines.orderno = $orderno 
		AND ff_orderlines.item = ff_items.id
		AND ff_items.producttype_id = " . FF_ANNUALFEE . "
		AND ff_orderlines.iteminfo = 0
		
		";
		$this->db->where($where, NULL, FALSE);
		$query = $this->db->get();
		return $query->num_rows();
	}
	
	
/// NON-CODEIGNITER EXTERNAL FUNCTIONS FOLLOWS

	
}
	include("ressources/.mysql_common.php");
	include("ressources/.library.php");
	include("ressources/.kvittering.php");
	include("ressources/.sendmail.php");


?>