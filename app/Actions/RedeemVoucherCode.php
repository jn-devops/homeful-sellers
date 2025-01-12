<?php

namespace App\Actions;

use Homeful\References\Facades\References;
use Lorisleiva\Actions\Concerns\AsAction;
use Illuminate\Support\Facades\Validator;
use Homeful\References\Models\Reference;

class RedeemVoucherCode
{
    use AsAction;

    protected function redeem(Reference $reference, array $meta)
    {
        $voucher_code = $reference->code;
        $user = $reference->owner;

        return References::redeem($voucher_code, $user, $meta);
    }

    public function handle(Reference $reference, ?array $attribs = []): bool
    {
        $validated = Validator::validate($attribs, $this->rules());

        return $this->redeem($reference, $validated);
    }

    public function rules(): array
    {
        return [
            'project_code' => ['nullable', 'string', 'min:2']
        ];
    }
}
