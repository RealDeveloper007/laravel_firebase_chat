<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/chat.css') }}" rel="stylesheet">
    <link href="{{ asset('css/toastr.css') }}" rel="stylesheet">


    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{ asset('js/toastr.min.js') }}"></script>

</head>

<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav mr-auto">

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Authentication Links -->
                        @guest

                        @else
                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                {{ Auth::user()->name }} <span class="caret"></span>
                            </a>

                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                    {{ __('Logout') }}
                                </a>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                    @csrf
                                </form>
                            </div>
                        </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            @yield('content')
        </main>
    </div>
</body>

</html>

<!-- <script src="https://www.gstatic.com/firebasejs/4.9.1/firebase.js"></script> -->
<script type="text/javascript" src="https://cdn.firebase.com/js/client/2.4.2/firebase.js"></script>

<script>
    var messageRef = new Firebase('https://laravel-chat-ec9d6.firebaseio.com/chats');

    var ImagePath = "{{ asset('images')}}";

    var SessionUser = "{{isset(Auth::User()->id) ? Auth::User()->id : ''}}";

    var SessionUsername = "{{isset(Auth::User()->name) ? Auth::User()->name : ''}}";


    // Open the Chat Window  Function
    function openChatWindow(user) {
        $('#userNameList li a.active.show').removeClass('active show').css('display', 'none');
        $('.tab-content .tab-pane.active.show').removeClass('active show').css('visibility', 'visible');
        $('#tab-' + user).addClass('active show').css('visibility', 'visible');
        $('#user-' + user).addClass('active show').show();
        scroll_message_window(user);
    }


    // Scroll Down Function
    function scroll_message_window(user) {
        $('#view_user' + user).hide();
        $('#sharefileaction_' + user).hide();
        $('.chat-tab.tabs-container').css({
            "float": "none",
            "width": "100%"
        });
        $('#bodyId_' + user).attr("placeholder", "Type your answer here");
        $('#bodyId_' + user).removeClass('placeholder');
        var length = $('#chat-discussion_' + user + ' > div').length;
        var height = $("#chat-discussion_" + user).height();
        length = length * 300;
        $("#chat-discussion_" + user).animate({
            scrollTop: height + length
        }, 300);
    }

    // Send Message Function
    function send(id) {
        var username = "{{ isset(\Auth::User()->name) ? \Auth::User()->name : ''}}";
        var toUsername = $('#user-' + id).text();
        var text = $('#bodyId_' + id).val();
        if (text != '') {
            $('#bodyId_' + id).attr("placeholder", "Type your answer here");
            $('#bodyId_' + id).removeClass('placeholder');

            let StoreData = {
                'to_id': id,
                'body': text,
                'fromUsername': $.trim(username),
                'toUsername': $.trim(toUsername),
                "_token": "{{ csrf_token() }}"
            };

            // Store Chats on Firebase
            messageRef.push(StoreData);

            // Store Chats on Mysql Database
            $.ajax({
                type: 'POST',
                url: "{{ route('SendMessage')}}",
                data: StoreData,
                dataType: 'json',
                success: function(data) {

                     $('#bodyId_' + id).val('');
                    scroll_message_window(id);
                }
            });
        } else {

            $('#bodyId_' + id).attr("placeholder", "Please Type message ");
            $('#bodyId_' + id).addClass('placeholder');

        }
    }

    messageRef.on('child_added', function(snapshot) {

        var ResultAll = snapshot.val();
         if(typeof(ResultAll)=='object')
         {


            var Result = ResultAll;
            if(Result.from_id!=undefined)
            {
                
                if (parseInt(Result.to_id) == parseInt(SessionUser)) 
                {
                    var NAME = $('#chat-discussion_' + Result.from_id).attr('data-name');
                    $('#chat-discussion_' + Result.from_id).append('<div class="chat-message left"> ' +
                        '<img class="message-avatar" src="' + ImagePath + '/default.jpg" alt="">' +
                        '<div class="message"> <span>' + NAME +
                        '</span><span class="message-content"> ' + Result.body + '</span> ' +
                        '<span class="message-date">  ' + Result.date + ' ' + Result.time + '</span> ' +
                        '</div>' +
                        '</div>');

                }

                if (parseInt(Result.from_id) == parseInt(SessionUser))
                {
                    console.log('#chat-discussion_' + Result.to_id);

                    $('#chat-discussion_' + Result.to_id).append('<div class="chat-message right"> ' +
                        '<div class="message"><span class="message-content"> ' + Result.body + '</span> ' +
                        '<span class="message-date">  ' + Result.date + ' ' + Result.time + '</span> ' +
                        '</div>' +
                        '</div>');

                }

                scroll_message_window(Result.from_id);
            }
         }
    });

    function exportTasks(_this) {
        let _url = $(_this).data('href');
        var chat_id = $(_this).data('chat_id');
        window.location.href = _url + '?chat_id=' + chat_id;
    }
</script>