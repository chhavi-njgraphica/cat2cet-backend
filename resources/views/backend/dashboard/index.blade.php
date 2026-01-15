@extends('backend.layout')
@section('content')

<div class="row mb-4">
  <div class="col-12 col-xl-4">
    <div class="card rounded-4">
      <div class="card-body">
        <div id="chart10" class="mb-4"></div>
        <h4 class="mb-0">Total CAT User</h4>
        <div class="d-flex align-items-center gap-4 mt-0">
          <div class="">
            <h1 class="mb-0"><span class="total-earning-count">{{$user_count}}</span></h1>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <div class="col-12 col-xl-4">
    <div class="card rounded-4">
      <div class="card-body">
        <div id="chart9" class="mb-4"></div>
        <h4 class="mb-0">Total CAT Students</h4>
        <div class="d-flex align-items-center gap-4 mt-0">
          <div class="">
            <h1 class="mb-0"><span class="total-earning-count">{{$student_count}}</span></h1>
          </div>
        </div>
      </div>
    </div>
  </div> 

  <div class="col-12 col-xl-4">
    <div class="card rounded-4">
      <div class="card-body">
        <div id="chart10" class="mb-4"></div>
        <h4 class="mb-0">Total CAT Colleges</h4>
        <div class="d-flex align-items-center gap-4 mt-0">
          <div class="">
            <h1 class="mb-0"><span class="total-earning-count">{{$college_count}}</span></h1>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-12 col-xl-4 mt-4">
    <div class="card rounded-4">
      <div class="card-body">
        <div id="chart10" class="mb-4"></div>
        <h4 class="mb-0">Total XAT User</h4>
        <div class="d-flex align-items-center gap-4 mt-0">
          <div class="">
            <h1 class="mb-0"><span class="total-earning-count">{{$xat_user_count}}</span></h1>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-12 col-xl-4 mt-4">
    <div class="card rounded-4">
      <div class="card-body">
        <div id="chart9" class="mb-4"></div>
        <h4 class="mb-0">Total XAT Students</h4>
        <div class="d-flex align-items-center gap-4 mt-0">
          <div class="">
            <h1 class="mb-0"><span class="total-earning-count">{{$xat_student_count}}</span></h1>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-12 col-xl-4 mt-4">
    <div class="card rounded-4">
      <div class="card-body">
        <div id="chart10" class="mb-4"></div>
        <h4 class="mb-0">Total XAT Colleges</h4>
        <div class="d-flex align-items-center gap-4 mt-0">
          <div class="">
            <h1 class="mb-0"><span class="total-earning-count">{{$xat_college_count}}</span></h1>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
