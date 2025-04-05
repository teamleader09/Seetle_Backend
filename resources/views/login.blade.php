@if(session('error'))
    <p>{{ session('error') }}</p>
@endif
<form method="POST" action="{{ route('login.post') }}">
    @csrf
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit">Login</button>
</form>