<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>OACMIS - Forgot Password</title>
    <link rel="stylesheet" href="{{ asset('/') }}css/bootstrap.css">
    <link rel="stylesheet" href="{{ asset('/') }}css/custom.css">
    <link rel="stylesheet" href="{{ asset('/') }}css/responsive.css">
    <link rel="stylesheet" href="{{ asset('/') }}css/bootstrap-icons/bootstrap-icons.css">
</head>

<body class="login-body">

    <div class="login-container">
        <p class="mb-0 text-center h2 fw-bold text-white align-items-center">OACMIS</p>
        <form method="post" id="forgot-pass-form">
            @csrf
            <div>
                <label for="" class="form-label fw-bold">Email</label>
                <input type="text" name="email" class="form-control rounded-0" id="email">
                <div class="email-errors bg-danger text-white rounded-1 px-2 py-2 bg-gradient my-2 d-none"></div>
            </div>

            <div class="mt-2 d-flex justify-content-between">
                <a href="" class="nav-link text-warning">Forgot Password <i class="bi bi-key"></i></a>
                <a href="{{ route('login') }}" class="nav-link text-warning">Login</a>
            </div>

            <div>
                <button class="btn mt-2 mb-3 w-100 bg-gradient submit-btn rounded-0" type="submit" id="login-btn"
                disabled>Submit</button>
            </div>{{-- button containers --}}
            <button class="btn w-100 mt-2 mb-3 rounded-0 d-none loading-btn py-2" style="background:purple; color:white;" type="button">
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                Pleasewait...
            </button>
            

        </form>
    </div>

    <script src="{{ asset('/') }}js/jquery.min.js"></script>
    <script src="{{ asset('') }}js/bootstrap.bundle.js"></script>
    <script>
        $(document).ready(function() {
            $("#login-btn").removeClass('submit-btn').addClass('submit-btn-disabled');

            $("#email, #password").on('change', function() {
                $("#login-btn").addClass('submit-btn').removeClass('submit-btn-disabled').prop('disabled',false);
            });

            function login_btn_active(){
                $(".loading-btn").addClass('d-none');
                $("#login-btn").removeClass('d-none');
            }

            function login_btn_inactive(){
                $(".loading-btn").removeClass('d-none');
                $("#login-btn").addClass('d-none');
            }

            $("#forgot-pass-form").on('submit', function(e){
                e.preventDefault();

                login_btn_inactive();
                
                $.ajax({
                    type:"POST",
                    url:"{{ route('forgotpass.send.email') }}",
                    data:new FormData(this),
                    contentType:false,
                    processData:false,
                    cache:false,
                    success:function(response){
                        alert(response);                        
                        login_btn_active();
                        //Change to the login route
                        setTimeout(function(){
                            location.replace("{{ route('login') }}");
                        },1000);
                    },
                    error:function(){
                        alert("Failed to Change password!");
                        login_btn_active();
                    }
                });
                
            });
            
        })
    </script>
</body>

</html>
