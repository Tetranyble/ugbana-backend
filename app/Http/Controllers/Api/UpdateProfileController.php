<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Resources\UserResource;

class UpdateProfileController extends Controller
{
    /**
     * @OA\Patch (
     * path="/users/profile",
     * operationId="Api/UpdateProfileController::__invoke",
     * tags={"Profiles"},
     * summary="Update user profile",
     * security={ * {"sanctum": {} } * },
     * description="Update basic user informations.",
     *
     *    @OA\RequestBody(
     *         description="Update user profile basic information.",
     *         required=false,
     *
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             example={
     *                 "firstname": "Ugbanawaji",
     *                 "middlename": "Leonard",
     *                 "lastname" : "Ekenekiso",
     *                 "experience" : "the quick brown fox profile update",
     *                 "introduction" : "the quick brown fox profile update",
     *                 "email" : "l.ekenekiso@ugbanawaji.com",
     *             },
     *
     *             @OA\Schema(ref="#/components/schemas/UpdateProfileRequest")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="The user rehydrated profile record is return with affected fields.",
     *
     *         @OA\JsonContent(
     *             type="object",
     *             example={
     *                 "message":"success",
     *                 "status": "success",
     *                 "status_code": 200,
     *                 "data": {
     *                     "firstname" : "Ugbanawaji",
     *                     "middlename" : "Leonard",
     *                     "lastname" : "Ekenekiso",
     *                     "phone" : "+23480666077**",
     *                     "email" : "leonard@hardeverse.org",
     *                     "path": "http://localhost:8000/storage/images/DU8Y739YQWHDLKuhluwqehdluiwked.png",
     *                     "id" : 24,
     *                     "created_at" : "2022-09-08T12:29:54.000000Z",
     *                     "updated_at" : "2022-09-08T12:29:54.000000Z"
     *                 }
     *             }
     *         )
     *     ),
     *
     *    @OA\Response(response=400, ref="#/components/responses/400"),
     *    @OA\Response(response=404, ref="#/components/responses/404"),
     *    @OA\Response(response=422, ref="#/components/responses/422"),
     *    @OA\Response(response="default", ref="#/components/responses/500")
     * )
     *
     * * Update the specified resource in storage and database.
     *
     * @return mixed
     */
    public function __invoke(UpdateProfileRequest $request)
    {
        $user = $request->user('api');
        $user->fill(
            array_filter($request->all())
        )->save();

        return $this->success(
            new UserResource($user->refresh()),
            'success'
        );
    }
}
