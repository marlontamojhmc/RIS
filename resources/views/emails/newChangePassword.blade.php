<!DOCTYPE html>
<html>
  <body>
    <h2>Hello, {{ $data['name'] ?? 'User' }}!</h2>
    <p>Account has been created!.</p>
    <hr/>
    <p>Your temporary password is: <strong>{{ $data['temp_password'] ?? 'temporary_password' }}</strong></p>
    <p>Please change your password by clicking the link below.</p>  
    <p><a href="{{ $data['change_password_link'] ?? '#' }}">Change Password</a></p>
  </body>
</html>
