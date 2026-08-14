@php
    $notification = getNotificationList();
@endphp
<i class="fa fa-bell fa-2x"></i>
<span class="notification-count">{!! getNotificationCount() !!}</span>
<div id="notification_list_outer">
    <div id="notification_list">
        <h4 class="text-center">Notifications</h4>
        @forelse(count($notification) > 0 ? $notification : array() as $n)
            <a href="{!! $n['redirect_url'] !!}">
                <div class="single-notification-outer {!! ($n['isRead'] ? 'read' : 'unread')  !!}">
                    <div class="d-flex">
                        <div class="notification-avatar-img"
                             style="background-image: url('{!! $n['logo'] !!}');">&nbsp;
                        </div>
                        <div class="color-navyblue">{!! $n['notificationText'] !!}<br/>
                            <div class="color-nobel">{!! $n['sentTime'] !!}</div>
                        </div>
                    </div>
                    @if(!$n['isRead'])
                        <div class="mark-as-read">
                            <input type="radio" class="cursor_pointer mr-2 mark-as-read-radio"
                                   datavalue="{!! $n["id"] !!}"
                                   value="{!! $n["id"] !!}">
                        </div>
                    @endif
                </div>
            </a>
        @empty
            <div class="text-center">No notification found</div>
        @endforelse
    </div>
</div>
