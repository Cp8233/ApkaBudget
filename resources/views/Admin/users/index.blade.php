@extends('Admin.layouts.app')

@section('content')
<h1 class="h3 mb-2 text-gray-800">Users</h1>

<div class="card shadow mb-4">
    <div class="card-header d-flex justify-content-end">
        <a href="{{ route('admin.add_users') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Add Users
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead class="bg-primary text-white">
                    <tr>
                        <th>Sr. no</th>
                        <th>Name</th>
                        <th>Email Id</th>
                        <th>Mobile No</th>
                        <th>Device ID</th>
                        <th>IP Address</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $key => $val)
                    <tr>
                        <td>{{ ++$key }}</td>
                        <td>{{ $val->name }}</td>
                        <td>{{ $val->email }}</td>
                        <td>{{ $val->mobile_no }}</td>
                        <td>{{ $val->device_id }}</td>
                        <td>{{$val->ip_address}}</td>
                        <td>
                            <a href="{{route('admin.edit_users', ['id' => $val->id])}}" class="btn btn-sm btn-primary" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            {{--<button class="btn btn-sm btn-danger delete-btn" data-url="{{ route('admin.delete_users', ['id' => $val->id]) }}" title="Delete"> <i class="fa fa-trash" aria-hidden="true"></i> </button>--}}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
