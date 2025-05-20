<?php
require_once __DIR__ . '/../../data/Roles.php';

/**
 * @OA\Get(
 *      path="/users",
 *      tags={"users"},
 *      summary="Return all users from the API.",
 *      security={
 *          {"APIKey":{}}
 *      },
 *      @OA\Response(
 *          response=200,
 *          description="List of users."
 *      )
 * )
 */
Flight::route("GET /users", function(){
    Flight::auth_middleware()->authorizeRoles([Roles::VOTER, Roles::ADMIN]);
    $user = Flight::get('user');
    if($user->role === Roles::ADMIN){
        Flight::json(Flight::user_service()->get_all());
    }
    else{
        Flight::json(['message'=>"Only admins have the permission for this operation!"]);
    }
});

/**
 * @OA\Get(
 *      path="/user_by_id",
 *      tags={"users"},
 *      summary="Fetch individual user by ID.",
 *      security={
 *          {"APIKey":{}}
 *      },
 *      @OA\Parameter(
 *          name="id",
 *          in="query",
 *          description="User ID.",
 *          required=true,
 *          @OA\Schema(type="integer"),
 *          example=1
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="Fetch individual user."
 *      ),
 *      @OA\Response(
 *          response=400,
 *          description="Bad request - missing or invalid ID."
 *      )
 * )
 */
Flight::route("GET /user_by_id",function(){
    Flight::auth_middleware()->authorizeRoles([Roles::VOTER, Roles::ADMIN]);
    $user = Flight::get('user');
    if($user->role === Roles::ADMIN){
        Flight::json(Flight::user_service()->get_by_id(Flight::request()->query['id']));
    }
    else{
        Flight::json(['message'=>"Only admins have the permission for this operation!"]);
    }
});

/**
 * @OA\Get(
 *      path="/users/{id}",
 *      tags={"users"},
 *      summary="Fetch individual user by ID from path.",
 *      security={
 *          {"APIKey":{}}
 *      },
 *      @OA\Parameter(
 *          name="id",
 *          in="path",
 *          required=true,
 *          description="User ID.",
 *          @OA\Schema(type="integer"),
 *          example=1
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="Fetch individual user."
 *      ),
 *      @OA\Response(
 *          response=400,
 *          description="Bad request - missing or invalid ID."
 *      )
 * )
 */
Flight::route("GET /users/@id",function($id){
    Flight::auth_middleware()->authorizeRoles([Roles::VOTER, Roles::ADMIN]);
    $user = Flight::get('user');
    if($user->role === Roles::ADMIN || $user->role === Roles::VOTER){
        Flight::json(Flight::user_service()->get_by_id($id));
    }
});


/**
 * @OA\Get(
 *      path="/user/{email}",
 *      tags={"users"},
 *      summary="Fetch individual user by email from path.",
 *      security={
 *          {"APIKey":{}}
 *      },
 *      @OA\Parameter(
 *          name="email",
 *          in="path",
 *          required=true,
 *          description="User email.",
 *          @OA\Schema(type="string"),
 *          example="email@gmail.com"
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="Fetch individual user by email."
 *      ),
 *      @OA\Response(
 *          response=400,
 *          description="Bad request - missing or invalid ID."
 *      )
 * )
 */
Flight::route("GET /user/@email", function($email){
    Flight::auth_middleware()->authorizeRoles([Roles::VOTER, Roles::ADMIN]);
    $user = Flight::get('user');
    if($user->role === Roles::ADMIN){
        Flight::json(Flight::user_service()->get_by_email($email));
    }
    else{
        Flight::json(['message'=>"Only admins have the permission for this operation!"]);
    }
});


/**
 * @OA\Post(
 *     path="/user",
 *     summary="Add a new user.",
 *     description="Add a new user to the database.",
 *     tags={"users"},
 *     security={
 *         {"APIKey": {}}
 *     },
 *     @OA\RequestBody(
 *         description="Add new user",
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(
 *                 required={"role", "name", "email", "phone", "password", "has_voted"},
 *                  @OA\Property(
 *                     property="role",
 *                     type="string",
 *                     example="voter",
 *                     description="User role"
 *                 ),
 *                 @OA\Property(
 *                     property="name",
 *                     type="string",
 *                     example="My name",
 *                     description="User name"
 *                 ),
 *                 @OA\Property(
 *                      property="email",
 *                      type="string",
 *                      example="email@gmail.com",
 *                      description="User email"
 *                  ),
 *                  @OA\Property(
 *                      property="phone",
 *                      type="string",
 *                      example="60000000",
 *                      description="User phone number"
 *                  ),
 *                  @OA\Property(
 *                      property="password",
 *                      type="string",
 *                      example="ascomplicatedaspossible123",
 *                      description="User password"
 *                  ),
 *                  @OA\Property(
 *                      property="has_voted",
 *                      type="integer",
 *                      example=0,
 *                      description="Has the user voted"
 *                  )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="User has been added."
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Internal server error."
 *     )
 * )
 */
Flight::route("POST /user", function(){
    Flight::auth_middleware()->authorizeRoles([Roles::VOTER, Roles::ADMIN]);
    $user = Flight::get('user');
    if($user->role === Roles::ADMIN){
        $request = Flight::request()->data->getData();
        if (isset($request['password'])) {
            $request['password'] = password_hash($request['password'], PASSWORD_BCRYPT);
        }
        Flight::json([
            'message'=>"User has been added!",
            'data'=>Flight::user_service()->add($request)
        ]);
    }
    else{
        Flight::json(['message'=>"Only admins have the permission for this operation!"]);
    }
});

/**
 * @OA\Patch(
 *     path="/user/{id}",
 *     summary="Edit user details",
 *     description="Update user information using their ID.",
 *     tags={"users"},
 *     security={
 *         {"APIKey": {}}
 *     },
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="User ID",
 *         @OA\Schema(type="integer"),
 *         example=1
 *     ),
 *     @OA\RequestBody(
 *         description="Updated user information",
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="role", type="string", example="voter", description="User role"),
 *             @OA\Property(property="name", type="string", example="My name", description="User name"),
 *             @OA\Property(property="email", type="string", example="email@gmail.com", description="User email"),
 *             @OA\Property(property="phone", type="string", example="060000000", description="User phone number"),
 *             @OA\Property(property="password", type="string", example="shifrashifrashifra", description="User password"),
 *             @OA\Property(property="has_voted", type="integer", example=0, description="Has the user voted"),
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="User has been edited successfully."
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
Flight::route("PATCH /user/@id",function($id){
    Flight::auth_middleware()->authorizeRoles([Roles::VOTER, Roles::ADMIN]);
    $userToAuthenticate = Flight::get('user');
    if($userToAuthenticate->role === Roles::VOTER) {
        Flight::user_service()->change_vote_status($id);
        Flight::json(['message' => 'User voting status updated.']);
        return;
    }
    if($userToAuthenticate->role === Roles::ADMIN){
        $user = Flight::request()->data->getData();
        if (isset($user['password'])) {
            $user['password'] = password_hash($user['password'], PASSWORD_BCRYPT);
        }
        Flight::json([
            'message'=>"User has been updated!",
            'data'=>Flight::user_service()->update($user,$id,'id')
        ]);
        return;
    }
    Flight::json(['message'=>"Only admins have the permission for this operation!"]);
});
/**
 * @OA\Delete(
 *      path="/user/{id}",
 *      tags={"users"},
 *      summary="Delete a user from the database using their ID.",
 *      security={
 *          {"APIKey":{}}
 *      },
 *      @OA\Parameter(
 *          name="id",
 *          in="path",
 *          required=true,
 *          description="User ID",
 *          @OA\Schema(type="integer"),
 *          example=1
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="User deleted successfully."
 *      ),
 *      @OA\Response(
 *          response=500,
 *          description="Internal server error."
 *      )
 * )
 */
Flight::route("DELETE /user/@id",function($id){
    Flight::auth_middleware()->authorizeRoles([Roles::VOTER, Roles::ADMIN]);
    $user = Flight::get('user');
    if($user->role === Roles::ADMIN){
        Flight::user_service()->delete($id);
        Flight::json(['message'=>"User has been deleted!"]);
    }
    else{
        Flight::json(['message'=>"Only admins have the permission for this operation!"]);
    }
});