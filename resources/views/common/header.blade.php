@php
    $system_header = 'Online Academic school Management System';
    //$system_header = 'Cornerstone High School Nangabo - Admin';
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
    <link rel="stylesheet" href="{{ asset('/') }}css/datepicker.css">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}?v=3">
</head>

<body>

    <div class="school-dashboard">
        <div class="dashboard-nav" id="dash_nav">
            <div class="mt-3 text-center user_section">
                <p class="mb-0 text-white text-center h5">User: <span
                        class="text-warning fw-bold">{{ Auth::user()->username }}</span></p>
                {{-- If The image is empty --}}
                @if (Auth::user()->image == null || Auth::user()->image == '')
                    @if (Auth::user()->gender == 'male')
                        <img src="{{ asset('/') }}images/static/male.jpg" class="img-fluid w-50 mt-2" style="border-radius: 60px;" alt="">
                    @elseif(Auth::user()->gender == 'female')
                        <img src="{{ asset('/') }}images/static/female.jpg" class="img-fluid w-50 mt-2" style="border-radius: 60px;"  alt="">
                    @endif
                @else
                    <img src="{{ asset('/') }}images/users/{{ Auth::user()->image }}" class="img-fluid w-50 mt-2" style="border-radius: 60px;"  alt="">
                @endif
                <div>
                    <a href="" class="nav-link mt-2">My Profile</a>
                </div>
            </div>{{-- -User Details here --}}

            <ul class="nav mt-2" id="navigation_bar">
                <div>
                    <div class="nav-item nav-title" id="student_items" title="Student Data">
                        Students
                    </div>
                    <div id="student_items_list">
                        <li class="nav-item" title="Add Student">
                            <a href="{{ route('student.add') }}" class="nav-link"><i class="bi bi-plus-circle"></i> Add
                                Student</a>
                        </li>

                        <li class="nav-item" title="View Student">
                            <a href="{{ route('student.view') }}" class="nav-link"><i class="bi bi-clipboard-plus"></i>
                                View Student</a>
                        </li>

                        <li class="nav-item" title="Student Status">
                            <a href="{{ route('student.status.index') }}" class="nav-link"><i
                                    class="bi bi-clipboard-plus"></i>
                                Student Status</a>
                        </li>
                    </div>
                </div>{{-- Students --}}

                <div>
                    <div class="nav-item nav-title" id="staff_items" title="Staff Data">
                        Staff
                    </div>
                    <div id="staff_items_list">
                        <li class="nav-item" title="Add Staff">
                            <a href="{{ route('staff.data.index') }}" class="nav-link"><i class="bi bi-plus-circle"></i> Add Staff</a>
                        </li>
                        <li class="nav-item" title="View Staff">
                            <a href="{{ route('staff.display') }}" class="nav-link"><i class="bi bi-clipboard-plus"></i> View Staff</a>
                        </li>
                    </div>
                </div>{{-- Staff --}}

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

                <div>
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
                            <a href="{{ route('marksheet.olevel') }}" class="nav-link"><i class="bi bi-file-earmark-check"></i> O'Level Marksheet</a>
                        </li>

                        <li class="nav-item" title="A'Level">
                            <a href="{{ route('alevel.index') }}" class="nav-link"><i class="bi bi-plus-circle"></i> Results (A'Level)</a>
                        </li>
                        <li class="nav-item" title="O'Level">
                            <a href="{{ route('olevel.index') }}" class="nav-link"><i class="bi bi-plus-circle"></i> Results (O'Level)</a>
                        </li>
                        <li class="nav-item" title="Reports">
                            <a href="{{ route('reports.alevel') }}" class="nav-link"><i class="bi bi-clipboard-plus"></i> Reports (A'Level)</a>
                        </li>
                        <li class="nav-item" title="Reports">
                            <a href="{{ route('reports.olevel') }}" class="nav-link"><i class="bi bi-clipboard-plus"></i> Reports (O'Level)</a>
                        </li>
                    </div>
                </div>{{-- Student Results --}}

                <div>
                    <div class="nav-item nav-title" id="settings_items" title="Settings">
                        Settings
                    </div>
                    <div id="settings_items_list">
                        <li class="nav-item" title="Term">
                            <a href="{{ route('setting.term') }}" class="nav-link"><i class="bi bi-clipboard-plus"></i> Change Term</a>
                        </li>

                        <li class="nav-item" title="Status">
                            <a href="{{ route('status.list.index') }}" class="nav-link"><i class="bi bi-plus-circle"></i> Status List</a>
                        </li>

                        <li class="nav-item" title="Classes">
                            <a href="{{ route('setting.subjects.index') }}" class="nav-link"><i class="bi bi-clipboard-plus"></i> Subjects</a>
                        </li>

                        <li class="nav-item" title="Classes">
                            <a href="{{ route('setting.results.index') }}" class="nav-link"><i class="bi bi-table"></i> Results Table</a>
                        </li>

                        <li class="nav-item" title="Classes">
                            <a href="" class="nav-link"><i class="bi bi-clipboard-plus"></i> Classes</a>
                        </li>

                        <li class="nav-item" title="Streams">
                            <a href="" class="nav-link"><i class="bi bi-clipboard-plus"></i> Streams</a>
                        </li>

                        <li class="nav-item" title="School Details">
                            <a href="{{ route('setting.school') }}" class="nav-link"><i class="fa fa-school"></i> School Details</a>
                        </li>

                        <li class="nav-item" title="Initials">
                            <a href="{{ route('setting.initials') }}" class="nav-link"><i class="bi bi-clipboard-plus"></i> Teacher Initials</a>
                        </li>

                        <li class="nav-item" title="Signatures">
                            <a href="{{ route('setting.signatures') }}" class="nav-link"><i class="bi bi-clipboard-plus"></i> Signatures</a>
                        </li>

                        <li class="nav-item" title="Signatures">
                            <a href="" class="nav-link"><i class="fa fa-users"></i> Users</a>
                        </li>

                        <li class="nav-item" title="Student Cards">
                            <a href="" class="nav-link"><i class="bi bi-clipboard-plus"></i> Student Cards</a>
                        </li>
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
                <h5 class="mb-0">{{ $system_header }}</h5>
                <div class="sign_lock align-items-center" style="gap:10px;">
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
            </div>
            
            {{-- Header here --}}

            <div class="system-body">

                @yield('body')

            </div>{{-- Actual Body here --}}
        </div>
    </div>

    @stack('body-scripts')

    <script src="{{ asset('') }}js/jquery.min.js"></script>
    <script src="{{ asset('') }}js/datatable.min.js"></script>
    <script src="{{ asset('') }}js/bootstrap.bundle.js"></script>
    <script src="{{ asset('') }}js/bootstrap-datepicker.js"></script>
    <script>
        //Hide the nav items
        $("#student_items_list").hide();
        $("#staff_items_list").hide();
        $("#results_items_list").hide();
        $("#settings_items_list").hide();
        $("#parents_items_list").hide();
        $("#parents_items_list").hide();
        $("#fees_items_list").hide();

        //Nav-items-dropdown
        $(".nav-title").on('click', function() {
            var id = $(this).prop('id');
            console.log("title = " + id);
            $("#" + id + "_list").slideToggle()
        })

        //Fetch the Term
        $.ajax({
            type: 'get',
            url: '{{ route('home.term') }}',
            success: function(data) {
                var term = data.term.term;
                var year = data.term.year;
                $("#term").text(term);
                $("#year").text(year);
            }
        })

        //Autoupdate the term
        $(window).focus(function() {
            $.ajax({
                type: 'get',
                url: '{{ route('home.term') }}',
                success: function(data) {
                    var term = data.term.term;
                    var year = data.term.year;
                    $("#term").text(term);
                    $("#year").text(year);
                }
            })
        });

        //Nav-bar
        $(".hamburger").on('click',function(){
            $(".dashboard-nav").toggleClass('is_mob_active');
            $(".bar").toggleClass('is_active');
        });

    </script>

</body>

</html>
