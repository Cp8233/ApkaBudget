@extends('Admin.layouts.app')

@section('content')
<h1 class="h3 mb-2 text-gray-800">Providers</h1>

<div class="card shadow mb-4">
    <div class="card-header d-flex justify-content-end">
        <a href="{{ route('admin.add_providers') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Add providers
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead class="thead-dark">
                    <tr>
                        <th>Sr. no</th>
                        <th>Name</th>
                        <th>Email Id</th>
                        <th>Mobile No</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($providers as $key => $val)
                    <tr>
                        <td>{{ ++$key  }}</td>
                        <td>{{ $val->name }}</td>
                        <td>{{ $val->email }}</td>
                        <td>{{ $val->mobile_no }}</td>
                        <td>
                            <a href="{{ route('admin.edit_providers', ['id' => $val->id]) }}" class="btn btn-sm btn-primary" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button class="btn btn-sm btn-danger delete-btn" data-url="{{ route('admin.delete_providers', ['id' => $val->id]) }}" title="Delete"> <i class="fa fa-trash" aria-hidden="true"></i> </button>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
