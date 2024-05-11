<?php

namespace App\Http\Controllers;

use App\Models\User;
use DateTime;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserController extends Controller
{
    public function create(Request $request)
    {
        try {
            $rules = [
                'first_name' => 'required',
                'last_name' => 'required',
                'date_of_birth' => 'required',
                'gender' => 'required',
                'email' => 'required',
                'password' => 'required',
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json([
                    "message" => $validator->messages(),
                    "status" => 422
                ], 422);
            }
            $findExsisting = User::where("email", $request->email)->first();
            if ($findExsisting) {
                return response()->json([
                    "message" => "user with this email already exsists",
                    "status" => 409
                ], 409);
            }


            $newUser = User::create([
                "first_name" => $request->first_name,
                "last_name" => $request->last_name,
                "email" => $request->email,
                "password" => bcrypt($request->password),
                "gender" => $request->gender,
                "date_of_birth" => DateTime::createFromFormat('Y-m-d', $request->date_of_birth),
            ]);
            $newUser->save();
            return response()->json([
                "message" => "user inserted",
                "result" => $newUser,
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
    public function login(Request $request)
    {
        try {
            $rules = [
                'email' => 'required|email',
                'password' => 'required',
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json([
                    "message" => $validator->messages(),
                    "status" => 422
                ], 422);
            }
            $findExsisting = User::where("email", $request->email)->first();
            if (!$findExsisting) {
                return response()->json([
                    "message" => "user with this email does not exsists",
                    "status" => 409
                ], 404);
            }
            if (!Hash::check($request->password, $findExsisting->password)) {
                return response()->json([
                    "message" => "Incorrect password",
                    "status" => 409
                ], 409);
            }
            $token = JWTAuth::attempt(['email' => $request->email, 'password' => $request->password]);


            return response()->json([
                "message" => "logged in sucessfull",
                "result" => $findExsisting,
                "token" => $token,
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

            // Get filter parameters from the request
            $filters = $request->only(['first_name', 'last_name', 'email', 'gender', 'date_of_birth']);

            // Build the query based on the filters
            $query = User::query();
            foreach ($filters as $field => $value) {
                if ($value !== null) { // Only add filter if value is provided
                    $query->where($field, '=', $value); // Properly quote the value
                }
            }

            // Apply pagination to the filtered query
            $results = $query->skip($skip)->paginate($perPage);

            // Get total count for pagination
            // $totalResults = $query->count();

            return response()->json([
                'results' => $results,
                // 'total' => $totalResults,
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
            $user = User::where('id', $id)->first();
            if (empty($user)) {
                return response()->json(['message' => 'user does not exsists'], 404);
            }
            // Retrieve paginated results using the paginate() method

            return response()->json([
                'results' => $user,
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
            if (!$id) {
                return response()->json(["message" => "id is required", 'status' => 409], 409);
            }
            $user = User::where("id", $id)->first();
            if (empty($user)) {
                return response()->json(["message" => "user does not exsists", 'status' => 404], 404);
            }
            $user->update($request->only([
                'first_name',
                'last_name',
                'date_of_birth',
                'gender',
                'email',
                'password',
            ]));
            $user->save();

            // Return success response
            return response()->json([
                "message" => "User updated successfully",
                "result" => $user,
                "status" => 200
            ], 200);
        } catch (Exception $e) {
            // Handle other exceptions
            echo "An error occurred: " . $e->getMessage();
        }
        return "in update";
    }
    public function destroy(Request $request)
    {
        try {
            $id = $request->input("id");
            if (!$id) {
                return response()->json(["message" => "id is required"], 404);
            }
            $user = User::where("id", $id)->first();
            if (empty($user)) {
                return response()->json(["message" => "user not found"], 404);
            }
            $user->delete();
            return response()->json([
                "message" => "user deleted",
                "result" => $user,
                "status" => 200
            ], 200);
        } catch (Exception $e) {
            // Handle other exceptions
            echo "An error occurred: " . $e->getMessage();
        }
        return "in destroy";
    }
}
