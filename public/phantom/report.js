var page = require('webpage').create();
page.paperSize = {
  format: "a3",
  orientation: "portrait",
  margin: {left:"1cm", right:"1cm", top:"1cm", bottom:"1cm"},
  footer: {
    contents: phantom.callback(function(pageNum, numPages) {
      return "<div style='text-align:center;'><small>" + pageNum +
        " / " + numPages + "</small></div>";
    })
  }
};
page.zoomFactor = 1;
page.open(phantom.args[0], function () {
    page.render(phantom.args[1]);
    phantom.exit();
});