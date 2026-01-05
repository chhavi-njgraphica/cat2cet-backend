@extends('backend.layout')
@section('content')

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Manage College</h4>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-body">
                <form method="post" action="{{(isset($college->id)) ? route('backend.colleges.update',[$college->id]) : route('backend.colleges.store') }}">
                    @if(isset($college->id))@method('PUT')@endif
                    @csrf
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">Select Percentile*</label>
                                <select name="percentile_between" id="" class="form-select" required>
                                    <option value="">Select Percentile</option>
                                    <option value="97-99" {{!empty($college) && $college->percentile_between == '97-99' ? 'selected' : ''}}>97 to 99% tile</option>
                                    <option value="95-97" {{!empty($college) && $college->percentile_between == '95-97' ? 'selected' : ''}}>95 to 97% tile</option>
                                    <option value="90-95" {{!empty($college) && $college->percentile_between == '90-95' ? 'selected' : ''}}>90 to 95% tile</option>
                                    <option value="80-90" {{!empty($college) && $college->percentile_between == '80-90' ? 'selected' : ''}}>80 to 90% tile</option>
                                    <option value="70-80" {{!empty($college) && $college->percentile_between == '70-80' ? 'selected' : ''}}>70 to 80% tile</option>
                                    <option value="60-70" {{!empty($college) && $college->percentile_between == '60-70' ? 'selected' : ''}}>60 to 70% tile</option>
                                    <option value="50-60" {{!empty($college) && $college->percentile_between == '50-60' ? 'selected' : ''}}>50 to 60% tile</option>
                                    <option value="below-50" {{!empty($college) && $college->percentile_between == 'below-50' ? 'selected' : ''}}>Below 50% tile</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">College Name*</label>
                                <input type="text" class="form-control"
                                    placeholder="XYZ of Management Ahmedabad" name="college_name" value="{{ $college->college_name ?? old('college_name') }}" required>
                                @if ($errors->has('college_name'))
                                    <div class="invalid-feedback">{{ $errors->first('college_name') }}</div>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Overall %tile</label>
                                <input type="text" class="form-control"
                                    placeholder="95+" name="overall_percentile" value="{{ $college->overall_percentile ?? old('overall_percentile') }}">
                                @if ($errors->has('overall_percentile'))
                                    <div class="invalid-feedback">{{ $errors->first('overall_percentile') }}</div>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Fees[2 Years]</label>
                                <input type="text" class="form-control"
                                    placeholder="22.5 Lakhs" name="fees" value="{{ $college->fees ?? old('fees') }}">
                                @if ($errors->has('fees'))
                                    <div class="invalid-feedback">{{ $errors->first('fees') }}</div>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Highest Package</label>
                                <input type="text" class="form-control"
                                    placeholder="81 LPA" name="highest_package" value="{{ $college->highest_package ?? old('highest_package') }}">
                                @if ($errors->has('highest_package'))
                                    <div class="invalid-feedback">{{ $errors->first('highest_package') }}</div>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Average Package</label>
                                <input type="text" class="form-control"
                                    placeholder="33 LPA" name="average_package" value="{{ $college->average_package ?? old('average_package') }}">
                                @if ($errors->has('average_package'))
                                    <div class="invalid-feedback">{{ $errors->first('average_package') }}</div>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Deadline</label>
                                <input type="date" class="form-control"
                                    placeholder="25 Jan" name="deadline" value="{{ $college->deadline ?? old('deadline') }}">
                                @if ($errors->has('deadline'))
                                    <div class="invalid-feedback">{{ $errors->first('deadline') }}</div>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Counsellor Whatsapp No.</label>
                                <input type="text" class="form-control"
                                    placeholder="+91 9856321470" name="whatsapp_no" value="{{ $college->whatsapp_no ?? old('whatsapp_no') }}">
                                @if ($errors->has('whatsapp_no'))
                                    <div class="invalid-feedback">{{ $errors->first('whatsapp_no') }}</div>
                                @endif
                            </div>
                        </div>

                        <div>
                            <button class="btn btn-primary" type="submit">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
