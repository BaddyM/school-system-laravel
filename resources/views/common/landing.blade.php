@extends('common.header')

@section('title')
    Home
@endsection

@section('body')
    <div class="container-fluid">

        <div class="modal fade" id="landingNotificationModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false"
            role="dialog" aria-labelledby="modalTitleId" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-sm"role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">
                            Notification
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-success" role="alert">
                            <strong id="notification-message"></strong>
                        </div>

                    </div>
                </div>
            </div>
        </div>{{-- Notification modal --}}

        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            <strong>Scroll Down for more info!</strong>
        </div>

        <div class="card-container">
            <div class="card col-md-3 border-0 card-one">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-block">
                            <p class="mb-0 h2 text-white">{{ count($students) }}</p>
                            <p class="mb-0 text-white">Active Students</p>
                        </div>
                        <div>
                            <i class="bi bi-mortarboard-fill" style="font-size:40px;"></i>
                        </div>
                    </div>
                </div>
            </div>{{-- card 1 --}}

            <div class="card col-md-3 border-0 card-two">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-block">
                            <p class="mb-0 h2 text-white">{{ count($subjects) }}</p>
                            <p class="mb-0 text-white">Subjects</p>
                        </div>
                        <div>
                            <i class="bi bi-book-half" style="font-size:40px;"></i>
                        </div>
                    </div>
                </div>
            </div>{{-- card 2 --}}

            <div class="card col-md-3 border-0 card-three">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-block">
                            <p class="mb-0 h2 text-white">{{ count($staff) }}</p>
                            <p class="mb-0 text-white">Active Staff</p>
                        </div>
                        <div>
                            <i class="bi bi-people-fill text-white" style="font-size:40px;"></i>
                        </div>
                    </div>
                </div>
            </div>{{-- card 3 --}}

        </div>{{-- cards --}}

        <div class="my-3 row justify-content-between">
            <div class="col-md-5 mb-4">
                <div class="card border-0 shadow-sm">
                    <card class="card-header d-flex justify-content-center">
                        <p class="mb-0 fw-bold h6">Term Planner</p>
                    </card>
                    <div class="card-body">
                        <table class="table table-hover">
                            <thead>
                                <tr class="bg-gradient" style="background: purple; color:white;">
                                    <th scope="col">#</th>
                                    <th scope="col">Date</th>
                                    <th scope="col">Activity</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $counter = 1;
                                @endphp
                                @foreach ($planner as $p)
                                    <tr style="cursor: pointer;">
                                        <td>{{ $counter++ }}</td>
                                        <td class="align-items-center">
                                            <span>{{ date('D, d M, Y', strtotime($p->date)) }}</span>
                                            <span class="badge purple-badge">{{ date('h:i A', strtotime($p->date)) }}</span>
                                        </td>
                                        <td>
                                            {{ $p->activity }}
                                        </td>
                                    </tr>
                                @endforeach

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-4  text-center">
                <div class="card shadow-sm border-0">
                    <div class="card-header d-flex justify-content-center">
                        <span style="border-color:purple !important;"
                            class="shadow-sm mb-0 fw-bold h6 border px-3 py-1 border-3 rounded-3">Staff
                            Population</span>
                    </div>
                    <div class="card-body">
                        <div>
                            <canvas id="staffSummaryChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-4  text-center">
                <div class="card shadow-sm border-0">
                    <div class="card-header d-flex justify-content-center">
                        <span style="border-color:purple !important;"
                            class="shadow-sm mb-0 fw-bold h6 border px-3 py-1 border-3 rounded-3">Students
                            Population</span>
                    </div>
                    <div class="card-body">
                        <div>
                            <canvas id="studentSummaryChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row justify-content-between align-items-center">
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm border-0">
                    <div class="card-header d-flex justify-content-center">
                        <p class="mb-0 fw-bold h6">Student Attendance Today</p>
                    </div>
                    <div class="card-body">
                        <div class="student_attendance">
                            <p class="mb-2 fw-bold h6">Student Attendance Summary</p>
                            <div class="progress">
                                <div id="student_present_progress"
                                    class="progress-bar progress-bar-striped progress-bar-animated bg-primary"
                                    role="progressbar" aria-valuemax="100">

                                </div>
                            </div>
                            <div class="mt-2 d-flex justify-content-between">
                                <div class="align-items-center">
                                    <span style="background: purple; color:white;" class="badge">Total</span>
                                    <span id="std_total">0</span>
                                </div>

                                <div class="align-items-center">
                                    <span class="badge bg-success">Present</span>
                                    <span id="std_present">0</span>
                                </div>

                                <div class="align-items-center">
                                    <span class="badge bg-danger">Absent</span>
                                    <span id="std_absent">0</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>{{-- Student --}}

            <div class="col-md-4 mb-4">
                <div class="card shadow-sm border-0">
                    <div class="card-header d-flex justify-content-center">
                        <p class="mb-0 fw-bold h6">Staff Attendance Today</p>
                    </div>
                    <div class="card-body">
                        <div class="student_attendance">
                            <p class="mb-2 fw-bold h6">Staff Attendance Summary</p>
                            <div class="progress">
                                <div id="staff_present_progress"
                                    class="progress-bar progress-bar-striped progress-bar-animated bg-primary"
                                    role="progressbar" aria-valuemax="100">

                                </div>
                            </div>
                            <div class="mt-2 d-flex justify-content-between">
                                <div class="align-items-center">
                                    <span style="background: purple; color:white;" class="badge">Total</span>
                                    <span id="staff_total">0</span>
                                </div>

                                <div class="align-items-center">
                                    <span class="badge bg-success">Present</span>
                                    <span id="staff_present">0</span>
                                </div>

                                <div class="align-items-center">
                                    <span class="badge bg-danger">Absent</span>
                                    <span id="staff_absent">0</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>{{-- Staff --}}

        </div>{{-- Attendance --}}

    </div>
@endsection

@push('body-scripts')
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{ asset('/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('/js/datatable.min.js') }}"></script>
    <script src="{{ asset('/js/bootstrap.bundle.js') }}"></script>
    <script src="{{ asset('') }}js/chart.js"></script>
    <script>
        //Student Summary chart
        function student_chart(girls, boys) {
            //Chart
            const piechart = $("#studentSummaryChart");
            new Chart(piechart, {
                type: 'doughnut',
                data: {
                    labels: ['Girls', 'Boys'],
                    datasets: [{
                        label: 'Total Students',
                        data: [girls, boys],
                        borderWidth: 1
                    }]
                },
            });
        }

        //Staff Summary Chart
        function staff_chart(girls, boys) {
            //Chart
            const piechart = $("#staffSummaryChart");
            new Chart(piechart, {
                type: 'doughnut',
                data: {
                    labels: ['Female', 'Male'],
                    datasets: [{
                        backgroundColor: [
                            'rgba(228, 114, 0, 0.82)',
                            'rgba(83, 0, 255, 0.82)'
                        ],
                        label: 'Total Students',
                        data: [girls, boys],
                        borderWidth: 1
                    }]
                },
            });
        }

        //Fetch the Term
        $.ajax({
            type: 'get',
            url: '{{ route("home.term") }}',
            success: function(data) {
                student_chart((data.girls), (data.boys));
                staff_chart((data.females), (data.males));

                $(".school_name_header").text(data.school.school_name)

                var term = data.term.term;
                var year = data.term.year;
                $("#term").text(term);
                $("#year").text(year);
            }
        });

        $(document).ready(function() {
            //Fetch Student attendance
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                url: '{{ route("attendance.student.get") }}',
                success: function(data) {
                    if (data.total == 0) {
                        $("#notification-message").text("Create Student Attendance Table!");
                        $("#landingNotificationModal").modal('show');
                    } else {
                        //student attendance
                        $("#std_total").text(data.total);
                        $("#std_present").text(data.present);
                        var present_val = parseInt((parseInt(data.present) / parseInt(data.total)) *
                            100);
                        $("#student_present_progress").css('width',  ''+present_val+'%');
                        $("#std_absent").text(data.absent);
                    }
                },
                error: function() {
                    alert("Failed to get attendance data!");
                }
            });

            //Fetch Staff attendance
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                url: '{{ route('attendance.staff.get') }}',
                success: function(data) {
                    if (data.total == 0) {
                        $("#notification-message").text("Create Staff Attendance Table!");
                        $("#landingNotificationModal").modal('show');
                    } else {
                        //staff attendance
                        $("#staff_total").text(data.total);
                        $("#staff_present").text(data.present);
                        var present_val = parseInt((parseInt(data.present) / parseInt(data.total)) *
                            100);
                        $("#staff_present_progress").css('width', ''+present_val+'%');
                        $("#staff_absent").text(data.absent);
                    }
                },
                error: function() {
                    alert("Failed to get attendance data!");
                }
            });
        })
    </script>
@endpush
