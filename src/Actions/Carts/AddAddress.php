<?php

namespace HeadlessEcom\Actions\Carts;

use HeadlessEcom\Actions\AbstractAction;
use HeadlessEcom\Base\Addressable;
use HeadlessEcom\Models\Cart;
use HeadlessEcom\Models\CartAddress;

class AddAddress extends AbstractAction
{
    protected $fillableAttributes = [
        'country_id',
        'title',
        'first_name',
        'last_name',
        'company_name',
        'line_one',
        'line_two',
        'line_three',
        'city',
        'state',
        'postcode',
        'delivery_instructions',
        'contact_email',
        'contact_phone',
        'meta',
    ];

    /**
     * Execute the action.
     *
     * @param  \HeadlessEcom\Models\CartLine  $cartLine
     * @param  \Illuminate\Database\Eloquent\Collection  $customerGroups
     * @return \HeadlessEcom\Models\CartLine
     */
    public function execute(
        Cart $cart,
        array|Addressable $address,
        string $type
    ): self {
        // Do we already have an address for this type?
        $cart->addresses()->whereType($type)->delete();

        if (is_array($address)) {
            $cartAddress = new CartAddress($address);
        }

        if ($address instanceof Addressable) {
            $cartAddress = new CartAddress(
                $address->only($this->fillableAttributes)
            );
        }

        // Force the type.
        $cartAddress->type = $type;
        $cartAddress->cart_id = $cart->id;
        $cartAddress->save();

        return $this;
    }
}
