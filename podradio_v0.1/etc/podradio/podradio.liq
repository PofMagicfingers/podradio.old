#!/usr/bin/liquidsoap
set("log.file.path","/tmp/podradio.log")
set("log.file.append",true)
set("init.daemon.pidfile.path", "/tmp/podradio.pid")
set("server.telnet",true)
set("harbor.bind_addr","0.0.0.0")
set("harbor.port",8005)
set("harbor.password","swag")
#set("frame.video.channels",1)
#set("frame.video.width",320)
#set("frame.video.height",480)
#set("frame.video.fps",10)
#output.icecast.theora(host="localhost",port=8000,password="swag",name="podradio.fr",url="http://www.podradio.fr/",description="Le m�dia nouvelle g�n�ration",genre="Technologie",mount="video",fallback([playlist(reload=2600, "/var/medias/play/videos/"), add([radio, video.image(width=240,height=240,"/etc/#podradio/podtv.ppm")])]))

start=single("/var/Jamendo/start.mp3")
radio=start;

def transition(previous,next)
  add([next,fade.final(duration=2.,previous)])
end

def live_start() =
	system("touch /tmp/in_live")
end

def live_stop() =
	system("rm /tmp/in_live")
end

def speech() =
	rewrite_metadata(
   [("artist",'$(if $(display_artist),"$(display_artist) - podradio","$(artist) - podradio")'),
    ("comment",""),
    ("title", '$(if $(display_title),"$(display_title)","$(title)")'),
    ("album", '$(if $(display_album),"$(display_album)","$(album)")')],radio)
    system("echo >> /dev/null")
end



jingles=playlist(reload=3600,"/var/Jamendo/jingles/global")
jingles_nuit=playlist(reload=3600,"/var/Jamendo/jingles/nuit")
jingles_jour=playlist(reload=3600,"/var/Jamendo/jingles/jour")

playlist_day=random(weights=[1,2,3],[delay(1.,jingles), delay(1.,jingles_jour), playlist(reload=86400,"/var/Jamendo/jour")])
playlist_night=random(weights=[1,2,3],[delay(1.,jingles), delay(1.,jingles_nuit), mksafe(playlist(reload=86400,"/var/Jamendo/nuit"))])
live = input.harbor(id="live", "live")
speech = input.harbor(id="speech", "speech")

#news =  fallback([
#				  delay(1000.,
#				  		fallback([
#				  			single("/var/medias/play/last_news.mp3"),
#				  			delay(1000.,
#				  				single("/var/Jamendo/pas_news.mp3"))
#				  		])
#				  ),
#				  delay(1000.,
#				  		fallback([
#				  			switch([
#				  				({00h00-14h00}, single("/var/medias/play/last_meteo.mp3")),
#				  				({14h00-00h00}, single("/var/medias/play/last_meteo_jplus1.mp3"))
#				  			]),
#				  			delay(1000.,
#				  				single("/var/Jamendo/pas_meteo.mp3")
#				  			)
#				  		])),
#				  playlist_day])

news = fallback([delay(1800., playlist(reload=86500, "/var/medias/news/playing")), playlist_day]);

mactinale = switch(track_sensitive=true, [
					({6h00-6h10}, fallback([delay(1000., single("/var/medias/play/today/mac1.mp3")), playlist_day])),
					({6h10-6h15}, playlist_day),

					({6h15-6h25}, fallback([delay(1000., single("/var/medias/play/today/mac2.mp3")), playlist_day])),
					({6h25-6h30}, playlist_day),

					({6h30-6h40}, fallback([delay(1000., single("/var/medias/play/today/mac3.mp3")), playlist_day])),
					({6h40-6h45}, playlist_day),

					({6h45-6h55}, fallback([delay(1000., single("/var/medias/play/today/mac4.mp3")), playlist_day])),
					({6h55-7h00}, playlist_day),

					({7h00-7h10}, fallback([delay(1000., single("/var/medias/play/today/mac5.mp3")), playlist_day])),
					({7h10-7h15}, playlist_day),

					({7h15-7h25}, fallback([delay(1000., single("/var/medias/play/today/mac6.mp3")), playlist_day])),
					({7h25-7h30}, playlist_day),

					({7h30-7h40}, fallback([delay(1000., single("/var/medias/play/today/mac7.mp3")), playlist_day])),
					({7h40-7h45}, playlist_day),

					({7h45-7h55}, fallback([delay(1000., single("/var/medias/play/today/mac8.mp3")), playlist_day])),
					({7h55-8h00}, playlist_day),

					({8h00-8h10}, fallback([delay(1000., single("/var/medias/play/today/mac9.mp3")), playlist_day])),
					({8h10-8h15}, playlist_day),

					({8h15-8h25}, fallback([delay(1000., single("/var/medias/play/today/mac10.mp3")), playlist_day])),
					({8h25-8h30}, playlist_day),

					({8h30-8h40}, fallback([delay(1000., single("/var/medias/play/today/mac11.mp3")), playlist_day])),
					({8h40-8h45}, playlist_day),

					({8h45-8h55}, fallback([delay(1000., single("/var/medias/play/today/mac12.mp3")), playlist_day])),
					({8h55-9h00}, playlist_day),

					])

mactinale = switch([({1w-6w}, mactinale),
					({6w-7w}, switch([
							  ( {6h00-6h15}, news ),
							  ( {6h15-7h00}, playlist_day ),
							  ( {7h00-7h15}, news ),
							  ( {7h15-8h00}, playlist_day ),
							  ( {8h00-8h15}, news ),
							  ( {8h15-9h00}, playlist_day)]))])

partavrac = playlist(reload=3600, "/var/medias/partenaires/vrac")

prog_09h25 = fallback([delay(3000.,fallback([delay(3000., single("/var/medias/play/today/09.mp3")), partavrac])) , playlist_day])
prog_10h25 = fallback([delay(3000.,fallback([delay(3000., single("/var/medias/play/today/10.mp3")), partavrac])) , playlist_day])
prog_11h25 = fallback([delay(3000.,fallback([delay(3000., single("/var/medias/play/today/11.mp3")), partavrac])) , playlist_day])
prog_12h25 = fallback([delay(3000.,fallback([delay(3000., single("/var/medias/play/today/12.mp3")), partavrac])) , playlist_day])
prog_13h25 = fallback([delay(3000.,fallback([delay(3000., single("/var/medias/play/today/13.mp3")), partavrac])) , playlist_day])
prog_14h25 = fallback([delay(3000.,fallback([delay(3000., single("/var/medias/play/today/14.mp3")), partavrac])) , playlist_day])
prog_15h25 = fallback([delay(3000.,fallback([delay(3000., single("/var/medias/play/today/15.mp3")), partavrac])) , playlist_day])
prog_16h25 = fallback([delay(3000.,fallback([delay(3000., single("/var/medias/play/today/16.mp3")), partavrac])) , playlist_day])
prog_17h25 = fallback([delay(3000.,fallback([delay(3000., single("/var/medias/play/today/17.mp3")), partavrac])) , playlist_day])
prog_18h25 = fallback([delay(3000.,fallback([delay(3000., single("/var/medias/play/today/18.mp3")), partavrac])) , playlist_day])
prog_19h25 = fallback([delay(3000.,fallback([delay(3000., single("/var/medias/play/today/19.mp3")), partavrac])) , playlist_day])
prog_20h25 = fallback([delay(3000.,fallback([delay(3000., single("/var/medias/play/today/20.mp3")), partavrac])) , playlist_day])
prog_prime = fallback([delay(3000.,fallback([delay(3000., single("/var/medias/play/today/21.mp3")), delay(3000., playlist(reload=3600,"/var/medias/play/prime/")), partavrac])) , playlist_day])
prog_soir  = fallback([delay(3000.,fallback([delay(3000., playlist(reload=3600,"/var/medias/play/today/22.mp3")), partavrac])) , playlist_day])

day=switch(track_sensitive=true,
	[ ( {6h00-9h00}, mksafe(mactinale)),
	  ( {9h00-9h15}, news),
	  ( {9h15-9h25}, playlist_day),
	  ( {9h25-10h00}, prog_09h25),
	  ( {10h00-10h15}, news),
	  ( {10h15-10h25}, playlist_day),
	  ( {10h25-11h00}, prog_10h25),
	  ( {11h00-11h15}, news),
	  ( {11h15-11h25}, playlist_day),
	  ( {11h25-12h00}, prog_11h25),
	  ( {12h00-12h15}, news),
	  ( {12h15-12h25}, playlist_day),
	  ( {12h25-13h00}, prog_12h25),
	  ( {13h00-13h15}, news),
	  ( {13h15-13h25}, playlist_day),
	  ( {13h25-14h00}, prog_13h25),
	  ( {14h00-14h15}, news),
	  ( {14h15-14h25}, playlist_day),
	  ( {14h25-15h00}, prog_14h25),
	  ( {15h00-15h15}, news),
	  ( {15h15-15h25}, playlist_day),
	  ( {15h25-16h00}, prog_15h25),
	  ( {16h00-16h15}, news),
	  ( {16h15-16h25}, playlist_day),
	  ( {16h25-17h00}, prog_16h25),
	  ( {17h00-17h15}, news),
	  ( {17h15-17h25}, playlist_day),
	  ( {17h25-18h00}, prog_17h25),
	  ( {18h00-18h15}, news),
	  ( {18h15-18h25}, playlist_day),
	  ( {18h25-19h00}, prog_18h25),
	  ( {19h00-19h15}, news),
	  ( {19h15-19h25}, playlist_day),
	  ( {19h25-20h00}, prog_19h25),
	  ( {21h00-22h30}, prog_prime),
	  ( {22h30-23h00}, prog_soir),
	])


radio=fallback([request.queue(id="request"), switch([({6h-23h}, day), ({23h-6h}, playlist_night)]), playlist_day, mksafe(start)])
radio=smooth_add(normal=radio, special=speech)

radio=fallback(track_sensitive=false,transitions=[transition, transition],[live, radio])
#radio=smart_crossfade(fade_out=0.2,fade_in=0.2,radio)
#radio = nrj(radio)

output.icecast.mp3(host="localhost",port=8000,password="swag",name="podradio.fr",url="http://www.podradio.fr/",description="La radio des podcasts",genre="Technologie",mount="adsl",bitrate=128, radio)
output.icecast.mp3(host="localhost",port=8000,password="swag",name="podradio.fr",url="http://www.podradio.fr/",description="La radio des podcasts",genre="Technologie",mount="3g",bitrate=64,stereo=false,radio)
