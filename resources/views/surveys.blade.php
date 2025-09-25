@extends('layout')
@section('content')
    {{-- ABOUT --}}
    <div ref="aboutSection" style="margin-top:20px;" class="container flex mx-auto mb-4 px-2">
        <div class="w-1/5 pt-10 md:px-3 lg:px-10">
            <img src="{{asset('images/logo2.svg')}}" alt="logo2">
        </div>
        <div class="w-full md:w-3/5">
            <div class="text-2xl md:text-4xl font-bold">EMM Survey Registry</div>
            <div class="ml-20">
                The EMM (Ethnic and Migrant Minorities) Survey Registry is a database of quantitative surveys that have
                been undertaken with EMM (sub)samples across Europe and beyond. Survey-level metadata is available for
                each of the surveys included in the EMM Survey Registry. Explore and learn about the different surveys
                and find specific surveys using the search, filtering (simple and advanced) and sorting features. Once the desired filtering, sorting
                and/or search parameters have been identified/selected, the list of surveys will be automatically updated.
            </div>
        </div>
    </div>
    <survey-search></survey-search>
@endsection
