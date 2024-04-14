<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\api\BaseController;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class RegisterController extends BaseController
{
    public function Register(Request $request): JsonResponse
    {
        $Validator = Validator::make($request->all(), [
            'name' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required'],
            'c_password' => ['required', 'same:password']
        ]);

        if ($Validator->fails()) {
            return $this->sendError('Validation error', $Validator->errors());
        }

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $success['token'] = $user->createToken('MyApp')->plainTextToken;
        $success['name'] = $user->name;
        return $this->sendResponse($success, 'User registered successfully.!');
    }

    public function Login(Request $request)
    {
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            $success['token'] = $user->createToken('MyApp')->plainTextToken;
            $success['name'] = $user->name;
            return $this->sendResponse($success, 'User login successfully.');
        } else {
            return $this->sendError('Unauthorised', ['Error' => 'Unauthorised']);
        }
    }
}
