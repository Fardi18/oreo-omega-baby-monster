@extends('_template_web.master')

@php
    $pagetitle = 'FAQ';
@endphp

@section('title', $pagetitle)

@section('content')
    <section>
        <div class="section_faq">
            <div class="container">
                <h2>Frequently Asked Questions</h2>
                <span class="label">Looking for answers? You've come to the right place.</span>
                <div class="faq_wrapper">
                    @if (isset($data[0]))
                        @foreach ($data as $item)
                            <div class="faq_box">
                                <h5>{!! $item->text_1 !!}</h5>
                                @if (isset($item->level_2))
                                    @foreach ($item->level_2 as $level_2)
                                        <div class="toggle_box">
                                            <div class="toggle_top">{!! $level_2->text_1 !!}</div>
                                            <div class="toggle_bottom">{!! $level_2->text_2 !!}</div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        @endforeach
                    @else
                        
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection
