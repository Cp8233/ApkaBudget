@extends('Admin.layouts.app')

@section('content')
    <h1 class="h3 mb-2 text-gray-800">Bookings</h1>

    <div class="card shadow mb-4">
       
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th>Sr. no</th>
                            <th>User</th>
                            <th>Booking Id</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Slot Date</th>
                            <th>Slot Start Time</th>
                            <th>Slot End Time</th>
                            <th>Provider</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($bookings as $key => $val)
                            <tr>
                                <td>{{ ++$key }}</td>
                                <td>{{ $val->user->name }}</td>
                                <td>{{ $val->booking_id }}</td>
                                <td>{{ $val->total_price }}</td>
                                <td>{{ $val->status }}</td>
                                <td>{{ $val->slot_date }}</td>
                                <td>{{ $val->slot_start_time }}</td>
                                <td>{{ $val->slot_end_time }}</td>
                                <td>{{ $val->provider->mobile_no??'N/A' }}({{ $val->provider->name??'N/A' }})</td>
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
