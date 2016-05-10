<?php
/*
 * Session Management for PHP3
 *
 * Copyright (c) 1998-2000 NetUSE AG
 *                    Boris Erdmann, Kristian Koehntopp
 *
 * $Id: db_mysql.inc,v 1.8 2001/10/12 16:16:16 layne_weathers Exp $
 *
 * Modified by cnet on 2002/08/02 
 * Removed vars:$Seq_Table 
 * Removed functions:next_id() metadata()
 * Changed fuctions:db_sql(),query()
 * Added vars: Explains
 * Added function:query_first($string),insert_id();
 */ 
class DB_Sql {
  
  /* public:
  
  
  connection parameters */
  var $Host;
  var $Database;
  var $User;
  var $Password;

  /* public: configuration parameters */
  var $Auto_Free     = 1;     ## Set to 1 for automatic mysql_free_result()
  var $Debug         = 0;     ## Set to 1 for debugging messages.
  var $Explains      = 0;     ## Set to 1 for explaining Query strings messages;
  var $Halt_On_Error = "yes"; ## "yes" (halt with message), "no" (ignore errors quietly), "report" (ignore errror, but spit a warning)
  //var $Seq_Table     = "db_sequence"; ##used in next_id()

  /* public: result array and current row number */
  var $Record   = array();
  var $Row;

  /* public: current error number and error text */
  var $Errno    = 0;
  var $Error    = "";

  /* public: this is an api revision, not a CVS revision. */
  var $type     = "mysql";
  var $revision = "1.2";

  /* private: link and query handles */
  var $Link_ID  = 0;
  var $Query_ID = 0;
  


  /* public: constructor */
  function DB_Sql($query = "") {	
  	  global $sys_vars;// should be set in config.php
  	  $this->Host = $sys_vars['dbhost'];
  	  $this->Database = $sys_vars['dbname'];
  	  $this->User = $sys_vars['dbuser'];
  	  $this->Password = $sys_vars['dbpwd'];
      $this->query($query);
  }

  /* public: some trivial reporting */
  function link_id() {
    return $this->Link_ID;
  }

  function Query_ID() {
    return $this->Query_ID;
  }

  /* public: connection management */
  function connect($Database = "", $Host = "", $User = "", $Password = "") {
    /* Handle defaults */
    if ("" == $Database)
      $Database = $this->Database;
    if ("" == $Host)
      $Host     = $this->Host;
    if ("" == $User)
      $User     = $this->User;
    if ("" == $Password)
      $Password = $this->Password;
	  
   
      
    /* establish connection, select database */
    if ( 0 == $this->Link_ID ) {
    
      $this->Link_ID=mysql_pconnect($Host, $User, $Password);
      if (!$this->Link_ID) {
        $this->halt("pconnect($Host, $User, \$Password) failed.");
        return 0;
      }

      if (!@mysql_select_db($Database,$this->Link_ID)) {
        $this->halt("cannot use database ".$Database);
        return 0;
      }
    }
    
    return $this->Link_ID;
  }

  /* public: discard the query result */
  function free() {
      @mysql_free_result($this->Query_ID);
      $this->Query_ID = 0;
  }
  /* public: return the last insert id */
  function insert_id() {
  	return mysql_insert_id($this->Link_ID);
  }
  /* public: perform a query */
  function query($Query_String) {
  	
  	global $query_count;
  	
    /* No empty queries, please, since PHP4 chokes on them. */
    if ($Query_String == "")
      /* The empty query string is passed on from the constructor,
       * when calling the class without a query, e.g. in situations
       * like these: '$db = new DB_Sql_Subclass;'
       */
      return 0;

    if (!$this->connect()) {
      return 0; /* we already complained in connect() about that. */
    };

    # New query, discard previous result.
    if ($this->Query_ID) {
      $this->free();
    }
######################### Experimetal Code START###########################################
    if ($this->Debug)
    {
    	global $pagestarttime,$querytime,$DebugMode; //should be defined in header file
      	if($DebugMode)
      		ob_end_clean();              
      	echo "<pre>";
      	printf("Debug: query = %s<br>\n", $Query_String);
      	$pageendtime = microtime();
      	$starttime = explode(" ",$pagestarttime);
      	$endtime = explode(" ",$pageendtime);

	    $beforetime = $endtime[0] - $starttime[0] + $endtime[1] - $starttime[1];
    	echo "Time before: $beforetime\n";
    }
    
    $query_count ++; // count the number of queries
######################### Experimetal Code END###########################################

    $this->Query_ID = @mysql_query($Query_String,$this->Link_ID);
    $this->Row   = 0;
    $this->Errno = mysql_errno();
    $this->Error = mysql_error();
    if (!$this->Query_ID) {
      $this->halt("Invalid SQL: ".$Query_String);
    }
######################### Experimetal Code START###########################################    
    if ($this->Debug)
    {
    	$pageendtime = microtime();
      	$starttime = explode(" ",$pagestarttime);
      	$endtime = explode(" ",$pageendtime);

      	$aftertime = $endtime[0] - $starttime[0] + $endtime[1] - $starttime[1];
      	$querytime += $aftertime - $beforetime; 
      	echo "Time after:  $aftertime\n";
        echo "</pre>\n";
     	if (($this->Explains) && substr(trim(strtoupper($Query_String)),0,6)=="SELECT")
      	{
      		$explain_id = mysql_query("EXPLAIN $Query_String",$this->Link_ID);
        	echo "
        <table width=100% border=1 cellpadding=2 cellspacing=1>
        <tr>
          <td><b>table</b></td>
          <td><b>type</b></td>
          <td><b>possible_keys</b></td>
          <td><b>key</b></td>
          <td><b>key_len</b></td>
          <td><b>ref</b></td>
          <td><b>rows</b></td>
          <td><b>Extra</b></td>
        </tr>\n";
        	while($array=mysql_fetch_array($explain_id))
        	{
          	echo "
          <tr>
            <td>$array[table]&nbsp;</td>
            <td>$array[type]&nbsp;</td>
            <td>$array[possible_keys]&nbsp;</td>
            <td>$array[key]&nbsp;</td>
            <td>$array[key_len]&nbsp;</td>
            <td>$array[ref]&nbsp;</td>
            <td>$array[rows]&nbsp;</td>
            <td>$array[Extra]&nbsp;</td>
          </tr>\n";
        	}
        	echo "</table>\n<BR><hr>\n";
    	}
    	if($DebugMode) ob_start();
    }
######################### Experimetal Code END###########################################

    # Will return nada if it fails. That's fine.
    return $this->Query_ID;
  }

  /* public: walk result set */
  function next_record() {
    if (!$this->Query_ID) {
      $this->halt("next_record called with no query pending.");
      return 0;
    }

    $this->Record = @mysql_fetch_array($this->Query_ID);
    $this->Row   += 1;
    $this->Errno  = mysql_errno();
    $this->Error  = mysql_error();

    $stat = is_array($this->Record);
    if (!$stat && $this->Auto_Free) {
      $this->free();
    }
    return $stat;
  }
  
  /* added by cnet:// does a query,return the first row free the result */
  function query_first($query_string)
  {    
    $this->query($query_string);
    if (!$this->Query_ID) {
      $this->halt("Invalid query id (".$this->Query_ID.") on this query: $query_string");
      return 0;
    }
    $this->Record = @mysql_fetch_array($this->Query_ID);
    $this->Errno  = mysql_errno();
    $this->Error  = mysql_error();
   	$this->free();
    return $this->Record;
  }

  /* public: position in result set */
  function seek($pos = 0) {
    $status = @mysql_data_seek($this->Query_ID, $pos);
    if ($status)
      $this->Row = $pos;
    else {
      $this->halt("seek($pos) failed: result has ".$this->num_rows()." rows.");

      /* half assed attempt to save the day, 
       * but do not consider this documented or even
       * desireable behaviour.
       */
      @mysql_data_seek($this->Query_ID, $this->num_rows());
      $this->Row = $this->num_rows();
      return 0;
    }
    return 1;
  }

  /* public: table locking */
  function lock($table, $mode = "write") {
    $query = "lock tables ";
    if (is_array($table)) {
      while (list($key,$value) = each($table)) {
        if (!is_int($key)) {
		  // texts key are "read", "read local", "write", "low priority write"
          $query .= "$value $key, ";
        } else {
          $query .= "$value $mode, ";
        }
      }
      $query = substr($query,0,-2);
    } else {
      $query .= "$table $mode";
    }
    $res = $this->query($query);
	if (!$res) {
      $this->halt("lock() failed.");
      return 0;
    }
    return $res;
  }
  
  function unlock() {
    $res = $this->query("unlock tables");
    if (!$res) {
      $this->halt("unlock() failed.");
    }
    return $res;
  }

  /* public: evaluate the result (size, width) */
  function affected_rows() {
    return @mysql_affected_rows($this->Link_ID);
  }

  function num_rows() {
    return @mysql_num_rows($this->Query_ID);
  }

  function num_fields() {
    return @mysql_num_fields($this->Query_ID);
  }

  /* public: shorthand notation */
  function nf() {
    return $this->num_rows();
  }

  function np() {
    print $this->num_rows();
  }

  function f($Name) {
    if (isset($this->Record[$Name])) {
      return $this->Record[$Name];
    }
  }

  function p($Name) {
    if (isset($this->Record[$Name])) {
      print $this->Record[$Name];
    }
  }
  /* private: error handling */
  function halt($msg) {
    $this->Error = @mysql_error($this->Link_ID);
    $this->Errno = @mysql_errno($this->Link_ID);
    if ($this->Halt_On_Error == "no")
      return;

    $this->haltmsg($msg);

    if ($this->Halt_On_Error != "report")
      die("Session halted.");
  }

  function haltmsg($msg) {
    printf("<b>Database error:</b> %s<br>\n", $msg);
    printf("<b>MySQL Error</b>: %s (%s)<br>\n",
      $this->Errno,
      $this->Error);
  }
}
?>