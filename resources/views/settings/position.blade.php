@extends('common.header')

@section('title')
    Position
@endsection

@section('body')
    <style>
        #update_user_form input,
        #update_user_form select {
            color: purple;
            font-weight: bold;
        }
    </style>

    <div class="container-fluid">
        <h5 class="mb-0 text-uppercase fw-bold text-center mb-3" style="color: purple;">Positions / Departments</h5>

        <div id="position-btn-container" class="d-flex justify-content-between">
            <button type="button" class="submit-btn fw-bold" id="position-display-btn">Positions</button>
            <button type="button" class="submit-btn fw-bold" id="department-display-btn">Departments</button>
        </div>

        <hr>

        <div class="positions-container">
            <div class="row justify-content-between">
                <div class="col-md-5 mt-4">
                    <p class="mb-2 text-center fw-bold h5 text-uppercase" style="color: purple;">
                        <span class="underline">Add Positions</span>
                    </p>

                    <form method="post" id="add-position-form">
                        @csrf
                        <div>
                            <label class="form-label h6 fw-bold">Position</label>
                            <input type="text" name="position" class="form-control rounded-0"
                                placeholder="Enter Position" required>
                        </div>
                        <button type="submit" class="submit-btn fw-bold mt-3" id="add-position-btn">Submit</button>
                    </form>

                </div>

                <div class="col-md-5 mt-4">
                    <p class="mb-2 text-center fw-bold h5 text-uppercase" style="color: purple;">
                        <span class="underline">Positions List</span>
                    </p>

                    <div class="class_list">
                        @foreach ($positions as $position)
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    {{ ucfirst($position->position) }}
                                </div>

                                @if ($position->position == 'teacher' || $position->position == 'head-teacher' || $position->position == 'it-support' || $position->position == 'dos')
                                    <span class="badge purple-badge">default</span>
                                @else
                                    <button value="{{ $position->id }}"
                                        class="delete-pos-btn btn btn-outline-danger rounded-5" type="button"><i
                                            class="fa fa-x"></i></button>
                                @endif
                            </div>
                        @endforeach
                    </div>

                </div>
            </div>
        </div>{{-- Positions --}}

        <div class="departments-container">
            <div class="row justify-content-between">
                <div class="col-md-5 mt-4">
                    <p class="mb-2 text-center fw-bold h5 text-uppercase" style="color: purple;">
                        <span class="underline">Add Departments</span>
                    </p>

                    <form method="post" id="add-department-form">
                        @csrf
                        <div>
                            <label class="form-label h6 fw-bold">Department</label>
                            <input type="text" name="dept" class="form-control rounded-0"
                                placeholder="Enter Department" required>
                        </div>
                        <button type="submit" class="submit-btn fw-bold mt-3" id="add-department-btn">Submit</button>
                    </form>

                </div>

                <div class="col-md-5 mt-4">
                    <p class="mb-2 text-center fw-bold h5 text-uppercase" style="color: purple;">
                        <span class="underline">Departments List</span>
                    </p>

                    <div class="class_list">
                        @foreach ($departments as $department)
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    {{ ucfirst($department->dept) }}
                                </div>
                                @if (
                                    $department->dept == 'admin' ||
                                        $department->dept == 'bursar' ||
                                        $department->dept == 'librarian' ||
                                        $department->dept == 'teacher' ||
                                        $department->dept == 'other')
                                    <span class="badge blue-badge">default</span>
                                @else
                                    <button value="{{ $department->id }}"
                                        class="delete-dept-btn btn btn-outline-danger rounded-5" type="button"><i
                                            class="fa fa-x"></i></button>
                                @endif
                            </div>
                        @endforeach
                    </div>

                </div>
            </div>
        </div>{{-- Departments --}}
    </div>
@endsection

@push('body-scripts')
    <script src="{{ asset('') }}js/jquery.min.js"></script>
    <script src="{{ asset('') }}js/datatable.min.js"></script>
    <script src="{{ asset('') }}js/bootstrap.bundle.js"></script>
    <script>
        $(document).ready(function() {
            //Display positions
            $("#position-display-btn").on('click', function() {
                $(".positions-container").slideDown(1200);
                $(".departments-container").slideUp(1200);
            });

            //Display departments
            $("#department-display-btn").on('click', function() {
                $(".positions-container").slideUp(1200);
                $(".departments-container").slideDown(1200);
            });

            //Add Positions
            $("#add-position-form").on('submit', function(e) {
                e.preventDefault();

                $.ajax({
                    type: 'POST',
                    url: '{{ route("positions.add") }}',
                    data: new FormData(this),
                    contentType: false,
                    processData: false,
                    cache: false,
                    success: function(response) {
                        alert(response);
                        location.reload();
                    },
                    error: function() {
                        alert("Failed to add position");
                    }
                });
            })

            //Delete Position

            //Add Department
            $("#add-department-form").on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    type: 'POST',
                    url: '{{ route("dept.add") }}',
                    data: new FormData(this),
                    contentType: false,
                    processData: false,
                    cache: false,
                    success: function(response) {
                        alert(response);
                        location.reload();
                    },
                    error: function() {
                        alert("Failed to add department");
                    }
                });
            });

            //Delete Department
            $(".delete-dept-btn").on('click', function(e) {
                e.preventDefault();
                var id = $(this).val();
                const confirm_delete = confirm('Are you sure?');

                if (confirm_delete == true) {
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: "POST",
                        url: '{{ route('dept.delete') }}',
                        data: {
                            id: id
                        },
                        success: function(response) {
                            alert(response);
                            location.reload();
                        },
                        error: function() {
                            alert("Failed to delete data!");
                        }
                    });
                }
            });
        });
    </script>
@endpush
