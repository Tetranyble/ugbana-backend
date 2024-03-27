<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    /**
     * @OA\Get(
     * path="/users/profile",
     * operationId="Api/ProfileController::invoke",
     * tags={"Profiles"},
     * summary="The authenticated user resources",
     * security={ * {"sanctum": {} } * },
     * description="The authenticated user resources.",
     *
     *     @OA\Response(
     *         response=200,
     *         description="The authenticated user resources.",
     *
     *         @OA\JsonContent(
     *             type="object",
     *             example={
     *                 "message":"success",
     *                 "status": "true",
     *                 "data": {
     *                     "name" : "Ugbanawaji",
     *                     "email" : "leonard@hardeverse.org",
     *                     "id" : 24,
     *                     "created_at" : "2022-09-08T12:29:54.000000Z",
     *                 }
     *             }
     *         )
     *     ),
     *
     *    @OA\Response(response=400, ref="#/components/responses/400"),
     *    @OA\Response(response=403, ref="#/components/responses/403"),
     *    @OA\Response(response=404, ref="#/components/responses/404"),
     *    @OA\Response(response=422, ref="#/components/responses/422"),
     *    @OA\Response(response="default", ref="#/components/responses/500")
     * )
     *
     * Get the authenticated user resource in storage.
     *
     * @param  User  $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(Request $request)
    {
        return $this->success(
            new UserResource($request->user('api')->load('profile')),
            'success'
        );
    }
}
