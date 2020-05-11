// Require path.
const path = require( 'path' );
const DependencyExtractionWebpackPlugin = require( '@wordpress/dependency-extraction-webpack-plugin' );

// Configuration object.
const config = {
    // Create the entry points.
    // One for frontend and one for the admin area.
    entry: {
        // frontend and admin will replace the [name] portion of the output config below.
        //frontend: './js_src/front/front-index.js',
        settings: './js_src/admin/settings.js',
        metabox: './js_src/admin/metabox.js',
        gutenberg: './js_src/admin/gutenberg.js',
        notice: './js_src/admin/notice.js'
    },

    // Create the output files.
    // One for each of our entry points.
    output: {
        // [name] allows for the entry object keys to be used as file names.
        filename: '[name].js',
        // Specify the path to the JS files.
        path: path.resolve( __dirname, 'js' )
    },

    // Setup a loader to transpile down the latest and great JavaScript so older browsers
    // can understand it.
    module: {
        rules: [
            {
                // Look for any .js files.
                test: /\.js$/,
                // Exclude the node_modules folder.
                exclude: /node_modules/,
                // Use babel loader to transpile the JS files.
                loader: 'babel-loader',
                options: {
                    presets: ["@babel/preset-env"]
                }
            },
            {
                test: /\.css$/i,
                use: ['style-loader', 'css-loader'],
            },
        ]
    },
    plugins: [
        new DependencyExtractionWebpackPlugin(),
    ]
    // optimization: {
    //     minimizer: [
    //         new UglifyJsPlugin({
    //             uglifyOptions: {
    //                 output: {
    //                     comments: /\<\/?fs_premium_only\>/i,
    //                 },
    //             },
    //             extractComments: true,
    //         }),
    //     ],
    // },
};

// Export the config object.
module.exports = config;
