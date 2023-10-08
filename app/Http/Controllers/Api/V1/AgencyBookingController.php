<?php

namespace App\Http\Controllers\Api\V1;

use Carbon\Carbon;
use App\Models\Booking;
use App\Models\Service;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response as HTTP;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\Api\V1\CustomerResource;
use App\Http\Requests\Api\V1\StoreBookingRequest;

class AgencyBookingController extends Controller
{
    /**
     * Retrieve booking info.
     */
    public function booking(Request $request)
    {
        try {
            // Get the user's id from token header and get his provider bookings
            // status
            $params = Arr::only($request->input(), ["query", "zone_id", "per_page"]);
            $agency = $request->user('agency');
            $bookings = Booking::with([
                // "agency",
                "customer",
                "service",
            ])->where("agency_id", $agency->id)->when($request->status != null && in_array($request->status, ["pending", "accepted", "rejected", "progressing", "progressed", "cancelled", "completed"]), function ($query) use ($request) {
                return $query->where("status", strtolower($request->status));
            })->orderBy("id", "DESC")->paginate($request->input("per_page", 10))->onEachSide(-1)->appends($params);

            return Response::json([
                'success'   => true,
                'status'    => HTTP::HTTP_OK,
                'message'   => "Successfully authorized.",
                'data'   => [
                    "bookings" => $bookings,
                ]
            ],  HTTP::HTTP_OK); // HTTP::HTTP_OK
        } catch (\Exception $e) {
            throw $e;
            return Response::json([
                'success'   => false,
                'status'    => HTTP::HTTP_FORBIDDEN,
                'message'   => "Something went wrong.",
                // 'err' => $e->getMessage(),
            ],  HTTP::HTTP_FORBIDDEN); // HTTP::HTTP_OK
        }
    }
}
