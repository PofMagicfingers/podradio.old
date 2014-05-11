<?php
echo "#!/bin/sh\nrm -r /etc/podradio/content/files\nmkdir /etc/podradio/content/files\n";

$handle = opendir('/etc/podradio/content/podcasts/');

while (false !== ($file = readdir($handle))) {
   	if(substr($file, 0, 8) == "podcast_") {
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
}

closedir($handle);

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
		echo "mkdir /etc/podradio/content/files/".md5($name)."\n";

		$items = array();

                foreach ($donnee->item as $value) {
                        $items[] = $value;
                }

                usort($items, "cmpItems");

		foreach($items as $valeur) {
			if((strtotime($valeur->pubDate)+4017600) > time()) {
				$array = $valeur->enclosure;
				$ext = substr($array["url"], (strlen($array["url"])-3));
				$file = "/etc/podradio/content/files/".md5($name)."/show.".($i+1).".mp3"; //.$ext;
              if($array["length"][0]<= $limit || $limit < 0) {
				if($ext == "mp3" || $ext == "m4a") { 
					echo "wget -q ".$array["url"]." -O /tmp/".substr($array["url"], (strripos($array["url"], "/")+1))." && mv /tmp/".substr($array["url"], (strripos($array["url"], "/")+1))." ".$file;
					if($ext == "m4a") {
					//	echo " &&  faad -o - ".$file." 2> /dev/null | lame - /etc/podradio/content/files/".md5($name)."/show.".($i+1).".mp3 && rm ".$file;
					}
				echo "\n";			
				$i++;
				if($i >= $number) return true; 
				}
			  }
			}
		}
	}
	else return false;
}

?>
