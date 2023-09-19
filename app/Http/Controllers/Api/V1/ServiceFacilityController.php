<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Facility;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response as HTTP;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\Api\V1\StoreFacilityRequest;
use App\Models\Service;

class ServiceFacilityController extends Controller
{

    /**
     * Store a newly created resource in storage.
     */
    public function register(StoreFacilityRequest $request)
    {
        try {
            $agency = $request->user("agency");
            $service = Service::where("agency_id", $agency->id)->where("id", $request->service_id)->firstOrFail();

            $facility = new Facility();
            $facility->title  = $request->title;
            $facility->agency_id  = $agency->id;
            $facility->service_id  = $service->id;
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
            $agency = $request->user("agency");
            $facility = Facility::where("agency_id", $agency->id)->where("id", $request->facility)->firstOrFail();
            $facility->delete();

            return response()->json([
                'success' => true,
                'status' => HTTP::HTTP_OK,
                'message' => "Facility successfully deleted.",
            ], HTTP::HTTP_OK);
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
