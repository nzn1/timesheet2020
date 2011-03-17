<?php
if(!class_exists('Site'))die(JText::_('RESTRICTED_ACCESS'));

if(Auth::ACCESS_GRANTED != $this->requestPageAuth('aclDaily'))return;

include('daily.class.php');
$dc = new DailyClass();

//define the command menu & we get these variables from $_REQUEST:
//  gbl::getMonth() gbl::getDay() gbl::getYear() gbl::getClientId() gbl::getProjId() gbl::getTaskId()

//check that project id is valid
if (gbl::getProjId() == 0)
	gbl::setTaskId(0);

$month = gbl::getMonth();
$day = gbl::getDay(); 
$year = gbl::getYear();
$startDayOfWeek = Common::getWeekStartDay();  //needed by NavCalendar
$todayDate = mktime(0, 0, 0,gbl::getMonth(), gbl::getDay(), gbl::getYear());

$tomorrowDate = strtotime(date("d M Y H:i:s",$todayDate) . " +1 days");

//get the timeformat
$CfgTimeFormat = Common::getTimeFormat();

$post="proj_id=".gbl::getProjId()."&amp;task_id=".gbl::getTaskId()."&amp;client_id=".gbl::getClientId()."";   //THIS LINE ISN'T USED!!

PageElements::setHead("<title>".Config::getMainTitle()." - ".JText::_('TIMESHEET_FOR').gbl::getContextUser()."</title>");
ob_start();

include("client_proj_task_javascript.php");
?>
<script type="text/javascript">

	function delete_entry(transNum) {
		if (confirm('Are you sure you want to delete this time entry?'))
			location.href = '<?php echo Config::getRelativeRoot(); ?>/delete?month=<?php echo gbl::getMonth(); ?>&amp;year=<?php echo gbl::getYear(); ?>&amp;day=<?php echo gbl::getDay(); ?>&amp;client_id=<?php echo gbl::getClientId(); ?>&amp;proj_id=<?php echo gbl::getProjId(); ?>&amp;task_id=<?php echo gbl::getTaskId(); ?>&amp;trans_num=' + transNum;
	}

</script>
<script type="text/javascript" src="<?php echo Config::getRelativeRoot();?>/js/datetimepicker_css.js"></script>
<?php 
PageElements::setHead(PageElements::getHead().ob_get_contents());
ob_end_clean();
PageElements::setBodyOnLoad('doOnLoad();');
?>
<form name="dayForm" action="<?php echo Rewrite::getShortUri(); ?>" method="get">
<!--<input type="hidden" name="month" value="<?php echo gbl::getMonth(); ?>" />-->
<!--<input type="hidden" name="year" value="<?php echo gbl::getYear(); ?>" />-->
<input type="hidden" name="task_id" value="<?php echo gbl::getTaskId(); ?>" />

<?php
	$currentDate = $todayDate;
	$fromPopup = "false";
	include("include/tsx/clockOnOff.inc"); 
?>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td align="left" nowrap class="outer_table_heading" nowrap>
			<?php echo ucwords(JText::_('DAILY')." ".JText::_('TIMESHEET')); ?>
		</td>
		<td align="left" nowrap class="outer_table_heading">
			<?php echo strftime(JText::_('DFMT_WKDY_MONTH_DAY_YEAR'), $todayDate); ?>
		</td>
		<td align="right" nowrap>
			<input id="date1" name="date1" type="text" size="25" onclick="javascript:NewCssCal('date1', 'ddmmmyyyy')" 
				value="<?php echo date('d-M-Y', $todayDate);  ?>" />
		</td>
		<td align="center" nowrap="nowrap" class="outer_table_heading">
			<input id="sub" type="submit" name="Change Date" value="<?php echo JText::_('CHANGE_DATE') ?>"></input>
		</td>
	</tr>
</table>

	<table width="100%" align="center" border="0" cellpadding="0" cellspacing="0" class="outer_table">
		<tr>
			<td>
			<tr class="inner_table_head">
			<td class="inner_table_column_heading" align="center"><?php print ucfirst(JText::_('CLIENT')) ?></td>
			<td class="inner_table_column_heading" align="center"><?php print ucfirst(JText::_('PROJECT')) ?></td>
			<td class="inner_table_column_heading" align="center"><?php print ucfirst(JText::_('TASK')) ?></td>
			<td class="inner_table_column_heading" align="center"><?php print ucwords(JText::_('WORK_DESCRIPTION')) ?></td>
			<td class="inner_table_column_heading" align="center" width="10%"><?php print ucfirst(JText::_('START')) ?></td>
			<td class="inner_table_column_heading" align="center" width="10%"><?php print ucfirst(JText::_('END')) ?></td>
			<td class="inner_table_column_heading" align="center" width="10%"><?php print ucfirst(JText::_('TOTAL')) ?></td>
			<td class="inner_table_column_heading" align="center" width="15%"><i><?php print ucfirst(JText::_('ACTIONS')) ?></i></td>
		</tr>
<?php

//Get the data
$startStr = date("Y-m-d H:i:s",$todayDate);
$endStr = date("Y-m-d H:i:s",$tomorrowDate);

$order_by_str = "start_stamp, ".tbl::getClientTable().".organisation, ".tbl::getProjectTable().".title, ".tbl::getTaskTable().".name, end_stamp";
list($num, $qh) = Common::get_time_records($startStr, $endStr, gbl::getContextUser(), 0, 0, $order_by_str);

if ($num == 0) {
	$ymdStrSd = "&amp;year=".$year . "&amp;month=".$month . "&amp;day=".$day;
	print "	<tr>\n";
	print "		<td class=\"calendar_cell_middle\"><i>No hours recorded.</i></td>\n";
	print "		<td class=\"calendar_cell_middle\">&nbsp;</td>\n";
	print "		<td class=\"calendar_cell_middle\">&nbsp;</td>\n";
	print "		<td class=\"calendar_cell_middle\" width=\"10%\">&nbsp;</td>\n";
	print "		<td class=\"calendar_cell_middle\" width=\"10%\">&nbsp;</td>\n";
	print "		<td class=\"calendar_cell_middle\" width=\"10%\">&nbsp;</td>\n";
	print "		<td class=\"calendar_cell_disabled_right\" width=\"15%\">&nbsp;</td>\n";
	$popup_href = "javascript:void(0)\" onclick=window.open(\"".Config::getRelativeRoot()."/clock_popup".
								"?client_id=".gbl::getClientId()."".
								"&amp;proj_id=".gbl::getProjId()."".
								"&amp;task_id=".gbl::getTaskId()."".
								"$ymdStrSd".
								"&amp;destination=$_SERVER[PHP_SELF]".
								"\",\"Popup\",\"location=0,directories=no,status=no,menubar=no,resizable=1,width=420,height=310\") dummy=\"";
	print "	<td class=\"calendar_cell_disabled_right\" width=\"15%\"><a href=\"$popup_href\" class=\"action_link\">".ucfirst(JText::_('ADD'))."</a>&nbsp;</td>\n";
	
	print "	</tr>\n";
	//print "</table>\n";
}
else {
	$last_task_id = -1;
	$taskTotal = 0;
	$todaysTotal = 0;

	$count = 0;
	while ($data = dbResult($qh)) {
		//There are several potential problems with the date/time data comming from the database
		//because this application hasn't taken care to cast the time data into a consistent TZ.
		//See: http://jokke.dk/blog/2007/07/timezones_in_mysql_and_php & read comments
		//So, we handle it as best we can for now...
		Common::fixStartEndDuration($data);

		$dateValues = getdate($data["start_stamp"]);
		$ymdStrSd = "&amp;year=".$dateValues["year"] . "&amp;month=".$dateValues["mon"] . "&amp;day=".$dateValues["mday"];
		$dateValues = getdate($data["end_stamp"]);
		$ymdStrEd = "&amp;year=".$dateValues["year"] . "&amp;month=".$dateValues["mon"] . "&amp;day=".$dateValues["mday"];
		
		//get the project title and task name
		$projectTitle = stripslashes($data["projectTitle"]);
		$taskName = stripslashes($data["taskName"]);
		$clientName = stripslashes($data["clientName"]);

		//start printing details of the task
		if (($count % 2) == 1)
			print "<tr class=\"diff\">\n";
		else
			print "<tr>\n";

		print "<td class=\"calendar_cell_middle\"><a href=\"javascript:void(0)\" onclick=\"javascript:window.open('client_info?client_id=$data[client_id]','Client Info','location=0,directories=no,status=no,scrollbar=yes,menubar=no,resizable=1,width=500,height=200')\">$clientName</a></td>\n";
		print "<td class=\"calendar_cell_middle\"><a href=\"javascript:void(0)\" onclick=\"javascript:window.open('proj_info?proj_id=$data[proj_id]','Project Info','location=0,directories=no,status=no,scrollbar=yes,menubar=no,resizable=1,width=500,height=200')\">$projectTitle</a></td>\n";
		print "<td class=\"calendar_cell_middle\"><a href=\"javascript:void(0)\" onclick=\"javascript:window.open('task_info?task_id=$data[task_id]','Task Info','location=0,directories=no,status=no,scrollbar=yes,menubar=no,resizable=1,width=300,height=150')\">$taskName</a></td>\n";
		print "<td class=\"calendar_cell_middle\">" . $data['log_message'] . "</td>\n";
		
		if ($data["duration"] > 0) {
			//format printable times
			if ($CfgTimeFormat == "12") {
				$formattedStartTime = date("g:iA",$data["start_stamp"]);
				$formattedEndTime = date("g:iA",$data["end_stamp"]);
			} else {
				$formattedStartTime = date("G:i",$data["start_stamp"]);
				$formattedEndTime = date("G:i",$data["end_stamp"]);
			}

			//if both start and end time are not today
			if ($data["start_stamp"] < $todayDate && $data["end_stamp"] > $tomorrowDate) {
				//all day - no one should work this hard!
				$taskTotal += get_duration($todayDate, $tomorrowDate);  

				$dc->open_cell_middle_td(); //<td....>
				echo "<font color=\"#909090\"><i>" . $formattedStartTime . ",";
				$dc->make_daily_link($ymdStrSd,gbl::getProjId(),date("d-M",$data["start_stamp"])); 
				echo "</i></font></td>" ;

				$dc->open_cell_middle_td(); //<td....>
				echo "<font color=\"#909090\"><i>" . $formattedEndTime . ",";
				$dc->make_daily_link($ymdStrEd,gbl::getProjId(),date("d-M",$data["end_stamp"])); 
				echo "</i></font></td>" ;

				$dc->open_cell_middle_td(); //<td....>
				echo Common::formatMinutes($taskTotal). "<font color=\"#909090\"><i> of " .
					Common::formatMinutes($data["duration"]) . "</i></font></td>\n";
			} //if end time is not today
			  elseif ($data["end_stamp"] > $tomorrowDate) {
				$taskTotal = Common::get_duration($data["start_stamp"],$tomorrowDate);

				$dc->open_cell_middle_td(); //<td....>
				echo $formattedStartTime . "</td>" ;

				$dc->open_cell_middle_td(); //<td....>
				echo "<font color=\"#909090\"><i>" . $formattedEndTime . "," ;
				$dc->make_daily_link($ymdStrEd,gbl::getProjId(),date("d-M",$data["end_stamp"])); 
				echo "</i></font></td>" ;

				$dc->open_cell_middle_td(); //<td....>
				echo  Common::formatMinutes($taskTotal). "<font color=\"#909090\"><i> of " . Common::formatMinutes($data["duration"]) . "</i></font></td>\n";
			} //elseif start time is not today
			  elseif ($data["start_stamp"] < $todayDate) {
				$taskTotal = Common::get_duration($todayDate,$data["end_stamp"]);

				$dc->open_cell_middle_td(); //<td....>
				echo "<font color=\"#909090\"><i>" . $formattedStartTime . "," ;
				$dc->make_daily_link($ymdStrSd,gbl::getProjId(),date("d-M",$data["start_stamp"])); 
				echo "</i></font></td>"; 

				$dc->open_cell_middle_td(); //<td....>
				echo $formattedEndTime . "</td>" ;

				$dc->open_cell_middle_td(); //<td....>
				echo Common::formatMinutes($taskTotal). "<font color=\"#909090\"><i> of " .
					Common::formatMinutes($data["duration"]) . "</i></font></td>\n";
			} else {
				$taskTotal = $data["duration"];
				$dc->open_cell_middle_td(); //<td....>
				print "$formattedStartTime</td>\n";
				$dc->open_cell_middle_td(); //<td....>
				print "$formattedEndTime</td>\n";
				$dc->open_cell_middle_td(); //<td....>
				print Common::formatMinutes($data["duration"]) . "</td>\n";
			}

			print "<td class=\"calendar_cell_disabled_right\" align=\"right\" nowrap>\n";
			if ($data['subStatus'] == "Open") {
				print "	<a href=\"".Config::getRelativeRoot()."/edit?client_id=".gbl::getClientId()."&amp;proj_id=".gbl::getProjId()."&amp;task_id=".gbl::getTaskId()."&amp;trans_num=$data[trans_num]&amp;year=".gbl::getYear()."&amp;month=".gbl::getMonth()."&amp;day=".gbl::getDay()."\" class=\"action_link\">".ucfirst(JText::_('EDIT'))."</a>,&nbsp;\n";
				//print "	<a href=\"".Config::getRelativeRoot()."/delete?client_id=".gbl::getClientId()."&amp;proj_id=".gbl::getProjId()."&amp;task_id=".gbl::getTaskId()."&amp;trans_num=$data[trans_num]\" class=\"action_link\">Delete, </a>\n";
				print "	<a href=\"javascript:delete_entry($data[trans_num]);\" class=\"action_link\">".ucfirst(JText::_('DELETE')).", </a>\n";
			} else {
				// submitted or approved times cannot be edited
				print  $data['subStatus'] . "&nbsp;\n";
			}
			$popup_href = "javascript:void(0)\" onclick=window.open(\"".Config::getRelativeRoot()."/clock_popup".
											"?client_id=".gbl::getClientId()."".
											"&amp;proj_id=".gbl::getProjId()."".
											"&amp;task_id=".gbl::getTaskId()."".
											"$ymdStrSd".
											"&amp;destination=$_SERVER[PHP_SELF]".
											"\",\"Popup\",\"location=0,directories=no,status=no,menubar=no,resizable=1,width=420,height=310\") dummy=\"";
			print "	<a href=\"$popup_href\" class=\"action_link\">".ucfirst(JText::_('ADD'))."</a>&nbsp;\n";
			print "</td>";

			//add to todays total
			$todaysTotal += $taskTotal;
		} else {
			if ($CfgTimeFormat == "12") 
				$formattedStartTime = date("g:iA",$data["start_stamp"]);
			else
				$formattedStartTime = date("G:i",$data["start_stamp"]);
			
			$dc->open_cell_middle_td(); //<td....>
			print "$formattedStartTime</td>\n";
			$dc->open_cell_middle_td(); //<td....>
			print "&nbsp;</td>\n";
			$dc->open_cell_middle_td(); //<td....>
			print "&nbsp;</td>\n";
			print "<td class=\"calendar_cell_disabled_right\" align=\"right\" nowrap>\n";
			/**
			 * Update by robsearles 26 Jan 2008
			 * Added a "Clock Off" link to make it easier to stop timing a task
			 * Common::getRealTodayDate() is defined in common.inc
			 */
			if ($data["start_stamp"] == Common::getRealTodayDate()) {
				$stop_link = '<a href="'.Config::getRelativeRoot().'/clock_action?client_id='.$data['client_id'].'&amp;proj_id='.
						$data['proj_id'].'&amp;task_id='.$data['task_id'].
						'&amp;clock_off_check=on&amp;clock_off_radio=now" class="action_link\">'.JText::_('CLOCK_OFF').'</a>, ';
				print $stop_link;
			}
			print "	<a href=\"javascript:delete_entry($data[trans_num]);\" class=\"action_link\">".ucfirst(JText::_('DELETE'))."</a>\n";
			print "</td>";
		}

		print "</tr>";
		$count++;
	}
	print "<tr>\n";
	print "	<td class=\"calendar_totals_line_weekly_right\" colspan=\"7\" align=\"right\">";
	print " Daily Total: <span class=\"calendar_total_value_weekly\" nowrap>" . Common::formatMinutes($todaysTotal) . "</span></td>\n";
	print "	<td class=\"calendar_cell_disabled_right\" align=\"right\" nowrap>&nbsp;</td>\n";
	print "</tr>\n";
	print "</table>";
}
?>

</form>
<!-- ?php include("include/tsx/clockOnOff.inc"); ?-->
