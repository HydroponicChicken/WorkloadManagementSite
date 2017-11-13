<?php
//NOTE: THERE MAY BE A BETTER WAY TO HANDLE 
//      DESTROYING THE SESSIONS

error_reporting(E_ALL);

//destroy any existing sessions
echo "destroying session";
session_unset();
session_destroy();
setcookie(session_name(),'',1,'/');
echo "session destroyed";



?>

<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" type="text/css">
  <link rel="stylesheet" href="index.css" type="text/css"> </head>

<body>
  <nav class="navbar navbar-expand-md bg-secondary navbar-dark p-2">
    <div class="container">
      <a class="navbar-brand mr-auto" href="createUser.html"></a>
      <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"> <span class="navbar-toggler-icon"></span> </button>
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto"> </ul>
        <a class="btn navbar-btn ml-2 text-white btn-secondary" href="createUser.html"><i class="fa d-inline fa-lg fa-sign-out"></i> Create Account</a>
      </div>
    </div>
  </nav>
  <div class="py-5">
    <div class="container">
      <div class="row">
        <img src="teamLogo.PNG" class="mx-auto"> </div>
    </div>
    <div class="py-5">
      <div class="container">
        <div class="row">
          <div class="col-md-3"> </div>
          <div class="col-md-6">
            <div class="card text-white p-5 bg-primary">
              <div class="card-body">
                <h1 class="mb-4">Login form</h1>
                <form method="post" action="index_handler.php">
                  <div class="form-group"> <label>Email address</label>
                    <input type="email" name="email" id="email" class="form-control" placeholder="Enter email"> </div>
                  <div class="form-group"> <label>Password</label>
                    <input type="password" name="password" id="password" class="form-control" placeholder="Password"> </div>
                  <button type="submit" name="login" id="login" class="btn btn-secondary text-white" value="login">Login</button>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
      <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js" integrity="sha384-b/U6ypiBEHpOf/4+1nzFpr53nxSS+GLCkfwBdFNTxtclqqenISfwAzpKaMNFNmj4" crossorigin="anonymous"></script>
      <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/js/bootstrap.min.js" integrity="sha384-h0AbiXch4ZDo7tp9hKZ4TsHbi047NrKGLO3SEJAg45jXxnGIfYzk4Si90RDIqNm1" crossorigin="anonymous"></script>
    </div>
  </div>
</body>

</html>