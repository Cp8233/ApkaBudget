@extends('Admin.layouts.app')

@section('content')
    <h1 class="h3 mb-2 text-gray-800">Addresses</h1>
    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('admin.add_address', ['userId' => $userId]) }}" method="POST" id="addForm">
                @csrf
                <input type="hidden" name="userId" value="{{ $userId }}">
                <div class="row">
                    <div class="form-group col-12">
                        <label for="address">Address</label>
                        <input type="text" name="address" id="address" class="form-control"
                            placeholder="Search location" required>
                        <span class="text-danger" id="address_error"></span>
                        <input type="hidden" id="latitude" name="latitude">
                        <span class="text-danger" id="latitude_error"></span>
                        <input type="hidden" id="longitude" name="longitude">
                        <span class="text-danger" id="longitude_error"></span>
                    </div>
                    <div class="form-group col-6">
                        <label for="flat_no">Flan No.</label>
                        <input type="text" name="flat_no" id="flat_no" class="form-control" required>
                        <span class="text-danger" id="flat_no_error"></span>
                    </div>
                    <div class="form-group col-6">
                        <label for="landmark">Landmark</label>
                        <input type="text" name="landmark" id="landmark" class="form-control" required>
                        <span class="text-danger" id="landmark_error"></span>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary btn-sm">Add Address</button>
            </form>
        </div>
    </div>
@endsection
