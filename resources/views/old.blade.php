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
     </style>
</head>
<body>
    <div class="container" class="container">
        @if ($banner['file']['type'] == 'IMAGEN')
            <img src="{{$banner['file']['url']}}" class="img">
        @endif

        @if ($banner['file']['type'] == 'VIDEO')
            <video autoplay src="{{$banner['file']['url']}}" />
        @endif
    </div>
</body>
    <script>

        const waitTime = {{$banner['duration']}} *1000;
        setTimeout(() => {
            console.log('change')
            window.location.href = "{{route('banner.old', ['id' => $next ?? ''])}}"
        }, waitTime);
    </script>
</html>