<!DOCTYPE html>
<html>
<head>
    <title>ekShop | Promotional SMS, Email sendt</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/js/bootstrap.min.js"></script>
    <script language="JavaScript" src="https://code.jquery.com/jquery-1.12.4.js" type="text/javascript"></script>
    <!------ Include the above in your HEAD tag ---------->

    <link rel="stylesheet" type="text/css"
          href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"/>


</head>
<body>
<div class="container">
    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-8">
            <div class="pt-5">
                <br><br><br><br>
                <form action="{{route('promotion.send')}}" method="post">
                    <div class="form-group">
                        <label for="exampleInputEmail1">Mobile number </label>
                        <input minlength="11" name = "number" type="number" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Number format, ex: 01612363773, 8801612363773">
                    </div>
                    <div class="form-group">
                        <label for="exampleInputPassword1">Email Address</label>
                        <input name = "email" type="email" class="form-control" aria-describedby="emailHelp" id="exampleInputPassword1" placeholder="Email address">

                    </div>
                    <div>
                        <label for="exampleFormControlTextarea1">Message</label>
                        <textarea name="smsText" class="form-control" id="exampleFormControlTextarea1" rows="4"></textarea>
                    </div>
                    <br>

                    <input type="hidden" name="_token" value="{{ csrf_token() }}" />

                    <button type="submit" class="btn btn-primary">Send</button>
                </form>
            </div>

        </div>
    </div>
</div>

</body>
</html>
