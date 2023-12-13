<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegistrationRequest;
use App\Http\Resources\UserResource;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RegistrationController extends Controller
{
    /**
     * @OA\Post(
     * path="/signup",
     * operationId="RegisterationController::__invoke",
     * tags={"Authentication"},
     * summary="User Registeration",
     * description="User Registers here",
     *
     *    @OA\RequestBody(
     *         description="Create new user",
     *         required=true,
     *
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             example={
     *                 "name": "Ugbanawaji Ekenekiso",
     *                 "email" : "e.ugabanwaji@interview.com",
     *                 "password" : "password",
     *                 "password_confirmation" : "password",
     *                  "permissions": {"userprofile_store","userprofile_show"}
     *             },
     *
     *             @OA\Schema(ref="#/components/schemas/RegistrationRequest")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=201,
     *         description="success",
     *
     *         @OA\JsonContent(
     *             type="object",
     *             example={
     *                 "message":"success.",
     *                 "status": true,
     *                 "data": {
     *                     "firstname" : "Ugbanawaji",
     *                     "lastname" : "Ekenekiso",
     *                     "email" : "test@verited.com",
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
     *  Store a newly created resource in storage.
     *
     * @return JsonResponse
     */
    public function __invoke(RegistrationRequest $request)
    {
        $user = User::create($this->filter($request));

        $user->assignRoles('user');
        $this->assignPermission($user, $request->permissions);
        return $this->created(
            new UserResource($user),
            'success.'
        );
    }

    public function assignPermission(User $user,array $perms){

        if ($permissions = Permission::whereIn('name', $perms)->get()){
            $user->assignPermissions($permissions);
        }

    }
}
