<!-- .bs-modal-lg -->
<div class="modal fade bs-modal-lg" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false" @if(View::hasSection('large_modal_id')) id="@yield('large_modal_id')" @endif>
    <div class="modal-dialog modal-lg">
        @if(View::hasSection('large_modal_form'))
            <form method="@yield('large_modal_method')" action="@yield('large_modal_url')" enctype="multipart/form-data" @if(View::hasSection('large_modal_form_validation')) onsubmit="@yield('large_modal_form_validation')" @endif>
            @csrf
        @endif
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" @if(View::hasSection('large_modal_btn_close_onclick')) onclick="@yield('large_modal_btn_close_onclick')"  @endif>×</span></button>
                    <h4 class="modal-title">@yield('large_modal_title')</h4>
                </div>
                <div class="modal-body">
                    @yield('large_modal_content')
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal" @if(View::hasSection('large_modal_btn_close_onclick')) onclick="@yield('large_modal_btn_close_onclick')"  @endif>{{ ucwords(lang('close', $translations)) }}</button>
                    @if(View::hasSection('large_modal_btn_label'))
						<button type="submit" class="btn btn-primary btn-submit" @if(View::hasSection('large_modal_btn_onclick')) onclick="@yield('large_modal_btn_onclick')"  @endif>
							@yield('large_modal_btn_label')
						</button>
					@endif
                </div>
            </div>
        @if(View::hasSection('large_modal_form'))
            </form>
        @endif
    </div>
</div>
<!-- /.bs-modal-lg -->

@yield('large_modal_script')