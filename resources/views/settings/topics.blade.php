@extends('common.header')

@section('title')
    Topics
@endsection

@section('body')
    <style>
        #update_user_form input,
        #update_user_form select {
            color: purple;
            font-weight: bold;
        }

        tr th {
            color: white;
        }

        tr td {
            vertical-align: middle;
        }
    </style>

    <div class="container-fluid">

        <div class="modal fade" id="updateTopicModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false"
            role="dialog" aria-labelledby="modalTitleId" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-md"role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">
                            Update Topic
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-container d-none">
                            <form method="post" class="update_topic_form">
                                @csrf
                                <input type="hidden" name="update_topic_id">
                                <div class="mb-3">
                                    <label class="form-label fw-bold align-items-center"> <i class="bi bi-book h4"></i> Class</label>
                                    <select name="update_class" class="rounded-0 form-select" required>
                                        @foreach ($classes as $class)
                                            <option value="{{ $class->class }}">{{ $class->class }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold align-items-center"> <i class="bi bi-pen h4"></i> Subject</label>
                                    <select type="text" placeholder="Subject" class="form-select rounded-0" name="update_subject">
                                        @foreach ($subjects as $subject)
                                            <option value="{{ $subject->name }}">{{ $subject->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="form-label fw-bold align-items-center"> <i class="bi bi-journal h4"></i>
                                        Topic</label>
                                    <textarea type="text" placeholder="Add Topic" name="update_topic" class="form-control rounded-0" required></textarea>
                                </div>
                                <button class="submit-btn fw-bold mt-3 update_topic_btn" type="submit">Update</button>
                            </form>
                        </div>
                        <div class="d-flex justify-content-center align-items-center spinner-container">
                            <div style="color:purple;" class="spinner-border spinner-border-lg" role="status">
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>{{-- Update topics modal --}}

        <h5 class="mb-0 text-uppercase fw-bold text-center mb-3" style="color: purple;">Topics</h5>

        <div class="row justify-content-between">
            <div class="col-md-4 mb-5 shadow-lg py-4 px-3">
                <p class="mb-2 text-center fw-bold h5 text-uppercase" style="color: purple;">
                    <span class="underline">Add Topics</span>
                </p>

                <form id="add_topics_form" method="post">
                    @csrf
                    <input type="hidden" name="user_id" value="{{ Auth::user()->id }}">

                    <div class="mb-2">
                        <label class="form-label h6 fw-bold">Topic</label>
                        <textarea type="text" name="topic" class="form-control rounded-0" placeholder="Enter Topic" required></textarea>
                    </div>

                    <div class="mb-2">
                        <label class="form-label h6 fw-bold">Select Class</label>
                        <select name="classname" class="rounded-0 form-select" required>
                            @foreach ($classes as $class)
                                <option value="{{ $class->class }}">{{ $class->class }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="form-label h6 fw-bold">Select Subject</label>
                        <select name="subject" class="rounded-0 form-select" required>
                            @foreach ($subjects as $subject)
                                <option value="{{ $subject->name }}">{{ $subject->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="submit-btn fw-bold mt-3" id="add-topic-btn">Submit</button>
                </form>
            </div>

            <div class="col-md-7 mb-3">
                <p class="mb-2 text-center fw-bold h5 text-uppercase" style="color: purple;">
                    <span class="underline">Available Topics</span>
                </p>

                <div class="card">
                    <div class="card-body">
                        <div>
                            <form id="select_topics_form" method="POST">
                                @csrf
                                <input type="hidden" name="user_id" value="{{ Auth::user()->id }}">
                                <div class="mb-2">
                                    <label class="form-label h6 fw-bold">Select Class</label>
                                    <select name="classname" class="rounded-0 form-select" required>
                                        @foreach ($classes as $class)
                                            <option value="{{ $class->class }}">{{ $class->class }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="form-label h6 fw-bold">Select Subject</label>
                                    <select name="subject" class="rounded-0 form-select" required>
                                        @foreach ($subjects as $subject)
                                            <option value="{{ $subject->name }}">{{ $subject->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="d-flex align-items-center">
                                    <button type="submit"
                                        class="submit-btn-disabled select-topics-btn-disabled fw-bold mt-3 d-none"
                                        style="width: 100px !important; height:44px !important;" disabled>
                                        <div class="d-flex justify-content-center align-items-center">
                                            <div style="color:white;" class="spinner-border spinner-border-sm"
                                                role="status">
                                            </div>
                                        </div>
                                    </button>

                                    <button type="submit" class="submit-btn select-topics-btn fw-bold mt-3">
                                        select
                                    </button>
                                </div>
                            </form>
                        </div>
                        <div class="w-100 overflow-scroll mt-2 d-none topics-table-container">
                            <table class="w-100 table-responsive-lg table" id="topics-table">
                                <thead class="bg-dark">
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Class</th>
                                        <th scope="col">Subject</th>
                                        <th scope="col">Topic</th>
                                        @if (Auth::user()->is_super_admin == 1 || Auth::user()->is_admin == 1)
                                            <th scope="col">Entered By</th>
                                        @endif
                                        <th class="text-center" scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
@endsection

@push('body-scripts')
    <script src="{{ asset('') }}js/jquery.min.js"></script>
    <script src="{{ asset('') }}js/datatable.min.js"></script>
    <script src="{{ asset('') }}js/bootstrap.bundle.js"></script>
    <script>
        //Fetch the topics
        $("#select_topics_form").on('submit', function(e) {
            $(".select-topics-btn-disabled").removeClass('d-none');
            $(".select-topics-btn").addClass('d-none');

            //Empty the table
            $("#topics-table tbody").empty();

            e.preventDefault();
            $.ajax({
                type: 'POST',
                url: "{{ route('topics.fetch') }}",
                data: new FormData(this),
                processData: false,
                cache: false,
                contentType: false,
                success: function(response) {
                    $(".topics-table-container").removeClass('d-none');
                    var counter = 0;
                    var data;
                    if (response.length != 0) {
                        $.each(response, function(k, v) {
                            data = '<tr>\
                                        <td>' + (counter += 1) + '</td>\
                                        <td style="width:100px;"><span class="badge purple-badge">' + (v.class) + '</span></td>\
                                        <td>' + (v.subject) + '</td>\
                                        <td>' + (v.topic) + '</td>\
                                        @if (Auth::user()->is_super_admin == 1 || Auth::user()->is_admin == 1)\
                                        <td>' + (v.username) + '</td>\
                                        @endif\
                                        <td class="text-center" style="text-align: center;">\
                                            <form class="edit_topic_form mb-1" method="post">\
                                                @csrf\
                                                <input type="hidden" name="topic_id" value="' + (v.id) + '">\
                                                <button type="submit" data-bs-toggle="tooltip" data-bs-placement="left" title="Edit" class="btn btn-sm btn-outline-success rounded-5"><i class="fa fa-pen"></i></button>\
                                            </form>\
                                            <form class="delete_topic_form" method="post">\
                                                @csrf\
                                                <input type="hidden" name="classname" value="' + (v.class) + '">\
                                                <input type="hidden" name="subject" value="' + (v.subject) + '">\
                                                <input type="hidden" name="topic" value="' + (v.topic) + '">\
                                                <button type="submit" data-bs-toggle="tooltip" data-bs-placement="left" title="Delete" class="btn btn-sm btn-outline-danger rounded-5"><i class="fa fa-trash"></i></button>\
                                            </form>\
                                        </td>\
                                    </tr>';
                            //Add to the table
                            $("#topics-table tbody").append(data);
                        });
                    } else {
                        data = "<tr>\
                                <td class='text-center' style='color:red; font-weight:bold;' colspan=6><img class='img-fluid w-25' src='{{ asset('/images/icon/empty_set.png') }}'/></td>\
                                </tr>";
                        //Add to the table
                        $("#topics-table tbody").append(data);
                    }

                    $(".select-topics-btn-disabled").addClass('d-none');
                    $(".select-topics-btn").removeClass('d-none');
                },
                error: function() {
                    alert("Failed to Fetch topics!");
                }
            });
        });

        //Add Topics
        $("#add_topics_form").on('submit', function(e) {
            e.preventDefault();
            $(".topics-table-container").addClass('d-none');
            $.ajax({
                type: 'POST',
                url: "{{ route('topics.add') }}",
                data: new FormData(this),
                processData: false,
                cache: false,
                contentType: false,
                success: function(response) {
                    alert(response);
                    //location.reload();
                    $("#add_topics_form")[0].reset();
                    $("#select_topics_form")[0].reset();
                },
                error: function() {
                    alert("Failed to Add topic!");
                }
            });
        });

        //Delete Topics
        $(document).on('submit', ".delete_topic_form", function(e) {
            e.preventDefault();
            const confirm_delete = confirm("Are you sure?");

            if (confirm_delete == true) {
                $.ajax({
                    type: 'POST',
                    url: "{{ route('topics.delete') }}",
                    data: new FormData(this),
                    processData: false,
                    cache: false,
                    contentType: false,
                    success: function(response) {
                        alert(response);
                        //location.reload();
                        $(".topics-table-container").addClass('d-none');
                        $("form")[0].reset();
                    },
                    error: function() {
                        alert("Failed to Delete topic!");
                    }
                });
            }
        });

        //Edit Topics
        $(document).on('submit', ".edit_topic_form", function(e) {
            e.preventDefault();
            $("#updateTopicModal").modal('show');
            $.ajax({
                type: 'POST',
                url: "{{ route('topics.edit') }}",
                data: new FormData(this),
                processData: false,
                cache: false,
                contentType: false,
                success: function(data) {
                    $.each(data, function(k, v) {
                        var id = v.id;
                        var topic = v.topic;
                        var subject = v.subject;
                        var classname = v.class;

                        $("input[name='update_topic_id']").val(id);
                        $("select[name='update_subject']").val(subject);
                        $("select[name='update_class']").val(classname);
                        $("textarea[name='update_topic']").val(topic);
                    });

                    $(".spinner-container").addClass('d-none');
                    $(".form-container").removeClass('d-none');
                },
                error: function() {
                    alert("Failed to Fetch Data!");
                }
            });
        });

        //Update the modal
        $(document).on('submit', ".update_topic_form", function(e) {
            e.preventDefault();
            $("#updateTopicModal").modal('hide');
            $.ajax({
                type: 'POST',
                url: "{{ route('topics.update') }}",
                data: new FormData(this),
                processData: false,
                cache: false,
                contentType: false,
                success: function(response) {
                    alert(response);
                    //location.reload();
                    $(".topics-table-container").addClass('d-none');
                    $("form")[0].reset();
                },
                error: function() {
                    alert("Failed to Update Data!");
                }
            });
        })

        $(document).ready(function() {
            //Reset the modal when hidden
            $("#updateTopicModal").on('hidden.bs.modal', function() {
                $(".spinner-container").removeClass('d-none');
                $(".form-container").addClass('d-none');
            })
        })
    </script>
@endpush
