<?php

namespace App\Actions;

use Homeful\References\Facades\References;
use Lorisleiva\Actions\Concerns\AsAction;
use Illuminate\Support\Facades\Validator;
use Homeful\References\Models\Reference;
use Homeful\Properties\Models\Project;

class RedeemVoucherCode
{
    use AsAction;

    protected function redeem(Reference $reference, array $meta)
    {
        $voucher_code = $reference->code;
        $user = $reference->owner;
        // dd($reference->codownere);
        $project = Project::where('code',"PVMP")->
        where('redeemed_at',Null)->first();
        dd($project);

        $meta=[
            "project"=>$project->toArray(),
            "project_code"=>$project->code
        ];
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
