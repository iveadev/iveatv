<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>IVEA</title>
     <script src="jquery.min.js"></script>
     <style>
        html{
            background-color: black;
        }
        .container {
            display: block;
            margin-left: auto;
            margin-right: auto;
            width: 99%;
        }
        .img {
            display: block;
            margin-left: auto;
            margin-right: auto;
            max-height: 99vh;
        }
        video {
        width:100%;
        height:auto;
        max-height:100%
        }

        .video-content {
        height:100vh;
        }
     </style>
</head>
<body>
    <div class="container" class="container">
        @if ($banner['event']['file']['type'] == 'IMAGEN')
            <img src="{{$banner['event']['file']['url']}}" class="img">
        @endif

        @if ($banner['event']['file']['type'] == 'VIDEO')
            <div class="video-content">
                <video id="videoplayer" autoplay muted controls  />
                <source src="{{route('streaming',$banner['event']['file']['id'])}}" type="video/mp4">
            </div>
        @endif
    </div>
</body>
    <script>

        const waitTime = {{$banner['duration']}} *1000;
        console.log(waitTime)
        setTimeout(() => {
            console.log('change')
            window.location.href = "{{route('banner.old', ['id' => $next ?? ''])}}"
        }, waitTime);
    </script>
</html>