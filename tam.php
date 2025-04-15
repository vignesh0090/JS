<?php
// Proxy part
if (isset($_GET['proxy'])) {
    $quality = isset($_GET['quality']) ? $_GET['quality'] : 'medium';
    
    // Define quality streams
    $streams = [
        'low' => "https://game.denver1769.fun/Js25/index.php?id=TAM&m=M1&q=audio_59236_tam=59200-video=254000.m3u8",
        'medium' => "https://game.denver1769.fun/Js25/index.php?id=TAM&m=M1&id=TAM&q=audio_75235_tam=75200-video=438000.m3u8",
        'high' => "https://game.denver1769.fun/Js25/index.php?id=TAM&m=M1&id=TAM&q=audio_75235_tam=75200-video=764800.m3u8",
        'hd' => "https://game.denver1769.fun/Js25/index.php?id=TAM&m=M1&id=TAM&q=audio_75235_tam=75200-video=2093200.m3u8",
    ];
    
    // If the quality parameter specifies 'master', return a master playlist
    if ($quality === 'master') {
        header("Content-Type: application/vnd.apple.mpegurl");
        header("Access-Control-Allow-Origin: *");
        echo "#EXTM3U\n";
        echo "#EXT-X-VERSION:3\n";
        
        // 240p
        echo "#EXT-X-STREAM-INF:BANDWIDTH=366000,AVERAGE-BANDWIDTH=332000,CODECS=\"mp4a.40.2,avc1.4D4015\",RESOLUTION=426x240,FRAME-RATE=25\n";
        echo "JST.php?proxy=1&quality=240p\n";
        
        // 360p
        echo "#EXT-X-STREAM-INF:BANDWIDTH=444000,AVERAGE-BANDWIDTH=404000,CODECS=\"mp4a.40.2,avc1.4D401E\",RESOLUTION=640x360,FRAME-RATE=25\n";
        echo "JST.php?proxy=1&quality=360p\n";
        
        // 480p
        echo "#EXT-X-STREAM-INF:BANDWIDTH=599000,AVERAGE-BANDWIDTH=544000,CODECS=\"mp4a.40.2,avc1.4D401F\",RESOLUTION=854x480,FRAME-RATE=25\n";
        echo "JST.php?proxy=1&quality=low\n";
        
        // 720p
        echo "#EXT-X-STREAM-INF:BANDWIDTH=980000,AVERAGE-BANDWIDTH=891000,CODECS=\"mp4a.40.2,avc1.640028\",RESOLUTION=1280x720,FRAME-RATE=25\n";
        echo "JST.php?proxy=1&quality=high\n";
        
        // 1080p
        echo "#EXT-X-STREAM-INF:BANDWIDTH=2529000,AVERAGE-BANDWIDTH=2299000,CODECS=\"mp4a.40.2,avc1.640028\",RESOLUTION=1920x1080,FRAME-RATE=25\n";
        echo "JST.php?proxy=1&quality=hd\n";
        
        exit;
    }
    
    // Map quality parameters to streams
    $qualityMap = [
        '240p' => "https://game.denver1769.fun/Js25/index.php?id=TAM&m=M1&q=audio_59236_tam=59200-video=254000.m3u8",
        '360p' => "https://game.denver1769.fun/Js25/index.php?id=TAM&m=M1&id=TAM&q=audio_75235_tam=75200-video=305200.m3u8",
        'low' => $streams['low'],
        'medium' => $streams['medium'],
        'high' => $streams['high'],
        'hd' => $streams['hd']
    ];
    
    // Select the target URL based on quality parameter
    $target = isset($qualityMap[$quality]) ? $qualityMap[$quality] : $streams['medium'];
    
    header("Content-Type: application/vnd.apple.mpegurl");
    header("Access-Control-Allow-Origin: *");
    $opts = [
        "http" => [
            "method" => "GET",
            "header" => "User-Agent: Denver1769\r\n"
        ]
    ];
    $context = stream_context_create($opts);
    echo file_get_contents($target, false, $context);
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>HLS Video Player</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://unpkg.com/video.js/dist/video-js.css" rel="stylesheet" />
    <style>
        html, body {
            margin: 0;
            padding: 0;
            background: black;
            height: 100%;
            width: 100%;
            overflow: hidden;
        }
        #hls-video {
            height: 100vh;
            width: 100vw;
        }
        .quality-selector {
            position: absolute;
            bottom: 70px;
            right: 20px;
            z-index: 1000;
            background: rgba(0,0,0,0.7);
            color: white;
            padding: 5px;
            border-radius: 4px;
            font-family: Arial, sans-serif;
        }
        select {
            background: #333;
            color: white;
            border: 1px solid #666;
            padding: 3px;
            border-radius: 3px;
        }
    </style>
</head>
<body>
    <video id="hls-video" class="video-js vjs-default-skin vjs-big-play-centered" controls autoplay playsinline></video>
    
    <div class="quality-selector">
        <label for="quality">Quality: </label>
        <select id="quality" onchange="changeQuality(this.value)">
            <option value="auto">Auto</option>
            <option value="240p">240p</option>
            <option value="360p">360p</option>
            <option value="low">480p</option>
            <option value="high">720p</option>
            <option value="hd">1080p</option>
        </select>
    </div>

    <script src="https://unpkg.com/video.js/dist/video.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/videojs-contrib-hls@5.15.0/dist/videojs-contrib-hls.min.js"></script>
    <script>
        var player = videojs('hls-video');
        var currentQuality = 'auto';
        
        // Initial setup with master playlist for auto quality
        player.src({
            src: 'JST.php?proxy=1&quality=master',
            type: 'application/x-mpegURL'
        });
        
        player.ready(function() {
            player.play();
        });
        
        function changeQuality(quality) {
            // Save current time to resume playback at the same position
            var currentTime = player.currentTime();
            var isPaused = player.paused();
            
            if (quality === 'auto') {
                player.src({
                    src: 'JST.php?proxy=1&quality=master',
                    type: 'application/x-mpegURL'
                });
            } else {
                player.src({
                    src: 'JST.php?proxy=1&quality=' + quality,
                    type: 'application/x-mpegURL'
                });
            }
            
            player.ready(function() {
                player.currentTime(currentTime);
                if (!isPaused) {
                    player.play();
                }
            });
            
            currentQuality = quality;
        }
        
        // Adjust quality selector position on fullscreen change
        player.on('fullscreenchange', function() {
            var qualitySelector = document.querySelector('.quality-selector');
            if (player.isFullscreen()) {
                qualitySelector.style.bottom = '100px';
            } else {
                qualitySelector.style.bottom = '70px';
            }
        });
    </script>
</body>
</html>