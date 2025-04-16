<?php

namespace App\Action;

use App\Models\User;

class RegisterUser
{
    public function execute(array $data, User $user): User
    {
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->password = bcrypt($data['password']);
        $user->save();

        return $user;
    }
}
