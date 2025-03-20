@extends('Admin.layouts.app')

@section('content')
    <h1 class="h3 mb-2 text-gray-800">Providers</h1>

    <div class="card shadow mb-4">
        <div class="card-header d-flex justify-content-end">
            {{-- <a href="{{ route('admin.add_providers') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Add providers
            </a> --}}
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
                            <th>Pincode</th>
                            <th>Address</th>
                            <th>Create Date</th>
                            <th>Security</th>
                            <th>Plan</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($providers as $key => $val)
                            <tr>
                                <td>{{ ++$key }}</td>
                                <td>{{ $val->name }}</td>
                                <td>{{ $val->email }}</td>
                                <td>{{ $val->mobile_no }}</td>
                                <td>{{ $val->pincode }}</td>
                                <td>{{ $val->address }}</td>
                                <td>{{ $val->created_at }}</td>
                                <td>
                                    @php
                                        $securityStatus = \App\Models\Subscription::hasActiveSecurity($val->id, 2);
                                    @endphp
                                    <span
                                        class="badge security-status security-plan {{ $securityStatus ? 'badge-success' : 'badge-danger' }}"
                                        data-id="{{ $val->id }}" data-type="2" style="cursor: pointer;">
                                        {{ $securityStatus ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    @php
                                        $planStatus = \App\Models\Subscription::hasActiveSecurity($val->id, 1);
                                    @endphp
                                    <span
                                        class="badge security-status main-plan {{ $planStatus ? 'badge-success' : 'badge-danger' }}"
                                        data-id="{{ $val->id }}" data-type="1" style="cursor: pointer;">
                                        {{ $planStatus ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>

                                <td>
                                    <button class="btn btn-sm btn-warning assign-zone-btn"
                                        data-provider-id="{{ $val->id }}" data-provider-name="{{ $val->name }}"
                                        data-toggle="modal" data-target="#assignZoneModal">
                                        <i class="fas fa-map-marker-alt"></i> Assign Zone
                                    </button>
                                    <a href="{{ route('admin.edit_providers', ['id' => $val->id]) }}"
                                        class="btn btn-sm btn-primary" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    {{-- <button class="btn btn-sm btn-danger delete-btn" data-url="{{ route('admin.delete_providers', ['id' => $val->id]) }}" title="Delete"> <i class="fa fa-trash" aria-hidden="true"></i> </button> --}}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <!-- Popup Modal -->
            <div class="modal fade" id="planModal" tabindex="-1" role="dialog" aria-labelledby="planModalLabel"
                aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="planModalLabel">Select a Plan</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" id="providerId">
                            <input type="hidden" id="planType">

                            <label>Select a Plan:</label>
                            <select class="form-control" id="planDropdown">
                                <!-- Options dynamically fill होंगे -->
                            </select>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-success" id="activatePlanBtn">Activate</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Assign Zone Modal -->
            <div class="modal" id="assignZoneModal" tabindex="-1" aria-labelledby="assignZoneModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <form id="assignZoneForm">
                        @csrf
                        <input type="hidden" name="provider_id" id="modalProviderId">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="assignZoneModalLabel">Assign Zones to <span
                                        id="providerName"></span></h5>
                                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body" id="zoneList">
                                Loading zones...
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">Assign Zones</button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
    <script>
        document.querySelectorAll('.assign-zone-btn').forEach(button => {
            button.addEventListener('click', function() {
                const providerId = this.getAttribute('data-provider-id');
                const providerName = this.getAttribute('data-provider-name');
                document.getElementById('modalProviderId').value = providerId;
                document.getElementById('providerName').textContent = providerName;

                fetch(`/admin/get-zones/${providerId}`)
                    .then(response => response.json())
                    .then(data => {
                        const zoneList = document.getElementById('zoneList');
                        zoneList.innerHTML = '';
                        data.zones.forEach(zone => {
                            const checked = data.assignedZones.includes(zone.id) ? 'checked' :
                                '';
                            zoneList.innerHTML += `
                                <div class="form-check">
                                    <input type="checkbox" name="zones[]" value="${zone.id}" class="form-check-input" ${checked}>
                                    <label class="form-check-label">${zone.name}</label>
                                </div>
                            `;
                        });
                    })
                    .catch(error => console.error('Error fetching zones:', error));
            });
        });

        document.getElementById('assignZoneForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            fetch('{{ route('admin.assign_zones') }}', {
                    method: 'POST',
                    body: formData,
                })
                .then(response => response.json())
                .then(data => {
                    alert(data.message);
                    $('#assignZoneModal').modal('hide');
                    location.reload();
                })
                .catch(error => console.error('Error assigning zones:', error));
        });
    </script>
@endsection
