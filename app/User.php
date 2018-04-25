<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Builder;
use \Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'active', 'activation_token'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function angebote()
    {
        return $this->hasMany(Angebot::class);
    }

    public function assistants()
    {
        return $this->belongsToMany(Lernzentrum::class, 'assistant_lernzentrum', 'assistant_id', 'lernzentrum_id');
    }

    public function lernzentrums()
    {
        return $this->belongsToMany(Lernzentrum::class);
    }

    public function hasRole($role)
    {
        return $role === lcfirst(DB::table('roles')->where('id', $this->role_id)->first()->name);
    }

    public function scopeByActivationColumns(Builder $builder, $email, $token)
    {
        return $builder->where('email', $email)->where('activation_token', $token);
    }
}
