<!DOCTYPE html>
<html lang="en">
<head>
<title>Shyft - A CMS for the Cloud</title>

<!-- HTML5 shim, for IE6-8 support of HTML elements -->
<!--[if lt IE 9]>
<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->

<link href="css://bootstrap.css" rel="stylesheet">
<link href="css://theme.css" rel="stylesheet">
<script src="base://media/lib_koowa/js/mootools.js"></script>
<script src="base://media/lib_koowa/js/koowa.js"></script>
<script src="js://jquery.js"></script>
<script src="js://tabs.js"></script>
<script src="js://dropdown.js"></script>
<script src="js://modal.js"></script>
</head>
<body>
    <header class="topbar">
        <div class="fill">
            <div class="container">
                <h3><a class="brand" href="index.php">Shyfted</a></h3>

                <?=@container('navigation')?>

                <?=@container('user')?>
            </div>
        </div>
    </header>

    <div class="container layout">

        <!--layout-->
        <div class="content">
            <div class="row">
                <div class="span4">
                    <?=@container('toolbar-left', 'wrapper')?>
                    <?=@container('left',         'wrapper')?>
                </div>
                <div class="span12">
                    <?=@container('toolbar-top', 'wrapper')?>
                    <?=@container('page')?>
                </div>
            </div>
        </div>
        <!--/endlayout-->

        <footer>
            <?=@container('footer')?>
        </footer>
    </div>
</body>
</html>
