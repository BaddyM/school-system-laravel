<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>OACMIS - Signup</title>
    <link rel="stylesheet" href="{{ asset('/') }}css/bootstrap.css">
    <link rel="stylesheet" href="{{ asset('/') }}css/custom.css">
    <link rel="stylesheet" href="{{ asset('/') }}css/responsive.css">
    <link rel="stylesheet" href="{{ asset('/') }}css/bootstrap-icons/bootstrap-icons.css">
</head>

<body class="login-body">

    <div class="login-container">
        <p class="mb-0 text-center h2 fw-bold text-white align-items-center">OACMIS - SIGN UP <i class="bi bi-door-open"></i></p>
        
        <form method="post" id="signup-form">
            @csrf
            <div class="row">
                <div class="col-md-6 mb-2">
                    <label for="" class="form-label fw-bold">First Name</label>
                    <input type="text" name="fname" class="form-control rounded-0" id="fname" required>
                    <div class="email-errors bg-danger text-white rounded-1 px-2 py-2 bg-gradient my-2 d-none"></div>
                </div>
                <div class="col-md-6 mb-2">
                    <label for="" class="form-label fw-bold">Last Name</label>
                    <input type="text" name="lname" class="form-control rounded-0" id="lname" required>
                    <div class="email-errors bg-danger text-white rounded-1 px-2 py-2 bg-gradient my-2 d-none"></div>
                </div>
            </div>
            <div>
                <label for="" class="form-label fw-bold">Email</label>
                <input type="text" name="email" class="form-control rounded-0" id="email" required>
                <div class="email-errors bg-danger text-white rounded-1 px-2 py-2 bg-gradient my-2 d-none"></div>
            </div>

            <div class="mt-2">
                <label for="" class="form-label fw-bold">Password</label>
                <input type="password" name="password" class="form-control rounded-0" id="password" required>
                <div class="password-errors bg-danger text-white rounded-1 px-2 py-2 bg-gradient my-2 d-none">
                </div>
            </div>

            <div class="mt-2">
                <label for="" class="form-label fw-bold">Confirm Password</label>
                <input type="password" name="confirm-password" class="form-control rounded-0" id="confirm-password" required>
                <div class="password-errors bg-danger text-white rounded-1 px-2 py-2 bg-gradient my-2 d-none"> </div>
            </div>

            <div class="mt-2 d-flex justify-content-between">
                <a href="{{ route('forgotpass.index') }}" class="nav-link text-warning">Forgot Password <i class="bi bi-key"></i></a>
                <a href="{{ route('login') }}" class="nav-link text-warning">Login</a>
            </div>

            <!-- Recaptcha -->
            <!-- <div class="g-recaptcha" data-sitekey="6LcUpeopAAAAAKXF32dP1D_F_RZqEu_MJxVkEAvz"> </div>-->            

            <button class="btn mt-2 mb-3 w-100 bg-gradient submit-btn rounded-0" type="submit" id="login-btn" disabled>Register</button>
        </form>
    </div>

    <script src="{{ asset('/') }}js/jquery.min.js"></script>
    <!-- <script src="https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit" async defer> </script> -->
    <script src="{{ asset('') }}js/bootstrap.bundle.js"></script>
    <script>
        $(document).ready(function() {
            $("#login-btn").removeClass('submit-btn').addClass('submit-btn-disabled');

            $("#email, #password").on('change', function() {
                $("#login-btn").addClass('submit-btn').removeClass('submit-btn-disabled').prop('disabled',
                    false);
            });

            //Load the recaptcha
            function onloadCallback() {
                alert("grecaptcha is ready!");
            };

            //Add User
            $("#signup-form").on('submit',function(e){
                e.preventDefault();

                //Check if passwords match
                var pass1 = $("input[name='password']").val();
                var pass2 = $("input[name='confirm-password']").val();

                if(pass1 == pass2){
                    if(pass1.length < 6){
                        alert('Password Length is short!');
                    }else{
                        //Check if the user passed recaptcha                        
                        $.ajax({
                            type:"POST",
                            url:"{{ route('register.user') }}",
                            processData:false,
                            cache:false,
                            contentType:false,
                            data:new FormData(this),
                            success:function(response){
                                alert(response);
                                location.replace("{{ route('login') }}");
                            },
                            error:function(){
                                alert("SignUp Failed!");
                            }
                        });
                    }
                }else{
                    alert("Password Mismatch!");
                }
            });
        });
    </script>
</body>

</html>
