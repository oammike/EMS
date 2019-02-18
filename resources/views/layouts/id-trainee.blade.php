<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta content="id, chroma, oam, dev kit" name="keywords">
<meta content="Simple ID tool for OAM." name="description">
<meta content="OAM Instant ID" name="title">
<meta name="csrf-token" content="{{ csrf_token() }}" />
<link href="{{asset('public/css/trainee-style.css')}}" rel="stylesheet">
<link href="{{asset('public/css/pace.css')}}" rel="stylesheet">
<title>ID Printing | Open Access EMS</title>

</head>
  
<body>
    @yield('content')
</body>
</html>