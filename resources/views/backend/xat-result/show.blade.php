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
                    <tr><th>Application No</th><td>{{ $result->details->{'XAT ID'} ?? 'N/A' }}</td></tr>
                    <tr><th>Test Center Name</th><td>{{ $result->details->{'TC Name'} ?? 'N/A' }}</td></tr>
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
                        {{-- <th>Total Marks</th> --}}
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
                            {{-- <td>{{ $section->total_marks }}</td> --}}
                            <td>{{ $section->obtain_marks }}</td>
                            <td>{{ $section->total_questions }}</td>
                            <td>{{ $section->attempt_questions }}</td>
                            <td>{{ $section->correct_answers }}</td>
                            <td>{{ $section->wrong_answers }}</td>
                        </tr>
                    @endforeach
                    @if (!empty($result->gk_section_marks))
                        @php $gk = $result->gk_section_marks; @endphp
                            <tr>
                                <td>4</td>
                                <td>{{ $gk->name }}</td>
                                <td>{{ $gk->obtain_marks }}</td>
                                <td>{{ $gk->total_questions }}</td>
                                <td>{{ $gk->attempt_questions }}</td>
                                <td>{{ $gk->correct_answers }}</td>
                                <td>{{ $gk->wrong_answers }}</td>
                            </tr>
                    @endif

                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
