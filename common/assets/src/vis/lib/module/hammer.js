// Only load hammer.js when in a browser environment
// (loading hammer.js in a node.js environment gives errors)
if (typeof window !== 'undefined') {
  var propagating = require('propagating-hammerjs');
  var Hammer = window['Hammer'] || require('hammerjs');
  module.exports = propagating(Hammer, {
    // https://github.com/almende/vis/issues/2525 -> vis+jquery draggable issue
    // preventDefault: 'mouse'
  });
}
else {
  module.exports = function () {
    throw Error('hammer.js is only available in a browser, not in node.js.');
  }
}
