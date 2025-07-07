@extends('_template_adm.master')

@php
    use App\Libraries\Helper;

    $module = ucwords(lang('note', $translations));
    $pagetitle = $module;
    $function_get_data = 'refresh_data();';

    if (isset($deleted_data)) {
        $pagetitle = ucwords(lang('deleted #item', $translations, ['#item' => $module]));
        $function_get_data = 'refresh_deleted_data();';
    }
@endphp

@section('title', $pagetitle)

@section('content')
    <div class="">
        {{-- display response message --}}
        @include('_template_adm.message')

        <div class="page-title">
            <div class="title_left">
                <h3>{{ $pagetitle }}</h3>
            </div>
            <div class="title_right">
                <div class="col-md-5 col-sm-5 col-xs-12 form-group pull-right top_search">
                    <form method="GET">
                        <div class="input-group">
                            <input type="text" name="q" value="{{ request()->input('q') }}" class="form-control" placeholder="Search for...">
                            <span class="input-group-btn">
                                <button class="btn btn-default" type="submit">Go!</button>
                            </span>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="clearfix"></div>

        <div class="row">
            <div class="col-md-12">
                <div class="x_panel">
                    <div class="x_content">
                        <div class="row">
                            <!-- MAIL LIST -->
                            <div class="col-sm-3 mail_list_column" id="note_list">
                                <a href="{{ route('admin.note.create') }}" id="compose" class="btn btn-sm btn-success btn-block"><i class="fa fa-plus-circle"></i>&nbsp; {{ strtoupper(lang('add new', $translations)) }}</a>
                                
                                @php
                                    $no = 1;
                                @endphp
                                @if (isset($data[0]))
                                    @foreach ($data as $item)
                                        @if ($no <= 10)
                                            <a href="javascript:void(0)" onclick="view_details({{ $item->id }})">
                                                <div class="mail_list">
                                                    <div class="left">
                                                        @if ($item->fav_status)
                                                            <i class="fa fa-star"></i>
                                                        @else
                                                            .
                                                        @endif
                                                    </div>
                                                    <div class="right">
                                                        <h3>{!! $item->title !!}</h3>
                                                        <p>{{ lang('updated', $translations) . ': ' . Helper::time_ago(strtotime($item->updated_at), lang('ago', $translations), Helper::get_periods($translations)) }}</p>
                                                        <p>{!! str_replace(',', ', ', $item->tags) !!}</p>
                                                    </div>
                                                </div>
                                            </a>
                                            @php
                                                $no++;
                                            @endphp
                                        @endif
                                    @endforeach
                                    @if (isset($data[10]))
                                        <button id="load-more" class="btn btn-sm btn-default btn-block" type="button" onclick="load_more({{ $no }})">{{ strtoupper(lang('load more', $translations)) }}</button>
                                    @endif
                                @else
                                    <h3 class="text-center">{!! strtoupper(lang('no data', $translations)) !!}</h3>
                                @endif
                            </div>
                            <!-- /MAIL LIST -->

                            <!-- CONTENT MAIL -->
                            <div class="col-sm-9 mail_view">
                                <div class="inbox-body text-center" id="note-loader" style="display: none">
                                    <img src="{{ asset('images/loading.gif') }}" alt="loading . . .">
                                </div>

                                @if (isset($data[0]))
                                    @php
                                        $item = $data[0];
                                    @endphp
                                    <div class="inbox-body" id="note-body">
                                        <input type="hidden" id="note-id">
                                        <div class="mail_heading row">
                                            <div class="col-md-8 col-sm-12">
                                                <div class="btn-group">
                                                    <button class="btn btn-sm btn-primary" type="button" onclick="edit_note()"><i class="fa fa-pencil"></i> Edit</button>
                                                    {{-- <button class="btn btn-sm btn-default" type="button" data-placement="top" data-toggle="tooltip" data-original-title="Trash"><i class="fa fa-trash-o"></i></button> --}}
                                                </div>
                                            </div>
                                            {{-- LAST UPDATED --}}
                                            <div class="col-md-4 col-sm-12 text-right">
                                                <p class="date" id="note-updated_date">{{ lang('updated', $translations) . ': ' . Helper::time_ago(strtotime($item->updated_at), lang('ago', $translations), Helper::get_periods($translations)) }}</p>
                                            </div>
                                        </div>
                                        <div class="mail_heading row">
                                            {{-- TITLE --}}
                                            <div class="col-md-12">
                                                <h4 id="note-title">{!! $item->title !!}</h4>
                                            </div>
                                        </div>
                                        {{-- CONTENT --}}
                                        <div class="view-mail">
                                            <p>Tags: <b id="note-tags">{!! str_replace(',', ', ', $item->tags) !!}</b></p>
                                            <hr>
                                            <p id="note-content">
                                                {{-- {!! Helper::validate_token($item->content, 25) !!} --}}
                                            </p>
                                        </div>
                                        <div class="btn-group">
                                            <button class="btn btn-sm btn-primary" type="button" onclick="edit_note()"><i class="fa fa-pencil"></i> Edit</button>
                                            {{-- <button class="btn btn-sm btn-default" type="button" data-placement="top" data-toggle="tooltip" data-original-title="Trash"><i class="fa fa-trash-o"></i></button> --}}
                                        </div>
                                    </div>
                                @else
                                    <div class="inbox-body" id="note-body-nodata">
                                        <h3 class="text-center">{!! strtoupper(lang('no data', $translations)) !!}</h3>
                                    </div>
                                @endif
                            </div>
                            <!-- /CONTENT MAIL -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
</div>
@endsection

@section('script')
    <script>
        @if (isset($data[0]))
            view_details({{ $data[0]->id }});
        @endif

        function view_details(id) {
            $('#note-loader').show();
            $('#note-body').hide();
            $('#note-body-nodata').hide();

            $.ajax({
                type: 'GET',
                url: "{{ substr(route('admin.note.get_data.single', 1), 0, -1) }}"+id,
                success: function(response){
                    if (typeof response.status != 'undefined') {
                        if (response.status == 'true') {
                            if (response.data == '') {
                                $('#note-body-nodata').show();
                            } else {
                                $('#note-updated_date').html(response.data.updated_at_label);
                                $('#note-title').html(response.data.title);
                                $('#note-tags').html(response.data.tags);
                                $('#note-content').html(response.data.content);
                                $('#note-id').val(response.id);
                                $('#note-body').show();

                                scroll_to_elm_id('note-body');
                            }
                        } else {
                            alert(response.message);
                        }
                    } else {
                        alert ("{!! lang('Server not respond, please refresh your page.', $translations); !!}");
                    }

                    $('#note-loader').hide();
                },
                error: function (data, textStatus, errorThrown) {
                    console.log(data);
                    console.log(textStatus);
                    console.log(errorThrown);
                    alert ("{!! lang('Oops, something went wrong please try again later.', $translations); !!}\n\n"+textStatus+': '+errorThrown);
                }
            });
        }

        function edit_note() {
            var note_id = $('#note-id').val();
            var url = "{{ substr(route('admin.note.edit', 1), 0, -1) }}"+note_id;

            window.location.href = url;
        }

        var last_item = {{ $no - 1 }};

        function load_more(per_page) {
            $.ajax({
                type: 'GET',
                url: "{{ route('admin.note.load_more') }}",
                data: {
                    limit: per_page,
                    offset: last_item
                },
                success: function(response){
                    if (typeof response.status != 'undefined') {
                        if (response.status == 'true') {
                            if (response.data == '') {
                                alert('NO DATA');
                            } else {
                                var html = '';
                                var no = 1;
                                $.each(response.data, function(index, value) {
                                    if (no < 11) {
                                        html += '<a href="javascript:void(0)" onclick="view_details('+value.id+')">';
                                            html += '<div class="mail_list">';
                                                html += '<div class="left">';
                                                    if (value.fav_status == 1) {
                                                        html += '<i class="fa fa-star"></i>';
                                                    } else {
                                                        html += '.';
                                                    }
                                                html += '</div>';
                                                html += '<div class="right">';
                                                    html += '<h3>'+value.title+'</h3>';
                                                    html += '<p>{{ lang("updated", $translations) }}: '+value.last_update+'</p>';
                                                    html += '<p>'+value.tags_label+'</p>';
                                                html += '</div>';
                                            html += '</div>';
                                        html += '</a>';
                                    }
                                    no++;
                                });

                                $('#load-more').remove();

                                if (no > 10) {
                                    html += '<button id="load-more" class="btn btn-sm btn-default btn-block" type="button" onclick="load_more({{ $no }})">{{ strtoupper(lang("load more", $translations)) }}</button>';
                                }
                                
                                $('#note_list').append(html);

                                last_item += response.total;
                            }
                        } else {
                            alert(response.message);
                        }
                    } else {
                        alert ("{!! lang('Server not respond, please refresh your page.', $translations); !!}");
                    }
                },
                error: function (data, textStatus, errorThrown) {
                    console.log(data);
                    console.log(textStatus);
                    console.log(errorThrown);
                    alert ("{!! lang('Oops, something went wrong please try again later.', $translations); !!}\n\n"+textStatus+': '+errorThrown);
                }
            });
        }
    </script>
@endsection