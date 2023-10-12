<?php

namespace App\Http\Controllers\Api\V1;

use Carbon\Carbon;
use App\Models\Guide;
use App\Models\Booking;
use App\Models\Service;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response as HTTP;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Guid\Guid;

class CustomerGuideController extends Controller
{
    /**
     * Retrieve guide info.
     */
    public function guide(Request $request)
    {
        try {
            // Get the user's id from token header and get his provider guides
            $customer = $request->user("customer");

            // status
            $params = Arr::only($request->input(), ["per_page"]);
            $guides = Guide::withCount([
                "service",
                "review",
            ])->with([
                "service",
                "review",
            ])->where("status", true)->orderBy("id", "DESC")->paginate($request->input("per_page", 10))->onEachSide(-1)->appends($params);

            return Response::json([
                'success'   => true,
                'status'    => HTTP::HTTP_OK,
                'message'   => "Successfully authorized.",
                'data'   => [
                    "guides" => $guides,
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

    /**
     * Retrieve top rated guide info.
     */
    public function top(Request $request)
    {
        try {
            // Get the user's id from token header and get his provider guides
            $customer = $request->user("customer");

            $guides = Guide::withCount([
                "service",
                "review",
            ])->with([
                "service",
                "review",
            ])->where("status", true)->where("rating_count", ">", 100)->orderBy("rating_count", "DESC")->limit(10)->get();

            return Response::json([
                'success'   => true,
                'status'    => HTTP::HTTP_OK,
                'message'   => "Successfully authorized.",
                'data'   => [
                    "guides" => $guides,
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

    /**
     * Retrieve top rated guide info.
     */
    public function details(Request $request)
    {
        try {
            // Get the user's id from token header and get his provider guides
            $customer = $request->user("customer");

            $guide = Guide::with([
                "service",
                "review",
            ])->where("id", $request->guide)->where("status", true)->get();

            return Response::json([
                'success'   => true,
                'status'    => HTTP::HTTP_OK,
                'message'   => "Successfully authorized.",
                'data'   => [
                    "guide" => $guide,
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
