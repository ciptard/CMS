<?

function db_connect(){
	global $config;
	//Connect to mysql server
	$DB = mysql_connect($config['db_server'], $config['db_username'], $config['db_password']);
	//Select appropraite database
	mysql_select_db($config['db_database'], $DB);
	
	//display error message if unable to connect
	if (!$DB){
	  die('Could not connect: ' . mysql_error());
	}
}

function db_query($sql){
	return mysql_query($sql,$DB);
}

function db_disconnect(){
	mysql_close($DB);
}

?>