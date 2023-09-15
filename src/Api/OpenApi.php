<?php
namespace App\Api;

use OpenApi\Attributes as OAT;

#[OAT\Server(
    url: 'https://fr.spacecoder.fun/api/',
    description: 'API SpaceCoder'
)]
#[OAT\Server(
    url: 'http://localhost:8000/api/',
    description: 'API Local'
)]
#[OAT\Info(
    version: '1.0.0',
    description: 'An API for SpaceCoder web appliation.',
    title: 'API SpaceCoder',
    termsOfService: 'https://fr.spacecoder.fun/terms-and-conditions',
    contact: new OAT\Contact('SpaceCoder API Team', 'https://fr.spacecoder.fun/contact', 'contact@spacecoder.fun'),
    license: new OAT\License('MIT', 'MIT')
)]
class OpenApi 
{}