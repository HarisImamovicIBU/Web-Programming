<?php
require_once __DIR__ . '/../../data/Roles.php';

/**
 * @OA\Get(
 *      path="/parties",
 *      tags={"parties"},
 *      summary="Return all parties from the API.",
 *      security={
 *          {"APIKey":{}}
 *      },
 *      @OA\Response(
 *          response=200,
 *          description="List of parties."
 *      )
 * )
 */
Flight::route("GET /parties", function(){
    Flight::auth_middleware()->authorizeRoles([Roles::VOTER, Roles::ADMIN]);
    Flight::json(Flight::party_service()->get_all());
});


/**
 * @OA\Get(
 *      path="/party_by_id",
 *      tags={"parties"},
 *      summary="Fetch individual party by ID.",
 *      security={{
 *          "APIKey":{}
 *      }},
 *      @OA\Parameter(
 *          name="id",
 *          in="query",
 *          required=true,
 *          description="Party ID",
 *          @OA\Schema(type="integer"),
 *          example=1
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="Fetch individual party."
 *      ),
 *      @OA\Response(
 *          response=400,
 *          description="Bad request - missing or invalid ID."
 *      )
 * )
 */
Flight::route("GET /party_by_id",function(){
    Flight::auth_middleware()->authorizeRoles([Roles::VOTER, Roles::ADMIN]);
    Flight::json(Flight::party_service()->get_by_id(Flight::request()->query['id']));
});

/**
 * @OA\Get(
 *      path="/party/{id}",
 *      tags={"parties"},
 *      summary="Fetch individual party by ID from path.",
 *      security={
 *          {"APIKey":{}}
 *      },
 *      @OA\Parameter(
 *          name="id",
 *          in="path",
 *          required=true,
 *          description="Party ID.",
 *          @OA\Schema(type="integer"),
 *          example=1
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="Fetch individual party."
 *      ),
 *      @OA\Response(
 *          response=400,
 *          description="Bad request - missing or invalid ID."
 *      )
 * )
 */
Flight::route("GET /party/@id",function($id){
    Flight::auth_middleware()->authorizeRoles([Roles::VOTER, Roles::ADMIN]);
    Flight::json(Flight::party_service()->get_by_id($id));
});

/**
 * @OA\Post(
 *     path="/party",
 *     summary="Add a new party.",
 *     description="Add a new party to the database.",
 *     tags={"parties"},
 *     security={
 *         {"APIKey": {}}
 *     },
 *     @OA\RequestBody(
 *         description="Add new party",
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(
 *                 required={"name", "abbreviation"},
 *                  @OA\Property(
 *                     property="name",
 *                     type="string",
 *                     example="Party X",
 *                     description="Party X"
 *                  ),
 *                 @OA\Property(
 *                     property="abbreviation",
 *                     type="string",
 *                     example="PAX",
 *                     description="Party abbreviation"
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Party has been added."
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Internal server error."
 *     )
 * )
 */
Flight::route("POST /party", function(){
    Flight::auth_middleware()->authorizeRoles([Roles::VOTER, Roles::ADMIN]);
    $user = Flight::get('user');
    if($user->role === Roles::ADMIN){
        $request = Flight::request()->data->getData();
        Flight::json([
            'message'=>"Party has been added!",
            'data'=>Flight::party_service()->add($request)
        ]);
    }
    else{
        Flight::json(['message'=>"Only admins have the permission for this operation!"]);
    }
});

/**
 * @OA\Patch(
 *     path="/party/{id}",
 *     summary="Edit party details",
 *     description="Update party information using its ID.",
 *     tags={"parties"},
 *     security={
 *         {"APIKey": {}}
 *     },
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Party ID",
 *         @OA\Schema(type="integer"),
 *         example=1
 *     ),
 *     @OA\RequestBody(
 *         description="Updated party information",
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="name", type="string", example="Party X", description="Party name"),
 *             @OA\Property(property="abbreviation", type="string", example="PAX", description="Party abbreviation")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Party has been edited successfully."
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
Flight::route("PATCH /party/@id",function($id){
    Flight::auth_middleware()->authorizeRoles([Roles::VOTER, Roles::ADMIN]);
    $user = Flight::get('user');
    if($user->role === Roles::ADMIN){
        $party = Flight::request()->data->getData();
        Flight::json([
            'message'=>"Party has been updated!",
            'data'=>Flight::party_service()->update($party,$id,'id')
        ]);
    }
    else{
        Flight::json(['message'=>"Only admins have the permission for this operation!"]);
    }
});


/**
 * @OA\Delete(
 *     path="/party/{id}",
 *     summary="Delete a party by ID.",
 *     description="Delete a party from the database using its ID.",
 *     tags={"parties"},
 *     security={
 *         {"APIKey": {}}
 *     },
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Party ID",
 *         @OA\Schema(type="integer"),
 *         example=1
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Party deleted successfully."
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Internal server error."
 *     )
 * )
 */
Flight::route("DELETE /party/@id",function($id){
    Flight::auth_middleware()->authorizeRoles([Roles::VOTER, Roles::ADMIN]);
    $user = Flight::get('user');
    if($user->role === Roles::ADMIN){
        Flight::party_service()->delete($id);
        Flight::json(['message'=>"Party has been deleted!"]);
    }
    else{
        Flight::json(['message'=>"Only admins have the permission for this operation!"]);
    }
});

