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
#set("frame.video.height",240)
#set("frame.video.fps",25)

start=single("/var/Jamendo/start.mp3")
radio=start;

def transition(previous,next)
  add([next,fade.final(duration=2.,previous)])
end

def live_start(value)
        system("touch /tmp/in_live")
end

def live_stop()
        system("rm /tmp/in_live")
end


if test_process("which faad") then
  def faad_p(file)=
    "faad -o /dev/stdout #{quote(file)} \
          2>/dev/null"
  end
  add_decoder("FAAD",faad_p)
  def faad_meta(~format,file)
    if format != "FAAD" then
      []
    else
      ret = get_process_lines("faad -i \
                   #{quote(file)} 2>&1")
      def get_meta(l,s)=
        ret = string.extract(
              pattern="^(\w+):\s(.+)$",s)
        if list.length(ret) > 0 then
          list.append([(ret["1"],ret["2"])],l)
        else
          l
        end
      end
      list.fold(get_meta,[],ret)
    end
  end
  add_metadata_resolver("FAAD",faad_meta)
else
  log(level=3,"faad binary not found: \
       faad decoder disabled.")
end

jingles=playlist(reload=3600,"/var/Jamendo/jingles/global")
jingles_nuit=playlist(reload=3600,"/var/Jamendo/jingles/nuit")
jingles_jour=playlist(reload=3600,"/var/Jamendo/jingles/jour")

playlist_day=random(weights=[1,2,3],[delay(1.,jingles), delay(1.,jingles_jour), playlist(reload=86400,"/var/Jamendo/jour")])
playlist_night=random(weights=[1,2,3],[delay(1.,jingles), delay(1.,jingles_nuit), mksafe(playlist(reload=86400,"/var/Jamendo/nuit"))])
playlists=switch([({6h-23h}, playlist_day), ({23h-6h}, playlist_night)])

live = input.harbor(id="live", "live", on_connect=live_start, on_disconnect=live_stop)
speech = input.harbor(id="speech", "speech")

radio=fallback([request.equeue(id="scheduler"), playlists, mksafe(start)])
#music_radio=mksafe(nrj(fallback([playlists, mksafe(start)])))
radio=fallback(track_sensitive=false,transitions=[transition, transition],[live, radio])

radio = nrj(radio)

radio=smooth_add(normal=radio, special=speech)

radio=mksafe(radio)

output.icecast.mp3(host="localhost",port=8000,password="swag",name="podradio.fr",url="http://www.podradio.fr/",description="La radio des podcasts",genre="Technologie",mount="adsl",bitrate=128, radio)
output.icecast.mp3(host="localhost",port=8000,password="swag",name="podradio.fr",url="http://www.podradio.fr/",description="La radio des podcasts",genre="Technologie",mount="3g",bitrate=64,stereo=false,radio)
#output.icecast.mp3(host="localhost",port=8000,password="swag",name="podradio.fr",url="http://www.podradio.fr/",description="La radio des musiques",genre="Musique",mount="music",bitrate=128, music_radio)
#output.icecast.mp3(host="localhost",port=8000,password="swag",name="podradio.fr",url="http://www.podradio.fr/",description="La radio des musiques",genre="Musique",mount="music_3g",bitrate=128, music_radio)

#tv = add([radio,video.image(width=40,height=40,x=10,y=10,"/etc/podradio/tv.ppm")])
#output.icecast.theora(host="localhost",port=8000,password="swag",name="podradio.fr",url="http://www.podradio.fr/",description="La radio des podcasts",genre="Technologie",mount="tv",tv)
