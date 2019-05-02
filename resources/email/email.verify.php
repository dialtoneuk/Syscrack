<!-- Emails use the XHTML Strict doctype -->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

        <meta name="viewport" content="width=device-width"/>
        <style type="text/css">
            .container{
                width: 90%;
                height: 30%;
                padding: 5%;
                margin-left: auto;
                margin-right: auto;
            }

            .heading{
                width: 95%;
                height: 20%;
                padding: 1.5%;
                background-color: rgb(255, 255, 255);
                border:1px solid rgb(221, 221, 221);
                border-radius: 4px;
            }

            .heading > h1, h2, h3, h4
            {
                color: rgb(51, 51, 51);
            }

            .body
            {

                text-align: center;
                width: 100%;
                height: 70%;
            }

            .verify
            {

                width: 200px;
                height: 20px;
                padding: 15px;
                background-color: rgb(255, 255, 255);
                border:1px solid rgb(221, 221, 221);
                border-radius: 4px;
                text-transform: uppercase;
                margin-left: auto;
                margin-right: auto;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="heading">
                <h1>
                    Verify your email
                </h1>
            </div>
            <div class="body">
                <p>
                    Thanks for signing up to Syscrack, please click the link below to verify your account with us
                </p>
                <div class="verify">
                    <a href="%link%verify?token=%token%">Verify your email</a>
                </div>
                <p>
                    Or copy and paste the token below into the %link%verify page
                </p>
                <h3>
                    %token%
                </h3>
            </div>
            <p style="text-align: center; font-size: 8px;">
                Syscrack was created by Lewis Lancaster
            </p>
        </div>
    </body>
</html>