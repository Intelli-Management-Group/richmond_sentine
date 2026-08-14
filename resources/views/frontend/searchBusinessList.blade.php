<ul class="nav nav-tabs w-100" id="storeTab" role="tablist">
    <li class="nav-item w-33">
        <a class="nav-link active" id="home-tab" data-toggle="tab" href="#featured_business" role="tab"
           aria-controls="featured_business" aria-selected="true"><span
                class="text-uppercase">Featured</span></a>
    </li>
    <li class="nav-item w-33">
        <a class="nav-link" id="profile-tab" data-toggle="tab" href="#categorywise_business" role="tab"
           aria-controls="categorywise_business" aria-selected="false"><span
                class="text-uppercase">Category</span></a>
    </li>
    <li class="nav-item w-33">
        <a class="nav-link" id="contact-tab" data-toggle="tab" href="#alphabetical_business"
           role="tab"
           aria-controls="alphabetical_business" aria-selected="false"><span
                class="text-uppercase">A-Z</span></a>
    </li>
</ul>
<div class="tab-content" id="storeTabContent">
    <div class="tab-pane fade show active" id="featured_business" role="tabpanel"
         aria-labelledby="home-tab">
        @forelse(count($featured) > 0 ? $featured :array() as $f)
            @include("frontend.businessRow",["data" => $f])
        @empty
            <div class="text-center mt-3">No business(es) found</div>
        @endforelse
    </div>
    <div class="tab-pane fade mt-3" role="tabpanel" id="categorywise_business"
         aria-labelledby="profile-tab">
        @forelse(count($categories) > 0 ? $categories : array() as $c)
            <div id="category_{!! $c["id"] !!}">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0 business-name">
                            <a class="d-block" data-toggle="collapse"
                               data-target="#category_detail_{!! $c["id"] !!}" aria-expanded="true"
                               aria-controls="collapseOne">
                                {!! $c["name"]." (".count($c["childs"]).")" !!}
                            </a>
                        </h5>
                    </div>
                    <div id="category_detail_{!! $c["id"] !!}" class="collapse"
                         aria-labelledby="headingOne" data-parent="#category_{!! $c["id"] !!}">
                        <div class="card-body">
                            @foreach(count($c["childs"]) > 0 ? $c["childs"] : array() as $cc)
                                @include("frontend.businessRow",["data" => $cc])
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center mt-3">No business(es) found</div>
        @endforelse
    </div>
    <div class="tab-pane fade mt-3" id="alphabetical_business" role="tabpanel"
         aria-labelledby="contact-tab">
        <div class="alphabetical-outer">
            <div class="w-97">
                @forelse(count($alphabetical_categories) > 0 ? $alphabetical_categories : array() as $c)
                    <div id="category_{!! $c["name"] !!}">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0 business-name">
                                    <a class="d-block" data-toggle="collapse"
                                       data-target="#category_detail_{!! $c["name"] !!}"
                                       aria-expanded="true"
                                       aria-controls="collapseOne">
                                        {!! $c["name"]." (".count($c["childs"]).")" !!}
                                    </a>
                                </h5>
                            </div>
                            <div id="category_detail_{!! $c["name"] !!}" class="collapse"
                                 aria-labelledby="headingOne"
                                 data-parent="#category_{!! $c["name"] !!}">
                                <div class="card-body">
                                    @foreach(count($c["childs"]) > 0 ? $c["childs"] : array() as $cc)
                                        @include("frontend.businessRow",["data" => $cc])
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center mt-3">No business(es) found</div>
                @endforelse
            </div>
            <div class="w-3 text-center">
                @foreach(count($alphabetical_categories) > 0 ? $alphabetical_categories : array() as $key => $value)
                    <div class="mb-2 single-alphabet">
                        <a class="d-block" data-toggle="collapse"
                           data-target="#category_detail_{!! $value["name"] !!}"
                           data-scroll="#category_{!! $key !!}"
                           aria-expanded="true"
                           aria-controls="collapseOne">
                            {!! $key !!}
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
