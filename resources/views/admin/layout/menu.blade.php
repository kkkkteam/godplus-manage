<div class="container">
  <div id="circle-menu">
    <button id="menu-toggle">&#9776;</button>
    <ul id="menu-items">
      <li><a href="{{ route('admin.command.list.html')}}">whatsapp設定</a></li>
      <li><a href="{{ route('admin.service.list.html')}}">崇拜/活動設定</a></li>
      <li><a href="{{ route('admin.service.registration.html')}}">崇拜/活動報名名單</a></li>
      <li><a href="{{ route('admin.service.registration.list.details.html')}}">崇拜/活動報名名單(招待)</a></li>
    </ul>
  </div>
</div>

<script>
    var menuToggle = document.getElementById('menu-toggle');
    var circleMenu = document.getElementById('circle-menu');

    menuToggle.addEventListener('click', function() {
      circleMenu.classList.toggle('open');
    });

    window.addEventListener('resize', function() {
    if (circleMenu.classList.contains('open')) {
            circleMenu.classList.remove('open');
        }
    });
</script>