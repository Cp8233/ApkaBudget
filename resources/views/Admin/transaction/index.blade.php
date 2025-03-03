@extends('Admin.layouts.app')

@section('content')
<h3 class="h3 mb-2 text-gray-800"> AllTransaction</h3>
<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
                <thead class="bg-primary text-white">
                    <tr>
                        <th>ID</th>
                        <th>Type</th>
                        <th>User ID</th>
                        <th>Transaction</th>
                        <th>Amount</th>
                        <th>Transaction ID</th>
                        <th>Created At</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($transactions as $trans)
                    <tr>
                        <td>{{ $trans->id }}</td>
                        <td>{{ $trans->type == 1 ? 'Security' : 'Subscription' }}</td>
                        <td>{{ $trans->user_id }}</td>
                        <td>{{ $trans->transaction == 1 ? 'Credit' : 'Debit' }}</td>
                        <td>{{ $trans->amount }}</td>
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
