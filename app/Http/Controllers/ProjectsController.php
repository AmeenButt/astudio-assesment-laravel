<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Models\Projects;
use App\Models\User;

use Illuminate\Support\Facades\Validator;

class ProjectsController extends Controller
{
    public function add(Request $request)
    {
        try {
            $rules = [
                'name' => 'required',
                'department' => 'required',
                'start_date' => 'required',
                'end_date' => 'required',
                'users' => 'nullable|array',
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json([
                    "message" => $validator->messages(),
                    "status" => 422
                ], 422);
            }
            $users = [];
            if ($request->users) {
                foreach ($request->users as $projectId) {
                    $findUser = User::find($projectId);
                    if (!$findUser) {
                        return response()->json(["message" => "One or more projects in the array do not exist in the database"], 404);
                    } else {
                        $users[] = $findUser->id;
                    }
                }
            }
            $findExsisting = Projects::where("name", $request->name)->first();
            if ($findExsisting) {
                return response()->json([
                    "message" => "Projects with this name already exsists",
                    "status" => 409
                ], 409);
            }


            $newProjects = Projects::create([
                "name" => $request->name,
                "department" => $request->department,
                "start_date" => $request->start_date,
                "end_date" => $request->end_date,
                "users" => array_unique($users),
            ]);
            $newProjects->save();
            return response()->json([
                "message" => "Projects inserted",
                "result" => $newProjects,
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

            $filters = $request->only(['name', 'department', 'start_date', 'end_date']);

            $query = Projects::query();
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
            $project = Projects::where('id', $id)->first();
            if (empty($project)) {
                return response()->json(['message' => 'project does not exsists'], 404);
            }

            return response()->json([
                'results' => $project,
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
            $id = $request->id;
            $rules = [
                'name' => 'sometimes|required',
                'department' => 'sometimes|required',
                'start_date' => 'sometimes|required',
                'end_date' => 'sometimes|required',
                'projects' => 'nullable|array',
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json([
                    "message" => $validator->messages(),
                    "status" => 422
                ], 422);
            }

            // Find the project by ID
            $project = Projects::find($id);
            if (!$project) {
                return response()->json([
                    "message" => "Project not found",
                    "status" => 404
                ], 404);
            }

            // Update fields if provided
            if ($request->has('name')) {
                $project->name = $request->name;
            }
            if ($request->has('department')) {
                $project->department = $request->department;
            }
            if ($request->has('start_date')) {
                $project->start_date = $request->start_date;
            }
            if ($request->has('end_date')) {
                $project->end_date = $request->end_date;
            }

            // Update projects if provided
            if ($request->has('projects')) {
                $projects = [];
                foreach ($request->projects as $projectId) {
                    $findUser = User::find($projectId);
                    if (!$findUser) {
                        return response()->json(["message" => "One or more projects in the array do not exist in the database"], 404);
                    } else {
                        $projects[] = $findUser->id;
                    }
                }
                $project->projects = array_unique($projects);
            }

            // Save the changes
            $project->save();

            return response()->json([
                "message" => "Project updated",
                "result" => $project,
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
            $project = Projects::where("id", $id)->first();
            if (empty($project)) {
                return response()->json(["message" => "project not found"], 404);
            }
            $project->delete();
            return response()->json([
                "message" => "project deleted",
                "result" => $project,
                "status" => 200
            ], 200);
        } catch (Exception $e) {
            // Handle other exceptions
            echo "An error occurred: " . $e->getMessage();
        }
        return "in destroy";
    }
}
