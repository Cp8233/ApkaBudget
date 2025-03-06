@extends('Admin.layouts.app')

@section('content')
<h1 class="h3 mb-2 text-gray-800">Add Service</h1>

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

@if($errors->any())
<div class="alert alert-danger">
    <ul>
        @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="card shadow mb-4">
    <div class="card-header">
        <h6 class="m-0 font-weight-bold text-primary">Add New Service</h6>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.add_service', ['category_id' => $category_id, 'subcategory_id' => $subcategory_id, 'sub_subcategory_id'=>$sub_subcategory_id]) }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label>Service Name</label>
                <input type="text" name="service_name" class="form-control" placeholder="Enter Service Name" required>
            </div>

            <div class="form-group">
                <label>Price</label>
                <input type="number" name="price" class="form-control" placeholder="Enter Price" required>
            </div>

            <div class="form-group">
                <label>Time (in Minutes)</label>
                <input type="time" name="time" class="form-control" placeholder="Enter Time Duration" required>
            </div>

            <div class="form-group">
                <label>Image (Optional)</label>
                <input type="file" name="image" class="form-control">
            </div>

            <button type="submit" class="btn btn-success btn-sm">
                <i class="fas fa-plus"></i> Add Service
            </button>
        </form>
    </div>
</div>
@endsection
