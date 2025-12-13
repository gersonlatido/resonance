<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <link rel="stylesheet" href="css/app.css">

    <title>Welcome</title>
</head>
<body>

    <div class="welcome-container">
        <div class="logo"><img src="{{ asset('/images/logo-image.png')}}" alt="logo-image"></div>
        <div class="tag-line-image"><img src="{{ asset('/images/tag-line-image.png') }}" alt="tagline"></div>
        <a href="{{ route('menu.breakfast')}}" class="order-btn">Order Here</a>
        <!-- <button class="order-btn"><a href="menu.html?category=All%20Day%20Breakfast">Order Here</a></button> -->
     </div>

     
    <script type="module" src="script.js" >
    </script>
   
</body>
</html>