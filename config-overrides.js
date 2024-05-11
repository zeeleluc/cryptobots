const { override } = require('customize-cra');
const { ProvidePlugin, DefinePlugin } = require('webpack'); // Import webpack modules
const dotenv = require('dotenv');

module.exports = override((config) => {
    // Load environment variables from .env files
    const envConfig = dotenv.config().parsed;
    if (envConfig) {
        Object.keys(envConfig).forEach((key) => {
            config.plugins.push(new DefinePlugin({ ['process.env.' + key]: JSON.stringify(envConfig[key]) }));
        });
    }

    // Add other customizations
    return {
        ...config,
        module: {
            ...config.module,
            rules: [
                ...config.module.rules,
                {
                    test: /\.m?[jt]sx?$/,
                    enforce: 'pre',
                    use: ['source-map-loader'],
                },
                {
                    test: /\.m?[jt]sx?$/,
                    resolve: {
                        fullySpecified: false,
                    },
                },
            ],
        },
        plugins: [
            ...config.plugins,
            new ProvidePlugin({
                process: 'process/browser',
            }),
        ],
        resolve: {
            ...config.resolve,
            fallback: {
                assert: require.resolve('assert'),
                buffer: require.resolve('buffer'),
                crypto: require.resolve('crypto-browserify'),
                http: require.resolve('stream-http'),
                https: require.resolve('https-browserify'),
                stream: require.resolve('stream-browserify'),
                url: require.resolve('url/'),
                zlib: require.resolve('browserify-zlib'),
            },
        },
        ignoreWarnings: [/Failed to parse source map/],
    };
});
