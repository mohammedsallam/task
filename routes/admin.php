<?php

Route::get('/home', function () {

    $users['user'] = Auth::user();
    $users[] = Auth::guard()->user();
    $users[] = Auth::guard('admin')->user();

//    dd($users);

    return view('admin.home');
})->name('home');


Route::resource('users', 'UsersController');


