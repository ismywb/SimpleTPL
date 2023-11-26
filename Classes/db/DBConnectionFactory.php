<?php

function loadDBConfig() {
/**
	if (!file_exists(DB_FILE)) 
		throw new Exception('DB Config File does not exist: ' . DB_FILE);
	$lines = file(DB_FILE);
	$numArray = preg_split("/,/",trim($lines[0]), -1);
**/
	$dbInfo = array();
	//mysql_connect('localhost','root','Jd9z1jD--')
	$dbInfo['host'] = 'localhost';//$numArray[0];
	$dbInfo['user'] = 'app';//$numArray[1];
	$dbInfo['pass'] = 'Jerald12';//$numArray[2];
	$dbInfo['schema'] = 'db'; //$numArray[3];
	return $dbInfo;
}

class DBConnectionFactory {
    private static $factory;
    public static function getFactory() {
        if (!self::$factory)
            self::$factory = new DBConnectionFactory();
        return self::$factory;
    }

    private $dbs = array();

    public function getConnection($host = false, $schema = false, $user = false, $pass = false) {
    	$mysqlInfo = loadDBConfig();
	if($host === false) { $host = $mysqlInfo['host']; }
	if($schema === false) { $schema = $mysqlInfo['schema']; }
	if($user === false) { $user = $mysqlInfo['user']; }
	if($pass === false) { $pass = $mysqlInfo['pass']; }
	$hash = md5($host.$schema.$user.$pass);
        if (!isset($this->dbs[$hash]))
            $this->dbs[$hash] = new PDO("mysql:host=$host;dbname=$schema", $user, $pass);
        return $this->dbs[$hash];
    }	

}

?>
