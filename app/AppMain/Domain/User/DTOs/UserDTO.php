<?php

namespace App\AppMain\Domain\User\DTOs;

use App\AppMain\Core\BaseDTO;

class UserDTO extends BaseDTO
{
    public $name;
    public $gender;
    public $date_of_birth;
    public $email;
    public $phone;
    public $image;
    public $status;
    public $password;
    public $is_verified;
    public $token;
    public $notes;
}
