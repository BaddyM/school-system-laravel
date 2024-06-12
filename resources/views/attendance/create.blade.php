@extends('common.header')

@section('title')
    Attendance
@endsection

@section('body')
    <div class="container-fluid">
        <h5 class="mb-0 text-uppercase fw-bold text-center mb-3" style="color: purple;">Create Attendance Table</h5>

        <div class="col-md-3 shadow-sm bg-light p-3 mb-3">
            <p><span class="text-danger fst-italic h6 fw-bold">Note:</span> Create One table for each Termly attendance.</p>
        </div>

        <div class="d-flex justify-content-between align-items-center w-100">
            <div class="">
                <form method="post" id="create_std_attendance_form">
                    @csrf
                    <input type="hidden" name="attendance" value="student_attend">
                    <button class="submit-btn" type="submit">Student</button>
                </form>
            </div>

            <div class="">
                <form method="post" id="create_staff_attendance_form">
                    @csrf
                    <input type="hidden" name="attendance" value="staff_attend">
                    <button class="submit-btn" type="submit">Staff</button>
                </form>
            </div>
        </div>

    </div>
@endsection

@push('body-scripts')
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{ asset('/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('/js/datatable.min.js') }}"></script>
    <script src="{{ asset('/js/bootstrap.bundle.js') }}"></script>
    <script>
        $("#create_std_attendance_form").on('submit', function(e) {
            e.preventDefault();
            const confirm_option = confirm("Are you sure?");
            if (confirm_option == true) {
                $.ajax({
                    type: "POST",
                    url: "{{ route('attendance.table.create') }}",
                    data: new FormData(this),
                    processData: false,
                    cache: false,
                    contentType: false,
                    success: function(response) {
                        alert(response);
                    },
                    error: function() {

                    }
                });
            }
        });

        $("#create_staff_attendance_form").on('submit', function(e) {
            e.preventDefault();
            const confirm_option = confirm("Are you sure?");
            if (confirm_option == true) {
                $.ajax({
                    type: "POST",
                    url: "{{ route('attendance.table.create') }}",
                    data: new FormData(this),
                    processData: false,
                    cache: false,
                    contentType: false,
                    success: function(response) {
                        alert(response);
                    },
                    error: function() {

                    }
                });
            }
        });
    </script>
@endpush
