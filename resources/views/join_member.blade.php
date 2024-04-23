<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>God+ JOIN</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <link rel="stylesheet" href="/css/style.css">

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+TC:wght@100..900&display=swap" rel="stylesheet">
    </head>
    <body class="font-sans antialiased dark:bg-black dark:text-white/50 center">
        <form id="applicationForm">
            <h2>加入 God Plus<img src="{{ url('images/logo.png') }}"></h2>
            <div class="name-input">
                <div>
                    <label for="surname_en">Surname (English):</label>
                    <input type="text" id="surname_en" name="surname_en">
                    <span id="surname_en_error" class="error"></span>
                </div>
                <div>
                    <label for="lastname_en">Lastname (English):</label>
                    <input type="text" id="lastname_en" name="lastname_en">
                    <span id="lastname_en_error" class="error"></span>
                </div>
            </div>
            <div class="name-input">
                <div>
                    <label for="surname_zh">名 (中文):*</label>
                    <input type="text" id="surname_zh" name="surname_zh" required>
                    <span id="surname_zh_error" class="error"></span>
                </div>
                <div>
                    <label for="lastname_zh">姓 (中文):*</label>
                    <input type="text" id="lastname_zh" name="lastname_zh" required>
                    <span id="lastname_zh_error" class="error"></span>
                </div>
            </div>

            <div style="width:49%;">
                <label for="nickname">平時點叫你😄</label>
                <input type="text" id="nickname" name="nickname" >
            </div>

            <div class="name-input">
                <div>
                    <label for="mobile">WhatsApp:*</label>
                    <input type="text" id="mobile" name="mobile" required>
                    <span id="mobileError" class="error"></span>
                </div>
                <div>
                    <label for="email">Email:*</label>
                    <input type="email" id="email" name="email" required>
                    <span id="EmailError" class="error"></span>
                </div>
            </div>
            <div class="name-input">
                <div>
                    <label for="birthday">出生日期:</label>
                    <input type="date" id="birthday" name="birthday" required>
                </div>
                <div>
                    <label for="gender">Gender:</label>
                    <select id="gender" name="gender" required>
                        <option value="">Select</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                    </select>
                </div>
            </div>
            <div>
                <label for="photo">Your Photo:</label>
                <input type="file" id="photo" name="photo" accept="image/*" capture>
            </div>
            <button type="button" class="btn btn-primary" onclick="submitForm()">Submit</button><br>
            <small>*為必需填寫。</small>
        </form>
        
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js?v=4"></script>
        <script type="text/javascript">
            var errorNum = 0;

            function submitForm() {
                // Get form values
                errorNum = 0;
                var mobile = document.getElementById('mobile').value;
                var emailInput = document.getElementById('email');

                var chineseSurnameInput = document.getElementById('surname_zh');
                var chineseLastnameInput = document.getElementById('lastname_zh');
                var englishSurnameInput = document.getElementById('surname_en');
                var englishLastnameInput = document.getElementById('lastname_en');

                var email = emailInput.value.trim();
                var chineseSurname = chineseSurnameInput.value.trim();
                var chineseLastname = chineseLastnameInput.value.trim();
                var englishSurname = englishSurnameInput.value.trim();
                var englishLastname = englishLastnameInput.value.trim();
                
                document.querySelectorAll('.error').forEach(el => {
                    el.textContent = "";
                });

                if (!email) {
                    document.getElementById('EmailError').textContent = '不能留空';
                    errorNum = errorNum+1;
                }else{
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/; // Regular expression to match email format
                    if (!emailRegex.test(email)) {
                        document.getElementById('EmailError').textContent = '需要電郵格式';
                        errorNum = errorNum+1;
                    }
                }

                const mobileRegex = /^\d{8}$/;    // Validate mobile number
                if (!mobileRegex.test(mobile)) {
                    document.getElementById('mobileError').textContent = '電話必需為8位數字';
                    errorNum = errorNum+1;
                }

                const chineseRegex = /^[\u4E00-\u9FA5]+$/; // Regular expression to match Chinese characters
                if (!chineseLastname) {
                    document.getElementById('lastname_zh_error').textContent = '不能留空';
                    errorNum = errorNum+1;
                }else{
                    if (!chineseRegex.test(chineseLastname)) {
                        document.getElementById('lastname_zh_error').textContent = '必需中文';
                        errorNum = errorNum+1;
                    }
                }
                
                if (!chineseSurname) {
                    document.getElementById('surname_zh_error').textContent = '不能留空';
                    errorNum = errorNum+1;
                }else{
                    if (!chineseRegex.test(chineseSurname)) {
                        document.getElementById('surname_zh_error').textContent = '必需中文';
                        errorNum = errorNum+1;
                    }
                }

                const EnglisgRegex = /^[A-Za-z\s]+$/; // Regular expression to match English characters
                if (englishLastname){
                    if (!EnglisgRegex.test(englishLastname)) {
                        document.getElementById('lastname_en_error').textContent = 'english please';
                        errorNum = errorNum+1;
                    }
                }
                if(englishSurname){
                    if (!EnglisgRegex.test(englishSurname)) {
                        document.getElementById('surname_en_error').textContent =  'english please';
                        errorNum = errorNum+1;
                    }
                }

                if(errorNum > 0){
                    return;
                }

                var formData = $("#applicationForm").serialize();

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
				$.ajax({
					type: "POST",
					data: formData,
                    dataType: "json",
					url: "{{ route('member.join.api') }}",
					success: function (result)  {
                        if(result.status == 0){
                            // window.location.replace(result.url) ;
                            alert(result.name+" 歡迎你成為GodPlus一份子😊");
                            // window.location.replace("https://godplus.org/") ;
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
        </script>

    </body>
</html>