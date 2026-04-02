<?php

namespace App\AppMain\Domain\Checkout\DTOs;

use App\AppMain\Core\BaseDTO;

class AddressDTO extends BaseDTO
{
    public string $address_type;
    public ?string $user_id = null;
    public ?string $cart_id = null;
    public ?string $order_id = null;
    public string $first_name;
    public string $last_name;
    public ?string $gender = null;
    public ?string $company_name = null;
    public string $address;
    public ?string $address1 = null;
    public string $city;
    public ?string $state = null;
    public ?string $country = null;
    public ?string $postcode = null;
    public ?string $email = null;
    public ?string $phone = null;
    public ?string $vat_id = null;
    public bool $default_address = false;
    public ?array $additional = null;

    public function __construct(array $data)
    {
        parent::__construct($data);
    }
}
