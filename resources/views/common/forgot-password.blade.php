@extends('common.layout')

@section('pageTitle', 'Forgot Password | ' . config('app.name'))

@section('content')

<!-- Content -->
<!-- Two-factor authentication -->
 

<div class="d-flex justify-content-center">
  <h4 class="mb-2">Password recovery</h4>
</div>
<p class="mb-4 small">To get a password reset verification code, first confirm the email address you added to your account.</p>
<form id="formAuthentication" class="mb-3" action="{{ route('password.reseting.check') }}" method="POST"> 
    @csrf 
    
    <div class="mb-3"> 
      <input type="email" class="form-control" id="email" name="email" placeholder="Enter email address" value="{{old('email')}}" autofocus />
      @if(session()->has('error')) <span class="small text-danger">{{ session()->get('error') }}</span> @endif
      @error('email') <span class="small text-danger">{{ $message }}</span> @enderror
    </div>
    
  <div class="d-grid gap-2 d-md-flex justify-content-md-end">
      <button class="btn btn-primary">Confirm</button>
  </div>

</form>
<!-- /Two-factor authentication -->
<!-- / Content -->  

@endsection

