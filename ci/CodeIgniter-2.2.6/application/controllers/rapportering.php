<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Rapportering extends CI_Controller {

    function __construct()
    {
        parent::__construct();
//        $this->load->library('javascript');
		$this->load->helper('menu');
		$this->load->helper('url');
		$this->load->model('Permission');
		$this->load->model('Memberinfo');
		$this->load->model('Report');
    }

    function index() {
		if (! intval($this->session->userdata('uid')) > 0)
			redirect('/login');		

//        $this->jquery->script('/ressources/jquery-1.6.2.min.js', TRUE);
//        $this->javascript->compile();
		$permissions = $this->session->userdata('permissions');

		$this->db->select('divisions.name, divisions.uid');
		$this->db->from('divisions');
		$this->db->order_by('divisions.name'); 
		$query = $this->db->get();

		foreach ($query->result_array() as $row)
		{
			$p_administrator = $this->Memberinfo->checkpermission($permissions, 'Administrator', $row['uid']);
			$p_kassemester   = $this->Memberinfo->checkpermission($permissions, 'Kassemester', $row['uid']);
			if (($p_administrator) || ($p_kassemester))
			{
				$sel .= '<a href="/rapportering/kassemester/' . $row['uid'] . '">Dagrapport, ' .$row['name'] . "</a><br>\n";
			}
		}
		

		$data = array(
               'title' => 'KBHFF Administrationsside',
               'heading' => 'KBHFF Administrationsside: Rapportering',
			   'content' => 'Test af rapportering af dagens salg, kassebev&aelig;gelser m.m.<br>' . $sel,
          );

		$this->load->view('page', $data);
    }

	function kassemester()
	{

		$pickupday = $this->input->post('pickupday');
		if ($this->input->post('status') == 'update')
		{
			$this->Report->save_form_data($pickupday, 'kassemester');
		}

		if ($this->uri->segment(3) > 0)
		{
			$division = $this->uri->segment(3);
		}	else    {
			$division = $this->input->post('division');
		}
			$this->db->select('name');
			$this->db->select('comment');
			$this->db->select('sort');
			$this->db->select('uid');
			$this->db->select('editable');
			$this->db->select('noterequired');
			$this->db->from('reportfields');
			$this->db->where('type', 'kassemester'); 
			$this->db->order_by('sort'); 
			$query = $this->db->get();
			$fields = $query->result_array();
			
			$data = $this->Report->getdata($pickupday,$division,'kassemester');

		
		
		$data = array(
               'title' => 'KBHFF Administrationsside',
               'heading' => $this->_divisionname($division),
			   'content' => 'Test af rapportering af dagens salg, kassebev&aelig;gelser m.m. Bel&oslash;b angives med komma som decimaltegn.<br>',
			   'afhentningsdage' => $this->Report->pickupdates($division),
			   'division' =>$division,
			   'pickupday' => $pickupday,
			   'pickupdayexpl' => $this->Report->get_pu_date($pickupday),
			   'fields' => $fields,
			   'data' => $data,
			   'weektotals' => $this->_weektotals($division, $this->Report->get_pu_date($pickupday)),
          );

		$this->load->view('v_rapportering', $data);
	}
	
	function salg()
	{
	
		$year = 2012;
		$col = 0;
		while ($year <=  date('Y'))
		{
			$month = 1;
			while ($month <= 12)
				{
							$this->db->select('name,  uid');
							$this->db->from('divisions');
							$this->db->order_by('name'); 
							$query = $this->db->get();
							$divisions = $query->num_rows();
							
							foreach ($query->result_array() as $row)
							{
								$amount = $this->_monthtotals($row['uid'], $year, $month);
								$data["$year"]["$month"][$row['uid']] = array('amount' => $amount);	
							}
					$col++;
					$month++;
				}
			$year++;
		}
/*
						$viewdata['title'] = 'test';
				$viewdata['heading'] = 'salg';
				$viewdata['content'] = print_r($data); 

				$this->load->view('page', $viewdata);
*/
		$this->_salgexcel($data);
	}

	private function _monthtotals($division, $year, $month)
	{
	
	$query = $this->db->query('
	SELECT sum(cc_trans_amount) as Total, year(created) as year, month(created) as month 
FROM 
	( 
	ff_orderhead, ff_division_members, ff_divisions
	)
WHERE 
((status1 = "nets") or (status1 = "kontant")or (status1 = "mobilepay"))
and
puid = ff_division_members.member
and ff_divisions.uid = ff_division_members.division
and year(created) = ' . (int)$year . '
and month(created) = ' . (int)$month . '
and ff_divisions.uid = ' . (int)$division . ' 
group by year, month
');
		$monthtotals = $query->result_array();
		
		return $monthtotals[0]['Total'];
	}
	
	private function _salgexcel($data)
	{
	
		/** Include PHPExcel */
//		require_once 'PHPExcel.php';
		require_once($_SERVER["LOCAL_PATH"]."/../ci/PHPExcel-1.8/Classes/PHPExcel.php");
		$this->load->helper('date');

		$locale = 'da';
		date_default_timezone_set('Europe/London');
		$now = Date("H:i d-m-Y");

		// Create a workbook
		$objPHPExcel = new PHPExcel();
		PHPExcel_CachedObjectStorageFactory::cache_in_memory_gzip;
		$objPHPExcel->getProperties()->setCreator("KBHFF Medlemssystem");
		$objPHPExcel->getProperties()->setLastModifiedBy("KBHFF Medlemssystem $now");
		$objPHPExcel->getProperties()->setTitle( utf8_decode($divisionname) . ' medlemsliste');
		$objPHPExcel->getProperties()->setSubject("Medlemsliste");
		$objPHPExcel->getProperties()->setDescription('KBHFF ' . $divisionname . "medlemsliste udskrevet $now");
		$objPHPExcel->getProperties()->setKeywords("KBHFF medlemsliste");
		$objPHPExcel->getProperties()->setCategory("medlemsliste");
		$objPHPExcel->getSheet(0);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setFitToWidth(1);
		$objPHPExcel->getActiveSheet()->getPageSetup()->setFitToHeight(0);

		// Rename worksheet
		$objPHPExcel->getActiveSheet()->setTitle(substr ( 'KBHFF_salg_ ' . Date("H.i d-m-Y"), 0, 31 ));

		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$objPHPExcel->setActiveSheetIndex(0);
		$objWorksheet = $objPHPExcel->getActiveSheet();
		$objWorksheet->getTabColor()->setRGB('33cc66');



		// Creating a title
		$objWorksheet = $objPHPExcel->getActiveSheet();
		$objWorksheet->getStyle('A1:I1')->getFont()->setSize(13)->getColor()->setARGB(PHPExcel_Style_Color::COLOR_DARKGREEN);
		$objWorksheet->setCellValueByColumnAndRow(0, 1, 'Afdeling');

		// Autoset widths
		$objWorksheet = $objPHPExcel->getActiveSheet();
		$objWorksheet->getColumnDimension('A')->setAutoSize(true);

		$rowformat1 = array(
		'font' => array(
			'bold' => false,
			),
		'fill' => array(
			'type' => PHPExcel_Style_Fill::FILL_SOLID,
			'color' =>  array(
				'rgb' =>  'd9ffe2',
				),
			)
		);

		$rowformat2 = array(
		'font' => array(
			'bold' => false,
			)
		);

		$this->db->select('name,  uid');
		$this->db->from('divisions');
		$this->db->order_by('name'); 
		$query = $this->db->get();
		$divisions = $query->num_rows();
		$currentrow = 2;
		foreach ($query->result() as $row)
		{
			$objPHPExcel->setActiveSheetIndex(0)
				->setCellValueByColumnAndRow(0,$currentrow, ("$row->name"));
			$dynformat = alternator('rowformat1', 'rowformat2');
			$format = $$dynformat;
			$objPHPExcel->getActiveSheet()->getStyle('A' . $currentrow . ':A' . $currentrow )->applyFromArray($format);

			$year = 2012;
			$col = 1;
			while ($year <=  date('Y'))
			{
				$month = 1;
				while ($month <= 12)
					{
						if ($currentrow == 2)
						{
							$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($col,1, ("$month/$year"));
						} 
						$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($col,$currentrow, ($data["$year"]["$month"]["$row->uid"]['amount']));
						$col++;
						$month++;
					}
				$year++;
			}
			$currentrow++;
		}

		// Align
		$objWorksheet = $objPHPExcel->getActiveSheet();
		$highestRow = $objWorksheet->getHighestRow();
		$objPHPExcel->getActiveSheet()->getStyle('F1:F' . $highestRow)
			->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		$objPHPExcel->getActiveSheet()->getStyle('A1:A' . $highestRow)
			->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

		// Set repeated headers
		$objPHPExcel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(1, 1);

		// Specify printing area
		$objWorksheet = $objPHPExcel->getActiveSheet();
		$highestRow = $objWorksheet->getHighestRow();
		$highestColumn = $objWorksheet->getHighestColumn();
		$objPHPExcel->getActiveSheet()->getPageSetup()->setPrintArea('A1:' . $highestColumn . $highestRow );


		// Redirect output to a clients web browser (Excel5)
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="KBHFF medlemsliste ' . $divisionname . ' ' . $now .'.xls"');
		header('Cache-Control: max-age=0');
		// If you're serving to IE 9, then the following may be needed
		header('Cache-Control: max-age=1');

		// If you're serving to IE over SSL, then the following may be needed
		header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
		header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
		header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
		header ('Pragma: public'); // HTTP/1.0

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
	}


	function statistik()
	{

		$this->db->select('name,  uid');
		$this->db->from('divisions');
		$this->db->where('type', 'aktiv'); 
		$this->db->order_by('name'); 
		$query = $this->db->get();
		$divisions = $query->num_rows();
		
		foreach ($query->result_array() as $row)
		{
			$data[$row['uid']]['name'] = $row['name'];	
			$data[$row['uid']]['count'] = $this->_getno('active',$row['uid']);	
			$data[$row['uid']]['M'] = $this->_getno('active',$row['uid'],'M');	
			$data[$row['uid']]['F'] = $this->_getno('active',$row['uid'],'F');	
			$data[$row['uid']]['active'] = $this->_getno('active',$row['uid'],'', 'yes');	
			$data[$row['uid']]['lastmonth'] = $this->_getno('active',$row['uid'],'', '', 'yes');	
			$data[$row['uid']]['privacy'] = $this->_getno('active',$row['uid'],'', '', '','yes');	
			$data[$row['uid']]['email'] = $this->_getno('active',$row['uid'],'', '', '','','yes');	
			$data[$row['uid']]['kollektiver'] = $this->_getkollektiver($row['uid']);	
			$data[$row['uid']]['nets'] = $this->_getsale('nets',$row['uid']);	
			$data[$row['uid']]['kontant'] = $this->_getsale('kontant',$row['uid']);	
			$data[$row['uid']]['medlemssystem'] = $this->_getsale('',$row['uid']);	
			$data[$row['uid']]['purchaselastmonth'] = $this->_getsale('',$row['uid'],'yes');	
		}
		$this->_savehistory($data); 
		
		$data = array(
               'title' => 'KBHFF Statistik',
               'heading' => 'KBHFF Statistik',
			   'content' => 'N&oslash;gletal for de forskellige afdelinger i KBHFF<br>',
			   'divisions' => $divisions,
			   'data' => $data,
          );

		$this->load->view('v_statistik', $data);
	}

	
	
	private function _savehistory($ary)
	{
			$this->db->query('replace ff_statistics_log (ary, date) values ("' . mysql_real_escape_string(serialize($ary)) . '", curdate())');
	}
	
	private function _getno($type,$division, $sex = '', $activated = '', $lastlogin = '', $privacy = '', $email = '')
	{
			$this->db->select('uid');
			$this->db->from('persons');
			$this->db->from('division_members ');
			$this->db->where('active', 'paid'); 
			if ($sex > '')
			{
				$this->db->where('sex', $sex); 
			}
			if ($activated > '')
			{
				$this->db->where('password > ""');
			}
			if ($lastlogin > '')
			{
				$this->db->where('last_login > DATE_SUB(curdate(), INTERVAL 31 DAY)');
			}
			if ($privacy > '')
			{
				$this->db->where('privacy', 'y'); 
			}
			if ($email > '')
			{
				$this->db->where('email > ""');
			}
		
			$this->db->where('division', $division); 
			$this->db->where('member', 'uid', false); 
			$query = $this->db->get();
			$row = $query->row();
// echo ('<!--' . $this->db->last_query() . '-->');
			return $query->num_rows();
	}

	private function _getkollektiver($division)
	{
			$this->db->select('puid');
			$this->db->from('persons_info');
			$this->db->from('persons');
			$this->db->from('division_members');
			$this->db->where('active', 'paid'); 
			$this->db->where('uid', 'puid', false); 
			$this->db->where('member', 'puid', false); 
			$this->db->where('kollektiv > ""');
			$this->db->where('division', $division); 
			$query = $this->db->get();
			$row = $query->row();
 // echo ("\n".'<!--' . $this->db->last_query() . '-->');
			return $query->num_rows();
	}

	private function _getsale($type,$division, $month = '')
	{
			$this->db->select('orderlines.puid');
			$this->db->from('orderhead');
			$this->db->from('orderlines');
			$this->db->from('division_members');
			$this->db->where('member', 'ff_orderlines.puid', false); 
			$this->db->where('division', $division); 
			if ($month > '')
			{
				$this->db->where('orderhead.created > DATE_SUB(curdate(), INTERVAL 31 DAY)');
			}
			$this->db->where('ff_orderhead.orderno', 'ff_orderlines.orderno', false); 
			$this->db->distinct();
			if ($type == 'nets')
			{
				$this->db->where('ff_orderhead.status1', 'nets'); 
			}
			if ($type == 'kontant')
			{
				$this->db->where('ff_orderhead.status1', 'kontant'); 
			}
			if ($type == '')
			{
				$where = "(ff_orderhead.status1='kontant' OR ff_orderhead.status1='nets')";
				$this->db->where($where);
			}
			$query = $this->db->get();
			$row = $query->row();
  echo ("\n".'<!--' . $this->db->last_query() . '-->');
			return $query->num_rows();
	}

	private function _weektotals($division, $date)
	{
	
	$query = $this->db->query('SELECT
week(ff_orderlines.created,3) as weekno,
ff_orderhead.status1,
count(*) as Antal,
sum(ff_orderlines.amount) as Total 
FROM 
	(ff_orderlines, 
	ff_orderhead, 
	ff_items, 
	ff_producttypes, 
	ff_pickupdates
	)
WHERE 
ff_orderlines.orderno = ff_orderhead.orderno 
AND ((ff_orderhead.status1 = "kontant") or (ff_orderhead.status1 = "nets")or (ff_orderhead.status1 = "mobilepay"))
AND ff_orderlines.item = ff_items.id
AND ff_items.producttype_id = ff_producttypes.id 	
AND ff_orderlines.iteminfo = ff_pickupdates.uid
AND ff_pickupdates.division = ff_items.division
and ff_pickupdates.division = ' . (int)$division . ' 
group by weekno,ff_orderhead.status1
having weekno = week("' . $date . '",3)');
		$weektotals = $query->result_array();
		
		return $weektotals;
	}

	
	private function _divisionname($division)
	{
			$this->db->select('name');
			$this->db->from('divisions');
			$this->db->where('uid', (int)$division); 
			$query = $this->db->get();
			$row = $query->row();
			return $row->name;
	}
	
	
	
} // class Rapportering 

/* End of file rapportering.php */
/* Location: ./application/controllers/rapportering.php */
