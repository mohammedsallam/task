<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\UserResource;
use App\User;
use App\VerifyPhone;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api');
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $validate = Validator::make($request->all(), [
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'phone' => ['required', 'regex:/[0-9]+/', 'min:11', 'max:14', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],

        ]);

        if ($validate->fails()){

            return response(['message' => $validate->messages()]);
        }


//        $request->validate([
//            'name' => ['required', 'string', 'min:3', 'max:255'],
//            'phone' => ['required', 'regex:/[0-9]+/', 'min:11', 'max:14', 'unique:users'],
//            'password' => ['required', 'string', 'min:8', 'confirmed'],
//        ]);




        $user =  User::create([

            'name' => $request->name,
            'phone' => $request->phone,
            'password' => Hash::make($request->password)

        ]);

        $code = rand(00000,99999);

//        Mobily::send($user->phone, 'Hello ' . $user->name . 'Code verify is: ' . $code);

        $verifyPhone = new VerifyPhone();
        $verifyPhone->user_id = $user->id;
        $verifyPhone->verify_number = $code;

        $verifyPhone->save();

//        $user->name = $request->name;
//        $user->phone = $request->phone;
//        $user->password = Hash::make($request->password);
//
//        $user->save();

        $user = new UserResource($user);

        return response()->json(['message' => 'User created successfully, we send code verification to your phone', 'data' => $user]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showProfile()
    {
        $user = Auth::user();

        return new UserResource($user);

    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {

        $user = Auth::user();

        $validate = Validator::make($request->all(), [
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'phone' => ['required', Rule::unique('users', 'phone')->ignore($user->id), 'regex:/[0-9]+/', 'min:11', 'max:14'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['nullable', 'string', 'min:8', 'same:password'],

        ]);

        if ($validate->fails()){

            return response(['message' => $validate->messages()]);
        }


        $user = User::find($user->id);

        if ($user->phone != $request->phone){

            $code = rand(00000,99999);

//            Mobily::send($user->phone, 'Hello ' . $user->name . 'Code verify is: ' . $code);

            $verifyPhone = VerifyPhone::where('user_id', $user->id)->first();
            $verifyPhone->verify_number = $code;
            $user->verified = false;
            $verifyPhone->save();


//            return response(['message' => 'We recently send code to your phone']);

        }

        if ($request->password != null) {
            $user->password = Hash::make( $request->password);
        }

        $user->phone = $request->phone;
        $user->name = $request->name;
        $user->save();

        return new UserResource($user);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy()
    {
        $user = Auth::user();

        $user->delete();

//        return response()->json('Deleted Successfully', 204);
    }
}
