<?php

namespace App\AppMain\Application\User\Customer\Responses;

use App\AppMain\Core\BaseResponseDTO;

class CustomerResponse extends BaseResponseDTO
{
    public $id;
    public $fullName;
    public $phoneNumber;
    public $email;
    public $customerType;
    public $customerStatus;
    public $assignedStaffId;
    public $address;
    public $dateOfBirth;
    public $gender;
    public $source;
    public $createdAt;
    public $updatedAt;


    public static function fromModel($model): self
    {
        $dto = new self();

        $dto->id = $model->id;
        $dto->fullName = $model->full_name;
        $dto->phoneNumber = $model->phone_number;
        $dto->email = $model->email;
        $dto->customerType = $model->customer_type;
        $dto->customerStatus = $model->customer_status;
        $dto->assignedStaffId = $model->assigned_staff_id;
        $dto->address = $model->address;
        $dto->gender = $model->gender;
        $dto->source = $model->source;
        $dto->dateOfBirth = $model->date_of_birth;
        $dto->createdAt = $model->created_at;
        $dto->updatedAt = $model->updated_at;

        return $dto;
    }
}
