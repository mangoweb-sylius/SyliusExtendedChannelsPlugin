const path = require('path');
const Encore = require('@symfony/webpack-encore');

const syliusBundles = path.resolve(__dirname, '../../vendor/sylius/sylius/src/Sylius/Bundle/');
const uiBundleScripts = path.resolve(syliusBundles, 'UiBundle/Resources/private/js/');
const uiBundleResources = path.resolve(syliusBundles, 'UiBundle/Resources/private/');

// Main config that creates manifest.json at root
Encore
  .setOutputPath('public/build/')
  .setPublicPath('/build')
  .addEntry('shop-entry', '../../vendor/sylius/sylius/src/Sylius/Bundle/ShopBundle/Resources/private/entry.js')
  .addEntry('admin-entry', '../../vendor/sylius/sylius/src/Sylius/Bundle/AdminBundle/Resources/private/entry.js')
  .disableSingleRuntimeChunk()
  .cleanupOutputBeforeBuild()
  .enableSourceMaps(!Encore.isProduction())
  .enableVersioning(Encore.isProduction())
  .enableSassLoader();

const mainConfig = Encore.getWebpackConfig();

mainConfig.resolve.alias['sylius/ui'] = uiBundleScripts;
mainConfig.resolve.alias['sylius/ui-resources'] = uiBundleResources;
mainConfig.resolve.alias['sylius/bundle'] = syliusBundles;
mainConfig.externals = Object.assign({}, mainConfig.externals, { window: 'window', document: 'document' });
mainConfig.name = 'main';

module.exports = mainConfig;
