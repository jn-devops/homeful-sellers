<?php

namespace App\Http\Controllers;

use Homeful\Contacts\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function delete_via_mobile(String $mobile)
    {
        $contact = Contact::where('mobile', $mobile);
        if($contact->count() == 0) {
            return response()->json(['message' => 'Contact not found'], 404);
        }else{
            $contact = $contact->delete();
            return response()->json(['message' => 'Contact deleted successfully'], 200);
        }
    }
}
