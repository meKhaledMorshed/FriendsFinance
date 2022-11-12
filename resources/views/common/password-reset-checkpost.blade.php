@extends('common.layout')
@section('pageTitle', 'Reset Password | ' . config('app.name'))

@section('content')
<!-- Content -->
<h4 class="mb-4">Reset Password</h4> 

@if(session()->has('notice'))
<p class="mb-2 small text-secondary">{{ session()->get('notice') }} </p>
@endif 

@if(session()->has('error')) 
<span class="mb-2 small text-danger">{{ session()->get('error') }}</span> 
@endif

<form id="formAuthentication" class="mb-3" action="{{ route('password.reset') }}" method="POST"  > 
    @csrf 

    <div class="mb-3"> 
        <label for="otp">Authentication code</label> 
        <input type="text" id="otp" class="form-control" name="otp" placeholder="Authentication code" value="{{old('otp')}}" id="otp" autofocus /> 
        @error('otp') <span class="small text-danger">{{ $message }}</span> @enderror
    </div>

    <div class="mb-3 form-password-toggle"> 
        <label for="password">New Password</label>
        <div class="input-group input-group-merge">
            <input type="password" id="password" class="form-control" name="password" placeholder="New Password" required />
            <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
        </div>
        @error('password') <span class="small text-danger">{{ $message }}</span> @enderror
    </div>

    <div class="mb-3 form-password-toggle"> 
        <label for="confirmpassword">Confirm Password</label>
        <div class="input-group input-group-merge">
            <input type="password" id="confirmpassword" class="form-control" name="confirmpassword" placeholder="Enter password again" required />
            <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
        </div>
        @error('confirmpassword') <span class="small text-danger">{{ $message }}</span> @enderror
    </div> 
    
    <div class="d-flex justify-content-between">
        <a class="btn btn-sm btn-outline-secondary text-warning" id="sendOTP" onclick="sendCodeAgain()" >Send code again</a> 
        <button type="submit" class="btn btn-primary btn-sm">Confirm</button>
    </div>
</form> 

<p class="text-center text-warning" id="otpNotice"></p>
<!-- / Content -->  
@endsection  



@section('script')

<script>
    
  // ================ script to send another code with ajax ================ 
  function sendCodeAgain(){ 
    // Get the base url "http://127.0.0.1:8000" 
    let base_url = window.location.origin;
    // set target url 
    let target_url = base_url + '/sendcodeagain'; 
    // make a XMLHttpRequest object
    const xmlhttp = new XMLHttpRequest();
    xmlhttp.open("GET", target_url, true);
    xmlhttp.send();
    // handel response 
    xmlhttp.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
          document.getElementById("otpNotice").innerHTML = this.response;
      }
    }; 
  }
  // ================ / script tosend another code with ajax ================ 

</script>

@endsection