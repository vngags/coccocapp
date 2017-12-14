@extends('layouts.app')
@section('title', 'Top thành viên')
@section('content')
@include('layouts.blocks.__navbar')
<div class="container pt80">
    @foreach($members as $member)
        {{ $member->name }}
    @endforeach
</div>
@endsection
