@extends('Admin.layouts.app')

@section('content')
<h1 class="h3 mb-2 text-gray-800">Sub-Sub Categories</h1>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<div class="card shadow mb-4">
    <div class="card-header d-flex justify-content-end">
        <a href="{{ route('admin.addSubSubCategory', ['subcategory_id' => $subcategory_id ?? 0, 'subcategory' => $subcategory_name ?? 'default']) }}" 
            class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Add Sub SubCategory
        </a>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered text-center" id="dataTable" width="100%" cellspacing="0">
                <thead class="thead-dark">
                    <tr>
                        <th>Sr.n</th>
                        <th>Sub_SubCategory Name</th>
                        <th>Image</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($subSubCategories as $key => $val)
                    <tr>
                        <td>{{ ++$key }}</td>
                        <td>{{ $val->sub_subcategory_name }}</td>
                        <td>
                            @if ($val->image)
                                <img src="{{ asset($val->image) }}" class="img-fluid rounded shadow-sm" style="width: 60px; height: 60px; object-fit: cover;">
                            @else
                                No Image
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.edit_subsubcategory', ['subcategory_id' => $val->subcategory_id, 'id' => $val->id]) }}" 
                               class="btn btn-info btn-sm">
                                <i class="fas fa-edit"></i> Edit
                            </a>

                            <button class="btn btn-sm btn-danger delete-btn" 
                                data-url="{{ route('admin.delete_subsubcategory', ['subcategory_id' => $val->subcategory_id, 'id' => $val->id]) }}" 
                                title="Delete">
                                <i class="fa fa-trash" aria-hidden="true"></i>
                            </button>

                            <a href="{{ route('admin.service', ['category_id' => $val->subcategory->category->id ?? 0, 'subcategory_id' => $val->subcategory_id, 'id' => $val->id ?? 0]) }}" 
                               class="btn btn-success btn-sm">
                                <i class="fas fa-eye"></i> Services
                            </a>    
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

