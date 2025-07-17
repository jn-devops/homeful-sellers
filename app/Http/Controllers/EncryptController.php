<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EncryptController extends Controller
{
    public static function encrypt($data)
    {
        $encryptKey = config('homeful-sellers.encrypt_key');
        $cipher = "AES-256-CBC";
        $ivLength = openssl_cipher_iv_length($cipher);
        $iv = openssl_random_pseudo_bytes($ivLength);
    
        $ciphertext = openssl_encrypt($data, $cipher, $encryptKey, OPENSSL_RAW_DATA, $iv);
        return bin2hex($iv . $ciphertext);
    }
    
    public static function decrypt($hexEncrypted)
    {
        $encryptKey = config('homeful-sellers.encrypt_key');
        $cipher = "AES-256-CBC";
        $data = hex2bin($hexEncrypted);
    
        $ivLength = openssl_cipher_iv_length($cipher);
        $iv = substr($data, 0, $ivLength);
        $ciphertext = substr($data, $ivLength);
    
        return openssl_decrypt($ciphertext, $cipher, $encryptKey, OPENSSL_RAW_DATA, $iv);
    }

}
