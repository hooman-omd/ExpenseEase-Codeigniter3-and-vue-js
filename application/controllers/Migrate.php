<?php
defined('BASEPATH') or exit('No direct script access allowed');
ob_start();

class Migrate extends CI_Controller
{
	//----------------------------------------------------------------------------
	public function index($show_time = 'true')
	{
		if ($show_time === 'true') {
			echo date('YmdHis');
			return;
		}

		$this->load->library('migration');

		if ($this->migration->current() === FALSE) {
			show_error($this->migration->error_string());
		} else {
			echo "\n\nmigration is done.\n";
		}
	}
	//-------------------------------------------------------------------------

	public function random_string($length = 8)
	{
		$length = intval($length);
		$random_string = generateRandomString($length);
		echo $random_string;
	}

	// ------------------------------------------------------------------------
}
