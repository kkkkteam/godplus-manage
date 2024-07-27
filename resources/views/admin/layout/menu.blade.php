<nav class="navbar navbar-default">

  <div class="container-fluid">
    <div class="navbar-header">
      <b>GodPlus<br>後台管理</b>
    </div>
    <ul class="nav navbar-nav">
      <li><a
          class="{{ ((Request::is('admin/service/attendance-summary') || Request::is('admin/service/attendance-summary/*')) ? 'active' : '') }}"
          href="{{ route('admin.service.attendance_summary.html')}}">崇拜出席Summary</a></li>
      <li><a
          class="{{ ((Request::is('admin/whatsapp-list') || Request::is('admin/whatsapp-list/*')) ? 'active' : '') }}"
          href="{{ route('admin.command.list.html')}}">whatsapp設定</a></li>
      <li><a
          class="{{ ((Request::is('admin/service') || Request::is('admin/service/list') || Request::is('admin/service/update')) ? 'active' : '') }}"
          href="{{ route('admin.service.list.html')}}">崇拜設定</a></li>
      <li><a class="{{ (Request::is('admin/service/registration') ? 'active' : '') }}"
          href="{{ route('admin.service.registration.html')}}">每週崇拜報名名單</a></li>
    </ul>
  </div>
</nav>

<div class="hamburger-menu">

  <input id="menu__toggle" type="checkbox" />
  <label class="menu__btn" for="menu__toggle">
    <span></span>
  </label>

  <ul class="menu__box">
    <h2>GodPlus<br>後台管理</h2>
    <li><a
        class="menu__item {{ ((Request::is('admin/service/attendance-summary') || Request::is('admin/service/attendance-summary/*')) ? 'active' : '') }}"
        href="{{ route('admin.service.attendance_summary.html')}}">崇拜出席Summary</a></li>
    <li><a
        class="menu__item {{ ((Request::is('admin/whatsapp-list') || Request::is('admin/whatsapp-list/*')) ? 'active' : '') }}"
        href="{{ route('admin.command.list.html')}}">whatsapp設定</a></li>
    <li><a
        class="menu__item {{ ((Request::is('admin/service') || Request::is('admin/service/list') || Request::is('admin/service/update')) ? 'active' : '') }}"
        href="{{ route('admin.service.list.html')}}">崇拜設定</a></li>
    <li><a class="menu__item {{ (Request::is('admin/service/registration') ? 'active' : '') }}"
        href="{{ route('admin.service.registration.html')}}">每週崇拜報名名單</a></li>
  </ul>
</div>