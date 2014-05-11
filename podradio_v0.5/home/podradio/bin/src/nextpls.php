<?
	date_default_timezone_set("Europe/Paris");
	$heure = date("H");
	$minute = date("i");
	if (($minute >= 0 && $minute <= 12) || $minute >= 58) {
			$interval = $heure."h00";
			if($minute >= 12 && date("s") >= 30) $interval = $heure."h15"; 
			if($minute >= 58 && date("s") >= 30) {
				echo $heure;
				if($heure == 23) $heure = "0";
				else $heure++;
				$interval = $heure."h00";		
			}
	} elseif ($minute >= 13 && $minute <= 27) {
			$interval = $heure."h15";
			if($minute >= 27 && date("s") >= 30) $interval = $heure."h30"; 
	} elseif ($minute >= 28 && $minute <= 42) {
			$interval = $heure."h30";
			if($minute >= 42 && date("s") >= 30) $interval = $heure."h45"; 
	} elseif ($minute >= 43) {
			$interval = $heure."h45";
	}
	if($heure >= 0 && $heure < 6) {
		if(date("N")-1 == 0) $file = "7.".$interval;
		else $file = (date("N")-1).".".$interval;
	} else $file = date("N").".".$interval;
	if(substr($file, 2, 1) == "0" && substr($file, 3, 1) != "h") $file = substr($file, 0, 2).substr($file, 3, strlen($file)-3);
	echo "#/etc/podradio/content/pls/".$file.".m3u\n";
	echo @file_get_contents("/etc/podradio/content/pls/".$file.".m3u");
?>
