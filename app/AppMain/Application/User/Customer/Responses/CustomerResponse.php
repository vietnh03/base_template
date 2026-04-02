<?php

namespace App\AppMain\Application\User\Customer\Responses;

use App\AppMain\Core\BaseResponseDTO;

class CustomerResponse extends BaseResponseDTO
{
    public $id;
    public $name;
    public $phone;
    public $email;
    public $image;
    public $status;
    public $isVerified;
    public $dateOfBirth;
    public $gender;
    public $createdAt;
    public $updatedAt;


    public static function fromModel($model): self
    {
        $dto = new self();

        $dto->id = $model->id;
        $dto->name = $model->name;
        $dto->phone = $model->phone;
        $dto->email = $model->email;
        $dto->image = $model->image;
        $dto->status = $model->status;
        $dto->isVerified = (bool) $model->is_verified;
        $dto->gender = $model->gender;
        $dto->dateOfBirth = $model->date_of_birth;
        $dto->createdAt = $model->created_at;
        $dto->updatedAt = $model->updated_at;

        return $dto;
    }
}
