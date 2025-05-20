<?php
require_once __DIR__ . '/../../data/Roles.php';

/**
 * @OA\Get(
 *      path="/inquiries",
 *      tags={"inquiries"},
 *      summary="Return all inquiries from the API.",
 *      security={
 *          {"APIKey": {}}
 *      },
 *      @OA\Response(
 *          response=200,
 *          description="List of inquiries"
 *      )
 * )
 */
Flight::route("GET /inquiries", function(){
    Flight::auth_middleware()->authorizeRoles([Roles::VOTER, Roles::ADMIN]);
    $user = Flight::get('user');
    if($user->role === Roles::ADMIN){
        Flight::json(Flight::inquiry_service()->get_all());
    }
    else{
        Flight::json(['message'=>"Only admins have the permission for this operation!"]);
    }
});

/**
 * @OA\Get(
 *      path="/inquiry_by_id",
 *      tags={"inquiries"},
 *      summary="Fetch individual inquiry by ID.",
 *      security={
 *          {"APIKey":{}}
 *      },
 *      @OA\Parameter(
 *          name="id",
 *          in="query",
 *          required=true,
 *          description="Inquiry ID.",
 *          @OA\Schema(type="integer"),
 *          example=1
 *      ),
 *      @OA\Response(
 *         response=200,
 *         description="Fetch individual inquiry."
 *      ),
 *      @OA\Response(
 *         response=400,
 *         description="Bad request - missing or invalid ID."
 *     )
 * )
 */
Flight::route("GET /inquiry_by_id", function(){
    Flight::auth_middleware()->authorizeRoles([Roles::VOTER, Roles::ADMIN]);
    $user = Flight::get('user');
    if($user->role === Roles::ADMIN){
        Flight::json(Flight::inquiry_service()->get_by_id(Flight::request()->query['id']));
    }
    else{
        Flight::json(['message'=>"Only admins have the permission for this operation!"]);
    }
});


/**
 * @OA\Get(
 *     path="/inquiry/{id}",
 *     tags={"inquiries"},
 *     summary="Fetch individual inquiry by ID from path.",
 *     security={
 *         {"APIKey": {}}
 *     },
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="inquiry ID",
 *         @OA\Schema(type="integer"),
 *         example=1
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Fetch individual inquiry."
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Bad request - missing or invalid ID."
 *     )
 * )
 */
Flight::route("GET /inquiry/@id", function($id){
    Flight::auth_middleware()->authorizeRoles([Roles::VOTER, Roles::ADMIN]);
    $user = Flight::get('user');
    if($user->role === Roles::ADMIN){
        Flight::json(Flight::inquiry_service()->get_by_id($id));
    }
    else{
        Flight::json(['message'=>"Only admins have the permission for this operation!"]);
    }
});

/**
 * @OA\Get(
 *      path="/inquiries/{user_id}",
 *      tags={"inquiries"},
 *      summary="Fetch inquiries by User ID from path.",
 *      security={
 *          {"APIKey":{}}
 *      },
 *      @OA\Parameter(
 *          name="user_id",
 *          in="path",
 *          required=true,
 *          description="User ID",
 *          @OA\Schema(type="integer"),
 *          example=1
 *      ),
 *      @OA\Response(
 *         response=200,
 *         description="Fetch inquiries by User ID."
 *     ),
 *      @OA\Response(
 *         response=400,
 *         description="Bad request - missing or invalid ID."
 *     )
 * )
 */
Flight::route("GET /inquiries/@user_id",function($user_id){
    Flight::auth_middleware()->authorizeRoles([Roles::VOTER, Roles::ADMIN]);
    $user = Flight::get('user');
    if($user->role === Roles::ADMIN){
        Flight::json(Flight::inquiry_service()->get_by_user_id($user_id));
    }
    else{
        Flight::json(['message'=>"Only admins have the permission for this operation!"]);
    }
});


/**
 * @OA\Post(
 *     path="/inquiry",
 *     summary="Add a new inquiry.",
 *     description="Add a new inquiry to the database.",
 *     tags={"inquiries"},
 *     security={
 *         {"APIKey": {}}
 *     },
 *     @OA\RequestBody(
 *         description="Add new inquiry",
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(
 *                 required={"user_id", "comment"},
 *                  @OA\Property(
 *                     property="user_id",
 *                     type="integer",
 *                     example="1",
 *                     description="User id"
 *                 ),
 *                 @OA\Property(
 *                     property="comment",
 *                     type="string",
 *                     example="My comment.",
 *                     description="Inquiry comment"
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Inquiry has been added."
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Internal server error."
 *     )
 * )
 */
Flight::route("POST /inquiry", function(){
    Flight::auth_middleware()->authorizeRoles([Roles::VOTER, Roles::ADMIN]);
    $request = Flight::request()->data->getData();
    Flight::json([
        'message'=>"Inquiry has been added!",
        'data'=>Flight::inquiry_service()->add($request)
    ]);
});

/**
 * @OA\Patch(
 *     path="/inquiry/{id}",
 *     summary="Edit inquiry details",
 *     description="Update an inquiry information using its ID.",
 *     tags={"inquiries"},
 *     security={
 *         {"APIKey": {}}
 *     },
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Inquiry ID",
 *         @OA\Schema(type="integer"),
 *         example=1
 *     ),
 *     @OA\RequestBody(
 *         description="Updated inquiry information",
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="user_id", type="integer", example="1", description="User ID"),
 *             @OA\Property(property="comment", type="string", example="My comment.", description="Inquiry comment")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Inquiry has been edited successfully."
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid input data."
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Internal server error."
 *     )
 * )
 */
Flight::route("PATCH /inquiry/@id", function($id){
    Flight::auth_middleware()->authorizeRoles([Roles::VOTER, Roles::ADMIN]);
    $user = Flight::get('user');
    if($user->role === Roles::ADMIN){
        $inquiry = Flight::request()->data->getData();
        Flight::json([
            'message'=>"Inquiry has been updated!",
            'data'=>Flight::inquiry_service()->update($inquiry,$id,'id')
        ]);
    }
    else{
        Flight::json(['message'=>"Only admins have the permission for this operation!"]);
    }
});

/**
 * @OA\Delete(
 *     path="/inquiry/{id}",
 *     summary="Delete a inquiry by ID.",
 *     description="Delete an inquiry from the database using its ID.",
 *     tags={"inquiries"},
 *     security={
 *         {"APIKey": {}}
 *     },
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Inquiry ID",
 *         @OA\Schema(type="integer"),
 *         example=1
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Inquiry deleted successfully."
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Internal server error."
 *     )
 * )
 */
Flight::route("DELETE /inquiry/@id", function($id){
    Flight::auth_middleware()->authorizeRoles([Roles::VOTER, Roles::ADMIN]);
    $user = Flight::get('user');
    if($user->role === Roles::ADMIN){
        Flight::inquiry_service()->delete($id);
        Flight::json(['message'=>"Inquiry has been deleted!"]);
    }
    else{
        Flight::json(['message'=>"Only admins have the permission for this operation!"]);
    }
});