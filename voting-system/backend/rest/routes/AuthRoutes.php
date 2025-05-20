<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

Flight::group('/auth', function() {

    /**
     * @OA\Post(
     *     path="/auth/register",
     *     summary="Register new user.",
     *     description="Add a new user to the database.",
     *     tags={"auth"},
     *     @OA\RequestBody(
     *         description="Add new user",
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"name", "email","phone","password"},
     *                 @OA\Property(
     *                     property="name",
     *                     type="string",
     *                     example="Name",
     *                     description="Name of the user."
     *                 ),
     *                 @OA\Property(
     *                     property="email",
     *                     type="string",
     *                     example="demo@gmail.com",
     *                     description="User email."
     *                 ),
     *                 @OA\Property(
     *                     property="phone",
     *                     type="string",
     *                     example="062456789",
     *                     description="User phone number."
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     type="string",
     *                     example="unesinekunormalnusifru",
     *                     description="User password."
     *                 )
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
    Flight::route("POST /register", function () {
        $data = Flight::request()->data->getData();

        $response = Flight::auth_service()->register($data);
    
        if ($response['success']) {
            Flight::json([
                'message' => 'User registered successfully',
                'data' => $response['data']
            ]);
        } else {
            Flight::halt(500, $response['error']);
        }
    });
    
    /**
     * @OA\Post(
     *      path="/auth/login",
     *      tags={"auth"},
     *      summary="Login to system using email, password and phone",
     *      @OA\Response(
     *           response=200,
     *           description="User data and JWT"
     *      ),
     *      @OA\RequestBody(
     *          description="Credentials",
     *          @OA\JsonContent(
     *              required={"email","password","phone"},
     *              @OA\Property(property="email", type="string", example="demo@gmail.com", description="User email address"),
     *              @OA\Property(property="password", type="string", example="some_password", description="User password"),
     *              @OA\Property(property="phone", type="string", example="123456789", description="User phone number")
     *          )
     *      )
     * )
     */
    Flight::route('POST /login', function() {
        $data = Flight::request()->data->getData();

        $response = Flight::auth_service()->login($data);
    
        if ($response['success']) {
            Flight::json([
                'message' => 'User logged in successfully',
                'data' => $response['data']
            ]);
        } else {
            Flight::halt(500, $response['error']);
        }
    });
 
});


 ?>