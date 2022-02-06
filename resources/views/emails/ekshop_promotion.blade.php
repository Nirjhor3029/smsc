@php
    namespace App\Http\Controllers;

    use App\Helpers\General;
    use App\Mail\BeelinkReportMail;
    use Illuminate\Http\Request;

@endphp
    <!DOCTYPE html>
<html>
<head>
</head>
<body>

<br><br>

<p> {{$textBody}}</p>


<footer>
<img src="{{asset('/images/logo2.png')}}" alt="" width="240" height="45">
<img src="{{asset('/images/logo.png')}}" width="75" height="40">
</footer>
</body>
</html>
