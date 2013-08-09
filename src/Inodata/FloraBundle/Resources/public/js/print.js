// Install JSPrintSetup
function installjsPrintSetup() {
    if (confirm("No tienes instalado el plugin de la impresora.\nQuieres instalarlo ahora?")) {
        var xpi = new Object();
        xpi['jsprintsetup'] = '/bundles/inodataflora/downloads/jsprintsetup-0.9.2.xpi';
        InstallTrigger.install(xpi);
    }
}

//This function was useless handling medium letter size.
//Define paper size 
//39 : {PD:39, PN: 'na_fanfold-us',PWG:'na_fanfold-us_11x14.875in',Name: 'US Std Fanfold', W: 11, H: 14.875, M: kPaperSizeInches}
function definePaperSizes(){
  //note
  jsPrintSetup.definePaperSize(50, 50, 'na_letter', 'na_letter_8.5x11in', 'US Letter', 8.5, 5.5, jsPrintSetup.kPaperSizeInches);
  //card
  jsPrintSetup.definePaperSize(51, 51, 'na_letter', 'na_letter_8.5x11in', 'US Letter', 4.7, 5.5, jsPrintSetup.kPaperSizeInches);
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

    //definePaperSizes();
    
    /* debugoptions */
    //jsPrintSetup.setPaperSizeData(1);
    //jsPrintSetup.setOption ("paperWidth", 216);
    //jsPrintSetup.setOption ("paperHeight", 140);
    //jsPrintSetup.setPaperSizeData(100);
    //alert(jsPrintSetup.getPaperSizeList());
    //alert(jsPrintSetup.getPrintersList());

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
// jsPrintSetup.print();
// add a delay to render correctly all elements fetched via AJAX
// setTimeout('jsPrintSetup.print()', print_delay);
var print_delay = 3000;

/**
* Gets the printer list in array and select the proper printer.
* If it's installed as network printer or local printer this function
* selects "\\pc-name\printerX" or "printerX" respectively.
* @param printer <String> Printer name, this can be a local or shared printer name
* @return void
*/
function setPrinter(printer){
  printersList = jsPrintSetup.getPrintersList();
  printersList = printersList.split(",");
  
  for (var i=0; i<printersList.length; i++){
    if(printersList[i].indexOf(printer) != -1){
      jsPrintSetup.setPrinter(printersList[i]);
      return true;
    }
  }

  alert('No se encontro la impresora' + ': ' +printer);
  return false;
}

function printCard(){
  setupGlobalOptions();
  if(setPrinter(card_printer)){
	  setTimeout('jsPrintSetup.print()', print_delay);
  }
}

function printNote(){
  setupGlobalOptions();
  if(setPrinter(note_printer)){
	  setTimeout('jsPrintSetup.print()', print_delay);
  }
}

function printInvoice(){
  setupGlobalOptions();
  if(setPrinter(invoice_printer)){
	  setTimeout('jsPrintSetup.print()', print_delay);
  }
  setTimeout('jsPrintSetup.print()', print_delay);
}

//TODO: Revisar la impresion de esta lista y seleccionar impresora.
function printDistributionList(){
  setupGlobalOptions();
  setPrinter(invoice_printer);
  setTimeout('jsPrintSetup.print()', print_delay);
}