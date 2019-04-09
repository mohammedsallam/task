@extends('admin.layout.default')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-4 col-md-offset-2">
            <div class="card">
                <div class="card-header">Dashboard</div>

                <div class="card-body">
                    <div><a href="{{route('admin.users.index')}}">All users</a></div>
                    <div><a href="{{route('admin.users.create')}}">Add users</a></div>
                </div>
            </div>
        </div>

        @yield('users_content')

    </div>
</div>
@endsection
