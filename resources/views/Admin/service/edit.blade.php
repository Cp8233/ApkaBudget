@extends('Admin.layouts.app')

@section('content')
<h1 class="h3 mb-2 text-gray-800">Edit Service</h1>

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
        <h6 class="m-0 font-weight-bold text-primary">Edit Service</h6>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.edit_service', ['category_id' => $category_id, 'subcategory_id' => $subcategory_id, 'id' => $subsubcategory->id]) }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label>Service Name</label>
                <input type="text" name="service_name" value="{{ $subsubcategory->service_name }}" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Price</label>
                <input type="text" name="price" value="{{ $subsubcategory->price }}" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Time (in Minutes)</label>
                <input type="time" name="time" value="{{ $subsubcategory->time }}" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Current Image</label><br>
                @if($subsubcategory->image)
                <img src="{{ asset('uploads/services/' . $subsubcategory->image) }}" alt="subsubcategory Image" class="img-thumbnail mb-2" style="max-width: 150px;">
                @endif
            </div>

            <div class="form-group">
                <label>New Image (Optional)</label>
                <input type="file" name="image" class="form-control">
            </div>

            <button type="submit" class="btn btn-primary btn-sm">
                <i class="fas fa-edit"></i> Update
            </button>
        </form>
    </div>
</div>
@endsection
