<?php

namespace App\Helpers;

use GuzzleHttp\Client;
use PHPUnit\Framework\MockObject\Stub\ReturnReference;

date_default_timezone_set('Asia/Dhaka');

class SmsProviders
{

    public static function commonSms($mobile, $sms)
    {
        //rokomari sms
        return 'failed';
        $url = 'https://api2.onnorokomsms.com/HttpSendSms.ashx?op=OneToOne&type=TEXT&mobile=' . $mobile . '&smsText=' . $sms . '&username=01612363773&password=asd12300&maskName=&campaignName=';
        $client = new Client([
            'Content-Type' => 'application/json',
            'Host' => 'ekshop.gov.bd',
            'Accept-Charset' => 'utf-8',
            'Date' => date(' Y-m-d H:i:s')
        ]);
        $promise1 = $client->getAsync($url)->then(
            function ($response) {
                return $response->getBody();
            },
            function ($exception) {
                return $exception->getMessage();
            }
        );
        $re = $promise1->wait();
        return $re;
    }

    public static function vfSms($data)
    {
        //        return 'guid=kkbbg130788050b130011c-3g3A2ITRANSHT&errorcode=&seqno=88016123637736';
        $url = 'https://http.myvfirst.com/smpp/sendsms?username=A2itranshttp&password=j3@W8mt@Lz&coding=3&category=bulk&from=eksShop&to=88' . $data['mobile'] . '&text=' . $data['smsText'];

        $client = new Client([
            'Content-Type' => 'application/json',
            'Host' => 'ekshop.gov.bd',
            'Accept-Charset' => 'utf-8',
            'Date' => date(' Y-m-d H:i:s')
        ]);
        $promise1 = $client->getAsync($url)->then(
            function ($response) {
                return $response->getBody();
            },
            function ($exception) {
                return $exception->getMessage();
            }
        );
        return $promise1->wait();
    }

    public static function teletalkSms($data)
    {

        //        return 'teletalk sms';

        $url = 'https://bulksms.teletalk.com.bd/link_sms_send.php?op=SMS&user=ekshop-wom&pass=ekShop@2021&mobile=88' . $data['mobile'] . '&charset=UTF-8&sms=' . urlencode($data['smsText']);

        $client = new Client([
            'Content-Type' => 'application/json',
            'Host' => 'ekshop.gov.bd',
            'Accept-Charset' => 'utf-8',
            'Date' => date(' Y-m-d H:i:s')
        ]);
        $promise1 = $client->getAsync($url)->then(
            function ($response) {
                return $response->getBody();
            },
            function ($exception) {
                return $exception->getMessage();
            }
        );
        return $promise1->wait();
    }


    public static function teletalkSmsA2i($data)
    {

        if (!is_array($data['mobile'])) {
            $data['mobile'] = [$data['mobile']];
        }

        //        dd(gettype($data['mobile']));

        $url = 'http://bulkmsg.teletalk.com.bd/api/sendSMS';
        $client = new Client(['headers' => ['content-type' => 'application/json', 'Accept' => 'applicatipon/json', 'charset' => 'utf-8']]);
        $response = $client->request('POST', $url, ['json' => [
            'auth' => [
                'username' => 'ekshop',
                'password' => 'A2ist2#0155',
                'acode' => 1005110,
            ],
            'smsInfo' => [
                'message' => $data['smsText'],
                'masking' => 8801552146224,
                'msisdn' => $data['mobile']
            ]
        ]]);
        return $response->getBody();
    }


    public static function robiAirtelSms($data)
    {
        //        return 'robi sms';
        $username = 'aspire';
        $password = 'ekShop@2021';

        $url = 'https://api.mobireach.com.bd/SendTextMessage?Username=' . $username . '&Password=' . $password . '&From=Nodes&To=88' . $data['mobile'] . '&Message=' . urlencode($data['smsText']);
        $client = new Client([
            'Content-Type' => 'application/json',
            'Host' => 'ekshop.gov.bd',
            'Accept-Charset' => 'utf-8',
            'Date' => date(' Y-m-d H:i:s')
        ]);
        $promise1 = $client->getAsync($url)->then(
            function ($response) {
                return $response->getBody();
            },
            function ($exception) {
                return $exception->getMessage();
            }
        );
        $response = $promise1->wait();
        return General::xmltoJson($response);
    }


    public static function robiAirtelBulkPromotion($data)
    {

        if (is_array($data['mobile'])) {
            $data['mobile'] = implode(',', $data['mobile']);
        }
        $username = 'aspire';
        $password = 'ekShop@2021';
        $from = 'Nodes';

        $url = 'https://api.mobireach.com.bd/SendTextMultiMessage?Username=' . $username . '&Password=' . $password . '&From=' . $from . '&To=' . $data['mobile'] . '&Message=' . $data['smsText'];

        file_get_contents($url);

        return true;
    }


    public static function gpSms($data)
    {

        $url = 'https://gpcmp.grameenphone.com/ecmapigw/webresources/ecmapigw.v2';
        $client = new Client([
            'Content-Type' => 'application/json',
            'Host' => 'ekshop.gov.bd',
            'Accept-Charset' => 'utf-8',
            'Date' => date(' Y-m-d H:i:s')
        ]);

        $response = $client->post($url, [
            'json' => [
                "username" => "ASPIREAdmin_4065",
                "password" => "Open@1234567890",
                "apicode" => "5",
                "msisdn" => $data['mobile'],
                "countrycode" => "880",
                "cli" => "ekshop",
                "messagetype" => "3",
                "message" => $data['smsText'],
                "messageid" => 0
            ]
        ]);
        return $response->getBody();
    }

    public static function blSms($data)
    {
        //        return 'Success Count : 1 and Fail Count : 0';

        $url = 'https://vas.banglalinkgsm.com/sendSMS/sendSMS';
        $client = new Client([
            'Content-Type' => 'application/json',
            'Host' => 'ekshop.gov.bd',
            'Accept-Charset' => 'utf-8',
            'Date' => date(' Y-m-d H:i:s')
        ]);

        $response = $client->post($url, [
            'form_params' => [
                "userID" => "AspireAPI",
                "passwd" => "Aspire@09876",
                "sender" => 'ekShop',
                "msisdn" => $data['mobile'],
                "message" => $data['smsText']
            ]
        ]);
        return $response->getBody();
    }

    public static function aglWebSms($data)
    {

        $url = 'http://sms.felnadma.com/api/v1/send';
        $client = new Client([
            'Content-Type' => 'application/json',
            'Host' => 'ekshop.gov.bd',
            'Accept-Charset' => 'utf-8',
            'Date' => date(' Y-m-d H:i:s')
        ]);

        $response = $client->post($url, [
            'form_params' => [
                "api_key" => "44516084460354901608446035",
                "senderid" => 'Aspire IT',
                "contacts" => $data['mobile'],
                "msg" => $data['smsText']
            ]
        ]);
        return $response->getBody();
    }
}