<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Arr;

/**
 * App\Models\User
 *
 * @property string $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string $is_admin
 * @property string $created_at
 * @property string $updated_at
 * @method static \Illuminate\Database\Query\Builder|User whereName($value)
 * @method static \Illuminate\Database\Query\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Query\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Query\Builder|User whereIsAdmin($value)
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'email_verified_at',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public static $bulkEditableFields = ['name', 'email', 'is_admin'];

    public static function findRequested()
    {
        $query = User::query();

        // search results based on user input
        if (\Request::get('name')) $query->where('name', 'like', '%' . request('name') . '%');
        if (\Request::get('email')) $query->where('email', 'like', '%' . request('email') . '%');
        if (\Request::get('password')) $query->where('password', 'like', '%' . request('password') . '%');
        if (\Request::has('is_admin')) $query->where('is_admin', request('is_admin'));

        // sort results
        if (\Request::has("sort")) $query->orderBy(request("sort"), request("sortType", "asc"));

        // paginate results
        if ($resPerPage = request("perPage"))
            return $query->paginate(intval($resPerPage));
        return $query->get();
    }

    public function validationRules($attributes = null)
    {
        $rules = [
            "name" => "required",
            "email" => "required|email",
            "password" => "required",
            "is_admin" => "required",
        ];

        // no list is provided
        if (!$attributes)
            return $rules;

        // a single attribute is provided
        if (!is_array($attributes))
            return [$attributes => Arr::get($rules, $attributes, '')];

        // a list of attributes is provided
        $newRules = [];
        foreach ($attributes as $attr)
            $newRules[$attr] = Arr::get($rules, $attr, '');
        return $newRules;
    }

}
