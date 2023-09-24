<?php

namespace App\Models;

use App\Models\Service;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Casts\AsCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Facility extends Model
{
    use HasFactory;

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    // protected $primaryKey = 'eiin';


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ["*"];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'metadata' => AsCollection::class,
    ];

    /**
     * Display the specified resource.
     *
     * @return  \App\Models\Service
     */
    public function service() //: HasMany
    {
        return $this->belongsTo(Service::class);
    }


    /**
     * Interact with the image.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function image(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value != null && file_exists(public_path($value)) ? asset($value) : asset("assets/images/service/default.png"),
            // set: fn ($value) => strtolower($value),
        );
    }
}
