<!DOCTYPE html>
<html lang="en">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0"/>
  <title>PrivApi - JokBot</title>

  <!-- CSS  -->
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <link href="css/materialize.css" type="text/css" rel="stylesheet" media="screen,projection"/>
  <link href="css/style.css" type="text/css" rel="stylesheet" media="screen,projection"/>
</head>
<body>
  <nav class="light-blue lighten-1" role="navigation">
    <div class="nav-wrapper container"><a id="logo-container" href="#" class="brand-logo">PrivApi</a>
      <ul class="right hide-on-med-and-down">
        <li><a href="#">Contact</a></li>
      </ul>

      <ul id="nav-mobile" class="sidenav">
        <li><a href="#">Contact</a></li>
      </ul>
      <a href="#" data-target="nav-mobile" class="sidenav-trigger"><i class="material-icons">menu</i></a>
    </div>
  </nav>
  <div class="section no-pad-bot" id="index-banner">
    <div class="container">
      <br><br>
      <h1 class="header center orange-text">PrivApi - JokBot</h1>
      <div class="row center">
        <h5 class="header col s12 light">provides services for detecting ip & bots, useful for preventing invalid traffic on your blog, website and application.</h5>
      </div>
      <div class="row center">

        <div class="card-panel">
            <span class="blue-text text-darken-2">Your IP :: {{request()->ip()}} | UserAgent :: {{request()->userAgent()}}</span>
        </div>
          <a target="_blank" href="/api/bot?ip={{request()->ip()}}&ua={{urlencode(request()->userAgent())}}" id="download-button" class="btn-large waves-effect waves-light orange">is my ip bot?</a>
          <a target="_blank" href="/api/ip2country?ip={{request()->ip()}}" id="download-button" class="btn-large waves-effect waves-light orange">what is my ip?</a>
      </div>
      <br><br>

    </div>
  </div>


  <div class="container">
    <div class="section">

      <!--   Icon Section   -->
      <div class="row">
        <div class="col s12 m4">
          <div class="icon-block">
            <h2 class="center light-blue-text"><i class="material-icons">flash_on</i></h2>
            <h5 class="center">Fast Load</h5>

            <p class="light">
                We provide fast load for detecting your traffic source
            </p>
          </div>
        </div>

        <div class="col s12 m4">
          <div class="icon-block">
            <h2 class="center light-blue-text"><i class="material-icons">group</i></h2>
            <h5 class="center">IP Detection</h5>

            <p class="light">We provide IP Detection for detecting your traffic source </p>
          </div>
        </div>

        <div class="col s12 m4">
          <div class="icon-block">
            <h2 class="center light-blue-text"><i class="material-icons">bug_report</i></h2>
            <h5 class="center">Bot Detection</h5>

            <p class="light">
                We provide Bot Detection for detecting your traffic source
            </p>
          </div>
        </div>
      </div>

    </div>
    <br><br>
  </div>


  <!--  Scripts-->
  <script src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
  <script src="js/materialize.js"></script>
  <script src="js/init.js"></script>

  </body>
</html>
