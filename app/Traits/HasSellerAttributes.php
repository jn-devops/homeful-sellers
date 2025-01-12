<?php

namespace App\Traits;

trait HasSellerAttributes
{

    public function initializeHasSellerAttributes(): void
    {
        $this->mergeFillable([
            'seller_commission_code',
        ]);

        $this->appends = array_merge($this->appends, ['seller_commission_code']);

    }

    public function setSellerCommissionCodeAttribute(string $value): self
    {
        $this->getAttribute('meta')->set('seller_commission_code', $value);

        return $this;
    }

    public function getSellerCommissionCodeAttribute(): ?string
    {
        return $this->getAttribute('meta')->get('seller_commission_code');
    }
}
