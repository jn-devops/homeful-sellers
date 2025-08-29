<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;//if need API
use App\Http\Controllers\APIController;
use App\Models\User;

class DefaultSetupController extends Controller
{
    //
    public static function getStatus(){

        //api if needed 
        //default
        return [
            ['status'=>'Consulted','mask'=>'Consulted'],
            ['status'=>'Paid','mask'=>'Paid'],
            ['status'=>'Pre-Qualified','mask'=>'Pre-Qualified'],
            ['status'=>'Approved/OS','mask'=>'Approved/OS'],
            ['status'=>'1st Filing','mask'=>'Filing'],
            ['status'=>'2nd Filing','mask'=>'Filing'],
            ['status'=>'For Remedy for 1st Filing','mask'=>'Remedial'],
            ['status'=>'For Remedy for 2nd Filing','mask'=>'Remedial'],
            ['status'=>'For Remedy for Sales','mask'=>'Remedial'],
            ['status'=>'Cancelled by Sales','mask'=>'Cancelled'],
            ['status'=>'Cancelled by CFU','mask'=>'Cancelled'],
            ['status'=>'Signed','mask'=>'Approved/OS'],
            ['status'=>'Filed','mask'=>'Approved/OS'],
            ['status'=>'Take Out','mask'=>'Approved/OS'],
            ['status'=>'Printed','mask'=>'Approved/OS'],
            ['status'=>'Prospects','mask'=>'Prospects'],
            ['status'=> Null,'mask'=>'Prospects'],
        ];
    }
    public static function getTransferStatus(){

        return [
            ['status'=>'Processing'],
            ['status'=>'Free'],
            ['status'=>'For Approval'],
            ['status'=>'']
        ];
    }
    public static function getSellerCode(){
        // $response = Http::asForm()
        // ->withToken('a34c9bef423b68fed296bd1e28e660a3bf282134c1dddb5458275b4bbb92360e')
        // ->get('https://everyhome.enclavewrx.io/developers/api_storefront_seller/seller_list_NOAH', [

        // ]);
        $response = APIController::getBrokerList();
        return $response->json();
    }

    public static function getBrokerSeqNumbering($seller_commission_code){
       $seller_group = User::where('meta->seller_commission_code',$seller_commission_code)->get();
       return  $seller_group->count()?str_pad($seller_group->count()+1, 3, '0', STR_PAD_LEFT):"001";
    }
}
