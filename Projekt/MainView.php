<?php

class MainView {

    public function echoHTML($body, $isStartPage) {
        $start =   '<!DOCTYPE html>
                    <html lang="sv">
			        <head>
				        <title>Botany Books Online</title>
				        <meta charset="utf-8" />
                         <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css"
                            integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7"
                            crossorigin="anonymous">
			        </head>
			        <body>
                        <div class="container">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="jumbotron">
                                        <h1>';
        if($isStartPage) {
            $header = 'Botany Books Online</h1>';
        } else {
            $header = '<a href="index.php">Botany Books Online</a></h1>';
        }

        $end =                      '</div>
                                </div>
                            </div>' .
            $body .
            '</div>
                        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
                        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"
                        integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
			        </body>
                </html>';
        echo $start . $header . $end;
    }

}