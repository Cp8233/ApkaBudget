@extends('Admin.layouts.app')

@section('content')
<h1 class="h3 mb-2 text-gray-800">Users</h1>
<div class="card shadow mb-4">
    <div class="card-body">
        <form action="{{ route('admin.add_users') }}" method="POST" id="addForm">
            @csrf
            <div class="form-group col-6">
                <label for="name">Name</label>
                <input type="text" name="name" id="name" class="form-control" placeholder="Enter name"required>
                <span class="text-danger" id="name_error"></span>
            </div>
            <div class="form-group col-6">
                <label for="mobile">Mobile Number</label>
                <input type="text" name="mobile_no" id="mobile" class="form-control" placeholder="Enter mobile number" required>
                <span class="text-danger" id="mobile_no_error"></span>
            </div>
            {{--<div class="form-group col-6">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" class="form-control" placeholder="Enter email" required>
                <span class="text-danger" id="email_error"></span>
            </div>--}}
            <div class="form-group col-6">
                    <label for="mobile">Address</label>
                    <input type="text" name="address" id="address" class="form-control" placeholder="Search location"
                        required>
                    <span class="text-danger" id="address_error"></span>
                    <input type="hidden" id="latitude" name="latitude">
                    <span class="text-danger" id="latitude_error"></span>
                    <input type="hidden" id="longitude" name="longitude">
                    <span class="text-danger" id="longitude_error"></span>
                </div>
            <div class="form-group col-6">
            <button type="submit" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Add
            </button>
        </div>
        </form>
    </div>
</div>
@endsection
