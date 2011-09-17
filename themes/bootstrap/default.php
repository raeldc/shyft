<!DOCTYPE html>
<html lang="en">
<head>
<title><?=@container('title')?></title>
<?=@container('meta')?>
<?=@container('scripts')?>
<?=@container('styles')?>

<!-- HTML5 shim, for IE6-8 support of HTML elements -->
<!--[if lt IE 9]>
<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->

<link href="css://bootstrap-1.2.0.css" rel="stylesheet">
<link href="css://theme.css" rel="stylesheet">
</head>

<body>

    <header class="topbar">
        <div class="fill">
            <div class="container">
                <h3><a href="#">Flowku Project</a></h3>

                <nav>
                    <ul class="nav">
                        <li class="active"><a href="#">Home</a></li>
                        <li><a href="#">The Tutorial</a></li>
                        <li><a href="#">About Me</a></li>
                    </ul>
                </nav>

            </div>
        </div>
    </header>

    <div class="container">
        <div class="row after-topbar">
            <div class="span4 columns">
                <a href="#" class="rounded highlight gray main btn-huge active">
                    <h2>Pages</h2>
                    <p>Manage the pages of your website</p>
                </a>
                
                <nav class="highlight lightblue rounded list">
                    <a class="underlined" href="index.php?com=content&view=content&layout=form">Add a Page</a>
                    <a class="underlined" href="index.php?com=content&view=contents">List of Contents</a><br />
                </nav>
            </div>

            <section class="span12 columns">
                <?=@container('page')?>
            </section>
        </div>


        <footer>
            <p>&copy; 2011 Israel D. Canasa</p>
        </footer>
    </div><!--/container-->

</body>
</html>
