<?php

/*
Run schedule: 11AM daily
*/

//WE Delivery daily report
file_get_contents('https://wedelivery.net/daily-summery-on-sms?auth_key=Check&action=send');

//WE Delivery support report
file_get_contents('https://support.wedelivery.net/reports?auth_key=Check&action=send');

?>