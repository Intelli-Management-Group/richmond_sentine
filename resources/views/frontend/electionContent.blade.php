<div class="pt-5 pb-5">
    <div class="col-sm-11 col-md-11 col-lg-11 pt-2 m-auto">
        <nav>
            <div class="nav nav-tabs election_tabs" role="tablist">
                <a class="nav-item nav-link active" data-toggle="tab" href="#{{ $tab }}-nav-mayor" role="tab"
                   aria-controls="nav-mayor" aria-selected="true"><h4>Mayor</h4></a>
                <a class="nav-item nav-link" data-toggle="tab" href="#{{ $tab }}-nav-councillor" role="tab"
                   aria-controls="nav-councillor" aria-selected="false"><h4>Councillor</h4></a>
                <a class="nav-item nav-link" data-toggle="tab" href="#{{ $tab }}-nav-trustee" role="tab"
                   aria-controls="nav-contact" aria-selected="false"><h4>Trustee</h4></a>
            </div>
        </nav>
        <div class="tab-content election_tab_content">
            <div class="tab-pane fade show active mt-5" id="{{ $tab }}-nav-mayor" role="tabpanel">
                <div class="row">
                    @foreach($mayor_tab as $key => $value)
                        @include('frontend.electionVideo',['key' => $key,'value' => $value])
                    @endforeach
                </div>
            </div>
            <div class="tab-pane fade mt-5" id="{{ $tab }}-nav-councillor" role="tabpanel">
                <div class="row">
                    @foreach($councillor_tab as $key => $value)
                        @include('frontend.electionVideo',['key' => $key,'value' => $value])
                    @endforeach
                </div>
            </div>
            <div class="tab-pane fade mt-5" id="{{ $tab }}-nav-trustee" role="tabpanel">
                <div class="row">
                    @foreach($trustee_tab as $key => $value)
                        @include('frontend.electionVideo',['key' => $key,'value' => $value])
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

