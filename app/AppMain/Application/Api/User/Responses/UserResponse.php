<?php

namespace App\AppMain\Application\Api\User\Responses;

use App\AppMain\Core\BaseResponseDTO;

class UserResponse extends BaseResponseDTO
{
    public $id;
    public $name;
    public $email;
    public $phone;
    public $gender;
    public $date_of_birth;
    public $image;
    public $status;
    public $email_verified_at;
    public $notes;
    public $created_at;

    public static function fromModel($model): self
    {
        return new self($model);
    }

    public function __construct($model)
    {
        if (!$model)
            return;

        $this->id = $model->id;
        $this->name = $model->name;
        $this->email = $model->email;
        $this->phone = $model->phone;
        $this->gender = $model->gender;
        $this->date_of_birth = $model->date_of_birth;
        $this->image = $model->image;
        $this->status = $model->status;
        $this->email_verified_at = $model->email_verified_at;
        $this->notes = $model->notes;
        $this->created_at = $model->created_at;
    }
}
