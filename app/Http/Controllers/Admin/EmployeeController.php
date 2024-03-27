<?php

namespace App\Http\Controllers\Admin;

use App\Events\ApiRegistered;
use App\Http\Controllers\Controller;
use App\Http\Requests\GeneralRequest;
use App\Http\Requests\RegistrationRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class EmployeeController extends Controller
{
    /**
     * @OA\Get(
     *     path="/admin/employee",
     *     tags={"Employees"},
     *     security={ * {"sanctum": {} } * },
     *     summary="The resource collection",
     *     description="The resource collection",
     *     operationId="Api/Admin/EmployeeController::index",
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
     *             @OA\Items(ref="#/components/schemas/UserResource")
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
     * @return UserCollection
     */
    public function index(GeneralRequest $request)
    {
        $employees = (new User)
            ->searchs($request->search ?? '')
            ->paginate($request->quantity);

        return new UserCollection($employees);
    }

    /**
     * @OA\Post(
     * path="/admin/employee",
     * operationId="Api/Admin/EmployeeController::store",
     * tags={"Employees"},
     * summary="Admin create new employee",
     * description="Admin create new employee",
     *
     *    @OA\RequestBody(
     *         description="Create new user",
     *         required=true,
     *
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             example={
     *                 "firstname": "Ugbanawaji",
     *                 "lastname": "Ekenekiso",
     *                 "role": "developer",
     *                 "email" : "test@ugbanawaji.com",
     *                 "password" : "password",
     *                 "password_confirmation" : "password"
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
     *                     "role" : "developer",
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
    public function store(RegistrationRequest $request)
    {
        $user = User::create($this->filter($request));
        $user->referredBy($request->referrer);
        event(new ApiRegistered($user));

        $user->assignRoles($request->role ?? 'staff');

        return $this->created(
            new UserResource($user),
            'Please verify your email address'
        );
    }

    /**
     * @OA\Get (
     *     path="/admin/employee/{employeeId}",
     *     summary="The employee resource",
     *     description="The employee resource",
     *     operationId="Api/Admin/EmployeeController::show",
     *     security={ * {"sanctum": {} } * },
     *     description="The employee resource.",
     *     tags={"Employees"},
     *
     *     @OA\Parameter(
     *         name="employeeId",
     *         in="path",
     *         description="The employee id",
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
     *             @OA\Schema(ref="#/components/schemas/UserResource"),
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
    public function show(User $user)
    {

        return $this->success(new UserResource($user), 'success');
    }

    /**
     * @OA\Patch (
     * path="/admin/employee/employeeId",
     * operationId="Api/Admin//EmployeeController::update",
     * tags={"Employees"},
     * summary="Update employee profile",
     * security={ * {"sanctum": {} } * },
     * description="Update basic user informations.",
     *
     *     @OA\Parameter(
     *          name="employeeId",
     *          in="path",
     *          description="The employee resource Id",
     *          required=true,
     *
     *          @OA\Schema(
     *              type="integer",
     *          )
     *      ),
     *
     *    @OA\RequestBody(
     *         description="Update employee profile basic information.",
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
    public function update(UpdateProfileRequest $request, User $user)
    {

        $user->fill(
            array_filter($request->all())
        )->save();

        return $this->success(
            new UserResource($user->refresh()),
            'success'
        );
    }

    /**
     * @OA\Delete (
     *     path="/admin/employee/{employeeId}",
     *     summary="The delete employee resource",
     *     description="The delete employee resource",
     *     operationId="Api/Admin/EmployeeController::destroy",
     *     security={ * {"sanctum": {} } * },
     *     description="The employee resource.",
     *     tags={"Employees"},
     *
     *     @OA\Parameter(
     *         name="employeeId",
     *         in="path",
     *         description="The employee id",
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
     *             @OA\Schema(ref="#/components/schemas/UserResource"),
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
    public function destroy(User $user)
    {
        $user->delete();

        return $this->delete([], 'success');
    }
}
