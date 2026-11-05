<div class="row mt-3 py-3 single-business">
    <div class="col-sm-3 mb-3">
        <img src="{!! $data['logo'] !!}" alt="{!! $data['name'] !!}" class="business-logo"/>
    </div>
    <div class="col-sm-6 mb-3 business-name">
        <a href="{!! url("stores/".$data["shortCode"]) !!}" class="d-block"><h5>{!! $data['name']  !!} </h5></a>
        <div>{!! $data['address']  !!}</div>
    </div>
    <div class="col-sm-3 text-right">
        <a class="mr-3 d-inline-block" title="{!! $data['phone'] !!}" href="{!! "tel://".$data['phone'] !!}"><img
                src="{!! asset("images/frontend/generic/icon_tel-1.png") !!}" alt="Phone"
                class="business-action"/></a>
        <a class="mr-3 d-inline-block" href="https://www.google.com/maps?q={!! urlencode($data['address'])  !!}"
           target="_blank"><img src="{!! asset("images/frontend/generic/icon_directions.png") !!}" alt="Direction"
                                class="business-action"/></a>
        <a href="{!! $data['website'] !!}" target="_blank"><img
                src="{!! asset("images/frontend/generic/icon_website.png") !!}" alt="Website" class="business-action"/></a>
    </div>
</div>
