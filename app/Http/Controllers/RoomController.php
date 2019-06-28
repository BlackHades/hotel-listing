<?php

namespace App\Http\Controllers;

use App\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class RoomController extends Controller
{

    /**
     * This methods handles the creation of a room and image upload
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    function create(Request $request)
    {
        $payload = $request->all();
        $val = Validator::make($payload, [
            "hotel_id" => "required",
            "name" => "required",
            "room_type_id" => "required",
        ]);

        if ($val->fails())
            return response()->json([
                "error" => implode(",", $val->errors()->all())
            ], 422);


        if (!$request->hasFile('image')) {
            return response()->json([
                "error" => "Room Image is required"
            ]);
        }


        $file = $request->file('image');
        $filename = time() . "." . $file->getClientOriginalExtension();
        $file->move(public_path("uploads/"), $filename);
        $payload["image"] = $filename;

        $room = Room::query()->create($payload);
        return response()->json([
            "data" => $room
        ], 201);
    }


    /**
     * This methods handles the update of a room
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    function update(Request $request)
    {
        $payload = $request->all();
        $val = Validator::make($payload, [
            "room_id" => "required",
            "hotel_id" => "required",
            "name" => "required",
            "room_type_id" => "required",
        ]);

        if ($val->fails())
            return response()->json([
                "error" => implode(",", $val->errors()->all())
            ], 422);

        $room = Room::query()->find($payload["room_id"]);

        if(!isset($room))
            return response()->json([
                "error" => "Room Not Found"
            ], 404);


        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . "." . $file->getClientOriginalExtension();
            $file->move(public_path("uploads/"), $filename);
            $payload["image"] = $filename;
        }

//        unset($payload["room_id"]);
        $room = $room->update($payload);
        return response()->json([
            "data" => $room
        ], 200);
    }


    /**
     * This methods handles the fetching of rooms
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    function fetch(Request $request)
    {
        $data = null;

        if (isset($request->hotel_id))
            $data = Room::query()->where(["hotel_id" => $request->hotel_id])->get();

        elseif (isset($request->room_id))
            $data = Room::query()->find($request->room_id);

        else
            $data = Room::all();

        return response()->json([
            "data" => $data
        ]);
    }


    /**
     *
     * Delete Room
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    function delete(Request $request)
    {
        if (!isset($request->room_id))
            return response()->json(["error" => "Room ID is required"]);


        $room = Room::query()->find($request->room_id);

        if(!isset($room))
            return response()->json([
                "error" => "Room Not Found"
            ], 404);

        try {
           $room->delete();
        } catch (\Exception $exception) {
            Log::error($exception->getMessage(), [$exception]);
        }

        return response()->json([
            "message" => "Room Deleted Successfully"
        ]);
    }
}
