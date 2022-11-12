@extends('common.layout')

@section('pageTitle', 'Two factor authentication | ' . config('app.name'))

@section('content')

<!-- Content -->
<!-- Two-factor authentication -->
<div class="text-center">
  <h4>Two factor authentication 🔒</h4>
</div>

@if(session()->has('notice'))
<p class="mb-4">{{ session()->get('notice') }}</p>
@else
<p class="mb-4">A verification code sent to email attached with your account.</p>
@endif

<form id="formAuthentication" class="mb-3" action="{{ route('login.2fa.check') }}" method="POST">
  @csrf

  <div class="mb-3">
    @if(session()->has('error')) <span class="text-danger">{{ session()->get('error') }}</span> @endif
    @error('otp') <span class="text-danger">{{ $message }}</span> @enderror
    <input type="text" class="form-control" id="otp" name="otp" placeholder="Enter authentication code" autofocus />
  </div>

  <button class="btn btn-primary d-grid w-100">Confirm</button>

</form>

<div class="text-center">
  <button class="btn" id="sendOTP" onclick="sendCodeAgain()"><i class='bx bx-send'></i> Send code again</button>
</div>

<p class="text-center text-success block" id="otpNotice"></p>
<!-- /Two-factor authentication -->
<!-- / Content -->

@endsection


@section('script')

<script>
  // ================ script to send another code with ajax ================ 
  function sendCodeAgain() {
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

        setTimeout(() => {

          document.getElementById('otpNotice').style.display = 'none';

        }, 10000);

      }
    };
  }
  // ================ / script tosend another code with ajax ================  
</script>

@endsection