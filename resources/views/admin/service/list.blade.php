<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Service</title>

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
                max-width: 1000px;
            }
            .updateButton{
                width:150px;
            }

            .dt-search{
                padding-bottom:5px;
            }
        </style>
    </head>
    <body class="font-sans antialiased dark:bg-black dark:text-white/50 center">
        <form id="commandForm">
            <h3>GodPlus+ 崇拜 list</h3>
            <div class="name-input">
                <div>
                    <label for="title">崇拜主題</label>
                    <input type="text" id="title" name="title" required>
                </div>
                <div>
                    <label for="speaker">講員</label>
                    <input type="text" id="speaker" name="speaker" required>
                </div>
            </div>
            <div class="name-input">
                <div>
                    <label for="meeting_date">日期</label>
                    <input type="date" id="meeting_date" name="meeting_date" required>
                </div>
                <div>
                    <label for="meeting_time">開始時間</label>
                    <input type="time" id="meeting_time" name="meeting_time" style="width:200px;padding:5px;" required>
                </div>
            </div>
            <button type="button" class="btn btn-primary" onclick="addService()" style="margin-top:20px;">Add 崇拜</button><br>
            <hr>

            <div class='row'>
			<div class=' col-sm-12'>
				<div class="table-responsive">

					<table class="table table-bordered table-hover" data-name="cool-table" id="dataTable">
						<thead><tr>
							<th>No.</th>
                            <th>slug</th>
                            <th>日期</th>
							<th>崇拜主題</th>
							<th>講員</th>
                            <th>Action</th>
						</tr></thead>
						<tfoot><tr>
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
        
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js?v=4"></script>
        <link rel="stylesheet" href="https://cdn.datatables.net/2.0.5/css/dataTables.dataTables.css" />
        <script src="https://cdn.datatables.net/2.0.5/js/dataTables.js"></script>
        <script type="text/javascript">

            function addService(){

                var title   = document.getElementById("title").value;
                var date    = document.getElementById("meeting_date").value;
                var time    = document.getElementById("meeting_time").value;
                var speaker = document.getElementById("speaker").value;

                var data = {
                    title:title,
                    start_date:date,
                    start_time:time,
                    speaker:speaker,
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
                    url: "{{ route('admin.service.add.api') }}",
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
                        url: '{{ route("admin.service.get.list.api")}}',
                    },

                    columnDefs: [
                        {targets:0, width:"20px"},
                        {targets:1, visible:false},
                        {targets:2, width:"200px"},
                        {targets:5, data:null, width:"180px", defaultContent:
                            "<button class='btn btn-sm btn-success updateButton'><i class='fa fa-envelope-o'></i> Update</button><br>"
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
                var slug = data[1];
                var parameters = {
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
                    url: "{{ route('admin.service.update.action') }}",
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