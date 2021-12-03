<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\RoleMenu;

class RoleUser extends Model
{
	/**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'role_users';

    public function menus() {
    	return $this->hasMany(RoleMenu::class, 'role_id', 'role_id');
    }
}