<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Summary</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <link rel="stylesheet" href="{{ url('/public/css/style.css')}}">
        <link rel="stylesheet" href="/css/style.css">

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+TC:wght@100..900&display=swap" rel="stylesheet">
        <style>
            form{
                max-width: 1000px;
                position: relative;
            }
            .dt-search{
                padding-bottom:5px;
            }
            red{
                color:#990000;
            }
        </style>
    </head>

    <body class="font-sans antialiased dark:bg-black dark:text-white/50 center">
@include('admin.layout.menu')
        <form id="summaryForm">
            <h3>GodPlus 崇拜 <red>統計</red></h3>
            <div class='row'>
                <div class=' col-sm-12'>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" data-name="cool-table" id="dataTable">
                            <thead><tr>
                                <th>No.</th>
                                <th>崇拜日期/時間</th>
                                <th>講題</th>
                                <th>講員</th>
                                <th>報名人數</th>
                                <th>出席人數</th>
                                <th>新朋友人數</th>
                            </tr></thead>
                            <tfoot><tr>
                                <th>Search</th>
                                <th>Search</th>
                                <th>Search</th>
                                <th>Search</th>
                                <th>Search</th>
                                <th>Search</th>
                                <th></th>
                            </tr></tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </form>
        <button id="people"  class="btn btn-primary summary-btn" onclick="goPoeple()">以會員排序</button>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js?v=4"></script>
        <link rel="stylesheet" href="https://cdn.datatables.net/2.0.5/css/dataTables.dataTables.css" />
        <script src="https://cdn.datatables.net/2.0.5/js/dataTables.js"></script>
        <script type="text/javascript">

            $(document).ready(function()  {

                $("#dataTable tfoot th").each(function()  {
                    var title = $(this).text();
                    $(this).html('<input type="text" placeholder="'+title+'" style="width:100%;"/>');
                });

                _table = $("#dataTable").DataTable({
                    info: true,
                    paging: true,
                    ordering: true,
                    autoWidth: true,
                    searching: true,
                    lengthChange: false,
                    buttons: ["csv", "excel", "copy"],
                    order: [[0, "desc"]],
                    dom: "Bfrtip",

                    ajax: {
                        url: '{{ route("admin.service.attendance.summary.api")}}',
                    },

                    columnDefs: [
                        {targets:0, width:"20px"},
                        {targets:1, width:"200px"},
                        {targets:2, width:"150px"},
                        {targets:5, visible:false},
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

                $("#dataTable tbody").on("click", "tr", function()  {
                    var table = $("#dataTable").DataTable();
                    var data = table.row(this).data();
                    var index = data.length-1;
                    var url = data[index];
                    location.href = url;
                });

            });

            function goPoeple(){
                location.href = '{{route("admin.service.attendance.by.poeple.html")}}';
            }

        </script>
    </body>
</html>