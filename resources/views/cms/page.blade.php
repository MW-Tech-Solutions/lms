@extends('layouts.app')

@section('title', $page->meta_title ?: $page->title)
@section('meta_description', $page->meta_description)

@section('content')
    <div class="cms-page-wrapper">
        @foreach($page->sections as $section)
            @switch(strtoupper($section->section_type))
                @case('HERO')
                    @include('cms.sections.hero', ['section' => $section])
                    @break

                @case('METRICS')
                    @include('cms.sections.metrics', ['section' => $section])
                    @break

                @case('CATALOG')
                    @include('cms.sections.catalog', ['section' => $section])
                    @break

                @case('CATEGORIES')
                    @include('cms.sections.categories', ['section' => $section])
                    @break

                @case('MENTORS')
                    @include('cms.sections.mentors', ['section' => $section])
                    @break

                @case('EVENTS')
                    @include('cms.sections.events', ['section' => $section])
                    @break

                @case('FAQ')
                    @include('cms.sections.faq', ['section' => $section])
                    @break

                @case('PRICING')
                    @include('cms.sections.pricing', ['section' => $section])
                    @break

                @case('DYNAMIC_FORM')
                    @if(($page->slug ?? '') !== 'home' && ($page->id ?? null) !== 1)
                        @include('cms.sections.dynamic_form', ['section' => $section])
                    @endif
                    @break

                @case('FOOTER')
                    @include('cms.sections.footer', ['section' => $section])
                    @break

                @case('CUSTOM_HTML')
                    @include('cms.sections.custom_html', ['section' => $section])
                    @break

                @default
                    @if(view()->exists('cms.sections.' . strtolower($section->section_type)))
                        @include('cms.sections.' . strtolower($section->section_type), ['section' => $section])
                    @endif
            @endswitch
        @endforeach
    </div>
@endsection
