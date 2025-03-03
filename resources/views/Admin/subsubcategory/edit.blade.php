@extends('Admin.layouts.app')

@section('content')
<h1 class="h3 mb-2 text-gray-800">Edit Sub-Sub Category</h1>

@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<form action="{{ route('admin.edit_subsubcategory', ['subcategory_id' => $subcategory_id, 'id' => $subSubCategory->id]) }}" method="POST" enctype="multipart/form-data" >
    @csrf

    <div class="form-group">
        <label>Sub-Sub Category Name</label>
        <input type="text" name="sub_subcategory_name" class="form-control" value="{{ $subSubCategory->sub_subcategory_name }}" required>
    </div>

    <div class="form-group">
        <label>Image</label>
        @if($subSubCategory->image)
            <img src="{{ asset($subSubCategory->image) }}" class="img-fluid" style="width: 100px; height: 100px;">
        @endif
        <input type="file" name="image" class="form-control mt-2">
    </div>

    <button type="submit" class="btn btn-success">Update</button>
    <a href="{{ route('admin.subSubCategories', ['subcategory_id' => $subcategory_id]) }}" class="btn btn-secondary">Cancel</a>
</form>
@endsection
