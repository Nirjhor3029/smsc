<?php

namespace App\Http\Controllers;

use App\Helpers\General;
use App\Helpers\SmsProviders;
use App\SmsDetails;
use GuzzleHttp\Client;
use http\Env\Response;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use Mail;
use DB;
use Symfony\Component\VarDumper\Cloner\Data;

class PromotionController extends Controller
{

    public function msgLengthUpdate(){

        $allData =  DB::table('bulk_sms')->get();

        foreach($allData as $data){

            $msg_char_count = (int) mb_strlen($data->sms);
            $msg_count = (int) General::getNumberOfSMSsegments($data->sms);
            DB::table('bulk_sms')->where('id',$data->id)->update([
                'msg_char_count'=> $msg_char_count,
                'msg_count' => $msg_count
            ]);

        }
    }


    public function ajaxMsgLengthCount(Request $request){

        return  (int) General::getNumberOfSMSsegments($request->msg_count);
    }
    

    public function promotionList(Request $request)
    {

        if ($request->ajax()) {

            $data = SmsDetails::where('msg_client', 'ekShop Promotion')->orderByDesc('id')->select('*');
            return Datatables::of($data)
                ->addIndexColumn()
                ->make(true);
        }

        return view('dashboard.promotion_dashboard');
    }

    public function bulkPromotionList(Request $request)
    {

        if ($request->ajax()) {

            $data = DB::table('bulk_sms')->orderByDesc('id')->select('*');
            return Datatables::of($data)
                ->addIndexColumn()
                ->make(true);
        }

        return view('dashboard.send_bulk_promotion');
    }


     public function bulkSend(Request $request)
    {
        $clean_number_array = array();
        $error_array = array();

        $secret_code = ['ekShop@2021'];

        if (isset($request->secret_code)) {
            if (!in_array($request->secret_code, $secret_code)) {
                return redirect('promotion/bulk')->withSuccess('Invalid Secret key')->withInput();
            }
        }


        $client_name = $request->client;
        // $number_array = explode(",", $request->numbers);
        $number_array = $split_strings = preg_split('/[\ \n\r\,]+/', $request->numbers, -1, PREG_SPLIT_NO_EMPTY);
        $number_array = str_replace(array("\n", "\r"), '', $number_array);
        $number_array = array_unique($number_array);

        $search_array = array("১", "২", "৩", "৪", "৫", "৬", "৭", "৮", "৯", "০");
        $replace_array = array("1", "2", "3", "4", "5", "6", "7", "8", "9", "0");
        $number_array = str_replace($search_array, $replace_array, $number_array);
        $number_array = str_replace("-", "", $number_array);
        $number_array = str_replace(" ", "", $number_array);
        $number_array = str_replace(".", "", $number_array);

        foreach ($number_array as $number) {
            
            $number = General::formatMobileNumber($number);
            $checkError = General::mobileValidaton($number);
            
            if (!is_null($checkError)) {
                array_push($error_array, $number);
                continue;
            }

            if (preg_match('/[0-9 +\-]/i', $number)) {
                array_push($clean_number_array, $number);
            } else {
                array_push($error_array, $number);
            }
        }
        $num_arr_chunk = array_chunk($clean_number_array, 50);
        $result = '';
        $text['smsText'] = $request->smsText;

//        $result .= SmsProviders::robiAirtelSmsBulk($data);

        foreach ($num_arr_chunk as $data_arr) {
            $data_arr['mobile'] = $data_arr;
            $data = array_merge($data_arr, $text);

           $result .= SmsProviders::teletalkSmsA2i($data);

/*
            if (SmsProviders::robiAirtelBulkPromotion($data)) {
                $result .= 'Numbers: '. json_encode($data_arr['mobile']);
            }
*/

        }

        $msg_char_count = (int) mb_strlen($request->smsText);
        $msg_count = (int) General::getNumberOfSMSsegments($request->smsText);


        $data = [
            "client" => $client_name,
            "campaign_name" => $request->campaign_name . " - Total send: " . sizeof($number_array). " (". sizeof($number_array) * $msg_count." sms)",
            "body" => $result,
            "msg_char_count" => $msg_char_count,
            "msg_count" => $msg_count,
            "sms" => json_encode($request->smsText, JSON_UNESCAPED_UNICODE),
            "error_numbers" => json_encode($error_array),
            "created_at" => now(),
            "updated_at" => now()
        ];


        DB::table('bulk_sms')->insert($data);

        return redirect('promotion/bulk')->withSuccess('Your messages queued for sending! Please check details in the table');
    }


    public function send(Request $request)
    {
//        return $request;

        global $mail;
        $msg = $request->smsText;
        $number = $request->number;
        $mail = $request->email;

        if (isset($number) && !empty($number)) {
            $url = url('/ekshop-promotion') . '?passkey=Open1234&number=' . $number . '&smsText=' . $msg;

            $client = new Client();
            $promise1 = $client->getAsync($url)->then(
                function ($response) {
                    return $response->getBody();
                }, function ($exception) {
                $exception->getMessage();
            }
            );
            $promise1->wait();
        }


        if (isset($mail) && !empty($mail)) {
            Mail::send('emails.ekshop_promotion', [
                'textBody' => $request->smsText],
                function ($message) {
                    global $mail;
                    $message->from('support@ekshop.gov.bd', 'EKSHOP SUPPORT');
                    $message->to($mail)
                        ->subject('একশপ আনন্দমেলা অনবোর্ডিং');
                });
        }

        return redirect('/promotion')->with(['message' => 'Sent', 'alert' => 'alert-success']);
    }


    public function anondomelaSend(Request $request)
    {
//        return $request;

        global $mail, $userId, $passWord;

        $mail = $request->email;

        if (isset($mail) && !empty($mail)) {
            Mail::send('emails.anondomela_promotion', [
                'userId' => ($request->userId),
                'passWord' => $request->passWord],
                function ($message) {
                    global $mail;
                    $message->from('support@ekshop.gov.bd', 'EKSHOP SUPPORT');
                    $message->to($mail)
                        ->subject('একশপ আনন্দমেলা অনবোর্ডিং');
                });
        }
        return redirect('promotion/anondomela')->with(['message' => 'Sent', 'alert' => 'alert-success']);
    }

    public function form()
    {
        return view('anondomela');
    }


}
