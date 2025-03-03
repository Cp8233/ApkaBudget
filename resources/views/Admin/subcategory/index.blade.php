@extends('Admin.layouts.app')

@section('content')
<h1 class="h3 mb-2 text-gray-800">SubCategory</h1>

<div class="card shadow mb-4">
    <div class="card-header d-flex justify-content-end">
        <a href="{{ route('admin.add_subcategories',['CategoryId'=>$CategoryId]) }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Add SubCategory
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered text-center" id="dataTable" width="100%" cellspacing="0">
                <thead class="thead-dark">
                    <tr>
                        <th>Sr.n</th>
                        <th>Sub Category</th>
                        <th>Image</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $key => $val)
                    <tr>
                        <td>{{++$key}}</td>
                        <td>{{$val->name}}</td>
                        <td>
                            <img src="{{ asset($val->image) }}" class="img-fluid rounded shadow-sm" 
                                 style="width: 60px; height: 60px; object-fit: cover;">
                        </td>
                        <td>
                            <a href="{{ route('admin.edit_subcategories', ['CategoryId' => $val->category_id, 'id' => $val->id]) }}" 
                               class="btn btn-info btn-sm my-1">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            
                            <button class="btn btn-sm btn-danger delete-btn" data-url="{{ route('admin.delete_subcategories', ['CategoryId' => $CategoryId, 'id' => $val->id]) }}" title="Delete"> 
                                <i class="fa fa-trash" aria-hidden="true"></i> 
                            </button>

                            <a href="{{ route('admin.subSubCategories', ['subcategory_id' => $val->id]) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-eye"></i> Sub-Sub Category
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
