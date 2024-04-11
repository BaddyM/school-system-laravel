@extends('common.header')

@section('title')
    School Term
@endsection

<style>
    label,
    p {
        margin-top: 10px;
        font-weight: bold;
        font-size: 14px;
    }
</style>

@section('body')
    <style>
        tr th {
            color: white;
            text-align: center;
        }

        td {
            text-align: center;
            vertical-align: middle;
            padding: 7px !important;
        }
    </style>
    <div class="container-fluid">
        <h5 class="mb-0 text-uppercase fw-bold text-center mb-3" style="color: purple;">Update School Term</h5>

        <div class="row justify-content-between">
            <div class="col-md-4 bg-light">
                <div>
                    <h5 class="mb-0 text-uppercase fw-bold text-center mb-3 mt-3" style="color: purple;">
                        <span class="underline">
                            Add New Term</span>
                    </h5>
                    <form action="" method="post" id="add_term_form">
                        @csrf
                        <div class="row">
                            <div class="col">
                                <label for="">Term</label>
                                <input type="number" min="1" max="3" name="add_term"
                                    class="form-control rounded-0" placeholder="Term" required>
                            </div>

                            <div class="col">
                                <label for="">Year</label>
                                <input type="number" min="2000" name="add_year" class="form-control rounded-0"
                                    placeholder="Year" required>
                            </div>
                        </div>

                        <button class="submit-btn mt-3" type="submit">add</button>
                    </form>
                </div>
            </div>
            <div class="card col-md-7 bg-white pt-3">
                <div class="card-header bg-light">
                    <p class="text-center mb-0 h5 fw-bold">Terms List</p>
                </div>
                <div class="card-body">
                    <table class="table table-hover">
                        <thead>
                            <tr class="bg-dark">
                                <th scope="col">Term</th>
                                <th scope="col">Year</th>
                                <th scope="col">Status</th>
                                <th scope="col">Date Enrolled</th>
                                @if (Auth::user()->is_admin == 1 || Auth::user()->is_super_admin == 1)
                                    <th scope="col">Action</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $counter = 0;
                            @endphp
                            @foreach ($term_list as $list)
                                <tr>
                                    <td>{{ $list->term }}</td>
                                    <td>{{ $list->year }}</td>
                                    <td>
                                        @if ($list->active == 1)
                                            <span class="badge bg-success">active</span>
                                        @else
                                            <span class="badge bg-secondary">inactive</span>
                                        @endif
                                    </td>
                                    <td>{{ date('D, d M, Y h:i a', strtotime($list->created_at)) }}</td>
                                    <td>
                                        @if (Auth::user()->is_admin == 1 || Auth::user()->is_super_admin == 1)
                                            <div class="d-flex align-items-center justify-content-center" style="gap:10px;">
                                                <form action="" method="post" class="mb-0 delete_term_form">
                                                    @csrf
                                                    <input type="hidden" value="{{ $list->id }}" name="delete_id">
                                                    <button title="Delete" class="btn btn-outline-danger py-2 rounded-5"
                                                        type="submit">
                                                        <i class="fa fa-x"></i>
                                                    </button>
                                                </form>

                                                <form action="" method="post" class="mb-0 update_term_form">
                                                    <input type="hidden" name="term" value="{{ $list->term }}">
                                                    <input type="hidden" name="year" value="{{ $list->year }}">
                                                    @if ($list->active == 1)
                                                        <button title="Active"
                                                            class="btn btn-success py-2 bg-gradient rounded-5"
                                                            type="submit">
                                                            <i class="fa fa-check"></i>
                                                        </button>
                                                    @else
                                                        <button title="Change"
                                                            class="btn btn-outline-success py-2 bg-gradient rounded-5"
                                                            type="submit">
                                                            <i class="fa fa-check"></i>
                                                        </button>
                                                    @endif
                                                </form>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
@endsection

@push('body-scripts')
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script>
        $(document).ready(function() {

            //Update Term
            $(".update_term_form").on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    type: "POST",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{ route('setting.term.change') }}",
                    data: new FormData(this),
                    processData: false,
                    contentType: false,
                    cache: false,
                    success: function(data) {
                        alert(data);
                        location.reload();
                    },
                    error: function(error) {
                        alert('Failed to Update Term');
                    }
                });
            });

            //Add New Term
            $("input[name='add_term']").on('keyup', function() {
                var term_value = $(this).val();
                //Check entered value for term
                if (term_value > 3 || term_value < 1) {
                    $(this).val(null);
                }
            });

            $("#add_term_form").on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    type: "POST",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{ route('setting.term.add') }}",
                    data: new FormData(this),
                    processData: false,
                    contentType: false,
                    cache: false,
                    success: function(data) {
                        $("#add_term_form")[0].reset();
                        alert(data);
                        location.reload();
                    },
                    error: function(error) {
                        alert('Failed to Add Term');
                    }
                });
            });

            //Delete Term
            $(".delete_term_form").on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    type: "POST",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{ route('setting.term.delete') }}",
                    data: new FormData(this),
                    processData: false,
                    contentType: false,
                    cache: false,
                    success: function(data) {
                        $(".delete_term_form")[0].reset();
                        alert(data);
                        location.reload();
                    },
                    error: function(error) {
                        alert('Failed to Add Term');
                    }
                });
            });
        });
    </script>
@endpush
