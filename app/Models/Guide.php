<?php

namespace App\Models;

use App\Models\Review;
use DateTimeInterface;
use App\Models\Package;
use App\Models\Service;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Sanctum\NewAccessToken;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Casts\AsCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Guide extends Authenticatable
{
    use HasFactory, HasApiTokens, Notifiable;

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
        'provider',
        'password',
        'created_at',
        'updated_at',
        'provider_id',
        'firebase_token',
        'access_token',
        'remember_token',
        'email_verified_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'password' => 'hashed',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'email_verified_at' => 'datetime',
        'metadata' => AsCollection::class,
    ];

    /**
     * Customer full name.
     *
     * @return  string
     */
    public function name()
    {
        return ucwords("$this->first_name $this->last_name");
    }

    /**
     * Display the specified resource.
     *
     * @return  \App\Models\Agency
     */
    public function agency() //: HasMany
    {
        return $this->belongsTo(Agency::class);
    }

    /**
     * Display the specified resource.
     *
     * @return  \App\Models\Service
     */
    public function service() //: HasMany
    {
        return $this->hasMany(Service::class)->whereNot("guide_id", 0);
    }

    /**
     * Display the specified resource.
     *
     * @return  \App\Models\Review
     */
    public function review() //: HasMany
    {
        return $this->hasMany(Review::class)->whereNot("guide_id", 0);
    }

    /**
     * Interact with the user's image.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function image(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value != null && file_exists(public_path($value)) ? asset($value) : asset("assets/images/guide/default.png"),
            // set: fn ($value) => strtolower($value),
        );
    }


    /**
     * Interact with the user's image.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function avgRating(): Attribute
    {
        $ratings = $this->review()->pluck("rating")->toArray();
        $avgRating = collect($ratings)->average();
        return Attribute::make(
            get: fn ($value) => number_format(($avgRating ?? $value), 2),
            set: fn ($value) => number_format(($avgRating ?? $value), 2),
        );
    }

    /**
     * Create a new personal access token for the user.
     *
     * @param  string  $name
     * @param  array  $abilities
     * @param  \DateTimeInterface|null  $expiresAt
     * @return \Laravel\Sanctum\NewAccessToken
     */
    public function createToken(string $name, array $abilities = ['*'], DateTimeInterface $expiresAt = null)
    {
        $token = $this->tokens()->create([
            'name' => $name,
            'token' => hash('sha256', $plainTextToken = Str::random(70)),
            'abilities' => $abilities,
            'expires_at' => $expiresAt,
        ]);

        return new NewAccessToken($token, $plainTextToken);
    }
}
