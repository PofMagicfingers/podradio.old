<?php

if(count($argv) > 1) {
	if(file_exists("/etc/podradio/content/podcasts/podcast_".$argv[1])) {
		file_task("podcast_".$argv[1]);
		die();
	} else die("\nCan't find this podcast\n");
}

$handle = opendir('/etc/podradio/content/podcasts/');
while (false !== ($file = readdir($handle))) {
   	if(substr($file, 0, 8) == "podcast_") {
		file_task($file);
       	}
}
closedir($handle);

function file_task($file) {
        $podcast = file("/etc/podradio/content/podcasts/".$file);
        
        if(!($podcast[3] > 0)) $limit = -1;
        else $limit = $podcast[3];
        if(trim($podcast[4]) == "s") $limit = ($podcast[3]/60);
        $limit *= 1000000;
       	$name = trim($podcast[0]);
       	$url = trim($podcast[1]);
       	$num = trim($podcast[2]);
	
	download_for($url, $name, $num, $limit);
}

function cmpItems($a, $b)
{
    if (strtotime($a->pubDate) == strtotime($b->pubDate)) {
        return 0;
    }
    return (strtotime($a->pubDate) > strtotime($b->pubDate)) ? -1 : 1;
}

function download_for($xml, $name, $number, $limit) {

   if($number <= 0) return false;
   if($name == "") return false;

   if($flux = @simplexml_load_file("http://radio.podradio.fr/ressources/xmlproxy.php?flux=".urlencode($xml))) {
		$donnee = $flux->channel;
		$i=0;
		echo "\n\n#name : ".$name."\n#url : ".$xml."\n\n";

		$items = array();

		foreach ($donnee->item as $value) {
    			$items[] = $value;
		}

		usort($items, "cmpItems");

		foreach($items as $valeur) {
			echo "\n\t#item";
			echo "\n\t\t#pubDate: ".$valeur->pubDate;
			echo "\n\t\t#title: ".$valeur->title;
			if((strtotime($valeur->pubDate)+4017600) > time()) {
				echo "\n\t\t\t#enclosure";
				$array = $valeur->enclosure;
				$ext = substr($array["url"], (strlen($array["url"])-3));
		                echo "\n\t\t\t#url: ".$array["url"];
		                echo "\n\t\t\t#ext: ".$ext;
		                echo "\n\t\t\t#length: ".$array["length"];
		                echo "\n\t\t\tlimit => ".(($limit > 0)?$limit:"none");

				if($array["length"][0] <= $limit || $limit < 0) {
					if($ext == "mp3" || $ext == "m4a") { 
				                echo "\n\t\t\t==> will download it";
						if($ext == "m4a") {
						//	echo " &&  faad -o - ".$file." 2> /dev/null | lame - /etc/podradio/content/files/".md5($name)."/show.".($i+1).".mp3 && rm ".$file;
						}
						echo "\n";			
						$i++;
				
						if($i >= $number) return true; 
				
					}
				}

			} else echo "\n\t\t\t skip => too old";
			echo "\n\n";
		}
	}
	else return false;
}

?>
