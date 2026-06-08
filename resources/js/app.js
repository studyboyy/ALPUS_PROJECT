import { Livewire } from "../../vendor/livewire/livewire/dist/livewire.esm";
import Chart from "chart.js/auto";

// Expose Chart globally so Alpine inline x-data scripts can use it
window.Chart = Chart;

Livewire.start();
