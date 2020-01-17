const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */
    mix.styles([
        'bootstrap.min.css',
        'bootstrap-select.min.css',
        'plugins/dataTables/dataTables.bootstrap.css',
        'plugins/dataTables/dataTables.responsive.css',
        'plugins/dataTables/dataTables.tableTools.min.css',
		//'animate.css',
        'style.css',
        'jquery-ui.min.css',
        'plugins/datepicker/datepicker3.css',
    ], './public/css/all.css')
    .scripts([
        'jquery-2.1.1.js',
        'bootstrap.min.js',
        'plugins/metisMenu/jquery.metisMenu.js',
        'plugins/slimscroll/jquery.slimscroll.min.js',
        'plugins/jeditable/jquery.jeditable.js',
        'autoNumeric.min.js',
        'bootstrap-select.min.js',
        'plugins/datepicker/bootstrap-datepicker.js',
        'plugins/dataTables/jquery.dataTables.min.js',
        'plugins/dataTables/dataTables.bootstrap.min.js',
        'plugins/dataTables/dataTables.responsive.min.js',
        'inspinia.js',
        'plugins/pace/pace.min.js',
    ], './public/js/all.js')
    .scripts([
        'poli.js',
        'fotozoom.js',
        'togglepanel.js',
        'resepjson.js',
        'informasi_obat.js',
        'riwobs.js',
        'uk.js'
    ], './public/js/allpoli.js');


