<?php
/*
Plugin Name: Loja WP PagSeguro
Version: 1.0
Plugin URI: https://github.com/vitorhugoro1/loja-wp-pagseguro
Description:
Author: Vitor Hugo R Merencio (Polyvenn)
Author URI: https://github.com/vitorhugoro1/
*/

define('LOJA_ROOT', plugin_dir_path( __FILE__ ));
define('LOJA_ASSETS', LOJA_ROOT . 'assets/');

require LOJA_ROOT . 'pagseguro/vendor/autoload.php';
require LOJA_ROOT . 'cmb2/init.php';
require LOJA_ROOT . 'cmb2-conditionals/cmb2-conditionals.php';
require LOJA_ROOT . 'cmb2-date-range/wds-cmb2-date-range-field.php';
require LOJA_ROOT . 'class-pagseguro-config.php';
require LOJA_ROOT . 'class-pager-template.php';
require LOJA_ROOT . 'class-post-types.php';
require LOJA_ROOT . 'class-user.php';
require LOJA_ROOT . 'class-screens.php';
require LOJA_ROOT . 'class-helpers.php';
require LOJA_ROOT . 'class-post-categories.php';
require LOJA_ROOT . 'class-meta-boxes.php';
require LOJA_ROOT . 'class-ingresso-functions.php';
require LOJA_ROOT . 'class-eventos-setup.php';
require LOJA_ROOT . 'class-loja-init.php';
require LOJA_ASSETS . 'load-assets.php';
