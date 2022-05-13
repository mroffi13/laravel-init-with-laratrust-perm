<?php

namespace App\Models;

use Laratrust\Models\LaratrustRole;

class Role extends LaratrustRole
{
    public $guarded = [];
    protected $casts = [
        'created_id' => 'integer',
        'updated_id' => 'integer',
        'status' => 'integer',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    protected $indexes = [
        'name',
        'display_name',
        'description',
        'created_name',
        'updated_name'
    ];

    public function getIndex()
    {
        return $this->indexes;
    }
}
