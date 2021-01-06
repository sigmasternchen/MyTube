#!/bin/sh

dir="content/$1/"
mkdir "$dir"
mkdir "$dir/360p/"
mkdir "$dir/480p/"
mkdir "$dir/720p/"
mkdir "$dir/1080p/"

#ffmpeg -hide_banner -y -i "landingzone/$1.vid" \
#  -vf scale=w=640:h=360:force_original_aspect_ratio=decrease -c:a aac -ar 48000 -c:v h264 -profile:v main -crf 20 \
#    -sc_threshold 0 -g 48 -keyint_min 48 -hls_time 4 -hls_playlist_type vod  -b:v 800k -maxrate 856k -bufsize 1200k \
#    -b:a 96k -hls_segment_filename "$dir/360p/%03d.ts" "$dir/360p.m3u8" \
#  -vf scale=w=842:h=480:force_original_aspect_ratio=decrease -c:a aac -ar 48000 -c:v h264 -profile:v main -crf 20 \
#    -sc_threshold 0 -g 48 -keyint_min 48 -hls_time 4 -hls_playlist_type vod -b:v 1400k -maxrate 1498k -bufsize 2100k \
#    -b:a 128k -hls_segment_filename "$dir/480p/%03d.ts" "$dir/480p.m3u8" \
#  -vf scale=w=1280:h=720:force_original_aspect_ratio=decrease -c:a aac -ar 48000 -c:v h264 -profile:v main -crf 20 \
#    -sc_threshold 0 -g 48 -keyint_min 48 -hls_time 4 -hls_playlist_type vod -b:v 2800k -maxrate 2996k -bufsize 4200k \
#    -b:a 128k -hls_segment_filename "$dir/720p/%03d.ts" "$dir/720p.m3u8" \
#  -vf scale=w=1920:h=1080:force_original_aspect_ratio=decrease -c:a aac -ar 48000 -c:v h264 -profile:v main -crf 20 \
#    -sc_threshold 0 -g 48 -keyint_min 48 -hls_time 4 -hls_playlist_type vod -b:v 5000k -maxrate 5350k -bufsize 7500k \
#    -b:a 192k -hls_segment_filename "$dir/1080p/%03d.ts" "$dir/1080p.m3u8"

ffmpeg -hide_banner -y -i "landingzone/$1.vid" \
  -vf scale=w='(oh/a/2)*2':h=360 -c:a aac -ar 48000 -c:v h264 -profile:v main -crf 20 \
    -sc_threshold 0 -g 48 -keyint_min 48 -hls_time 4 -hls_playlist_type vod  -b:v 800k -maxrate 856k -bufsize 1200k \
    -b:a 96k -hls_segment_filename "$dir/360p/360p-%03d.ts" "$dir/360p/playlist.m3u8" \
  -vf scale=w='(oh/a/2)*2':h=480 -c:a aac -ar 48000 -c:v h264 -profile:v main -crf 20 \
    -sc_threshold 0 -g 48 -keyint_min 48 -hls_time 4 -hls_playlist_type vod -b:v 1400k -maxrate 1498k -bufsize 2100k \
    -b:a 128k -hls_segment_filename "$dir/480p/480p-%03d.ts" "$dir/480p/playlist.m3u8" \
  -vf scale=w='(oh/a/2)*2':h=720 -c:a aac -ar 48000 -c:v h264 -profile:v main -crf 20 \
    -sc_threshold 0 -g 48 -keyint_min 48 -hls_time 4 -hls_playlist_type vod -b:v 2800k -maxrate 2996k -bufsize 4200k \
    -b:a 128k -hls_segment_filename "$dir/720p/720p-%03d.ts" "$dir/720p/playlist.m3u8" \
  -vf scale=w='(oh/a/2)*2':h=1080 -c:a aac -ar 48000 -c:v h264 -profile:v main -crf 20 \
    -sc_threshold 0 -g 48 -keyint_min 48 -hls_time 4 -hls_playlist_type vod -b:v 5000k -maxrate 5350k -bufsize 7500k \
    -b:a 192k -hls_segment_filename "$dir/1080p/1080p-%03d.ts" "$dir/1080p/playlist.m3u8"

cat > "$dir/playlist.m3u8" <<EOF
#EXTM3U
#EXT-X-VERSION:3
#EXT-X-STREAM-INF:BANDWIDTH=800000,RESOLUTION=640x360
360p.m3u8
#EXT-X-STREAM-INF:BANDWIDTH=1400000,RESOLUTION=842x480
480p.m3u8
#EXT-X-STREAM-INF:BANDWIDTH=2800000,RESOLUTION=1280x720
720p.m3u8
#EXT-X-STREAM-INF:BANDWIDTH=5000000,RESOLUTION=1920x1080
1080p.m3u8
EOF