<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Wishlist;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response as HTTP;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\Api\V1\StoreWishlistRequest;
use App\Http\Requests\Api\V1\UpdateWishlistRequest;

class WishlistController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $customer = $request->user("customer");
            $wishlist = Wishlist::where("customer_id", $customer->id)->orderBy("id", "DESC")->get();
            $wishlist = $wishlist->pluck("service_id");
            $services = Service::whereIn("id", $wishlist)->get();

            return Response::json([
                'success'   => true,
                'status'    => HTTP::HTTP_OK,
                'message'   => "Successfully authorized.",
                'data'      => [
                    'wishlist'  => $services,
                ]
            ],  HTTP::HTTP_OK); // HTTP::HTTP_OK
        } catch (\Exception $e) {
            //throw $e;
            return Response::json([
                'success'   => false,
                'status'    => HTTP::HTTP_FORBIDDEN,
                'message'   => "Something went wrong.",
                // 'err' => $e->getMessage(),
            ],  HTTP::HTTP_FORBIDDEN); // HTTP::HTTP_OK
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function register(StoreWishlistRequest $request)
    {
        try {
            $customer = $request->user("customer");
            $service = Service::where("id", $request->service_id)->firstOrFail();
            $wishlist = Wishlist::where("customer_id", $customer->id)->where("service_id", $service->id)->firstOrNew();
            $wishlist->customer_id = $customer->id;
            $wishlist->service_id = $service->id;
            $wishlist->category_id = $request->input("category_id", $service->category_id);
            $wishlist->save();

            return Response::json([
                'success'   => true,
                'status'    => HTTP::HTTP_CREATED,
                'message'   => "Wishlist successfully registered.",
            ],  HTTP::HTTP_CREATED); // HTTP::HTTP_OK
        } catch (\Exception $e) {
            //throw $e;
            return Response::json([
                'success'   => false,
                'status'    => HTTP::HTTP_FORBIDDEN,
                'message'   => "Something went wrong.",
                // 'err' => $e->getMessage(),
            ],  HTTP::HTTP_FORBIDDEN); // HTTP::HTTP_OK
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Wishlist $wishlist)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Wishlist $wishlist)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateWishlistRequest $request, Wishlist $wishlist)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'service_id' => "required|integer|exists:services,id",
        ]);

        if ($validator->fails()) {
            return Response::json([
                'success'   => false,
                'status'    => HTTP::HTTP_UNPROCESSABLE_ENTITY,
                'message'   => "Validation failed.",
                'errors' => $validator->errors()
            ],  HTTP::HTTP_UNPROCESSABLE_ENTITY); // HTTP::HTTP_OK
        }

        try {
            $customer = $request->user("customer");
            $wishlist = Wishlist::where("customer_id", $customer->id)->where("service_id", $request->service_id)->delete();

            return Response::json([
                'success'   => true,
                'status'    => HTTP::HTTP_OK,
                'message'   => "Wishlist successfully deleted.",
            ],  HTTP::HTTP_OK); // HTTP::HTTP_OK
        } catch (\Exception $e) {
            //throw $e;
            return Response::json([
                'success'   => false,
                'status'    => HTTP::HTTP_FORBIDDEN,
                'message'   => "Something went wrong.",
                // 'err' => $e->getMessage(),
            ],  HTTP::HTTP_FORBIDDEN); // HTTP::HTTP_OK
        }
    }
}
