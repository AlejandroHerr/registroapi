/**
 * This file/module contains all configuration for the build process.
 */
module.exports = {
  /**
   * The `build_dir` folder is where our projects are compiled during
   * development and the `compile_dir` folder is where our app resides once it's
   * completely built.
   */
  build_dir: 'build',
  compile_dir: 'app',

  /**
   * This is a collection of file patterns that refer to our app code (the
   * stuff in `src_frontend/`). These file paths are used in the co nfiguration of
   * build tasks. `js` is all project javascript, less tests. `ctpl` contains
   * our reusable components' (`src_frontend/common`) template HTML files, while
   * `atpl` contains the same, but for our app's code. `html` is just our
   * main HTML file, `less` is our main stylesheet, and `unit` contains our
   * app's unit tests.
   */
  app_files: {
    js: [ 'src_frontend/**/*.js', '!src_frontend/**/*.spec.js', '!src_frontend/assets/**/*.js' ],
    jsunit: [ 'src_frontend/**/*.spec.js' ], 

    atpl: [ 'src_frontend/app/**/*.tpl.html' ],
    ctpl: [ 'src_frontend/common/**/*.tpl.html' ],

    html: [ 'src_frontend/index.html' ],
    less: 'src_frontend/less/main.less',
    html: [ 'src_frontend/less/*.css' ]
  },

  /**
   * This is the same as `app_files`, except it contains patterns that
   * reference vendor code (`vendorjs/`) that we need to place into the build
   * process somewhere. While the `app_files` property ensures all
   * standardized files are collected for compilation, it is the user's job
   * to ensure non-standardized (i.e. vendor-related) files are handled
   * appropriately in `vendor_files.js`.
   *
   * The `vendor_files.js` property holds files to be automatically
   * concatenated and minified with our project source files.
   *
   * The `vendor_files.css` property holds any CSS files to be automatically
   * included in our app.
   *
   * The `vendor_files.assets` property holds any assets to be copied along
   * with our app's assets. This structure is flattened, so it is not
   * recommended that you use wildcards.
   */
  vendor_files: {
    js: [
      'vendorjs/angular/angular.min.js',
      'vendorjs/angular-route/angular-route.min.js',
      'vendorjs/angular-bootstrap/ui-bootstrap-tpls.min.js',
      'vendorjs/angular-xeditable/dist/js/xeditable.min.js'
    ],
    css: [
    'src_frontend/less/*.css'

    ],
    assets: [

    ]
  },
};
