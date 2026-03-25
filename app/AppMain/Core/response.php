<?php

namespace App\AppMain\Core\Helpers;

const RESPONSE_STATUS_SUCCESS = 1;
const RESPONSE_STATUS_FAIL = 0;

const HTTP_CODE_SUCCESS = 200;
const HTTP_CODE_UNAUTHORIZED = 401;

function responseJsonSuccess($data = [], $message = '')
{
    return response(
        [
            'status' => RESPONSE_STATUS_SUCCESS,
            'message' => $message,
            'data' => $data
        ]
        , HTTP_CODE_SUCCESS);
}

function responseJsonFail($message = '', $httpCode = HTTP_CODE_SUCCESS, $errors=[])
{
    return response(
        [
            'status' => RESPONSE_STATUS_FAIL,
            'message' => $message,
        ]
        , $httpCode);
}

function responseJsonFailMultipleErrors($errors=[], $message = '', $httpCode = HTTP_CODE_SUCCESS)
{
    return response(
        [
            'status' => RESPONSE_STATUS_FAIL,
            'message' => $message,
            'errors' => $errors,
        ]
        , $httpCode);
}

