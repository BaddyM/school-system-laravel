@extends('common.header')

@section('title')
    School Term
@endsection

<style>
    label,p{
        margin-top: 10px;
        font-weight: bold;
        font-size: 14px;
    }
</style>

@section('body')
    <div class="container-fluid">
        <h5 class="mb-0 text-uppercase fw-bold text-center mb-3" style="color: purple;">Update School Term</h5>

        <div class="row justify-content-between">
            <div class="col-md-5 bg-light">
                <form action="" method="post">
                    <div class="row">
                        <div class="col">
                            <label for="">Select Term</label>
                            <select name="term" id="term" class="form-select rounded-0">
                                @foreach ($term as $t)
                                    <option value="{{ $t->term }}">{{ $t->term }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col">
                            <label for="">Select Year</label>
                            <select name="term" id="term" class="form-select rounded-0">
                                @foreach ($year as $y)
                                    <option value="{{ $y->year }}">{{ $y->year }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col bg-white pt-3">
                <p class="text-center">Terms List</p>
            </div>
        </div>

    </div>
@endsection

@push('body-scripts')
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script>
        $(document).ready(function() {

        });
    </script>
@endpush
