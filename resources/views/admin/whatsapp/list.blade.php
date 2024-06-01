<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Whatsapp</title>

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
            textarea{
                width:100%;
            }

            form{
                max-width: 100%;
            }

            .updateButton,
            .deleteButton{
                width: 150px;
            }

            .dt-search{
                padding-bottom:5px;
            }
        </style>
    </head>
    <body class="font-sans antialiased dark:bg-black dark:text-white/50 center">
<!-- @include('admin.layout.menu') -->
        <form id="commandForm">
            <h3>GodPlus+ | Whatsapp List</h3>
            <div>
                <label for="command">弟兄姊妹需輸入:</label>
                <input type="text" id="command" name="command" required>
            </div>
            <div class="name-input">
                <div>
                    <label for="reply">回覆信息: ( 電話: __MOBILE__ ｜參加的崇拜: __SERVICE__)</label>
                    <textarea type="text" id="reply" name="reply" required></textarea>
                </div>
                <div>
                    <label for="reply_with_name">回覆信息[會友]: ( 會友名字:__NAME__ | 電話: __MOBILE__ ｜參加的崇拜: __SERVICE__)</label>
                    <textarea type="text" id="reply_with_name" name="reply_with_name" required></textarea>
                </div>
            </div>
            <button type="button" class="btn btn-primary" onclick="addCommand()">Add 新回應</button><br>
            <hr>

            <div class='row'>
			<div class=' col-sm-12'>
				<div class="table-responsive">

					<table class="table table-bordered table-hover" data-name="cool-table" id="dataTable">
						<thead><tr>
							<th>No.</th>
                            <th>弟兄姊妹輸入</th>
							<th>信息回應</th>
							<th>信息回應 [GodPlus+會友]</th>
                            <th>Action</th>
						</tr></thead>
						<tfoot><tr>
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

        <form id="commandForm" style="margin-top:10px;background:#fcfbf0;">
            <h3>GodPlus+ | 崇拜點名 Whatsapp</h3>
            <!-- <div class="name-input">
                <div>
                    <label for="welcome_times">到來次數:( 1:第一次來，2:第二次，如此類推，如有range用「-」表示，例如3-5｜0:default message)</label>
                    <input type="text" id="welcome_times" name="welcome_times" required></input>
                </div>
                <div>
                    <label for="welcome_message">回覆信息: ( 報名名字:__NAME__ )</label>
                    <textarea type="text" id="welcome_message" name="welcome_message" required></textarea>
                </div>
            </div>
            <button type="button" class="btn btn-primary" onclick="addWelcomeMessage()">Add 歡迎訊息</button><br>
            <hr> -->

            <div class='row'>
			<div class=' col-sm-12'>
				<div class="table-responsive">

					<table class="table table-bordered table-hover" data-name="cool-table2" id="dataTable2">
						<thead><tr>
							<th>No.</th>
							<th>到來次數</th>
                            <th>訊息內容</th>
                            <th>Action</th>
						</tr></thead>
						<tfoot><tr>
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

            function addCommand(){

                var command = document.getElementById("command").value;
                var reply = document.getElementById("reply").value;
                var reply_with_name = document.getElementById("reply_with_name").value;

                var data = {
                    command:command,
                    reply:reply,
                    reply_with_name:reply_with_name,
                };

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    type: "POST",
                    data: data,
                    dataType: "json",
                    url: "{{ route('admin.command.set.api') }}",
                    success: function (result)  {
                        if (result.status == 0)  {
                            _table.ajax.reload(null, false);
                        }
                    },
                    error: function (XMLHttpRequest, textStatus, errorThrown)  {
                        alert("Oops...\n#"+textStatus+": "+errorThrown);
                    }
                });
            }

            $(document).ready(function()  {

                // whatsapp list ----------------------------------------------
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
                    order: [[0, "asc"]],
                    dom: "Bfrtip",

                    ajax: {
                        url: '{{ route("admin.command.get.list.api")}}',
                    },

                    columnDefs: [
                        {targets:0, width:"20px"},
                        {targets:4, data:null, width:"180px", defaultContent:
                            "<button class='btn btn-sm btn-success updateButton'><i class='fa fa-envelope-o'></i> Update</button><br>"+
                            "<button class='btn btn-sm btn-danger deleteButton'><i class='fa fa-trash-o'></i> Delete</button>"
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

                //  Action buttons
                $("#dataTable tbody").on("click", "button.updateButton", function()  {
                    var data = _table.row($(this).parents("tr")).data();
                    var id = data[0];
                    var parameters = {
                        id:id,
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
                        url: "{{ route('admin.command.update.action') }}",
                        success: function (result)  {
                            if (result.status == 0)  {
                                window.location.href = result.url;
                            }
                        },
                        error: function (XMLHttpRequest, textStatus, errorThrown)  {
                            alert("Oops...\n#"+textStatus+": "+errorThrown);
                        }
                    });
                    return false;
                });

                $("#dataTable tbody").on("click", "button.deleteButton", function()  {
                    var data = _table.row($(this).parents("tr")).data();
                    var id = data[0];
                    var parameters = {
                        id:id
                    };

                    var url = "{{ route('admin.command.delete.api') }}";
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.ajax({
                        type: "POST",
                        dataType: "json",
                        data: parameters,
                        url: url,
                        success: function (result)  {
                            if (result.status == 0)  {
                                _table.ajax.reload(null, false);
                                alert("Deleted.");
                            }
                        },
                        error: function (XMLHttpRequest, textStatus, errorThrown)  {
                            alert("Oops...\n#"+textStatus+": "+errorThrown);
                        }
                    });
                    return false;
                });


                // Welcome Message list ----------------------------------------------
                $("#dataTable2 tfoot th").each(function()  {
                    var title = $(this).text();
                    $(this).html('<input type="text" placeholder="'+title+'" style="width:100%;"/>');
                });

                _table = $("#dataTable2").DataTable({
                    info: true,
                    paging: true,
                    ordering: true,
                    autoWidth: true,
                    searching: true,
                    lengthChange: false,
                    buttons: ["csv", "excel", "copy"],
                    order: [[0, "asc"]],
                    dom: "Bfrtip",

                    ajax: {
                        url: '{{ route("admin.command.get.welcome.list.api")}}',
                    },

                    columnDefs: [
                        {targets:0, width:"20px"},
                        {targets:1, width:"180px"},
                        {targets:3, data:null, width:"200px", defaultContent:
                            "<button class='btn btn-sm btn-success updateButton2'><i class='fa fa-envelope-o'></i> Update</button>"
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

                //  Action buttons
                $("#dataTable tbody").on("click", "button.updateButton2", function()  {
                    var data = _table.row($(this).parents("tr")).data();
                    var id = data[0];
                    var parameters = {
                        id:id,
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
                        url: "{{ route('admin.command.welcome.update.action') }}",
                        success: function (result)  {
                            if (result.status == 0)  {
                                window.location.href = result.url;
                            }
                        },
                        error: function (XMLHttpRequest, textStatus, errorThrown)  {
                            alert("Oops...\n#"+textStatus+": "+errorThrown);
                        }
                    });
                    return false;
                });

            });
       
        </script>

    </body>
</html>