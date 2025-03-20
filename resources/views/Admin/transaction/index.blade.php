@extends('Admin.layouts.app')

@section('content')
<h3 class="h3 mb-2 text-gray-800">Transactions</h3>
<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
                <thead class="bg-primary text-white">
                    <tr>
                       <th>ID</th>
                            <th>Type</th>
                            <th>User</th>
                            <th>Mobile</th>
                            <th>Transaction</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Transaction ID</th>
                            <th>Created At</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($transactions as $trans)
                    <tr>
                       <td>{{ $loop->iteration }}</td>
                                <td>{{ $trans->type == 1 ? 'Subscription' : 'Security' }}</td>
                                <td>{{ $trans->user->name ?? 'N/A' }}</td>
                                <td>{{ $trans->user->mobile_no ?? 'N/A' }}</td>
                                <td>{{ $trans->transaction == 1 ? 'Credit' : 'Debit' }}</td>
                                <td>{{ $trans->amount }}</td>
                                <td>
                                    @if ($trans->status == 'success')
                                        <span class="badge badge-success">Success</span>
                                    @elseif($trans->status == 'failed')
                                        <span class="badge badge-danger">Failed</span>
                                    @else
                                        <span class="badge badge-warning">Pending</span>
                                    @endif
                                </td>
                                <td>{{ $trans->transaction_id }}</td>
                                <td>{{ $trans->created_at }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
