<?php

$file = file("rss.conf");

echo "#!/bin/sh\nrm /var/medias/partenaires/vrac/auto/*\nrm /var/medias/play/prime/auto/*\n";
echo "echo \"RSS Update [partenaires] : \"\n";
echo "echo ''\n";
echo "echo \"J'ai viré le vieux vrac auto et le vieux prime auto.\"\n";
echo "echo ''\n";

foreach($file as $item) {
$url = substr($item, 0, strripos($item,":"));
$num = substr($item, (strripos($item,":")+1));

download_for($url, $num);

}

 
function download_for($xml, $number) {
	$long = "/var/medias/play/prime/auto";
	$short = "/var/medias/partenaires/vrac/auto";

   if($number <= 0) return false;
   if($flux = simplexml_load_file($xml)) {
		$donnee = $flux->channel;
		$i=0;
		foreach($donnee->item as $valeur) {
			if((strtotime($valeur->pubDate)+2678400) > time()) {
				$array = $valeur->enclosure;
				if(substr($array[url], (strlen($array[url])-3)) == "mp3" || substr($array[url], (strlen($array[url])-3)) == "m4a") { 
				if($array[length][0]<= 35000000) {
					echo "echo \"J'ai téléchargé ".$array[url].".\" && wget -q ".$array[url]." -O ".substr($array[url], (strripos($array[url], "/")+1))." && mv ".substr($array[url], (strripos($array[url], "/")+1))." ".$short."/".substr($array[url], (strripos($array[url], "/")+1));
					if(substr($array[url], (strlen($array[url])-3)) == "m4a") {
						$file_local = $short."/".substr($array[url], (strripos($array[url], "/")+1));
						echo " && echo \"J'ai converti ".$file_local." en ".substr($file_local,0,(strlen($file_local)-3))."mp3. \n\" && mencoder -msglevel all=-1 -q -quiet -vc null -ovc copy -oac mp3lame -lameopts br=192 ".$file_local." -o ".substr($file_local,0,(strlen($file_local)-3))."mp3 > /dev/null && rm ".$file_local;
					}
				} else {
					echo "echo \"J'ai téléchargé ".$array[url].".\" && wget -q ".$array[url]." -O ".substr($array[url], (strripos($array[url], "/")+1))." && mv ".substr($array[url], (strripos($array[url], "/")+1))." ".$long."/".substr($array[url], (strripos($array[url], "/")+1));
					if(substr($array[url], (strlen($array[url])-3)) == "m4a") {
						$file_local = $long."/".substr($array[url], (strripos($array[url], "/")+1));
						echo " && echo \"J'ai converti ".$file_local." en ".substr($file_local,0,(strlen($file_local)-3))."mp3. \n\" && mencoder -msglevel all=-1 -q -quiet -vc null -oac mp3lame -ovc copy -lameopts br=192 ".$file_local." -o ".substr($file_local,0,(strlen($file_local)-3))."mp3 > /dev/null && rm ".$file_local;
					}
				}				
				echo "\n";			
				$i++;
				if($i >= $number) return true; 
				}
			}
		}
	}
	else return false;
}

?>
