@extends('Admin.layouts.app')

@section('content')
<h1 class="h3 mb-2 text-gray-800">Services</h1>

<div class="card shadow mb-4">
    <div class="card-header d-flex justify-content-end">
        <a href="{{ route('admin.add_service', ['category_id' => $category_id, 'subcategory_id' => $subcategory_id, 'sub_subcategory_id' => $id]) }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Add Service
        </a>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered text-center" id="dataTable" width="100%" cellspacing="0">
                <thead class="thead-dark">
                    <tr>
                        <th>Sr.n</th>
                        <th>Service Name</th>
                        <th>Image</th>
                        <th>Time</th>
                        <th>Price</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($services as $key => $service)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $service->service_name }}</td>
                        <td>
                            @if ($service->image)
                                <img src="{{ asset($service->image) }}" class="img-fluid rounded shadow-sm" 
                                     style="width: 60px; height: 60px; object-fit: cover;">
                            @else
                                No Image
                            @endif
                        </td>
                        <td>{{ $service->time }}</td>
                        <td>{{ $service->price }}</td>
                        <td>
                            <a href="{{ route('admin.edit_service', ['category_id' => $category_id, 'subcategory_id' => $subcategory_id, 'sub_subcategory_id' => $id, 'service_id' => $service->id]) }}" class="btn btn-info btn-sm">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <button class="btn btn-danger btn-sm delete-btn" 
                                data-url="{{ route('admin.delete_service', ['id' => $service->id]) }}" 
                                title="Delete">
                                <i class="fa fa-trash"></i> Delete
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

