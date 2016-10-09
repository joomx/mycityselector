<?php
/**
 * MyCitySelector
 * @author Konstantin Kutsevalov
 */

/* @var $this JViewLegacy */
/* @var $subMenu string */
/* @var $icons array */

defined('_JEXEC') or die(header('HTTP/1.0 403 Forbidden') . 'Restricted access');

?>
<div id="j-sidebar-container" class="span2">
    <?= $sidebar ?>
</div>
<div id="j-main-container" class="span10 debug-mode">

    <nav class="navbar navbar-default">
        <div class="container-fluid">
            <div class="navbar-header">
                MCS
            </div>
            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav">

                    <?= $subMenu ?>

                    <li class="active"><a href="#">Link <span class="sr-only">(current)</span></a></li>
                    <li><a href="#">Link</a></li>
<!--                    <li class="dropdown">-->
<!--                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Dropdown <span class="caret"></span></a>-->
<!--                        <ul class="dropdown-menu">-->
<!--                            <li><a href="#">Action</a></li>-->
<!--                            <li><a href="#">Another action</a></li>-->
<!--                            <li><a href="#">Something else here</a></li>-->
<!--                            <li role="separator" class="divider"></li>-->
<!--                            <li><a href="#">Separated link</a></li>-->
<!--                            <li role="separator" class="divider"></li>-->
<!--                            <li><a href="#">One more separated link</a></li>-->
<!--                        </ul>-->
<!--                    </li>-->
                </ul>
            </div><!-- /.navbar-collapse -->
        </div>
    </nav>

    <h3>
        Isis Template Icons Demo
        <?php
        foreach (['black', 'red', 'orange', 'yellow', 'green', 'lightblue', 'blue', 'magenta'] as $color) {
            echo '<a href="javascript:void(0)" class="icon-color" style="background: ' . $color . '"></a>';
        }
        ?>
    </h3>
    <div id="system-message-container"><?= $this->getMessage() ?></div>
    <script>
        jQuery(function($) {
            $(".icon-color").on("click", function() {
                var color = $(this).css("background-color");
                $(".isis-demo .icon").css("color", color);
            });
        });
    </script>
    <form action="index.php" method="post" name="adminForm" class="admin-form <?= $this->getComponentName() ?>" id="adminForm">

        <?php
        foreach ($icons as $icon) {
            echo '<span class="isis-demo"><span class="icon ' . $icon . '"></span> .' . $icon . '</span>';
        }
        ?>

        <div class="clr"></div>
        <?= $this->formControllerName() ?>
        <?= $this->formOption() ?>
        <?= $this->formTask() ?>
        <?= $this->formToken() ?>
    </form>
</div>
