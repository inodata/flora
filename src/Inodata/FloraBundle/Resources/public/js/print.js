// Install JSPrintSetup
function installjsPrintSetup() {
    //if (confirm("You don't have printer plugin.\nDo you want to install the Printer Plugin now?")) {
    if (confirm("No tienes instalado el plugin de la impresora.\nQuieres instalarlo ahora?")) {
        var xpi = new Object();
        xpi['jsprintsetup'] = '/downloads/jsprintsetup-0.9.2.xpi';
        InstallTrigger.install(xpi);
    }
}

//Define paper size 
//39 : {PD:39, PN: 'na_fanfold-us',PWG:'na_fanfold-us_11x14.875in',Name: 'US Std Fanfold', W: 11, H: 14.875, M: kPaperSizeInches}
function definePaperSizes(){
  //note
  jsPrintSetup.definePaperSize(99, 99, 'na_fanfold-us', 'na_fanfold-us_24.3x14cm', 'Medium Letter Fanfold', 243, 140, jsPrintSetup.kPaperSizeMillimeters);
  //card
  jsPrintSetup.definePaperSize(98, 98, 'na_fanfold-us', 'na_fanfold-us_11x14cm', 'Card Fanfold', 110, 140, jsPrintSetup.kPaperSizeMillimeters);
}

function setupGlobalOptions(){
  //check if jsPrintSetup is installed
  if (typeof(jsPrintSetup) == 'undefined') {
          installjsPrintSetup();
  } else {
    // set page orientation.
    jsPrintSetup.setOption('orientation', jsPrintSetup.kPortraitOrientation);
    //jsPrintSetup.setOption('orientation', jsPrintSetup.kLandscapeOrientation);

    // set margins (in millimeters, firefox defaults 12.7mm).
    jsPrintSetup.setOption('marginTop', 0);
    jsPrintSetup.setOption('marginBottom', 0);
    jsPrintSetup.setOption('marginLeft', 0);
    jsPrintSetup.setOption('marginRight', 0);

    // set page header
    jsPrintSetup.setOption('headerStrLeft', '');
    jsPrintSetup.setOption('headerStrCenter', '');
    jsPrintSetup.setOption('headerStrRight', '');
    // set empty page footer
    jsPrintSetup.setOption('footerStrLeft', '');
    jsPrintSetup.setOption('footerStrCenter', '');
    jsPrintSetup.setOption('footerStrRight', '');

    definePaperSizes();

    // clears user preferences always silent print value
    // to enable using 'printSilent' option
    jsPrintSetup.clearSilentPrint();
    // Suppress print dialog (for this context only)
    jsPrintSetup.setOption('printSilent', 1);

  }
}

// Do Print 
// When print is submitted it is executed asynchronous and
// script flow continues after print independently of completetion of print process! 
//  jsPrintSetup.print();
function printCard(){
  setupGlobalOptions();
  jsPrintSetup.setPrinter('LaserJet-M5035-MFP');
  jsPrintSetup.setPaperSizeData(98);
  jsPrintSetup.print();
}

function printNote(){
  setupGlobalOptions();
  jsPrintSetup.setPrinter('LaserJet-M5035-MFP');
  jsPrintSetup.setPaperSizeData(99);
  jsPrintSetup.print();
}