@extends('backend.layout')
@section('content')

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Users</h4>
            {{-- <div class="page-title-right">
                <a href="{{ route('backend.colleges.create') }}"
                    class="btn btn-success btn-sm text-right">Add College</a>
            </div> --}}
        </div>
    </div>
</div>


<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('backend.users') }}" method="get">
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
                                <a href="{{route('backend.users')}}" class="btn btn-primary">Reset</a>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="table-responsive">
                    <table id="planTable" class="table table-striped table-bordered" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>Sr. No.</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Whatsapp Number</th>
                                {{-- <th>Fees</th>
                                <th>Action</th> --}}
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($results as $user)
                                <tr>
                                    <td>{{ $loop->iteration}}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email}}</td>
                                    <td>{{ $user->whatsapp_number }}</td>
                                    {{-- <td>{{ $college->fees }}</td>
                                    <td>
                                        <div class="button-items d-flex">
                                            <a class="btn btn-primary btn-sm mx-2 text-white" href="{{ route('backend.colleges.edit', $college->id) }}"><i class="material-icons-outlined pages-icon">edit</i></a>
                                        </div>
                                    </td> --}}
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
