<?
	        $n = date('N');
                $j = array(1 => 'lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi', 'dimanche');

                for($i = 1; $i <=12; $i++) {
                        $ep = $i+$n;
                        while($ep > 12) $ep -= 12;
                        exec("cp -f /var/medias/play/mactinale/mactinale".$ep.".mp3 /var/medias/play/today/mac".$i.".mp3");
                }

                for($h = 9; $h < 21; $h++) {
                        if(file_exists("/var/medias/play/".$j[$n]."/".$h.".mp3")) exec("cp -f /var/medias/play/".$j[$n]."/".$h.".mp3 /var/medias/play/today/".$h."h25.mp3 > /dev/null");
                }

                if(file_exists("/var/medias/play/".$j[$n]."/prime.mp3")) exec("cp -f /var/medias/play/".$j[$n]."/prime.mp3 /var/medias/play/today/prime.mp3 > /dev/null");

                exit(0);
?>
