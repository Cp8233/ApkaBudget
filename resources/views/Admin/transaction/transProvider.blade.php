@extends('Admin.layouts.app')

@section('content')
<h3 class="h3 mb-2 text-gray-800">Providers Transaction</h3>
<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
                <thead class="thead-dark">
                    <tr>
                        <th>Id</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Plan Name</th>
                        <th>Price</th>
                        <th>Duration</th>
                        <th>Status</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Total Amount</th>
                        <th>Transaction IDs</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($providers as $item)
                    <tr>
                        <td>{{$item->id}}</td>
                        <td>{{ $item->name }}</td>
                        <td>{{ $item->email }}</td>
                        <td>{{ $item->mobile_no }}</td>
                        <td>
                            @if($item->subscriptions->isNotEmpty())
                                {{ optional($item->subscriptions->first()->plan)->name ?? 'N/A' }}
                            @else
                                N/A
                            @endif
                        </td>

                        <td>
                            @if($item->subscriptions->isNotEmpty())
                                {{ optional($item->subscriptions->first()->plan)->price ?? 'N/A' }}
                            @else
                                N/A
                            @endif
                        </td>
                        <td>
                            @if($item->subscriptions->isNotEmpty())
                                {{ optional($item->subscriptions->first()->plan)->duration ?? 'N/A' }}
                            @else
                                N/A
                            @endif
                        </td>
                        <td>
                            @if($item->subscriptions->isNotEmpty())
                                @if($item->subscriptions->first()->status == 'active')
                                    <span class="badge bg-success">{{ ucfirst($item->subscriptions->first()->status) }}</span>
                                @elseif($item->subscriptions->first()->status == 'expired')
                                    <span class="badge bg-danger">{{ ucfirst($item->subscriptions->first()->status) }}</span>
                                @else
                                    <span class="badge bg-warning">{{ ucfirst($item->subscriptions->first()->status) }}</span>
                                @endif
                            @else
                                <span class="badge bg-secondary">N/A</span>
                            @endif
                        </td>
                        
                        <td>
                            @if($item->subscriptions->isNotEmpty())
                                {{ date('d-m-Y', strtotime($item->subscriptions->first()->start_date ?? '')) }}
                            @else
                                N/A
                            @endif
                        </td>
                        
                        <td>
                            @if($item->subscriptions->isNotEmpty())
                                {{ date('d-m-Y', strtotime($item->subscriptions->first()->end_date ?? '')) }}
                            @else
                                N/A
                            @endif
                        </td>
                        
                        <td>
                            @if($item->subscriptions->isNotEmpty())
                                ₹{{ $item->subscriptions->first()->plan->price ?? '0' }}
                            @else
                                ₹0
                            @endif
                        </td>
                        
                        <td>
                            @if($item->transactions->isNotEmpty())
                                {{ $item->transactions->first()->transaction_id ?? 'N/A' }}
                            @else
                                N/A
                            @endif
                        </td>
                        
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
                