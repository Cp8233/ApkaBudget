@extends('Admin.layouts.app')

@section('content')
    <h1 class="h3 mb-2 text-gray-800">Addresses</h1>

    <div class="card shadow mb-4">
        <div class="card-header d-flex justify-content-end">
            <a href="{{ route('admin.add_address', ['userId' => $userId]) }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Add Address
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th>Sr. no</th>
                            <th>Type</th>
                            <th>Address</th>
                            <th>Flat No</th>
                            <th>Landmark</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($addresses as $key => $val)
                            <tr>
                                <td>{{ ++$key }}</td>
                                <td>{{ $val->type == 1 ? 'Home' : 'Other' }}</td>
                                <td>{{ $val->address }}</td>
                                <td>{{ $val->flat_no }}</td>
                                <td>{{ $val->landmark }}</td>
                                <td>
                                    <a href="#" class="btn btn-sm btn-primary" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    {{-- <button class="btn btn-sm btn-danger delete-btn" data-url="{{ route('admin.delete_providers', ['id' => $val->id]) }}" title="Delete"> <i class="fa fa-trash" aria-hidden="true"></i> </button> --}}
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
