<?php
namespace App\Filament\Resources\EmployeeResource\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Contracts\View\View;

class EmployeeQrWidget extends Widget
{
    public $record;

    public function mount(): void
    {
        $this->record = request()->route('record'); // atau gunakan Filament's `$this->record`
    }

    public function render(): View
    {
        return view('filament.resources.employee-resource.widgets.employee-qr-widget', [
            'record' => $this->record,
        ]);
    }
}
