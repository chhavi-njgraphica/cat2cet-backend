@extends('backend.layout')
@section('content')

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Student Result</h4>
        </div>
    </div>
</div>


<div class="row mt-4">    
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                    

                <form action="{{ route('backend.student-result') }}" method="get">
                    <div class="row mb-3 mt-3">
                        <div class="col-md-3">
                            <div class="filter-wrapper">
                                <h6>Select Date</h6>
                                <input type="text" class="form-control" name="date" id="userDatePicker" placeholder="Select date" value="{{ $selectedBookingDate ?? '' }}">
                                <input type="hidden" id="start_date" name="start_date">
                                <input type="hidden" id="end_date" name="end_date">

                            </div>
                        </div>
                        <div class="col-md-2 mt-4">
                            <div class="book-filter-btn">
                                <button type="submit" class="btn btn-success" name="export" value="1">Export</button>
                                <a href="{{route('backend.student-result')}}" class="btn btn-primary">Reset</a>
                            </div>
                        </div>
                    </div>
                </form>
                    
                <div class="table-responsive">
                    <table id="planTable" class="table table-striped table-bordered" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Whatsapp Number</th>
                                <th>Candidate Name</th>
                                <th>Application No</th>
                                <th>Subject</th>
                                {{-- <th>Shift</th>
                                <th>Test Date</th>
                                <th>Test Time</th> --}}
                                <th>Test Center Name</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($results as $record)
                                @php
                                    $result = $record->decoded_data;
                                @endphp
                                <tr>
                                    <td>{{ $record->user->name ?? 'N/A'}}</td>
                                    <td>{{ $record->user->email ?? 'N/A'}}</td>
                                    <td>{{ $record->user->whatsapp_number ?? 'N/A'}}</td>
                                    <td>{{ $result->details->{'Candidate Name'} ?? 'N/A' }}</td>
                                    <td>{{ $result->details->{'Application No'} ?? 'N/A' }}</td>
                                    <td>{{ $result->details->Subject ?? 'N/A' }}</td>
                                    {{-- <td>{{ $result->details->Shift ?? 'N/A' }}</td>
                                    <td>{{ $result->details->{'Test Date'} ?? 'N/A' }}</td>
                                    <td>{{ $result->details->{'Test Time'} ?? 'N/A' }}</td> --}}
                                    <td>{{ $result->details->{'Test Center Name'} ?? 'N/A' }}</td>
                                    <td>
                                        <div class="button-items d-flex">
                                            <a class="btn btn-primary btn-sm mx-2 text-white" href="{{ route('backend.student-result.show', $record->id) }}"><i class="material-icons-outlined pages-icon">visibility</i></a>
                                            <a class="btn btn-success btn-sm mx-2 text-white" href="{{ route('backend.student-result.export', $record->id) }}">
                                                <i class="material-icons-outlined pages-icon">download</i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection
@section('script')
<script>
    $(document).ready(function() {
        $('#planTable').DataTable();
    });

    
    $('#userDatePicker').flatpickr({
        mode: "range", // Enable date range selection
        altInput: true, // Use a visually enhanced input
        altFormat: "M j, Y", // Human-readable format for users
        dateFormat: "Y-m-d",
        onClose: function (selectedDates) {
            if (selectedDates.length === 1) {
                var startDate = selectedDates[0].toLocaleDateString('en-CA');
                var endDate = null;
            } else if (selectedDates.length === 2) {
                var startDate = selectedDates[0].toLocaleDateString('en-CA');
                var endDate = selectedDates[1].toLocaleDateString('en-CA');
            }

            $('#start_date').val(startDate || '');
            $('#end_date').val(endDate || '');
        }
    });
</script>
@endsection
