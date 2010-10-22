<?php
// $Header: /cvsroot/tsheet/timesheet.php/submit_action.php,v 1.7 2005/05/23 07:32:00 vexil Exp $
// Authenticate

require("class.AuthenticationManager.php");
require("class.CommandMenu.php");
require_once("debuglog.php");
$debug = new logfile();
	
if (!$authenticationManager->isLoggedIn() || !$authenticationManager->hasAccess('aclTasks')) {
	Header("Location: login.php?redirect=$_SERVER[PHP_SELF]&clearanceRequired=" . get_acl_level('aclTasks'));
	exit;
}

// Connect to database.
$dbh = dbConnect();
$contextUser = strtolower($_SESSION['contextUser']);

// submit.php?uid=peter&orderby=project&client_id=0&mode=monthly&year=2010&month=8&day=1
//load local vars from superglobals
$action = $_REQUEST["submit"];

	$name = $_REQUEST["name"];
	$orderby = $_REQUEST["orderby"];
	$client_id = $_REQUEST["client_id"];
	$mode = $_REQUEST["mode"];
	$year = $_REQUEST["year"];
	$month = $_REQUEST["month"];
	$day = $_REQUEST["day"];
	$uid = $_REQUEST["uid"];

	if (isset($action)) {

	if (isset($_REQUEST['approve'])) {
		//var_dump ($_REQUEST['sub']);
		$transids = "";
		foreach ($_REQUEST['approve'] as $transId) {
			if($transids == "")
				$transids = $transId;
			else 
				$transids = $transids . ", " . $transId;
		}
		list($qh, $num) = dbQuery("UPDATE $TIMES_TABLE SET status = \"Approved\"" .
				" WHERE trans_num IN ( $transids )");
		//$debug->write("update query transids = \" $transids\" qh = \"$qh\"  num=\"".$num. "\"\n");		
		}
	
	if (isset($_REQUEST['reject'])) {
		//var_dump ($_REQUEST['sub']);
		$transids = "";
		foreach ($_REQUEST['reject'] as $transId) {
			if($transids == "")
				$transids = $transId;
			else 
				$transids = $transids . ", " . $transId;
		}
		list($qh, $num) = dbQuery("UPDATE $TIMES_TABLE SET status = \"Open\"" .
				" WHERE trans_num IN ( $transids )");
		//$debug->write("update query transids = \" $transids\" qh = \"$qh\"  num=\"".$num. "\"\n");		
	}
}
	
	// we're done so redirect to the submission page
	Header("Location: supervisor.php?uid=$uid&orderby=$orderby&client_id=$client_id&mode=$mode&year=$year&month=$month&day=$day");

?>




