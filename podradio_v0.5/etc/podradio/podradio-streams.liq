#!/usr/bin/liquidsoap
set("log.file.path","/tmp/podradio-streams.log")
set("log.file.append",true)
set("init.daemon.pidfile.path", "/tmp/podradio-streams.pid")

#final=fallback(track_sensitive=true,[once(single("/var/podradio/blank.mp3")),mksafe(single("http://localhost:8000/feedback"))])
final=fallback(track_sensitive=true,[once(noise(duration=8.0)),input.http("http://localhost:8000/feedback")])

final=mksafe(final)

output.icecast.mp3(host="localhost",port=8000,password="swag",name="podradio.fr",url="http://www.podradio.fr/",description="La radio des podcasts",genre="Technologie",mount="adsl",bitrate=128, final)
#output.icecast.mp3(host="localhost",port=8000,password="swag",name="podradio.fr",url="http://www.podradio.fr/",description="La radio des podcasts",genre="Technologie",mount="adsl+6",bitrate=128, final)
output.icecast.mp3(host="localhost",port=8000,password="swag",name="podradio.fr",url="http://www.podradio.fr/",description="La radio des podcasts",genre="Technologie",mount="3g",bitrate=64,stereo=false,final)
#output.icecast.mp3(host="localhost",port=8000,password="swag",name="podradio.fr",url="http://www.podradio.fr/",description="La radio des podcasts",genre="Technologie",mount="3g+6",bitrate=64,stereo=false,final)

