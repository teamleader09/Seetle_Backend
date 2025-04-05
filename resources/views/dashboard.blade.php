<h1>Welcome, {{ auth()->user()->name }}</h1>

<form method="POST" action="{{ route('logout') }}">
    @csrf
    <button type="submit">Logout</button>
</form>