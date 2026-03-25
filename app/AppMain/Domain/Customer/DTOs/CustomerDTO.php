<?php

namespace App\AppMain\Domain\Customer\DTOs;

use App\AppMain\Core\BaseDTO;

class CustomerDTO extends BaseDTO
{
    public $full_name;
    public $phone_number;
    public $email;
    public $customer_type;
    public $customer_status;
    public $assigned_staff_id;
    public $address;
    public $date_of_birth;
    public $gender;
    public $source;
    public $notes;
}
