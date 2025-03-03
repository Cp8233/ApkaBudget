@extends('Admin.layouts.app')

@section('content')
<h1 class="h3 mb-2 text-gray-800">Country</h1>

<div class="card shadow mb-4">
    <div class="card-header d-flex justify-content-end">
        <a href="{{ route('admin.add_countries') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Add Country
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead class="thead-dark">
                    <tr>
                        <th>Sr.n</th>
                        <th>Short Name</th>
                        <th>Name</th>
                        <th>Country Code</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $key => $val)
                    <tr>
                        <td>{{++$key}}</td>
                        <td>{{$val->shortname}}</td>
                        <td>{{$val->name}}</td>
                        <td>{{$val->phonecode}}</td>
                        <td>
                            <a href="{{ route('admin.edit_countries', $val->id) }}" class="btn btn-info btn-sm">
                                <i class="fas fa-edit"></i>
                            </a>
                            
                            <form action="{{ route('admin.delete_countries', $val->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this category?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
