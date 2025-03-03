@extends('Admin.layouts.app')

@section('content')
<div class="container">
    <h1 class="mt-3">Edit Subcategory</h1>

    <div class="card shadow mb-4">
        <div class="card-header">
            <h3 class="m-0">Edit Subcategory</h3>
        </div>

        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('admin.edit_subcategories', ['CategoryId' => $CategoryId, 'id' => $subcategory->id]) }}" method="POST" enctype="multipart/form-data">
                @csrf
              
              
                <div class="form-group col-md-6">
                    <label for="name">Subcategory Name</label>
                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
                           placeholder="Enter subcategory name" value="{{ old('name', $subcategory->name) }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                
                <div class="form-group col-md-6">
                    <label for="image">Subcategory Image</label>
                    <input type="file" name="image" id="image" class="form-control-file @error('image') is-invalid @enderror">
                    @error('image')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror

                    
                    @if ($subcategory->image)
                        <div class="mt-3">
                            <label>Current Image:</label><br>
                            <img src="{{ asset($subcategory->image) }}" class="img-fluid rounded w-25">
                        </div>
                    @endif
                </div>

                {{-- Buttons --}}
                <div class="mt-3">
                    <button type="submit" class="btn btn-success">Update Subcategory</button>
                    <a href="{{ route('admin.subcategories', ['CategoryId' => $CategoryId]) }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
