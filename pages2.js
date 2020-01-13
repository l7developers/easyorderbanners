var page = require('webpage').create(),
    system = require('system'),
    address, output, size, pageWidth, pageHeight;


    address = system.args[1];
    output = system.args[2];
    page.viewportSize = { width: 1200, height: 1400 };
    if (system.args.length > 3 && system.args[2].substr(-4) === ".pdf") {
        size = system.args[3].split('*');
        page.paperSize = size.length === 2 ? { format: "A4", orientation: 'portrait', margin: {left:"0.2cm", right:"0.2cm", top:"1cm", bottom:"1cm"} } : { format: "A4", orientation: 'portrait', margin: {left:"0.2cm", right:"0.2cm", top:"1cm", bottom:"1cm"} };
                              
    } 
    page.open(address, function (status) {
        if (status !== 'success') {
            console.log('Unable to load the address!');
            phantom.exit(1);
        } else {
            window.setTimeout(function () {
                page.render(output);
                phantom.exit();
            }, 200);
        }
    });
/*var page = require('webpage').create();
page.paperSize = {
  format: "a4",
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
page.open('https://staging.easyorderbanners.com/invoice_receipt/7567', function () {
    page.render(phantom.args[1]);
    phantom.exit();
});*/