@extends('Admin.layouts.app')

@section('content')
<h1 class="h3 mb-2 text-gray-800"> SubCategory</h1>

<div class="card shadow mb-4">
    <div class="card-body">
        <form class="" action="{{route('admin.add_subcategories',['CategoryId' => $CategoryId])}}" method="post" enctype="multipart/form-data" id="addFrom">
            @csrf  
            <div class="form-group col-6">
                <label for="category">Category Name</label>
                <input type="text" name="name" id="category" class="form-control" placeholder="Enter category name" required>
                <span class="text-danger" id="name_error"></span>
            </div>

            <div class="form-group col-6">
                <label for="image">Category Image</label>
                <input type="file" name="image" id="image" class="form-control-file" required>
                <span class="text-danger" id="image_error"></span>
            </div>

            <button type="submit" class="btn btn-success">Save</button>
        </form>
    </div>
</div>
@endsection
