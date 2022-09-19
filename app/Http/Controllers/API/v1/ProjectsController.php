<?php

namespace App\Http\Controllers\API\v1;

use App\Helpers\JSON;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class ProjectsController extends Controller
{
    /**
     * Return JSON response for this API endpoint
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $user = auth('api')->user(); // Grab the user

        $projects = $user
            ->projectsWithRoles
            ->map(function (Project $project) {
                $project->makeVisible('role');

                return $project;
            });

        return JSON::success(data: compact('projects'));
    }

    public function update(Request $request, Project $project)
    {
        $validation = Validator::make($request->all(), [
            'concept'       => ['required', 'numeric', 'min:0', 'max:100'],
            'development'   => ['required', 'numeric', 'min:0', 'max:100'],
            'documentation' => ['required', 'numeric', 'min:0', 'max:100'],
            'commissioning' => ['required', 'numeric', 'min:0', 'max:100'],
            'workload'      => ['required', 'numeric', 'min:0', 'max:100'],
            'progress'      => ['required', 'numeric', 'min:0', 'max:100'],
        ]);

        if ($validation->fails())
            return JSON::error(
                'Missing or invalid data provided!',
                $validation->errors(),
                Response::HTTP_BAD_REQUEST
            );

        $user = auth('api')->user(); // Grab the user

        // Only admin and project leader can update settings!
        if ($user->isAdmin || $project->leader_id !== $user->id)
            return JSON::error(
                message: 'Only project leaders can update settings!',
                status: Response::HTTP_UNAUTHORIZED
            );

        $project->update($request->only([
            'concept',
            'development',
            'documentation',
            'commissioning',
            'workload',
            'progress',
        ]));

        return JSON::success(
            'The project details were updated successfully!',
            compact('project')
        );
    }
}
