<?php

namespace App\Actions;

use Homeful\Contacts\Models\Customer as Contact;
use Homeful\Contacts\Classes\ContactMetaData;
use Lorisleiva\Actions\Concerns\AsAction;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Arr;
use App\Models\User;
use Homeful\Contacts\Actions\PersistContactAction;

class SyncContact
{
    use AsAction;

    /**
     * @var array|string[]
     */
    protected array $keys = [
        'contact_reference_code'
    ];

    /**
     * @param array $validated
     * @return Contact|false
     * @throws \Illuminate\Http\Client\ConnectionException
     */
    protected function sync(user $user, array $validated): false|Contact
    {
        // dd($user);
        $url =$this->getRoute($validated);
        $response = Http::acceptJson()->get($this->getRoute($validated));
        // $response = Http::acceptJson()->get("https://contacts-staging.homeful.ph/api/references/H-4EYR4A");
        // dd($response->json('contact'));
        return $response->ok()
            ? $this->persistContact($user, $response)
            : false
            ;
        /** get the json response of the specific contact in Homeful Contacts */
        // $response = Http::acceptJson()->get($this->getRoute($validated));

        // return $response->ok()
        //     ? $this->persistContact($response)
        //     : false
        //     ;
    }

    /**
     * @param string|array $contact_reference_code
     * @return Contact|false
     * @throws \Illuminate\Http\Client\ConnectionException
     */
    public function handle(user $user, string|array $contact_reference_code): false|Contact
    {
        $attribs = is_array($contact_reference_code) ? $contact_reference_code : compact('contact_reference_code');
        $validated = Validator::validate($attribs, $this->rules());
        return $this->sync($user, $validated);
    }

    /**
     * @param array $attribs
     * @return string
     */
    protected function getRoute(array $attribs): string
    {
        return __(config('homeful-sellers.end-points.customer'), $attribs);
    }

    public function rules(): array
    {
        return [
            'contact_reference_code' => ['required', 'string', 'min:4']
        ];
    }

    public function newPersistContact(string $homeful_id): false|Contact{
        $request =Http::withToken(config('homeful-sellers.keys.contact_key'))->post(config('homeful-sellers.end-points.contact').'api/get-contact-by-homeful-id', [
            'code' => $homeful_id,
        ]);


        if($request->ok()) {
            $attributes = $request->json('data');
            $keys = Arr::only($attributes, $this->keys);
        }

        $contact = app(Contact::class)->where('reference_code',$homeful_id)->first();
        if($contact==null){
            $action = app(PersistContactAction::class);
            $validator = Validator::make($attributes, $action->rules());
            $validated = $validator->validated();
            $contact = $action->run($attributes);
            $contact->save();
            $contact = app(Contact::class)->where('reference_code',$homeful_id)->first();
        }

        return $contact instanceof Contact ? $contact : false;
    }

    /**
     * @param \GuzzleHttp\Promise\PromiseInterface|\Illuminate\Http\Client\Response $response
     * @return false|Contact
     */
    public function persistContact(user $user,\GuzzleHttp\Promise\PromiseInterface|\Illuminate\Http\Client\Response $response): false|Contact
    {
        /** cast the specific contact node of the json response to ContactMetaData
         *  then transform to array for further consumption of a Contact model.
         */
        // dd($response->json('contact'));
        // dd($user);
        $attributes = ContactMetaData::from($response->json('contact'))->toArray();
        // $attributes['order']['seller_id'] = $user->seller_email;
        $attributes['order']['seller_commission_code'] = $user->meta->seller_commission_code;
        $attributes['landline'] = $user->email."/".$user->meta->seller_commission_code;
        // $attributes['seller_info'] =[
        //     "seller_id"=>$user->id,
        //     "seller_commission_code" => $user->meta->seller_commission_code
        // ];
        
        // dd($attributes);
        /** retrieve key values to used for searching unique values in the contacts table */
        // $keys = Arr::only($attributes['order'], $this->keys);
        $keys=[
            "reference_code" => $attributes['order']['homeful_id']
        ];
        
        // dd($keys,$attributes);
        /** persist or update the contact in the contacts table */
        $contact = app(Contact::class)->updateOrCreate($keys, $attributes);
        // dd( $contact );
        return $contact instanceof Contact ? $contact : false;
    }
}
