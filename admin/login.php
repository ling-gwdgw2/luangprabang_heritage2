<?php
session_start();
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ເຂົ້າສູ່ລະບົບຜູ້ດູແລ | Admin Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@100..900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        * { font-family: 'Noto Sans Lao', 'Phetsarath OT', sans-serif; }
        body { background: linear-gradient(135deg, #1a472a, #2d6a4f); min-height: 100vh; display: flex; align-items: center; }
        .login-card { background: white; border-radius: 25px; box-shadow: 0 25px 50px rgba(0,0,0,0.2); padding: 40px; transition: transform 0.3s; width: 100%; max-width: 450px; margin: auto; }
        .login-card:hover { transform: translateY(-5px); }
        .login-header { text-align: center; margin-bottom: 30px; }
        .login-header i { font-size: 60px; color: #2d6a4f; }
        .btn-login { background: linear-gradient(135deg, #2d6a4f, #1a472a); color: white; border-radius: 50px; padding: 12px; font-weight: bold; width: 100%; border: none; transition: all 0.3s; }
        .btn-login:hover { transform: scale(1.02); background: linear-gradient(135deg, #1a472a, #0d2b1f); color: white; }
        .form-control { border-radius: 50px; padding: 12px 20px; }
        .form-label { font-weight: bold; color: #2d6a4f; }
        
        /* Password toggle styles */
        .password-wrapper {
            position: relative;
        }
        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #6c757d;
            background: transparent;
            border: none;
            font-size: 1.1rem;
            z-index: 10;
        }
        .password-toggle:hover {
            color: #2d6a4f;
        }
        .form-control.password-input {
            padding-right: 45px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="login-card">
                    <div class="login-header">
                        <i class="fas fa-landmark"></i>
                        <h2 class="mt-2">ລະບົບຄຸ້ມຄອງ</h2>
                        <p class="text-muted">ມໍລະດົກຫຼວງພະບາງ</p>
                    </div>
                    
                    <form id="loginForm">
                        <div class="mb-3">
                            <label class="form-label"><i class="fas fa-user"></i> ຊື່ຜູ້ໃຊ້ / Username</label>
                            <input type="text" id="username" class="form-control" placeholder="ຕົວຢ່າງ admin123" required autofocus>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label"><i class="fas fa-lock"></i> ລະຫັດຜ່ານ / Password</label>
                            <div class="password-wrapper">
                                <input type="password" id="password" class="form-control password-input" placeholder="••••••" required>
                                <button type="button" class="password-toggle" id="togglePassword">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn-login">
                            <i class="fas fa-sign-in-alt"></i> ເຂົ້າສູ່ລະບົບ
                        </button>
                    </form>
                    
                    <div class="text-center mt-3">
                        <small class="text-muted">ກະລຸນາປ້ອນລະຫັດກ່ອນເຂົ້າໃຊ້</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Toggle password visibility
        $('#togglePassword').on('click', function() {
            const passwordInput = $('#password');
            const icon = $(this).find('i');
            
            if (passwordInput.attr('type') === 'password') {
                passwordInput.attr('type', 'text');
                icon.removeClass('fa-eye').addClass('fa-eye-slash');
            } else {
                passwordInput.attr('type', 'password');
                icon.removeClass('fa-eye-slash').addClass('fa-eye');
            }
        });

        $('#loginForm').on('submit', function(e) {
            e.preventDefault();
            const username = $('#username').val();
            const password = $('#password').val();
            
            if (!username || !password) {
                Swal.fire({ icon: 'warning', title: 'ກະລຸນາປ້ອນຂໍ້ມູນ', text: 'ກະລຸນາປ້ອນຊື່ຜູ້ໃຊ້ ແລະ ລະຫັດຜ່ານ' });
                return;
            }
            
            Swal.fire({ title: 'ກຳລັງເຂົ້າສູ່ລະບົບ...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });
            
            $.ajax({
                url: '../api/admin_login.php',
                method: 'POST',
                data: { username: username, password: password },
                dataType: 'json',
                success: function(response) {
                    Swal.close();
                    if (response.success) {
                        Swal.fire({ icon: 'success', title: 'ສຳເລັດ!', text: response.message, timer: 1500, showConfirmButton: false })
                            .then(() => { window.location.href = 'dashboard.php'; });
                    } else {
                        Swal.fire({ icon: 'error', title: 'ເຂົ້າສູ່ລະບົບບໍ່ສຳເລັດ', text: response.message });
                    }
                },
                error: function() {
                    Swal.close();
                    Swal.fire({ icon: 'error', title: 'ຜິດພາດ', text: 'ບໍ່ສາມາດເຊື່ອມຕໍ່ກັບເຊີບເວີໄດ້' });
                }
            });
        });
    </script>
</body>
</html>