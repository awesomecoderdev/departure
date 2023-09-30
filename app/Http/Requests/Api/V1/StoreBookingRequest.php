<?php

namespace App\Http\Requests\Api\V1;

use App\Traits\ApiErrorResponse;
use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
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
            // "guide_id" => "required|exists:guides,id",
            // "agency_id" => "required|exists:agencies,id",
            // "customer_id" => "required|exists:customers,id",
            "service_id" => "required|exists:services,id",
            "payment_method" => "required|in:cod,online",
            // "trx_id" => "required",
            // "category_id" => "required|exists:categories,id",
            // "zone_id" => "required|exists:zones,id",
            // "status" => "in:pending,accepted,rejected,progressing,progressed,cancelled,completed",
            // "is_paid" => "boolean",
            // "total_amount" => "required|string",
            // "total_tax" => "string",
            // "total_discount" => "string",
            // "additional_charge" => "string",
            "check_in" => "date_format:Y-m-d",
            "check_out" => "date_format:Y-m-d",
            // "is_rated" => "boolean",
            "quantity" => "integer"
        ];
    }
}
