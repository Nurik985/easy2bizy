<!DOCTYPE html>
<html lang="ru">
<head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<title>Login Form</title>
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
	<style>
        * {
            padding: 0;
            margin: 0;
            box-sizing: border-box;
            font-family: "Roboto", sans-serif
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #23242a;
        }

        /* Main container styles */
        .box {
            position: relative;
            width: 370px;
            height: 450px;
            background: #1c1c1c;
            border-radius: 50px 5px;
            overflow: hidden;
        }

        /* Create animated gradient border effect */
        .box::before,
        .box::after {
            content: "";
            position: absolute;
            top: -50%;
            left: -50%;
            width: 370px;
            height: 450px;
            background: linear-gradient(60deg, transparent, #45f3ff, #45f3ff);
            transform-origin: bottom right;
            animation: animate 6s linear infinite;
        }

        .box::after {
            background: linear-gradient(60deg, transparent, #d9138a, #d9138a);
            animation-delay: -3s;
        }

        /* Keyframes for gradient animation */
        @keyframes animate {
            0% {
                transform: rotate(0deg);
            }
            100% {
                transform: rotate(360deg);
            }
        }

        /* Form container styles */
        form {
            position: absolute;
            inset: 2px;
            background: #28292d;
            border-radius: 50px 5px;
            z-index: 10;
            padding: 30px 30px;
            display: flex;
            flex-direction: column;
        }

        /* Title styles */
        .title {
            width: 100%;
        }

        .title h1 {
            color: #45f3ff;
            justify-content: center;
            font-size: 2rem;
            font-weight: 600;
            opacity: 0.9;
            margin-bottom: 20px;
        }

        /* Input box styles */
        form .input-box {
            width: 100%;
            margin-top: 20px;
        }

        form .input-box input {
            width: 100%;
            background-color: rgba(255, 255, 255, 0.2);
            border: none;
            outline: none;
            border-width: 3px;
            border-radius: 15px;
            padding: 10px 20px;
            font-size: 1rem;
            margin: 10px 0px 10px 0px;
            color: white;
        }

        form .input-box input::placeholder {
            color: #cdd1d2;
        }

        /* Submit button styles */
        form .input-box input[type="submit"] {
            background-color: #45f3ff;
            cursor: pointer;
            color: #16100e;
            filter: drop-shadow(0 5px 10px #45f3ff);
            margin-bottom: 20px;
        }

        /* Link text styles */
        form .link-text {
            padding-top: 15px;
            color: rgb(103, 173, 183);
            font-size: 0.85rem;
        }

        form .link-text a {
            text-decoration: none;
            color: rgb(153, 41, 99);
            font-weight: 700;
        }

        /* Label color */
        .label-color {
            color: #9eb3b5;
        }
        
        .login:hover {
            opacity: .8;
        }
	</style>
</head>
<body>
<div class="box">
	<form action="{{ route('login.auth') }}" method="post">
		@csrf
		<div class="title">
			<h1 style="text-align: center"><span style="color: #ff705d">Easy</span><span style="color: #fff">Bizy</span></h1>
			<p style="text-align: center; color: #fff; text-transform: uppercase">Сервис для автоматизации однотипных звонков <br>вашим клиентам</p>
		</div>
		<div class="input-box">
			<label for="login" class="label-color">Логин</label>
			<input id="login" name="login" type="text" required />
			<br />
			<label for="password" class="label-color">Пароль</label>
			<input id="password" name="password" type="password" required />
			<br />
			<br />
			<input type="submit" class="login" value="Войти" />
		</div>
	</form>
</div>
</body>
</html>
