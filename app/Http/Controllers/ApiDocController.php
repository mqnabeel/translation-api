<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * @OA\Info(
 *     title="Translation API",
 *     version="1.0.0",
 *     description="API for managing translation keys, content, and tags across multiple languages",
 *     @OA\Contact(
 *         email="admin@example.com"
 *     )
 * )
 * 
 * @OA\Server(
 *     url="http://localhost:8080/api",
 *     description="Local development server"
 * )
 * 
 * @OA\SecurityScheme(
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     securityScheme="bearerAuth"
 * )
 */
class ApiDocController extends Controller
{
    // This controller exists solely for Swagger annotations
} 