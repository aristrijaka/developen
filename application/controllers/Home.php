<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home extends CI_Controller {

	public function __construct()
	{
		parent::__construct(); 

		$this->load->library('grocery_CRUD');
	}

	public function _example_output($output = null)
	{ 
		$query = $this->db->query('SELECT id FROM tagihan');
		$jumtag = $query->num_rows();
		$output->jumtag= $jumtag;
		$query = $this->db->query('SELECT id FROM tagihan where status = 1 ');
		$jumbay = $query->num_rows();
		$output->jumbay= $jumbay;
		$query = $this->db->query('SELECT id FROM tagihan where status = "0" ');
		$jumbbay = $query->num_rows();
		$output->jumbbay= $jumbbay;
		$output->base_url= base_url();

		$this->load->view('dashboard.php',(array)$output);
	}

	public function offices()
	{
		$output = $this->grocery_crud->render();

		$this->_example_output($output);
	}

	public function index()
	{
		$this->_example_output((object)array('output' => '' , 'js_files' => array() , 'css_files' => array()));
	}

	public function list_tagihan()
	{
		$crud = new grocery_CRUD();

		$crud->set_theme('datatables');
		$crud->set_table('tagihan'); 
		$crud->set_subject('Tagihan');
		$crud->required_fields('lastName');
		$crud->callback_column('ammount',array($this,'valueToIdr'));
		$crud->set_field_upload('file_url','assets/uploads/files');

		$output = $crud->render();

		$this->_example_output($output);
	}
	public function list_rekening()
	{
		$crud = new grocery_CRUD();

		$crud->set_theme('datatables');
		$crud->set_table('rekening'); 
		$crud->set_subject('rekening');
		$crud->required_fields('kode_rek','norek');  
		$output = $crud->render();

		$this->_example_output($output);
	}
	public function valueToIdr($value='')
	{
		return ttk($value);
	}
	public function employees_management2()
	{
		$crud = new grocery_CRUD();

		$crud->set_theme('datatables');
		$crud->set_table('employees');
		$crud->set_relation('officeCode','offices','city');
		$crud->display_as('officeCode','Office City');
		$crud->set_subject('Employee');

		$crud->required_fields('lastName');

		$crud->set_field_upload('file_url','assets/uploads/files');

		$crud->set_crud_url_path(site_url(strtolower(__CLASS__."/".__FUNCTION__)),site_url(strtolower(__CLASS__."/multigrids")));

		$output = $crud->render();

		if($crud->getState() != 'list') {
			$this->_example_output($output);
		} else {
			return $output;
		}
	}
	public function import_tagihan($output = null)
	{
		
		$data = array('base_url' => base_url() );
		$this->load->view('upload_tag.php',$data); 

	}
	public function upload(){
		$this->load->library(array('PHPExcel','PHPExcel/IOFactory'));
		$fileName = $this->input->post('file', TRUE);
		$config['upload_path'] = './uploads/'; 
		$config['file_name'] = $fileName;
		$config['allowed_types'] = 'xls|xlsx|csv|ods|ots';
		$config['max_size'] = 10000;

		$this->load->library('upload', $config);
		$this->upload->initialize($config); 

		if (!$this->upload->do_upload('file')) {
			$error = array('error' => $this->upload->display_errors());
			$this->session->set_flashdata('msg','Ada kesalah dalam upload'); 
			redirect('Welcome'); 
		} else {
			$media = $this->upload->data();
			$inputFileName = 'uploads/'.$media['file_name'];

			try { 
				$inputFileType = IOFactory::identify($inputFileName);
				$objReader = IOFactory::createReader($inputFileType);
				$objPHPExcel = $objReader->load($inputFileName);
			} catch(Exception $e) {
				die('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());
			}

			$sheet = $objPHPExcel->getSheet(0);
			$highestRow = $sheet->getHighestRow();
			$highestColumn = $sheet->getHighestColumn();

			for ($row = 2; $row <= $highestRow; $row++){  
				$rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,
					NULL,
					TRUE,
					FALSE);
				$data = array(
					"npm"=> $rowData[0][1],
					"nama"=> $rowData[0][2],
					"ammount"=> $rowData[0][3],
					"keterangan"=> $rowData[0][4],
					"kode_rekening"=> $rowData[0][5],
					"status"=>$rowData[0][6],
					); 
    //$this->db->insert("tbimport",$data);
				$this->db->insert('tagihan', $data); 

			} 
			$this->session->set_flashdata('msg','Berhasil upload ...!!'); 
			redirect('home/list_tagihan');
		}  
	}

}
