<?php

namespace App\Http\Requests\Api\V1;

use App\Traits\ApiErrorResponse;
use Illuminate\Foundation\Http\FormRequest;

class StoreGuideRequest extends FormRequest
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
            'category_id' => 'required|exists:categories,id',
            "agency_id"             => "nullable|integer|exists:agencies,id",
            "first_name"            => "required|string|min:3|max:20",
            "last_name"             => "required|string|min:3|max:20",
            "email"                 => "required|email|string|unique:guides,email",
            "password"              => "required|required|min:5|max:10",
            "phone"                 => "required|string|unique:guides,phone",
            'image'                 => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            "city"                  => "required|string",
            "country"               => "required|string",
            // "metadata"              => "required",
            "provider"              => "nullable|in:credential,facebook,google",
            "provider_id"           => "nullable|string",
            "access_token"          => "nullable|string",
            // "email_verified_at"     => "required",
            "firebase_token"        => "nullable|string",
            // "status"                => "required",
        ];
    }
}
