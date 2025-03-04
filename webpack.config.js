const Encore = require('@symfony/webpack-encore');

// Manually configure the runtime environment if not already configured yet by the "encore" command.
// It's useful when you use tools that rely on webpack.config.js file.
if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    // directory where compiled assets will be stored
    .setOutputPath('public/build/')
    // public path used by the web server to access the output path
    .setPublicPath('/build')
    // only needed for CDN's or subdirectory deploy
    //.setManifestKeyPrefix('build/')

    /*
     * ENTRY CONFIG
     *
     * Each entry will result in one JavaScript file (e.g. app.js)
     * and one CSS file (e.g. app.css) if your JavaScript imports CSS.
     */
    .addEntry('app', './assets/app.js')
    .addEntry('bootstrap', './assets/bootstrap.js')

    //layouts
    .addEntry('layout_main', './assets/js/layouts/main.js')

    //pages
    .addEntry('page_home', './assets/js/pages/home.js')
    .addEntry('page_login', './assets/js/pages/login.js')
    .addEntry('page_register', './assets/js/pages/register.js')
    .addEntry('page_profile','./assets/js/pages/profile.js')
    .addEntry('page_profile_edit','./assets/js/pages/profile_edit.js')

    .addEntry('page_new_story','./assets/js/pages/new_story.js')

    .addEntry('admin_user','./assets/js/admin/users.js')
    // When enabled, Webpack "splits" your files into smaller pieces for greater optimization.
    .splitEntryChunks()

    // will require an extra script tag for runtime.js
    // but, you probably want this, unless you're building a single-page app
    .enableSingleRuntimeChunk()

    /*
     * FEATURE CONFIG
     *
     * Enable & configure other features below. For a full
     * list of features, see:
     * https://symfony.com/doc/current/frontend.html#adding-more-features
     */
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    // enables hashed filenames (e.g. app.abc123.css)
    .enableVersioning(Encore.isProduction())

    // configure Babel
    // .configureBabel((config) => {
    //     config.plugins.push('@babel/a-babel-plugin');
    // })

    // enables and configure @babel/preset-env polyfills
    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = 'usage';
        config.corejs = '3.38';
    })

    // enables Sass/SCSS support
    .enableSassLoader()

    // uncomment if you use TypeScript
    .enableTypeScriptLoader()

    // uncomment if you use React
    //.enableReactPreset()

    // uncomment to get integrity="..." attributes on your script & link tags
    // requires WebpackEncoreBundle 1.4 or higher
    .enableIntegrityHashes(Encore.isProduction())

    // uncomment if you're having problems with a jQuery plugin
    //.autoProvidejQuery()

    .copyFiles({
        from: './assets/favicons',
        to: 'favicons/[path][name].[ext]',
    })
    .copyFiles({
        from: './assets/images',
        to: 'images/[path][name].[hash:8].[ext]',
    })
    .copyFiles({
        from: './assets/fonts',
        to: 'fonts/[path][name].[hash:8].[ext]',
    })
;

module.exports = Encore.getWebpackConfig();
