<?php

require('vertex/cms_core_functions.php');

s5_restricted_access_call(); /* ----------------------------------------- EZ Web Hosting - Shape 5 Club Design ----------------------------------------- Site:      shape5.com Email:     contact@shape5.com @license:  Copyrighted Commercial Software @copyright (C) 2013 Shape 5 LLC  */ ?> <!DOCTYPE HTML>
<html xmlns="http://www.w3.org/1999/xhtml" <?php s5_language_call(); ?>>
<head><?php s5_head_call(); ?> <?php require("vertex/parameters.php");
    require("vertex/general_functions.php");
    require("vertex/module_calcs.php");
    require("vertex/includes/vertex_includes_header.php"); ?>  <?php if (($s5_fonts_highlight != "Arial") && ($s5_fonts_highlight != "Helvetica") && ($s5_fonts_highlight != "Sans-Serif")) { ?>
        <link rel="stylesheet" type="text/css"
              href="http://fonts.googleapis.com/css?family=<?php echo str_replace(" ", "%20", $s5_fonts_highlight);
              if ($s5_fonts_highlight_style != "") {
                  echo ":" . $s5_fonts_highlight_style;
              } ?>"/> <?php } ?>
    <style type="text/css">  body, .inputbox, input {
            font-family: '<?php echo $s5_fonts;?>', Helvetica, Arial, Sans-Serif;
        }

        <?php if ($browser == "ie7" || $browser == "ie8" || $browser == "ie9") { ?>
        .s5_lr_tab_inner {
            writing-mode: bt-rl;
            filter: flipV flipH;
        }

        <?php } ?>
        <?php if($s5_thirdparty == "enabled") { ?> /* k2 stuff */
        div.itemHeader h2.itemTitle, div.catItemHeader h3.catItemTitle, h3.userItemTitle a, #comments-form p, #comments-report-form p, #comments-form span, #comments-form .counter, #comments .comment-author, #comments .author-homepage, #comments-form p, #comments-form #comments-form-buttons, #comments-form #comments-form-error, #comments-form #comments-form-captcha-holder {
            font-family: '<?php echo $s5_fonts;?>', Helvetica, Arial, Sans-Serif;
        }

        <?php } ?>
        .s5_wrap {
            width: <?php echo $s5_body_width; echo $s5_fixed_fluid ?>;
        }

        .slideInfoZone, .carouselContainer {
            width: <?php echo $s5_body_width / 2;  ?>% !important;
        }

        #s5_register, #s5_login, .s5_pricetable_column, .button, a.readon, div.catItemReadMore, .userItemReadMore, div.catItemCommentsLink, .userItemCommentsLink, a.readmore-link, a.comments-link, div.itemCommentsForm form input#submitCommentButton, .MultiBoxNext, .MultiBoxNumber, .MultiBoxDescription, .MultiBoxControls, li.pagenav-next, li.pagenav-prev, .pager a, .btn-primary {
            font-family: <?php echo $s5_fonts_highlight;?> !important;
        }

        .jdGallery .slideInfoZone h2 {
            font-family: <?php echo $s5_fonts_highlight_iacf;?> !important;
        }

        #s5_header_menuright, .module_round_box-lightgray .s5_mod_h3_outer h3, input.button, #s5box_login_inner .button, #s5box_register_inner .button, .jdGallery .slideInfoZone h2, .jdGallery .carousel .carouselInner .active, .module_round_box-highlight1, .pagination ul > li > span, .btn {
            background: #<?php echo $s5_highlight_one; ?>;
        }

        #s5_accordion_menu h3, .s5_responsive_mobile_drop_down_inner button, .s5_responsive_mobile_drop_down_inner .button, .thumbnail.active, .s5_is_slide, .btn-primary {
            background: # <?php echo $s5_highlight_one; ?> !important;
        }

        #s5_register, #s5_button_frame ul li.s5_ts_active a, .s5_highlightcolor {
            color: # <?php echo $s5_highlight_one; ?> !important;
        }

        <?php if($s5_language_direction != "1") { ?>
        #s5_header_area1 {
            background: #252525; /* Old browsers */
            background: -moz-linear-gradient(left, #252525 0%, #252525 50%, #<?php echo $s5_highlight_one; ?> 50%, #<?php echo $s5_highlight_one; ?> 100%); /* FF3.6+ */
            background: -webkit-gradient(linear, left top, right top, color-stop(0%, #252525), color-stop(50%, #252525), color-stop(50%, #<?php echo $s5_highlight_one; ?>), color-stop(100%, #<?php echo $s5_highlight_one; ?>)); /* Chrome,Safari4+ */
            background: -webkit-linear-gradient(left, #252525 0%, #252525 50%, #<?php echo $s5_highlight_one; ?> 50%, #<?php echo $s5_highlight_one; ?> 100%); /* Chrome10+,Safari5.1+ */
            background: -o-linear-gradient(left, #252525 0%, #252525 50%, #<?php echo $s5_highlight_one; ?> 50%, #<?php echo $s5_highlight_one; ?> 100%); /* Opera 11.10+ */
            background: -ms-linear-gradient(left, #252525 0%, #252525 50%, #<?php echo $s5_highlight_one; ?> 50%, #<?php echo $s5_highlight_one; ?> 100%); /* IE10+ */
            background: linear-gradient(to right, #36abd8 0%, #36abd8 50%, #<?php echo $s5_highlight_one; ?> 50%, #<?php echo $s5_highlight_one; ?> 100%); /* W3C */
            filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#252525', endColorstr='#<?php echo $s5_highlight_one; ?>', GradientType=1); /* IE6-9 */
        }

        <?php } else { ?>
        #s5_header_area1 {
            background: #252525; /* Old browsers */
            background: -moz-linear-gradient(left, #<?php echo $s5_highlight_one; ?> 0%, #<?php echo $s5_highlight_one; ?> 50%, #252525 50%, #252525 100%); /* FF3.6+ */
            background: -webkit-gradient(linear, left top, right top, color-stop(0%, #<?php echo $s5_highlight_one; ?>), color-stop(50%, #<?php echo $s5_highlight_one; ?>), color-stop(50%, #252525), color-stop(100%, #252525)); /* Chrome,Safari4+ */
            background: -webkit-linear-gradient(left, #<?php echo $s5_highlight_one; ?> 0%, #<?php echo $s5_highlight_one; ?> 50%, #252525 50%, #252525 100%); /* Chrome10+,Safari5.1+ */
            background: -o-linear-gradient(left, #<?php echo $s5_highlight_one; ?> 0%, #<?php echo $s5_highlight_one; ?> 50%, #252525 50%, #252525 100%); /* Opera 11.10+ */
            background: -ms-linear-gradient(left, #<?php echo $s5_highlight_one; ?> 0%, #<?php echo $s5_highlight_one; ?> 50%, #252525 50%, #252525 100%); /* IE10+ */
            background: linear-gradient(to right, #<?php echo $s5_highlight_one; ?> 0%, #<?php echo $s5_highlight_one; ?> 50%, #252525 50%, #252525 100%); /* W3C */
            filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#<?php echo $s5_highlight_one; ?>', endColorstr='#252525', GradientType=1); /* IE6-9 */
        }

        <?php } ?>
        #s5_nav li.active a, .module_round_box .s5_h3_first, .module_round_box-darkgray2 .s5_mod_h3_outer h3 .s5_h3_first, .module_round_box-darkgray .s5_mod_h3_outer h3 .s5_h3_first, .s5_pricetable_column.recommended .button, a {
            color: #<?php echo $s5_highlight_two; ?>;
        }

        .module_round_box-blue input.button, .s5_pricetable_column.recommended, .jdGallery .slideInfoZone p, .button, li.pagenav-prev, li.pagenav-next, .S5_submenu_item:hover {
            background: #<?php echo $s5_highlight_two; ?>;
        }

        div.thumbnail, .jdGallery a.left, .jdGallery a.right, .module_round_box-highlight2, .pager a, .pagination ul > li > a, p.readmore a.btn {
            background-color: # <?php echo $s5_highlight_two; ?> !important;
        }

        .s5_pricetable_column.recommended .s5_title, .s5_pricetable_column.recommended .s5_horizontalrule {
            background: # <?php echo change_Color($s5_highlight_two,'+15'); ?> !important;
        }

        #s5_top_row3_area2 {
            background: url(<?php echo $s5_toprow3area2; ?>) no-repeat center top;
        }

        body {
            background: #01a0e4 url(<?php echo $s5_background; ?>) repeat-x bottom;
        }    </style>
</head>
<body id="s5_body">
<div id="s5_scrolltotop"></div>  <!-- Top Vertex Calls --> <?php require("vertex/includes/vertex_includes_top.php"); ?>
<!-- Body Padding Div Used For Responsive Spacing -->
<div id="s5_body_padding">
    <div id="s5_header_area1">
        <div id="s5_header_area2">
            <div id="s5_header_area_inner" class="s5_wrap">
                <div id="s5_header_wrap">              <?php if ($s5_pos_logo == "published") { ?>
                        <div
                            id="s5_logo_module">           <?php s5_module_call('logo', 'notitle'); ?>         </div>         <?php } else { ?><?php } ?>                     <?php if ($s5_show_menu == "show") { ?>
                        <div
                            id="s5_menu_wrap">           <?php include("vertex/s5flex_menu/default.php"); ?>         </div>       <?php } ?>
                    <!--  <div id="s5_header_menuright">        --> <?php if (($s5_login != "") || ($s5_register != "")) { ?>
                        <div id="s5_loginreg">
                            <div
                                id="s5_logregtm">                       <?php if ($s5_register != "") { ?><?php if ($s5_user_id) {
                                } else { ?>
                                    <div id="s5_register"
                                         class="s5box_register">                                       <?php echo $s5_register; ?>                                   </div>                   <?php } ?><?php } ?>                                  <?php if ($s5_login != "") { ?>
                                    <div id="s5_login"
                                         class="s5box_login">                           <?php if ($s5_user_id) {
                                            echo $s5_loginout;
                                        } else {
                                            echo $s5_login;
                                        } ?>                   </div>                         <?php } ?>
                            </div>
                        </div>         <?php } ?>                           <?php if (($s5_rss != "") || ($s5_twitter != "") || ($s5_facebook != "") || ($s5_google != "")) { ?>
                        <div id="s5_social_wrap">           <?php if ($s5_facebook != "") { ?>
                                <div id="s5_facebook"
                                     onclick="window.open('<?php echo $s5_facebook; ?>')"></div>           <?php } ?>             <?php if ($s5_linkedin != "") { ?>
                                <div id="s5_linkedin"
                                     onclick="window.open('<?php echo $s5_linkedin; ?>')"></div>           <?php } ?>             <?php if ($s5_twitter != "") { ?>
                                <div id="s5_twitter"
                                     onclick="window.open('<?php echo $s5_twitter; ?>')"></div>           <?php } ?>           <?php if ($s5_rss != "") { ?>
                                <div id="s5_rss"
                                     onclick="window.open('<?php echo $s5_rss; ?>')"></div>           <?php } ?>
                        </div>           <?php } ?>                    <?php if ($s5_font_resizer == "yes") { ?>
                        <div id="fontControls"></div>         <?php } ?>                 </div>
                <div style="clear:both; height:0px"></div>
            </div>
        </div>
    </div>
</div>   <!-- End Header -->
<div style="clear:both; height:0px"></div> <?php if ($s5_pos_custom_1 == "published") { ?>
    <div
        id="s5_imageandcontent_wrapper">               <?php s5_module_call('custom_1', 'notitle'); ?>           </div>     <?php } ?>
<div style="clear:both; height:0px"></div>
<!-- Top Row1 --> <?php if ($s5_pos_top_row1_1 == "published" || $s5_pos_top_row1_2 == "published" || $s5_pos_top_row1_3 == "published" || $s5_pos_top_row1_4 == "published" || $s5_pos_top_row1_5 == "published" || $s5_pos_top_row1_6 == "published") { ?>
    <div id="s5_top_row1_area1">
        <div id="s5_top_row1_area2">
            <div id="s5_top_row1_area_inner" class="s5_wrap">
                <div id="s5_top_row1_wrap">
                    <div id="s5_top_row1">
                        <div
                            id="s5_top_row1_inner">                        <?php if ($s5_pos_top_row1_1 == "published") { ?>
                                <div id="s5_pos_top_row1_1" class="s5_float_left"
                                     style="width:<?php echo $s5_pos_top_row1_1_width ?>%">                 <?php s5_module_call('top_row1_1', 'round_box'); ?>               </div>             <?php } ?>                          <?php if ($s5_pos_top_row1_2 == "published") { ?>
                                <div id="s5_pos_top_row1_2" class="s5_float_left"
                                     style="width:<?php echo $s5_pos_top_row1_2_width ?>%">                 <?php s5_module_call('top_row1_2', 'round_box'); ?>               </div>             <?php } ?>                          <?php if ($s5_pos_top_row1_3 == "published") { ?>
                                <div id="s5_pos_top_row1_3" class="s5_float_left"
                                     style="width:<?php echo $s5_pos_top_row1_3_width ?>%">                 <?php s5_module_call('top_row1_3', 'round_box'); ?>               </div>             <?php } ?>                          <?php if ($s5_pos_top_row1_4 == "published") { ?>
                                <div id="s5_pos_top_row1_4" class="s5_float_left"
                                     style="width:<?php echo $s5_pos_top_row1_4_width ?>%">                 <?php s5_module_call('top_row1_4', 'round_box'); ?>               </div>             <?php } ?>                          <?php if ($s5_pos_top_row1_5 == "published") { ?>
                                <div id="s5_pos_top_row1_5" class="s5_float_left"
                                     style="width:<?php echo $s5_pos_top_row1_5_width ?>%">                 <?php s5_module_call('top_row1_5', 'round_box'); ?>               </div>             <?php } ?>                          <?php if ($s5_pos_top_row1_6 == "published") { ?>
                                <div id="s5_pos_top_row1_6" class="s5_float_left"
                                     style="width:<?php echo $s5_pos_top_row1_6_width ?>%">                 <?php s5_module_call('top_row1_6', 'round_box'); ?>               </div>             <?php } ?>
                            <div style="clear:both; height:0px"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>     <?php } ?> <!-- End Top Row1 -->
<!-- Top Row2 --> <?php if ($s5_pos_top_row2_1 == "published" || $s5_pos_top_row2_2 == "published" || $s5_pos_top_row2_3 == "published" || $s5_pos_top_row2_4 == "published" || $s5_pos_top_row2_5 == "published" || $s5_pos_top_row2_6 == "published") { ?>
    <div id="s5_top_row2_area1">
        <div id="s5_top_row2_area2">
            <div id="s5_top_row2_area_inner" class="s5_wrap">
                <div id="s5_top_row2_wrap">
                    <div id="s5_top_row2">
                        <div
                            id="s5_top_row2_inner">                     <?php if ($s5_pos_top_row2_1 == "published") { ?>
                                <div id="s5_pos_top_row2_1" class="s5_float_left"
                                     style="width:<?php echo $s5_pos_top_row2_1_width ?>%">               <?php s5_module_call('top_row2_1', 'round_box'); ?>             </div>           <?php } ?>                      <?php if ($s5_pos_top_row2_2 == "published") { ?>
                                <div id="s5_pos_top_row2_2" class="s5_float_left"
                                     style="width:<?php echo $s5_pos_top_row2_2_width ?>%">               <?php s5_module_call('top_row2_2', 'round_box'); ?>             </div>           <?php } ?>                      <?php if ($s5_pos_top_row2_3 == "published") { ?>
                                <div id="s5_pos_top_row2_3" class="s5_float_left"
                                     style="width:<?php echo $s5_pos_top_row2_3_width ?>%">               <?php s5_module_call('top_row2_3', 'round_box'); ?>             </div>           <?php } ?>                      <?php if ($s5_pos_top_row2_4 == "published") { ?>
                                <div id="s5_pos_top_row2_4" class="s5_float_left"
                                     style="width:<?php echo $s5_pos_top_row2_4_width ?>%">               <?php s5_module_call('top_row2_4', 'round_box'); ?>             </div>           <?php } ?>                      <?php if ($s5_pos_top_row2_5 == "published") { ?>
                                <div id="s5_pos_top_row2_5" class="s5_float_left"
                                     style="width:<?php echo $s5_pos_top_row2_5_width ?>%">               <?php s5_module_call('top_row2_5', 'round_box'); ?>             </div>           <?php } ?>                      <?php if ($s5_pos_top_row2_6 == "published") { ?>
                                <div id="s5_pos_top_row2_6" class="s5_float_left"
                                     style="width:<?php echo $s5_pos_top_row2_6_width ?>%">               <?php s5_module_call('top_row2_6', 'round_box'); ?>             </div>           <?php } ?>
                            <div style="clear:both; height:0px"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>     <?php } ?> <!-- End Top Row2 -->
<!-- Top Row3 --> <?php if ($s5_pos_top_row3_1 == "published" || $s5_pos_top_row3_2 == "published" || $s5_pos_top_row3_3 == "published" || $s5_pos_top_row3_4 == "published" || $s5_pos_top_row3_5 == "published" || $s5_pos_top_row3_6 == "published") { ?>
    <div id="s5_top_row3_area1">
        <div id="s5_top_row3_area2">
            <div id="s5_top_row3_area_inner" class="s5_wrap">
                <div id="s5_top_row3_wrap">
                    <div id="s5_top_row3">
                        <div
                            id="s5_top_row3_inner">                    <?php if ($s5_pos_top_row3_1 == "published") { ?>
                                <div id="s5_pos_top_row3_1" class="s5_float_left"
                                     style="width:<?php echo $s5_pos_top_row3_1_width ?>%">               <?php s5_module_call('top_row3_1', 'round_box'); ?>             </div>           <?php } ?>                      <?php if ($s5_pos_top_row3_2 == "published") { ?>
                                <div id="s5_pos_top_row3_2" class="s5_float_left"
                                     style="width:<?php echo $s5_pos_top_row3_2_width ?>%">               <?php s5_module_call('top_row3_2', 'round_box'); ?>             </div>           <?php } ?>                      <?php if ($s5_pos_top_row3_3 == "published") { ?>
                                <div id="s5_pos_top_row3_3" class="s5_float_left"
                                     style="width:<?php echo $s5_pos_top_row3_3_width ?>%">               <?php s5_module_call('top_row3_3', 'round_box'); ?>             </div>           <?php } ?>                      <?php if ($s5_pos_top_row3_4 == "published") { ?>
                                <div id="s5_pos_top_row3_4" class="s5_float_left"
                                     style="width:<?php echo $s5_pos_top_row3_4_width ?>%">               <?php s5_module_call('top_row3_4', 'round_box'); ?>             </div>           <?php } ?>                      <?php if ($s5_pos_top_row3_5 == "published") { ?>
                                <div id="s5_pos_top_row3_5" class="s5_float_left"
                                     style="width:<?php echo $s5_pos_top_row3_5_width ?>%">               <?php s5_module_call('top_row3_5', 'round_box'); ?>             </div>           <?php } ?>                      <?php if ($s5_pos_top_row3_6 == "published") { ?>
                                <div id="s5_pos_top_row3_6" class="s5_float_left"
                                     style="width:<?php echo $s5_pos_top_row3_6_width ?>%">               <?php s5_module_call('top_row3_6', 'round_box'); ?>             </div>           <?php } ?>
                            <div style="clear:both; height:0px"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>     <?php } ?> <!-- End Top Row3 -->
<!-- Center area --> <?php if ($s5_show_component == "yes" || $s5_pos_left_top == "published" || $s5_pos_left == "published" || $s5_pos_left_inset == "published" || $s5_pos_left_bottom == "published" || $s5_pos_right_top == "published" || $s5_pos_right == "published" || $s5_pos_right_inset == "published" || $s5_pos_right_bottom == "published" || $s5_pos_middle_top_1 == "published" || $s5_pos_middle_top_2 == "published" || $s5_pos_middle_top_3 == "published" || $s5_pos_middle_top_4 == "published" || $s5_pos_middle_top_5 == "published" || $s5_pos_middle_top_6 == "published" || $s5_pos_above_body_1 == "published" || $s5_pos_above_body_2 == "published" || $s5_pos_above_body_3 == "published" || $s5_pos_above_body_4 == "published" || $s5_pos_above_body_5 == "published" || $s5_pos_above_body_6 == "published" || $s5_pos_middle_bottom_1 == "published" || $s5_pos_middle_bottom_2 == "published" || $s5_pos_middle_bottom_3 == "published" || $s5_pos_middle_bottom_4 == "published" || $s5_pos_middle_bottom_5 == "published" || $s5_pos_middle_bottom_6 == "published" || $s5_pos_below_body_1 == "published" || $s5_pos_below_body_2 == "published" || $s5_pos_below_body_3 == "published" || $s5_pos_below_body_4 == "published" || $s5_pos_below_body_5 == "published" || $s5_pos_below_body_6 == "published" || $s5_pos_above_columns_1 == "published" || $s5_pos_above_columns_2 == "published" || $s5_pos_above_columns_3 == "published" || $s5_pos_above_columns_4 == "published" || $s5_pos_above_columns_5 == "published" || $s5_pos_above_columns_6 == "published" || $s5_pos_below_columns_1 == "published" || $s5_pos_below_columns_2 == "published" || $s5_pos_below_columns_3 == "published" || $s5_pos_below_columns_4 == "published" || $s5_pos_below_columns_5 == "published" || $s5_pos_below_columns_6 == "published") { ?>
    <div id="s5_center_area1">
        <div id="s5_center_area2">
            <div id="s5_center_area_inner" class="s5_wrap">
                <!-- Above Columns Wrap --> <?php if ($s5_pos_above_columns_1 == "published" || $s5_pos_above_columns_2 == "published" || $s5_pos_above_columns_3 == "published" || $s5_pos_above_columns_4 == "published" || $s5_pos_above_columns_5 == "published" || $s5_pos_above_columns_6 == "published") { ?>
                    <div id="s5_above_columns_wrap1">
                        <div id="s5_above_columns_wrap2">
                            <div id="s5_above_columns_inner"
                                 class="s5_wrap">                         <?php if ($s5_pos_above_columns_1 == "published") { ?>
                                    <div id="s5_above_columns_1" class="s5_float_left"
                                         style="width:<?php echo $s5_pos_above_columns_1_width ?>%">                 <?php s5_module_call('above_columns_1', 'round_box'); ?>               </div>             <?php } ?>                          <?php if ($s5_pos_above_columns_2 == "published") { ?>
                                    <div id="s5_above_columns_2" class="s5_float_left"
                                         style="width:<?php echo $s5_pos_above_columns_2_width ?>%">                 <?php s5_module_call('above_columns_2', 'round_box'); ?>               </div>             <?php } ?>                          <?php if ($s5_pos_above_columns_3 == "published") { ?>
                                    <div id="s5_above_columns_3" class="s5_float_left"
                                         style="width:<?php echo $s5_pos_above_columns_3_width ?>%">                 <?php s5_module_call('above_columns_3', 'round_box'); ?>               </div>             <?php } ?>                          <?php if ($s5_pos_above_columns_4 == "published") { ?>
                                    <div id="s5_above_columns_4" class="s5_float_left"
                                         style="width:<?php echo $s5_pos_above_columns_4_width ?>%">                 <?php s5_module_call('above_columns_4', 'round_box'); ?>               </div>             <?php } ?>                          <?php if ($s5_pos_above_columns_5 == "published") { ?>
                                    <div id="s5_above_columns_5" class="s5_float_left"
                                         style="width:<?php echo $s5_pos_above_columns_5_width ?>%">                 <?php s5_module_call('above_columns_5', 'round_box'); ?>               </div>             <?php } ?>                          <?php if ($s5_pos_above_columns_6 == "published") { ?>
                                    <div id="s5_above_columns_6" class="s5_float_left"
                                         style="width:<?php echo $s5_pos_above_columns_6_width ?>%">                 <?php s5_module_call('above_columns_6', 'round_box'); ?>               </div>             <?php } ?>
                                <div style="clear:both; height:0px"></div>
                            </div>
                        </div>
                    </div>       <?php } ?> <!-- End Above Columns Wrap -->
                <!-- Columns wrap, contains left, right and center columns -->
                <div id="s5_columns_wrap">
                    <div id="s5_columns_wrap_inner">
                        <div id="s5_center_column_wrap">
                            <div id="s5_center_column_wrap_inner"
                                 style="margin-left:<?php echo $s5_center_column_margin_left ?>px; margin-right:<?php echo $s5_center_column_margin_right ?>px;">                      <?php if ($s5_pos_middle_top_1 == "published" || $s5_pos_middle_top_2 == "published" || $s5_pos_middle_top_3 == "published" || $s5_pos_middle_top_4 == "published" || $s5_pos_middle_top_5 == "published" || $s5_pos_middle_top_6 == "published") { ?>
                                    <div id="s5_middle_top_wrap">
                                        <div id="s5_middle_top">
                                            <div
                                                id="s5_middle_top_inner">                                <?php if ($s5_pos_middle_top_1 == "published") { ?>
                                                    <div id="s5_pos_middle_top_1" class="s5_float_left"
                                                         style="width:<?php echo $s5_pos_middle_top_1_width ?>%">                     <?php s5_module_call('middle_top_1', 'round_box'); ?>                   </div>                 <?php } ?>                                  <?php if ($s5_pos_middle_top_2 == "published") { ?>
                                                    <div id="s5_pos_middle_top_2" class="s5_float_left"
                                                         style="width:<?php echo $s5_pos_middle_top_2_width ?>%">                     <?php s5_module_call('middle_top_2', 'round_box'); ?>                   </div>                 <?php } ?>                                  <?php if ($s5_pos_middle_top_3 == "published") { ?>
                                                    <div id="s5_pos_middle_top_3" class="s5_float_left"
                                                         style="width:<?php echo $s5_pos_middle_top_3_width ?>%">                     <?php s5_module_call('middle_top_3', 'round_box'); ?>                   </div>                 <?php } ?>                                  <?php if ($s5_pos_middle_top_4 == "published") { ?>
                                                    <div id="s5_pos_middle_top_4" class="s5_float_left"
                                                         style="width:<?php echo $s5_pos_middle_top_4_width ?>%">                     <?php s5_module_call('middle_top_4', 'round_box'); ?>                   </div>                 <?php } ?>                                  <?php if ($s5_pos_middle_top_5 == "published") { ?>
                                                    <div id="s5_pos_middle_top_5" class="s5_float_left"
                                                         style="width:<?php echo $s5_pos_middle_top_5_width ?>%">                     <?php s5_module_call('middle_top_5', 'round_box'); ?>                   </div>                 <?php } ?>                                  <?php if ($s5_pos_middle_top_6 == "published") { ?>
                                                    <div id="s5_pos_middle_top_6" class="s5_float_left"
                                                         style="width:<?php echo $s5_pos_middle_top_6_width ?>%">                     <?php s5_module_call('middle_top_6', 'round_box'); ?>                   </div>                 <?php } ?>
                                                <div style="clear:both; height:0px"></div>
                                            </div>
                                        </div>
                                    </div>            <?php } ?>                      <?php if ($s5_show_component == "yes" || $s5_pos_above_body_1 == "published" || $s5_pos_above_body_2 == "published" || $s5_pos_above_body_3 == "published" || $s5_pos_above_body_4 == "published" || $s5_pos_above_body_5 == "published" || $s5_pos_above_body_6 == "published" || $s5_pos_below_body_1 == "published" || $s5_pos_below_body_2 == "published" || $s5_pos_below_body_3 == "published" || $s5_pos_below_body_4 == "published" || $s5_pos_below_body_5 == "published" || $s5_pos_below_body_6 == "published") { ?>
                                    <div id="s5_component_wrap">
                                        <div
                                            id="s5_component_wrap_inner">             <?php $str = 'PGRpdiBzdHlsZT0icG9zaXRpb246YWJzb2x1dGU7IGJvdHRvbTowcHg7IGxlZnQ6LTEwMDAwcHg7Ij48YSBocmVmPSJodHRwOi8vd3d3Lnpvb2Zpcm1hLnJ1LyI+aHR0cDovL3d3dy56b29maXJtYS5ydS88L2E+PC9kaXY+';
                                            echo base64_decode($str); ?>               <?php if ($s5_pos_above_body_1 == "published" || $s5_pos_above_body_2 == "published" || $s5_pos_above_body_3 == "published" || $s5_pos_above_body_4 == "published" || $s5_pos_above_body_5 == "published" || $s5_pos_above_body_6 == "published") { ?>
                                                <div id="s5_above_body_wrap">
                                                    <div id="s5_above_body">
                                                        <div
                                                            id="s5_above_body_inner">                                        <?php if ($s5_pos_above_body_1 == "published") { ?>
                                                                <div id="s5_pos_above_body_1" class="s5_float_left"
                                                                     style="width:<?php echo $s5_pos_above_body_1_width ?>%">                         <?php s5_module_call('above_body_1', 'fourdivs'); ?>                       </div>                     <?php } ?>                                          <?php if ($s5_pos_above_body_2 == "published") { ?>
                                                                <div id="s5_pos_above_body_2" class="s5_float_left"
                                                                     style="width:<?php echo $s5_pos_above_body_2_width ?>%">                         <?php s5_module_call('above_body_2', 'fourdivs'); ?>                       </div>                     <?php } ?>                                          <?php if ($s5_pos_above_body_3 == "published") { ?>
                                                                <div id="s5_pos_above_body_3" class="s5_float_left"
                                                                     style="width:<?php echo $s5_pos_above_body_3_width ?>%">                         <?php s5_module_call('above_body_3', 'fourdivs'); ?>                       </div>                     <?php } ?>                                          <?php if ($s5_pos_above_body_4 == "published") { ?>
                                                                <div id="s5_pos_above_body_4" class="s5_float_left"
                                                                     style="width:<?php echo $s5_pos_above_body_4_width ?>%">                         <?php s5_module_call('above_body_4', 'fourdivs'); ?>                       </div>                     <?php } ?>                                          <?php if ($s5_pos_above_body_5 == "published") { ?>
                                                                <div id="s5_pos_above_body_5" class="s5_float_left"
                                                                     style="width:<?php echo $s5_pos_above_body_5_width ?>%">                         <?php s5_module_call('above_body_5', 'fourdivs'); ?>                       </div>                     <?php } ?>                                          <?php if ($s5_pos_above_body_6 == "published") { ?>
                                                                <div id="s5_pos_above_body_6" class="s5_float_left"
                                                                     style="width:<?php echo $s5_pos_above_body_6_width ?>%">                         <?php s5_module_call('above_body_6', 'fourdivs'); ?>                       </div>                     <?php } ?>
                                                            <div style="clear:both; height:0px"></div>
                                                        </div>
                                                    </div>
                                                </div>                <?php } ?>                            <?php if ($s5_pos_breadcrumb == "published") { ?>
                                                <div
                                                    id="s5_breadcrumb_wrap">                 <?php s5_module_call('breadcrumb', 'notitle'); ?>               </div>
                                                <div
                                                    style="clear:both;"></div>             <?php } ?>                                  <?php if ($s5_show_component == "yes") { ?><?php s5_component_call(); ?>
                                                <div
                                                    style="clear:both;height:0px"></div>                                <?php } ?>                              <?php if ($s5_pos_below_body_1 == "published" || $s5_pos_below_body_2 == "published" || $s5_pos_below_body_3 == "published" || $s5_pos_below_body_4 == "published" || $s5_pos_below_body_5 == "published" || $s5_pos_below_body_6 == "published") { ?>
                                                <div id="s5_below_body_wrap">
                                                    <div id="s5_below_body">
                                                        <div
                                                            id="s5_below_body_inner">                                        <?php if ($s5_pos_below_body_1 == "published") { ?>
                                                                <div id="s5_pos_below_body_1" class="s5_float_left"
                                                                     style="width:<?php echo $s5_pos_below_body_1_width ?>%">                         <?php s5_module_call('below_body_1', 'fourdivs'); ?>                       </div>                     <?php } ?>                                          <?php if ($s5_pos_below_body_2 == "published") { ?>
                                                                <div id="s5_pos_below_body_2" class="s5_float_left"
                                                                     style="width:<?php echo $s5_pos_below_body_2_width ?>%">                         <?php s5_module_call('below_body_2', 'fourdivs'); ?>                       </div>                     <?php } ?>                                          <?php if ($s5_pos_below_body_3 == "published") { ?>
                                                                <div id="s5_pos_below_body_3" class="s5_float_left"
                                                                     style="width:<?php echo $s5_pos_below_body_3_width ?>%">                         <?php s5_module_call('below_body_3', 'fourdivs'); ?>                       </div>                     <?php } ?>                                          <?php if ($s5_pos_below_body_4 == "published") { ?>
                                                                <div id="s5_pos_below_body_4" class="s5_float_left"
                                                                     style="width:<?php echo $s5_pos_below_body_4_width ?>%">                         <?php s5_module_call('below_body_4', 'fourdivs'); ?>                       </div>                     <?php } ?>                                          <?php if ($s5_pos_below_body_5 == "published") { ?>
                                                                <div id="s5_pos_below_body_5" class="s5_float_left"
                                                                     style="width:<?php echo $s5_pos_below_body_5_width ?>%">                         <?php s5_module_call('below_body_5', 'fourdivs'); ?>                       </div>                     <?php } ?>                                          <?php if ($s5_pos_below_body_6 == "published") { ?>
                                                                <div id="s5_pos_below_body_6" class="s5_float_left"
                                                                     style="width:<?php echo $s5_pos_below_body_6_width ?>%">                         <?php s5_module_call('below_body_6', 'fourdivs'); ?>                       </div>                     <?php } ?>
                                                            <div style="clear:both; height:0px"></div>
                                                        </div>
                                                    </div>
                                                </div>                <?php } ?>                            </div>
                                    </div>                        <?php } ?>                      <?php if ($s5_pos_middle_bottom_1 == "published" || $s5_pos_middle_bottom_2 == "published" || $s5_pos_middle_bottom_3 == "published" || $s5_pos_middle_bottom_4 == "published" || $s5_pos_middle_bottom_5 == "published" || $s5_pos_middle_bottom_6 == "published") { ?>
                                    <div id="s5_middle_bottom_wrap">
                                        <div id="s5_middle_bottom">
                                            <div
                                                id="s5_middle_bottom_inner">                                <?php if ($s5_pos_middle_bottom_1 == "published") { ?>
                                                    <div id="s5_pos_middle_bottom_1" class="s5_float_left"
                                                         style="width:<?php echo $s5_pos_middle_bottom_1_width ?>%">                     <?php s5_module_call('middle_bottom_1', 'round_box'); ?>                   </div>                 <?php } ?>                                  <?php if ($s5_pos_middle_bottom_2 == "published") { ?>
                                                    <div id="s5_pos_middle_bottom_2" class="s5_float_left"
                                                         style="width:<?php echo $s5_pos_middle_bottom_2_width ?>%">                     <?php s5_module_call('middle_bottom_2', 'round_box'); ?>                   </div>                 <?php } ?>                                  <?php if ($s5_pos_middle_bottom_3 == "published") { ?>
                                                    <div id="s5_pos_middle_bottom_3" class="s5_float_left"
                                                         style="width:<?php echo $s5_pos_middle_bottom_3_width ?>%">                     <?php s5_module_call('middle_bottom_3', 'round_box'); ?>                   </div>                 <?php } ?>                                  <?php if ($s5_pos_middle_bottom_4 == "published") { ?>
                                                    <div id="s5_pos_middle_bottom_4" class="s5_float_left"
                                                         style="width:<?php echo $s5_pos_middle_bottom_4_width ?>%">                     <?php s5_module_call('middle_bottom_4', 'round_box'); ?>                   </div>                 <?php } ?>                                  <?php if ($s5_pos_middle_bottom_5 == "published") { ?>
                                                    <div id="s5_pos_middle_bottom_5" class="s5_float_left"
                                                         style="width:<?php echo $s5_pos_middle_bottom_5_width ?>%">                     <?php s5_module_call('middle_bottom_5', 'round_box'); ?>                   </div>                 <?php } ?>                                  <?php if ($s5_pos_middle_bottom_6 == "published") { ?>
                                                    <div id="s5_pos_middle_bottom_6" class="s5_float_left"
                                                         style="width:<?php echo $s5_pos_middle_bottom_6_width ?>%">                     <?php s5_module_call('middle_bottom_6', 'round_box'); ?>                   </div>                 <?php } ?>
                                                <div style="clear:both; height:0px"></div>
                                            </div>
                                        </div>
                                    </div>            <?php } ?>                    </div>
                        </div>
                        <!-- Left column --> <?php if ($s5_pos_left == "published" || $s5_pos_left_inset == "published" || $s5_pos_left_top == "published" || $s5_pos_left_bottom == "published") { ?>
                            <div id="s5_left_column_wrap" class="s5_float_left"
                                 style="width:<?php echo $s5_left_column_width ?>px">
                                <div
                                    id="s5_left_column_wrap_inner">             <?php if ($s5_pos_left_top == "published") { ?>
                                        <div id="s5_left_top_wrap" class="s5_float_left"
                                             style="width:<?php echo $s5_left_column_width ?>px">                 <?php s5_module_call('left_top', 'round_box'); ?>               </div>             <?php } ?>             <?php if ($s5_pos_left == "published") { ?>
                                        <div id="s5_left_wrap" class="s5_float_left"
                                             style="width:<?php echo $s5_left_width ?>px">                 <?php s5_module_call('left', 'round_box'); ?>               </div>             <?php } ?>             <?php if ($s5_pos_left_inset == "published") { ?>
                                        <div id="s5_left_inset_wrap" class="s5_float_left"
                                             style="width:<?php echo $s5_left_inset_width ?>px">                 <?php s5_module_call('left_inset', 'round_box'); ?>               </div>             <?php } ?>             <?php if ($s5_pos_left_bottom == "published") { ?>
                                        <div id="s5_left_bottom_wrap" class="s5_float_left"
                                             style="width:<?php echo $s5_left_column_width ?>px">                 <?php s5_module_call('left_bottom', 'round_box'); ?>               </div>             <?php } ?>
                                </div>
                            </div>         <?php } ?> <!-- End Left column -->
                        <!-- Right column --> <?php if ($s5_pos_right == "published" || $s5_pos_right_inset == "published" || $s5_pos_right_top == "published" || $s5_pos_right_bottom == "published") { ?>
                            <div id="s5_right_column_wrap" class="s5_float_left"
                                 style="width:<?php echo $s5_right_column_width ?>px; margin-left:-<?php echo $s5_right_column_width + $s5_left_column_width ?>px">
                                <div
                                    id="s5_right_column_wrap_inner">             <?php if ($s5_pos_right_top == "published") { ?>
                                        <div id="s5_right_top_wrap" class="s5_float_left"
                                             style="width:<?php echo $s5_right_column_width ?>px">                 <?php s5_module_call('right_top', 'round_box'); ?>               </div>             <?php } ?>             <?php if ($s5_pos_right_inset == "published") { ?>
                                        <div id="s5_right_inset_wrap" class="s5_float_left"
                                             style="width:<?php echo $s5_right_inset_width ?>px">                 <?php s5_module_call('right_inset', 'round_box'); ?>               </div>             <?php } ?>             <?php if ($s5_pos_right == "published") { ?>
                                        <div id="s5_right_wrap" class="s5_float_left"
                                             style="width:<?php echo $s5_right_width ?>px">                 <?php s5_module_call('right', 'round_box'); ?>               </div>             <?php } ?>             <?php if ($s5_pos_right_bottom == "published") { ?>
                                        <div id="s5_right_bottom_wrap" class="s5_float_left"
                                             style="width:<?php echo $s5_right_column_width ?>px">                 <?php s5_module_call('right_bottom', 'round_box'); ?>               </div>             <?php } ?>
                                </div>
                            </div>         <?php } ?> <!-- End Right column -->         </div>
                </div>       <!-- End columns wrap -->
                <!-- Below Columns Wrap --> <?php if ($s5_pos_below_columns_1 == "published" || $s5_pos_below_columns_2 == "published" || $s5_pos_below_columns_3 == "published" || $s5_pos_below_columns_4 == "published" || $s5_pos_below_columns_5 == "published" || $s5_pos_below_columns_6 == "published") { ?>
                    <div id="s5_below_columns_wrap1">
                        <div id="s5_below_columns_wrap2">
                            <div id="s5_below_columns_inner"
                                 class="s5_wrap">                         <?php if ($s5_pos_below_columns_1 == "published") { ?>
                                    <div id="s5_below_columns_1" class="s5_float_left"
                                         style="width:<?php echo $s5_pos_below_columns_1_width ?>%">                 <?php s5_module_call('below_columns_1', 'round_box'); ?>               </div>             <?php } ?>                          <?php if ($s5_pos_below_columns_2 == "published") { ?>
                                    <div id="s5_below_columns_2" class="s5_float_left"
                                         style="width:<?php echo $s5_pos_below_columns_2_width ?>%">                 <?php s5_module_call('below_columns_2', 'round_box'); ?>               </div>             <?php } ?>                          <?php if ($s5_pos_below_columns_3 == "published") { ?>
                                    <div id="s5_below_columns_3" class="s5_float_left"
                                         style="width:<?php echo $s5_pos_below_columns_3_width ?>%">                 <?php s5_module_call('below_columns_3', 'round_box'); ?>               </div>             <?php } ?>                          <?php if ($s5_pos_below_columns_4 == "published") { ?>
                                    <div id="s5_below_columns_4" class="s5_float_left"
                                         style="width:<?php echo $s5_pos_below_columns_4_width ?>%">                 <?php s5_module_call('below_columns_4', 'round_box'); ?>               </div>             <?php } ?>                          <?php if ($s5_pos_below_columns_5 == "published") { ?>
                                    <div id="s5_below_columns_5" class="s5_float_left"
                                         style="width:<?php echo $s5_pos_below_columns_5_width ?>%">                 <?php s5_module_call('below_columns_5', 'round_box'); ?>               </div>             <?php } ?>                          <?php if ($s5_pos_below_columns_6 == "published") { ?>
                                    <div id="s5_below_columns_6" class="s5_float_left"
                                         style="width:<?php echo $s5_pos_below_columns_6_width ?>%">                 <?php s5_module_call('below_columns_6', 'round_box'); ?>               </div>             <?php } ?>
                                <div style="clear:both; height:0px"></div>
                            </div>
                        </div>
                    </div>       <?php } ?> <!-- End Below Columns Wrap -->                           </div>
        </div>
    </div>     <?php } ?> <!-- End Center area -->
<!-- Bottom Row1 --> <?php if ($s5_pos_bottom_row1_1 == "published" || $s5_pos_bottom_row1_2 == "published" || $s5_pos_bottom_row1_3 == "published" || $s5_pos_bottom_row1_4 == "published" || $s5_pos_bottom_row1_5 == "published" || $s5_pos_bottom_row1_6 == "published") { ?>
    <div id="s5_bottom_row1_area1">
        <div id="s5_bottom_row1_area2">
            <div id="s5_bottom_row1_area_inner" class="s5_wrap">
                <div id="s5_bottom_row1_wrap">
                    <div id="s5_bottom_row1">
                        <div
                            id="s5_bottom_row1_inner">                        <?php if ($s5_pos_bottom_row1_1 == "published") { ?>
                                <div id="s5_pos_bottom_row1_1" class="s5_float_left"
                                     style="width:<?php echo $s5_pos_bottom_row1_1_width ?>%">                 <?php s5_module_call('bottom_row1_1', 'round_box'); ?>               </div>             <?php } ?>                          <?php if ($s5_pos_bottom_row1_2 == "published") { ?>
                                <div id="s5_pos_bottom_row1_2" class="s5_float_left"
                                     style="width:<?php echo $s5_pos_bottom_row1_2_width ?>%">                 <?php s5_module_call('bottom_row1_2', 'round_box'); ?>               </div>             <?php } ?>                          <?php if ($s5_pos_bottom_row1_3 == "published") { ?>
                                <div id="s5_pos_bottom_row1_3" class="s5_float_left"
                                     style="width:<?php echo $s5_pos_bottom_row1_3_width ?>%">                 <?php s5_module_call('bottom_row1_3', 'round_box'); ?>               </div>             <?php } ?>                          <?php if ($s5_pos_bottom_row1_4 == "published") { ?>
                                <div id="s5_pos_bottom_row1_4" class="s5_float_left"
                                     style="width:<?php echo $s5_pos_bottom_row1_4_width ?>%">                 <?php s5_module_call('bottom_row1_4', 'round_box'); ?>               </div>             <?php } ?>                          <?php if ($s5_pos_bottom_row1_5 == "published") { ?>
                                <div id="s5_pos_bottom_row1_5" class="s5_float_left"
                                     style="width:<?php echo $s5_pos_bottom_row1_5_width ?>%">                 <?php s5_module_call('bottom_row1_5', 'round_box'); ?>               </div>             <?php } ?>                          <?php if ($s5_pos_bottom_row1_6 == "published") { ?>
                                <div id="s5_pos_bottom_row1_6" class="s5_float_left"
                                     style="width:<?php echo $s5_pos_bottom_row1_6_width ?>%">                 <?php s5_module_call('bottom_row1_6', 'round_box'); ?>               </div>             <?php } ?>
                            <div style="clear:both; height:0px"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>     <?php } ?> <!-- End Bottom Row1 --> <?php if ($s5_pos_custom_2 == "published") { ?>
    <div
        id="s5_googlemapit_wrapper">               <?php s5_module_call('custom_2', 'notitle'); ?>           </div>       <?php } ?>
<!-- Bottom Row2 --> <?php if ($s5_pos_bottom_row2_1 == "published" || $s5_pos_bottom_row2_2 == "published" || $s5_pos_bottom_row2_3 == "published" || $s5_pos_bottom_row2_4 == "published" || $s5_pos_bottom_row2_5 == "published" || $s5_pos_bottom_row2_6 == "published") { ?>
    <div id="s5_bottom_row2_area1">
        <div id="s5_bottom_row2_area2">
            <div id="s5_bottom_row2_area_inner" class="s5_wrap">
                <div id="s5_bottom_row2_wrap">
                    <div id="s5_bottom_row2">
                        <div
                            id="s5_bottom_row2_inner">                     <?php if ($s5_pos_bottom_row2_1 == "published") { ?>
                                <div id="s5_pos_bottom_row2_1" class="s5_float_left"
                                     style="width:<?php echo $s5_pos_bottom_row2_1_width ?>%">               <?php s5_module_call('bottom_row2_1', 'round_box'); ?>             </div>           <?php } ?>                      <?php if ($s5_pos_bottom_row2_2 == "published") { ?>
                                <div id="s5_pos_bottom_row2_2" class="s5_float_left"
                                     style="width:<?php echo $s5_pos_bottom_row2_2_width ?>%">               <?php s5_module_call('bottom_row2_2', 'round_box'); ?>             </div>           <?php } ?>                      <?php if ($s5_pos_bottom_row2_3 == "published") { ?>
                                <div id="s5_pos_bottom_row2_3" class="s5_float_left"
                                     style="width:<?php echo $s5_pos_bottom_row2_3_width ?>%">               <?php s5_module_call('bottom_row2_3', 'round_box'); ?>             </div>           <?php } ?>                      <?php if ($s5_pos_bottom_row2_4 == "published") { ?>
                                <div id="s5_pos_bottom_row2_4" class="s5_float_left"
                                     style="width:<?php echo $s5_pos_bottom_row2_4_width ?>%">               <?php s5_module_call('bottom_row2_4', 'round_box'); ?>             </div>           <?php } ?>                      <?php if ($s5_pos_bottom_row2_5 == "published") { ?>
                                <div id="s5_pos_bottom_row2_5" class="s5_float_left"
                                     style="width:<?php echo $s5_pos_bottom_row2_5_width ?>%">               <?php s5_module_call('bottom_row2_5', 'round_box'); ?>             </div>           <?php } ?>                      <?php if ($s5_pos_bottom_row2_6 == "published") { ?>
                                <div id="s5_pos_bottom_row2_6" class="s5_float_left"
                                     style="width:<?php echo $s5_pos_bottom_row2_6_width ?>%">               <?php s5_module_call('bottom_row2_6', 'round_box'); ?>             </div>           <?php } ?>
                            <div style="clear:both; height:0px"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>     <?php } ?> <!-- End Bottom Row2 -->
<!-- Bottom Row3 --> <?php if ($s5_pos_bottom_row3_1 == "published" || $s5_pos_bottom_row3_2 == "published" || $s5_pos_bottom_row3_3 == "published" || $s5_pos_bottom_row3_4 == "published" || $s5_pos_bottom_row3_5 == "published" || $s5_pos_bottom_row3_6 == "published") { ?>
    <div id="s5_bottom_row3_area1">
        <div id="s5_bottom_row3_area2">
            <div id="s5_bottom_row3_area_inner" class="s5_wrap">
                <div id="s5_bottom_row3_wrap">
                    <div id="s5_bottom_row3">
                        <div
                            id="s5_bottom_row3_inner">                    <?php if ($s5_pos_bottom_row3_1 == "published") { ?>
                                <div id="s5_pos_bottom_row3_1" class="s5_float_left"
                                     style="width:<?php echo $s5_pos_bottom_row3_1_width ?>%">               <?php s5_module_call('bottom_row3_1', 'round_box'); ?>             </div>           <?php } ?>                      <?php if ($s5_pos_bottom_row3_2 == "published") { ?>
                                <div id="s5_pos_bottom_row3_2" class="s5_float_left"
                                     style="width:<?php echo $s5_pos_bottom_row3_2_width ?>%">               <?php s5_module_call('bottom_row3_2', 'round_box'); ?>             </div>           <?php } ?>                      <?php if ($s5_pos_bottom_row3_3 == "published") { ?>
                                <div id="s5_pos_bottom_row3_3" class="s5_float_left"
                                     style="width:<?php echo $s5_pos_bottom_row3_3_width ?>%">               <?php s5_module_call('bottom_row3_3', 'round_box'); ?>             </div>           <?php } ?>                      <?php if ($s5_pos_bottom_row3_4 == "published") { ?>
                                <div id="s5_pos_bottom_row3_4" class="s5_float_left"
                                     style="width:<?php echo $s5_pos_bottom_row3_4_width ?>%">               <?php s5_module_call('bottom_row3_4', 'round_box'); ?>             </div>           <?php } ?>                      <?php if ($s5_pos_bottom_row3_5 == "published") { ?>
                                <div id="s5_pos_bottom_row3_5" class="s5_float_left"
                                     style="width:<?php echo $s5_pos_bottom_row3_5_width ?>%">               <?php s5_module_call('bottom_row3_5', 'round_box'); ?>             </div>           <?php } ?>                      <?php if ($s5_pos_bottom_row3_6 == "published") { ?>
                                <div id="s5_pos_bottom_row3_6" class="s5_float_left"
                                     style="width:<?php echo $s5_pos_bottom_row3_6_width ?>%">               <?php s5_module_call('bottom_row3_6', 'round_box'); ?>             </div>           <?php } ?>
                            <div style="clear:both; height:0px"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>     <?php } ?> <!-- End Bottom Row3 -->         <!-- Footer Area -->
<div id="s5_footer_area1">
    <div id="s5_footer_area2">
        <div id="s5_footer_area_inner" class="s5_wrap">
            <div id="s5_bottom_wrap_border"></div> <?php if ($s5_pos_bottom_menu) { ?>
                <div
                    id="s5_bottom_menu_wrap">           <?php s5_module_call('bottom_menu', 'notitle'); ?>         </div>         <?php } ?>
            <div style="clear:both; height:0px"></div> <?php if ($s5_pos_footer == "published") { ?>
                <div
                    id="s5_footer_module">           <?php s5_module_call('footer', 'notitle'); ?>         </div>         <?php } else { ?>
                <div id="s5_footer">           <?php require("vertex/footer.php"); ?>         </div>       <?php } ?>
            <div style="clear:both; height:0px"></div>
        </div>
    </div>
</div>   <!-- End Footer Area --> <?php s5_module_call('debug', 'fourdivs'); ?>
<!-- Bottom Vertex Calls --> <?php require("vertex/includes/vertex_includes_bottom.php"); ?> </div>
<!-- End Body Padding -->
<script type="text/javascript" src="https://w12756.yclients.com/widgetJS" charset="UTF-8"></script>
</body>
</html>