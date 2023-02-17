<?php
namespace App\Api\Controller;

use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @OA\Parameter(
 *      name="slug",
 *      in="path",
 *      description="Ressource slug",
 *      required=true,
 *      @OA\Schema(type="string")
 * ),
 * 
 * 
 * @OA\Response(
 *      response="NotFound",
 *      description="Ressource not found.",
 *      @OA\JsonContent(
 *          @OA\Property(property="code", type="integer", example="404"),
 *          @OA\Property(property="message", type="string", example="Ressource not found."),
 *      )
 * ),
 * 
 * @OA\Response(
 *      response="ExpiredToken",
 *      description="Unauthaurized",
 *      @OA\JsonContent(
 *          @OA\Property(property="code", type="integer", example="401"),
 *          @OA\Property(property="message", type="string", example="Expired JWT Token"),
 *      )
 * ),
 * 
 * @OA\SecurityScheme(
 *      securityScheme="Bearer", 
 *      type="http", 
 *      scheme="bearer",
 *      name="Authorization",
 *      in="header",
 *      bearerFormat="JWT",
 *      description=">- Enter the token with the `Bearer: {token}`, e.g. Bearer abcde12345"
 * )
 */
class AbstractApiController extends AbstractController
{
}