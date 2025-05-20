<?php
require_once __DIR__ . '/../../data/Roles.php';

/**
 * @OA\Get(
 *      path="/votes",
 *      tags={"votes"},
 *      summary="Return all votes from the API.",
 *      security={{"APIKey":{}}},
 *      @OA\Response(
 *          response=200,
 *          description="List of votes."
 *      )
 * )
 */
Flight::route("GET /votes", function(){
    Flight::auth_middleware()->authorizeRoles([Roles::VOTER, Roles::ADMIN]);
    $user = Flight::get('user');
    if($user->role === Roles::ADMIN){
        Flight::json(Flight::vote_service()->get_all());
    }
    else{
        Flight::json(['message'=>"Only admins have the permission for this operation!"]);
    }
});

/**
 * @OA\Get(
 *      path="/vote_by_id",
 *      tags={"votes"},
 *      summary="Fetch individual vote by ID (query param).",
 *      security={{"APIKey":{}}},
 *      @OA\Parameter(
 *          name="id",
 *          in="query",
 *          required=true,
 *          description="Vote ID",
 *          @OA\Schema(type="integer"),
 *          example=1
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="Single vote returned."
 *      ),
 *      @OA\Response(
 *          response=400,
 *          description="Invalid ID."
 *      )
 * )
 */
Flight::route("GET /vote_by_id", function(){
    Flight::auth_middleware()->authorizeRoles([Roles::VOTER, Roles::ADMIN]);
    $user = Flight::get('user');
    if($user->role === Roles::ADMIN){
        Flight::json(Flight::vote_service()->get_by_id(Flight::request()->query['id']));
    }
    else{
        Flight::json(['message'=>"Only admins have the permission for this operation!"]);
    }
});

/**
 * @OA\Get(
 *      path="/vote/{id}",
 *      tags={"votes"},
 *      summary="Fetch individual vote by ID from path.",
 *      security={{"APIKey":{}}},
 *      @OA\Parameter(
 *          name="id",
 *          in="path",
 *          required=true,
 *          description="Vote ID",
 *          @OA\Schema(type="integer"),
 *          example=1
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="Single vote returned."
 *      ),
 *      @OA\Response(
 *          response=400,
 *          description="Invalid ID."
 *      )
 * )
 */
Flight::route("GET /vote/@id", function($id){
    Flight::auth_middleware()->authorizeRoles([Roles::VOTER, Roles::ADMIN]);
    $user = Flight::get('user');
    if($user->role === Roles::ADMIN){
        Flight::json(Flight::vote_service()->get_by_id($id));
    }
    else{
        Flight::json(['message'=>"Only admins have the permission for this operation!"]);
    }
});


/**
 * @OA\Get(
 *      path="/votes/{user_id}",
 *      tags={"votes"},
 *      summary="Fetch individual vote by User ID.",
 *      security={{"APIKey":{}}},
 *      @OA\Parameter(
 *          name="user_id",
 *          in="path",
 *          required=true,
 *          description="User ID",
 *          @OA\Schema(type="integer"),
 *          example=1
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="Vote returned."
 *      ),
 *      @OA\Response(
 *          response=400,
 *          description="Invalid ID."
 *      )
 * )
 */
Flight::route("GET /votes/@user_id",function($user_id){
    Flight::auth_middleware()->authorizeRoles([Roles::VOTER, Roles::ADMIN]);
    $user = Flight::get('user');
    if($user->role === Roles::ADMIN){
        Flight::json(Flight::vote_service()->get_by_user_id($user_id));
    }
    else{
        Flight::json(['message'=>"Only admins have the permission for this operation!"]);
    }
}); 
/**
 * @OA\Post(
 *     path="/vote",
 *     summary="Add a new vote.",
 *     description="Insert a new vote into the database.",
 *     tags={"votes"},
 *     security={{"APIKey":{}}},
 *     @OA\RequestBody(
 *         description="Vote information",
 *         required=true,
 *         @OA\JsonContent(
 *             required={"user_id", "candidate_id"},
 *             @OA\Property(property="user_id", type="integer", example=5, description="ID of the user"),
 *             @OA\Property(property="candidate_id", type="integer", example=2, description="ID of the candidate voted for")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Vote has been added."
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Internal server error."
 *     )
 * )
 */
Flight::route("POST /vote", function(){
    Flight::auth_middleware()->authorizeRoles([Roles::VOTER, Roles::ADMIN]);
    $request = Flight::request()->data->getData();
    Flight::json([
        'message'=>"Vote has been added!",
        'data'=>Flight::vote_service()->add($request)
    ]);    
});

/**
 * @OA\Patch(
 *     path="/vote/{id}",
 *     summary="Edit vote details",
 *     description="Update vote information using its ID.",
 *     tags={"votes"},
 *     security={{"APIKey":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Vote ID",
 *         @OA\Schema(type="integer"),
 *         example=1
 *     ),
 *     @OA\RequestBody(
 *         description="Updated vote information",
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="user_id", type="integer", example=5, description="User ID"),
 *             @OA\Property(property="candidate_id", type="integer", example=3, description="Candidate ID"),
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Vote has been edited successfully."
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
Flight::route("PATCH /vote/@id", function($id){
    Flight::auth_middleware()->authorizeRoles([Roles::VOTER, Roles::ADMIN]);
    $user = Flight::get('user');
    if($user->role === Roles::ADMIN){
        $vote = Flight::request()->data->getData();
        Flight::json([
            'message'=>"Vote has been updated!",
            'data'=>Flight::vote_service()->update($vote, $id, 'id')
        ]);
    }
    else{
        Flight::json(['message'=>"Only admins have the permission for this operation!"]);
    }
});

/**
 * @OA\Delete(
 *     path="/vote/{id}",
 *     summary="Delete a vote",
 *     description="Delete a vote record by ID.",
 *     tags={"votes"},
 *     security={{
 *          "APIKey":{}
 *     }},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Vote ID",
 *         @OA\Schema(type="integer"),
 *         example=1
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Vote deleted successfully."
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Internal server error."
 *     )
 * )
 */
Flight::route("DELETE /vote/@id", function($id){
    Flight::auth_middleware()->authorizeRoles([Roles::VOTER, Roles::ADMIN]);
    $user = Flight::get('user');
    if($user->role === Roles::ADMIN){
        Flight::vote_service()->delete($id);
        Flight::json(['message'=>"Vote has been deleted!"]);
    }
    else{
        Flight::json(['message'=>"Only admins have the permission for this operation!"]);
    }
});
