<?php

namespace App\Helpers;

use App\Dlr;
use App\SmsDetails;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Carbon\Carbon;
use DB;
use phpDocumentor\Reflection\Types\This;

class General
{

    public static function passKey()
    {
        return
            [
                'client 1' => 'Open1234', //For client 1
                'client 2' => 'jfbwajJGUHYFG237yr3wkjBUYG', //For Client 2
                'common' => '09978bg45SD3Sa9', //Common fo all
                'ekShop' => '09978bg45SD3SWQ', //For ekShop-Code
                'ekshop-delivery' => '099218bg45SD3SWQ12', //For ekshopDelivery
                'ed_dev' => '099218bg45SD3SWQsjs2', //For ekshopDelivery
                'anondomela_dev' => '099218bg45SD3SWQsjs2scs',
            ];
    }
    
    static function getClientName()
    {
        return $client_name_arr = [
                                    'Buy Now',
                                    'ekShop Delivery',
                                    'ekShop Test',
                                    'bpo delivery',
                                    'bloodman',
                                    'Test-ing',
                                    'joyeeta',
                                    'ekShop',
                                    'ekshop',
                                    'bsciclive',
                                    'moc_grs',
                                    'ed_dev',
                                    'anondomela_dev'
                                    // 'digitalhaat'
                                ];
    }

    public static function checkValidation($request, $noTemplateCheck = null)
    {
        //Check if passkey valid
        if (!in_array($request->passkey, General::passKey())) {
            return 'Invalid passkey';
        }
        // Check if number & sms text is valid
        if ($request->has('number') && empty($request->number)) {
            return 'No receiver number';
        }

        if ($request->has('smsText') && empty($request->smsText)) {
            return 'Text empty';
        }

        if ($request->has('tMsgId') && empty($request->tMsgId)) {
            return 'No tMsgId';
        }
        if ($noTemplateCheck == null && !strpos($request->smsText, 'আপনার কোড')) {
            return 'Template not matched';
        }
    }

    public static function formatMobileNumber($mobile)
    {
        $opCode = substr($mobile, 0, 5);
        if (substr($opCode, 0, 2) == 88) {
            $mobile = $request['number'] = ltrim($mobile, '88');
        } else if (substr($opCode, 0, 3) == +88) {
            $mobile = $request['number'] = ltrim($mobile, '+88');
        } else if (substr($opCode, 0, 2) == 00) {
            $mobile = $request['number'] = ltrim($mobile, '00');
        }
        if (substr($mobile, 0, 1) != 0) {
            $mobile = $request['number'] = '0' . $mobile;
        }


        $search_array = array("১", "২", "৩", "৪", "৫", "৬", "৭", "৮", "৯", "০");
        $replace_array = array("1", "2", "3", "4", "5", "6", "7", "8", "9", "0");
        $mobile = str_replace($search_array, $replace_array, $mobile);
        $mobile = str_replace("-", "", $mobile);
        $mobile = str_replace(" ", "", $mobile);
        $mobile = str_replace(".", "", $mobile);

        return $mobile;
    }

    public static function setVfCreateValues($var)
    {
        $operator = General::setOperatorName($var);

        $data['receiver_number'] = '88' . $var['mobile'];
        $data['msg_guid'] = $var['guid'];
        $data['msg_body'] = $var['smsText'];
        $data['msg_client'] = $var['client'];
        $data['msg_provider'] = $var['provider'];
        $data['error_code'] = (isset($var['error_code']) ? $var['error_code'] : NULL);
        $data['tMsgId'] = (isset($var['tMsgId']) && !is_null($var['tMsgId'])) ? $var['tMsgId'] : NULL;
        $data['telecom_operator'] = $operator;
        $data['msg_char_count'] = $var['msg_char_count'];
        $data['msg_count'] = $var['msg_count'];

        return $data;
    }

    public static function setOperatorName($var)
    {

        $operator = '';
        $opCode = substr($var['mobile'], 0, 3);
        if ($opCode == '015') {
            $operator = 'Teletalk';
        } elseif ($opCode == '016') {
            $operator = 'Airtel';
        } elseif ($opCode == '013' || $opCode == '017') {
            $operator = 'Grameenphone';
        } elseif ($opCode == '018') {
            $operator = 'Robi';
        } elseif ($opCode == '016') {
            $operator = 'Airtel';
        } elseif ($opCode == '014' || $opCode == '019') {
            $operator = 'Banglalink';
        }
        return $operator;
    }

    public static function dlrValidation($CLIENT_GUID)
    {

        $data = SmsDetails::where('msg_guid', $CLIENT_GUID)->orderBy('id', 'desc')->first();

        if (is_null($data)) {

            $response = [
                'status_code' => '202',
                'message' => 'No data found'
            ];
            return $response;
        }
        if ($data->is_dlr_received != 0) {

            $response = [
                'status_code' => '202',
                'message' => 'Already updated'
            ];
            return $response;
        }
        return $data;
    }

    public static function sendDlrToBeelink($data)
    {


        $url = 'http://161.117.59.25:6666/receive_report/BD_Nodes?tMsgId=' . $data['tMsgId'] . '&status=' . $data['status'];

        //        $url = 'http://smsproxy.test/api/bulk/dlr/client?tMsgId=' . $data['tMsgId'] . '&status=' . $data['status'];

        // $url = 'http://smsc.ekshop.world/api/bulk/dlr/client?tMsgId='.$data['tMsgId'].'&status='.$data['status'];

        $client = new Client([
            'Content-Type' => 'text/html',
            'Host' => 'ekshop.gov.bd',
            'Accept-Charset' => 'utf-8',
            'Date' => date(' Y-m-d H:i:s')
        ]);
        $promise1 = $client->getAsync($url)->then(
            function ($response) {
                return
                    [
                        'status_code' => $response->getStatusCode(),
                        'message' => (string)$response->getBody()
                    ];
            },
            function ($exception) {
                return
                    $exception->getMessage();
            }
        );
        return
            $promise1->wait();
    }

    public static function getClient()
    {
        return [
            'nodes' => 'nodes',
            'ekshop' => 'ekshop',
            'ekshop-smsc' => 'ekshop Delivery',
        ];
    }

    public static function beelinkReport()
    {

        $data['successful'] = SmsDetails::where('msg_client', 'nodes')
            ->where('is_dlr_received', 1)
            ->whereDate('created_at', Carbon::today())
            ->count();

        $data['total'] = SmsDetails::where('msg_client', 'nodes')
            ->whereDate('created_at', Carbon::today())
            ->count();
        return $data;
    }

    public static function mobileValidaton($mobile)
    {
        if (strlen((string)$mobile) != 11) {
            return 'Invalid number length';
        }
        $OpArr = ['013', '014', '015', '016', '017', '018', '019'];
        $op = substr($mobile, 0, 3);

        if (!in_array($op, $OpArr)) {
            return 'Invalid number';
        }
        
        $checkDdos = SmsDetails::where('receiver_number', 'like', '%'.$mobile.'%')->orderBy('id', 'DESC');
        
        if ($checkDdos->exists()) {
            
        $checkDdosAttempt = $checkDdos->first()->created_at;
        
        $last = Carbon::createFromTimeString($checkDdosAttempt);
        $minuteDifference = abs(Carbon::now()->diffInMinutes($last,false));

            if($minuteDifference < 1){
                return Response()->json([
                                        'code' => 401,
                                        'reason' => 'You can not send sms to a same number within a minute'
                                    ], 401);
            }

        }
        
        return null;
    }

    public static function xmltoJson($response)
    {
        $xml = simplexml_load_string($response);
        $json = json_encode($xml);
        return json_decode($json, TRUE);
    }

    static function isGsm7bit($text)
    {
        $gsm7bitChars = "\\\@£\$¥èéùìòÇ\nØø\rÅåΔ_ΦΓΛΩΠΨΣΘΞÆæßÉ !\"#¤%&'()*+,-./0123456789:;<=>?¡ABCDEFGHIJKLMNOPQRSTUVWXYZÄÖÑÜ§¿abcdefghijklmnopqrstuvwxyzäöñüà^{}[~]|€";
        $textlen = mb_strlen($text);
        for ($i = 0; $i < $textlen; $i++) {
            if ((strpos($gsm7bitChars, $text[$i]) == false) && ($text[$i] != "\\")) {
                return false;
            } //strpos not     able to detect \ in string
        }
        return true;
    }

    static function getNumberOfSMSsegments($text, $MaxSegments = 6)
    {

        $TotalSegment = 0;
        $textlen = mb_strlen($text);
        if ($textlen == 0) return false; //I can see most mobile devices will not allow you to send empty sms, with this check we make sure we don't allow empty SMS

        if (General::isGsm7bit($text)) { //7-bit
            $SingleMax = 160;
            $ConcatMax = 153;
        } else { //UCS-2 Encoding (16-bit)
            $SingleMax = 70;
            $ConcatMax = 67;
        }

        if ($textlen <= $SingleMax) {
            $TotalSegment = 1;
        } else {
            $TotalSegment = ceil($textlen / $ConcatMax);
        }

        if ($TotalSegment > $MaxSegments) return false; //SMS is very big.
        return $TotalSegment;
    }

}
