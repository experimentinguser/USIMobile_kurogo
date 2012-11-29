<?php
error_reporting(E_ERROR);
Kurogo::includePackage('db');

class UsidbAPIModule extends APIModule {

	protected $id='usidb';
	protected $vmin = 1;
	protected $vmax = 1;

	// special factory method for core
	public static function factory($id='usidb', $command='', $args=array()) {
		$module = new UsidbAPIModule();
		$module->init($command, $args);
		return $module;
	}

	public function initializeForCommand() {  
	
		switch ($this->command) {
			case 'hello':
				$response = array(
					'salute'=>'hi there'
				);
				$this->setResponse($response);
				$this->setResponseVersion(1);
				break;
			case 'get_courses':
				$courses = $this->getCourses();
				$this->setResponse($courses);
				$this->setResponseVersion(1);
				break;
			case 'get_people':
				$people = $this->getPeople();
				$this->setResponse($people);
				$this->setResponseVersion(1);
				break;
			default:
				$this->invalidCommand();
				break;
		}
	}

	private function query($sql) {
		$DB_HOST = $this->getModuleVar('DB_HOST', 'database');
		$DB_USER = $this->getModuleVar('DB_USER', 'database');
		$DB_PASS = $this->getModuleVar('DB_PASS', 'database');
		$DB_DBNAME = $this->getModuleVar('DB_DBNAME', 'database');
		$connection = mssql_connect($DB_HOST, $DB_USER, $DB_PASS);
		if($connection != false) {
			
			if(mssql_select_db($DB_DBNAME, $connection)) {
				$query_result = mssql_query($sql);
				$row = mssql_fetch_array($query_result);
				return $row;
			} else {
				$this->raiseError(1);
			}
		
		} else {
			$this->raiseError(0);
		}
	}

	private function getCourses(){
		$sql = "SELECT * FROM Corsi";
		$result = $this->query($sql);
		return $result;
	}

	private function getPeople(){
		$sql = "SELECT * FROM People";
		$result = $this->query($sql);
		return $result;
	}

	public function raiseError($code) {

		$error = new KurogoError();
		$error->code = $code;

		switch ($code) {
			case 0:
				$error->title = 'Connection attempt';
				$error->message = 'Connection to the USI DB failed.';
				break;
			default:
				$error->title = 'Database selection';
				$error->message = 'Selecting the database failed';
		}
		$this->throwError($error);
	}

}
