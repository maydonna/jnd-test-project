<?php

namespace App\Http\Controllers;

use App\Action\RegisterUser;
use App\Http\Requests\RegisterUserRequest;
use App\Models\User;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as ResponseHttpCode;

class RegisterUserController extends Controller
{
    public function store(RegisterUserRequest $request, RegisterUser $registerUser): Response
    {
        $data = $request->validated();

        $registerUser->execute($data, new User());

        return response([], ResponseHttpCode::HTTP_CREATED);
    }
}
