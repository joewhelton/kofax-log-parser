/**
 * Created by Joe Whelton on 18/06/14.
 */
function initialiseDataTable(getUrl, dataTable){
    $('#output').html('<table id="outputTable"><thead><tr id="filterRow"><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th></tr><tr><th>batchName</th><th>batchType</th><th>batchScanDate</th><th>avgPerPageRecognition</th><th>batchDocs</th><th>batchPages</th><th>batchPDFDate</th><th>batchPDFStartTime</th><th>batchPDFStopTime</th><th>avgPerPagePDF</th><th>avgExportPerPage</th><th>batchExportDate</th></tr></thead><tbody></tbody></table>');
    $.get(getUrl, function (data, dataTable) {
        dataTable = $('#outputTable').DataTable({
            "data": data,
            "columns": [
                { "data": "batchName", "title": "Batch Name" },
                { "data": "batchType", "title": "Type"},
                { "data": "batchScanDate", "title": "Scan Date", "type": "date" },
                { "data": "avgPerPageRecognition", "title": "Recog Secs/Page" },
                { "data": "batchDocs", "title": "#Docs" },
                { "data": "batchPages", "title": "#Pages" },
                { "data": "batchPDFDate", "title": "PDF Date", "type": "date" },
                { "data": "batchPDFStartTime", "title": "PDF Start" },
                { "data": "batchPDFStopTime", "title": "PDF Stop" },
                { "data": "avgPerPagePDF", "title": "PDF Secs/Page" },
                { "data": "avgExportPerPage", "title": "Export Secs/Page"},
                { "data": "batchExportDate", "title": "Export Date"}
            ],
            "columnDefs": [{
                "targets": 9,
                "render": function ( data, type, full, meta ) {
                    return (parseFloat(data).toFixed(4));
                }
            },{
                "targets": 3,
                "render": function ( data, type, full, meta ) {
                    return (parseFloat(data).toFixed(4));
                }
            },{
                "targets": 10,
                "render": function ( data, type, full, meta ) {
                    return (parseFloat(data).toFixed(4));
                }
            }
            ],
            dom: 'T<"clear">lfrtip',
            "tableTools": {
                "sSwfPath": "./swf/copy_csv_xls_pdf.swf"
            },
            "lengthMenu": [ [10, 50, 100, -1], [10, 50, 100, "All"] ],
            "pageLength": 50,
            "search": {
                "regex": true
            }
        } );
        $("#filterRow th").each( function ( i ) {
            if(i == 1 || i == 2 || i == 6 || i == 11){
                var select = $('<select multiple><option value=".*">(All)</option></select>')
                    .appendTo( $(this).empty() )
                    .on( 'change', function () {
                        var selectValue = $(this).val();
                        if (selectValue.constructor !== Array){
                            searchRegex = selectValue;
                        } else {
                            var searchRegex = '(';
                            for(k=0; k<selectValue.length; k++ ){
                                searchRegex += selectValue[k];
                                searchRegex += '|';
                            }
                            searchRegex = searchRegex.substr(0, searchRegex.length - 1) + ')';
                        }
                        dataTable.column( i )
                            .search( '^'+searchRegex+'$', true, false )
                            .draw();
                        calculateAverages(dataTable);
                    } );

                dataTable.column( i ).data().unique().sort().each( function ( d, j ) {
                    select.append( '<option value="'+d+'">'+d+'</option>' )
                } );
            }
        } );
        calculateAverages(dataTable);
    }, "json");
}
function calculateAverages(dataTable){
    var totalCount = 0;
    var validCount = 0;
    var totalTime = 0;
    var totalDocs = 0;
    var totalPages = 0;

    var recogArray = [];
    var exportArray = [];
    var pdfArray = [];

    /*** Average Recognition queue time per page**/
    dataTable
        .column( 3, { filter: 'applied' } )
        .data()
        .each( function ( value, index ) {
            totalCount++;
            if($.isNumeric(value) && value > 0 ){
                recogArray.push(value);
                validCount++;
                totalTime += value;
            }
        } );
    $('#spnAverageRecog').html(parseFloat(totalTime/validCount).toFixed(3));
    validCount = 0;
    totalTime = 0;

    /***80% median mean calculation***/
	/*
    recogArray.sort(function(a, b){return a-b});
    for(k = Math.round(recogArray.length / 10); k < Math.round(recogArray.length / 10 * 9); k++ ){
        validCount++;
        totalTime += recogArray[k];
    }
    $('#spnMeanRecog').html(parseFloat(totalTime/validCount).toFixed(3));
    validCount = 0;
    totalTime = 0;*/

    /*** Average Export queue time per page**/
    dataTable
        .column( 10, { filter: 'applied' } )
        .data()
        .each( function ( value, index ) {
            if($.isNumeric(value) && value > 0 ){
                exportArray.push(value);
                validCount++;
                totalTime += value;
            }
        } );
    $('#spnAverageExport').html(parseFloat(totalTime/validCount).toFixed(3));
    validCount = 0;
    totalTime = 0;

    /***80% median mean calculation***/
	/*
    exportArray.sort(function(a, b){return a-b});
    for(k = Math.round(exportArray.length / 10); k < Math.round(exportArray.length / 10 * 9); k++ ){
        validCount++;
        totalTime += exportArray[k];
    }
    $('#spnMeanExport').html(parseFloat(totalTime/validCount).toFixed(3));
    validCount = 0;
    totalTime = 0;*/

    dataTable
        .column( 9, { filter: 'applied' } )
        .data()
        .each( function ( value, index ) {
            if($.isNumeric(value) && value > 0 ){
                pdfArray.push(value);
                validCount++;
                totalTime += value;
            }
        } );
    $('#spnAveragePDF').html(parseFloat(totalTime/validCount).toFixed(3));
    validCount = 0;
    totalTime = 0;

    /*pdfArray.sort(function(a,b){return a-b});
    for(k = Math.round(pdfArray.length / 10); k < Math.round(pdfArray.length / 10 * 9); k++){
        validCount++;
        totalTime += pdfArray[k];
    }
    $('#spnMeanPDF').html(parseFloat(totalTime/validCount).toFixed(3));
    validCount = 0;
    totalTime = 0;*/

    dataTable
        .column( 4, { filter: 'applied' } )
        .data()
        .each( function ( value, index ) {
            if($.isNumeric(value) && value > 0){
                validCount++;
                totalDocs += Number(value, 2);
            }
        } );
    $('#spnAverageDocs').html(parseFloat(totalDocs/validCount).toFixed(1));
    validCount = 0;

    dataTable
        .column( 5, { filter: 'applied' } )
        .data()
        .each( function ( value, index ) {
            if($.isNumeric(value) && value > 0){
                validCount++;
                totalPages += Number(value);
            }
        } );
    $('#spnAveragePages').html(parseFloat(totalPages/validCount).toFixed(1));
    $('#spnTotalBatches').html(totalCount);
    $('#loadingGif').fadeOut(100, function(){
        $('#output').fadeIn(100);
    });


}

