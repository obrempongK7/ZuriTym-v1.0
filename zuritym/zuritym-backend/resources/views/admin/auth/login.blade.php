<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>ZuriTym Admin Login</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:'Inter',sans-serif;background:linear-gradient(135deg,#1a1a2e 0%,#16213e 50%,#0f3460 100%);min-height:100vh;display:flex;align-items:center;justify-content:center}
.login-card{background:#fff;border-radius:20px;padding:42px;width:420px;box-shadow:0 20px 60px rgba(0,0,0,.4)}
.login-logo{text-align:center;margin-bottom:32px}
.login-logo .icon{width:60px;height:60px;background:linear-gradient(135deg,#6C63FF,#a29bfe);border-radius:14px;display:inline-flex;align-items:center;justify-content:center;font-size:1.6rem;color:#fff;margin-bottom:14px}
.login-logo h1{font-size:1.5rem;font-weight:700;color:#1a1a2e}
.login-logo p{color:#888;font-size:.88rem;margin-top:4px}
.form-group{margin-bottom:18px}
.form-label{display:block;font-size:.85rem;font-weight:500;color:#555;margin-bottom:6px}
.form-control{width:100%;padding:11px 14px;border:1.5px solid #e0e0e0;border-radius:10px;font-size:.9rem;outline:none;transition:.2s}
.form-control:focus{border-color:#6C63FF;box-shadow:0 0 0 3px rgba(108,99,255,.12)}
.btn-login{width:100%;padding:13px;background:linear-gradient(135deg,#6C63FF,#a29bfe);color:#fff;border:none;border-radius:10px;font-size:.95rem;font-weight:600;cursor:pointer;transition:.2s}
.btn-login:hover{transform:translateY(-1px);box-shadow:0 8px 20px rgba(108,99,255,.4)}
.alert{padding:11px 16px;border-radius:8px;margin-bottom:16px;font-size:.85rem;background:#fde8e8;color:#9b1c1c;border-left:4px solid #e74c3c}
</style>
</head>
<body>
<div class="login-card">
    <div class="login-logo">
        <div class="icon">Z</div>
        <h1>ZuriTym Admin</h1>
        <p>Sign in to manage your app</p>
    </div>
    @if($errors->any())<div class="alert"><i class="fas fa-exclamation-circle"></i> {{ $errors->first() }}</div>@endif
    @if(session('error'))<div class="alert">{{ session('error') }}</div>@endif
    <form action="{{ route('admin.login.post') }}" method="POST">
        @csrf
        <div class="form-group"><label class="form-label">Email Address</label><input type="email" name="email" class="form-control" placeholder="admin@zuritym.com" value="{{ old('email') }}" required></div>
        <div class="form-group"><label class="form-label">Password</label><input type="password" name="password" class="form-control" placeholder="••••••••" required></div>
        <button type="submit" class="btn-login"><i class="fas fa-sign-in-alt"></i> Sign In</button>
    </form>
</div>
</body>
</html>