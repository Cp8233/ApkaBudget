@extends('Admin.layouts.app')

@section('content')
<h1 class="h3 mb-2 text-gray-800">Category</h1>

<div class="card shadow mb-4">
    <div class="card-header d-flex justify-content-end">
        <a href="{{ route('admin.add_categories') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Add Category
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead class="thead-dark">
                    <tr>
                        <th>Sr.n</th>
                        <th>Category</th>
                        <th>Image</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($categories as $key => $val)
                    <tr>
                        <td>{{++$key}}</td>
                        <td>{{$val->category}}</td>
                        <td><img src="{{ asset($val->image) }}" class="img-fluid w-25"></td>
                        <td>
                            <a href="{{ route('admin.edit_categories', $val->id) }}" class="btn btn-info btn-sm">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button class="btn btn-sm btn-danger delete-btn" data-url="{{ route('admin.delete_categories', ['id' => $val->id]) }}" title="Delete"> <i class="fa fa-trash" aria-hidden="true"></i> </button>



                            <a href="{{route('admin.subcategories',['CategoryId'=>$val->id])}}" class="btn btn-warning btn-sm">
                                <i class="fas fa-eye"></i> Subcategories
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
