@extends('common.header')

@section('title')
    Olevel - Marksheet
@endsection

@section('body')
<div class="container-fluid">
    <h5 class="text-center text-uppercase fw-bold mb-3">O level Marksheet</h5>

    <div class="mb-3">
        <form action="" method="post">
            @csrf
            <div class="row">
                <div class="col-sm-6 form-floating">
                    <select name="classname" id="class" class="form-select">
                        <option value="senior5">Senior 5</option>
                        <option value="senior6">Senior 6</option>
                    </select>
                    <label for="" class="form-label fw-bold" style="color:blue;">Select Class</label>
                </div> {{-- Select class here --}}

                <div class="col-sm-6 form-floating">
                    <select name="result_set" id="resultset" class="form-select">
                        @foreach ($result_set as $result)
                            <option value="{{ $result->result_set }}">{{ $result->result_set }}</option>
                        @endforeach
                    </select>
                    <label for="" class="form-label fw-bold" style="color:red;">Select Result Set</label>
                </div>{{-- -Select Result set here --}}
            </div>
            <button class="btn submit-btn mt-3 rounded-1" id="alevel-btn" type="button">SUBMIT</button>
        </form>
    </div><!-- Select class here -->
</div>
@endsection

@include('common.scripts')

<script>

</script>