<!DOCTYPE html>
<html>
    <head>
        <title>Open Access Employee Management System</title>

        <link href="https://fonts.googleapis.com/css?family=Lato:100" rel="stylesheet" type="text/css">
        <link href="{{asset('public'.elixir('css/all.css'))}}" rel="stylesheet" />

        <style>
            html, body {
                height: 100%;
            }

            body {
                margin: 0;
                padding: 0;
                width: 100%;
                color: #B0BEC5;
                display: table;
                font-weight: 100;
               // 
            }

            .container {
                text-align: center;
                display: table-cell;
                vertical-align: middle;
            }

            .content {
                text-align: center;
                display: inline-block;
            }

            .title {
                font-size: 72px;
                font-family: 'Lato';
                margin-bottom: 40px;

            }
            a{color: #67aa08; font-weight: bold}
        </style>
    </head>
    <body class="login-page">
        <div class="container">
            <div class="content" style="margin-top: -300px"><img src="{{asset('public/img/oam_favicon1-55027f4ev1_site_icon-256x256.png')}} " width="50" />
                <h3>Hi there! <br/> Sorry, we're currently having a makeover situation...</h3>
                <!-- <div class="title" style="margin-top: -20px"><strong>Bolder. Fiercer. Better.</strong></div>  -->
                 <div class="login-box-body" style="box-shadow: 0 0 10px #fefefe; color: #666">
                <p>Installing New Module for <img src="./public/img/logo_postmates.png" width="160" />... <i class="fa fa-spinner fa-spin"></i><br /><br/>Sorry for the inconvenience. If you have questions and/or immediate concerns,<br/> please send an e-mail to <a class="text-primary" href="mailto:mpamero@openaccessbpo.com">mpamero@openaccessbpo.com</a> <br/> Thank you.</p></div>
            </div>
        </div>
    </body>
</html>
