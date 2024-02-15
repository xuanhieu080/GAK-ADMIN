<?php

namespace App\Models;

use App\Traits\Filterable;
use App\Traits\Searchable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Log;
use Laravel\Sanctum\HasApiTokens;
use Silber\Bouncer\Database\HasRolesAndAbilities;

class Customer extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    use HasRolesAndAbilities;

    use Searchable, Filterable;

    /**
     * ALlowed search fields
     * @var string[]
     */
    protected $fillable = ['password','first_name', 'last_name', 'name', 'is_active', 'email', 'username', 'code', 'balance'];
    protected $searchFields = ['first_name', 'last_name', 'email', 'username', 'code'];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array<string>|bool
     */
    protected $guarded = ['id'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'full_name'
    ];

    /**
     * Bootstrap the model and its traits.
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        static::deleting(function (self $record) {
            Log::info('Deleting user...');
            Log::info(json_encode($record->mediaFiles));
            Log::info(json_encode($record->mediaFiles()->get()));
            foreach ($record->mediaFiles()->get() as $entry) {
                $entry->delete();
            }
        });
    }


    /**
     * Returns the full_name attribute
     * @return string
     */
    public function getFullNameAttribute()
    {
        $names = [];
        foreach (['first_name', 'last_name'] as $key) {
            $value = $this->getAttribute($key);
            if (!empty($value)) {
                $names[] = $value;
            }
        }
        return implode(' ', $names);
    }

    /**
     * Returns the is_admin attribute
     * @return bool
     */
    public function getIsAdminAttribute()
    {
        return $this->isAn('admin');
    }
}
