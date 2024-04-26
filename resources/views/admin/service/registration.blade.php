<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Registration List</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <link rel="stylesheet" href="/css/style.css">
        <link rel="stylesheet" href="{{ url('/public/css/style.css')}}">

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+TC:wght@100..900&display=swap" rel="stylesheet">

        <style>
            form{
                max-width: 800px;
            }
            .dt-search{
                padding-bottom:5px;
            }
            table{
                font-size: .8rem;
                font-weight: 400;
            }
            .table>:not(caption)>*>* {
                padding: .1em;
            }
            table.dataTable > tbody > tr > th, table.dataTable > tbody > tr > td{
                padding: 1px 4px;
            }
        </style>
    </head>
    <body class="font-sans antialiased dark:bg-black dark:text-white/50 center">
        <form id="commandForm">
            <h3>GodPlus+ 崇拜報名登記</h3>
            <div class="name-input">
                <div>
                    <label for="service">檢視聚會場次</label>
                    <select class="service" id="service" name="service" style="width:300px;" required>
@if (isset($serviceList) and !empty($serviceList))
@foreach ($serviceList as $service)
                        <option value="{{$service["slug"]}}">{{$service["start_at"] ?? ""}} 《{{$service["title"] ?? ""}}》</option>
@endforeach
@endif
                    </select>
                    <button type="button" class="btn btn-info" onclick="selectServiceView()">Select</button><br>
                </div>
            </div>
            <hr>
            
            <div class='row'>
                <div class='col-sm-12'>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" data-name="cool-table" id="dataTable2">
                            <thead><tr>
                                <th>No.</th>
                                <th>報名時間</th>
                                <th>參加者</th>
                                <th>是新朋友？</th>
                                <th>邀請者</th>
                            </tr></thead>
                            <tfoot><tr>
                                <th>Search</th>
                                <th>Search</th>
                                <th>Search</th>
                                <th>Search</th>
                                <th>Search</th>
                            </tr></tfoot>
                        </table>
                    </div>
                </div>
            </div>

        </form>
        
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js?v=4"></script>
        <link rel="stylesheet" href="https://cdn.datatables.net/2.0.5/css/dataTables.dataTables.css" />
        <script src="https://cdn.datatables.net/2.0.5/js/dataTables.js"></script>
        <script type="text/javascript">

            $(document).ready(function()  {
                
                $("#dataTable2 tfoot th").each(function()  {
                    var title = $(this).text();
                    $(this).html('<input type="text" placeholder="'+title+'" style="width:100%;"/>');
                });

                _table = $("#dataTable2").DataTable({
                    info: true,
                    paging: true,
                    ordering: true,
                    autoWidth: true,
                    searching: false,
                    lengthChange: false,
                    pageLength: 30,
                    buttons: ["csv", "excel", "copy"],
                    order: [
                        [0, "asc"]
                    ],
                    dom: "Bfrtip",
                    
                    ajax: {
                        url: '{{ route("admin.service.registration.list.api")}}',
                        data:  function(data)  {
                            data.slug = $("#service").val();
                        }
                    },

                    columnDefs: [
                        {targets:0, width:"10px"},
                        {targets:1, width:"150px"},
                        {targets:2, width:"300px"},
                    ],

                });

                _table.columns().every(function()  {
                    var that = this;
                    $('input', this.footer()).on('keyup change clear', function()  {
                        if (that.search() !== this.value)  {
                            that.search( this.value ).draw();
                        }
                    });
                });
            });

            function selectServiceView(){
                var slug = $("#service").val();
                var url = "?slug="+slug;
                window.history.pushState(null, null, url);
                _table.ajax.reload(null, false);
            }

        </script>
    </body>
</html>