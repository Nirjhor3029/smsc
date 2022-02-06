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

    public function deliverVfDlr()
    {

        $tMsgId = [

            "408f8cf169bc479fbd92417c023af9bd",

        ];
        $i = 0;

        foreach ($tMsgId as $t) {
            $data = [
                'tMsgId' => $t,
                'status' => 'Delivered'
            ];
            General::sendDlrToBeelink($data);
            echo $i++ . ' <br>';
        }
    }

    public function testCase()
    {

        for ($i = 0; $i < 100; $i++) {
            $url = 'http://localhost/other/msg/public/api/bulk';
            $client = new Client(['headers' => ['content-type' => 'application/json', 'Accept' => 'applicatipon/json', 'charset' => 'utf-8']]);
            $response = $client->request('POST', $url, ['form_params' => [
                'passkey' => 'Open1234',
                'number' => '01612363773',
                'smsText' => rand(4000, 10000) . 'আপনার কোড',
                'tMsgId' => rand(1000000, 10125244124),
            ]]);
            echo $response->getBody();

        }
    }

    public function generateUrl(Request $request)
    {
        $passkey = $request->passkey;
        $number = $request->number;
        $smstext = $request->smsText;
        $tMsgId = $request->tMsgId;

        $url = url("api/bulk/" . rand(100, 10000));

        $client = new Client(['headers' => ['content-type' => 'application/json', 'Accept' => 'applicatipon/json', 'charset' => 'utf-8']]);
        $response = $client->request('POST', $url, ['form_params' => [
            'passkey' => $passkey,
            'number' => $number,
            'smsText' => $smstext,
            'tMsgId' => $tMsgId,
        ]]);
        return $response->getBody();
    }

    public function nodesSmsRefactored(Request $request)
    {

        $check = General::checkValidation($request);
        if (!is_null($check)) {
            return $check;
        }

        try {
            $mobile = $request->number;
            $tMsgId = $request->tMsgId;

            $smsText = (int)filter_var($request->smsText, FILTER_SANITIZE_NUMBER_INT);
            if (empty($smsText)) {
                return 'No OTP code found';
            }

            // $search_array= array("1", "2", "3", "4", "5", "6", "7", "8", "9", "0");
            // $replace_array= array("১", "২", "৩", "৪", "৫", "৬", "৭", "৮", "৯", "০");
            // $smsText = str_replace($search_array, $replace_array, $smsText);


            // $smsText = wordwrap($smsText , $space , '`' , true );
            // $smsText = number_format($smsText , 0, '.', ' ');


            $t = rand(1, 2);

            if ($t == 1) {
                $smsText = $smsText . ' আপনার কো ড ';
            } else if ($t == 2) {
                $smsText = $smsText . ' y0ur C0de ';
            } else {
                $smsText = $smsText . ' use c00d ';
            }

            $ra = substr(str_shuffle(str_repeat($x = '0123456789abcdefghijklmnopqrstuvwxyz',
                ceil(10 / strlen($x)))), 1, 10);

            $smsText = (string)$smsText . ' - ' . $ra;

            // $smsText = $smsText . ' আপনার কোড - EKSHOP';

            //  $smsText = 'আপনার কোড '.$smsText . ' - একশপ';
            //  $smsText = 'কোড '.$smsText . ' - একশপ';

            $mobile = General::formatMobileNumber($mobile);
            $checkError = General::mobileValidaton($mobile);

            if (!is_null($checkError)) {
                return $checkError;
            }

            $data = [
                'mobile' => $mobile,
                'smsText' => $smsText,
                'client' => General::getClient()['nodes'],
                'tMsgId' => $tMsgId
            ];

            return $this->sendSms($data);

        } catch (Exception $e) {
            echo $e->getMessage();
        }
        return 0;
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

            $dataArr = [
                'mobile' => $mobile,
                'smsText' => $smsText,
                'client' => General::getClient()['ekshop'],
            ];

            // return $this->sendSms($dataArr);
            // return $this->gpSendSms($dataArr);
             return $this->robiSendSms($dataArr);
            // return $this->teletalkSendSms($dataArr);
//            return $this->teletalkSendSmsA2i($dataArr);



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

            // return $this->sendSms($dataArr);
            //  return $this->gpSendSms($dataArr);
             return $this->robiSendSms($dataArr);
//            return $this->teletalkSendSmsA2i($dataArr);


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

            $dataArr = [
                'mobile' => $mobile,
                'smsText' => $smsText,
                'client' => General::getClient()['ekshop-smsc'],
            ];

            // return $this->sendSms($dataArr);
            //  return $this->gpSendSms($dataArr);
             return $this->robiSendSms($dataArr);
//            return $this->teletalkSendSmsA2i($dataArr);

        } catch (Exception $e) {
            echo $e->getMessage();
        }
        return 0;
    }

    public function dlrReport(Request $request)
    {
        $DELIVERED_DATA = null;
        if (isset($request->DELIVERED_DATA) && !empty($request->DELIVERED_DATA)) {
            $DELIVERED_DATA = Carbon::parse($request->DELIVERED_DATA)->addMinutes(30);
        }
        $MSG_STATUS = $request->MSG_STATUS;
        $CLIENT_GUID = $request->CLIENT_GUID;

        Log::channel('vflog')->info('receive_dlr',
            ['client_guid' => $CLIENT_GUID,
                'details' =>
                    [
                        'msg_status' => $MSG_STATUS,
                        'delivered_data' => $DELIVERED_DATA,
                        'number' => $request->TO
                    ]]);;

        $data = General::dlrValidation($CLIENT_GUID);
        if (!is_object($data)) {
            return $data;
        }

        Dlr::where('sms_id', $data->id)
            ->update([
                'delivered_data' => $DELIVERED_DATA,
                'msg_status' => $MSG_STATUS
            ]);

        SmsDetails::where('id', $data->id)
            ->update([
                'is_dlr_received' => '1',
                'msg_guid' => $CLIENT_GUID
            ]);

        $passData = [
            'tMsgId' => $data['tMsgId'],
            'status' => $MSG_STATUS,
            'delivered_time' => $DELIVERED_DATA
        ];

        $res = General::sendDlrToBeelink($passData);

        if (isset($res['status_code']) && $res['status_code'] == 200) {
            return $res;
        }
        return [
            'stauts_code' => '500',
            'message' => 'Something went wrong'
        ];
    }

    public function dlrReportFromClient(Request $request)
    {
        return $request;
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
                ->where('msg_client',$request->msg_client)
                ->limit($limit)
                ->get();
        }

        if (!is_null($number)) {
            return Dlr::join('sms_details', 'sms_details.id', 'dlrs.sms_id')
                ->select($select)
                ->where('dlrs.to', $number)
                ->orderBy('sms_details.id', 'desc')
                ->limit($limit)
                ->get();
        }

        return Dlr::join('sms_details', 'sms_details.id', 'dlrs.sms_id')
            ->select($select)
            ->orderBy('sms_details.id', 'desc')
            ->limit($limit)
            ->get();


    }

    public function sendSms($dataArr)
    {

        $opCode = substr($dataArr['mobile'], 0, 3);


        if ($opCode == '015') {
            return $this->teletalkSendSms($dataArr);

        } else if ($opCode == '011' || $opCode == '011') {

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
        } else if ($opCode == '013' || $opCode == '017') {


            //Sending GP sms through ROBI for Nodes
            if ($dataArr['client'] == 'nodes') {
                return 'Currently unavailable!';
            }

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

        } else if ($opCode == '011' || $opCode == '011') {
            $provider = 'Banglalink';
            $status = SmsProviders::blSms($dataArr);
            $status = (int)strpbrk($status, "0123456789");
            $guid = substr(str_shuffle(str_repeat($x = '0123456789ABCXYL',
                ceil(20 / strlen($x)))), 1, 20);

            if ($status != 0) {

                $t_arr = [
                    'guid' => $guid,
                    'provider' => $provider
                ];

                $var = array_merge($dataArr, $t_arr);
                return $this->storeSuccessSms($var);

            } else {

                $error_code = '990';

                $t_arr = [
                    'guid' => $guid,
                    'provider' => $provider,
                    'error_code' => (int)$error_code
                ];
                $var = array_merge($dataArr, $t_arr);
                return $this->storeUnsentSms($var);

            }
        } else if ($opCode == '014' || $opCode == '019') {

            //Sending GP sms through ROBI for Nodes
            if ($dataArr['client'] == 'nodes') {
                return 'Currently unavailable!';
            }

            $status = SmsProviders::vfSms($dataArr);

            if (!is_object($status)) {
                return 'error on VF';
            }

            $tmpClone = clone $status;
            Log::channel('vflog')->info('sendVfSms',
                ['Log' => ltrim($tmpClone, 'GuzzleHttp\\Psr7\\Stream":')]);

            $data = explode("&", $status);
            $check = strpos($status, 'errorcode=0');

            if ($check) {
                $guid = ltrim($data[0], 'guid=');
                $t_arr = [
                    'guid' => $guid,
                    'provider' => 'ValueFirst'
                ];
                $var = array_merge($dataArr, $t_arr);
                return $this->storeSuccessSms($var);
            } else {
                $guid = ltrim($data[0], 'guid=');
                $error_code = !empty(ltrim($data[1], 'errorcode=')) ? ltrim($data[1], 'errorcode=') : 404;

                $t_arr = [
                    'guid' => $guid,
                    'provider' => 'ValueFirst',
                    'error_code' => $error_code
                ];
                $var = array_merge($dataArr, $t_arr);
                return $this->storeUnsentSms($var);
            }
        } else {

            //Block Nodes SMS
            if ($dataArr['client'] == 'nodes') {
                return 'Currently unavailable!';
            }

            $status = SmsProviders::aglWebSms($dataArr);
            $check = strpos($status, 'Message has been sent');
            $rand_guid = substr(str_shuffle(str_repeat($x = '0123456789abcdefghijklmnopqrstuvwxyz',
                ceil(20 / strlen($x)))), 1, 20);
            $guid = $rand_guid;
            $provider = 'aglWeb';

            if ($check) {
                $t_arr = [
                    'guid' => $guid,
                    'provider' => $provider
                ];
                $var = array_merge($dataArr, $t_arr);
                return $this->storeSuccessSms($var);
            } else {

                $error_code = 900;
                $t_arr = [
                    'guid' => $guid,
                    'provider' => $provider,
                    'error_code' => $error_code
                ];
                $var = array_merge($dataArr, $t_arr);
                return $this->storeUnsentSms($var);
            }
        }
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
                    }, function ($exception) {
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
                    }, function ($exception) {
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
                    }, function ($exception) {
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


    public function requestCustomDlrForVf()
    {

        // $checkforDlr = SmsDetails::where('msg_provider', 'ValueFirst')
        //     ->where('is_dlr_received', 0);


        $checkforDlr = SmsDetails::join('dlrs', 'dlrs.sms_id', 'sms_details.id')
            ->where('sms_details.msg_provider', 'ValueFirst')
            ->where('sms_details.is_dlr_received', 0)
            ->where('sms_details.created_at', '<', Carbon::now()->subMinutes(10)->toDateTimeString())
            ->whereNull('dlrs.delivered_data');


        // return $checkforDlr->count();

        if ($checkforDlr->count() > 0) {


            $dataArr = $checkforDlr->select('sms_details.id', 'sms_details.msg_guid', 'sms_details.tMsgId', 'sms_details.msg_provider', 'sms_details.msg_client')->get();

            $i = 0;

            // $j =0;

            foreach ($dataArr as $data) {

                $url = 'http://www.myvaluefirst.com/smpp/status?username=A2itranshttp&password=j3@W8mt@Lz&guid=' . $data->msg_guid;

                $client = new Client();
                $res = $client->getAsync($url)->then(
                    function ($response) {
                        return $response->getBody();
                    }, function ($exception) {
                    return $exception->getMessage();
                }
                );

                $ress = $res->wait();
                $responsee = explode("&", $ress);


                $deliveryStatus = 'Delivered';
                $is_dlr_received = '1';

                $ddata['t_msg_id'] = $data->tMsgId;
                $ddata['dlr_status'] = $deliveryStatus;

                DlrToClient::create($ddata);

                $idArr[$i] = $data->id;
                $i++;

                /*
                if (isset($responsee[2]) && $responsee[2] == 'errorcode=8448') {
                    $deliveryStatus = 'Delivered';
                    $is_dlr_received = '1';

                    $ddata['t_msg_id'] = $data->tMsgId;
                    $ddata['dlr_status'] = $deliveryStatus;

                    DlrToClient::create($ddata);

                    $idArr[$i] = $data->id;
                    $i++;
                }
                else{
                    Dlr::where('sms_id', $data->id)
                    ->update([
                        'delivered_data' => Carbon::now()->toDateTimeString()
                    ]);
                    echo 'No dlr for'. $data->id . ' <br>';
                }

                */


                // if($j==100){
                //      break;
                // }

                // $j++;

            }

            if (!empty($idArr)) {
                Dlr::whereIn('sms_id', $idArr)
                    ->update([
                        'msg_status' => 'Delivered',
                        'delivered_data' => Carbon::now()->toDateTimeString()

                    ]);
                SmsDetails::whereIn('id', $idArr)
                    ->update([
                        'is_dlr_received' => '1',
                    ]);

                echo $i . ' Updated';

            }

            echo 'End';

        } else {
            return 'No data';
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
                    }, function ($exception) {
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
                        }, function ($exception) {
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
                        }, function ($exception) {
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

    public function sendDlrToBeelink()
    {
        $checkforDlr = DlrToClient::where('is_dlr_sent', 0)->whereNotNull('t_msg_id');

        if ($checkforDlr->count() > 0) {
            $msg_guid = $checkforDlr->select('t_msg_id', 'dlr_status')->get();

            foreach ($msg_guid as $data) {

                if (is_null($data->t_msg_id)) {
                    continue;
                }
                $ddata['status'] = (isset($data['dlr_status'])) ? $data['dlr_status'] : 'Waiting';
                $ddata['tMsgId'] = (isset($data['t_msg_id'])) ? $data['t_msg_id'] : NULL;

                $d = General::sendDlrToBeelink($ddata);
                if (isset($d['message']) && $d['message'] == 'ok') {
                    DlrToClient::where('t_msg_id', $data->t_msg_id)->update([
                        'is_dlr_sent' => 1,
                        'client_response' => $d['message']
                    ]);
                }
            }
        }
    }

    public function hourlyDlrCheck()
    {
        $numberArr = [
            'airtel' => '01612363773',
            'robi' => '01821778364',
            'gp' => '01313347699',
            'bl' => '01920080000',
            'teletalk' => '01555111555'
        ];


        $msg = '999' . date('Hi') . ' আপনার কোড';

        foreach ($numberArr as $number) {

            $url = url("api/bulk/" . rand(100, 10000));

            $client = new Client(['headers' => ['content-type' => 'application/json', 'Accept' => 'applicatipon/json', 'charset' => 'utf-8']]);
            $response = $client->request('POST', $url, ['form_params' => [
                'passkey' => 'Open1234',
                'number' => $number,
                'smsText' => $msg
            ]]);
            $response->getBody();

        }

        sleep(150);

        $i = 0;

        foreach ($numberArr as $number) {
            $number = '88' . $number;
            $data = Dlr::where('to', $number)->orderBy('id', 'desc')->first();
            if (!isset($data)) {
                continue;
            }
            $dlrStatusArr[$number] = [
                'dlr' => $data->msg_status
            ];
        }

        (isset($dlrStatusArr['8801612363773'])) ? $airtelDlr = $dlrStatusArr['8801612363773']['dlr'] : $airtelDlr = 'Not available';
        (isset($dlrStatusArr['8801821778364'])) ? $robiDlr = $dlrStatusArr['8801821778364']['dlr'] : $robiDlr = 'Not available';
        (isset($dlrStatusArr['8801313347699'])) ? $gpDlr = $dlrStatusArr['8801313347699']['dlr'] : $gpDlr = 'Not available';
        (isset($dlrStatusArr['8801920080000'])) ? $blDlr = $dlrStatusArr['8801920080000']['dlr'] : $blDlr = 'Not available';
        (isset($dlrStatusArr['8801555111555'])) ? $ttDlr = $dlrStatusArr['8801555111555']['dlr'] : $ttDlr = 'Not available';;

        $msg = date('h:i:s a') . ' | GP: ' . $gpDlr . ', Airtel: ' . $airtelDlr . ', Robi: ' . $robiDlr . ', BL: ' . $blDlr . ', Teletalk: ' . $ttDlr;

        $url = url('/ekshop') . '?passkey=Open1234&number=8801920080000&smsText=' . $msg;

        $client = new Client();
        $promise1 = $client->getAsync($url)->then(
            function ($response) {
                $response->getBody();
            }, function ($exception) {
            $exception->getMessage();
        }
        );
        $promise1->wait();

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

    public function teletalkSendSms($dataArr){
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

        $client_name_arr = [

            'Buy Now',
            'ekShop Delivery',
            'ekShop Test',
            'bpo delivery'

        ];


        if(!in_array($request->client, $client_name_arr)){

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

            $dataArr = [
                'mobile' => $mobile,
                'smsText' => $smsText,
                'client' => $client_name,
            ];

            // return $this->sendSms($dataArr);
            // return $this->gpSendSms($dataArr);
             return $this->robiSendSms($dataArr);
            // return $this->teletalkSendSms($dataArr);
//            return $this->teletalkSendSmsA2i($dataArr);



        } catch (Exception $e) {
            echo $e->getMessage();
        }
        return 0;
    }
}
