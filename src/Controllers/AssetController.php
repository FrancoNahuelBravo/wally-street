<?php
namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\Asset;

class AssetController {
    public function index(Request $request, Response $response) {
        $assets = Asset::all();
        $response->getBody()->write(json_encode($assets));
        return $response->withHeader('Content-Type', 'application/json');
    }
}