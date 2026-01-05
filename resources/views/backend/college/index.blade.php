@extends('backend.layout')
@section('content')

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Colleges</h4>
            <div class="page-title-right">
                <a href="{{ route('backend.colleges.create') }}"
                    class="btn btn-success btn-sm text-right">Add College</a>
            </div>
        </div>
    </div>
</div>


<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="planTable" class="table table-striped table-bordered" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>Sr. No.</th>
                                <th>College Name</th>
                                <th>Percentile Between</th>
                                <th>Overall Percentile</th>
                                <th>Fees</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($colleges as $college)
                                <tr>
                                    <td>{{ $loop->iteration}}</td>
                                    <td>{{ $college->college_name }}</td>
                                    <td>{{ $college->percentile_between}}</td>
                                    <td>{{ $college->overall_percentile }}</td>
                                    <td>{{ $college->fees }}</td>
                                    <td>
                                        <div class="button-items d-flex">
                                            <a class="btn btn-primary btn-sm mx-2 text-white" href="{{ route('backend.colleges.edit', $college->id) }}"><i class="material-icons-outlined pages-icon">edit</i></a>
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
</script>
@endsection
