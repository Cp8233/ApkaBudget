@extends('Admin.layouts.app')

@section('content')
<h1 class="h3 mb-2 text-gray-800">Add Sub-Sub Category</h1>

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
    <div class="card-body">
        <form action="{{ route('admin.addSubSubCategory', ['subcategory_id' => $subcategory_id]) }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label for="name">Sub-Sub Category Name</label>
                <input type="text" name="sub_subcategory_name" id="name" class="form-control" placeholder="Enter name" required>
            </div>

            <div class="form-group">
                <label>Image</label>
                <input type="file" name="image" class="form-control">
            </div>

            <button type="submit" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Add
            </button>
        </form>
    </div>
</div>
@endsection
