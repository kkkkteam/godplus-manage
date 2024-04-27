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
        <div id="show-list"></div>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js?v=4"></script>
        <link rel="stylesheet" href="https://cdn.datatables.net/2.0.5/css/dataTables.dataTables.css" />
        <script src="https://cdn.datatables.net/2.0.5/js/dataTables.js"></script>
        <script type="text/javascript">
                var _scaning = 0;
                function onScanSuccess(decodedText, decodedResult) {
                    // Handle on success condition with the decoded text or result.
                    $.ajax({
                        type: "POST",
                        data: {code:decodedText},
                        dataType: "json",
                        url: '{{ route("admin.service.scan.show.registration.api") }}',
                        success: function (result)  {
                            if (result.status == 0)  {
                                _scaning = 1;
                                document.getElementById("show-list").innerHTML = result.html+`<button type="button" class="btn btn-primary" onclick="submit()">Confirm</button>`;
                            }
                        },
                        error: function (XMLHttpRequest, textStatus, errorThrown)  {
                            alert("Oops...\n#"+textStatus+": "+errorThrown);
                        }
                    });
                    event.preventDefault();
                }
                
                var html5QrcodeScanner = new Html5QrcodeScanner(
                    "reader", { fps: 10, qrbox: 250 });
                if (_scaning == 0 ){html5QrcodeScanner.render(onScanSuccess);}

                function submit(){
                    _scaning = 0;
                }
        </script>
    </body>
</html>