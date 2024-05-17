<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <!-- <meta http-equiv="refresh" content="15"> -->

        <title>Registration List</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <link rel="stylesheet" href="/css/style.css">
        <!-- <link rel="stylesheet" href="{{ url('/public/css/style.css')}}"> -->

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
            select{
                width: 300px;
            }
            @media screen and (max-width: 600px) {
                .dt-search{
                    display:none;
                }
                select{
                    width: 250px;
                }
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
            .already_marked{
                color :red;
                font-weight:600;
            }
        </style>
    </head>
    <body class="font-sans antialiased dark:bg-black dark:text-white/50 center">
        <form id="commandForm">
            <h3>GodPlus+ 崇拜報名登記（招待專用）</h3>
            <div class="name-input">
                <div>
                    <label for="service">檢視聚會場次</label>
                    <select class="service" id="service" name="service" required>
@if (isset($serviceList) and !empty($serviceList))
@foreach ($serviceList as $service)
                        <option value="{{$service["slug"]}}">{{$service["start_at"] ?? ""}} 《{{$service["title"] ?? ""}}》</option>
@endforeach
@endif
                    </select>
                    <button type="button" class="btn btn-info" onclick="selectServiceView()">Select</button><br>
                </div>
            </div>
            <div style="margin-top:10px;"><button type="button" class="btn btn-success" onclick="downloadQRcode()">Download：點名QR code</button></div>
            <hr>
            
            <div class='row'>
            <p>已到場人數：<span id="people" style="font-weight:600;color:red;"></span></p>
                <div class='col-sm-12'>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" data-name="cool-table" id="dataTable2">
                            <thead><tr>
                                <th>No.</th>
                                <th>參加者</th>
                                <th>背景</th>
                                <th>是新朋友？</th>
                                <th>同行者</th>
                                <th>到達時間</th>
                            </tr></thead>
                            <tfoot><tr>
                                <th>Search</th>
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
        <link rel="stylesheet" href="https://cdn.datatables.net/2.0.5/css/dataTables.bootstrap4.min.css" />
        <script src="https://cdn.datatables.net/2.0.5/js/dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/2.0.5/js/dataTables.bootstrap5.min.js"></script>
        <link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.0.2/css/buttons.bootstrap5.min.css" />
        <script src="https://cdn.datatables.net/buttons/3.0.2/js/dataTables.buttons.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.bootstrap5.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.html5.min.js"></script>
        
        <script type="text/javascript">
            var attendedCount = 0;
            $(document).ready(function()  {
                
                $("#dataTable2 tfoot th").each(function()  {
                    var title = $(this).text();
                    $(this).html('<input type="text" placeholder="'+title+'" style="width:100%;"/>');
                });
                attendedCount = 0;
                _table = $("#dataTable2").DataTable({
                    info: true,
                    paging: true,
                    ordering: true,
                    autoWidth: true,
                    searching: true,
                    pageLength: 80,
                    lengthChange: false,
                    buttons: ["csv", "excel", "copy"],
                    order: [[0, "asc"]],
                    dom: "Bfrtip",
                    
                    ajax: {
                        url: '{{ route("admin.service.registration.list.details.api")}}',
                        data:  function(data)  {
                            data.slug = $("#service").val();
                        }
                    },

                    columnDefs: [
                        {targets:0, width:"10px"},
                        {targets:1, width:"150px"},
                        {targets:2, width:"200px"},
                        {targets:5,
                            render: function (data, type, row, meta) {
                                if (data == ""){
                                    return `<button class="btn btn-sm btn-warning attendButton" >報到</button>`; 
                                }else{
                                    return data; 
                                }
                            },
                        }
                    ],

                    createdRow: function (row, data, dataIndex) {
                        if (data[6] == true) {
                            $(row).addClass('already_marked');
                            attendedCount++;
                        }
                        document.getElementById("people").innerHTML = attendedCount;
                    },

                });

                _table.columns().every(function()  {
                    var that = this;
                    $('input', this.footer()).on('keyup change clear', function()  {
                        if (that.search() !== this.value)  {
                            that.search( this.value ).draw();
                        }
                    });
                });

                $("#dataTable2 tbody").on("click", "button.attendButton", function()  {
              
                    var data = _table.row($(this).parents("tr")).data();
                    var id = data[7];
                    var slug = $("#service").val();
                    var parameters = {
                        id:id,
                        slug:slug,
                    };

                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.ajax({
                        type: "POST",
                        dataType: "json",
                        data: parameters,
                        url: "{{ route('admin.service.registration.individual.attend.api')}}",
                        success: function (result)  {
                            if (result.status == 0)  {
                                _table.ajax.reload(null, false);
                            }
                        },
                        error: function (XMLHttpRequest, textStatus, errorThrown)  {
                            alert("Oops...\n#"+textStatus+": "+errorThrown);
                        }
                    });
                    return false;
                });
            });

            function selectServiceView(){
                var slug = $("#service").val();
                var url = "?slug="+slug;
                window.history.pushState(null, null, url);
                _table.ajax.reload(null, false);
            }

            function downloadQRcode(){
                var slug = $("#service").val();
                var parameters = {
                        slug:slug,
                };
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    type: "get",
                    dataType: "json",
                    data: parameters,
                    url: "{{ route('admin.service.download.qrcode.api')}}",
                    success: function (result)  {

                    },
                    error: function (XMLHttpRequest, textStatus, errorThrown)  {
                        alert("Oops...\n#"+textStatus+": "+errorThrown);
                    }
                });
                return false;
            }
        </script>
    </body>
</html>