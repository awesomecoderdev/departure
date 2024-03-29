<?php

namespace App\Http\Controllers\Api\V1;

use Exception;
use Carbon\Carbon;
use App\Models\Review;
use App\Models\Booking;
use App\Models\Service;
use App\Models\Facility;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use PHPUnit\Event\Code\Throwable;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Response as HTTP;
use Illuminate\Support\Facades\Cache;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\Api\V1\StoreServiceRequest;
use App\Http\Requests\Api\V1\UpdateServiceRequest;
use Illuminate\Contracts\Filesystem\FileNotFoundException;

class AgencyServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $category = $request->category_id;
        $validator = Validator::make($request->all(), [
            'category_id' => 'nullable|integer|exists:categories,id',
            'per_page' => 'nullable|integer',
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
            $agency = $request->user("agency");

            $params = Arr::only($request->input(), ["category_id"]);
            $services = Service::with(["facilities.icon"])->where("agency_id", $agency->id)->when($category, function ($query) use ($category) {
                return $query->where('category_id', $category);
            })->orderBy("id", "DESC")->paginate($request->input("per_page", 10))->onEachSide(-1)->appends($params);

            return Response::json([
                'success'   => true,
                'status'    => HTTP::HTTP_OK,
                'message'   => "Successfully authorized.",
                'data'      => [
                    'services'  => $services,
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
    public function register(StoreServiceRequest $request)
    {
        try {
            $agency = $request->user("agency");

            $service = new Service();
            $service->name  = $request->name;
            $service->price  = $request->price;
            $service->agency_id  = $agency->id;
            $service->short_description  = $request->short_description;
            $service->long_description  = $request->long_description;
            $service->address  = $request->address;
            $service->discount  = $request->input("discount", 0);
            // $service->image  = $request->image;
            // $service->booking_count  = $request->booking_count;
            $service->booking_count  = 0;

            // $service->thumbnail  = $request->thumbnail;
            // $service->metadata  = $request->metadata;
            $service->save();

            // Handle image upload and update
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = "service_$service->id.png";
                $imagePath = "assets/images/service/$imageName";

                try {
                    // Create the "public/images" directory if it doesn't exist
                    if (!File::isDirectory(public_path("assets/images/service"))) {
                        File::makeDirectory((public_path("assets/images/service")), 0777, true, true);
                    }

                    // Save the image to the specified path
                    $image->move(public_path('assets/images/service'), $imageName);

                    // Save the main image to the specified path, resize it to 200x200 pixels
                    // Image::make($image)->resize(200, 200)->save(public_path($imagePath));

                    $service->image = $imagePath;
                    $service->save();
                } catch (\Exception $e) {
                    // throw $e;
                    // skip if not uploaded
                }
            }


            // Check if there are thumbnail images in the request
            if ($request->hasFile('thumbnail')) {
                // Create the "public/images" directory if it doesn't exist
                if (!File::isDirectory(public_path("assets/images/service/thumbnails/{$service->id}"))) {
                    File::makeDirectory((public_path("assets/images/service/thumbnails/{$service->id}")), 0777, true, true);
                }

                $images = [];
                foreach ($request->file('thumbnail') as $key => $image) {
                    $key = md5(time() . rand(1, 1000));
                    $imageName = "thumbnail_{$service->id}_{$key}.png";
                    $imagePath = "assets/images/service/thumbnails/{$service->id}/{$imageName}";

                    if (count($images) > 4) {
                        break; // skip if images is more then 5
                    }

                    try {

                        // Save the image to the specified path
                        $image->move(public_path("assets/images/service/thumbnails/{$service->id}"), $imageName);

                        // Save the main image to the specified path, resize it to 200x200 pixels
                        // Image::make($image)->resize(200, 200)->save(public_path($imagePath));

                        $images[] = asset($imagePath);
                    } catch (\Exception $e) {
                        // throw $e;
                        // skip if not uploaded
                    }
                }

                // Set the "thumbnail" attribute as an array of relative paths
                $service->thumbnail = $images;
                $service->save();
            }
            // Save the Service instance to the database

            return Response::json([
                'success'   => true,
                'status'    => HTTP::HTTP_CREATED,
                'message'   => "Service successfully registered.",
                "data"      => [
                    "service" => $service,
                ]
            ],  HTTP::HTTP_CREATED); // HTTP::HTTP_OK
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Display the specified resource.
     */
    public function details(Request $request)
    {
        try {
            $agency = $request->user("agency");
            $service = Service::with(["facilities"])->where("agency_id", $agency->id)->where("id", $request->service)->firstOrFail();
            $service->facilities->load("icon");

            return Response::json([
                'success'   => true,
                'status'    => HTTP::HTTP_OK,
                'message'   => "Successfully authorized.",
                'data'      => [
                    'service'  => $service
                ]
            ],  HTTP::HTTP_OK); // HTTP::HTTP_OK
        } catch (\Exception $e) {
            throw $e;
            // return Response::json([
            //     'success'   => false,
            //     'status'    => HTTP::HTTP_FORBIDDEN,
            //     'message'   => "Something went wrong.",
            //     'err' => $e->getMessage(),
            // ],  HTTP::HTTP_FORBIDDEN); // HTTP::HTTP_OK
        }
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateServiceRequest $request)
    {

        try {
            $agency = $request->user("agency");
            $service = Service::where("agency_id", $agency->id)->where("id", $request->service)->firstOrFail();

            $service->name  = $request->input("name", $service->name);
            $service->price  = $request->input("price", $service->price);
            $service->short_description  = $request->input("short_description", $service->short_description);
            $service->long_description  = $request->input("long_description", $service->long_description);
            $service->address  = $request->input("address", $service->address);
            $service->discount  = $request->input("discount", $service->discount ?? 0);
            $service->save();

            // Handle image upload and update
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = "service_$service->id.png";
                $imagePath = "assets/images/service/$imageName";

                try {
                    // Create the "public/images" directory if it doesn't exist
                    if (!File::isDirectory(public_path("assets/images/service"))) {
                        File::makeDirectory((public_path("assets/images/service")), 0777, true, true);
                    }

                    // Save the image to the specified path
                    $image->move(public_path('assets/images/service'), $imageName);

                    // Save the main image to the specified path, resize it to 200x200 pixels
                    // Image::make($image)->resize(200, 200)->save(public_path($imagePath));

                    $service->image = $imagePath;
                    $service->save();
                } catch (\Exception $e) {
                    // throw $e;
                    // skip if not uploaded
                }
            }


            // Check if there are thumbnail images in the request
            if ($request->hasFile('thumbnail')) {
                // Create the "public/images" directory if it doesn't exist
                if (!File::isDirectory(public_path("assets/images/service/thumbnails/{$service->id}"))) {
                    File::makeDirectory((public_path("assets/images/service/thumbnails/{$service->id}")), 0777, true, true);
                }

                $images = $service?->thumbnail?->toArray() ?? [];
                foreach ($request->file('thumbnail') as $key => $image) {
                    $key = md5(time() . rand(1, 1000));
                    $imageName = "thumbnail_{$service->id}_{$key}.png";
                    $imagePath = "assets/images/service/thumbnails/{$service->id}/{$imageName}";

                    if (count($images) > 4) {
                        break; // skip if images is more then 5
                    }

                    try {

                        // Save the image to the specified path
                        $image->move(public_path("assets/images/service/thumbnails/{$service->id}"), $imageName);

                        // Save the main image to the specified path, resize it to 200x200 pixels
                        // Image::make($image)->resize(200, 200)->save(public_path($imagePath));

                        $images[] = asset($imagePath);
                    } catch (\Exception $e) {
                        // throw $e;
                        // skip if not uploaded
                    }
                }

                $images = array_unique(array_values($images));
                // Set the "thumbnail" attribute as an array of relative paths
                $service->thumbnail = $images;
                $service->save();
            }
            // Save the Service instance to the database

            return Response::json([
                'success'   => true,
                'status'    => HTTP::HTTP_OK,
                'message'   => "Service successfully updated.",
                "data"      => [
                    "service" => $service,
                ]
            ],  HTTP::HTTP_OK); // HTTP::HTTP_OK

        } catch (\Throwable $e) {
            throw $e;
            // return Response::json([
            //     'success'   => false,
            //     'status'    => HTTP::HTTP_FORBIDDEN,
            //     'message'   => "Something went wrong.",
            //     'err' => $e->getMessage(),
            // ],  HTTP::HTTP_FORBIDDEN); // HTTP::HTTP_OK
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        try {
            $agency = $request->user("agency");
            $service = Service::where("agency_id", $agency->id)->where("id", $request->service)->firstOrFail();

            if (!empty($service->image)) {
                if (strpos($service->image, "assets/images/service") !== false) {
                    $path = str_replace(asset("/"), "", $service->image);
                    $imagePath = public_path($path);
                    if (file_exists($imagePath)) {
                        unlink($imagePath);
                    }
                }
            }

            if ($service->thumbnail) {
                // Check if the folder exists before attempting to delete it
                $directory = "assets/images/service/thumbnails/$service->id";
                if (File::exists($directory)) {
                    // Use the `deleteDirectory` method to recursively delete the folder and its contents
                    File::deleteDirectory($directory);

                    // Optionally, you can check if the folder was successfully deleted
                    if (!File::exists($directory)) {
                        foreach ($service->thumbnail as $key => $thumbnail) {
                            if (strpos($thumbnail, "assets/images/service/thumbnails") !== false) {
                                $path = str_replace(asset("/"), "", $thumbnail);

                                $imagePath = public_path($path);
                                if (file_exists($imagePath)) {
                                    unlink($imagePath);
                                }
                            }
                        }

                        File::deleteDirectory($directory);
                    }
                }
            }

            // delete facility
            Facility::where("service_id", $service->id)->delete();

            $service->delete();

            return Response::json([
                'success' => true,
                'status' => HTTP::HTTP_OK,
                'message' => "Service successfully deleted.",
            ], HTTP::HTTP_OK);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function thumbnail(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'thumbnail_id' => 'required|integer',
            ]);

            if ($validator->fails()) {
                return Response::json([
                    'success'   => false,
                    'status'    => HTTP::HTTP_UNPROCESSABLE_ENTITY,
                    'message'   => "Validation failed.",
                    'errors' => $validator->errors()
                ],  HTTP::HTTP_UNPROCESSABLE_ENTITY); // HTTP::HTTP_OK
            }

            $agency = $request->user("agency");
            $service = Service::where("agency_id", $agency->id)->where("id", $request->service)->firstOrFail();
            $thumbnails = $service?->thumbnail?->toArray() ?? [];

            if (isset($thumbnails[$request->thumbnail_id])) {
                $thumbnail = $thumbnails[$request->thumbnail_id];

                if (strpos($thumbnail, "assets/images/service/thumbnails") !== false) {
                    $path = str_replace(asset("/"), "", $thumbnail);

                    $imagePath = public_path($path);
                    if (file_exists($imagePath)) {
                        unlink($imagePath);
                    }

                    unset($thumbnails[$request->thumbnail_id]);

                    $service->thumbnail = array_values($thumbnails) ?? [];
                    $service->save();

                    return Response::json([
                        'success' => true,
                        'status' => HTTP::HTTP_OK,
                        'message' => "Service successfully deleted.",
                        'data'      => [
                            'service'  => $service
                        ]
                    ], HTTP::HTTP_OK);
                }
            }

            return Response::json([
                'success'   => false,
                'status'    => HTTP::HTTP_NOT_FOUND,
                'message'   => "Thumbnail dose not exist.",
            ],  HTTP::HTTP_NOT_FOUND); // HTTP::HTTP_OK
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
