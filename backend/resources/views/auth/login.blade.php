<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Login — DCTech Shop</title>
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      font-family: 'Inter', system-ui, -apple-system, sans-serif;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      padding: 20px;
    }
    .login-card {
      background: white;
      border-radius: 16px;
      box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
      padding: 48px 40px;
      width: 100%;
      max-width: 420px;
    }
    .login-head {
      text-align: center;
      margin-bottom: 32px;
    }
    .login-logo {
      width: 56px;
      height: 56px;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      border-radius: 14px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-size: 24px;
      font-weight: 800;
      margin-bottom: 16px;
    }
    .login-title {
      font-size: 24px;
      font-weight: 700;
      color: #111827;
      margin-bottom: 6px;
    }
    .login-subtitle {
      font-size: 14px;
      color: #6b7280;
    }
    .form-group {
      margin-bottom: 20px;
    }
    .form-label {
      display: block;
      font-size: 14px;
      font-weight: 500;
      color: #374151;
      margin-bottom: 8px;
    }
    .form-input {
      width: 100%;
      padding: 12px 14px;
      border: 1.5px solid #e5e7eb;
      border-radius: 10px;
      font-size: 14px;
      transition: border-color 0.15s, box-shadow 0.15s;
      outline: none;
    }
    .form-input:focus {
      border-color: #667eea;
      box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.15);
    }
    .form-error {
      color: #dc2626;
      font-size: 13px;
      margin-top: 6px;
    }
    .form-row {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 24px;
    }
    .checkbox-row {
      display: flex;
      align-items: center;
      gap: 8px;
      font-size: 14px;
      color: #374151;
    }
    .checkbox-row input {
      width: 16px;
      height: 16px;
      accent-color: #667eea;
    }
    .btn-primary {
      width: 100%;
      padding: 13px;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      border: none;
      border-radius: 10px;
      font-size: 15px;
      font-weight: 600;
      cursor: pointer;
      transition: transform 0.1s, box-shadow 0.15s;
    }
    .btn-primary:hover {
      box-shadow: 0 10px 20px -8px rgba(102, 126, 234, 0.5);
    }
    .btn-primary:active { transform: translateY(1px); }
    .alert {
      padding: 12px 14px;
      border-radius: 10px;
      font-size: 14px;
      margin-bottom: 20px;
    }
    .alert-error {
      background: #fef2f2;
      border: 1px solid #fecaca;
      color: #dc2626;
    }
    .alert-success {
      background: #f0fdf4;
      border: 1px solid #bbf7d0;
      color: #16a34a;
    }
    .demo-info {
      margin-top: 24px;
      padding: 14px;
      background: #f9fafb;
      border-radius: 10px;
      border: 1px solid #e5e7eb;
    }
    .demo-info p {
      font-size: 13px;
      color: #6b7280;
      line-height: 1.5;
    }
    .demo-info code {
      background: white;
      padding: 2px 6px;
      border-radius: 4px;
      font-family: ui-monospace, monospace;
      color: #374151;
      font-size: 12px;
    }
  </style>
</head>
<body>
  <div class="login-card">
    <div class="login-head">
      <div class="login-logo">DC</div>
      <h1 class="login-title">Admin Panel</h1>
      <p class="login-subtitle">Sign in to manage DCTech Shop</p>
    </div>

    @if ($errors->any())
      <div class="alert alert-error">
        {{ $errors->first() }}
      </div>
    @endif

    @if (session('status'))
      <div class="alert alert-success">
        {{ session('status') }}
      </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
      @csrf
      <div class="form-group">
        <label class="form-label" for="email">Email address</label>
        <input type="email" id="email" name="email" value="{{ old('email') }}"
               class="form-input" required autofocus autocomplete="email" />
        @error('email')<div class="form-error">{{ $message }}</div>@enderror
      </div>

      <div class="form-group">
        <label class="form-label" for="password">Password</label>
        <input type="password" id="password" name="password"
               class="form-input" required autocomplete="current-password" />
        @error('password')<div class="form-error">{{ $message }}</div>@enderror
      </div>

      <div class="form-row">
        <label class="checkbox-row">
          <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }} />
          Remember me
        </label>
      </div>

      <button type="submit" class="btn-primary">Sign in</button>
    </form>


  </div>
</body>
</html>
