const path = require('path')
const webpackConfig = require('@nextcloud/webpack-vue-config')

webpackConfig.entry.emptyContent = path.resolve(path.join('src', 'emptyContent.js'))
webpackConfig.entry.submit = path.resolve(path.join('src', 'submit.js'))
webpackConfig.entry.settings = path.resolve(path.join('src', 'settings.js'))
webpackConfig.entry.workflow = path.resolve(path.join('src', 'workflow.js'))

module.exports = webpackConfig
