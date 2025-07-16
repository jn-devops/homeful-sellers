<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EncryptController extends Controller
{
    //
// function _build(){
// // Example usage

// // Encrypt
// $encrypted = encrypt($original_text, $secret_key);
// echo "Encrypted: $encrypted\n";

// // Decrypt
// $decrypted = decrypt($encrypted, $secret_key);
// echo "Decrypted: $decrypted\n";
// }
public static function encrypt($data) {
    $encryptKey = config('homeful-sellers.encrypt_key');  
    $cipher = "AES-256-CBC";
    $ivlen = openssl_cipher_iv_length($cipher);
    $iv = openssl_random_pseudo_bytes($ivlen);
    $ciphertext = openssl_encrypt($data, $cipher, $encryptKey, 0, $iv);
    return base64_encode($iv . $ciphertext);
}

public static function decrypt($encrypted) {
    $encryptKey = config('homeful-sellers.encrypt_key');  
    $cipher = "AES-256-CBC";
    $data = base64_decode($encrypted);
    $ivlen = openssl_cipher_iv_length($cipher);
    $iv = substr($data, 0, $ivlen);
    $ciphertext = substr($data, $ivlen);
    return openssl_decrypt($ciphertext, $cipher, $encryptKey, 0, $iv);
}



}
