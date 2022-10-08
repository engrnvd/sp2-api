<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Traits\HasValidationRules;
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
 * @property string $otp
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
    use HasValidationRules, HasApiTokens, HasFactory, Notifiable;

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

    public static function current(): \Illuminate\Contracts\Auth\Authenticatable|self|null
    {
        return auth('sanctum')->user();
    }

    public function generateOtp(): int
    {
        try {
            $this->otp = random_int(100000, 999999);
        } catch (\Exception $e) {
        }
        $this->save();
        return $this->otp;
    }

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

    public function validationRules(): array
    {
        return [
            "name" => "required",
            "email" => "required|email",
            "password" => "required|min:6",
            "is_admin" => "",
        ];
    }

}
