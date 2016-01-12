<!DOCTYPE html>
<html>
	<head>
        <link rel="stylesheet" type="text/css" href="css/default.css" >
        <link rel="stylesheet" type="text/css" href="css/jquery-ui-1.10.4.custom.css">
        <link rel="stylesheet" type="text/css" href="css/jquery.dataTables_themeroller.css">
        <link rel="stylesheet" type="text/css" href="css/dataTables.tableTools.css">

        <script type="text/javascript" src="./js/jquery-2.1.1.min.js"></script>
        <script type="text/javascript" src="./js/jquery.dataTables.js"></script>
        <script type="text/javascript" src="./js/dataTables.tableTools.js"></script>
        <script type="text/javascript" src="./js/jquery-ui-1.10.4.custom.js"></script>
        <script type="text/javascript" src="./js/dataTables.jqueryui.js"></script>
        <script type="text/javascript" src="./js/customDatatableFunctions.js"></script>

        <script type="text/javascript" language="javascript">

            $.fn.dataTable.TableTools.defaults.aButtons = [ "copy", "csv", "xls" ];
            var dataTable = "";

            $(document).ready(function(){
                getDefaultPath();
                getFileList();
            });
			function getFileList(){
                $.get( "controller.php?path=" + encodeURI($('#txtFilePath').val()), function( data ) {
                    $("#divDirectoryListing").html("");
                    for (var k = 0; k < data.length; k++)
                    $("#divDirectoryListing")
                        .append( "<a href='#' class='logNameLink' onclick='getLogByName(\"" + data[k] + "\");'>" + data[k] + "</a><br />" )
                }, "json" );
			}
            function getDefaultPath(){
                $.get( "controller.php?action=getpath", function( data ) {
                    $('#txtFilePath').val(data);
                });
            }
            function getLogByName(filename){
                $('#output').fadeOut(200, function(){
                    $('#loadingGif').fadeIn(100, function(){
                        var getUrl = "controller.php?action=getlog&filename=" + encodeURI(filename) + "&path=" + encodeURI($('#txtFilePath').val());
                        try{
                            dataTable.destroy();
                        } catch (err) {
                            //First init
                        }
                        $('#output').html("");
                        initialiseDataTable(getUrl, dataTable);
                    });
                });
            }
		</script>
	</head>
	<body>
        <div class="leftMenu">
            <fieldset>
                <legend>
                    File Path
                </legend>
                <input type="text" id="txtFilePath" name="txtFilePath" /><input type="button" value="Go" onclick="getFileList()" />
                <div id="divDirectoryListing">

                </div>
            </fieldset>

        </div>
        <div class="dataPane">
            <fieldset>
                <legend>
                    Data Pane
                </legend>
                <div id="dateRange" class="dateRange">

                </div>
                <fieldset id="statsPane">
                    <legend>Stats Pane</legend>
                    <div class="clearFix">&nbsp;</div>
                    <div id="averageDisplay" class="averageDisplay">
                        Average Recognition time per page: <span class="stats" id="spnAverageRecog"></span><br/>
                        &emsp;80% Median Mean: <span class="stats" id="spnMeanRecog"></span><br/>
                        Average PDF Processing time per page: <span class="stats" id="spnAveragePDF"></span><br/>
                        &emsp;80% Median Mean: <span class="stats" id="spnMeanPDF"></span><br/>
                        Average Export time per page: <span class="stats" id="spnAverageExport"></span><br/>
                        &emsp;80% Median Mean: <span class="stats" id="spnMeanExport"></span><br/>
                        Total Batches: <span class="stats" id="spnTotalBatches"></span><br/>
                        Average Docs/Pages per Batch: <span class="stats" id="spnAverageDocs"></span> / <span class="stats" id="spnAveragePages"></span>
                    </div>
                </fieldset>
                <div class="clearFix">
                    &nbsp;
                </div>
                <div id="output">

                </div>
                <div id="loadingGif" style="display: none;">
                    <img height="50" width="50" src="/logs/images/loading.gif">

                </div>
            </fieldset>
        </div>

	</body>
</html>