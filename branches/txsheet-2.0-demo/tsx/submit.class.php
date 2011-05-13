<?php
if(!class_exists('Site'))die('Restricted Access');
class SubmitClass{

	private $time_fmt;
  
	public function setTimeFmt($t){
		$this->time_fmt = $t;
	}
	
	public function __construct(){}
	
	public function format_time($time) {
		if($time > 0) {
			if($this->time_fmt == "decimal")
				return Common::minutes_to_hours($time);
			else 
				return Common::format_minutes($time);
		} 
		else 
			return "-";
	}
	
	public function jsPopupInfoLink($script, $variable, $info, $title = "Info") {
		print "<a href=\"javascript:void(0)\" ONCLICK=window.open(\"" . $script .
			"?$variable=$info\",\"$title\",\"location=0,directories=no,status=no,scrollbar=yes," .
			"menubar=no,resizable=1,width=500,height=200\")>";
	}
	
	public function make_daily_link($ymdStr, $proj_id, $string) {
		echo "<a href=\"".Config::getRelativeRoot()."/daily?" .  $ymdStr .  "&amp;proj_id=$proj_id\">" . 
			$string .  "</a>&nbsp;"; 
	}
	
	/**
	 * Print a line of time information
	 * @params String $type - the name of the field to be printed
	 * @params String Array - the data to be formatted and printed
	 */
	public function printInfo($type, $data) {	
		switch ($type) {
			case "projectTitle": 
				self::jsPopupInfoLink(Config::getRelativeRoot()."/client_info", "client_id", $data["client_id"], "Client_Info");
				print stripslashes($data["clientName"])."</a> / ";
				self::jsPopupInfoLink(Config::getRelativeRoot()."/proj_info", "proj_id", $data["proj_id"], "Project_Info");
				print stripslashes($data["projectTitle"])."</a>&nbsp;\n";
				break;
			case "taskName":
				self::jsPopupInfoLink(Config::getRelativeRoot()."/task_info", "task_id", $data["task_id"], "Task_Info");
				print stripslashes($data["taskName"])."</a>&nbsp;\n";
					break;
			case "duration":
				//self::jsPopupInfoLink(Config::getRelativeRoot()."/trans_info", "trans_num", $data["trans_num"], "Time_Entry_Info");
				print self::format_time($data["duration"]);
				break;
				 
			case "start_stamp":
				$dateValues = getdate($data["start_stamp"]);
				$ymdStr = "&amp;year=".$dateValues["year"] . "&amp;month=".$dateValues["mon"] . "&amp;day=".$dateValues["mday"];
				$formattedDate = sprintf("%04d-%02d-%02d",$dateValues["year"],$dateValues["mon"],$dateValues["mday"]); 
				self::make_daily_link($ymdStr,0,$formattedDate); 
				break;
				
			case "start_time":
				$dateValues = getdate($data["start_stamp"]);
				//$hmStr = "&hour=".$dateValues["hours"] . "&mins=".$dateValues["minutes"];
				$formattedTime = sprintf("%02d:%02d",$dateValues["hours"],$dateValues["minutes"]); 
		//	LogFile::write("starttime start_stamp = \"" .  $data["start_stamp"]   ."\" hr =\"" .  $dateValues["hours"]   .
		//		"\" min =\"" .  $dateValues["minutes"] . "\" formattedtime =\"" .  $formattedTime . "\"\n");
				print $formattedTime;
				break;
				
			case "stop_time":
				$dateValues = getdate($data["end_stamp"]);
				//$hmStr = "&hour=".$dateValues["hours"] . "&mins=".$dateValues["minutes"];
				$formattedTime = sprintf("%02d:%02d",$dateValues["hours"],$dateValues["minutes"]); 
				print $formattedTime;
				break;
				
			case "log":
				if ($data['log_message']) print stripslashes($data['log_message']);
				else print "&nbsp;";
				break;
				
			case "status":
				if ($data['status']) {
					// print the different status descriptions internationalised
					switch($data['status']) {
						case "Open":
							print JText::_('STATUS_OPEN');
							break;
						case "Submitted":
							print JText::_('STATUS_SUBMITTED');
							break;
						case "Approved":
							print JText::_('STATUS_APPROVED');
							break;
					}
							
				}
				else print "&nbsp;";
				break;
				
			case "submit":
				if ($data['status'] == "Open") print "<input type=\"checkbox\" name=\"sub[]\" value=\"" . $data["trans_num"] . "\">";
				else print "&nbsp;";
				break;
				
			case "approve":
				if ($data['status'] == "Submitted") print "<input type=\"checkbox\" name=\"approve[]\" value=\"" . $data["trans_num"] . "\">";
					else print "&nbsp;";
				break;
				
			case "reject":
				if ($data['status'] == "Submitted") print "<input type=\"checkbox\" name=\"reject[]\" value=\"" . $data["trans_num"] . "\">";
					else print "&nbsp;";
				break;
				
			default:
				print "&nbsp;";
		}
	}

}