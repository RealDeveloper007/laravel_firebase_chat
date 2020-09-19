@extends('layouts.app')

@section('content')

<div class="content-wrapper chat-page">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox chat-view">
                <div class="ibox-title row">
                    <div class="col-md-12">
                        <h4>Chat</h4>
                    </div>
                </div>
            </div>
            <div class="ibox-content">
                <div class="row">
                    <div class="col-md-4 chat-head">
                        <div class="chat-users">
                            <div class="users-list" id="userList">

                                @foreach ($users as $alluser)

                                <div class="chat-user">
                                    <div class="chat-photo" onclick="openChatWindow(<?= $alluser['id'] ?>);">
                                        <img class="chat-avatar" src="{{asset('images/'.$alluser['profile_img'])}}" alt="">
                                    </div>
                                    <div class="chat-user-name">
                                        <a href="#" onclick="openChatWindow(<?= $alluser['id'] ?>);">{{ $alluser['name'] }}
                                        </a>
                                    </div>
                                </div>
                                @endforeach
                            </div>

                            @if(!isset($users))
                            <div id="no_users"> No User Found</div>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-8 chat-content">
                        <div class="chat-tab tabs-container">
                            <ul class="nav nav-tabs" id="userNameList">
                                @foreach ($users as $alluser)
                                <li class="">
                                    <a data-toggle="tab" href="#tab-{{ $alluser['id'] }}" aria-expanded="false" id="user-{{ $alluser['id'] }}" style="display: none">
                                        <span class="user-pic">
                                            <img class="message-avatar" src="{{asset('images/'.$alluser['profile_img'])}}" alt="">
                                        </span>
                                        {{ $alluser['name'] }}
                                        <span class="pull-right" data-chat_id="{{ $alluser['id'] }}" data-href="{{ route('ExportCsv')}}" id="export" class="btn btn-success btn-sm" onclick="exportTasks(event.target);">Export</span>
                                    </a>
                                  
                                </li>
                                @endforeach
                            </ul>
                            <div class="tab-content">
                                @foreach ($userchats as $AlluserChats)
                                <div id="tab-{{ $AlluserChats['id'] }}" class="tab-pane" style="visibility: hidden;">
                                    <div class="chat-discussion" id="chat-discussion_{{ $AlluserChats['id'] }}" data-name="{{$AlluserChats['name']}}">

                                        <!-- Chat Area -->

                                    </div>
                                    <div class="chat-message-form">
                                        <textarea class="form-control message-input" name="message" placeholder="Type Your Message" id="bodyId_{{ $AlluserChats['id']  }}"></textarea>


                                        <button class="btn btn-primary dim btn-large-dim" type="button" onclick="send(<?= $AlluserChats['id'] ?>);"> Send
                                        </button>
                                    </div>
                                </div>
                                @endforeach
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection