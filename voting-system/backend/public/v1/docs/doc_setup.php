<?php

/**
 * @OA\Info(
 *   title="API",
 *   description="Voting System API",
 *   version="1.0",
 *   @OA\Contact(
 *     email="haris.imamovic2208@gmail.com",
 *     name="Voting System"
 *   )
 * ),
 * @OA\Server(
 *     url=BASE_URL,
 *     description="API server"
 * ),
 * @OA\SecurityScheme(
 *     securityScheme="APIKey",
 *     type="apiKey",
 *     in="header",
 *     name="Authentication"
 * )
 */