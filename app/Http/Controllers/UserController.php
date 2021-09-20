<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Models\User;

class UserController extends Controller
{
    public function register(Request $request){
        $validator = Validator::make($request->all(),[
            "first_name"=>"required",
            "last_name"=>"required",
            "email"=>"required|email|unique:users,email",
            "password"=>"required|min:3",
            "phone"=>"required|min:10"
        ]);
        if($validator->fails()){
            return $this->validationErrors($validator->errors());
        }

        $user=User::create([
            'first_name'=>$request->first_name,
            'last_name'=>$request->last_name,
            'full_name'=>$request->first_name." ".$request->last_name,
            "email"=>$request->email,
            "phone"=>$request->phone,
            "password"=>Hash::make($request->password)
        ]);

        return response()->json(["status"=>"success","error"=>false,"message"=>"success user registered"],201);
    }

    public function login(Request $request){
        $validator=Validator::make($request->all(),[
            "email"=>"required|email",
            "password"=>"required|min:3"
        ]);
        if($validator->fails()){
            return $this->validationErrors($validator->errors());
        }

        try{
            if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){
                $user=Auth::user();
                $token=$user->createToken('token')->accessToken;
                return response()->json([
                    "status"=>"success",
                    "error"=>false,
                    "message"=>"success you are logged in.",
                    "token"=>$token,
                ]);
            }
            return response()->json(["status"=>"failed","message"=>"Failed invalid credentials"],404);
        }
        catch(Exception $e){
            return response()->json(["status"=>"failed","message"=>$e->getMessage()],404);
        }
    }

    public function user(){
        try{
            $user=Auth::user();
            return response()->json(["status"=>"success","error"=>false, "data"=>$user],200);
        }catch(NotFoundHttpException $exception){
            return response()->json(["status"=>"failed","error"=>$exception],401);
        }
    }

    public function logout(){
        if(Auth::check()){
            Auth::user()->token()->revoke();
            return response()->json(["status"=>"success","error"=>false, "message"=>"success. you are logged out"],200);

        }
        return response()->json(["status"=>"failed","error"=>true, "message"=>"Failed you are already logged out "]);
    }
}
