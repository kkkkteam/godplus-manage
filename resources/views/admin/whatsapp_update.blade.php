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

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+TC:wght@100..900&display=swap" rel="stylesheet">

        <style>
            textarea{
                width:100%;
            }

            h3{
                padding-bottom: 20px;
            }

            form{
                max-width: 100%;
            }
        </style>
    </head>
    <body class="font-sans antialiased dark:bg-black dark:text-white/50 center">
        <form id="updateForm">
            <h3>WhatsApp ID: {{ $whatsapp->id }} 更新</h3>
            <div>
                <label for="command">弟兄姊妹需輸入:</label>
                <input type="text" id="command" name="command" value="{{ $whatsapp->command }}" required>
            </div>
            <div class="name-input">
                <div>
                    <label for="reply">回覆信息:</label>
                    <textarea type="text" id="reply" name="reply" required>{{ $whatsapp->reply }}</textarea>
                </div>
                <div>
                    <label for="reply_with_name">回覆信息: (包括對方名字:__NAME__)</label>
                    <textarea type="text" id="reply_with_name" name="reply_with_name" required>{{ $whatsapp->reply_with_name }}</textarea>
                </div>
            </div>
            <button type="button" class="btn btn-primary" onclick="updateCommand()">update 回應</button><br>

        </form>
        
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js?v=4"></script>
        <link rel="stylesheet" href="https://cdn.datatables.net/2.0.5/css/dataTables.dataTables.css" />
        <script src="https://cdn.datatables.net/2.0.5/js/dataTables.js"></script>
        <script type="text/javascript">

            function updateCommand(){

                    var command = document.getElementById("command").value;
                    var reply = document.getElementById("reply").value;
                    var reply_with_name = document.getElementById("reply_with_name").value;

                    var data = {
                        id:'{{$whatsapp->id}}',
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
                        url: "{{ route('admin.command.update.api') }}",
                        success: function (result)  {
console.log(result)
                            if (result.status == 0)  {
                                alert("Updated");
                                window.location.href = result.url;
                            }
                        },
                        error: function (XMLHttpRequest, textStatus, errorThrown)  {
                            alert("Oops...\n#"+textStatus+": "+errorThrown);
                        }
                    });

                }
               
        </script>
    </body>
</html>