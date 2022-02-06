<?php

namespace App\Http\Controllers;

use App\Helpers\General;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use mysql_xdevapi\Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

date_default_timezone_set('Asia/Dhaka');

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;


    public function mobileNoFormatValidation(Request $request)
    {


        if (!$request->has('number') || empty($request->number)) {
            return "No Number";
        }

        try {
            $mobile = $request->number;
            $mobile = General::formatMobileNumber($mobile);


            $checkError = General::mobileValidaton($mobile);
            if (!is_null($checkError)) {


                //Save Log if unsuccessful
            Log::channel('numberFormatLog')->info('data',
            [
                'requested_number' => $request->number,
                'detail' =>
                            [
                            'timestamp' => now(),
                            'formatted_number' => $mobile,
                            'error' => $checkError,
                            'client_ip'=> $request->ip()
                            ]
                
            ]);

            return Response()->json([
                            'code' => 400,
                            'reason' => $checkError
                        ], 400);
            }


            //Save Log if successful
            Log::channel('numberFormatLog')->info('data',
            [
                'requested_number' => $request->number,
                'detail' =>
                            [
                            'timestamp' => now(),
                            'formatted_number' => $mobile,
                            'error' => NULL,
                            'client_ip'=> $request->ip()
                            ]
                
            ]);
            
            return Response()->json([
                            'code' => 200,
                            'requested number' => $request->number,
                            'formatted number' => $mobile
                        ], 200);


        } catch (Exception $e) {

            echo $e->getMessage();

        }

        return 0;

    }



    public function showLogs(Request $request){

        $limit= 50;

        if (!$request->has('passkey') || $request->passkey != 'rana') {
            return "Invalid passkey";
        }

        if($request->has('limit') && !empty($request->limit)){
            $limit = $request->limit;
        }

        $file = storage_path('logs/number-format.log');

        if(file_exists($file)){

            $data = array();
            $set = array();
            
            $file = new \SplFileObject($file , 'r');
            $file->seek(PHP_INT_MAX);
            $last_line = $file->key();

            ($limit > $last_line) ? $limit = $last_line : $limit = $limit;

            $lines = new \LimitIterator($file, $last_line - $limit, $last_line); //n being non-zero positive integer
            $data = iterator_to_array($lines);

            foreach($data as $d){
                 array_push($set, substr($d,38));
            }


            echo '<pre>';
                print_r($set);
            echo '<pre>';

        }

    }

}
