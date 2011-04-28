<?php
if(!class_exists('Site'))die('Restricted Access');
if(Auth::ACCESS_GRANTED != $this->requestPageAuth('Open'))return;

//check that this form has been submitted
if (isset($_POST["username"]) && isset($_POST["password"])) {
	//try logging the user in
	if (!Site::getAuthenticationManager()->login($_POST["username"], $_POST["password"]))
		$loginFailure = true;
	else {
		if (!empty($_REQUEST['redirect']))
			gotoLocation($_REQUEST['redirect']);
		else
			Common::gotoStartPage();

		exit();
	}
}
else
	//destroy the session by logging out
	Site::getAuthenticationManager()->logout();

function printMessage($message) {
	print "<tr>" .
				"	<td>&nbsp;</td>" .
				"	<td colspan=\"3\">" .
				"		<table width=\"100%\" border=\"0\" bgcolor=\"black\" cellspacing=\"0\" cellpadding=\"1\">" .
				"			<tr>" .
				"				<td>" .
				"					<table width=\"100%\" border=\"0\" bgcolor=\"yellow\">" .
				"						<tr><td class=\"login_error\">$message</td></tr>" .
				"					</table>" .
				"				</td>" .
				"			</tr>" .
				"		</table>" .
				"	</td>" .
				"</tr>";
}

$redirect = isset($_REQUEST["redirect"]) ? $_REQUEST["redirect"] : "";

PageElements::setHead("<title>".Config::getMainTitle()." | " .JText::_('LOGIN')."</title>");
PageElements::setBodyOnLoad("document.loginForm.username.focus();");
?>

<div id="inputArea">
<form action="<?php echo Config::getRelativeRoot();?>/login" method="POST" name="loginForm" style="margin: 0px;">
<input type="hidden" name="redirect" value="<?php echo $redirect; ?>" />

<table border="0" cellspacing="0" cellpadding="0" align="center">
	<tr>
<?php if(gbl::getSiteClosed()) { 
		?>
		<td style="padding-top: 40;">
			<font face="Verdana, Arial, Helvetica, sans-serif">
			
			<p align="center"><font color="red"><strong>The Site is temporarily closed.</strong></font></p>
			<p align="center">The timesheet system is temporarily closed for maintenance.</p>
			<p align="center">If you are not an Administrator, you will not be allowed to login; please check back later.</p>
			</font>
<?php 	
	} 
  else {
    echo "<td style=\"padding-top: 100;\">";    
  } 
?>

			<h1> <?php echo JText::_('TIMESHEET_LOGIN') ?></h1>
			<tr>
				<td class="label"><?php echo JText::_('USERNAME') ?>:<br /><input type="text" name="username" size="25" maxlength="25" /></td>
				<td class="label"><?php echo JText::_('PASSWORD') ?>:<br /><input type="password" name="password" size="25" maxlength="25" /></td>
				<td class="label"><br /><input type="submit" name="Login" value="<?php echo JText::_('LOGIN') ?>" /></td>
			</tr>
			<?php	if (isset($loginFailure))
						printMessage(Site::getAuthenticationManager()->getErrorMessage());
					else if (isset($_REQUEST["clearanceRequired"]))
						printMessage("$_REQUEST[clearanceRequired] clearance is required for the page you have tried to access.");
			?>
		</td>
	</tr>
</table>

</form>
</div>
