@extends('common.header')

@section('title')
    Term Planner
@endsection

@section('body')
    <style>
        label,
        p {
            margin-top: 10px;
            font-weight: bold;
            font-size: 14px;
        }
    </style>
    <div class="container-fluid">
        <h5 class="mb-0 text-uppercase fw-bold text-center mb-3" style="color: purple;">Topics</h5>

        <div class="row justify-content-between">
            <div class="col-md-4 mb-5 shadow-lg py-4 px-3">
                <p class="mb-2 text-center fw-bold h5 text-uppercase" style="color: purple;">
                    <span class="underline">Add Term Activity</span>
                </p>

                <form id="add_planner_form" method="post">
                    @csrf
                    <input type="hidden" name="user_id" value="{{ Auth::user()->id }}">
                    
                    <div class="mb-2">
                        <label class="form-label h6 fw-bold">Activity</label>
                        <textarea name="activity" class="form-control rounded-0" placeholder="Add Activity" required></textarea>
                    </div>

                    <div>
                        <label class="form-label h6 fw-bold">Date - Time</label>
                        <input type="text" name="activity_date" class="form-control rounded-0" placeholder="XX-XXX-XXXX XX:XX" required>
                    </div>

                    <button type="submit" class="submit-btn fw-bold mt-3" id="add-topic-btn">Submit</button>
                </form>
            </div>

            <div class="col-md-7 mb-3">
                <p class="mb-2 text-center fw-bold h5 text-uppercase" style="color: purple;">
                    <span class="underline">Termly Activities</span>
                </p>

                <div class="card">
                    <div class="card-body">
                        <div class="w-100 overflow-scroll mt-2">
                            <table class="w-100 table-responsive-lg table" id="topics-table">
                                <thead class="bg-dark">
                                    <tr class="text-white">
                                        <th scope="col">#</th>
                                        <th scope="col">Activity</th>
                                        <th scope="col">Date - Time</th>
                                        <th class="text-center" scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $counter = 1;
                                    @endphp
                                    @foreach ($planner  as $p)
                                        <tr style="vertical-align: middle;">
                                            <td>{{ $counter++ }}</td>
                                            <td>{{ $p->activity }}</td>
                                            <td class="align-items-center"><span>{{ date('D, d M, Y',strtotime($p->date)) }}</span> <span class="badge purple-badge">{{ date('h:i A',strtotime($p->date)) }}</span></td>
                                            <td class="text-center">
                                                <form class="delete_activity" method="post">
                                                    @csrf
                                                    <input type="hidden" name="activity_id" value="{{ $p->id }}">
                                                    <button data-bs-toggle="tooltip" data-bs-placement="left" title="Delete" class="btn btn-sm btn-outline-danger rounded-5"><i class="fa fa-trash"></i></button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                    
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
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{ asset('js/flatpickr.js') }}"></script>
    <script>
        $("input[name='activity_date']").flatpickr({
            enableTime: true,
            dateFormat: "d-M-Y H:i",
            minDate:"today"
        });

        //Add planner
        $("#add_planner_form").on('submit',function(e){
            e.preventDefault();
            $.ajax({
                type:"POST",
                url:'{{ route("planner.add") }}',
                data:new FormData(this),
                processData:false,
                contentType:false,
                cache:false,
                success:function(response){
                    alert(response);
                    location.reload()
                },
                error:function(){
                    alert("Failed to add activity!");
                }
            })
        });

        //Delete Planner
        $(".delete_activity").on('submit',function(e){
            e.preventDefault();
            $.ajax({
                type:"POST",
                url:'{{ route("planner.delete") }}',
                data:new FormData(this),
                processData:false,
                contentType:false,
                cache:false,
                success:function(response){
                    alert(response);
                    location.reload()
                },
                error:function(){
                    alert("Failed to add activity!");
                }
            })
        });

    </script>
@endpush
