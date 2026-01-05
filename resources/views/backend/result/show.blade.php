@extends('backend.layout')
@section('content')
<div class="container">
    <h3 class="mb-4">Candidate Result Details</h3>

    <div class="card mb-4">
        <div class="card-body">
            <h5 class="mb-3">User Details</h5>
            <table class="table table-bordered">
                <tbody>
                    <tr><th>Name</th><td>{{ $record->user->name ?? 'N/A' }}</td></tr>
                    <tr><th>Email</th><td>{{ $record->user->email ?? 'N/A' }}</td></tr>
                    <tr><th>Whatsapp Number</th><td>{{ $record->user->whatsapp_number ?? 'N/A' }}</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <h5 class="mb-3">Candidate Info</h5>
            <table class="table table-bordered">
                <tbody>
                    <tr><th>Candidate Name</th><td>{{ $result->details->{'Candidate Name'} ?? 'N/A' }}</td></tr>
                    <tr><th>Application No</th><td>{{ $result->details->{'Application No'} ?? 'N/A' }}</td></tr>
                    <tr><th>Subject</th><td>{{ $result->details->Subject ?? 'N/A' }}</td></tr>
                    <tr><th>Shift</th><td>{{ $result->details->Shift ?? 'N/A' }}</td></tr>
                    <tr><th>Test Date</th><td>{{ $result->details->{'Test Date'} ?? 'N/A' }}</td></tr>
                    <tr><th>Test Time</th><td>{{ $result->details->{'Test Time'} ?? 'N/A' }}</td></tr>
                    <tr><th>Test Center Name</th><td>{{ $result->details->{'Test Center Name'} ?? 'N/A' }}</td></tr>
                    <tr><th>Percentile</th><td>{{ $result->percentile ?? 'N/A' }}</td></tr>
                    <tr><th>Total Marks</th><td>{{ $result->total_marks ?? 'N/A' }}</td></tr>
                    <tr><th>Obtained Marks</th><td>{{ $result->obtain_marks ?? 'N/A' }}</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <h5>Section-wise Marks</h5>
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Sr No.</th>
                        <th>Section Name</th>
                        <th>Total Marks</th>
                        <th>Obtained Marks</th>
                        <th>Total Questions</th>
                        <th>Attempted</th>
                        <th>Correct</th>
                        <th>Wrong</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($result->sections_marks as $section)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $section->name }}</td>
                            <td>{{ $section->total_marks }}</td>
                            <td>{{ $section->obtain_marks }}</td>
                            <td>{{ $section->total_questions }}</td>
                            <td>{{ $section->attempt_questions }}</td>
                            <td>{{ $section->correct_answers }}</td>
                            <td>{{ $section->wrong_answers }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
