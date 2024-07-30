<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <style>
        .toast {
            visibility: hidden;
            min-width: 250px;
            margin-left: -125px;
            text-align: center;
            border-radius: 2px;
            position: fixed;
            z-index: 1;
            left: 50%;
            bottom: 30px;
            font-size: 17px;
            white-space: nowrap;
            padding: 10px;
        }

        .toast.show {
            visibility: visible;
            -webkit-animation: fadein 0.5s, fadeout 0.5s 2.5s;
            animation: fadein 0.5s, fadeout 0.5s 2.5s;
        }

        @-webkit-keyframes fadein {
            from {bottom: 0; opacity: 0;} 
            to {bottom: 30px; opacity: 1;}
        }

        @keyframes fadein {
            from {bottom: 0; opacity: 0;}
            to {bottom: 30px; opacity: 1;}
        }

        @-webkit-keyframes fadeout {
            from {bottom: 30px; opacity: 1;} 
            to {bottom: 0; opacity: 0;}
        }

        @keyframes fadeout {
            from {bottom: 30px; opacity: 1;}
            to {bottom: 0; opacity: 0;}
        }

        .toast-success {
            background-color: #4CAF50;
            color: white;
        }

        .toast-error {
            background-color: #f44336;
            color: white;
        }
    </style>
</head>
<body>
    <div id="toast" class="toast"></div>

    <form id="loginForm">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required><br><br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br><br>
        <button type="submit">Login</button>
    </form>

    <script>
        function showToast(message, type) {
            var toast = document.getElementById("toast");
            toast.innerHTML = message;
            toast.className = "toast show toast-" + type;
            setTimeout(function() { toast.className = toast.className.replace("show", ""); }, 3000);
        }

        document.getElementById("loginForm").addEventListener("submit", function(event) {
            event.preventDefault();

            var formData = new FormData(this);

            fetch("../php/login_process.php", {
                method: "POST",
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast(data.message, "success");
                    // Redirect or perform other actions here
                } else {
                    showToast(data.message, "error");
                }
            })
            .catch(error => {
                console.error("Error:", error);
                showToast("An error occurred.", "error");
            });
        });
    </script>
</body>
</html>
