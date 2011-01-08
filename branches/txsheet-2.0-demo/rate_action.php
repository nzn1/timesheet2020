<?php
die('NOT CONVERTED TO OO YET');
if(!class_exists('Site'))die('Restricted Access');
// Authenticate
require("class.AuthenticationManager.php");
require("class.CommandMenu.php");
if (!$authenticationManager->isLoggedIn() || !$authenticationManager->hasClearance(CLEARANCE_ADMINISTRATOR)) {
	gotoLocation(Config::getRelativeRoot()."/login?redirect=".urlencode($_SERVER['REQUEST_URI'])."&amp;clearanceRequired=Administrator");
	exit;
}

// Connect to database.
$dbh = dbConnect();

//load local vars from superglobals
$action = $_REQUEST["action"];
$rate_id = $_REQUEST["rate_id"];
$bill_rate = $_REQUEST["bill_rate"];

//print "<p>isAdministrator='$isAdministrator'</p>";

include("table_names.inc");
	
if ($action == "addupdate") {
	if ($rate_id == 1) {
	    //redirect back to the rate management page
	    gotoLocation(Config::getRelativeRoot()."/rate_maint");
	    exit(0);
	}
	//check whether the rate exists
	list($qh,$num) = dbQuery("select rate_id, bill_rate from $RATE_TABLE where rate_id='$rate_id'");

	//if there is a match
	if ($data = dbResult($qh)) {

		//has the bill_rate changed
		if ($data["bill_rate"] != $bill_rate) {
			//update the assignments
			dbQuery("update $RATE_TABLE set bill_rate='$bill_rate' where rate_id='$data[rate_id]'");
		}
	} else {
		// a new rate
		dbquery("insert into $RATE_TABLE (bill_rate) values ('$bill_rate')");
	}
} 

//redirect back to the rate management page
gotoLocation(Config::getRelativeRoot()."/rate_maint");

// vim:ai:ts=4:sw=4
?>
