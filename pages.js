var page = require('webpage').create();
page.paperSize = {
  format: "A4",
  orientation: "portrait",
  margin: {left:"0.2cm", right:"0.2cm", top:"1cm", bottom:"1cm"},
  footer: {
    contents: phantom.callback(function(pageNum, numPages) {
      return "<div style='text-align:center;'><small>" + pageNum +
        " / " + numPages + "</small></div>";
    })
  }
};
page.zoomFactor = 0.65;
page.open(phantom.args[0], function () {
    page.render(phantom.args[1]);
    phantom.exit();
});