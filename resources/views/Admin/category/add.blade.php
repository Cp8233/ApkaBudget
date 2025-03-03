@extends('Admin.layouts.app')

@section('content')
<h1 class="h3 mb-2 text-gray-800">Category</h1>

<div class="card shadow mb-4">
    <div class="card-body">
        <h3 class="h3 mb-2 text-gray-800 m-4">Add Category</h3>
        <form action="{{ route('admin.add_categories') }}" method="POST" enctype="multipart/form-data"  id="addForm">
            @csrf

            <div class="form-group">
                <label for="category">Category Name</label>
                <input type="text" name="category" class="form-control" id="category" placeholder="Enter Category Name" required>
            </div>

            <div class="form-group">
                <label for="image">Category Image</label>
                <input type="file" name="image" class="form-control-file" id="image" required>
            </div>

            <button type="submit" class="btn btn-success">Add Category</button>
        </form>
    </div>
</div>
@endsection

