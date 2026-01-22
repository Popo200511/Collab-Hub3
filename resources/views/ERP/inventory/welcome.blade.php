<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!--=============== REMIXICONS ===============-->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">

    <!--=============== Google Fonts ===============-->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <title>ยินต้อนรับเข้าสู่ระบบ</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Poppins", sans-serif;
        }
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: url('https://cdn.pixabay.com/photo/2020/10/01/17/11/store-5619201_1280.jpg') no-repeat center center;
            background-size: cover;
        }
        .wrapper {
            height: 30rem;
            width: 420px;
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid #fff;
            backdrop-filter: blur(9px);
            border-radius: 12px;
            padding: 30px 40px;
            color: #fff;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }
        .wrapper h1 {
            font-weight: 400;
            font-size: 36px;
            text-align: center;
            margin-bottom: 20px;
        }
        .input-box {
            position: relative;
            width: 100%;
            height: 50px;
            margin: 30px 0;
        }
        .input-box input {
            width: 100%;
            height: 100%;
            background: transparent;
            border: 2px solid rgba(255, 255, 255, 0.2);
            border-radius: 40px;
            outline: none;
            font-size: 16px;
            color: #fff;
            padding: 20px 45px 20px 20px;
        }
        .input-box input::placeholder {
            color: #fff;
        }
        .input-box i {
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 20px;
            color: rgb(0, 0, 0);
            cursor: pointer;
        }
        .remember-forgot {
            display: flex;
            justify-content: space-between;
            font-size: 14px;
            margin: 10px 0;
        }
        .remember-forgot label input {
            accent-color: #fff;
            margin-right: 5px;
        }
        .remember-forgot a {
            color: #e5c2ff;
            text-decoration: none;
        }
        .remember-forgot a:hover {
            text-decoration: underline;
        }
        .btn {
            width: 100%;
            height: 45px;
            background: #fff;
            border: none;
            border-radius: 40px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            color: #333;
        }
        .register-link {
            text-align: center;
            margin-top: 20px;
        }
        .register-link p a {
            color: #e5c2ff;
            font-weight: 600;
            text-decoration: none;
        }
        .register-link p a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <h1>Login</h1>
        <form action="{{ route('login') }}" method="POST">
            @csrf
            <div class="input-box">
                <input type="email" name="email" required placeholder="Email">
                <i class="ri-user-3-line"></i>
                @error('email')
                <span style="color: red; font-size: 0.875rem;">{{ $message }}</span>
                @enderror
            </div>
            <div class="input-box">
                <input type="password" name="password" required placeholder="Password" id="password">
                <i class="ri-eye-off-line" id="togglePassword"></i>
                @error('password')
                <span style="color: red; font-size: 0.875rem;">{{ $message }}</span>
                @enderror
            </div>
            <div class="remember-forgot">
                <label>
                    <input type="checkbox" name="remember">
                    Remember me
                </label>
            </div>
            <button type="submit" class="btn">Login</button>
            <div class="register-link">
                <p>Don't have an account? <a href="{{ route('register') }}">Register</a></p>
            </div>

            <div class="register-link">
                <p><a href="{{ route('register') }}">หน้าแรก</a></p>
            </div>
        </form>
    </div>

    <script>
        // Toggle password visibility
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');

        togglePassword.addEventListener('click', () => {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            togglePassword.classList.toggle('ri-eye-off-line');
            togglePassword.classList.toggle('ri-eye-line');
        });
    </script>
</body>
</html>
