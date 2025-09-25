@extends('layout')
@section('content')
    <div class="container mx-auto">
        <div class="flex flex-col md:flex-row items-start">
            <div class="w-full md:w-1/4 pr-16 md:px-12 lg:pl-24 flex-shrink-0">
                <img src="/images/ethmig_back_arrow.png" class="right cursor-pointer" onclick="window.history.back()">
            </div>
            <div class="flex flex-col flex-1 border-b border-black">
                <div class="text-4xl font-bold w-full md:w-3/4 text-blue-500 mb-10">
                    {{$survey->answers->f_1_3}}
                </div>
                <div class="flex pb-3 mb-4 items-start">
                    <div class="flex-shrink-0 pt-2">
                        <img src="/images/options.png" class="w-5 mx-5">
                    </div>
                    <div>
                        <ul class="text-base">
                            @foreach($fieldHeadings as $group)
                                <li>
                                    <a href="#group-{{$group['field_code']}}"
                                       class="hover:text-blue-500">{{$group['code']}}
                                        . {{$group['name']}}</a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <div class="mb-1 text-right">
                    <a href="/api/xml-surveys/{{$survey->id}}" target="_blank"
                       class="text-xs text-blue-800"
                    >XML</a>
                </div>
            </div>
        </div>
        @foreach($fieldHeadings as $group)
            <div class="flex flex-col md:flex-row  mb-10">
                <div class="anchor relative" id="group-{{$group['field_code']}}" style="top:-180px"></div>
                <div class="w-full md:w-1/4 flex-shrink-0 px-8 relative h-32">
                    <div class="text-xl font-bold z-10">
                        {{$group['code']}}.<br>
                        {{$group['name']}}
                    </div>
                    <div class="purgecss-svg-bg text-red-300 text-green-300 text-yellow-300 text-blue-300 hidden"></div>
                    <svg xmlns="http://www.w3.org/2000/svg"
                         class="absolute top-0 text-{{\Arr::random(['blue','green','yellow','red'])}}-300 fill-current"
                         style="z-index:-1;"
                    >
                        <ellipse mask="url(#mask_5c7d2fdb8120a9356584bad6_viewer-e4796aff-5c69-4421-8ad1-3a1efeeef9ed)"
                                 cx="67.5" cy="67.5"
                                 rx="67.5" ry="67.5" pointer-events="visiblePainted"/>
                    </svg>
                    <div class="flex">
                        @if(!$loop->first)
                            <a href="#group-{{$fieldHeadings[$loop->index-1]['field_code']}}" class="mr-4">
                                <img src="/images/left_arrow.png" class="w-6">
                            </a>
                        @endif
                        @if(!$loop->last)
                            <a href="#group-{{$fieldHeadings[$loop->index+1]['field_code']}}">
                                <img src="/images/left_arrow.png" class="w-6 rotate-z-180">
                            </a>
                        @endif
                    </div>
                </div>
                <div class="w-full md:w-3/4 px-8 md:px-0 pb-4 mb-6">
                    @foreach($group['displayFields'] as $field)
                        @unless($field->only_subnational && !$survey->isSubnational())
                            @if($field['type'] !== \App\Enum\FieldType::SUB_HEADING)
                                @component('components.field_view')
                                    @slot('code')
                                        {{ $field['code'].'. ' }}
                                    @endslot
                                    @slot('label')
                                        {{ $field['name'] }}
                                    @endslot
                                    {{ field_value_asString($survey, $field['field_code']) }}
                                @endcomponent
                            @else
                                <div class="mb-2">
                                    <div class="bg-gray-200 font-bold py-4 px-2">
                                        <h3>{{$field['code'].'. '.$field['name']}}</h3>
                                    </div>
                                    @foreach($field->displayFields as $_field)
                                        <div class="pl-4">
                                            @component('components.field_view')
                                                @slot('code')
                                                    {{ $_field['code'].'. ' }}
                                                @endslot
                                                @slot('label')
                                                    {{ $_field['name'] }}
                                                @endslot
                                                {{ field_value_asString($survey, $_field['field_code']) }}
                                            @endcomponent
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        @endunless
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
@endsection
