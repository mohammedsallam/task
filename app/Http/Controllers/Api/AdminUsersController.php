<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\UserResource;
use App\User;
use App\VerifyPhone;
use Illuminate\Validation\Rule;
use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class AdminUsersController extends Controller
{

    public function __construct()
    {
        $this->middleware('admin:adminApi');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::all();

        return UserResource::collection($users);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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

        return new UserResource($user);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::find($id);

        return new UserResource($user);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

//        $request->validate([
//            'name' => ['required', 'string', 'min:3', 'max:255'],
//            'phone' => ['required', Rule::unique('users', 'phone')->ignore($id), 'regex:/[0-9]+/', 'min:11', 'max:14'],
//            'password' => ['required', 'string', 'min:8', 'confirmed'],
//
//        ]);

        $validate = Validator::make($request->all(), [
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'phone' => ['required', Rule::unique('users', 'phone')->ignore($id), 'regex:/[0-9]+/', 'min:11', 'max:14'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['nullable', 'string', 'min:8', 'same:password'],

        ]);

        if ($validate->fails()){

            return back()->withErrors($validate)->withInput();
        }


        $user = User::find($id);

        if ($user->phone != $request->phone){

            $code = rand(00000,99999);

//            Mobily::send($user->phone, 'Hello ' . $user->name . 'Code verify is: ' . $code);

            $verifyPhone = VerifyPhone::where('user_id', $id)->first();
            $verifyPhone->verify_number = $code;
            $user->verified = false;
            $verifyPhone->save();

            return response(['message' => 'We recently send code to your phone']);

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
    public function destroy($id)
    {
        $user = User::find($id);

        $user->delete();

        return response()->json('Deleted Successfully', 204);
    }
}
