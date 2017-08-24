<?php
include 'dbconfig.php';
//d($old_db, $new_db);
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
        <meta name="description" content="">
        <meta name="author" content="">
        <link rel="icon" href="../../favicon.ico">

        <title>Expert Online Training</title>

        <!-- Bootstrap core CSS -->
        <link href="bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="bower_components/bootstrap/dist/css/bootstrap-theme.min.css" rel="stylesheet" type="text/css"/>
        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
          <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
        <style>
            body{
                padding-top:15px;
            }
        </style>
    </head>

    <body>

        <div class="container">
            <div class="header clearfix">
                <nav>
                    <ul class="nav nav-pills pull-right">
                        <li role="presentation" class="active"><a href="index.php">Home</a></li>
                    </ul>
                </nav>
                <h3 class="text-muted">Expert Online Training</h3>
            </div>

            <div class="jumbotron">
                <h1>Data Import</h1>
                <p class="lead">Cras justo odio, dapibus ac facilisis in, egestas eget quam. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus.</p>
<?php

d($old_db, $new_db);
?>
            </div>

            <div class="row marketing">
                <div class="col-lg-6">
                    <div class="btn-group">
                        <button class="btn btn-default btn-lg dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Users <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a href="users.php">Import</a></li>
                        </ul>
                    </div>
                    <div class="btn-group">
                        <button class="btn btn-default btn-lg dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            User Meta <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a href="user-meta.php">Import</a></li>
                        </ul>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="btn-group">
                        <button class="btn btn-default btn-lg dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Posts & Post Meta <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a href="posts-post-meta.php">Import</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <footer class="footer">
                <p>&copy; 2016 Company, Inc.</p>
            </footer>

        </div> <!-- /container -->

        <script src="bower_components/jquery/dist/jquery.min.js" type="text/javascript"></script>
        <script src="bower_components/bootstrap/dist/js/bootstrap.min.js" type="text/javascript"></script>
        <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->

    </body>
</html>

