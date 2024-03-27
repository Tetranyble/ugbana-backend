<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\GeneralRequest;
use App\Http\Requests\RoleRequest;
use App\Http\Resources\RoleCollection;
use App\Http\Resources\RoleResource;
use App\Models\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class RoleController extends Controller
{
    /**
     * @OA\Get(
     *     path="/admin/roles",
     *     tags={"Admin Roles"},
     *     security={ * {"sanctum": {} } * },
     *     summary="The resource collection",
     *     description="The resource collection",
     *     operationId="Api/Admin/RoleController::index",
     *
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search the resource by name or description",
     *         required=false,
     *
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *
     *     @OA\Parameter(
     *         name="quantity",
     *         in="query",
     *         description="The quantity",
     *         required=false,
     *
     *         @OA\Schema(
     *             type="integer",
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="The resource collection",
     *
     *         @OA\JsonContent(
     *             type="array",
     *
     *             @OA\Items(ref="#/components/schemas/RoleResource")
     *         )
     *     ),
     *
     *    @OA\Response(response=400, ref="#/components/responses/400"),
     *    @OA\Response(response=403, ref="#/components/responses/403"),
     *    @OA\Response(response=404, ref="#/components/responses/404"),
     *    @OA\Response(response=422, ref="#/components/responses/422"),
     *    @OA\Response(response="default", ref="#/components/responses/500")
     * )
     * Display a listing of the resource.
     *
     * @return RoleCollection
     */
    public function index(GeneralRequest $request)
    {
        $roles = (new Role)->search($request->search ?? '')
            ->paginate($request->quantity);

        return new RoleCollection($roles);
    }

    /**
     * @OA\Post(
     * path="/admin/roles",
     * operationId="Api/Admin/RoleController::store",
     * tags={"Admin Roles"},
     * security={ * {"sanctum": {} } * },
     * summary="Store Role resource.",
     * description="Store Role resource.",
     *
     *    @OA\RequestBody(
     *         description="Create new Role",
     *         required=true,
     *
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             example={
     *                 "name": "The name",
     *                 "description": "The name",
     *             },
     *
     *             @OA\Schema(ref="#/components/schemas/RoleRequest")
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
     *                 "message":"success",
     *                 "status": true,
     *                 "data": {
     *                     "name": "The program resource",
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
     *  Store a newly created resource in storage.
     *
     * @return JsonResponse
     */
    public function store(RoleRequest $request)
    {
        $role = Role::create([
            'name' => Str::slug($request->name),
            'label' => $request->name,
            'is_system' => false,
            'description' => $request->description,
        ]);

        return $this->created(
            new RoleResource($role),
            'success'
        );
    }

    /**
     * @OA\Get (
     *     path="/admin/roles/{role}",
     *     summary="The role resource",
     *     description="The role resource",
     *     operationId="Api/Admin/RoleController::show",
     *     security={ * {"sanctum": {} } * },
     *     description="The role resource.",
     *     tags={"Admin Roles"},
     *
     *     @OA\Parameter(
     *         name="role",
     *         in="path",
     *         description="The role id",
     *         required=true,
     *
     *         @OA\Schema(
     *             type="integer",
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="success",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Schema(ref="#/components/schemas/RoleResource"),
     *             type="object",
     *             example={
     *                 "message":"success",
     *                 "status": true,
     *             }
     *         )
     *     ),
     *
     *    @OA\Response(response=400, ref="#/components/responses/400"),
     *    @OA\Response(response=404, ref="#/components/responses/404"),
     *    @OA\Response(response=422, ref="#/components/responses/422"),
     *    @OA\Response(response="default", ref="#/components/responses/500")
     * )
     * Store an applicant newly created resource in storage.
     *
     * @return JsonResponse
     */
    public function show(Role $role)
    {
        return $this->success(
            new RoleResource($role),
            'success'
        );
    }

    /**
     * @OA\Patch(
     * path="/admin/roles/{role}",
     * operationId="Api/Admin/RoleController::update",
     * tags={"Admin Roles"},
     * security={ * {"sanctum": {} } * },
     * summary="Store Role resource.",
     * description="Store Role resource.",
     *
     *     @OA\Parameter(
     *         name="role",
     *         in="path",
     *         description="The Role resource id",
     *         required=false,
     *
     *         @OA\Schema(
     *             type="integer",
     *         )
     *     ),
     *
     *    @OA\RequestBody(
     *         description="Create new Role",
     *         required=true,
     *
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             example={
     *                 "name": "The name",
     *                 "description": "The name",
     *             },
     *
     *             @OA\Schema(ref="#/components/schemas/RoleRequest")
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="success",
     *
     *         @OA\JsonContent(
     *             type="object",
     *             example={
     *                 "message":"success",
     *                 "status": true,
     *                 "data": {
     *                     "name": "The program resource",
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
     *  Store a newly created resource in storage.
     *
     * @return JsonResponse
     */
    public function update(RoleRequest $request, Role $role)
    {
        $role->update([
            'name' => Str::slug($request->name),
            'label' => $request->name,
            'description' => $request->description,
        ]);

        return $this->success(
            new RoleResource($role->refresh()),
            'success'
        );
    }

    /**
     * @OA\Delete (
     *     path="/admin/roles/{role}",
     *     summary="The role resource",
     *     description="The role resource",
     *     operationId="Api/Admin/RoleController::destroy",
     *     security={ * {"sanctum": {} } * },
     *     description="The role resource.",
     *     tags={"Admin Roles"},
     *
     *     @OA\Parameter(
     *         name="role",
     *         in="path",
     *         description="The role id",
     *         required=true,
     *
     *         @OA\Schema(
     *             type="integer",
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response=200,
     *         description="success",
     *
     *         @OA\JsonContent(
     *
     *             @OA\Schema(ref="#/components/schemas/RoleResource"),
     *             type="object",
     *             example={
     *                 "message":"success",
     *                 "status": true,
     *             }
     *         )
     *     ),
     *
     *    @OA\Response(response=400, ref="#/components/responses/400"),
     *    @OA\Response(response=404, ref="#/components/responses/404"),
     *    @OA\Response(response=422, ref="#/components/responses/422"),
     *    @OA\Response(response="default", ref="#/components/responses/500")
     * )
     * Store an applicant newly created resource in storage.
     *
     * @return JsonResponse
     */
    public function destroy(Role $role)
    {
        $role->removePermissionTo($role->permissions);
        $role->delete();

        return $this->delete([], 'success');
    }
}
