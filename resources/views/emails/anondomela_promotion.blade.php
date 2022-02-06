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

<div>প্রিয় উদ্যোক্তা ,</div>
<br>
আনন্দমেলার একজন ভেন্ডর হিসেবে আপনাকে স্বাগতম।
<br><br>
নিম্নে একশপ ডেলিভারি রেজিস্ট্রেশন লিংক সহ আনন্দমেলার ইউজার আইডি এবং পাসওয়ার্ড দেয়া হলো।
<br><br>
আনন্দমেলা লিংক  : https://anondomela.shop/my-account/
<br><br>
<span style="font-weight:bold; color:blue">ইউজার আইডি  : {{$userId}}</span>
<br>

<span style="font-weight:bold; color:blue">পাসওয়ার্ড : {{$passWord}}</span>
<br><br>

একশপ ডেলিভারি রেজিস্ট্রেশন লিংক : https://ekshopdelivery.com/register

<br><br>

আনন্দমেলা ভিডিও লিংক  : https://www.youtube.com/watch?fbclid=IwAR2XVAFzlsWK2yQHRngU6blzGE-gDqSE4JLZuEC_FJ-ASjkDgQQx2L_VGuM&v=s-erQiecn70&feature=youtu.be&ab_channel=ekshop

<br><br>
ধন্যবাদ।
<br>

একশপ - আনন্দমেলা

</body>
</html>
