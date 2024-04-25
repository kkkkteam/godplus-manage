<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>QR Code Scanner</title>
        <script src="{{ url('/public/js/html5-qrcode.min.js') }}"></script>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <link rel="stylesheet" href="{{ url('/public/css/style.css')}}">
        
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+TC:wght@100..900&display=swap" rel="stylesheet">
        <style>
            span{
                font-size:1rem;
                color: #555;
                font-weight: 500;
            }
            video{
                max-height:50vh;
                width:100vw;
            }
        </style>
    </head>
    <body>
        <img src="{{ url('/public/images/logo.png') }}" style="width:25px"><span>QR code scanner</span>
        <div style="width: 100vw;" id="reader"></div>
        <div id="table-container">
            <!-- Your related table content here -->
            <div class='row'>
                <div class=' col-sm-12'>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" data-name="cool-table" id="dataTable">
                            <thead><tr>
                                <th>參加者</th>
                                <th>出席</th>
                            </tr></thead>
                            <tfoot><tr>
                                <th></th>
                                <th></th>
                                <th></th>
                            </tr></tfoot>
                        </table>

                    </div>
                </div>
            </div>
        </div>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js?v=4"></script>
        <link rel="stylesheet" href="https://cdn.datatables.net/2.0.5/css/dataTables.dataTables.css" />
        <script src="https://cdn.datatables.net/2.0.5/js/dataTables.js"></script>
        <script type="text/javascript">
                function onScanSuccess(decodedText, decodedResult) {
                    // Handle on success condition with the decoded text or result.
                    document.getElementById("result").innerHTML = decodedText;
                    event.preventDefault();
                }
                
                var html5QrcodeScanner = new Html5QrcodeScanner(
                    "reader", { fps: 10, qrbox: 250 });
                html5QrcodeScanner.render(onScanSuccess);

                $(document).ready(function()  {

                    $("#dataTable tfoot th").each(function()  {
                        var title = $(this).text();
                        $(this).html('<input type="text" placeholder="'+title+'" style="width:100%;"/>');
                    });

                    _table = $("#dataTable").DataTable({
                        info: true,
                        paging: fale,
                        ordering: true,
                        autoWidth: true,
                        searching: false,
                        lengthChange: false,
                        buttons: ["csv", "excel", "copy"],
                        order: [[0, "desc"]],
                        dom: "frtip",

                        ajax: {
                            url: '{{ route("admin.service.attendance.list.api")}}',
                        },

                        columnDefs: [
                            {targets:0, width:"20px"},
                            {targets:1, data:null, width:"40px", defaultContent:
                                "<input type="checkbox" id=date name=data checked />"
                            },
                        ],

                    });

                    _table.columns().every(function()  {
                        var that = this;
                        $('input', this.footer()).on('keyup change clear', function()  {
                            if (that.search() !== this.value)  {
                                that
                                .search( this.value )
                                .draw();
                            }
                        });
                    });
                });
        </script>
    </body>
</html>