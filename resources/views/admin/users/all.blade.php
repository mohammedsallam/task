@extends('admin.home')

@section('users_content')
    <div class="col-10 offset-md-1 mt-5">
        @include('partials.flash')
        <table class="table table-striped table-hover table-bordered">
            <thead>
            <tr>
                <th>#ID</th>
                <th>User name</th>
                <th>Phone number</th>
                {{--<th>Verified</th>--}}
                {{--<th>Code</th>--}}
                <th>Delete</th>
                <th>Edit</th>
            </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    <tr>
                        <td scope="row">{{$user->id}}</td>
                        <td>{{$user->name}}</td>
                        <td>{{$user->phone}}</td>
{{--                        <td>{{$user->verified}}</td>--}}
{{--                        <td>{{$user->VerifyPhone->verify_number}}</td>--}}
                        <td>
                            <form action="{{route("admin.users.destroy", $user->id)}}" method="POST">
                                {{csrf_field()}}
                                <input type="hidden" name="_method" value="DELETE">
                                <input type="submit" value="Delete" class="btn btn-danger" onclick="if (!confirm('Do you want delete user?')){return false}">
                            </form>
                        </td>
                        <td>
                            <a class="btn btn-primary" href="{{route("admin.users.edit", $user->id)}}">Edit</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        {{$users->links()}}
    </div>

@endsection
