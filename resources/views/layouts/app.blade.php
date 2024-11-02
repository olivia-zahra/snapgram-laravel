<!DOCTYPE html>
<html lang="en">
<head>
  <title>Snapgram</title>
  <link rel="stylesheet" href="{{ asset('assets/style.css') }}">
</head>
<body>
 <!-- kode lainnya -->
<ul>
<li><a href="{{ route('home') }}">Home</a></li>
<li><a href="{{ route('albums.index') }}">Albums</a></li>
<li><a href="{{ route('photos.create') }}">Upload</a></li>
<li><a href="{{ route('profile.index') }}">Profile</a></li>
</ul>
<!-- kode lainnya -->
    @yield('content')
</body>
</html>