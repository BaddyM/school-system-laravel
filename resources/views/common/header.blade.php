@php

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    $school_name = 'Cornerstone H.S';

@endphp

<!doctype html>
<html lang="en">

<head>
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title')</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap-icons/bootstrap-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('css/all.css') }}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    <link rel="stylesheet" href="{{ asset('css/datatable.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="shortcut icon" href="{{ asset('images/icon/nangabo.ico') }}" type="image/x-icon">


    <style>
        body {
            font-family: 'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif;
        }

        /*---------------- Marksheet css -------------------------*/

        input,
        select,
        text-area {
            outline: none !important;
            box-shadow: none !important;
        }

        th {
            text-transform: uppercase;
        }

        .dataTables_filter input {
            margin-bottom: 10px;
        }

        .dataTables_length select {
            width: 100px;
        }

        .dataTables_filter input,
        .dataTables_length select {
            padding: 7px !important;
        }

        .bd-placeholder-img {
            font-size: 1.125rem;
            text-anchor: middle;
            -webkit-user-select: none;
            -moz-user-select: none;
            user-select: none;
        }

        @media (min-width: 768px) {
            .bd-placeholder-img-lg {
                font-size: 3.5rem;
            }
        }

        .b-example-divider {
            height: 3rem;
            background-color: rgba(0, 0, 0, .1);
            border: solid rgba(0, 0, 0, .15);
            border-width: 1px 0;
            box-shadow: inset 0 .5em 1.5em rgba(0, 0, 0, .1), inset 0 .125em .5em rgba(0, 0, 0, .15);
        }

        .b-example-vr {
            flex-shrink: 0;
            width: 1.5rem;
            height: 100vh;
        }

        .bi {
            vertical-align: -.125em;
            fill: currentColor;
        }

        .nav-scroller {
            position: relative;
            z-index: 2;
            height: 2.75rem;
            overflow-y: hidden;
        }

        .nav-scroller .nav {
            display: flex;
            flex-wrap: nowrap;
            padding-bottom: 1rem;
            margin-top: -1px;
            overflow-x: auto;
            text-align: center;
            white-space: nowrap;
            -webkit-overflow-scrolling: touch;
        }

        #std-img-view {
            width: 100px;
            height: 100px;
        }

        input,
        button,
        select {
            box-shadow: none !important;
        }
    </style>



</head>

<body>
    <header class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0 shadow">
        <a class="navbar-brand fw-bold col-md-3 col-lg-2 me-0 px-3 fs-6" href="#"><?php echo $school_name; ?> Admin</a>
        <button class="navbar-toggler position-absolute d-md-none collapsed" type="button" data-bs-toggle="collapse"
            data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false"
            aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="bg-light w-100">
            <h2 class="text-center h4 text-primary p-lg-2 fw-bold">ONLINE ACADEMIC MANAGEMENT INFORMATION SYSTEM <b
                    class="text-danger">(OAMIS)</b></h2>
        </div>
        <div class="navbar-nav">
            <div class="nav-item text-nowrap">
                <form action="logout.php" method="post">
                    <button type="submit" class="btn btn-outline-dark text-white px-3"><i
                            class="bi bi-box-arrow-in-right px-2 fs-5"></i> Sign out</button>
                </form><!-- Logout form -->
            </div>
        </div>
    </header>

    <div class="container-fluid">
        <div class="row">
            <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
                <div class="position-sticky pt-3 sidebar-sticky">
                    <ul class="nav flex-column fw-bold">
                        <li class="nav-item">
                            <a class="nav-link active text-decoration-underline" aria-current="page" href="index">
                                <span data-feather="home" class="fw-bold align-text-bottom"></span>
                                Director
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="collapse" role="button" aria-expanded="false"
                                aria-controls="marksheet" href="#marksheet">
                                <span data-feather="layers" class="align-text-bottom"></span>
                                <i class="bi bi-card-checklist fs-5"></i> Marksheet
                            </a>
                            <div class="collapse ms-3" id="marksheet">
                                <ul class="nav flex-column">
                                    <li class="nav-item"><a class="text-info nav-link" target="_blank"
                                            href="{{ route('alevel.marksheet') }}">A Level Marksheet</a></li>
                                    <li class="nav-item"><a class="text-info nav-link" href="olevelmarksheet">O Level
                                            Marksheet</a></li>
                                </ul>
                            </div>
                        </li>
                        <!--Marklist -->
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="collapse" role="button" aria-expanded="false"
                                aria-controls="marklist" href="#marklist">
                                <span data-feather="layers" class="align-text-bottom"></span>
                                <i class="bi bi-file-earmark-spreadsheet-fill fs-5"></i> Marklist
                            </a>
                            <div class="collapse ms-3" id="marklist">
                                <ul class="nav flex-column">
                                    <li class="nav-item"><a class="text-info nav-link"
                                            href="a_level_marklist">A'Level</a></li>
                                    <li class="nav-item"><a class="text-info nav-link"
                                            href="o_level_marklist">O'Level</a></li>
                                </ul>
                            </div>
                        </li>
                        <!--Marklist -->

                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="collapse" role="button" aria-expanded="false"
                                aria-controls="Staff" href="#Staff">
                                <span data-feather="layers" class="align-text-bottom"></span>
                                <i class="bi bi-person-circle fs-5"></i> Staff
                            </a>
                            <div class="collapse ms-3" id="Staff">
                                <ul class="nav flex-column"><a class="text-info nav-link" href="viewStaff">View
                                        Staff</a>
                        </li>
                        <li class="nav-item"><a class="text-info nav-link" href="addStaff">Add Staff</a></li>
                        <li class="nav-item"><a class="text-info nav-link" href="removeStaff">Remove Staff</a></li>
                        <li class="nav-item"><a class="text-info nav-link" href="updateStaff">Update Staff</a></li>
                    </ul>
                </div>
                </li>
                <!--Staff -->

                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="collapse" role="button" aria-expanded="false"
                        aria-controls="student" href="#student">
                        <span data-feather="layers" class="align-text-bottom"></span>
                        <i class="bi bi-people-fill fs-5"></i> Students
                    </a>
                    <div class="collapse ms-3" id="student">
                        <ul class="nav flex-column">
                            <li class="nav-item"><a class="text-info nav-link" href="addStudent">Add Student</a></li>
                            <!-- <li class="nav-item"><a class="text-info nav-link" href="addSenior4">Add Senior 4(Old)</a></li>
                <li class="nav-item"><a class="text-info nav-link" href="stdstatus">Status</a></li>
            -->
                            <li class="nav-item"><a class="text-info nav-link" href="discipline">Discipline</a></li>
                            <li class="nav-item"><a class="text-info nav-link" href="studentdetails">Student
                                    Details</a></li>
                            <li class="nav-item"><a class="text-info nav-link" href="viewStudent">Continuing</a></li>
                            <li class="nav-item"><a class="text-info nav-link" href="completed">Completed</a></li>
                            <li class="nav-item"><a class="text-info nav-link" href="remStudent">Remove Student</a>
                            </li>
                            <li class="nav-item"><a class="text-info nav-link" href="updateStudent">Update
                                    Student</a></li>
                            <li class="nav-item"><a class="text-info nav-link" href="promotestd">Promote Student</a>
                            </li>
                            <li class="nav-item"><a class="text-info nav-link"
                                    href="stdapplications">Applications</a></li>
                        </ul>
                    </div>
                </li><!-- Students -->

                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="collapse" role="button" aria-expanded="false"
                        aria-controls="results" href="#results">
                        <span data-feather="layers" class="align-text-bottom"></span>
                        <i class="bi bi-table fs-5"></i> Results
                    </a>
                    <div class="collapse ms-3" id="results">
                        <ul class="nav flex-column">
                            <li class="nav-item"><a class="text-info nav-link" href="oldcurr">Old Curriculum</a></li>
                            <li class="nav-item"><a class="text-info nav-link" href="newcurr">New Curriculum</a></li>
                            <li class="nav-item"><a class="text-info nav-link" href="printreports">Print Reports</a>
                            </li>
                            <li class="nav-item"><a class="text-info nav-link" href="vd_printreports">V.D Reports</a>
                            </li>
                        </ul>
                    </div>
                </li><!-- Enter Results -->

                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="collapse" role="button" aria-expanded="false"
                        aria-controls="fees" href="#fees">
                        <span data-feather="layers" class="align-text-bottom"></span>
                        <i class="bi bi-cash fs-5"></i> School Fees
                    </a>
                    <div class="collapse ms-3" id="fees">
                        <ul class="nav flex-column">
                            <li class="nav-item"><a class="text-info nav-link" href="enterfees">Pay Fees</a></li>
                            <li class="nav-item"><a class="text-info nav-link" href="viewfees">View Fees</a></li>
                            <li class="nav-item"><a class="text-info nav-link" href="updatefees">Update Fees</a></li>
                            <li class="nav-item"><a class="text-info nav-link" href="newtermfees">New Term Fees</a>
                            </li>
                        </ul>
                    </div>
                </li><!-- Enter School fees -->

                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="collapse" role="button" aria-expanded="false"
                        aria-controls="expenses" href="#income">
                        <span data-feather="layers" class="align-text-bottom"></span>
                        <i class="bi bi-bank2 fs-5"></i> School Income
                    </a>
                    <div class="collapse ms-3" id="income">
                        <ul class="nav flex-column">
                            <li class="nav-item"><a class="text-info nav-link" href="enterIncome">Enter Income</a>
                            </li>
                            <li class="nav-item"><a class="text-info nav-link" href="viewIncome">View Income</a></li>
                            <li class="nav-item"><a class="text-info nav-link" href="updateIncome">Update Income</a>
                            </li>
                        </ul>
                    </div>
                </li><!-- Enter School Expenditure -->

                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="collapse" role="button" aria-expanded="false"
                        aria-controls="budget" href="#budget">
                        <span data-feather="layers" class="align-text-bottom"></span>
                        <i class="bi bi-coin fs-5"></i> School Budget
                    </a>
                    <div class="collapse ms-3" id="budget">
                        <ul class="nav flex-column">
                            <li class="nav-item"><a class="text-info nav-link" href="enterBudget">Enter Budget</a>
                            </li>
                            <li class="nav-item"><a class="text-info nav-link" href="viewBudget">View Budget</a></li>
                            <li class="nav-item"><a class="text-info nav-link" href="updateBudget">Update Budget</a>
                            </li>
                            <li class="nav-item"><a class="text-info nav-link" href="enterPBudget">Enter Prop.
                                    budget</a></li>
                            <li class="nav-item"><a class="text-info nav-link" href="viewPBudget">View Prop.
                                    budget</a></li>
                            <li class="nav-item"><a class="text-info nav-link" href="updatePBudget">Update Prop.
                                    budget</a></li>
                        </ul>
                    </div>
                </li><!-- Enter School Budget -->

                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="collapse" role="button" aria-expanded="false"
                        aria-controls="academics" href="#academics">
                        <span data-feather="layers" class="align-text-bottom"></span>
                        <i class="bi bi-book-half fs-5"></i> Academics
                    </a>
                    <div class="collapse ms-3" id="academics">
                        <ul class="nav flex-column">
                            <li class="nav-item"><a class="text-info nav-link" href="timetable">Timetables</a></li>
                            <li class="nav-item"><a class="text-info nav-link" href="announce">Announcements</a></li>
                            <li class="nav-item"><a class="text-info nav-link" href="schooltuition">Fees
                                    Structure</a></li>
                            <li class="nav-item"><a class="text-info nav-link" href="#">Class Materials</a>
                            </li>
                        </ul>
                    </div>
                </li><!-- Academics -->

                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="collapse" role="button" aria-expanded="false"
                        aria-controls="academics" href="#users">
                        <span data-feather="layers" class="align-text-bottom"></span>
                        <i class="bi bi-person-workspace fs-5"></i> Users
                    </a>
                    <div class="collapse ms-3" id="users">
                        <ul class="nav flex-column">
                            <li class="nav-item"><a class="text-info nav-link" href="adduser">Add User</a></li>
                            <li class="nav-item"><a class="text-info nav-link" href="removeuser">Remove User</a></li>
                            <li class="nav-item"><a class="text-info nav-link" href="updateuser">Update User</a></li>
                            <li class="nav-item"><a class="text-info nav-link" href="viewuser">View Users</a></li>
                        </ul>
                    </div>
                </li><!-- Users -->

                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="collapse" role="button" aria-expanded="false"
                        aria-controls="academics" href="#library">
                        <span data-feather="layers" class="align-text-bottom"></span>
                        <i class="bi bi-journals fs-5"></i> Library
                    </a>
                    <div class="collapse ms-3" id="library">
                        <ul class="nav flex-column">
                            <li class="nav-item"><a class="text-info nav-link" href="addbook">Add Book(s)</a></li>
                            <li class="nav-item"><a class="text-info nav-link" href="removebook">Remove Book(s)</a>
                            </li>
                            <li class="nav-item"><a class="text-info nav-link" href="updatebook">Update Book(s)</a>
                            </li>
                            <li class="nav-item"><a class="text-info nav-link" href="viewbook">View Book(s)</a></li>
                            <li class="nav-item"><a class="text-info nav-link" href="borrowbook">Borrow Book(s)</a>
                            </li>
                            <li class="nav-item"><a class="text-info nav-link" href="returnbook">Return Book(s)</a>
                            </li>
                        </ul>
                    </div>
                </li><!-- Library -->

                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="collapse" role="button" aria-expanded="false"
                        aria-controls="academics" href="#sms">
                        <span data-feather="layers" class="align-text-bottom"></span>
                        <i class="bi bi-chat-dots-fill fs-5"></i> SMS
                    </a>
                    <div class="collapse ms-3" id="sms">
                        <ul class="nav flex-column">
                            <li class="nav-item"><a class="text-info nav-link" href="sms">Send SMS</a></li>
                        </ul>
                    </div>
                </li><!-- SMS -->

                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="collapse" role="button" aria-expanded="false"
                        aria-controls="academics" href="#password">
                        <span data-feather="layers" class="align-text-bottom"></span>
                        <i class="bi bi-key-fill fs-5"></i> Change Password
                    </a>
                    <div class="collapse ms-3" id="password">
                        <ul class="nav flex-column">
                            <li class="nav-item"><a class="text-info nav-link" href="changepass">Change Password</a>
                            </li>
                        </ul>
                    </div>
                </li><!-- Change Password -->

                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="collapse" role="button" aria-expanded="false"
                        aria-controls="academics" href="#enroll">
                        <span data-feather="layers" class="align-text-bottom"></span>
                        <i class="bi bi-unlock-fill fs-5"></i> New Term
                    </a>
                    <div class="collapse ms-3" id="enroll">
                        <ul class="nav flex-column">
                            <li class="nav-item"><a class="text-info nav-link" href="enroll">Enroll</a></li>
                        </ul>
                    </div>
                </li><!-- Change Password -->

                </ul>
        </div>
        </nav>

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div
                class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h4 fw-bold">Dashboard</h1>
                <h3 class="h4 fw-bold text-success"><?php $date = date('D, d M, Y');
                echo $date; ?></h3>
            </div>

            @yield('body')

            <div class="text-center p-3">
              <h5 class="text-center fw-bold p-1 text-white badge bg-primary">Version 2.0</h5>
            </div>
        </main>
</body>

</html
