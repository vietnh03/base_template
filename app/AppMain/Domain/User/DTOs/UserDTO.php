<?php

namespace App\AppMain\Domain\User\DTOs;

use App\AppMain\Core\BaseDTO;

class UserDTO extends BaseDTO
{
    public $name;
    public $email;
    public $password;
    public $role;
    public $status;
}
