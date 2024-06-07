@php
    $system_header = 'Online Academic Management Information System';
@endphp

<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title')</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap-icons/bootstrap-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('css/fontawesome/css/all.css') }}">
    <link rel="stylesheet" href="{{ asset('css/responsive.css') }}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    <link rel="stylesheet" href="{{ asset('css/datatable.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/flatpickr.css') }}">
    <link rel="stylesheet" href="{{ asset('/') }}css/datepicker.css">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}?v=3">
</head>

<body>

    <div class="school-dashboard d-none">
        <div class="dashboard-nav" id="dash_nav">
            <div class="mt-3 text-center user_section">
                <p class="mb-0 text-white text-center h5"><i class="fa fa-user"></i> <span class="text-warning fw-bold">
                        @php
                            if (Auth::user()->username != null) {
                                $user_name = explode(' ', Auth::user()->username);
                                echo $user_name[0];
                            } else {
                                $username = '';
                            }
                        @endphp
                    </span></p>
                <div>
                    {{-- If The image is empty --}}
                    @if (Auth::user()->image == null || Auth::user()->image == '')
                        @if (Auth::user()->gender == 'male')
                            <img src="{{ asset('/') }}images/static/male.jpg" class="img-fluid w-50 mt-2"
                                style="border-radius: 60px;" alt="">
                        @elseif(Auth::user()->gender == 'female')
                            <img src="{{ asset('/') }}images/static/female.jpg" class="img-fluid w-50 mt-2"
                                style="border-radius: 60px;" alt="">
                        @endif
                    @else
                        <a target="_blank" href="{{ asset('/') }}images/users/{{ Auth::user()->image }}">
                            <img src="{{ asset('/') }}images/users/{{ Auth::user()->image }}"
                                class="img-fluid w-50 mt-2" style="border-radius: 60px; width:150px !important; height:150px !important; object-fit:contain;" alt="">
                        </a>
                    @endif
                </div>

                <div>
                    <form id="view_user_form_home" method="post">
                        @csrf
                        <input type="hidden" name="user_id" value="{{ Auth::user()->id }}">
                        <button type="submit" class="btn btn-sm btn-outline-warning text-uppercase mt-2">My
                            Profile</button>
                    </form>
                </div>
            </div>{{-- -User Details here --}}

            <ul class="nav mt-2" id="navigation_bar">
                @if (Auth::user()->is_super_admin == 1 || Auth::user()->is_admin == 1)
                    <div>
                        <div class="nav-item nav-title" id="student_items" title="Student Data">
                            Students
                        </div>
                        <div id="student_items_list">
                            <li class="nav-item" title="Add Student">
                                <a href="{{ route('student.add') }}" class="nav-link"><i class="bi bi-plus-circle"></i>
                                    Add
                                    Student</a>
                            </li>

                            <li class="nav-item" title="View Student">
                                <a href="{{ route('student.view') }}" class="nav-link"><i class="bi bi-binoculars"></i>
                                    View Student</a>
                            </li>

                            <li class="nav-item" title="Student Status">
                                <a href="{{ route('student.status.index') }}" class="nav-link"><i
                                        class="bi bi-broadcast-pin"></i>
                                    Student Status</a>
                            </li>
                        </div>
                    </div>{{-- Students --}}
                @endif

                @if (Auth::user()->is_super_admin == 1 || Auth::user()->is_admin == 1)
                    <div>
                        <div class="nav-item nav-title" id="staff_items" title="Staff Data">
                            Staff
                        </div>
                        <div id="staff_items_list">
                            <li class="nav-item" title="Add Staff">
                                <a href="{{ route('staff.data.index') }}" class="nav-link"><i
                                        class="bi bi-plus-circle"></i> Add Staff</a>
                            </li>
                            <li class="nav-item" title="View Staff">
                                <a href="{{ route('staff.display') }}" class="nav-link"><i
                                        class="bi bi-binoculars"></i> View Staff</a>
                            </li>
                        </div>
                    </div>{{-- Staff --}}
                @endif

                <div>
                    <div class="nav-item nav-title" id="attendance_items" title="Student Data">
                        Attendance
                    </div>
                    <div id="attendance_items_list">
                        <li class="nav-item" title="Add Student">
                            <a href="{{ route('attendance.student') }}" class="nav-link"><i class="bi bi-plus-circle"></i>
                                Student Attendance</a>
                        </li>

                        <li class="nav-item" title="View Student">
                            <a href="{{ route('attendance.staff') }}" class="nav-link"><i class="bi bi-plus-circle"></i>
                                Staff Attendance</a>
                        </li>

                        <li class="nav-item" title="Student Status">
                            <a href="" class="nav-link"><i class="fa fa-calendar"></i>
                                Attendance Summary</a>
                        </li>
                    </div>
                </div>{{-- Attendance --}}

                @if (Auth::user()->is_super_admin == 1 || Auth::user()->is_admin == 1 || Auth::user()->is_bursar == 1)
                    <div>
                        <div class="nav-item nav-title" id="fees_items" title="Fees Collection">
                            Fees Collection
                        </div>
                        <div id="fees_items_list">
                            <li class="nav-item" title="Pending">
                                <a href="" class="nav-link">
                                    <i class="bi bi-file-earmark-check"></i> Pending Fees
                                </a>
                            </li>
                            <li class="nav-item" title="Pay Fees">
                                <a href="" class="nav-link">
                                    <i class="bi bi-file-earmark-check"></i> Pay Fees
                                </a>
                            </li>
                        </div>
                    </div>{{-- Student Fees --}}
                @endif

                <div class="d-none">
                    <div class="nav-item nav-title" id="parents_items" title="Parents Data">
                        Parents - Information
                    </div>
                    <div id="parents_items_list">
                        <li class="nav-item" title="View Parents">
                            <a href="" class="nav-link"><i class="bi bi-clipboard-plus"></i> View Parents</a>
                        </li>
                    </div>
                </div>{{-- Parents --}}

                <div>
                    <div class="nav-item nav-title" id="results_items" title="Results">
                        Results
                    </div>
                    <div id="results_items_list">
                        <li class="nav-item" title="Marksheet">
                            <a href="{{ route('alevel.marksheet') }}" class="nav-link">
                                <i class="bi bi-file-earmark-check"></i> A'Level Marksheet</a>
                        </li>
                        <li class="nav-item" title="Marksheet">
                            <a href="{{ route('marksheet.olevel') }}" class="nav-link"><i
                                    class="bi bi-file-earmark-check"></i> O'Level Marksheet</a>
                        </li>

                        <li class="nav-item" title="Marksheet">
                            <a href="{{ route('marksheet.olevel') }}" class="nav-link"><i
                                    class="bi bi-file-earmark-check"></i> A'Level Marklist</a>
                        </li>
                        <li class="nav-item" title="Marksheet">
                            <a href="{{ route('marksheet.olevel') }}" class="nav-link"><i
                                    class="bi bi-file-earmark-check"></i> O'Level Marklist</a>
                        </li>

                        <li class="nav-item" title="A'Level">
                            <a href="{{ route('alevel.index') }}" class="nav-link"><i class="bi bi-plus-circle"></i>
                                Results (A'Level)</a>
                        </li>
                        <li class="nav-item" title="O'Level">
                            <a href="{{ route('olevel.index') }}" class="nav-link"><i class="bi bi-plus-circle"></i>
                                Results (O'Level)</a>
                        </li>
                        @if (Auth::user()->is_super_admin == 1 || Auth::user()->is_admin == 1)
                            <li class="nav-item" title="Reports">
                                <a href="{{ route('reports.alevel') }}" class="nav-link"><i
                                        class="bi bi-clipboard-plus"></i> Reports (A'Level)</a>
                            </li>
                            <li class="nav-item" title="Reports">
                                <a href="{{ route('reports.olevel') }}" class="nav-link"><i
                                        class="bi bi-clipboard-plus"></i> Reports (O'Level)</a>
                            </li>
                        @endif
                    </div>
                </div>{{-- Student Results --}}


                <div>
                    <div class="nav-item nav-title" id="settings_items" title="Settings">
                        Settings
                    </div>

                    <div id="settings_items_list">
                        @if (Auth::user()->is_super_admin == 1 || Auth::user()->is_admin == 1)
                            <li class="nav-item" title="Term">
                                <a href="{{ route('setting.term') }}" class="nav-link"><i
                                        class="bi bi-clipboard-plus"></i> Change Term</a>
                            </li>

                            <li class="nav-item" title="Status">
                                <a href="{{ route('status.list.index') }}" class="nav-link"><i
                                        class="bi bi-plus-circle"></i> Status List</a>
                            </li>

                            <li class="nav-item" title="Classes">
                                <a href="{{ route('setting.subjects.index') }}" class="nav-link"><i
                                        class="bi bi-book"></i> Subjects</a>
                            </li>
                        @endif

                        <li class="nav-item" title="Classes">
                            <a href="{{ route('topics.index') }}" class="nav-link"><i class="bi bi-book-half"></i>
                                Topics</a>
                        </li>

                        @if (Auth::user()->is_super_admin == 1 || Auth::user()->is_admin == 1)
                            <li class="nav-item" title="Classes">
                                <a href="{{ route('setting.results.index') }}" class="nav-link"><i
                                        class="bi bi-table"></i> Results Table</a>
                            </li>

                            <li class="nav-item" title="Classes">
                                <a href="{{ route('class.index') }}" class="nav-link"><i
                                        class="bi bi-door-open-fill"></i> Classes</a>
                            </li>

                            <li class="nav-item" title="Streams">
                                <a href="{{ route('stream.index') }}" class="nav-link"><i
                                        class="bi bi-bar-chart-line"></i> Streams</a>
                            </li>

                            <li class="nav-item" title="School Details">
                                <a href="{{ route('setting.school') }}" class="nav-link"><i
                                        class="fa fa-school"></i>
                                    School Details</a>
                            </li>

                            <li class="nav-item" title="Initials">
                                <a href="{{ route('setting.initials') }}" class="nav-link"><i
                                        class="bi bi-bookmark-plus"></i> Teacher Initials</a>
                            </li>

                            <li class="nav-item" title="Positions">
                                <a href="{{ route('positions.index') }}" class="nav-link"><i
                                        class="bi bi-1-circle"></i> Positions</a>
                            </li>

                            <li class="nav-item" title="Signatures">
                                <a href="{{ route('setting.signatures') }}" class="nav-link"><i
                                        class="bi bi-at h4"></i> Signatures</a>
                            </li>

                            <li class="nav-item" title="Signatures">
                                <a href="{{ route('users.index') }}" class="nav-link"><i class="fa fa-users"></i>
                                    Users</a>
                            </li>

                            <li class="nav-item" title="Planner">
                                <a href="{{ route('planner.index') }}" class="nav-link"><i
                                        class="fa fa-calendar"></i> Term Planner</a>
                            </li>

                            <li class="nav-item" title="Student Cards">
                                <a href="" class="nav-link"><i class="bi bi-credit-card"></i> Student
                                    Cards</a>
                            </li>
                        @endif
                    </div>
                </div>{{-- Settings --}}


            </ul>

            <div class="text-center mb-3">
                <div id="software-version" class="badge bg-warning text-dark"></div>
            </div>

        </div>{{-- Nav bar here --}}

        <div class="w-100">
            <div class="system-header">
                @php
                    $year = date('Y', strtotime(now()));
                @endphp
                <div class="badge p-2 academic-year">
                    <p class="mb-0 h6 text-dark">Term:
                        <span id="term" style="color:purple;font-weight:bold;"></span>
                        Year:
                        <span id="year" style="color:purple;font-weight:bold;"></span>
                    </p>
                </div>

                <h5 class="mb-0 school_name_header">{{ $system_header }}</h5>{{-- School Name --}}

                <div class="sign_lock align-items-center" style="gap:10px;">
                    <div data-bs-toggle="tooltip" data-bs-placement="left" title="Notifications" id="messages">
                        <a href="" class="text-dark d-flex">
                            <div class="envelope">
                                <i class="bi bi-envelope"></i>
                            </div>
                            <div class="badge text-white fw-bold"
                                style="background:purple; position: absolute; margin-left:12px;">
                                0
                            </div>
                        </a>
                    </div>
                    <div title="Sign-out" id="sign-out">
                        <a href="{{ route('logout') }}" class="text-dark"><i class="bi bi-lock-fill"></i></a>
                    </div>
                    <div title="Home" id="go-home">
                        <a href="{{ route('home') }}" class="text-dark"><i class="bi bi-house-fill"></i>
                        </a>
                    </div>
                    <button class="hamburger">
                        <div class="bar"></div>
                        <div class="bar"></div>
                        <div class="bar"></div>
                    </button>
                </div>
            </div>{{-- Header here --}}

            <div class="system-body">
                @include('common.modals')               

                @yield('body')

            </div>{{-- Actual Body here --}}
        </div>
    </div>

    <div class="loader-container justify-content-center w-100 d-flex">
        <div class="loader">

        </div>
    </div>

    <div>

    </div>

    @stack('body-scripts')

    <script src="{{ asset('') }}js/jquery.min.js"></script>
    <script src="{{ asset('') }}js/datatable.min.js"></script>
    <script src="{{ asset('') }}js/bootstrap.bundle.js"></script>
    <script src="{{ asset('') }}js/bootstrap-datepicker.js"></script>
    <script>
        //Tooltip
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });

        $(window).on('load', function() {
            $(".loader-container").addClass('d-none');

            var counter = 0;

            function increment(value) {
                counter += value
                return counter;
            }

            const counterInterval = setInterval(function() {
                value = ((increment(1)) / 10);
                $(".school-dashboard").removeClass('d-none').css({
                    'opacity': value
                });
                if (value == 1) {
                    clearInterval(counterInterval);
                }
            }, 100);
        });

        //Hide the nav items
        $("#student_items_list").hide();
        $("#staff_items_list").hide();
        $("#results_items_list").hide();
        $("#settings_items_list").hide();
        $("#parents_items_list").hide();
        $("#parents_items_list").hide();
        $("#fees_items_list").hide();
        $("#attendance_items_list").hide();        

        //Nav-items-dropdown
        $(".nav-title").on('click', function() {
            var id = $(this).prop('id');
            $("#" + id + "_list").slideToggle()
        });

        

        //Fetch the Term
        $.ajax({
            type: 'get',
            url: '{{ route("home.term") }}',
            success: function(data) {
                $(".school_name_header").text(data.school.school_name)

                var term = data.term.term;
                var year = data.term.year;
                $("#term").text(term);
                $("#year").text(year);
            }
        });

        /*
        //Autoupdate the term
        $(window).focus(function() {
            $.ajax({
                type: 'get',
                url: '{{ route("home.term") }}',
                success: function(data) {
                    var term = data.term.term;
                    var year = data.term.year;
                    $("#term").text(term);
                    $("#year").text(year);
                }
            })
        });
        */

        //Nav-bar
        $(".hamburger").on('click', function() {
            $(".dashboard-nav").toggleClass('is_mob_active');
            $(".bar").toggleClass('is_active');
        });

        //User profile
        //View the user details
        $("#view_user_form_home").on('submit', function(e) {
            e.preventDefault();

            //Show the modal
            $("#userProfileModal").modal('show');

            //Clear the form
            $(".update_user_form")[0].reset();
            $.ajax({
                type: "POST",
                url: "{{ route('users.fetch') }}",
                data: new FormData(this),
                processData: false,
                contentType: false,
                cache: false,
                success: function(response) {
                    var id = response.id;
                    var username = response.username;
                    var email = response.email;
                    var dept = response.dept;
                    var is_active = response.is_active;
                    var email_verified = response.email_verified;
                    var image = response.image;
                    var gender = response.gender;

                    //Add data to the inputs
                    $("input[name='user_update_id']").val(id);
                    $("input[name='username']").val(username);
                    $("select[name='gender']").val(gender);
                    $("input[name='email']").val(email);
                    $("input[name='department']").val(dept);

                    if (response.is_admin == 1) {
                        $("input[name='priviledge']").val('admin');
                    } else if (response.is_teacher == 1) {
                        $("input[name='priviledge']").val('teacher');
                    } else if (response.is_bursar == 1) {
                        $("input[name='priviledge']").val('bursar');
                    } else if (response.is_librarian == 1) {
                        $("input[name='priviledge']").val('librarian');
                    } else if (dept == 'it-support') {
                        $("input[name='priviledge']").val('it-support');
                    } else {
                        $("input[name='priviledge']").val('other');
                    }

                    //User-Active
                    if (response.is_active == 1) {
                        $("input[name='active']").prop('checked', true);
                    } else {
                        $("input[name='active']").prop('checked', false);
                    }

                    //Email verified
                    if (response.email_verified == 1) {
                        $("input[name='verify_email']").prop('checked', true);
                    } else {
                        $("input[name='verify_email']").prop('checked', false);
                    }

                    //Check image
                    if (image == null || image == "" || image == 'NULL') {
                        if (gender == 'male') {
                            var img_src = "/images/static/male.jpg";
                        } else if (gender == 'female') {
                            var img_src = "/images/static/female.jpg";
                        }

                        $("#user_img").attr('src', img_src);
                    } else {
                        var img_src = "/images/users/" + image + "";
                        $("#user_img").attr('src', img_src);
                    }

                    //Remove Spinner
                    $(".spinner_container").addClass('d-none');
                    $(".user_form_container").removeClass('d-none');
                },
                error: function() {
                    alert("Failed to Fetch User!");
                }
            });
        });

        $("#userProfileModal").on('hidden.bs.modal', function() {
            //Add Spinner
            $(".spinner_container").removeClass('d-none');
            $(".user_form_container").addClass('d-none');
        });

        //Update User
        $(".update_user_form").on('submit', function(e) {
            e.preventDefault();
            //Check password match
            var pass1 = $('#update_password_home').val();
            var pass2 = $('#update_confirm_password_home').val();

            if (pass1 == pass2) {
                if (pass1.length > 6) {
                    //Hide the modal
                    $("#userProfileModal").modal('hide');
                    $.ajax({
                        type: "POST",
                        url: "{{ route('users.update') }}",
                        data: new FormData(this),
                        processData: false,
                        contentType: false,
                        cache: false,
                        success: function(response) {
                            alert(response);
                            location.reload();
                        },
                        error: function() {
                            alert("Failed to Fetch User!");
                        }
                    });
                } else if (pass1.length == 0 && pass2.length == 0) {
                    //Hide the modal
                    $("#userProfileModal").modal('hide');
                    $.ajax({
                        type: "POST",
                        url: "{{ route('users.update') }}",
                        data: new FormData(this),
                        processData: false,
                        contentType: false,
                        cache: false,
                        success: function(response) {
                            alert(response);
                            location.reload();
                        },
                        error: function() {
                            alert("Failed to Fetch User!");
                        }
                    });
                } else {
                    alert("Password should exceed 6 characters!");
                }
            } else {
                alert("Error:Password Mismatch!");
            }
        });

        //Display password
        $(".display_password").on('click', function() {
            var pass_prop = $("input[name='password']").prop('type');
            if (pass_prop == 'text') {
                $("input[name='password']").prop('type', 'password');
            } else {
                $("input[name='password']").prop('type', 'text');
            }
        });

        $(".display_confirm_password").on('click', function() {
            var pass_prop = $("input[name='confirm_password']").prop('type');
            if (pass_prop == 'text') {
                $("input[name='confirm_password']").prop('type', 'password');
            } else {
                $("input[name='confirm_password']").prop('type', 'text');
            }
        });

        //Change Image
        $("#user_img").on('click', function() {
            $("#userProfileModal").modal('hide');
            //Clear the form
            $(".update_user_image_form")[0].reset();
            $("#updateImageModal").modal('show');
        });

        //Enable the image update button
        $("input[name='user_image']").on('change', function() {
            $(".update_img_btn").removeClass('submit-btn-disabled').addClass('submit-btn').prop('disabled', false);
        })

        $(".update_user_image_form").on('submit', function(e) {
            e.preventDefault();
            //Hide the modal
            $("#updateImageModal").modal('hide');

            var user_id = $("input[name='user_update_id']").val();
            $("input[name='update_user_image_id']").val(user_id);

            $.ajax({
                type: 'POST',
                url: '{{ route("users.update.image") }}',
                data: new FormData(this),
                contentType: false,
                processData: false,
                cache: false,
                success: function(response) {
                    alert(response);
                    location.reload();
                },
                error: function() {
                    alert("Failed to Save Image");
                }
            })
        });

    </script>

</body>

</html>
