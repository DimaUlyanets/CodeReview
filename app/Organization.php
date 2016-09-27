<?php

namespace App;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    use HasApiTokens, Notifiable;


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'description', 'icon'
    ];

    public function users()
    {
        return $this->belongsToMany('App\User')->withTimestamps();;
    }


}
