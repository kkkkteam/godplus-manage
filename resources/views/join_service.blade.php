<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>God+ Service</title>

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
            #serviceForm{
                max-width: 600px;
                width:100%;
            }

            .friend{
                display: flex;
                flex-direction: row;
                justify-content:flex-start;
                margin-bottom:5px;
            }

            .friend .friendName{
                width:40%;
                min-width:250px;
            }
            .friend .friendAge{
                width:30%;
                min-width:150px;
            }

            @media screen and (max-width: 600px) {
                .friend .friendName{
                    min-width:140px;
                }
                .friend .friendAge{
                    min-width:100px;
                }
                label{
                    font-size:.8em;
                }
            }

            .friend .is_newcomer{
               transform: translateY(90%);
            }
            
            .friend input,
            .friend select{
                margin-right: 5px;
            }

            .friend label{
                margin-right: 5px;
                white-space: nowrap;
            }

            button{
                width:120px;
            }

        </style>
    </head>
    <body class="font-sans antialiased dark:bg-black dark:text-white/50 center">
        <form id="serviceForm">
            <h3>參加 God Plus崇拜<img src="{{ url('images/logo.png') }}" style="width:100px;"></h3>
            <h5>參加者資料</h5>
            <div class="name-input">
                <div>
                    <label for="name">名字*</label>
                    <input type="text" id="name" name="name" required>
                    <span id="name_error" class="error"></span>
                </div>
                <div>
                    <label for="mobile">電話*</label>
                    <input type="text" id="mobile" name="mobile" value="{{$mobile}}" required>
                    <span id="mobile_error" class="error"></span>
                </div>
            </div>

            <div>
                <label for="service">參予場次*</label>
                <select class="service" id="service" name="service" required>
@if (isset($serviceList) and !empty($serviceList))
                    <option value=''>請選擇</option>
@foreach ($serviceList as $service)
                    <option value="{{$service["slug"]}}">{{$service["start_at"] ?? ""}} 《{{$service["title"] ?? ""}}》</option>
@endforeach
@endif
                </select>
            </div>

            <hr>
            <h5>有家人/朋友一齊嗎？</h5>
            <fieldset id="friendsSection">
                <div class="friend">
                    <div>
                        <label for="friendName">姓名:</label>
                        <input type="text" class="friendName" name="friendName" required>
                    </div>
                    <div>
                        <label for="friendage">佢係：</label>
                        <select class="friendAge" name="friendAge" required>
                            <option value=''>請選擇</option>
                            <option value="bady">0-3歲</option>
                            <option value="kindergarten">幼稚園學生</option>
                            <option value="primary">小學</option>
                            <option value="junior-high-school">初中</option>
                            <option value="high-school">高中</option>
                            <option value="college">大專/大學</option>
                            <option value="adult">在職</option>
                            <option value="elderly">長者</option>
                        </select>
                    </div>
                    <div>
                        <label for="is_newcomer">是新朋友？</label>
                        <input type="checkbox" class="is_newcomer" name="is_newcomer" >
                    </div>
                </div>
            </fieldset>
                
            <button type="button" onclick="addFriend()" style="margin-top:20px;">加添朋友</button><br>
            <small>新朋友: 指參加GodPlus聚會不超過五次。</small>
            <hr>
            <button type="button" class="btn btn-primary" onclick="submitForm()">Submit</button><br>
            <small>*為必需填寫。</small>
        </form>
        
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js?v=4"></script>
        <script type="text/javascript">
            var errorNum = 0;

            function submitForm() {
                // Get form values
                errorNum = 0;
                var mobileInput = document.getElementById('mobile');
                var nameInput = document.getElementById('name');
                var service = document.getElementById('service').value;

                var name = nameInput.value;
                var mobile = mobileInput.value.trim();

                document.querySelectorAll('.error').forEach(el => {
                    el.textContent = "";
                });

                if (!name) {
                    document.getElementById('nameError').textContent = '不能留空';
                    errorNum = errorNum+1;
                }

                const mobileRegex = /^\d{8}$/;    // Validate mobile number
                if (!mobileRegex.test(mobile)) {
                    document.getElementById('mobileError').textContent = '電話必需為8位數字';
                    errorNum = errorNum+1;
                }

                if(errorNum > 0){
                    return;
                }

                // Retrieve friend information
                const friends = [];
                const friendInputs = document.getElementsByClassName('friend');
                for (let i = 0; i < friendInputs.length; i++) {
                    const friendName = friendInputs[i].getElementsByClassName('friendName')[0].value.trim();
                    const friendAge = friendInputs[i].getElementsByClassName('friendAge')[0].value.trim();
                    const is_newcomer = friendInputs[i].getElementsByClassName('is_newcomer')[0].checked;
                    friends.push({
                        name: friendName,
                        age: friendAge,
                        is_newcomer: is_newcomer
                    });
                }

                const formData = {
                    name:name,
                    mobile:mobile,
                    service:service,
                    friends:friends
                };

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
				$.ajax({
					type: "POST",
					data: formData,
                    dataType: "json",
					url: "{{ route('member.service.register.api') }}",
					success: function (result)  {
                        if(result.status == 0){
                            alert(result.message);
                            window.location.href = result.url;
                        }else{
                            // reload the current page
                            alert(result.error);
                            window.location.reload();
                        }
					},
					error: function (XMLHttpRequest, textStatus, errorThrown)  {
						// alert("Oops...\n#"+textStatus+": "+errorThrown);
					}
				});
            }
        
            function addFriend() {
                const friendsSection = document.getElementById('friendsSection');
                const friendDiv = document.createElement('div');
                friendDiv.classList.add('friend');

                const nameDiv = document.createElement('div');
                const nameInput = document.createElement('input');
                nameInput.type = 'text';
                nameInput.classList.add('friendName');
                nameInput.setAttribute('name','friendName');
                nameInput.required = true;
                nameDiv.appendChild(nameInput);

                const ageDiv = document.createElement('div');
                const ageSelect = document.createElement('select');
                ageSelect.classList.add('friendAge');
                ageSelect.setAttribute('name','friendAge');
                ageSelect.innerHTML = `
                    <option value=''>請選擇</option>
                    <option value="bady">0-3歲</option>
                    <option value="kindergarten">幼稚園學生</option>
                    <option value="primary">小學</option>
                    <option value="junior-high-school">初中</option>
                    <option value="high-school">高中</option>
                    <option value="college">大專/大學</option>
                    <option value="adult">在職</option>
                    <option value="elderly">長者</option>`;
                ageSelect.required = true;
                ageDiv.appendChild(ageSelect);

                const newcomerDiv = document.createElement('div');
                const newcomerCheckbox = document.createElement('input');
                newcomerCheckbox.type = 'checkbox';
                newcomerCheckbox.classList.add('is_newcomer');
                newcomerCheckbox.setAttribute('name','is_newcomer');
                newcomerDiv.appendChild(newcomerCheckbox);

                friendDiv.appendChild(nameDiv);
                friendDiv.appendChild(ageDiv);
                friendDiv.appendChild(newcomerDiv);

                friendsSection.appendChild(friendDiv);
            }
        
        </script>

    </body>
</html>