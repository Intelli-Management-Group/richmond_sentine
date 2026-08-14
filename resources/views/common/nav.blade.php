@section('navsection')
@include('common.topbar')
<section>
    {{-- {{ Request::is('human-resources*')? 'active' : '' }} --}}
    <div class="leftpanel">
        <div class="leftpanelinner">
            <div class="tab-content">
                <div class="tab-pane active">
                    <h5 class="sidebar-title">Favorites</h5>
                    <ul class="nav nav-pills nav-stacked nav-quirk">
                        <li class="{!! \Request::is('dashboard') ? 'active' : '' !!}"><a href="{!! url('dashboard') !!}"><i class="fa fa-home"></i> <span>Dashboard</span></a>
                        </li>
                    </ul>
                    <h5 class="sidebar-title">Main Menu</h5>
                    <ul class="nav nav-pills nav-stacked nav-quirk">
                        <li class="nav-parent  {!! (\Request::is('article-list') || \Request::is('unpublished-article-list') || \Request::is('author-list') || \Request::is('add-author') || \Request::is('edit-author/*') || \Request::is('add-article/*') || \Request::is('edit-article/*')) ? 'active' : '' !!}"><a href="javascript:void(0);"><i class="fa fa-th-list"></i>
                                <span>Article</span></a>
                            <ul class="children">
                                <li class="{!! (\Request::is('article-list') || \Request::is('add-article/*') || (\Request::is('edit-article/*') && (isset($article_data) && $article_data->status))) ? 'active' : '' !!}"><a href="{!! url('article-list') !!}">Article</a></li>
                                <li class="{!! (\Request::is('unpublished-article-list') || (\Request::is('edit-article/*') && (isset($article_data) && !$article_data->status))) ? 'active' : '' !!}"><a href="{!! url('unpublished-article-list') !!}">Unpublish Article</a></li>
                                {{-- <li><a href="{!! url('article/Most_viewer') !!}">View Count List</a></li> --}}
                                <li class="{!! (\Request::is('author-list') || \Request::is('add-author')  || \Request::is('edit-author/*')) ? 'active' : '' !!}"><a href="{!! url('author-list') !!}">Add Author Info.</a></li>
                            </ul>
                        </li>
                        <li class="nav-parent {!! (\Request::is('edition-list') || \Request::is('add-edition') || \Request::is('edit-edition/*'))  ? 'active' : '' !!}"><a href="javascript:void(0);"><i class="fa fa-th-list"></i>
                                <span>Digital edition</span></a>
                            <ul class="children">
                                <li class="{!! (\Request::is('edition-list') || \Request::is('add-edition') || \Request::is('edit-edition/*'))  ? 'active' : '' !!}"><a href="{!! url('edition-list') !!}">Edition list</a></li>
                            </ul>
                        </li>
                        {{-- <li class="nav-parent"><a href="javascript:void(0);"><i class="fa fa-file-text"></i>
                          <span>Add Meta Tag</span></a>
                            <ul class="children">
                                <li><a href="{!! url('article/add_Metatag') !!}">Meta Tag</a></li>
                            </ul>
                        </li> --}}
                        {{-- <li class="nav-parent"><a href="javascript:void(0);"><i class="fa fa-map-signs"></i>
                          <span>All Subscribe Users</span></a>
                            <ul class="children">
                                <li><a href="{!! url('admin/subscribe') !!}">Subscribe Users</a></li>
                            </ul>
                        </li> --}}
                        <li class="nav-parent {!! (\Request::is('category-list') || \Request::is('edit-category/*')) ? 'active' : '' !!}">
                            <a href="javascript:void(0);"><i class="fa fa-list-ul"></i>
                                <span>Category</span></a>
                            <ul class="children">
                                <li class="{!! (\Request::is('category-list') || \Request::is('edit-category/*')) ? 'active' : '' !!}"><a href="{!! url('category-list') !!}">Add Category</a></li>
                            </ul>
                        </li>
                        <li class="nav-parent {!! (\Request::is('advertisement-list') || \Request::is('add-advertisement') || \Request::is('edit-advertisement/*'))  ? 'active' : '' !!}"><a href="javascript:void(0);"><i class="fa fa-file-image-o"></i>
                                <span>Advertisement</span></a>
                            <ul class="children">
                                <li class="{!! (\Request::is('advertisement-list') || \Request::is('add-advertisement') || \Request::is('edit-advertisement/*')) ? 'active' : '' !!}"><a href="{!! route('advertisement-list') !!}">Add Advertisement</a></li>
                            </ul>
                        </li>
                        <li class="nav-parent {!! (\Request::is('coupon-list') || \Request::is('add-coupon') || \Request::is('edit-coupon/*'))  ? 'active' : '' !!}"><a href="javascript:void(0);"><i class="fa fa-gift"></i>
                                <span>Coupon</span></a>
                            <ul class="children">
                                <li class="{!! (\Request::is('coupon-list') || \Request::is('add-coupon') || \Request::is('edit-coupon/*')) ? 'active' : '' !!}"><a href="{!! url('coupon-list') !!}">Add Coupon</a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-parent {!! (\Request::is('edit-about-us') || \Request::is('edit-terms-of-use')|| \Request::is('edit-privacy-policy')) ? 'active' : '' !!}">
                            <a href="javascript:void(0);"><i class="fa fa-user"></i>
                                <span>Footer Detail</span></a>
                            <ul class="children">
                                <li class="{!! \Request::is('edit-about-us') ? 'active' : '' !!}">
                                    <a href="{!! url('edit-about-us') !!}">About Us</a></li>
                                <li class="{!! \Request::is('edit-terms-of-use') ? 'active' : '' !!}">
                                    <a href="{!! url('edit-terms-of-use') !!}">Terms of Use</a></li>
                                <li class="{!! \Request::is('edit-privacy-policy') ? 'active' : '' !!}">
                                    <a href="{!! url('edit-privacy-policy') !!}">Privacy Policy</a></li>
                            </ul>
                        </li>
                        <li class="nav-parent {!! (\Request::is('admin-contact-us')) ? 'active' : '' !!}">
                            <a href="javascript:void(0);"><i class="fa fa-map-signs"></i>
                                <span>Contact Us</span></a>
                            <ul class="children">
                                <!-- <li><a href="{!! url('custompages/contact_users') !!}">Contact Users List</a></li> -->
                                <li class="{!! \Request::is('admin-contact-us') ? 'active' : '' !!}">
                                    <a href="{!! url('admin-contact-us') !!}">Contact Info.</a></li>
                            </ul>
                        </li>
{{--                        <li class="nav-parent {!! (\Request::is('business-category-list') || \Request::is('edit-business-category/*') || \Request::is('business-list') || \Request::is('edit-business/*') || \Request::is('edit-approved-business/*') || \Request::is('unapproved-business-list')) ? 'active' : '' !!}">--}}
{{--                            <a href="javascript:void(0);"><i class="fa fa-list-ul"></i>--}}
{{--                                <span>Business</span></a>--}}
{{--                            <ul class="children">--}}
{{--                                <li class="{!! (\Request::is('business-category-list') || \Request::is('edit-business-category/*')) ? 'active' : '' !!}"><a href="{!! url('business-category-list') !!}">Add Category</a></li>--}}
{{--                                <li class="{!! (\Request::is('business-list') || \Request::is('edit-approved-business/*')) ? 'active' : '' !!}"><a href="{!! url('business-list') !!}">Business list</a></li>--}}
{{--                                <li class="{!! (\Request::is('unapproved-business-list') || \Request::is('edit-business/*')) ? 'active' : '' !!}"><a href="{!! url('unapproved-business-list') !!}">Unapproved Business list</a></li>--}}
{{--                            </ul>--}}
{{--                        </li>--}}
{{--                        <li class="nav-parent {!! (\Request::is('event-category-list') || \Request::is('edit-event-category/*') || \Request::is('event-list') || \Request::is('add-event') || \Request::is('edit-event/*')) ? 'active' : '' !!}">--}}
{{--                            <a href="javascript:void(0);"><i class="fa fa-list-ul"></i>--}}
{{--                                <span>Event</span></a>--}}
{{--                            <ul class="children">--}}
{{--                                <li class="{!! (\Request::is('event-category-list') || \Request::is('edit-event-category/*')) ? 'active' : '' !!}"><a href="{!! url('event-category-list') !!}">Add Category</a></li>--}}
{{--                                <li class="{!! (\Request::is('event-list') || \Request::is('add-event') || \Request::is('edit-event/*')) ? 'active' : '' !!}"><a href="{!! url('event-list') !!}">Event list</a></li>--}}
{{--                            </ul>--}}
{{--                        </li>--}}
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="mainpanel">
        <div class="contentpanel">
            @yield('content')
        </div>
    </div>
</section>
@endsection
