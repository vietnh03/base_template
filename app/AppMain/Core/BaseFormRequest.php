<?php

namespace App\AppMain\Core;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

use function App\AppMain\Core\Helpers\responseJsonFailMultipleErrors;

abstract class BaseFormRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors()->all();
        throw new HttpResponseException(
            responseJsonFailMultipleErrors($errors)
        );
    }

    protected function passedValidation(): void
    {
        foreach ($this->validated() as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
    }
}
