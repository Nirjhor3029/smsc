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
use Illuminate\Support\Facades\DB;

use App\Dlr;


date_default_timezone_set('Asia/Dhaka');


class SmsReportController extends Controller
{

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

    public function smsCount(Request $request){

        if (!in_array($request->passkey, General::passKey())) {
            return 'Invalid passkey';
        }

        if (!in_array($request->client, General::getClientName())) {
            return "Please provide valid client name";
        }
        
        $count =  DB::table('sms_details')->where('msg_client',$request->client)->sum('msg_count');

        return response()->json([
            'client'=> $request->client,
            'count' => $count
        ],200);

    }


}
