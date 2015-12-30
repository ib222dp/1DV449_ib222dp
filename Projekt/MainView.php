<?php

class MainView
{
    public function echoHTML($body)
    {
        echo    '<!DOCTYPE html>
                <html lang="sv">
			        <head>
				        <title>Botany Books Online</title>
				        <meta charset="utf-8" />
                         <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css"
          integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
			        </head>
			        <body>
                        <div class="container">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="jumbotron">
                                        <h1><a href="index.php" class="h1">Botany Books Online</a></h1>
                                    </div>
                                </div>
                            </div>' .
            $body .
            '</div>
                        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
                        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"
                        integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
			        </body>
                </html>';
    }

}