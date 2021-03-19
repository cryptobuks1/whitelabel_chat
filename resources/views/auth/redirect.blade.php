<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Chat</title>
    <script src="{{asset('assets/js/jquery.min.js')}}"></script>
    <style>
        h2 {
            margin-left: auto;
            text-align: center;
            margin-right: auto;
            margin-top: 25%;
        }

        h1 {
            margin-left: auto;
            text-align: center;
            margin-right: auto;
        }
    </style>
</head>

<body>
    <h2>You are being redirected to chat app. Please wait.</h2>
    <br>
    <h1 id="seconds"></h1>
</body>
<script>
    localStorage.setItem('infyChatAppToken', '{{ $access_token }}');
    var timeleft = 5;
    var downloadTimer = setInterval(function(){
        if(timeleft == 0){ 
            clearInterval(downloadTimer); 
            window.location.href = '/conversations';
        }
        document.getElementById("seconds").innerHTML= timeleft; 
        timeleft -= 1; 
        }, 1000);
</script>

</html>