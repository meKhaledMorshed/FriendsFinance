@extends('common.layout')

@section('pageTitle', 'Add default Admin | ' . config('app.name'))

<!-- Content -->
@section('content')

<!-- <h4 class="mb-2">Welcome to {{ $entity->name }} ! </h4> -->
<p class="mb-4">Please add core Admin to start the adventure</p>

@if(session()->has('error'))
<span class="text-danger">{{ session()->get('error') }}</span>
@endif

<form id="formAuthentication" class="mb-3" action="{{ url('create-admin') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="mb-3 row g-1">
        <label for="name" class="col-3">Name</label>
        <div class="col-9">
            <input type="text" class="form-control" id="name" name="name" placeholder="Enter Name" value="{{old('name')}}" autofocus required />
            @error('name') <span class="text-danger">{{ $message }}</span> @enderror
        </div>
    </div>

    <div class="mb-3 row g-1">
        <label for="html5-date-input" class="col-md-3 col-form-label" title="Entity established Date.">Birthday</label>
        <div class="col-md-5">
            <input class="form-control" type="date" name="dob" id="html5-date-input" value="{{old('dob')}}" required />
            @error('dob') <span class="text-danger">{{ $message }}</span> @enderror
        </div>
        <div class="col-md-4">
            <select class="form-select" name="gender" aria-label="Default select example">
                @foreach($genders as $gender)
                <option value="{{$gender->optionValue}}" {{ $gender->optionValue == 'Male'?'Selected':''}}>{{$gender->optionName}}</option>
                @endforeach
            </select>
            @error('gender') <span class="text-danger">{{ $message }}</span> @enderror
        </div>
    </div>

    <div class="mb-3 row g-1">
        <label for="html5-email-input" class="col-md-3 col-form-label">Email</label>
        <div class="col-md-9">
            <input class="form-control" type="email" name="email" placeholder="admin@example.com" value="{{old('email')}}" id="html5-email-input" required />
            @error('email') <span class="text-danger">{{ $message }}</span> @enderror
        </div>
    </div>

    <div class="mb-3">

        <div class="mb-3">
            <div class="row  g-1">
                <div class="col-md-6">
                    <select class="form-select" name="ccc" aria-label="Default select example">
                        @foreach($cccs as $ccc)
                        <option value="{{$ccc->optionValue}}">{{$ccc->optionName}}</option>
                        @endforeach
                    </select>
                    @error('ccc') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="col-md-6" style="display: flex; align-items: center; gap: 10px;">
                    <input class="form-control" type="tel" name="tel" pattern="[0-9]{10}" value="{{old('tel')}}" placeholder="1824608637" id="html5-tel-input" required />
                    @error('tel') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

    </div>


    <div class="mb-3 row g-1 form-password-toggle">
        <label for="password" class="col-md-3 col-form-label">Password</label>
        <div class="col-md-9">
            <div class="input-group input-group-merge">
                <input type="password" id="password" class="form-control" name="password" placeholder="Password" required />
                <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
            </div>
            @error('password') <span class="text-danger">{{ $message }}</span> @enderror
        </div>
    </div>
    <div class="mb-3 row g-1">
        <label for="photo" class="col-md-3 col-form-label">Photo</label>
        <div class="col-md-9">
            <input class="form-control" type="file" name="photo" id="photo" required />
            @error('photo') <span class="small text-danger">{{ $message }}</span> @enderror
        </div>
    </div>
    <button type="submit" class="btn btn-primary d-grid w-100">Create Admin</button>
</form>
<div class="text-center">
    <a href="{{ url('addentity') }}" class="d-flex align-items-center justify-content-center">
        <i class="bx bx-chevron-left scaleX-n1-rtl bx-sm"></i>Back
    </a>
</div>



@endsection