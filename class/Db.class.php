<?php
/**
*@author 	: JIHAD SINNAOUR | mordieteq
*@package	: Database connection
*@version 	: 0.1 alpha
**/
require('Log.class.php');

class Db extends Log {
	# @Array , database settings
	private $_settings = array(
		'host'	=> null,
		'dbname'=> null,
		'user'	=> null,
		'pass'	=> null
		);
	
	# @Object , Database port
	private $_port;
	
	# @object, The PDO object
	private $_pdo;
    
    # @object, PDO statement object
	private $_query;

	# @bool ,  Connected to the database
	private $_status = false;

	# @object, Object for logging exceptions	
	private $_log;

	# @array, The parameters of the SQL query
	private $_parameters;
	/**
	*   Default Constructor 
	*
	*	1. Instantiate Log class.
	*	2. Decode password.
	*	3. Connect to database.
	*	4. Creates the parameter array.
	*/
	public function __construct() {
			$this->_log = new Log();
			$this->Decode();
			$this->Connect();	
			$this->_parameters = array();
	}
	private function Decode(){
		$this->_settings = parse_ini_file("/../config/settings.ini.php");
		$this->_settings['pass'] = base64_decode(base64_decode($this->_settings['pass']));
		return $this->_settings['pass'];
	}
	// Connect function with PDO API
	private function Connect() {
			$dsn = 'mysql:dbname='.$this->_settings["dbname"].';host='.$this->_settings["host"].'';
			try 
			{
				# Read settings from INI file, set UTF8
				$this->_pdo = new PDO($dsn, $this->_settings["user"], $this->_settings["pass"], array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
				
				# We can now log any exceptions on Fatal error. 
				$this->_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				
				# Disable emulation of prepared statements, use REAL prepared statements instead.
				$this->_pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
				
				# Connection succeeded, set the boolean to true.
				$this->_status = true;
			}
			catch (PDOException $e) 
			{
				# Write into log
				echo $this->ExceptionLog($e->getMessage());
				die();
			}
		}
	 	public function CloseConnection() {
	 		# Set the PDO object to null to close the connection
	 		$this->_pdo = null;
	 		die();
	 	}
    /**
	*	Every method which needs to execute a SQL query uses this method.
	*	
	*	1. If not connected, connect to the database.
	*	2. Prepare Query.
	*	3. Parameterize Query.
	*	4. Execute Query.	
	*	5. On exception : Write Exception into the log + SQL query.
	*	6. Reset the Parameters.
	*/	
		private function Init($query,$parameters = "") {

		# Connect to database
		if(!$this->_status) { $this->Connect(); }
		try {
				# Prepare query
				$this->_query = $this->_pdo->prepare($query);
				
				# Add parameters to the parameter array	
				$this->bindMore($parameters);

				# Bind parameters
				if(!empty($this->_parameters)) {
					foreach($this->_parameters as $param)
					{
						$parameters = explode("\x7F",$param);
						$this->_query->bindParam($parameters[0],$parameters[1]);
					}		
				}

				# Execute SQL 
				$this->succes 	= $this->_query->execute();		
			}
			catch(PDOException $e)
			{
					# Write into log and display Exception
					echo $this->ExceptionLog($e->getMessage(), $query );
					die();
			}

			# Reset the parameters
			$this->_parameters = array();
		}
    /**
	*	@void 
	*
	*	Add the parameter to the parameter array
	*	@param string $para  
	*	@param string $value 
	*/	
		public function bind($para, $value) {

			$this->_parameters[sizeof($this->_parameters)] = ":" . $para . "\x7F" . utf8_encode($value);
		}
    /**
	*	@void
	*	
	*	Add more parameters to the parameter array
	*	@param array $parray
	*/	
		public function bindMore($parray) {
			if(empty($this->_parameters) && is_array($parray)) {
				$columns = array_keys($parray);
				foreach($columns as $i => &$column)	{
					$this->bind($column, $parray[$column]);
				}
			}
		}
    /**
	*   If the SQL query  contains a SELECT or SHOW statement it returns an array containing all of the result set row
	*	If the SQL statement is a DELETE, INSERT, or UPDATE statement it returns the number of affected rows
	*
	*   @param  string $query
	*	@param  array  $params
	*	@param  int    $fetchmode
	*	@return mixed
	*/			
		public function query($query,$params = null, $fetchmode = PDO::FETCH_ASSOC) {

			$query = trim($query);

			$this->Init($query,$params);

			$rawStatement = explode(" ", $query);
			
			# Which SQL statement is used 
			$statement = strtolower($rawStatement[0]);
			
			if ($statement === 'select' || $statement === 'show') {
				return $this->_query->fetchAll($fetchmode);
			}
			elseif ( $statement === 'insert' ||  $statement === 'update' || $statement === 'delete' ) {
				return $this->_query->rowCount();	
			}	
			else {
				return NULL;
			}
		}
    /**
    *  Returns the last inserted id.
    *  @return string
    */	
		public function lastInsertId() {
			return $this->_pdo->lastInsertId();
		}		
    /**
	*	Returns an array which represents a column from the result set 
	*
	*	@param  string $query
	*	@param  array  $params
	*	@return array
	*/	
		public function column($query,$params = null)
		{
			$this->Init($query,$params);
			$Columns = $this->_query->fetchAll(PDO::FETCH_NUM);		
			
			$column = null;

			foreach($Columns as $cells) {
				$column[] = $cells[0];
			}

			return $column;
			
		}
    /**
	*	Returns an array which represents a row from the result set 
	*
	*	@param  string $query
	*	@param  array  $params
	*   @param  int    $fetchmode
	*	@return array
	*/	
		public function row($query,$params = null,$fetchmode = PDO::FETCH_ASSOC)
		{				
			$this->Init($query,$params);
			return $this->_query->fetch($fetchmode);			
		}
       /**
	*	Returns the value of one single field/column
	*
	*	@param  string $query
	*	@param  array  $params
	*	@return string
	*/	
		public function single($query,$params = null)
		{
			$this->Init($query,$params);
			return $this->_query->fetchColumn();
		}
    /**	
	* Writes the log and returns the exception
	*
	* @param  string $message
	* @param  string $sql
	* @return string
	*/
	private function ExceptionLog($message , $sql = "")
	{
		$exception  = 'Unhandled Exception. <br />';
		$exception .= $message;
		$exception .= "<br /> You can find the error back in the log.";

		if(!empty($sql)) {
			# Add the Raw SQL to the Log
			$message .= "\r\nRaw SQL : "  . $sql;
		}
			# Write into log
			$this->_log->write($message);

		return $exception;
	}
	/**
	* Returns database port
	*
	* @param port
	* @return string
	*/
	public function setPort($port){
		$this->_port = $port;
		return $this->_port;
	}

}

?>