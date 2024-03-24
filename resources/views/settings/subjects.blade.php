@extends('common.header')

@section('title')
    Settings - Subjects
@endsection

<style>
    tr {
        vertical-align: middle !important;
    }
</style>

@section('body')
    <div class="container-fluid">
        <h5 class="mb-0 text-uppercase fw-bold text-center mb-3" style="color: purple;">Update/Add Subjects</h5>
        <div class="row justify-content-between">
            <div class="col-md-4">
                <p class="fw-bold text-uppercase text-center h5">Add Subjects</p>
                <form action="" method="post" id="add_subject_form" class="my-4 shadow-lg p-4 rounded-3">
                    <div>
                        <label for="" class="form-label fw-bold">Subject Name</label>
                        <input type="text" class="form-control rounded-0" name="subject_name" id="subject-name">
                    </div>

                    <div class="mt-2">
                        <label for="" class="form-label fw-bold">Paper</label>
                        <input type="number" min=0 class="form-control rounded-0" name="papers" id="papers-name">
                    </div>

                    <div class="mt-2 mb-4">
                        <label for="" class="form-label fw-bold">Level</label>
                        <select class="form-select rounded-0" name="level">
                            <option value="O Level">O Level</option>
                            <option value="A Level">A Level</option>
                        </select>
                    </div>

                    <p class="fw-bold text-uppercase text-center h5">Select Subsidiraries</p>
                    <div class="mb-4">
                        @php
                            $subs = array('SubICT', 'SubMath', 'GeneralPaper');
                        @endphp

                        @foreach ($subs as $sub)
                            <div class="d-flex align-items-center mb-2" style="gap:10px;">
                                <input type="checkbox" name="{{ $sub }}" value="{{ $sub }}" id=""
                                    class="form-check-input p-2 rounded-0">
                                <p class="m-0 h6">{{ $sub }}</p>
                            </div>
                        @endforeach

                    </div>

                    <button class="submit-btn-disabled" id="add-subject-btn" disabled>ADD</button>
                </form>
            </div>

            <div class="col-md-6 bg-white rounded-3 p-3">
                <div class="mb-3">
                    <p class="fw-bold text-uppercase text-center h5">A-Level Subjects</p>
                    <table class="table table-hover">
                        <thead>
                            <tr class="table-secondary bg-gradient">
                                <th>#</th>
                                <th scope="col">Subject</th>
                                <th scope="col">Paper</th>
                                <th scope="col" class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $row_num_a = 1;
                            @endphp
                            @foreach ($alevel as $a)
                                <tr>
                                    <td>{{ $row_num_a++ }}</td>
                                    <td>{{ $a->name }}</td>
                                    <td>{{ $a->paper }}</td>
                                    <td class="text-center">
                                        <form action="" method="post" class="m-0">
                                            <button class="btn btn-sm btn-danger rounded-5 delete-subject"
                                                value="{{ $a->id }}" type="submit">
                                                <i class="bi bi-x" style="font-size:20px;"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>{{-- A-Level --}}

                <div>
                    <p class="fw-bold text-uppercase text-center h5">O-Level Subjects</p>
                    <table class="table table-hover">
                        <thead>
                            <tr class="table-dark bg-gradient">
                                <th>#</th>
                                <th scope="col">Subject</th>
                                <th scope="col">Paper</th>
                                <th scope="col" class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $row_num = 1;
                            @endphp
                            @foreach ($olevel as $o)
                                <tr>
                                    <td>{{ $row_num++ }}</td>
                                    <td>{{ $o->name }}</td>
                                    <td>{{ $o->paper }}</td>
                                    <td class="text-center">
                                        <form action="" method="post" class="m-0">
                                            <button class="btn btn-sm btn-danger rounded-5 delete-subject"
                                                value="{{ $o->id }}" type="submit">
                                                <i class="bi bi-x" style="font-size:20px;"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>{{-- O-Level --}}

            </div>

        </div>
    </div>
@endsection


@push('body-scripts')
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            //Activate the submit btn
            $("#subject-name").on('change', function() {
                $("#add-subject-btn").removeClass('submit-btn-disabled').addClass('submit-btn').prop(
                    'disabled', false);
            })

            //Delete Subject
            $(".delete-subject").on('click', function(e) {
                e.preventDefault();
                var id = $(this).val();
                $.ajax({
                    type: "POST",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{ route('setting.subjects.delete') }}",
                    data: {
                        id: id
                    },
                    success: function(data) {
                        alert(data);
                        location.reload();
                    },
                    error: function(error) {
                        alert('Failed to Save Subject details');
                    }
                });
            });

            //Add Subject
            $('#add_subject_form').on('submit', function(event) {
                event.preventDefault();
                $.ajax({
                    type: "POST",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{ route('setting.subjects.add') }}",
                    data: new FormData(this),
                    processData: false,
                    contentType: false,
                    cache: false,
                    success: function(data) {
                        $("#add_subject_form")[0].reset();
                        alert(data);
                        location.reload();
                    },
                    error: function(error) {
                        alert('Failed to Save Subject details');
                    }
                });
            });

        });
    </script>
@endpush
