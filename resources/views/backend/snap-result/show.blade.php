@extends('backend.layout')
@section('content')
<div class="container">
    <h3 class="mb-4">Candidate Result Details</h3>

    <div class="card mb-4">
        <div class="card-body">
            <h5 class="mb-3">User Details</h5>
            <table class="table table-bordered">
                <tbody>
                    <tr><th>Candidate Name</th><td>{{ $record->snap_user->name ?? 'N/A' }}</td></tr>
                    <tr><th>Email</th><td>{{ $record->snap_user_email ?? 'N/A' }}</td></tr>
                    <tr><th>Whatsapp Number</th><td>{{ $record->snap_user->whatsapp_number ?? 'N/A' }}</td></tr>
                    <tr><th>Category</th><td>{{ $record->category ?? 'N/A' }}</td></tr>
                    <tr><th>Overall Percentile</th><td>{{ $record->overall_percentile ?? 'N/A' }}</td></tr>
                    <tr><th>English Marks</th><td>{{ $record->english ?? 'N/A' }}</td></tr>
                    <tr><th>Logical Marks</th><td>{{ $record->logical ?? 'N/A' }}</td></tr>
                    <tr><th>Quantative Marks</th><td>{{ $record->quant ?? 'N/A' }}</td></tr>
                    <tr><th>Total Obtained Marks</th><td>{{ $record->max_score ?? 'N/A' }}</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    
</div>
@endsection
