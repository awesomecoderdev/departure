<?php

namespace App\Traits;

use Illuminate\Http\Response as HTTP;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;

trait ApiErrorResponse
{
    /**
     * Convert a validation exception into a JSON response.
     *
     * @param  \Illuminate\Validation\ValidationException  $e
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    protected function convertValidationExceptionToResponse(ValidationException $e, $request)
    {
        $errors = $e->validator->errors()->getMessages();

        return Response::json([
            'success' => false,
            'status' => HTTP::HTTP_UNPROCESSABLE_ENTITY,
            'message' => 'Validation failed.',
            'errors' => $errors,
        ], HTTP::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    protected function failedValidation($validator)
    {
        throw new HttpResponseException(Response::json([
            'success' => false,
            'status' => HTTP::HTTP_UNPROCESSABLE_ENTITY,
            'message' => 'Validation failed.',
            'errors' => $validator->errors(),
        ], HTTP::HTTP_UNPROCESSABLE_ENTITY));
    }
}
