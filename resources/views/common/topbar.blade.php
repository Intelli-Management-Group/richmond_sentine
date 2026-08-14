<header>
    <div class="headerpanel">
        <div class="logopanel">
            <a href="{{ url('/') }}" target="_blank">
                <img src="{{ asset('ui/img/foot_logo.png') }}" alt="" width="170"/>
            </a>
        </div>
        <!-- logopanel -->
        <div class="headerbar"><a id="menuToggle" class="menutoggle"><i class="fa fa-bars"></i></a>
            <div class="header-right">
                <ul class="headermenu">
                    <li class="bell-icon hide">
                        @include("common.notificationList")
                    </li>
                    <li>
                        <div class="btn-group">
                            <button type="button" class="btn btn-logged" data-toggle="dropdown">Welcome, Admin<span
                                    class="caret"></span></button>
                            <ul class="dropdown-menu pull-right">
                            <!--<li><a href="<?php
                            ?>"><i class="glyphicon glyphicon-cog"></i> Account Settings</a></li>-->
                                <li><a href="{!! route('logout') !!}"><i class="glyphicon glyphicon-log-out"></i> Log
                                        Out</a></li>
                            </ul>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</header>
