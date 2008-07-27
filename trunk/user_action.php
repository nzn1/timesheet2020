<?php
// $Header: /cvsroot/tsheet/timesheet.php/user_action.php,v 1.7 2005/04/17 12:19:31 vexil Exp $
// Authenticate
require("class.AuthenticationManager.php");
require("class.CommandMenu.php");
if (!$authenticationManager->isLoggedIn() || !$authenticationManager->hasClearance(CLEARANCE_ADMINISTRATOR)) {
	Header("Location: login.php?redirect=$_SERVER[PHP_SELF]&clearanceRequired=Administrator");
	exit;
}

// Connect to database.
$dbh = dbConnect();

//load local vars from superglobals
$action = $_REQUEST["action"];
$uid = $_REQUEST["uid"];
$first_name = $_REQUEST["first_name"];
$last_name = $_REQUEST["last_name"];
$username = $_REQUEST["username"];
$email_address = $_REQUEST["email_address"];
$password = $_REQUEST["password"];
$isAdministrator = isset($_REQUEST["isAdministrator"]) ? $_REQUEST["isAdministrator"]: "false";
$isManager = isset($_REQUEST["isManager"]) ? $_REQUEST["isManager"]: "false";

//print "<p>isAdministrator='$isAdministrator'</p>";

include("table_names.inc");

if ($action == "delete") {
	dbquery("DELETE FROM $USER_TABLE WHERE uid='$uid'");
	dbquery("DELETE FROM $ASSIGNMENTS_TABLE WHERE username='$username'");
	dbquery("DELETE FROM $TASK_ASSIGNMENTS_TABLE WHERE username='$username'");
}
else if ($action == "addupdate") {
	//set the level
	if ($isAdministrator == "true")
		$level = 11;
	else if ($isManager == "true")
		$level = 6;
	else
		$level = 1;

	//check whether the user exists, and get his encrypted password.
	list($qh,$num) = dbQuery("SELECT username, password FROM $USER_TABLE WHERE uid='$uid'");

	//if there is a match
	if ($data = dbResult($qh)) {

		//has the username changed
		if ($data["username"] != $username) {
			//update the assignments
			dbQuery("UPDATE $ASSIGNMENTS_TABLE SET username='$username' WHERE username='$data[username]'");
			dbQuery("UPDATE $TASK_ASSIGNMENTS_TABLE SET username='$username' WHERE username='$data[username]'");
			dbQuery("UPDATE $PROJECT_TABLE SET proj_leader='$username' WHERE proj_leader='$data[username]'");
			dbQuery("UPDATE $TIMES_TABLE SET uid='$username' WHERE uid='$data[username]'");
		}

		if ($data["password"] == $password) {
			//then we are not updating the password
			dbquery("UPDATE $USER_TABLE SET first_name='$first_name', last_name='$last_name', ".
								"username='$username', " .
								"email_address='$email_address', ".
								"level='$level' ".
								"WHERE uid='$uid'");
		}
		else {
			//set the password as well
			dbquery("UPDATE $USER_TABLE SET first_name='$first_name', last_name='$last_name', ".
								"username='$username', " .
								"email_address='$email_address', ".
								"level='$level', ".
								"password=$DATABASE_PASSWORD_FUNCTION('$password') " .
								"WHERE uid='$uid'");
		}
	}
	else {
		// a new user
		dbquery("INSERT INTO $USER_TABLE (username, level, password, first_name, ".
							"last_name, email_address, time_stamp, status) " .
						"VALUES ('$username',$level,$DATABASE_PASSWORD_FUNCTION('$password'),'$first_name',".
							"'$last_name','$email_address',0,'OUT')");
		dbquery("INSERT INTO $ASSIGNMENTS_TABLE VALUES (1,'$username', 1)"); // add default project.
		dbquery("INSERT INTO $TASK_ASSIGNMENTS_TABLE VALUES (1,'$username', 1)"); // add default task
	}
}

//redirect back to the user management page
Header("Location: user_maint.php");
?>
