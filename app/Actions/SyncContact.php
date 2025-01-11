<?php

namespace App\Actions;

use Homeful\Contacts\Classes\ContactMetaData;
use Lorisleiva\Actions\Concerns\AsAction;
use Illuminate\Support\Facades\Validator;
use Homeful\Contacts\Models\Contact;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Arr;

class SyncContact
{
    use AsAction;

    /**
     * @var array|string[]
     */
    protected array $keys = [
        'mobile'
    ];

    /**
     * @param array $validated
     * @return Contact|false
     * @throws \Illuminate\Http\Client\ConnectionException
     */
    protected function sync(array $validated): Contact|false
    {
        //TODO: try catch here

        /** get the json response of the specific contact in Homeful Contacts */
        $response = Http::acceptJson()->get($this->getRoute($validated));
        /** cast the specific contact node of the json response to ContactMetaData
         *  then transform to array for further consumption of a Contact model.
         */
        $attributes = ContactMetaData::from($response->json('contact'))->toArray();
        /** retrieve key values to used for searching unique values in the contacts table */
        $keys = Arr::only($attributes, $this->keys);
        /** persist or update the contact in the contacts table */
        $contact = app(Contact::class)->updateOrCreate($keys, $attributes);

        return $contact instanceof Contact ? $contact : false;
    }

    /**
     * @param string|array $contact_reference_code
     * @return Contact|false
     * @throws \Illuminate\Http\Client\ConnectionException
     */
    public function handle(string|array $contact_reference_code): Contact|false
    {
        $attribs = is_array($contact_reference_code) ? $contact_reference_code : compact('contact_reference_code');
        $validated = Validator::validate($attribs, $this->rules());

        return $this->sync($validated);
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
}
