CREATE TABLE `timesheet_allowances` (`entry_id` INT NOT NULL AUTO_INCREMENT, `username` varchar(32) NOT NULL default '0',
 `date` DATE NOT NULL, `holiday` INT NOT NULL, `glidetime` TIME NOT NULL, PRIMARY KEY (`entry_id`)) ENGINE = MyISAM