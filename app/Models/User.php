<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

   
protected $fillable = [
    'username', 
    'email',
    'phone',    
    'role',     
    'password',
];

public function loans() {
    return $this->hasMany(Loan::class);
}

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // cambie el reset de contraseña para que mande un codigo de 6 digitos en lugar de un token largo
    // asi el usuario solo copia el codigo y lo pega, mas facil que un link gigante
    public function sendPasswordResetNotification($token)
    {
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        \Illuminate\Support\Facades\DB::table('password_reset_tokens')
            ->where('email', $this->email)->delete();
        \Illuminate\Support\Facades\DB::table('password_reset_tokens')->insert([
            'email' => $this->email,
            'token' => $code,
            'created_at' => now(),
        ]);
        $this->notify(new \App\Notifications\CustomResetPassword($code));
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
