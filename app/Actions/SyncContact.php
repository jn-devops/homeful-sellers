<?php

namespace App\Actions;

use Homeful\Contacts\Models\Customer as Contact;
use Homeful\Contacts\Classes\ContactMetaData;
use Lorisleiva\Actions\Concerns\AsAction;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Arr;
use Homeful\Contacts\Actions\PersistContactAction;

class SyncContact
{
    use AsAction;

    /**
     * @var array|string[]
     */
    protected array $keys = [
        'reference_code'
    ];

    /**
     * @param array $validated
     * @return Contact|false
     * @throws \Illuminate\Http\Client\ConnectionException
     */
    protected function sync(array $validated): false|Contact
    {
        /** get the json response of the specific contact in Homeful Contacts */
        $response = Http::acceptJson()->get($this->getRoute($validated));

        return $response->ok()
            ? $this->persistContact($response)
            : false
            ;
    }

    /**
     * @param string|array $contact_reference_code
     * @return Contact|false
     * @throws \Illuminate\Http\Client\ConnectionException
     */
    public function handle(string|array $contact_reference_code): false|Contact
    {
        $attribs = is_array($contact_reference_code) ? $contact_reference_code : compact('contact_reference_code');
        $validated = Validator::validate($attribs, $this->rules());

        return $this->newPersistContact($contact_reference_code);
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
            $contact = $action->run($validated);
        }

        return $contact instanceof Contact ? $contact : false;
    }

    /**
     * @param \GuzzleHttp\Promise\PromiseInterface|\Illuminate\Http\Client\Response $response
     * @return false|Contact
     */
    public function persistContact(\GuzzleHttp\Promise\PromiseInterface|\Illuminate\Http\Client\Response $response): false|Contact
    {
        /** cast the specific contact node of the json response to ContactMetaData
         *  then transform to array for further consumption of a Contact model.
         */
        $attributes = ContactMetaData::from($response->json('contact'))->toArray();

        /** retrieve key values to used for searching unique values in the contacts table */
        $keys = Arr::only($attributes, $this->keys);

        /** persist or update the contact in the contacts table */
        $contact = app(Contact::class)->firstOrCreate($keys, $attributes);

        return $contact instanceof Contact ? $contact : false;
    }
}
