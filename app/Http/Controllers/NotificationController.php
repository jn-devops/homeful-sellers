<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\SendMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;

class NotificationController extends Controller
{
    //
    public function send_email(Request $request)
    {   
        dd($request);
        $jsonBody = $request->json()->all();
        $recipient = $jsonBody['recipient'];
        $template = $jsonBody['template'];
        $mailBody = $jsonBody['mailBody'];
        Mail::to($recipient)->send(new SendMail($template,$mailBody));
        return response()->json(['status' => 'Email sent successfully']);
    
    }
    public function send_sms(Request $request){
        $recipient = $request->mobile;
        $message = $request->message;
        // dd(config("homeful-sellers.engagespark"));
        $credential = [
            "ENGAGESPARK_ORGANIZATION_ID"=>$request->header("x-sms-org-id")?$request->header("x-sms-org-id"):config("homeful-sellers.engagespark.org_id"),
            "ENGAGESPARK_SENDER_ID"=>$request->header("x-sms-sender-id")?$request->header("x-sms-sender-id"):config("homeful-sellers.engagespark.sender_id"),
            "ENGAGESPARK_API_KEY"=>$request->header("x-sms-api")?$request->header("x-sms-api"):config("homeful-sellers.engagespark.api_key")
        ];
        // dd($credential['ENGAGESPARK_ORGANIZATION_ID']);
       
        // return [$recipient];

        $apiUrl = 'https://api.engagespark.com/v1/sms/contact';
        $apiKey = $credential['ENGAGESPARK_API_KEY'];

        $response = Http::withHeaders([
            'Authorization' => 'Token ' . $apiKey,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->post($apiUrl, [
            'orgId' => $credential['ENGAGESPARK_ORGANIZATION_ID'],
            'from' =>  $credential['ENGAGESPARK_SENDER_ID'],
            'message' => $message,
            'fullPhoneNumber' => $recipient,
            'to'=>$recipient,
        ]);

        if (!$response->successful()) {
            return [
                'success' => false,
                'error' => $response->json('error') ?? 'Unknown error',
            ];
        }

        return [
            'success' => true,
            'response' => $response->json(),
        ];
    
    }
}
