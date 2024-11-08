const RemoveEmptyScriptsPlugin = require( 'webpack-remove-empty-scripts' );
const { DefinePlugin } = require( "webpack" );
const path = require("path");

const defaultConfig = require( "@wordpress/scripts/config/webpack.config" );

module.exports = function( { entry, output, plugins = [] } ) {
    return {
        ...defaultConfig,
        optimization: {
            ...defaultConfig.optimization,
            usedExports: process.env.NODE_ENV === "production",
        },
        entry,
        output: {
            ...defaultConfig.output,
            ...output,
        },
        module: {
            rules: [
                ...defaultConfig.module.rules,
                {
                    test: /\.js$/,
                    //exclude: /node_modules/,
                    use: [
                        {
                            loader: require.resolve('babel-loader'),
                            options: {
                                // Babel uses a directory within local node_modules
                                // by default. Use the environment variable option
                                // to enable more persistent caching.
                                cacheDirectory:
                                    process.env.BABEL_CACHE_DIRECTORY || true,
                            },
                        },
                    ]
                }
            ]
        },
        resolve: {
            alias: {
                'turbo-combine-reducers': path.resolve(__dirname, 'javascript/packages/combine-reducers/'),
            }
        },
        plugins: [
            ...defaultConfig.plugins.filter(
                ( plugin ) =>
                    plugin.constructor.name !== "DependencyExtractionWebpackPlugin" &&
                    plugin.constructor.name !== "DefinePlugin"
            ),
            new DefinePlugin( {
                // Inject the `process.env.NODE_DEBUG` global, used for development features flagging inside the `yoastseo` package.
                "process.env.NODE_DEBUG": JSON.stringify( process.env.NODE_DEBUG ),
                // Copied from WP config: Inject the `SCRIPT_DEBUG` global, used for development features flagging.
                SCRIPT_DEBUG: process.env.NODE_ENV !== "production",
            } ),
            new RemoveEmptyScriptsPlugin( {
                stage: RemoveEmptyScriptsPlugin.STAGE_AFTER_PROCESS_PLUGINS
            } ),
            ...plugins,
        ].filter( Boolean ),
    };
};
