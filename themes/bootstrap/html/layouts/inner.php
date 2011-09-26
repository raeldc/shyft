<!--Inner Page Layout: Use this layout for the home page. It has various containers.-->
<div class="content">
    <div class="page-header">
        <h1>Inner Page <small>You know that this was loaded!</small></h1>
    </div>
    <div class="row">
        <div class="span4">
            <?=@container('left', 'wrapper')?>
        </div>
        <div class="span10">
            <?=@container('page')?>
        </div>
    </div>
</div>