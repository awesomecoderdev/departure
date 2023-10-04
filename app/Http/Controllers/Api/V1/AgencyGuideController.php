<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Guide;
use App\Models\Agency;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Events\RegisteredAgency;
use App\Events\RegisteredGuide;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Intervention\Image\Facades\Image;
use Illuminate\Http\Response as HTTP;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\Api\V1\GuideResource;
use App\Http\Requests\Api\V1\StoreGuideRequest;
use App\Http\Requests\Api\V1\UpdateAgencyGuideRequest;
use App\Http\Requests\Api\V1\UpdateGuideRequest;

class AgencyGuideController extends Controller
{
    /**
     * Retrieve guide info.
     */
    public function guide(Request $request)
    {
        try {
            $agency = $request->user('agency');
            $agency->load(["guides"]);
            // $guide->load(["address", "bookings"]);
            return Response::json([
                'success'   => true,
                'status'    => HTTP::HTTP_OK,
                'message'   => "Successfully authorized.",
                'data'      => [
                    "guides" => $agency->guides
                ],
            ],  HTTP::HTTP_OK); // HTTP::HTTP_OK
        } catch (\Exception $e) {
            //throw $e;
            return Response::json([
                'success'   => false,
                'status'    => HTTP::HTTP_FORBIDDEN,
                'message'   => "Something went wrong.",
                'err' => $e->getMessage(),
            ],  HTTP::HTTP_FORBIDDEN); // HTTP::HTTP_OK
        }
    }

    /**
     * Crete a newly created guide in database.
     */
    public function register(StoreGuideRequest $request)
    {
        try {
            $agency = $request->user('agency');
            // If the guide is not registered, proceed with registration
            $guide = new Guide();

            $guide->agency_id = $agency->id;
            $guide->first_name = $request->first_name;
            $guide->last_name = $request->last_name;
            $guide->phone = $request->phone;
            $guide->email = $request->email;
            $guide->password = Hash::make($request->password);
            $guide->city = $request->city;
            $guide->country = $request->country;
            $guide->provider = strtolower($request->input("provider", "credential"));
            $guide->provider_id = $request->provider_id;
            $guide->access_token = $request->access_token;
            $guide->firebase_token = $request->firebase_token;
            $guide->save();


            event(new RegisteredGuide($guide));

            // Handle image upload and update
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = "guide_$guide->id.png";
                $imagePath = "assets/images/guide/$imageName";

                try {
                    // Create the "public/images" directory if it doesn't exist
                    if (!File::isDirectory(public_path("assets/images/guide"))) {
                        File::makeDirectory((public_path("assets/images/guide")), 0777, true, true);
                    }

                    // Save the image to the specified path
                    $image->move(public_path('assets/images/guide'), $imageName);

                    // Save the main image to the specified path, resize it to 200x200 pixels
                    // Image::make($image)->resize(200, 200)->save(public_path($imagePath));

                    $guide->image = $imagePath;
                    $guide->save();
                } catch (\Exception $e) {
                    //throw $e;
                    // skip if not uploaded
                }
            }

            return Response::json([
                'success'   => true,
                'status'    => HTTP::HTTP_CREATED,
                'message'   => "Successfully registered.",
            ],  HTTP::HTTP_CREATED); // HTTP::HTTP_OK
        } catch (\Exception $e) {
            //throw $e;
            return Response::json([
                'success'   => false,
                'status'    => HTTP::HTTP_FORBIDDEN,
                'message'   => "Something went wrong.",
                'err' => $e->getMessage(),
            ],  HTTP::HTTP_FORBIDDEN); // HTTP::HTTP_OK
        }
    }

    /**
     * Update guide data database.
     */
    public function update(UpdateAgencyGuideRequest $request)
    {
        try {
            // get guide
            $agency = $request->user('agency');
            $guide = Guide::where("agency_id", $agency->id)->where("id", $request->guide)->firstOrFail();

            $credentials = Arr::only($request->all(), [
                // "agency_id",
                "guide_id",
                "first_name",
                "last_name",
                "email",
                "password",
                "phone",
                // 'image',
                "city",
                "country",
                // "metadata",
                // "provider",
                "provider_id",
                "access_token",
                // "email_verified_at",
                "firebase_token",
                // "status",
            ]);

            $guide->agency_id = $agency->id;
            $guide->first_name = $request->input("first_name", $guide->first_name);
            $guide->last_name = $request->input("last_name", $guide->last_name);
            $guide->email = $request->input("email", $guide->email);
            if ($request->filled("password")) {
                $guide->password = Hash::make($request->password);
            }
            $guide->phone = $request->input("phone", $guide->phone);
            $guide->city = $request->input("city", $guide->city);
            $guide->country = $request->input("country", $guide->country);
            $guide->access_token = $request->input("access_token", $guide->access_token);
            $guide->firebase_token = $request->input("firebase_token", $guide->firebase_token);

            // Handle image upload and update
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = "guide_$guide->id.png";
                $imagePath = "assets/images/guide/$imageName";

                try {
                    // Create the "public/images" directory if it doesn't exist
                    if (!File::isDirectory(public_path("assets/images/guide"))) {
                        File::makeDirectory((public_path("assets/images/guide")), 0777, true, true);
                    }

                    // Save the image to the specified path
                    $image->move(public_path('assets/images/guide'), $imageName);
                    $guide->image = $imagePath;
                } catch (\Exception $e) {
                    // throw $e;
                    // skip if not uploaded
                }
            }


            // Update the guide data
            $guide->save();

            return Response::json([
                'success'   => true,
                'status'    => HTTP::HTTP_OK,
                'message'   => "Profile updated successfully.",
            ],  HTTP::HTTP_OK); // HTTP::HTTP_OK
        } catch (\Exception $e) {
            throw $e;
            return Response::json([
                'success'   => false,
                'status'    => HTTP::HTTP_FORBIDDEN,
                'message'   => "Something went wrong.",
                'err' => $e->getMessage(),
            ],  HTTP::HTTP_FORBIDDEN); // HTTP::HTTP_OK
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(Guide $guide)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete(Request $request)
    {
        try {
            // get guide
            $agency = $request->user('agency');
            $guide = Guide::where("agency_id", $agency->id)->where("id", $request->guide)->firstOrFail();
            $guide->delete();

            return Response::json([
                'success'   => true,
                'status'    => HTTP::HTTP_OK,
                'message'   => "Guide deleted successfully.",
            ],  HTTP::HTTP_OK); // HTTP::HTTP_OK
        } catch (\Exception $e) {
            throw $e;
            return Response::json([
                'success'   => false,
                'status'    => HTTP::HTTP_FORBIDDEN,
                'message'   => "Something went wrong.",
                'err' => $e->getMessage(),
            ],  HTTP::HTTP_FORBIDDEN); // HTTP::HTTP_OK
        }
    }
}
