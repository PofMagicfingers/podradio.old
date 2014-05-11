<?php

$file = file("internal_rss.conf");

echo "#!/bin/sh\n";
echo "echo \"RSS Update [internal] : \"\n";
echo "echo ''\n";

foreach($file as $item) {
$url = substr($item, 0, strripos($item,":"));
$num = substr($item, (strripos($item,":")+1));
$news = false;
if($url == "http://feeds2.feedburner.com/Pod__News") $news = true;

download_for($url, $num, $news);

}

 
function download_for($xml, $number, $isnews) {
	$long = "/var/medias/play/prime/auto";
	$short = "/var/medias/partenaires/vrac/auto";
	$news = "/var/medias/news/playing";

   if($number <= 0) return false;
   if($flux = simplexml_load_file($xml)) {
		$donnee = $flux->channel;
		$i=0;
		foreach($donnee->item as $valeur) {
				$array = $valeur->enclosure;
				if(substr($array[url], (strlen($array[url])-3)) == "mp3" || substr($array[url], (strlen($array[url])-3)) == "m4a") { 
				
				if($array[length][0]<= 35000000) {
					if($isnews) $folder = $news;
					else $folder = $short;

					echo "echo \"J'ai téléchargé ".$array[url].".\" && wget -q ".$array[url]." -O ".substr($array[url], (strripos($array[url], "/")+1))." && mv ".substr($array[url], (strripos($array[url], "/")+1))." ".$folder."/".substr($array[url], (strripos($array[url], "/")+1));
					if(substr($array[url], (strlen($array[url])-3)) == "m4a") {
						$file_local = $folder."/".substr($array[url], (strripos($array[url], "/")+1));
						echo " && echo \"J'ai converti ".$file_local." en ".substr($file_local,0,(strlen($file_local)-3))."mp3. \n\" && mencoder -msglevel all=-1 -q -quiet -vc null -ovc copy -oac mp3lame -lameopts br=192 ".$file_local." -o ".substr($file_local,0,(strlen($file_local)-3))."mp3 > /dev/null && rm ".$file_local;
					}
				} else {
					echo "echo \"J'ai téléchargé ".$array[url].".\" && wget -q ".$array[url]." -O ".substr($array[url], (strripos($array[url], "/")+1))." && mv ".substr($array[url], (strripos($array[url], "/")+1))." ".$long."/".substr($array[url], (strripos($array[url], "/")+1));
					if(substr($array[url], (strlen($array[url])-3)) == "m4a") {
						$file_local = $long."/".substr($array[url], (strripos($array[url], "/")+1));
						echo " && echo \"J'ai converti ".$file_local." en ".substr($file_local,0,(strlen($file_local)-3))."mp3. \n\" && mencoder -msglevel all=-1 -q -quiet -vc null -ovc copy -oac mp3lame -lameopts br=192 ".$file_local." -o ".substr($file_local,0,(strlen($file_local)-3))."mp3 > /dev/null && rm ".$file_local;
					}
				}				
				echo "\n";			
				$i++;
				if($i >= $number) return true; 
			}
		}
	}
	else return false;
}

?>
