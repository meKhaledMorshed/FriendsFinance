@extends('common.layout')

@section('pageTitle', 'Login | ' . config('app.name'))

<!-- Content -->
@section('content')

  <h4 class="mb-2">Welcome to {{ $entity->name }} ! </h4>
  <p class="mb-4">Please sign-in to start the adventure</p>

  @if(session()->has('error'))
  <span class="text-danger">{{ session()->get('error') }}</span>
  @endif

  <form id="formAuthentication" class="mb-3" action="{{ route('login.check') }}" method="POST">
    @csrf 
    <div class="mb-3">
      <label for="email" class="form-label">Email</label>
      <input type="text" class="form-control" id="email" name="email" placeholder="Enter your email" autofocus value="{{ old('email') }}" />
      @error('email') <span class="text-danger">{{ $message }}</span> @enderror
    </div>
    <div class="mb-3 form-password-toggle">
      <div class="d-flex justify-content-between">
        <label class="form-label" for="password">Password</label>
        <a href="{{ route('password.forgot') }}"> <small>Forgot Password?</small> </a>
      </div>
      <div class="input-group input-group-merge">
        <input type="password" id="password" class="form-control" name="password" placeholder="Type your password here" aria-describedby="password" />
        <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
      </div>
      @error('password') <span class="text-danger">{{ $message }}</span> @enderror
    </div>
    <div class="mb-3">
      <div class="form-check">
        <input class="form-check-input" type="checkbox" id="remember-me" />
        <label class="form-check-label" for="remember-me"> Remember Me </label>
      </div>
    </div>
    <div class="mb-3">
      <button class="btn btn-primary d-grid w-100" type="submit">Sign in</button>
    </div>
  </form>
  <p class="text-center">
    <span>New on our platform?</span>
    <a href="{{ url('apply-registration') }}"> <span>Create an account</span> </a>
  </p>
                  

                
@endsection