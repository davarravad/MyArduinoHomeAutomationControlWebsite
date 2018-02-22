<?php
/**
* Default Footer
*
* UserApplePie
* @author David (DaVaR) Sargent <davar@userapplepie.com>
* @version 4.0.0
*/

use Libs\Assets, Libs\Language;
?>

                <div class='col-lg-12 col-md-12 col-sm-12'>
                    <!-- Footer (sticky) -->
                    <footer class='navbar navbar-default'>
                        <div class='container'>
                            <div class='navbar-text'>

                                <!-- Footer links / text -->
        						<?=Language::show('uap_poweredby', 'Welcome');?> <a href='http://www.userapplepie.com' title='View UserApplePie Website' ALT='UserApplePie' target='_blank'>UserApplePie v4</a>

        						<!-- Display Copywrite stuff with auto year -->
        						<Br> &copy; <?php echo date("Y") ?> <?php echo SITE_TITLE;?> <?=Language::show('uap_all_rights', 'Welcome');?>.


                            </div>
                        </div>
                    </footer>
                </div>
            </div>
        </div><!-- /.container -->

        <?=Assets::js([
            'https://code.jquery.com/jquery-1.12.1.min.js',
            'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js',
            'https://cdn.rawgit.com/google/code-prettify/master/loader/run_prettify.js'
        ])?>
        <?=(isset($js)) ? $js : ""?>
        <?php if(isset($ownjs)){ foreach ($ownjs as $eachownjs) { echo "$eachownjs"; } } ?>
        <?=(isset($footer)) ? $footer : ""?>
    </body>
</html>
