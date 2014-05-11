<?php
		if (!@socket_connect(@socket_create(AF_INET, SOCK_STREAM, SOL_TCP), "localhost", 1234)) {
			exec("/sbin/start-stop-daemon --start --pidfile /var/run/podradio.pid --chuid podradio:podradio --exec /usr/bin/liquidsoap -- -d /etc/podradio/podradio.liq", $retour);
			foreach($retour as $line) {
				echo $line;
			}
			$retour = "";
//			exec("/sbin/start-stop-daemon --start --pidfile /var/run/podradio-streams.pid --chuid liquidsoap:liquidsoap --exec /usr/bin/liquidsoap -- -d /etc/podradio/podradio-streams.liq", $retour);	
//			echo var_dump($retour);	
			echo "Serveur liquidsoap relancé\n";
		}
?>
