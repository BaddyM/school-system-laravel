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
    <link rel="stylesheet" href="{{ asset('css/all.css') }}">
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
        <div class="dashboard-nav">
            <ul class="nav d-block mt-2">
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
                    </div>
                </div>{{-- Students --}}

                <div>
                    <div class="nav-item nav-title" id="staff_items" title="Staff Data">
                        Staff
                    </div>
                    <div id="staff_items_list">
                        <li class="nav-item" title="Add Staff">
                            <a href="" class="nav-link"><i class="bi bi-plus-circle"></i> Add Staff</a>
                        </li>
                        <li class="nav-item" title="View Staff">
                            <a href="" class="nav-link"><i class="bi bi-clipboard-plus"></i> View Staff</a>
                        </li>
                    </div>
                </div>{{-- Staff --}}

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
                            <a href="{{ route('alevel.marksheet') }}" class="nav-link"><i
                                    class="bi bi-file-earmark-check"></i> A'Level Marksheet</a>
                        </li>
                        <li class="nav-item" title="Marksheet">
                            <a href="" class="nav-link"><i class="bi bi-file-earmark-check"></i> O'Level
                                Marksheet</a>
                        </li>
                        <li class="nav-item" title="A'Level">
                            <a href="" class="nav-link"><i class="bi bi-plus-circle"></i> Results (A'Level)</a>
                        </li>
                        <li class="nav-item" title="O'Level">
                            <a href="" class="nav-link"><i class="bi bi-plus-circle"></i> Results (O'Level)</a>
                        </li>
                        <li class="nav-item" title="Reports">
                            <a href="" class="nav-link"><i class="bi bi-clipboard-plus"></i> V.D Reports</a>
                        </li>
                        <li class="nav-item" title="Reports">
                            <a href="" class="nav-link"><i class="bi bi-clipboard-plus"></i> E.O.T Reports</a>
                        </li>
                    </div>
                </div>{{-- Student Results --}}

                <div>
                    <div class="nav-item nav-title" id="settings_items" title="Settings">
                        Settings
                    </div>
                    <div id="settings_items_list">
                        <li class="nav-item" title="Term">
                            <a href="" class="nav-link"><i class="bi bi-clipboard-plus"></i> Change Term</a>
                        </li>

                        <li class="nav-item" title="Status">
                            <a href="" class="nav-link"><i class="bi bi-plus-circle"></i> Status</a>
                        </li>
                        <li class="nav-item" title="Classes">
                            <a href="" class="nav-link"><i class="bi bi-clipboard-plus"></i> Classes</a>
                        </li>

                        <li class="nav-item" title="Streams">
                            <a href="" class="nav-link"><i class="bi bi-clipboard-plus"></i> Streams</a>
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
                <div class="d-flex" style="gap:10px;">
                    <div title="Sign-out" id="sign-out"><i class="bi bi-lock-fill"></i></div>
                    <div title="Home" id="go-home"><a href="{{ route('home') }}" class="text-dark"><i
                                class="bi bi-house-fill"></i></a></div>
                </div>
            </div>{{-- Header here --}}

            <div class="system-body">

                @yield('body')

            </div>{{-- Actual Body here --}}
        </div>
    </div>

    @stack('body-scripts')

    <script src="{{ asset('') }}js/jquery.min.js"></script>
    <script src="{{ asset('') }}js/datatable.min.js"></script>
    <script src="{{ asset('') }}js/bootstrap-datepicker.js"></script>
    <script>
        //Hide the nav items
        $("#student_items_list").hide();
        $("#staff_items_list").hide();
        $("#results_items_list").hide();
        $("#settings_items_list").hide();
        $("#parents_items_list").hide();

        //Nav-items-dropdown
        $(".nav-title").on('click', function() {
            var id = $(this).prop('id');
            console.log("title = " + id);
            $("#" + id + "_list").slideToggle()
        })

        //Fetch the Term
        $.ajax({
            type:'get',
            url:'{{ route("home.term") }}',
            success:function(data){
                var term = data.term.term;
                var year = data.term.year;
                $("#term").text(term);
                $("#year").text(year);
            }
        })

        //Autoupdate the term
        $(window).focus(function(){
            $.ajax({
                type:'get',
                url:'{{ route("home.term") }}',
                success:function(data){
                    var term = data.term.term;
                    var year = data.term.year;
                    $("#term").text(term);
                    $("#year").text(year);
                }
            })
        })
    </script>

</body>

</html>
