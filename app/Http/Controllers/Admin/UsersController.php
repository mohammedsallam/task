<?php

namespace App\Http\Controllers\Admin;

use App\Http\Resources\UserResource;
use App\User;
use App\VerifyPhone;
use function Couchbase\defaultDecoder;
use Illuminate\Validation\Rule;
use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $users = User::orderBy('id', 'ASC')->paginate(4);

        $users = UserResource::collection($users);

        return view('admin.users.all', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.users.add');
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

            return back()->withErrors($validate)->withInput();
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

        return redirect()->route('admin.users.index')->with('success', 'User added successfully');
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
        $user = User::find($id);

        return view('admin.users.editForm', compact('user'));
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

//        $messages = [
//
//            'confirmed' => 'passwords not matchs',
//            'required' => 'The :attribute must be filled'
//        ];


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

//            return redirect()->route('admin.users.index')->with('success', 'We recently send code to your phone');
        }

        if ($request->password != null) {
            $user->password = Hash::make( $request->password);
        }

        $user->phone = $request->phone;
        $user->name = $request->name;
        $user->save();



        return redirect()->route('admin.users.index')->with('success', 'User Updated successfully');
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

        return back()->with('success', 'Deleted Successfully');
    }
}
