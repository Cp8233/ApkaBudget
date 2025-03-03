@extends('Admin.layouts.app')

@section('content')
<h1 class="h3 mb-2 text-gray-800">Providers</h1>
<div class="card shadow mb-4">
    <div class="card-body">
        <h4 class="m-3">Add Provider</h4>
        <form action="{{ route('admin.add_providers') }}" method="POST" enctype="multipart/form-data" id="addForm">
            @csrf

            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" name="name" class="form-control" required>
                        <span class="text-danger" id="name_error"></span>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label>Mobile No</label>
                        <input type="text" name="mobile_no" class="form-control" pattern="^\d{10}$" title="Please enter a valid 10-digit mobile number" required>
                        <span class="text-danger" id="mobile_no_error"></span>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control" required>
                        <span class="text-danger" id="password_error"></span>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" required>
                        <span class="text-danger" id="email_error"></span>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label>Profile Image</label>
                        <input type="file" name="profile" class="form-control" required>
                        <span class="text-danger" id="profile_error"></span>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Country</label>
                        <select id="country" name="country_id" class="form-control" required>
                            <option value="">-- Select Country --</option>
                            @foreach($countries as $country)
                                <option value="{{ $country->id }}">{{ $country->name }}</option>
                            @endforeach
                        </select>
                        <span class="text-danger" id="country_id_error"></span>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label>State</label>
                        <select id="state" name="state_id" class="form-control" required>
                            <option value="">-- Select State --</option>
                        </select>
                        <span class="text-danger" id="state_id_error"></span>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>City</label>
                        <select id="city" name="city_id" class="form-control" required>
                            <option value="">-- Select City --</option>
                        </select>
                        <span class="text-danger" id="city_id_error"></span>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label>Pincode</label>
                        <input type="text" name="pincode" class="form-control" required>
                        <span class="text-danger" id="pincode_error"></span>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Address</label>
                        <textarea name="address" class="form-control" required></textarea>
                        <span class="text-danger" id="address_error"></span>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label>Category</label>
                        <select name="category_id" class="form-control" required>
                            <option value="">-- Select Category --</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->category }}</option>
                            @endforeach
                        </select>
                        <span class="text-danger" id="category_id_error"></span>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Experience (in Years)</label>
                        <input type="text" name="experience" class="form-control" required>
                        <span class="text-danger" id="experience_error"></span>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label>Identity Type</label>
                        <select name="identity_id" class="form-control" required>
                            <option value="">-- Select Identity Type --</option>
                            @foreach($identities as $identity)
                                <option value="{{ $identity->id }}">{{ $identity->identity }}</option>
                            @endforeach
                        </select>
                        <span class="text-danger" id="identity_id_error"></span>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Identity Number</label>
                        <input type="text" name="identity_number" class="form-control" required>
                        <span class="text-danger" id="identity_number_error"></span>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label>Identity Image</label>
                        <input type="file" name="identity_image" class="form-control" required>
                        <span class="text-danger" id="identity_image_error"></span>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-2">
                    <button type="submit" class="btn btn-success btn-block">Add</button>
                </div>
            </div>

        </form>
    </div>
</div>
@endsection
