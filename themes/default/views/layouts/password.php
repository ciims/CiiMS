<!DOCTYPE html>
<html>
    <head>
        <title><?php echo $this->pageTitle; ?></title>
        <link href="//netdna.bootstrapcdn.com/twitter-bootstrap/2.3.0/css/bootstrap-combined.min.css" rel="stylesheet">
        <link href="//netdna.bootstrapcdn.com/font-awesome/3.0.2/css/font-awesome.css" rel="stylesheet">
        <link href="/css/install/main.css" rel="stylesheet" />
    </head>
    <body>
        <div class="well well-container">
            <div class="navbar navbar-inverse">
                <div class="navbar-inner">
                    <a class="brand" href="#">Password Required!</a>
                </div>
            </div>
            <ul class="breadcrumb">
                <li class ="active">This Post Requires a Password to View<span class="divider">></span></li>
            </ul>
            <div class="content">
                <hr />
                <?php echo $content; ?>
            </div>
            <div class="clearfix"></div>
        </div>
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
        <script src="//netdna.bootstrapcdn.com/twitter-bootstrap/2.3.0/js/bootstrap.min.js"></script>

    </body>
</html>