@extends('Admin.layouts.app')

@section('content')
    <h1 class="h3 mb-2 text-gray-800">Provider</h1>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('admin.edit_providers', $provider->id) }}" method="POST" enctype="multipart/form-data" id="addForm">
                @csrf
                <h3 class="h3 m-4 text-gray-800 text-center">Edit Provider</h3>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" name="name" class="form-control"  value="{{ old('name', $provider->name) }}"required>
                            <span class="text-danger" id="name_error"></span>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="mobile_no">Mobile Number</label>
                            <input type="text" name="mobile_no" class="form-control" value="{{ old('mobile_no', $provider->mobile_no) }}" required disabled>
                            <span class="text-danger" id="mobile_no_error"></span>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Profile Image</label>
                            <input type="file" name="profile" class="form-control">
                            @if($provider->profile)
                                <img src="{{ asset('uploads/profiles/' . $provider->profile) }}" alt="Profile Image" width="80" class="mt-2">
                            @endif
                            <span class="text-danger" id="profile_error"></span>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $provider->email) }}" required disabled>
                            <span class="text-danger" id="email_error"></span>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="country_id">Country</label>
                            <select name="country_id" class="form-control">
                                @foreach ($countries as $country)
                                    <option value="{{ $country->id }}" {{ old('country_id', $provider->country_id) == $country->id ? 'selected' : '' }}>
                                        {{ $country->name }}
                                    </option>
                                @endforeach
                            </select>
                            <span class="text-danger" id="country_id_error"></span>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="state_id">State</label>
                            <select name="state_id" id="state_id" class="form-control">
                                <option value="">Select State</option>
                                @foreach ($states as $state)
                                    <option value="{{ $state->id }}" {{ old('state_id', $provider->state_id) == $state->id ? 'selected' : '' }}>
                                        {{ $state->name }}
                                    </option>
                                @endforeach
                            </select>
                            <span class="text-danger" id="state_id_error"></span>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="city_id">City</label>
                            <select name="city_id" id="city_id" class="form-control">
                                @if ($cities->isNotEmpty())
                                    @foreach ($cities as $city)
                                        <option value="{{ $city->id }}" {{ old('city_id', $provider->city_id) == $city->id ? 'selected' : '' }}>
                                            {{ $city->name }}
                                        </option>
                                    @endforeach
                                @else
                                    <option value="">Select State First</option>
                                @endif
                            </select>
                            <span class="text-danger" id="city_id_error"></span>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="pincode">Pincode</label>
                            <input type="text" name="pincode" class="form-control" value="{{ old('pincode', $provider->pincode) }}" required>
                            <span class="text-danger" id="pincode_error"></span>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="address">Address</label>
                            <textarea name="address" class="form-control" required>{{ old('address', $provider->address) }}</textarea>
                            <span class="text-danger" id="address_error"></span>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="category_id">Category</label>
                            <select name="category_id" id="category_id" class="form-control" required>
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id', $provider->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->category }}
                                    </option>
                                @endforeach
                            </select>
                            <span class="text-danger" id="category_id_error"></span>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="experience">Experience</label>
                            <input type="text" name="experience" class="form-control" value="{{ old('experience', $provider->experience) }}" required>
                            <span class="text-danger" id="experience_error"></span>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="identity_id">Identity Type</label>
                            <select name="identity_id" class="form-control" required>
                                <option value="">Select Identity Type</option>
                                @foreach($identities as $identity)
                                    <option value="{{ $identity->id }}" {{ old('identity_id', $provider->identity_id ?? '') == $identity->id ? 'selected' : '' }}>
                                        {{ $identity->identity }}
                                    </option>
                                @endforeach
                            </select>
                            <span class="text-danger" id="identity_id_error"></span>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="identity_number">Identity Number</label>
                            <input type="text" name="identity_number" id="identity_number" class="form-control" value="{{ old('identity_number', $provider->identity_number) }}" required>
                            <span class="text-danger" id="identity_number_error"></span>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="identity_image">Identity Image</label>
                            <input type="file" name="identity_image" class="form-control">
                            @if($provider->identity_image)
                                <img src="{{ asset('uploads/identities/' . $provider->identity_image) }}" alt="identity_image" width="80" class="mt-2">
                            @endif
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-success mt-3">Update Provider</button>
            </form>
        </div>
    </div>
@endsection

