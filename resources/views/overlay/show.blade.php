@extends('layouts.overlay')

@section('content')
    @livewire('overlay.toast-display', [
        'overlayKey' => $overlayKey
    ])
@endsection

