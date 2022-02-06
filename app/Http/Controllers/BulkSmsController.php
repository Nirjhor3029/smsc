<?php

namespace App\Http\Controllers;

use App\DlrToClient;
use App\Helpers\General;
use App\Helpers\SmsProviders;
use App\SmsDetails;
use App\UnsentSmsDetail;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use mysql_xdevapi\Exception;
use Async;
use App\Dlr;
use Illuminate\Support\Facades\Log;

date_default_timezone_set('Asia/Dhaka');


class BulkSmsController extends Controller
{


    public function msgLengthUpdate()
    {

        $allData =  SmsDetails::get();

        foreach ($allData as $data) {

            $msg_char_count = (int) mb_strlen($data->msg_body);
            $msg_count = (int) General::getNumberOfSMSsegments($data->msg_body);
            $data->msg_char_count =  $msg_char_count;
            $data->msg_count =  $msg_count;
            $data->save();
        }
    }




    public function ekShopSms(Request $request)
    {
        $check = General::checkValidation($request, true);
        if (!is_null($check)) {
            return $check;
        }

        try {
            $mobile = $request->number;
            $smsText = $request->smsText;

            $mobile = General::formatMobileNumber($mobile);
            $checkError = General::mobileValidaton($mobile);

            if (!is_null($checkError)) {
                return $checkError;
            }

            $msg_char_count = (int) mb_strlen($smsText);
            $msg_count = (int) General::getNumberOfSMSsegments($smsText);

            if ($msg_count > 5) {
                return response()->json([
                    'reason' => 'SMS text is too long',
                    'Max accepted' => 800,
                    'SMS length' =>  $msg_char_count,
                ], 203);
            }

            $dataArr = [
                'mobile' => $mobile,
                'smsText' => $smsText,
                'client' => General::getClient()['ekshop'],
                'msg_char_count' => $msg_char_count,
                'msg_count' => $msg_count
            ];

            return $this->sendSms($dataArr);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        return 0;
    }

    public function ekShopPromotionSms(Request $request)
    {
        $check = General::checkValidation($request, true);
        if (!is_null($check)) {
            return $check;
        }

        try {
            $mobile = $request->number;
            $smsText = $request->smsText;

            $mobile = General::formatMobileNumber($mobile);
            $checkError = General::mobileValidaton($mobile);

            if (!is_null($checkError)) {
                return $checkError;
            }

            $dataArr = [
                'mobile' => $mobile,
                'smsText' => $smsText,
                'client' => 'ekShop Promotion',
            ];

            return $this->sendSms($dataArr);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        return 0;
    }

    public function ekShopSmsSmsc(Request $request)
    {
        $check = General::checkValidation($request, true);
        if (!is_null($check)) {
            return $check;
        }

        try {
            $mobile = $request->number;
            $smsText = $request->smsText;

            $mobile = General::formatMobileNumber($mobile);
            $checkError = General::mobileValidaton($mobile);

            if (!is_null($checkError)) {
                return $checkError;
            }
            
            $msg_char_count = (int) mb_strlen($smsText);
            $msg_count = (int) General::getNumberOfSMSsegments($smsText);

            if ($msg_count > 5) {
                return response()->json([
                    'reason' => 'SMS text is too long',
                    'Max accepted' => 800,
                    'SMS length' =>  $msg_char_count,
                ], 203);
            }

            $dataArr = [
                'mobile' => $mobile,
                'smsText' => $smsText,
                'client' => 'ekshop Delivery',
                'msg_char_count' => $msg_char_count,
                'msg_count' => $msg_count
            ];

            return $this->sendSms($dataArr);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        return 0;
    }

    public function dlrReportAll(Request $request, $number = null)
    {
        $limit = 20;
        $passkey = '';
        $select = [
            'sms_details.id',
            'dlrs.to',
            'dlrs.msg_status',
            'dlrs.delivered_data',
            'sms_details.msg_body',
            'sms_details.msg_char_count',
            'sms_details.msg_count',
            'sms_details.msg_client',
            'sms_details.msg_provider',
            'sms_details.telecom_operator'

        ];

        if (!isset($request->key)) {
            return 'Authentication required';
        }

        if (isset($request->limit)) {
            $limit = $request->limit;
        }

        if (isset($request->msg_client)) {
            return Dlr::join('sms_details', 'sms_details.id', 'dlrs.sms_id')
                ->select($select)
                ->orderBy('sms_details.id', 'desc')
                ->where('msg_client', $request->msg_client)
                ->limit($limit)
                ->get();
        }

        if (!is_null($number)) {
            $datas = Dlr::join('sms_details', 'sms_details.id', 'dlrs.sms_id')
                ->select($select)
                ->where('dlrs.to', $number)
                ->orderBy('sms_details.id', 'desc')
                ->limit($limit)
                ->get();
            // foreach ($datas as $data) {
            //     $search_array = array("1", "2", "3", "4", "5", "6", "7", "8", "9", "0");
            //     $replace_array = array("x", "x", "x", "x", "x", "x", "x", "x", "x", "x");
            //     $data->msg_body = str_replace($search_array, $replace_array, $data->msg_body);
            // }
            return $datas;
        }

        $datas = Dlr::join('sms_details', 'sms_details.id', 'dlrs.sms_id')
            ->select($select)
            ->orderBy('sms_details.id', 'desc')
            ->limit($limit)
            ->get();
        foreach ($datas as $data) {
            $search_array = array("1", "2", "3", "4", "5", "6", "7", "8", "9", "0");
            $replace_array = array("x", "x", "x", "x", "x", "x", "x", "x", "x", "x");
            $data->msg_body = str_replace($search_array, $replace_array, $data->msg_body);
        }
        return $datas;
    }

    public function storeSuccessSms($var)
    {

        $data = General::setVfCreateValues($var);
        $result = SmsDetails::create($data)->id;
        $dlr['to'] = '88' . $var['mobile'];
        $dlr['sms_id'] = $result;
        Dlr::create($dlr)->id;

        //Temporary commented to set dlr to Delivered for all VF

        // $dlr = $this->requestCustomDlr($data['msg_guid']);

        if ($data['msg_provider'] != 'ValueFirst') {
            $dlr = $this->requestCustomDlr($data['msg_guid']);
        }

        $storeStatus = [
            'code' => 200,
            'msg' => 'Successful',
            'data' => [
                'guid' => $data['msg_guid']
            ],
            'tMsgId' => $data['tMsgId'],
            'dlr_status' => (isset($dlr['status'])) ? $dlr['status'] : NULL,
        ];

        return $storeStatus;
    }

    public function storeUnsentSms($var)
    {
        $data = General::setVfCreateValues($var);
        UnsentSmsDetail::create($data)->id;

        $storeStatus = [
            'code' => 200,
            'msg' => 'Unsuccessful',
            'data' => [
                'guid' => $data['msg_guid'],
                'error_code' => $data['error_code']
            ],
            'tMsgId' => $data['tMsgId'],
        ];
        return $storeStatus;
    }

    public function sendTeletalk(Request $request)
    {
        return SmsProviders::teletalkSms($request);
    }

    public function requestCustomDlr($guid)
    {
        $checkforDlr = SmsDetails::where('msg_guid', $guid)
            ->where('is_dlr_received', 0);

        if ($checkforDlr->count() > 0) {

            $data = $checkforDlr->select('id', 'msg_guid', 'tMsgId', 'msg_provider', 'msg_client')->first();

            if ($data->msg_provider == 'Teletalk') {

                sleep(2);

                $url = 'https://bulksms.teletalk.com.bd/link_sms_send.php?op=STATUS&user=Aspire&pass=ekShop@2021&sms_id=' . $data->msg_guid;
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

                $dlrStatus = '1';

                if (strpos($response, 'SUCCESSFULLY SENT TO')) {
                    $deliveryStatus = 'Delivered';
                } else {
                    // $deliveryStatus = 'Failed';
                    $deliveryStatus = NULL;
                    $dlrStatus = '0';
                }
                Dlr::where('sms_id', $data->id)
                    ->update([
                        'msg_status' => $deliveryStatus,
                        'delivered_data' => Carbon::now()->toDateTimeString()
                    ]);

                SmsDetails::where('id', $data->id)
                    ->update([
                        'is_dlr_received' => $dlrStatus,
                        'msg_guid' => $data->msg_guid
                    ]);

                $passData = [
                    'tMsgId' => $data->tMsgId,
                    'status' => $deliveryStatus
                ];

                $ddata['t_msg_id'] = $data->tMsgId;
                $ddata['dlr_status'] = $deliveryStatus;
                DlrToClient::create($ddata);

                return $passData;
            } elseif ($data->msg_provider == 'Teletalk-a2i') {

                $dlrStatus = '1';
                $deliveryStatus = 'Sent';

                Dlr::where('sms_id', $data->id)
                    ->update([
                        'msg_status' => $deliveryStatus,
                        'delivered_data' => Carbon::now()->toDateTimeString()
                    ]);

                SmsDetails::where('id', $data->id)
                    ->update([
                        'is_dlr_received' => $dlrStatus,
                        'msg_guid' => $data->msg_guid
                    ]);

                $passData = [
                    'tMsgId' => $data->tMsgId,
                    'status' => $deliveryStatus
                ];

                return $passData;
            } elseif ($data->msg_provider == 'Robi/Airtel') {

                $url = 'https://api.mobireach.com.bd/GetMessageStatus?Username=aspire&Password=ekShop@2021&MessageId=' . $data->msg_guid;

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

                $d = General::xmltoJson($response);

                $var = $d['ServiceClass'];

                if ($var['ErrorCode'] == '0') {
                    $deliveryStatus = 'Delivered';
                } else {
                    $deliveryStatus = 'Failed';
                }
                Dlr::where('sms_id', $data->id)
                    ->update([
                        'msg_status' => $deliveryStatus,
                        'delivered_data' => Carbon::now()->toDateTimeString()
                    ]);

                SmsDetails::where('id', $data->id)
                    ->update([
                        'is_dlr_received' => '1',
                        'msg_guid' => $data->msg_guid
                    ]);

                $passData = [
                    'tMsgId' => $data->tMsgId,
                    'status' => $deliveryStatus
                ];

                $ddata['t_msg_id'] = $data->tMsgId;
                $ddata['dlr_status'] = $deliveryStatus;
                DlrToClient::create($ddata);

                return $passData;
            } elseif ($data->msg_provider == 'Banglalink' || $data->msg_provider == 'aglWeb') {

                $deliveryStatus = 'Delivered';
                $dlrStatus = '1';
                Dlr::where('sms_id', $data->id)
                    ->update([
                        'msg_status' => $deliveryStatus,
                        'delivered_data' => Carbon::now()->toDateTimeString()
                    ]);

                SmsDetails::where('id', $data->id)
                    ->update([
                        'is_dlr_received' => $dlrStatus,
                        'msg_guid' => $data->msg_guid
                    ]);

                $passData = [
                    'tMsgId' => $data->tMsgId,
                    'status' => $deliveryStatus
                ];

                $ddata['t_msg_id'] = $data->tMsgId;
                $ddata['dlr_status'] = $deliveryStatus;
                DlrToClient::create($ddata);
                return $passData;
            }
        }
    }

    public function requestCustomDlrForTeletalk()
    {
        $checkforDlr = SmsDetails::where('msg_provider', 'Teletalk')
            ->where('is_dlr_received', 0);

        if ($checkforDlr->count() > 0) {

            $dataArr = $checkforDlr->select('id', 'msg_guid', 'tMsgId', 'msg_provider', 'msg_client')->get();
            $i = 0;

            foreach ($dataArr as $data) {
                $url = 'https://bulksms.teletalk.com.bd/link_sms_send.php?op=STATUS&user=Aspire&pass=ekShop@2021&sms_id=' . $data->msg_guid;

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

                if (strpos($response, 'SUCCESSFULLY SENT TO')) {
                    $deliveryStatus = 'Delivered';
                    $idArr[$i] = $data->id;
                    $i++;
                } else {
                    $deliveryStatus = 'Failed';
                    Dlr::where('sms_id', $data->id)
                        ->update([
                            'msg_status' => $deliveryStatus
                        ]);

                    SmsDetails::where('id', $data->id)
                        ->update([
                            'is_dlr_received' => '1',
                            'msg_guid' => $data->msg_guid
                        ]);
                }

                $ddata['t_msg_id'] = $data->tMsgId;
                $ddata['dlr_status'] = $deliveryStatus;
                DlrToClient::create($ddata);
            }

            Dlr::whereIn('sms_id', $idArr)
                ->update([
                    'msg_status' => 'Delivered',
                    'delivered_data' => Carbon::now()->toDateTimeString()

                ]);

            SmsDetails::whereIn('id', $idArr)
                ->update([
                    'is_dlr_received' => '1',
                ]);
        }
    }

    public function requestCustomDlrRobi()
    {
        $checkforDlr = SmsDetails::where('msg_provider', 'Robi/Airtel')
            ->where('is_dlr_received', 0);

        if ($checkforDlr->count() > 0) {

            $dataArr = $checkforDlr->select('id', 'msg_guid', 'tMsgId', 'msg_provider', 'msg_client')->get();

            foreach ($dataArr as $data) {
                $url = 'https://api.mobireach.com.bd/GetMessageStatus?Username=aspire&Password=ekShop@2021&MessageId=' . $data->msg_guid;

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

                $d = General::xmltoJson($response);

                $var = $d['ServiceClass'];

                if ($var['ErrorCode'] == '0') {
                    $deliveryStatus = 'Delivered';
                } else {
                    $deliveryStatus = 'Failed';
                }
                Dlr::where('sms_id', $data->id)
                    ->update([
                        'msg_status' => $deliveryStatus
                    ]);

                SmsDetails::where('id', $data->id)
                    ->update([
                        'is_dlr_received' => '1',
                        'msg_guid' => $data->msg_guid
                    ]);

                $ddata['t_msg_id'] = $data->tMsgId;
                $ddata['dlr_status'] = $deliveryStatus;
                DlrToClient::create($ddata);
            }
        }
    }

    public function requestCustomDlrGp()
    {
        $checkforDlr = SmsDetails::where('msg_provider', 'Grameenphone')
            ->where('is_dlr_received', 0);

        if ($checkforDlr->count() > 0) {

            $i = 0;

            $dataArr = $checkforDlr->select('id', 'receiver_number', 'msg_guid', 'tMsgId', 'msg_provider', 'msg_client')->get();

            foreach ($dataArr as $data) {

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
                        "apicode" => "4",
                        "msisdn" => ltrim($data->receiver_number, '88'),
                        "countrycode" => "0",
                        "cli" => "ekshop",
                        "messagetype" => "3",
                        "message" => "ABC",
                        "messageid" => $data->msg_guid
                    ]
                ]);

                $response = $response->getBody();
                $response = json_decode($response);

                if ($response->statusCode == 200) {

                    $arr = explode('#', $response->message);

                    if ($arr[0] == 'Delivered') {

                        $idArr[$i] = $data->id;

                        $ddata['t_msg_id'] = $data->tMsgId;
                        $ddata['dlr_status'] = 'Delivered';
                        DlrToClient::create($ddata);

                        $i++;
                    } elseif ($arr[0] == 'UnDelivered') {

                        Dlr::where('sms_id', $data->id)
                            ->update([
                                'msg_status' => 'Failed',
                                'delivered_data' => Carbon::now()->toDateTimeString()
                            ]);

                        SmsDetails::where('id', $data->id)
                            ->update([
                                'is_dlr_received' => '1'
                            ]);
                    }
                }
            }

            if (!empty($idArr)) {
                Dlr::whereIn('sms_id', $idArr)
                    ->update([
                        'msg_status' => 'Delivered',
                        'delivered_data' => Carbon::now()->toDateTimeString()
                    ]);

                SmsDetails::whereIn('id', $idArr)
                    ->update([
                        'is_dlr_received' => '1'
                    ]);
            }
        }
    }

    public function requestDlr()
    {
        $checkforDlr = SmsDetails::where('msg_provider', 'Teletalk')
            ->orWhere('msg_provider', 'Robi/Airtel')
            ->orWhere('msg_provider', 'ValueFirst')
            ->where('is_dlr_received', 0);

        if ($checkforDlr->count() > 0) {

            $msg_guid = $checkforDlr->select('id', 'msg_guid', 'tMsgId', 'msg_provider', 'msg_client')->get();


            foreach ($msg_guid as $data) {

                if ($data['msg_provider'] == 'Teletalk') {
                    $url = 'https://bulksms.teletalk.com.bd/link_sms_send.php?op=STATUS&user=Aspire&pass=ekShop@2021&sms_id=' . $data->msg_guid;
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

                    if (strpos($response, 'SUCCESSFULLY SENT TO')) {
                        $deliveryStatus = 'Delivered';
                    } else {
                        $deliveryStatus = 'Failed';
                    }
                    Dlr::where('sms_id', $data->id)
                        ->update([
                            'msg_status' => $deliveryStatus,
                            'delivered_data' => Carbon::now()->toDateTimeString()
                        ]);

                    SmsDetails::where('id', $data->id)
                        ->update([
                            'is_dlr_received' => '1',
                            'msg_guid' => $data->msg_guid
                        ]);

                    $passData = [
                        'tMsgId' => $data['tMsgId'],
                        'status' => $deliveryStatus
                    ];

                    if ($data->msg_client == 'nodes') {
                        General::sendDlrToBeelink($passData);
                    }
                } elseif ($data['msg_provider'] == 'Robi/Airtel') {

                    $url = 'https://api.mobireach.com.bd/GetMessageStatus?Username=aspire&Password=ekShop@2021&MessageId=' . $data->msg_guid;

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

                    $d = General::xmltoJson($response);

                    $var = $d['ServiceClass'];

                    if ($var['ErrorCode'] == '0') {
                        $deliveryStatus = 'Delivered';
                    } else {
                        $deliveryStatus = 'Failed';
                    }
                    Dlr::where('sms_id', $data->id)
                        ->update([
                            'msg_status' => $deliveryStatus,
                            'delivered_data' => Carbon::now()->toDateTimeString()
                        ]);

                    SmsDetails::where('id', $data->id)
                        ->update([
                            'is_dlr_received' => '1',
                            'msg_guid' => $data->msg_guid
                        ]);

                    $passData = [
                        'tMsgId' => $data['tMsgId'],
                        'status' => $deliveryStatus
                    ];

                    if ($data->msg_client == 'nodes') {
                        General::sendDlrToBeelink($passData);
                    }
                }
            }
        }
    }

    public function robiSendSms($dataArr)
    {
        $provider = 'Robi/Airtel';
        $status = SmsProviders::robiAirtelSms($dataArr);

        $status = (object)$status['ServiceClass'];

        if ($status->StatusText == 'success') {
            $guid = $status->MessageId;

            $t_arr = [
                'guid' => $guid,
                'provider' => $provider
            ];

            $var = array_merge($dataArr, $t_arr);
            return $this->storeSuccessSms($var);
        } else {
            $guid = $status->MessageId;
            $error_code = $status->ErrorCode;

            $t_arr = [
                'guid' => $guid,
                'provider' => $provider,
                'error_code' => (int)$error_code
            ];
            $var = array_merge($dataArr, $t_arr);
            return $this->storeUnsentSms($var);
        }
    }

    public function gpSendSms($dataArr)
    {

        $provider = 'Grameenphone';
        $status = SmsProviders::gpSms($dataArr);
        $status = json_decode($status);

        if ($status->statusCode == 200) {
            $guid = $status->message;

            $t_arr = [
                'guid' => $guid,
                'provider' => $provider
            ];

            $var = array_merge($dataArr, $t_arr);
            return $this->storeSuccessSms($var);
        } else {
            $guid = $status->message;
            $error_code = $status->statusCode;

            $t_arr = [
                'guid' => $guid,
                'provider' => $provider,
                'error_code' => (int)$error_code
            ];
            $var = array_merge($dataArr, $t_arr);
            return $this->storeUnsentSms($var);
        }
    }

    public function teletalkSendSms($dataArr)
    {
        $provider = 'Teletalk';
        $status = SmsProviders::teletalkSms($dataArr);
        $data = explode(",", $status);

        if (ltrim($data[0], '<reply>') == 'SUCCESS') {
            $guid = ltrim($data[1], 'ID=');
            $t_arr = [
                'guid' => $guid,
                'provider' => $provider
            ];

            $var = array_merge($dataArr, $t_arr);
            return $this->storeSuccessSms($var);
        } else {

            $guid = ltrim($data[1], 'ID=');
            $error_code = 99;

            $t_arr = [
                'guid' => $guid,
                'provider' => $provider,
                'error_code' => $error_code
            ];
            $var = array_merge($dataArr, $t_arr);
            return $this->storeUnsentSms($var);
        }
    }

    public function teletalkSendSmsA2i($dataArr)
    {
        $provider = 'Teletalk-a2i';
        $status = SmsProviders::teletalkSmsA2i($dataArr);

        $status = json_decode($status, TRUE);

        if ($status['error_code'] == '0') {
            $guid = $status['smsInfo'][0]['smsID'];
            $t_arr = [
                'guid' => $guid,
                'provider' => $provider
            ];

            $var = array_merge($dataArr, $t_arr);
            return $this->storeSuccessSms($var);
        } else {

            $guid = $status['smsInfo'][0]['smsID'];
            $error_code = $status['error_code'];

            $t_arr = [
                'guid' => $guid,
                'provider' => $provider,
                'error_code' => $error_code
            ];
            $var = array_merge($dataArr, $t_arr);
            return $this->storeUnsentSms($var);
        }
    }

    public function externalApi(Request $request)
    {


        $client_name_arr = General::getClientName();
        
        if (!in_array($request->client, $client_name_arr)) {

            return "Please provide valid client name";
        }

        $check = General::checkValidation($request, true);
        if (!is_null($check)) {
            return $check;
        }

        try {
            $mobile = $request->number;
            $smsText = $request->smsText;
            $client_name = $request->client;

            $mobile = General::formatMobileNumber($mobile);
            $checkError = General::mobileValidaton($mobile);

            if (!is_null($checkError)) {
                return $checkError;
            }

            $msg_char_count = (int) mb_strlen($smsText);
            $msg_count = (int) General::getNumberOfSMSsegments($smsText);

            if ($msg_count > 5) {
                return response()->json([
                    'reason' => 'SMS text is too long',
                    'Max accepted' => 800,
                    'SMS length' =>  $msg_char_count,
                ], 203);
            }

            $dataArr = [
                'mobile' => $mobile,
                'smsText' => $smsText,
                'client' => $client_name,
                'msg_char_count' => $msg_char_count,
                'msg_count' => $msg_count
            ];


            return $this->sendSms($dataArr);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        return 0;
    }

    public function sendSms($dataArr)
    {

        $priority_strings = [

            'ওটিপি', 'otp', 'OTP', 'code', 'ইউজার আইডি', 'পাসওয়ার্ড'
        ];


        foreach ($priority_strings as $string) {

            if (strpos($dataArr['smsText'], $string)) {

                return $this->robiSendSms($dataArr);
            }
        }



        // Check time

        /*
        
        $now = Carbon::now();
        $start = Carbon::createFromTimeString('10:00');
        $end = Carbon::createFromTimeString('23:59');

        if ($now->between($start, $end)) {

            return $this->robiSendSms($dataArr);
            
        }
        
*/



        //        return $this->gpSendSms($dataArr);
        //        return $this->robiSendSms($dataArr);
        //        return $this->teletalkSendSms($dataArr);
        return $this->teletalkSendSmsA2i($dataArr);
    }
}
