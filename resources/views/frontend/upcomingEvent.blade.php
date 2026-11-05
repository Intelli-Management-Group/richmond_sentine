@forelse(count($data) > 0 ? $data : array() as $key => $value)
    <div class="row">
        <div class="event-row py-3 col-sm-12" id="event_{!! $key !!}">
            <div class="border-bottom mt-3">
                <h5>{!! \Carbon\Carbon::parse($key)->format("D M j, Y") !!}</h5>
            </div>
            @forelse(count($value) > 0 ? $value : array() as $e)
                <div class="my-2">
                    <div class="col-sm-3 float-left color-blue font-weight-bold mt-3">
                        {!! $e["time"] !!}
                    </div>
                    <div class="col-sm-5 float-left color-666 text-left mt-3">
                        <div class="mb-2"><h6>{!! $e["name"] !!}</h6></div>
                        <div>{!! ($e["cost"] != "" ? "$".$e["cost"] : $e["price"]) !!}</div>
                    </div>
                    @if($e["linkText"] != "")
                        <div class="float-right col-sm-4 mt-3 text-right">
                            <div class="mt-2">
                                <a href="{!! $e["bookingLink"] !!}" target="_blank"
                                   class="booking-link">{!! $e["linkText"] !!}</a>
                            </div>
                        </div>
                    @endif
                    <div class="clearfix"></div>
                </div>
            @empty
                <div class="text-center font-weight-bold mt-3">No events found</div>
            @endforelse
        </div>
    </div>
@empty
    <div class="text-center font-weight-bold mt-3">No events found</div>
@endforelse
