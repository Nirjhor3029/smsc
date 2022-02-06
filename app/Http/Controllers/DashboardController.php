<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\SmsDetails;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function adminIndex()
    {

        $select = [
            'receiver_number',
            'msg_status',
            'msg_guid',
            'tMsgId',
            'msg_provider',
            'telecom_operator',
            'delivered_data',
            'sms_details.created_at',
            'sms_details.telecom_operator'
        ];

         $todaysNodesSms = SmsDetails::toBase()
            ->select($select)
            ->join('dlrs','sms_details.id','dlrs.sms_id')
            ->where('sms_details.msg_client', 'nodes')
            ->whereDate('sms_details.created_at', '=', Carbon::today()->toDateString())
            ->get();

         $totalNodesSms = SmsDetails::toBase()
            ->select($select)
            ->join('dlrs','sms_details.id','dlrs.sms_id')
            ->where('sms_details.msg_client', 'nodes')
            ->get();

         $totalNodesDelivered = $totalNodesSms->where('msg_status','Delivered')->count();
         $todaysNodesDelivered = $todaysNodesSms->where('msg_status','Delivered')->count();


//       Operator wise total
         $gpTotal = $totalNodesSms->where('telecom_operator', 'Grameenphone')->count();
         $robiTotal = $totalNodesSms->where('telecom_operator', 'Robi')->count();
         $airtelTotal = $totalNodesSms->where('telecom_operator', 'Airtel')->count();
         $blTotal = $totalNodesSms->where('telecom_operator', 'Banglalink')->count();
         $ttTotal = $totalNodesSms->where('telecom_operator', 'Teletalk')->count();

//      Operator wise total delivered
        $gpTotalDelivered = $totalNodesSms->where('telecom_operator', 'Grameenphone')->where('msg_status','Delivered')->count();
        $robiTotalDelivered = $totalNodesSms->where('telecom_operator', 'Robi')->where('msg_status','Delivered')->count();
        $airtelTotalDelivered = $totalNodesSms->where('telecom_operator', 'Airtel')->where('msg_status','Delivered')->count();
        $blTotalDelivered = $totalNodesSms->where('telecom_operator', 'Banglalink')->where('msg_status','Delivered')->count();
        $ttTotalDelivered = $totalNodesSms->where('telecom_operator', 'Teletalk')->where('msg_status','Delivered')->count();

        $totalFailed =  $totalNodesSms->where('msg_status','Failed')->count();
        $totalDlrWaiting = SmsDetails::where('msg_client','nodes')->where('is_dlr_received',0)->count();

         if((int) count($todaysNodesSms) != 0){
             $toDayPercentage = round($todaysNodesDelivered*100/(int) count($todaysNodesSms)) ;
             $toDayPercentage = floor($toDayPercentage);
         }else{
             $toDayPercentage = 0;
         }

        if((int)count($totalNodesSms) != 0){
            $totalPercentage = round((int)$totalNodesDelivered*100/(int)count($totalNodesSms)) ;
            $totalPercentage = floor($totalPercentage);
        }else{
            $totalPercentage = 0;
        }



        return view('dashboard.admin_dashboard',compact('todaysNodesSms',
            'totalNodesSms',
            'totalNodesDelivered',
            'todaysNodesDelivered',
            'toDayPercentage',
            'totalPercentage',
            'gpTotal',
            'robiTotal',
            'airtelTotal',
            'blTotal',
            'ttTotal',
            'gpTotalDelivered',
            'robiTotalDelivered',
            'airtelTotalDelivered',
            'blTotalDelivered',
            'ttTotalDelivered',
            'totalFailed',
            'totalDlrWaiting'

        ));
    }
}
