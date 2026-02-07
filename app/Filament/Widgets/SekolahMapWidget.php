<?php

namespace App\Filament\Widgets;

use App\Models\Sekolah;
use Dotswan\MapPicker\Fields\Map;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class SekolahMapWidget extends Widget implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'filament.widgets.sekolah-map-widget';

    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 2;

    public ?array $data = [];

    public ?Sekolah $primarySekolah = null;

    public function mount(): void
    {
        $user = Auth::user();

        // Default location (Malinau)
        $lat = 3.5700;
        $lng = 116.6300;

        // If user has sekolah role, show their sekolah location
        if ($user && $user->hasRole('sekolah') && $user->sekolahs()->exists()) {
            $this->primarySekolah = $user->sekolahs()->first();
            if ($this->primarySekolah && $this->primarySekolah->latitude && $this->primarySekolah->longitude) {
                $lat = (float) $this->primarySekolah->latitude;
                $lng = (float) $this->primarySekolah->longitude;
            }
        } else {
            // For admin, get first sekolah with coordinates
            $firstSekolah = Sekolah::whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->first();
            if ($firstSekolah) {
                $lat = (float) $firstSekolah->latitude;
                $lng = (float) $firstSekolah->longitude;
            }
        }

        $this->data = [
            'location' => [
                'lat' => $lat,
                'lng' => $lng,
            ],
        ];

        $this->form->fill($this->data);
    }

    public function form(Schema $form): Schema
    {
        $lat = $this->data['location']['lat'] ?? 3.5700;
        $lng = $this->data['location']['lng'] ?? 116.6300;

        return $form
            ->schema([
                Map::make('location')
                    ->defaultLocation(latitude: $lat, longitude: $lng)
                    ->showMarker(true)
                    ->clickable(false)
                    ->draggable(false)
                    ->tilesUrl('https://tile.openstreetmap.de/{z}/{x}/{y}.png')
                    ->zoom(15)
                    ->showFullscreenControl(true)
                    ->showZoomControl(true)
                    ->detectRetina(true)
                    ->extraStyles([
                        'min-height: 400px',
                        'border-radius: 8px',
                    ]),
            ])
            ->statePath('data');
    }

    protected function getViewData(): array
    {
        return [
            'primarySekolah' => $this->primarySekolah,
            'isSekolahRole' => Auth::user()?->hasRole('sekolah') ?? false,
        ];
    }
}
