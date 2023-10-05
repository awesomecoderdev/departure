<?php

namespace App\Http\Controllers\Api\v1;

use App\Models\Service;
use App\Models\Facility;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response as HTTP;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\Api\V1\StoreFacilityRequest;

class GuideServiceFacilityController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function register(StoreFacilityRequest $request)
    {
        try {
            $guide = $request->user("guide");
            if ($guide->agency_id != 0) {
                return Response::json([
                    'success'   => false,
                    'status'    => HTTP::HTTP_FORBIDDEN,
                    'message'   => "You are not allowed to create facility.",
                ],  HTTP::HTTP_FORBIDDEN); // HTTP::HTTP_OK
            }

            $service = Service::where("guide_id", $guide->id)->where("id", $request->service_id)->firstOrFail();

            $facility = new Facility();
            $facility->title  = $request->title;
            $facility->guide_id  = $guide->id;
            $facility->service_id  = $service->id;
            $facility->icon_id  = $request->icon_id;
            $facility->description  = $request->description;
            $facility->save();

            return Response::json([
                'success'   => true,
                'status'    => HTTP::HTTP_CREATED,
                'message'   => "Facility successfully created.",
            ],  HTTP::HTTP_CREATED); // HTTP::HTTP_OK
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        try {
            $guide = $request->user("guide");

            if ($guide->agency_id != 0) {
                return Response::json([
                    'success'   => false,
                    'status'    => HTTP::HTTP_FORBIDDEN,
                    'message'   => "You are not allowed to delete facility.",
                ],  HTTP::HTTP_FORBIDDEN); // HTTP::HTTP_OK
            }



            $facility = Facility::where("guide_id", $guide->id)->where("id", $request->facility)->firstOrFail();
            $facility->delete();

            return Response::json([
                'success' => true,
                'status' => HTTP::HTTP_OK,
                'message' => "Facility successfully deleted.",
            ], HTTP::HTTP_OK);
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
