var elixir = require('laravel-elixir');

elixir(function(mix) {
    
    	mix.styles([
        "font-awesome.css",
        "ionicons.css",
        "bootstrap/css/bootstrap.css",
        "bootstrap/css/bootstrap.vertical-tabs.css",
        "dist/css/AdminLTE.css",
        "dist/css/skins/_all-skins.css",
         "plugins/iCheck/flat/blue.css",
         "plugins/iCheck/all.css",
         "plugins/morris/morris.css",
         "plugins/jvectormap/jquery-jvectormap-1.2.2.css",
         "plugins/datepicker/datepicker3.css",
         "plugins/daterangepicker/daterangepicker-bs3.css",
         "plugins/datatables/dataTables.bootstrap.css",
         "plugins/jquery.qtip.custom/jquery.qtip.css"
         
        ]);

   
   mix.scripts([
   		"plugins/jQuery/jQuery-2.2.0.min.js",
   		"plugins/jQueryUI/jquery-ui.min.js",
        "bootstrap.js",
        "plugins/fastclick/fastclick.js",
        "plugins/flot/jquery.flot.min.js",
        "plugins/flot/jquery.flot.resize.min.js",
        "plugins/flot/jquery.flot.pie.min.js",
        "plugins/flot/jquery.flot.categories.min.js",
        "plugins/raphael-min.js",
        "plugins/morris/morris.js",
        "plugins/sparkline/jquery.sparkline.min.js",
        "plugins/jvectormap/jquery-jvectormap-1.2.2.min.js",
        "plugins/jvectormap/jquery-jvectormap-world-mill-en.js",
        "plugins/knob/jquery.knob.js",
        "plugins/moment.min.js",
        "plugins/daterangepicker/daterangepicker.js",
        "plugins/datepicker/bootstrap-datepicker.js",
        "plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js",
        "plugins/slimScroll/jquery.slimscroll.min.js",
        "plugins/chartjs/Chart.min.js",
        "plugins/datatables/jquery.dataTables.min.js",
        "plugins/datatables/dataTables.bootstrap.min.js",
        "plugins/iCheck/icheck.min.js",
        "plugins/jquery.qtip.custom/jquery.qtip.min.js",
        "clipboard.min.js",
        "app.js",
        "demo.js",
        
    ]);

  mix.version(["css/all.css", "js/all.js"]);

  
   
});
