<style>
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
<div class="modal fade" id="viewStudentModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content confirm-modal">
            <div class="modal-header justify-content-between text-white align-items-center">
                <p class="mb-0">Full Name : <input type="text" id="std_full_name" disabled style="color:black;"></p>
                <p class="mb-0">Gender :
                    <select id="std_gender">
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                    </select>
                </p>
                <p class="mb-0">ID : <input type="text" id="std_id" disabled style="color:black;"></p>
            </div>

            <div class="modal-body text-center">
                <div class="d-flex" style="gap:20px;">
                    <div>
                        <p class="mb-2 text-uppercase fw-bold" style="color:white; font-size:18px;">Student Image
                        </p>
                        <img src="" alt="" id="std_image">
                    </div>{{-- Student Image here --}}

                    <div class="d-flex" style="gap:30px;">
                        <div class="inner-elements">
                            <div class="mb-2">First Name : <input type="text" id="std_fname"></div>
                            <div class="mb-2">Class :
                                <select id="std_class" class="text-dark">
                                    @foreach ($classes as $class)
                                        <option value="{{ $class->class }}">{{ $class->class }}</option>
                                    @endforeach
                                </select>{{-- Select Student Stream --}}
                            </div>
                            <div class="mb-2">Section :
                                <select name="" id="std_section">
                                    @php
                                        $sections = ['Day', 'Boarding'];
                                    @endphp
                                    @foreach ($sections as $section)
                                        <option value="{{ $section }}">{{ $section }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-2">Stream :
                                <select id="std_stream" class="text-dark">
                                    @foreach ($streams as $stream)
                                        <option value="{{ $stream->stream }}">{{ $stream->stream }}</option>
                                    @endforeach
                                </select>{{-- Select Student Stream --}}
                            </div>
                            <div class="mb-2">House : <input type="text" id="std_house"></div>
                            <div class="mb-2">Status :
                                <select id="std_status" class="text-dark">
                                    @foreach ($status as $s)
                                        <option value="{{ $s->status }}">{{ $s->status }}</option>
                                    @endforeach
                                </select>{{-- Select Student Status --}}
                            </div>
                            <div class="mb-2">Nationality : <input type="text" id="nationality"></div>
                            <div class="mb-2">Created On : <input type="text" id="created_at"></div>
                        </div>
                        <div class="inner-elements">
                            <div class="mb-2">Last Name : <input type="text" id="std_lname"></div>
                            <div class="mb-2">Middle Name : <input type="text" id="std_mname"></div>
                            <div class="mb-2">LIN : <input type="text" id="lin"></div>
                            <div class="mb-2">Password : <input type="text" id="std_pass"></div>
                            <div class="mb-2">Combination : <input type="text" id="std_comb"></div>
                            <div class="mb-2">Year of Entry : <input type="text" id="std_year"></div>
                            <div class="mb-2">Total Fees : <input type="text" id="std_total_fees"></div>
                            <div class="mb-2">Updated On : <input type="text" id="updated_at"></div>
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
