<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class WellKnownController extends Controller
{
    public function assetLinks(): Response
    {
        $path = public_path('.well-known/assetlinks.json');

        return response(file_get_contents($path), 200, [
            'Content-Type' => 'application/json',
            'Cache-Control' => 'public, max-age=300',
        ]);
    }

    public function appleAppSiteAssociation(): Response
    {
        $path = resource_path('well-known/apple-app-site-association');

        return response(file_get_contents($path), 200, [
            'Content-Type' => 'application/json',
            'Cache-Control' => 'public, max-age=300',
        ]);
    }
}
