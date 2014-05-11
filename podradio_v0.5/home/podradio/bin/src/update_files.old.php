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



 
function download_for($xml, $name, $number, $limit) {
	$long = "/var/medias/play/prime/auto";
	$short = "/var/medias/partenaires/vrac/auto";

   if($number <= 0) return false;
   if($name == "") return false;
   if($flux = @simplexml_load_file($xml)) {
		$donnee = $flux->channel;
		$i=0;
		echo "mkdir /etc/podradio/content/files/".md5($name)."\n";
		foreach($donnee->item as $valeur) {
			if((strtotime($valeur->pubDate)+2678400) > time()) {
				$array = $valeur->enclosure;
				$ext = substr($array[url], (strlen($array[url])-3));
				$file = "/etc/podradio/content/files/".md5($name)."/show.".($i+1).".".$ext;
              if($array[length][0]<= $limit || $limit < 0) {
				if($ext == "mp3" || $ext == "m4a") { 
					echo "wget -q ".$array[url]." -O /tmp/".substr($array[url], (strripos($array[url], "/")+1))." && mv /tmp/".substr($array[url], (strripos($array[url], "/")+1))." ".$file;
					if($ext == "m4a") {
						echo " && mencoder -msglevel all=-1 -q -quiet -vc null -ovc copy -oac mp3lame -lameopts br=192 ".$file." -o /etc/podradio/content/files/".md5($name)."/show.".($i+1).".mp3 > /dev/null && rm ".$file;
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