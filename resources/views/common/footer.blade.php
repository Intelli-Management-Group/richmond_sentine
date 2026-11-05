@section('footer')
    <script src="{{ asset('ui/lib/jquery/jquery.js') }}"></script>
    <script src="{{ asset('ui/lib/jquery-ui/jquery-ui.js') }}"></script>
    <script src="{{ asset('ui/lib/bootstrap/js/bootstrap.js') }}"></script>
    <script src="{{ asset('ui/lib/select2/select2.js') }}"></script>
    <script src="{{ asset('ui/lib/jquery-toggles/toggles.js') }}"></script>
    <script src="{{ asset('ui/lib/datatables/jquery.dataTables.js') }}"></script>
    <script src="{{ asset('ui/lib/datatables-plugins/integration/bootstrap/3/dataTables.bootstrap.js') }}"></script>

    <!-- <script src="{{ asset('ui/lib/bootstrap3-wysihtml5-bower/bootstrap3-wysihtml5.all.js') }}"></script> -->
    <script src="{{ asset('ui/js/quirk.js') }}"></script>
    <script type="text/javascript" src="{{ asset('ui/validation/validate.js') }}"></script>
    <script src="{{ asset('ui/lib/summernote/summernote.js') }}"></script>
    <script src="{{ asset('js/common.js').'?t='.Carbon\Carbon::now()->timestamp }}"></script>
    <script src="{{ asset('js/pusher.min.js') }}"></script>
    <script type="text/javascript">
        //Remember to replace key and cluster with your credentials.
        Pusher.logToConsole = false;
        var pusher = new Pusher('6d56f99ab391ea59a1c7', {
            cluster: 'eu',
            encrypted: true
        });
        //Also remember to change channel and event name if your's are different.
        var channel = pusher.subscribe('business');
        channel.bind('update-business', function (message) {
            refreshNotificationList(true);
        });
        $(document).on("click", ".mark-as-read-radio", function () {
            $.ajax({
                url: '{!! url("read-notification") !!}',
                data: {
                    id: $(this).val()
                },
                type: 'POST',
                success: function (data) {
                    if (data == "success") {
                        refreshNotificationList(false);
                    }
                }
            });
        });

        function refreshNotificationList(add_class) {
            $.ajax({
                url: '{!! url("notification-list") !!}',
                type: 'GET',
                success: function (data) {
                    $("#notification_list_outer").fadeOut();
                    $(".bell-icon").html(data);
                    if(add_class){
                        $(".bell-icon i").addClass("ring")
                        setTimeout(function () {
                            $(".bell-icon i").removeClass("ring")
                        }, 8000);
                    }
                }
            });
        }
    </script>
    @yield('jquerysection')
@endsection
