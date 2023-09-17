<?php

namespace App\Http\Requests\Api\V1;

use App\Traits\ApiErrorResponse;
use Illuminate\Foundation\Http\FormRequest;

class UpdateFacilityRequest extends FormRequest
{
    use ApiErrorResponse;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            "icon_id"           => "required|integer|exists:icons,id",
            "service_id"        => "required|integer|exists:services,id",
            "title"             => "required|string|max:50",
            "description"       => "required|string|max:250",
        ];
    }
}
