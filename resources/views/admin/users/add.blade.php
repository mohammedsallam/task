@extends('admin.home')
@section('users_content')
    <div class="col-10 offset-md-1 mt-5">
        @include('partials.flash')
        <div class="card">
          <div class="card-body">
            <h4 class="card-title">Add new user</h4>
              <form method="POST" action="{{route('admin.users.store')}}">
                  @csrf
                  <div class="form-group row">
                      <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('Name') }}</label>

                      <div class="col-md-6">
                          <input id="name" type="text" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" name="name" value="{{ old('name') }}"  autofocus>

                          @if ($errors->has('name'))
                              <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('name') }}</strong>
                        </span>
                          @endif
                      </div>
                  </div>

                  <div class="form-group row">
                      <label for="phone" class="col-md-4 col-form-label text-md-right">{{ __('Phone number') }}</label>

                      <div class="col-md-6">
                          <input id="phone" type="text" class="form-control{{ $errors->has('phone') ? ' is-invalid' : '' }}" name="phone" value="{{ old('phone') }}" required>

                          @if ($errors->has('phone'))
                              <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('phone') }}</strong>
                        </span>
                          @endif
                      </div>
                  </div>

                  <div class="form-group row">
                      <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('Password') }}</label>

                      <div class="col-md-6">
                          <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password">

                          @if ($errors->has('password'))
                              <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('password') }}</strong>
                        </span>
                          @endif
                      </div>
                  </div>

                  <div class="form-group row">
                      <label for="password-confirm" class="col-md-4 col-form-label text-md-right">{{ __('Confirm Password') }}</label>

                      <div class="col-md-6">
                          <input id="password-confirm" type="password" class="form-control" name="password_confirmation">
                      </div>
                  </div>

                  <div class="form-group row mb-0">
                      <div class="col-md-6 offset-md-4">
                          <button type="submit" class="btn btn-primary">
                              {{ __('Add user') }}
                          </button>
                      </div>
                  </div>
              </form>
          </div>
        </div>
    </div>
@endsection
