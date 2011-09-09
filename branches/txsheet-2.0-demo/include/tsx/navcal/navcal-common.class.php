<?php
if(!class_exists('Site'))die('Restricted Access');

// navigational calendar functions
class NavCalCommon{
	protected function getNextMonth($time,$orig_day=0,$need_ldom=-1) {
		$dti=getdate($time);
		$next_month = $dti["mon"] + 1;
		$next_mon_year = $dti["year"];

		//calculate the next month
		if($next_month == 13) {
			$next_month = 1;
			$next_mon_year++;
		}

		if($orig_day==0)
			$orig_day=$dti["mday"];

		$nmday=$orig_day;

		if($need_ldom>-1) {
			$need_last_day_of_month=$need_ldom;
		} else {
			if($nmday == date('t',$time))
				$need_last_day_of_month=1;
			else
				$need_last_day_of_month=0;
		}

		if($nmday > 27) {
			if(!checkdate($next_month, $nmday, $next_mon_year) || $need_last_day_of_month )
				$nmday = date('t', strtotime("$next_mon_year-$next_month-15"));
		}

		return mktime(0,0,0,$next_month,$nmday,$next_mon_year);
	}

	protected function getPrevNextMonth($time,$need_ldom=-1) {
		$dti=getdate($time);
		// Calculate the previous month.
		$last_month = $dti["mon"] - 1;
		$last_mon_year = $dti["year"];

		$next_month = $dti["mon"] + 1;
		$next_mon_year = $dti["year"];

		if($last_month == 0) {
			$last_month = 12;
			$last_mon_year --;
		}

		//calculate the next month
		if($next_month == 13) {
			$next_month = 1;
			$next_mon_year++;
		}

		$lmday=$dti["mday"];
		$nmday=$dti["mday"];

		if($need_ldom>-1) {
			$need_last_day_of_month=$need_ldom;
		} else {
			if($lmday == date('t',$time))
				$need_last_day_of_month=1;
			else
				$need_last_day_of_month=0;
		}

		if($lmday > 27) {
			if(!checkdate($last_month, $lmday, $last_mon_year) || $need_last_day_of_month )
				$lmday = date('t', strtotime("$last_mon_year-$last_month-15"));

			if(!checkdate($next_month, $nmday, $next_mon_year) || $need_last_day_of_month )
				$nmday = date('t', strtotime("$next_mon_year-$next_month-15"));
		}

		return array(mktime(0,0,0,$last_month,$lmday,$last_mon_year),
					 mktime(0,0,0,$next_month,$nmday,$next_mon_year));
	}

	protected function getPrevNextYear($time,$need_ldom=-1) {
		$dti=getdate($time);

		$month = $dti["mon"];
		$last_year = $dti["year"] - 1;
		$next_year = $dti["year"] + 1;

		$lyday=$dti["mday"];
		$nyday=$dti["mday"];

		if($need_ldom>-1) {
			$need_last_day_of_month=$need_ldom;
		} else {
			if($lyday == date('t',$time))
				$need_last_day_of_month=1;
			else
				$need_last_day_of_month=0;
		}

		if($lyday > 27) {
			if(!checkdate($month, $lyday, $last_year) || $need_last_day_of_month )
				$lyday = date('t', strtotime("$last_year-$month-15"));

			if(!checkdate($month, $nyday, $next_year) || $need_last_day_of_month )
				$nyday = date('t', strtotime("$next_year-$month-15"));
		}

		return array(mktime(0,0,0,$month,$lyday,$last_year),
					 mktime(0,0,0,$month,$nyday,$next_year));
	}

	protected function __print_month_name($entry,$post) {
		$dti=getdate($entry);
		print "\t<td valign=\"top\" align=\"center\" class=\"calendar_cell_middle\" style=\"font-size: 11\">";
		if($dti["mon"] == gbl::getMonth() && $dti["year"] == gbl::getYear()) {
			print "\t<font color=\"#CC9900\"><b>".utf8_encode(strftime("%b",$entry))."</b></font></td>\n";
		} else {
			echo "\t<a href=\"".Rewrite::getShortUri()."?$post";
			echo "&amp;year=".$dti["year"]."&amp;month=".$dti["mon"]."&amp;day=".$dti["mday"]."\">" . utf8_encode(strftime("%b",$entry)) . "</a></td>\n";
		}
	}

	protected function draw_month_year_navigation($navyear,$orient="tall") {

		$post = gbl::getPost();
		$navmon = 1;
		$navday = gbl::getDay();

		$curDate = mktime(0,0,0,gbl::getMonth(),gbl::getDay(),$navyear);
		$contextDate = mktime(0,0,0,gbl::getMonth(),gbl::getDay(),gbl::getYear());

		//determine if our context date is on the last day of the month
		$need_ldom=0;
		if($navday == date('t',$contextDate)) {
			$need_ldom=1;
			$navday = date('t', strtotime("$navyear-1-15"));
		}

		//January has 31 days, so we don't need to verify that Jan, $day, $navyear is a valid date
		$firstDate = mktime(0,0,0,1,$navday,$navyear);

		if($orient == "tall"){
			echo '<table width="102" border="1"  cellspacing="0" cellpadding="0">
				<tr>
					<td width="100%" class="face_padding_cell" style="background-color: #000788">';
		}
		else {
			echo '<table width="202"  border="1"  cellspacing="0" cellpadding="0">
				<tr>
					<td width="100%" class="face_padding_cell_small" style="background-color: #000788">';
		}

		echo'<!-- print monthly header -->';

		echo'<table width="100%" border="0">
			<tr>';
			if($orient == "tall"){
				echo'<td align="left"  class="navcal_header">';
			}
			else{
				echo '<td align="left"  class="navcal_header" style="font-size: 10">';
			}
					
			list($prev_year,$next_year) = $this->getPrevNextYear($curDate,$need_ldom);
			echo "<a href=\"".Rewrite::getShortUri()."?$post";
			$dti=getdate($prev_year);
			echo 	"&amp;year=".$dti["year"].	
					"&amp;month=".$dti["mon"].
					"&amp;day=".$dti["mday"].
					"\"><font size=\"+1\">&laquo;</font></a>";

				echo '</td>';
						if($orient == "tall"){
							echo '<td align="center"  class="navcal_header">';
						}
						else{
							echo '<td align="center"  class="navcal_header" style="font-size: 12">';
						}
						echo date('Y',$firstDate);
						echo '</td>';
						
						if($orient == "tall"){
							echo '<td align="right"  class="navcal_header">';
						}
						else{
							echo '<td align="right"  class="navcal_header" style="font-size: 10">';
						}
						

						echo "<a href=\"".Rewrite::getShortUri()."?$post";
						$dti=getdate($next_year);
						echo "&amp;year=".$dti["year"].	
								"&amp;month=".$dti["mon"].
								"&amp;day=".$dti["mday"].
								"\"><font size=\"+1\">&raquo;</font></a>";

						echo '</td>
					</tr>
				</table>';

				echo '<!-- print month names  -->';
				echo '<table width="100%" align="center" border="0" cellpadding="0" cellspacing="0" class="outer_table">
						<tr>
							<td>';
							if($orient == "tall"){
								echo '<table width="100%" border="0" cellspacing="0" cellpadding="2" class="table_body">';
							}
							else{
								echo '<table width="100%" border="0" cellspacing="0" cellpadding="1" class="table_body">';
							}

							$curDate = $firstDate;
							if($orient == "tall") {
								//print tall format, 2 columns by 6 rows
								for($i=0; $i<12; $i++) {	
									$stampsary[]=$curDate;
									$curDate=$this->getNextMonth($curDate,$navday,$need_ldom);
								}

								for($i=0; $i<6; $i++) {	
									$dti=getdate($stampsary[$i]);
									print "<tr>\n";
									$this->__print_month_name($stampsary[$i],$post);
									$this->__print_month_name($stampsary[$i+6],$post);
									print   "</tr>\n";
								} 
							} 
							else {
								//print wide format, 4 columns by 3 rows
								for($i=0; $i<3; $i++) {	
									echo "<tr>\n";
									for($j=0; $j<4; $j++) {	
										$this->__print_month_name($curDate,$post);
										$curDate=$this->getNextMonth($curDate,$navday,$need_ldom);
									}
									echo "</tr>\n";
								} 
							}
								?>
								</table>
							</td>
						</tr>
					</table>
					<!-- End month names -->
				</td>
			</tr>
		</table>
<?php 
	}  //end of function

	protected function __print_month_name_with_end_date($start,$end,$post) {
		global $start_time;
		$sdti=getdate($start);
		$edti=getdate($end);
		$cdti=getdate($start_time);
		print "\t<td valign=\"top\" align=\"center\" class=\"calendar_cell_middle\" style=\"font-size: 11\">";
		if($sdti["mon"] == $cdti["mon"] && $sdti["year"] == $cdti["year"]) {
			print "\t<font color=\"#CC9900\"><b>".utf8_encode(strftime("%b",$start))."</b></font></td>\n";
		} else {
			print "\t<a href=\"" . Rewrite::getShortUri() . "?$post";
			print 	"&amp;start_year=".$sdti["year"] .	
					"&amp;start_month=".$sdti["mon"] .
					"&amp;start_day=".$sdti["mday"] .
				 	"&amp;end_year=".$edti["year"] .	
					"&amp;end_month=".$edti["mon"] .
					"&amp;end_day=".$edti["mday"] .
					"\">" . utf8_encode(strftime("%b",$start)) . "</a></td>\n";
		}
	}

	protected function draw_month_year_navigation_with_end_dates($startDate,$endDate,$orient="tall") {

		$post = gbl::getPost();

		$sdti=getdate($startDate);
		$edti=getdate($endDate);

		$mdiff = $sdti["mon"]-1; //how many months between $startDate and January
		$firstEndMonth=$edti["mon"] - $mdiff;
		$firstEndYear=$edti["year"];

		if($firstEndMonth < 1) {
			$firstEndMonth += 12;
			$firstEndYear--;
		}

		//determine if either start or end dates are on the current last day of the month
		if($sdti["mday"] == date('t',$startDate)) {
			$s_need_ldom=1;
			$sdti["mday"] = date('t', strtotime($sdti["year"]."-1-15"));
		} else
			$s_need_ldom=0;


		if($edti["mday"] == date('t',$endDate)) {
			$e_need_ldom=1;
			$edti["mday"] = date('t', strtotime("$firstEndYear-$firstEndMonth-15"));
		} else
			$e_need_ldom=0;

		$orig_sday = $sdti["mday"];
		$orig_eday = $edti["mday"];

		//January has 31 days, so we don't need to verify that Jan, $day, $navyear is a valid date

		$firstEndDay=$edti["mday"];

		$firstStartDate = mktime(0,0,0,1,$sdti["mday"],$sdti["year"]);
		$firstEndDate = mktime(0,0,0,$firstEndMonth,$firstEndDay,$firstEndYear);

?>
		<?php if($orient == "tall"): ?>
			<table width="102" border="1" cellspacing="0" cellpadding="0">
				<tr>
					<td width="100%" class="face_padding_cell" style="background-color: #000788">
		<?php else: ?>
			<table width="202" border="1" cellspacing="0" cellpadding="0">
				<tr>
					<td width="100%" class="face_padding_cell_small" style="background-color: #000788">
		<?php endif; ?>

				<!-- print monthly header -->

					<table width="100%" border="0">
						<tr >
						<?php if($orient == "tall"): ?>
							<td align="left"  class="navcal_header">
						<?php else: ?>
							<td align="left"  class="navcal_header" style="font-size: 10">
						<?php endif; ?>
								<?php 
								list($sprev_year,$snext_year) = $this->getPrevNextYear($startDate,$s_need_ldom);
								list($eprev_year,$enext_year) = $this->getPrevNextYear($endDate,$e_need_ldom);
								print "<a href=\"".Rewrite::getShortUri()."?$post";
								$sdti=getdate($sprev_year);
								$edti=getdate($eprev_year);
								print 	"&amp;start_year=".$sdti["year"].	
										"&amp;start_month=".$sdti["mon"].
										"&amp;start_day=".$sdti["mday"].
									 	"&amp;end_year=".$edti["year"].	
										"&amp;end_month=".$edti["mon"].
										"&amp;end_day=".$edti["mday"].
										"\"><font size=\"+1\">&laquo;</font></a>";
								?>
							</td>
						<?php if($orient == "tall"): ?>
							<td align="center"  class="navcal_header">
						<?php else: ?>
							<td align="center"  class="navcal_header" style="font-size: 12">
						<?php endif; ?>
								<?php echo date('Y',$firstStartDate); ?>
							</td>
						<?php if($orient == "tall"): ?>
							<td align="right"  class="navcal_header">
						<?php else: ?>
							<td align="right"  class="navcal_header" style="font-size: 10">
						<?php endif; ?>
								<?php
								print "<a href=\"".Rewrite::getShortUri()."?$post";
								$sdti=getdate($snext_year);
								$edti=getdate($enext_year);
								print 	"&amp;start_year=".$sdti["year"].	
										"&amp;start_month=".$sdti["mon"].
										"&amp;start_day=".$sdti["mday"].
									 	"&amp;end_year=".$edti["year"].	
										"&amp;end_month=".$edti["mon"].
										"&amp;end_day=".$edti["mday"].
										"\"><font size=\"+1\">&raquo;</font></a>";
								?>
							</td>
						</tr>
					</table>

					<!-- print month names  -->
					<table width="100%" align="center" border="0" cellpadding="0" cellspacing="0" class="outer_table">
						<tr>
							<td>
							<?php if($orient == "tall"): ?>
								<table width="100%" border="0" cellspacing="0" cellpadding="2" class="table_body">
							<?php else: ?>
								<table width="100%" border="0" cellspacing="0" cellpadding="1" class="table_body">
							<?php endif; ?>
								<?php
									$curStartDate = $firstStartDate;
									$curEndDate = $firstEndDate;
									if($orient == "tall") {
										//print tall format, 2 columns by 6 rows
										for($i=0; $i<12; $i++) {	
											$startary[]=$curStartDate;
											$endary[]=$curEndDate;
											$curStartDate=$this->getNextMonth($curStartDate,$orig_sday,$s_need_ldom);
											$curEndDate=$this->getNextMonth($curEndDate,$orig_eday,$e_need_ldom);
										}

										for($i=0; $i<6; $i++) {	
											print "<tr>\n";
											$this->__print_month_name_with_end_date($startary[$i],$endary[$i],$post);
											$this->__print_month_name_with_end_date($startary[$i+6],$endary[$i+6],$post);
											print   "</tr>\n";
										} 
									} else {
										//print wide format, 4 columns by 3 rows
										for($i=0; $i<3; $i++) {	
											print "<tr>\n";
											for($j=0; $j<4; $j++) {	
												$this->__print_month_name_with_end_date($curStartDate,$curEndDate,$post);
												$curStartDate=$this->getNextMonth($curStartDate,$orig_sday,$s_need_ldom);
												$curEndDate=$this->getNextMonth($curEndDate,$orig_eday,$e_need_ldom);
											}
											print "</tr>\n";
										} 
									}
								?>
								</table>
							</td>
						</tr>
					</table>
					<!-- End month names -->
				</td>
			</tr>
		</table>
<?php 
	}  //end of function
}

?>
