<?php
    session_start();

    $errors = [
        'login' => $_SESSION['login_error'] ?? '',
        'register' => $_SESSION['register_error'] ?? ''
    ];

    $activeForm = $_SESSION['active_form'] ?? 'login';

    session_unset();

    function showError($error) {
        return !empty($error) ? "<p class='error-message'>$error</p>": '';
    }

    function isActiveForm($formName, $activeForm){
        return $formName === $activeForm ? 'active' : '';
    }

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lost and Found Management Systems</title>
    <link rel="stylesheet" href="allCss/index.css?v=<?php  echo time(); ?>">
    <link rel="icon" type="image/jpg" href="stylingPhotos/icons/lostAndFound_icon.jpg">
</head>
<body>
    <div class="bigCont container">
            <div class="box active <?= isActiveForm('login', $activeForm); ?>" id="login" >
                <form action="login_or_Reg.php" method="post">
                    <h2>Login</h2>
                    <?= showError($errors['login']); ?>
                    <input type="text" name="username" placeholder="Username" required> <br>
                    <input type="password" name="password" placeholder="Password" required> <br>
                    <button type="submit" name="login">Log-in</button>
                    Don't have an account yet? <a onclick="showThis('register')">Sign-up</a>
                </form>
            </div>
            <div class="box <?= isActiveForm('register', $activeForm); ?>" id="register">
                <form action="login_or_Reg.php" method="post">
                    <h2>Sign-up</h2>
                    <?= showError($errors['register']); ?>
                    <input type="text" name="fName" placeholder="First Name" name="fName" required> <br>
                    <input type="text" name="lName" placeholder="Last Name" required> <br>
                    <input type="text" name="email" placeholder="Email" required> <br>
                    <input type="text" name="username" placeholder="Chosen Username" required> <br>
                    <input type="text" name="password" placeholder="Chosen Password" required> <br>
                    <input type="text" name="secondPassword" placeholder="Chosen Password Again" required> <br>
                    <button type="submit" name="register">Sign-up</button>
                    Already have an account yet? <a onclick="showThis('login')">Log-in</a>
                </form>
            </div>
    </div>
    <script src="allJavascripts/lostAndFound.js"></script>
</body>
</html>