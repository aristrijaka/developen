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
 
}
