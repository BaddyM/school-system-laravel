@extends('common.header')

@section('title')
    Attendance
@endsection

@section('body')
    <style>
        td {
            vertical-align: middle;
        }
    </style>
    <div class="container-fluid">
        <h5 class="mb-0 text-uppercase fw-bold text-center mb-3" style="color: purple;">Staff Attendance</h5>

        <div class="row justify-content-between">

            <div class="modal fade" id="printStaffAttendanceModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false"
                role="dialog" aria-labelledby="modalTitleId" aria-hidden="true">
                <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-sm"role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title fw-bold">
                                Choose Dates
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form method="get" class="print_staff_attendance_form">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label h6 fw-bold">From</label>
                                    <input type="text" class="form-control from-date rounded-0" name="from_date" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label h6 fw-bold">To</label>
                                    <input type="text" class="form-control to-date rounded-0" name="to_date" required>
                                </div>

                                <button type="button" class="btn rounded-0 btn-warning px-4 bg-gradient fw-bold" id="print_staff_attendance">Print</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>{{-- Print Attendance modal --}}

            <div class="col-md-4 mb-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <form method="post" id="select_staff_form">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-bold h6">Select Date</label>
                                <input type="text" name="date" class="form-control rounded-0" id="staff_attend_date"
                                    required>
                            </div>
                            <button id="select_date_btn" class="submit-btn mt-3" type="submit">Submit</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-7 mb-4 d-none attendance_body">
                <form method="post" id="staff_attendance_form">
                    @csrf
                    <input type="hidden" name="classname">
                    <input type="hidden" name="update_date">
                    <div class="card shadow-sm border-0 d-none attendance_card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <p class="mb-0 h6 fw-bold text-center">Take Staff Attandance</p>
                            @if (Auth::user()->is_super_admin == 1 || Auth::user()->is_admin == 1)
                            <div><button id="std_attend_popup" class="btn rounded-0 btn-warning bg-gradient fw-bold">Print Attendance <i
                                        class="fa fa-calendar"></i></button></div>
                            @endif
                        </div>
                        <div class="card-body overflow-scroll">
                            <table class="table table-responsive w-100 table-hover" id="attendance_table">
                                <thead>
                                    <tr class="table-dark bg-gradient w-100" >
                                        <th scope="col">#</th>
                                        <th scope="col">Staff Name</th>
                                        <th scope="col">Present</th>
                                        <th scope="col">Absent</th>
                                        <th class="text-center" style="width:300px !important;" scope="col">Note</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer">
                            <button class="submit-btn fw-bold" id="save_attendance_btn" type="submit">Save</button>
                        </div>
                </form>
            </div>

            <div class="spinner_body d-none">
                <div class="d-flex justify-content-center align-items-center">
                    <div style="color:purple;" class="spinner-border spinner-border-lg" role="status">
                    </div>
                </div>{{-- spinner --}}
            </div>
        </div>
    </div>

    </div>
@endsection

@push('body-scripts')
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{ asset('/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('/js/datatable.min.js') }}"></script>
    <script src="{{ asset('/js/bootstrap.bundle.js') }}"></script>
    <script src="{{ asset('js/flatpickr.js') }}"></script>
    <script>
        //Date picker
        $("#staff_attend_date, .from-date, .to-date").flatpickr({
            enableTime: false,
            dateFormat: "d-M-Y H:i",
            maxDate: "today"
        });

        //Pop the modal
        $("#std_attend_popup").on('click',function(e){
            e.preventDefault();
            $("#printStaffAttendanceModal").modal('show');
        })

        $("#select_staff_form").on('submit', function(e) {
            $("#attendance_table tbody").empty();

            //Add the date
            var date_val = $("#staff_attend_date").val();
            $('input[name="update_date"]').val(date_val);

            //Disable the button
            $("#select_date_btn").removeClass('submit-btn').addClass('submit-btn-disabled').prop('disabled', true);

            $(".attendance_body").removeClass('d-none');
            $(".spinner_body").removeClass('d-none');
            $(".attendance_card").addClass('d-none');

            e.preventDefault();
            $.ajax({
                type: 'POST',
                url: '{{ route("attendance.staff.fetch") }}',
                data: new FormData(this),
                processData: false,
                contentType: false,
                cache: false,
                success: function(data) {
                    //Enable the button
                    $("#select_date_btn").addClass('submit-btn').removeClass('submit-btn-disabled')
                        .prop('disabled', false);
                    $(".spinner_body").addClass('d-none');
                    $(".attendance_card").removeClass('d-none');

                    $("textarea").text()

                    var row_data;
                    var counter = 0;
                    $.each(data, function(k, v) {
                        row_data = "<tr>\
                                                <input type='hidden' name='std_id[]' value='" + v.std_id + "'>\
                                                <td>" + (counter += 1) + "</td>\
                                                <td style='text-transform:capitalize;'>" + ((v.lname) + " " + ((v.mname == null || v.mname == '' || v.mname ==
                                'NULL') ? "" : (v.mname)) + " " + (v.fname)) +
                            "</td>\
                                                <td><div class='d-flex justify-content-center'><input type='radio' value='present' name='" + v.std_id +
                            "' style='height:20px; width:20px;' " + ((v.status == 'present') ?
                                'checked' : '') +
                            "></div></td>\
                                                <td><div class='d-flex justify-content-center'><input type='radio' value='absent' name='" + v
                            .std_id + "' style='height:20px; width:20px;' " + ((v.status ==
                                'absent') ? 'checked' : '') + "></div></td>\
                                                <td><textarea class='form-control rounded-0 w-100' name='note_" + v.std_id +
                            "' cols=20 rows=2 placeholder='Note'>" + ((v.note != null) ? (v
                                .note) : "") + "</textarea></td>\
                                            </tr>";
                        $("#attendance_table tbody").append(row_data);
                    });
                },
                error: function() {
                    alert("Failed!");
                    //Enable the button
                    $("#select_date_btn").addClass('submit-btn').removeClass('submit-btn-disabled')
                        .prop('disabled', false);
                }
            });
        });

        //Save student attendance
        $('#staff_attendance_form').on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                type: 'post',
                url: '{{ route("attendance.staff.save") }}',
                data: new FormData(this),
                processData: false,
                cache: false,
                contentType: false,
                success: function(response) {
                    alert(response);
                    $(".spinner_body").removeClass('d-none');
                    $(".attendance_card").addClass('d-none');
                    $(".attendance_body").addClass('d-none');

                    //Clear the previous dates
                    $("#staff_attend_date").val(null);

                },
                error: function() {
                    alert("Failed to Save!");
                }
            });
        });

        //Print Attendance
        $('#print_staff_attendance').on('click', function(e) {            
            var from_date = $("input[name='from_date']").val();
            var to_date = $("input[name='to_date']").val();
            var classname = $("select[name='print_classname']").val();
            
            if(from_date.length != 0 && to_date.length != 0){
                $("#printStaffAttendanceModal").modal('hide');
                window.open('/attendance/print_staff_attendance/'+from_date+'/'+to_date+'', '_blank');
            }else{
                alert("Fill all Fields!");
            }

        });

        $(document).ready(function(){
            $('#printStaffAttendanceModal').on('hidden.bs.modal',function(){
                $(".print_staff_attendance_form")[0].reset();
            })
        })
    </script>
@endpush
