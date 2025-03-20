@extends('Admin.layouts.app')

@section('content')
    <h1 class="h3 mb-2 text-gray-800">Zones</h1>

    <div class="card shadow mb-4">
        <div class="card-header d-flex justify-content-end">
            <a href="{{ route('admin.add_zone') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Add Zone
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th>Sr. No</th>
                            <th>Name</th>
                            <th>Perimeter (km)</th>
                            <th>Area (sq. km)</th>
                            <th>Assign Providers</th>
                            {{-- <th>Boundaries</th> --}}
                            <th>Areas</th>
                            {{--<th>Actions</th>--}}
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($data as $key => $val)
                            <tr>
                                <td>{{ ++$key }}</td>
                                <td>{{ $val->name }}</td>
                                <td>{{ $val->perimeter }}</td>
                                <td>{{ $val->area }}</td>
                                 <td>
                                    @if ($val->providers->isNotEmpty())
                                        {{ $val->providers->pluck('name')->implode(' || ') }}
                                    @else
                                        No providers assigned
                                    @endif
                                </td>
                                {{-- <td>
                                    @php $boundaries = json_decode($val->boundary, true); @endphp
                                    @foreach ($boundaries as $boundary)
                                        ({{ $boundary['lat'] }}, {{ $boundary['lng'] }})
                                        <br>
                                    @endforeach
                                </td> --}}
                                <td>
                                    @php $areas = json_decode($val->areas, true); @endphp
                                    @foreach ($areas as $area)
                                        {{ $area }}<br>
                                    @endforeach
                                </td>
                                <!--<td>-->
                                    {{-- <a href="" class="btn btn-info btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </a> --}}
                                    {{--<button class="btn btn-sm btn-warning assign-btn" data-zone-id="{{ $val->id }}"
                                        data-zone-name="{{ $val->name }}" data-toggle="modal"
                                        data-target="#assignProviderModal">
                                        <i class="fas fa-user-plus"></i> Assign
                                    </button>--}}
                                    {{-- <button class="btn btn-sm btn-danger delete-btn" data-url="" title="Delete"> <i
                                            class="fa fa-trash" aria-hidden="true"></i> </button> --}}


                                <!--</td>-->
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center">No zones found</td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>
        </div>
    </div>
    <!-- Assign Provider Modal -->
    {{--<div class="modal fade" id="assignProviderModal" tabindex="-1" aria-labelledby="assignProviderModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('admin.assign_provider') }}" method="POST" id="addForm">
                @csrf
                <input type="hidden" name="zone_id" id="modalZoneId">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="assignProviderModalLabel">Assign Providers to <span
                                id="zoneName"></span></h5>
                        <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Search bar -->
                        <div class="mb-3">
                            <input type="text" id="providerSearch" class="form-control"
                                placeholder="Search providers...">
                        </div>

                        <!-- Select/Deselect All -->
                        <div class="mb-3">
                            <button type="button" id="selectAll" class="btn btn-sm btn-primary">Select All</button>
                            <button type="button" id="deselectAll" class="btn btn-sm btn-secondary">Deselect All</button>
                        </div>

                        <!-- Provider List -->
                        <div class="form-check" id="providerList" style="max-height: 400px; overflow-y: auto;">
                            @foreach ($providers as $provider)
                                <div class="provider-item">
                                    <input type="checkbox" name="providers[]" value="{{ $provider->id }}"
                                        class="form-check-input provider-checkbox" id="provider_{{ $provider->id }}">
                                    <label class="form-check-label" for="provider_{{ $provider->id }}">
                                        {{ $provider->name }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Assign Providers</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>--}}

    <!-- JavaScript for search, select/deselect all -->
    <!--<script>-->
    <!--    document.querySelectorAll('.assign-btn').forEach(button => {-->
    <!--        button.addEventListener('click', function() {-->
    <!--            const zoneId = this.getAttribute('data-zone-id');-->
    <!--            const zoneName = this.getAttribute('data-zone-name');-->

    <!--            document.getElementById('modalZoneId').value = zoneId;-->
    <!--            document.getElementById('zoneName').textContent = zoneName;-->

    <!--            // AJAX call to get providers-->
    <!--            fetch(`/admin/get_providers/${zoneId}`)-->
    <!--                .then(response => response.json())-->
    <!--                .then(data => {-->
    <!--                    const providerList = document.getElementById('providerList');-->
    <!--                    providerList.innerHTML = ''; // Clear previous entries-->

    <!--                    data.providers.forEach(provider => {-->
    <!--                        const isChecked = data.assignedProviders.includes(provider.id) ?-->
    <!--                            'checked' : '';-->
    <!--                        providerList.innerHTML += `-->
    <!--                    <div class="provider-item">-->
    <!--                        <input type="checkbox" name="providers[]" value="${provider.id}"-->
    <!--                            class="form-check-input provider-checkbox" id="provider_${provider.id}" ${isChecked}>-->
    <!--                        <label class="form-check-label" for="provider_${provider.id}">-->
    <!--                            ${provider.name}-->
    <!--                        </label>-->
    <!--                    </div>-->
    <!--                `;-->
    <!--                    });-->
    <!--                })-->
    <!--                .catch(error => console.error('Error fetching providers:', error));-->
    <!--        });-->
    <!--    });-->


    <!--    // Select All-->
    <!--    document.getElementById('selectAll').addEventListener('click', () => {-->
    <!--        document.querySelectorAll('.provider-checkbox').forEach(checkbox => checkbox.checked = true);-->
    <!--    });-->

    <!--    // Deselect All-->
    <!--    document.getElementById('deselectAll').addEventListener('click', () => {-->
    <!--        document.querySelectorAll('.provider-checkbox').forEach(checkbox => checkbox.checked = false);-->
    <!--    });-->

    <!--    // Search Providers-->
    <!--    document.getElementById('providerSearch').addEventListener('input', function() {-->
    <!--        const searchTerm = this.value.toLowerCase();-->
    <!--        document.querySelectorAll('.provider-item').forEach(item => {-->
    <!--            const text = item.textContent.toLowerCase();-->
    <!--            item.style.display = text.includes(searchTerm) ? '' : 'none';-->
    <!--        });-->
    <!--    });-->
    <!--</script>-->

@endsection
