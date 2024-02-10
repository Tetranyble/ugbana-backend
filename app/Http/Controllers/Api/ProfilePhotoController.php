<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfilePhotoRequest;
use App\Http\Resources\MediaResource;
use App\Services\FileSystem;
use Illuminate\Http\JsonResponse;

class ProfilePhotoController extends Controller
{
    /**
     * @OA\Post(
     * path="/users/profile-photo",
     * operationId="ProfilePhotoController::__invoke",
     * tags={"Profiles"},
     * summary="Update user profile photo",
     * security={ * {"sanctum": {} } * },
     * description="Update user profile photo",
     *
     *    @OA\RequestBody(
     *         description="upload user profile photo",
     *         required=true,
     *
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             example={
     *                 "photo": "Ugbanawaji",
     *             },
     *
     *             @OA\Schema(ref="#/components/schemas/ProfilePhotoRequest")
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
     *                     "disk" : "s3-public",
     *                     "path": "http://localhost:8000/storage/images/DU8Y739YQWHDLKuhluwqehdluiwked.png",
     *                     "id" : 24,
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
     * @return JsonResponse
     */
    public function __invoke(ProfilePhotoRequest $request, FileSystem $fileSystem)
    {

        $url = $fileSystem->store(
            $request->file('photo'),
            'photo',
            $fileSystem->disk
        );
        $user = $request->user('api');
        $media = $user
            ->media()
            ->updateOrCreate([
                'description' => 'profile-photo',
                'mediable_id' => $user->id,
                'mediable_type' => get_class($user),
            ], [
                'path' => $url,
                'size' => $request->file('photo')->getSize(),
                'disk' => $fileSystem->disk,
                'current' => true,
                'mime_type' => $request->file('photo')->getMimeType(),
            ]);

        return $this->success(
            new MediaResource($media),
            'success'
        );
    }
}
