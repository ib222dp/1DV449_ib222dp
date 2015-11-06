<?php

class HTMLView
{
    public function echoHTML($body)
    {
        echo "<!DOCTYPE html>
            <html lang='sv'>
			<head>
				<title>Labb 1</title>
				<meta charset='utf-8' />
			</head>
			<body>
                $body
			</body>
            </html>";
    }
}