<?php
namespace App\Api;

use OpenApi\Annotations as OA;

/**
 * We use annotation because Attribute Api is not yet available.
 * 
 * @OA\OpenApi(
 *     @OA\Server(
 *         url="https://fr.spacecoder.fun/api/",
 *         description="API SpaceCoder",
 *     ),
 *     @OA\Server(
 *         url="http://localhost:8000/api/"
 *     ),
 *     @OA\Info(
 *         version="1.0.0",
 *         title="API SpaceCoder",
 *         description="An API for SpaceCoder web appliation.",
 *         termsOfService="https://fr.spacecoder.fun/terms-and-conditions",
 *         @OA\Contact(name="SpaceCoder API Team", email="contact@spacecoder.fun", url="https://fr.spacecoder.fun/contact"),
 *         @OA\License(name="MIT", identifier="MIT")
 *     ),
 * )
 */
class OpenApi 
{}