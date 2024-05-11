<?php

namespace App\Http\Controllers;

use App\Models\Projects;
use App\Models\TimeSheet;
use App\Models\User;
use DateTime;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class TimeSheetController extends Controller
{
    public function add(Request $request)
    {
        try {
            $rules = [
                'task_name' => 'required',
                'date' => 'required',
                'hours' => 'required',
                'user_id' => 'required',
                'project_id' => 'required',
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json([
                    "message" => $validator->messages(),
                    "status" => 422
                ], 422);
            }
            $findExsistingUser = User::where("id", $request->user_id)->first();
            if (!$findExsistingUser) {
                return response()->json([
                    "message" => "user with this id does not exsists",
                    "status" => 409
                ], 409);
            }
            $findExsistingProject = Projects::where("id", $request->project_id)->first();
            if (!$findExsistingProject) {
                return response()->json([
                    "message" => "project with this id does not exsists",
                    "status" => 409
                ], 409);
            }

            $newTimeSheet = TimeSheet::create([
                "task_name" => $request->task_name,
                "date" => DateTime::createFromFormat('Y-m-d', $request->date),
                "time" => DateTime::createFromFormat('H:i', $request->hours),
                "user_id" => $request->user_id,
                "project_id" => $request->project_id,
            ]);
            $newTimeSheet->save();
            return response()->json([
                "message" => "data inserted",
                "result" => $newTimeSheet,
                "status" => 200
            ], 200);
        } catch (Exception $e) {
            // Handle other exceptions
            return response()->json([
                "message" => "An error occurred: " . $e->getMessage(),
                "status" => 500
            ], 500);
        }
    }
    public function get(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 10);
            $page = $request->input('page', 1);
            $skip = ($page - 1) * $perPage;

            $filters = $request->only(['task_name', 'date', 'time', 'user_id', 'project_id']);

            $query = TimeSheet::query()->with(['user', 'project']);;
            foreach ($filters as $field => $value) {
                if ($value !== null) {
                    $query->where($field, '=', $value);
                }
            }

            $results = $query->skip($skip)->paginate($perPage);

            return response()->json([
                'results' => $results,
                'status' => 200
            ], 200);
        } catch (Exception $e) {
            // Handle other exceptions
            return response()->json([
                'error' => 'An error occurred: ' . $e->getMessage(),
                'status' => 500
            ], 500);
        }
    }


    public function getByID($id)
    {
        try {
            if (!$id) {
                return response()->json(['message' => 'id is required', 'status' => 409], 409);
            }
            $data = TimeSheet::where('id', $id)->with('user', 'project')->first();

            if (empty($data)) {
                return response()->json(['message' => 'record does not exsists'], 404);
            }

            return response()->json([
                'results' => $data,
                'status' => 200
            ], 200);
        } catch (Exception $e) {
            // Handle other exceptions
            echo "An error occurred: " . $e->getMessage();
        }
    }
    public function update(Request $request)
    {
        try {
            $id = $request->input("id");
            $timeSheet = TimeSheet::find($id);

            if (!$timeSheet) {
                return response()->json([
                    "message" => "TimeSheet not found",
                    "status" => 404
                ], 404);
            }

            $fieldsToUpdate = array_filter($request->only(['task_name', 'date', 'hours', 'user_id', 'project_id']));

            $timeSheet->fill($fieldsToUpdate);

            $timeSheet->save();

            return response()->json([
                "message" => "TimeSheet updated",
                "result" => $timeSheet,
                "status" => 200
            ], 200);
        } catch (Exception $e) {
            // Handle other exceptions
            return response()->json([
                "message" => "An error occurred: " . $e->getMessage(),
                "status" => 500
            ], 500);
        }
    }

    public function destroy(Request $request)
    {
        try {
            $id = $request->input("id");
            if (!$id) {
                return response()->json(["message" => "id is required"], 404);
            }
            $data = TimeSheet::where("id", $id)->first();
            if (empty($data)) {
                return response()->json(["message" => "record not found"], 404);
            }
            $data->delete();
            return response()->json([
                "message" => "record deleted",
                "result" => $data,
                "status" => 200
            ], 200);
        } catch (Exception $e) {
            // Handle other exceptions
            echo "An error occurred: " . $e->getMessage();
        }
        return "in destroy";
    }
}
