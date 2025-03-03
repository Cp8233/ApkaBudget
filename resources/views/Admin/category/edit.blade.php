@extends('Admin.layouts.app')

@section('content')
<h1 class="h3 mb-2 text-gray-800">Category</h1>

<div class="card shadow mb-4">
    <div class="card-body">
        <h3 class="h3 mb-2 text-gray-800 m-4">Edit Category</h3>
        <form action="{{ route('admin.edit_categories', $category->id) }}" method="POST" enctype="multipart/form-data" id="addForm">
            @csrf

            <div class="form-group">
                <label for="category">Category Name</label>
                <input type="text" name="category" class="form-control" id="category" placeholder="Enter Category Name"
                value="{{ old('category', $category->category) }}" required>
            </div>

            <div class="form-group">
                <label for="image">Category Image</label>
                <input type="file" name="image" class="form-control-file" id="image">
                
                @if($category->image)
                    <br>
                    <img src="{{ asset($category->image) }}" class="img-thumbnail" width="100">
                @endif
            </div>

            <button type="submit" class="btn btn-success">Update Category</button>
        </form>
    </div>
</div>
@endsection
