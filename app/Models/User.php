<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, HasRoles, HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'last_name', 'email', 'password', 'contact_number', 'postcode', 'gender', 'hobbies', 'country_id', 'state_id', 'city_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
	
	 // Accessor for getting full name
    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }
	
	public function roles()
	{
		 return $this->belongsToMany(Role::class, 'role_users');
	}
	
	public function country()
	{
		return $this->belongsTo(Country::class, 'country_id');
	}
	
	public function state()
	{
		return $this->belongsTo(State::class, 'state_id');
	}
	
	public function city()
	{
		return $this->belongsTo(City::class, 'city_id');
	}
}
