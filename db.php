<?php
class db
{
	private $hostname;
	private $username;
	private $password;
	private $database;
	private $dblink;
	private $debug;
	private $port = 3306;
    private $suppress = false; // query result

	function __construct($db = array(), $debug = false)
	{
        if(empty($db)){
            require "config.php";
            $db = $db['default'];
        }
		$this->hostname = $db['hostname'];
		$this->username = $db['username'];
		$this->password = $db['password'];
		$this->database = $db['database'];
		$this->dbdriver = $db['dbdriver'];
		$this->debug = $debug;

		if ($debug) {
            echo $this->hostname." ".$this->port." ".$this->username." ".$this->password."\n";
        }

        $this->connect();
	}

	private function connect()
	{
		if (!is_resource($this->dblink)) {
            $this->dblink = @mysql_connect($this->hostname.":".$this->port, $this->username, $this->password);
            mysql_query("set names 'utf8'");
        }


        if ($this->database) {
            @mysql_select_db($this->database, $this->dblink);
        }
	}

	// check connection
    private function checkConnect()
    {
        if (!$this->dblink) {
            return false;
        } else {
            return true;
        }
    }

    private function close()
    {
        if (is_resource($this->dblink)) {
            mysql_close($this->dblink);
        }
    }

     // query
    public function query($sql)
    {
        if ($this->debug && $_SERVER['REMOTE_ADDR'] == $this->hostname) {
            echo $sql.'<br />';
        }
        if (!$this->checkConnect()) {
            if (!$this->suppress) {
                echo "Could not connect to MySQL<br />";
            }
            return false;
        }
        $this->dbresult = mysql_query($sql, $this->dblink);
        if ($this->dbresult) {
            return true;
        } else {
            if (!$this->suppress) {
                echo "MySQL error in query: ".$sql."<br />";
                echo "MySQL says: ".mysql_errno($this->dblink)." ".mysql_error($this->dblink)."<br />";
            }
            return false;
        }
    }

    // number of rows
    public function numRows()
    {
        return mysql_num_rows($this->dbresult);
    }

    // get array
    final function getRow()
    {
        $row = @mysql_fetch_assoc($this->dbresult);
        if ($row) {
            return $row;
        } else {
            return array();
        }
    }

    final function getArray()
    {
    	$arr = array();
        while($dbres = mysql_fetch_assoc($this->dbresult))
		{			
            $arr[] = $dbres;
		}
		return $arr;
    }

    // escape a string
    public function escapeString($string)
    {
        return mysql_real_escape_string($string);
    }

    public function escStr($string)
    {
        return mysql_real_escape_string($string);
    }

    // get insert id
    public function getInsID()
    {
        return mysql_insert_id($this->dblink);
    }

    public function getAffRows()
    {
        return mysql_affected_rows($this->dblink);
    }


}