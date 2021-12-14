<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RoleStoreRequest;
use App\Http\Requests\RoleUpdateRequest;
use App\Http\Resources\RoleResource;
use App\Models\Role;
use App\Services\RoleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class RoleController extends Controller
{
    protected $service;

    public function __construct(RoleService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        return $this->service->getRoles();
    }

    public function store(RoleStoreRequest $request)
    {
        return $this->service->storeRole($request);
    }

    public function update(RoleUpdateRequest $request, Role $role)
    {
        return $this->service->updateRole($request, $role);
    }
}
