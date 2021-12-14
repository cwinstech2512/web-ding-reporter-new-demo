<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MenuResource;
use App\Models\Menu;

class MenuController extends Controller
{
    public function index()
    {
        $menu = MenuResource::collection(Menu::all());

        return response()->json([
            'menu' => $menu
        ]);
    }
}
