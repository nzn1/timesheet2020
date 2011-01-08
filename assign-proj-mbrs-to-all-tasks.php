<?php

if(!class_exists('Site'))die('Restricted Access');
                              
// Authenticate
if (!Site::getAuthenticationManager()->isLoggedIn() || !Site::getAuthenticationManager()->hasAccess('aclReports')) {
	gotoLocation(Config::getRelativeRoot()."/login?redirect=".urlencode($_SERVER['REQUEST_URI'])."&amp;clearanceRequired=" . Common::get_acl_level('aclReports'));
	exit;
}

$assignTasks = isset($_REQUEST["assignTasks"]) ? $_REQUEST["assignTasks"]: false;

function do_query($sql) {
	$result = mysql_query($sql);
	if(!$result) {
		print "Query failed: \"$sql\"\n";
		print get_db_error(mysql_error())."\n";
		return false;
	}
	return $result;
}

?>

<html>
<head>
	<title>Assign all Tasks</title>

<script type="text/javascript">

	function delete_task(projectId, taskId) {
		if (confirm('Deleting a task which has been used in the past will make those timesheet ' +
				'entries invalid, and may cause errors. This action is not recommended. ' +
				'Are you sure you want to delete this task?'))
			location.href = '<?php echo Config::getRelativeRoot();?>/task_action?proj_id=' + projectId + '&task_id=' + taskId + '&action=delete';
	}

</script>
</head>


<form name="changeForm" action="<?php echo $_SERVER["PHP_SELF"]; ?>" style="margin-bottom: 0px;">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td width="100%" class="face_padding_cell">


			<table width="100%" border="0">
				<tr>
					<td align="center" nowrap>
						<h2>Assign all Tasks to all Project Members</h2>
					</td>
				</tr>
			</table>



			<table width="100%" align="center" border="0" cellpadding="0" cellspacing="0" class="outer_table">
				<tr>
					<td width="40">&nbsp;</td>
					<td>
<?php if($assignTasks!='checked') { ?>
<h2><font color="red"><b>Attention!</b></font> &nbsp;<font color="red">Read this carefully</font><br /></h2>
<h3>Clicking on the checkbox below and submitting this form will:
<ol><li> clear the entire task assignment database table</li>
<li> iterate through the users and project tables</li>
<li> and assign each user to all the tasks for every project of which they are a member</li>
</ol>
&nbsp;&nbsp;&nbsp;&nbsp;<a href="<?php echo Config::getRelativeRoot(); ?>/explain-assign-all-tasks">Click here if you don't understand</a><br /><br />
If this is what you want to do, check this box <input type="checkbox" name="assignTasks" value="checked" <?php if($assignTasks) echo " checked=\"checked\"" ?> />  and hit submit, or return. <br />
<img src="<?php echo Config::getRelativeRoot(); ?>/images/spacer.gif" alt="" width="50" height="1" /><input type="submit" value="Submit" /></h3>
<?php } else { 
	$task_array=array();

	$sql = "SELECT task_id, proj_id FROM $TASK_TABLE order by task_id";
	$result = do_query($sql);
	if($result) {
		while($data = mysql_fetch_array($result)) {
			$task_id=$data["task_id"];
			$proj_id=$data["proj_id"];

			$task_array[$proj_id][$task_id]=1;
		}
	}

	$sql = "DELETE from $TASK_ASSIGNMENTS_TABLE";
	$rslt = do_query($sql);
	print "All task assignments removed<br />\n";

	$lastuser="";
	$userprojcnt=0;
	$usertaskcnt=0;

	$sql = "SELECT username, proj_id FROM $ASSIGNMENTS_TABLE order by username";
	$result = do_query($sql);
	if($result) {
		while($data = mysql_fetch_array($result)) {
			$proj_id=$data["proj_id"];
			$username=$data["username"];

			if($lastuser=='') $lastuser=$username;
			if($lastuser != $username) {
				print "$lastuser assigned to $usertaskcnt tasks in $userprojcnt projects<br />\n";
				$lastuser=$username;
				$userprojcnt=0;
				$usertaskcnt=0;
			}
			$userprojcnt++;

			foreach($task_array[$proj_id] as $task_id => $value) {	
				$sql = "INSERT into $TASK_ASSIGNMENTS_TABLE VALUES ($task_id,'$username',$proj_id)";
				$rslt = do_query($sql);
				$usertaskcnt++;
			}
		}
		print "$lastuser assigned to $usertaskcnt tasks in $userprojcnt projects<br />\n";
	}
}

?>
					</td>
				</tr>
			</table>

		</td>
	</tr>
</table>

</form>