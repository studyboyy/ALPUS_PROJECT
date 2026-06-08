@props([
    'chartId',
    'labels'     => [],
    'datasets'   => [],
    'height'     => 220,
    'showLegend' => true,
])
@php
    $labelsJson   = json_encode($labels,   JSON_UNESCAPED_UNICODE);
    $datasetsJson = json_encode($datasets, JSON_UNESCAPED_UNICODE);
    $legendStr    = $showLegend ? 'true' : 'false';
@endphp

{{--
    wire:ignore keeps this element alive across Livewire re-renders.
    When PHP data changes, Livewire dispatches 'prodi-chart-update' with new data.
    Alpine listens and updates the Chart.js instance in-place (no flicker).
--}}
<div
    wire:ignore
    x-data="prodiChartInit('{{ $chartId }}', {{ $labelsJson }}, {{ $datasetsJson }}, {{ $legendStr }})"
    x-init="boot()"
    @prodi-chart-update.window="onUpdate($event.detail)"
    style="position:relative; height:{{ $height }}px; width:100%;">
    <canvas id="{{ $chartId }}" style="display:block; width:100%; height:100%;"></canvas>
</div>
