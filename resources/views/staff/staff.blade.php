@extends('common.header')

@section('title')
    Staff
@endsection

@section('body')

<style>
    .table thead{
        background:brown;
        color:white;
    }
    .table tbody{
        background:rgb(229, 191, 191);
        color:black;
    }
</style>

<div>
    <h5 class="text-center text-uppercase fw-bold h3 mb-3">Staff Details</h5>

    <div class="overflow-scroll p-2">
        <table class="table table-active" id="staff-table">
            <thead class="bg-gradient">
                <th scope="col">No.</th>
                <th scope="col">StaffID</th>
                <th scope="col">FirstName</th>
                <th scope="col">LastName</th>
                <th scope="col">Position</th>
                <th scope="col">Gender</th>
                <th scope="col">Location</th>
                <th scope="col">Status</th>
                <th scope="col">Class</th>
                <th scope="col">Entered</th>
                <th scope="col">Updated</th>
                <th scope="col">Action</th>
            </thead>
            <tbody>

            </tbody>
        </table>
    </div>

</div>
@include('staff.staff-details-modal')

@endsection

@push('body-scripts')
@include('common.scripts')
<script>
    $(document).ready(function(){
        var staff_table = $("#staff-table").DataTable({
        serverSide: true,
        processing: true,
        autoWidth: false,
        searchable: true,
        stateSave: true,
        ajax: {
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: "POST",
            url: "{{ route('staff.data') }}"
        },
        columns:[
            {data:'DT_RowIndex'},
            {
                data:'staffid'
            },
            {
                data:'FName'
            },
            {
                data:'LName'
            },
            {
                data:'position'
            },
            {
                data:'gender'
            },
            {
                data:'location'
            },
            {
                data:'status'
            },
            {
                data:'Class'
            },
            {
                data:'created_at'
            },

            {
                data:'updated_at'
            },
            {
                data:'action',
                render:function(data,type,row){
                    return '<button class="action-btn btn btn-warning bg-gradient btn-sm" value="'+row.id+'">Action</button>'
                }
            }
        ],
        columnDefs:[
            {
                target:[9,10],
                className:'dt-center',
                width:'120px'
            }
        ]
    });

        //Autoupdate table
        $(window).focus(function(){
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                url:"{{ route('staff.data') }}",
                success:function(){
                    console.log("Auto update success")
                    staff_table.draw()
                }
            });
        });

        //Clicking the action button
        $(window).on('load',function(){
            $("#staffModal").modal('show');
        })

        //Delete/Suspend/Continue/Dismiss
        $(".staff-action-btn").on("click",function(){
            var action_clicked = $(this).attr('id');
            console.log("Clicked = "+action_clicked);
        })
    });
</script>
@endpush