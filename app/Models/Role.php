<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
	/**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'roles';

    public $fillable = ['name', 'slug'];

    public function menus()
    {
        return $this->belongsToMany(Menu::class, 'role_menu')
            ->withTimestamps()
            ->withPivot('updated_at');
    }
}