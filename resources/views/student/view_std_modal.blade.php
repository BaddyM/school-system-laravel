<style>
    select, input, textarea{
        /*width: 100% !important;*/
    }
    label{
        font-size: 15px;
        margin-bottom: 0 !important;
        width: 100px;
        text-align: left;
        font-weight: bold;
    }
    .confirm-modal {
        background: linear-gradient(1.3deg, rgb(91, 117, 163) 11.4%, rgb(68, 98, 128) 77%);
    }

    .yes-button {
        background: purple;
    }

    .no-button {
        background: red;
    }

    .yes-button,
    .no-button {
        color: white !important;
        transition: .3s all ease-in-out;
    }

    .yes-button:hover,
    .no-button:hover {
        background: green;
    }

    #std_image {
        width: 200px;
        height: 200px;
        object-fit: contain;
        cursor: pointer;
    }

    #viewStudentModal input,
    #viewStudentModal select {
        width: 300px;
        border-radius: 2px;
        padding: 5px;
        border: none !important;
    }

    #viewStudentModal input:focus,
    #viewStudentModal input:focus-visible {
        border: none !important;
    }

    #viewStudentModal div {
        font-size: 15px;
    }

    .inner-elements div {
        display: flex;
        justify-content: space-between;
        gap: 10px;
        align-items: center;
    }
</style> 
<div class="modal fade" id="viewStudentModal" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content confirm-modal">
            <div class="modal-header d-block justify-content-between text-white align-items-center">
                <div class="d-flex justify-content-end">
                    <button class="close-std-view btn btn-danger bg-gradient rounded-5"><i class="fa fa-x"></i></button>
                </div>
                <div class="row align-items-center justify-content-between w-100">
                    <p class="col-md-4"><label class="form-label">Full Name :</label> <input class="w-100" type="text" id="std_full_name" disabled style="color:black;"></p>
                    <p class="col-md-3"><label class="form-label">Gender :</label>
                        <select id="std_gender" class="w-100">
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                    </p>
                    <p class="col-md-4"><label class="form-label">Student ID :</label> <input class="w-100" type="text" id="std_id" disabled style="color:black;"></p>
                </div>
            </div>

            <div class="modal-body text-center">
                <div class="row justify-content-between w-100" style="gap:20px;">
                    <div class="col-md-2">
                        <p class="mb-2 text-uppercase fw-bold" style="color:white; font-size:18px;">Student Image
                        </p>
                        <img src="" alt="" id="std_image">
                    </div>{{-- Student Image here --}}

                    <div class="col-md-9 d-flex overflow-scroll" style="gap:30px;">
                        <div class="inner-elements">
                            <div class="mb-2 align-items-center"><label class="form-label">First Name :</label> <input type="text" id="std_fname"></div>
                            <div class="mb-2"><label class="form-label">Class :</label>
                                <select id="std_class" class="text-dark">
                                    @foreach ($classes as $class)
                                        <option value="{{ $class->class }}">{{ $class->class }}</option>
                                    @endforeach
                                </select>{{-- Select Student Stream --}}
                            </div>
                            <div class="mb-2"><label class="form-label">Section :</label>
                                <select name="" id="std_section">
                                    @php
                                        $sections = ['Day', 'Boarding'];
                                    @endphp
                                    @foreach ($sections as $section)
                                        <option value="{{ $section }}">{{ $section }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-2"><label class="form-label">Stream :</label>
                                <select id="std_stream" class="text-dark">
                                    @foreach ($streams as $stream)
                                        <option value="{{ $stream->stream }}">{{ $stream->stream }}</option>
                                    @endforeach
                                </select>{{-- Select Student Stream --}}
                            </div>
                            <div class="mb-2"><label class="form-label">House :</label> <input type="text" id="std_house"></div>
                            <div class="mb-2"><label class="form-label">Status :</label>
                                <select id="std_status" class="text-dark">
                                    @foreach ($status as $s)
                                        <option value="{{ $s->status }}">{{ $s->status }}</option>
                                    @endforeach
                                </select>{{-- Select Student Status --}}
                            </div>
                            <div class="mb-2"><label class="form-label">Nationality :</label> <input type="text" id="nationality"></div>
                            <div class="mb-2"><label class="form-label">Created On :</label> <input type="text" id="created_at"></div>
                        </div>
                        <div class="inner-elements">
                            <div class="mb-2"><label class="form-label">Last Name :</label> <input type="text" id="std_lname"></div>
                            <div class="mb-2"><label class="form-label">Middle Name :</label> <input type="text" id="std_mname"></div>
                            <div class="mb-2"><label class="form-label">LIN :</label> <input type="text" id="lin"></div>
                            <div class="mb-2"><label class="form-label">Password :</label> <input type="text" id="std_pass"></div>
                            <div class="mb-2"><label class="form-label">Combination :</label> <input type="text" id="std_comb"></div>
                            <div class="mb-2"><label class="form-label">Year of Entry :</label> <input type="text" id="std_year"></div>
                            <div class="mb-2"><label class="form-label">Total Fees :</label> <input type="text" id="std_total_fees"></div>
                            <div class="mb-2"><label class="form-label">Updated On :</label> <input type="text" id="updated_at"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer text-center justify-content-center mb-0">
                <div class="w-100 text-center d-flex justify-content-center button_cont" style="gap:30px;">
                    <button class="btn yes-button px-5 py-2 bg-gradient" id="update-std">
                        UPDATE
                    </button>
                    <button class="btn no-button px-5 py-2 bg-gradient" id="disable-std" data-std_status=''>
                        DISABLE
                    </button>
                    <input type="hidden" id="std_id_buffer">
                </div>
            </div>
        </div>
    </div>
</div>
