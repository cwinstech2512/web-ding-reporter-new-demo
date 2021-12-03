<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Menu;

class RoleMenu extends Model
{
	/**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'role_menu';

    public function menuInfo() {
    	return $this->hasOne(Menu::class, 'id', 'menu_id');
    }
}