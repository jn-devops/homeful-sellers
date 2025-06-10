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
        $credential = [
            "ENGAGESPARK_ORGANIZATION_ID"=>$request->header("x-sms-org-id"),
            "ENGAGESPARK_SENDER_ID"=>$request->header("x-sms-sender-id"),
            "ENGAGESPARK_API_KEY"=>$request->header("x-sms-api")
        ];
        // dd($credential['ENGAGESPARK_ORGANIZATION_ID']);
       
        // return [$recipient];

        $apiUrl = 'https://api.engagespark.com/v1/sms/contact';
        $apiKey = $credential['ENGAGESPARK_API_KEY']?$credential['ENGAGESPARK_API_KEY']:env('ENGAGESPARK_API_KEY');

        $response = Http::withHeaders([
            'Authorization' => 'Token ' . $apiKey,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->post($apiUrl, [
            'orgId' => $credential['ENGAGESPARK_ORGANIZATION_ID']?$credential['ENGAGESPARK_ORGANIZATION_ID']:env('ENGAGESPARK_ORGANIZATION_ID', ''),
            'from' =>  $credential['ENGAGESPARK_SENDER_ID']?$credential['ENGAGESPARK_SENDER_ID']:env('ENGAGESPARK_SENDER_ID', ''),
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
