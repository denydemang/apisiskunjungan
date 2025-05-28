<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserLogoutRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\remember_token;
use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{

    public function register(UserRegisterRequest $request) : UserResource
    {
        $datavalidated = $request->validated();

        try {
            $user =  new User($datavalidated);
            $user->password = Hash::make($datavalidated["password"]);
            $user->is_active = 1;
            $user->save();
        } catch (\Throwable $th) {
            throw new HttpResponseException(response([
                "errors" => [
                    "general" => [
                        $th->getMessage()
                    ]
                ]
                ],500));
        }


        return new UserResource($user, "Successfully Created User");

    }
    
    public function loginUser(UserLoginRequest $request) {
        $dataValidated = $request->validated();
        try {
            $user = User::where("email", $dataValidated["email"])->where('is_active', 1)->first();
            if($user && Hash::check($dataValidated['password'], $user->password)){
                $user->remember_token = Str::uuid()->toString();
                $user->update();
            }
        } catch (\Throwable $th) {
            throw new HttpResponseException(response([
                "errors" => [
                    "general" => [
                        $th->getMessage()
                    ]
                ]
                ],500));
        };
        if (!$user || !Hash::check($dataValidated['password'], $user->password)) {
            throw new HttpResponseException(response([
                "errors" => [
                    "general" => [
                        "email or password is invalid" 
                    ]
                ]
                ],401));
        }
        return new UserResource($user, "User Login Successfully",$user->remember_token);

    }

    public function logout(UserLogoutRequest $request) :JsonResponse
    {
        $dataValidated = $request->validated();

        try {

            $user = User::where("email" , $dataValidated["email"])->where("remember_token" , $dataValidated["token"])->first();
            if($user){

                $data = [
                    "success" => "Successfully Log Out"
                    ];
                $user->remember_token = null;
                $user->update();
            }
        } catch (\Throwable $th) {
            throw new HttpResponseException(response([
                "errors" => [
                    "general" => [
                        $th->getMessage()
                    ]
                ]
                ],500));
        }

        if (!$user){
            throw new HttpResponseException(response([
                "errors" => [
                    "general" => [
                        "Token or User Not Exists" 
                    ]
                ]
                ],401));
        } 
        return response()->json($data)->setStatusCode(200);
    }
}
