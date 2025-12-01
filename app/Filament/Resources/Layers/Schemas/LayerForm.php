<?php

namespace App\Filament\Resources\Layers\Schemas;

use App\Enums\LayerType;
use App\Models\Layer;
use Dotswan\MapPicker\Fields\Map;
use Filament\Actions\Action;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;

class LayerForm
{
    public static function isEditable(): bool
    {
        $path = Request::path(); // Contoh: 'users/edit/1' atau 'users/create'
        return strpos($path, 'edit') !== false || strpos($path, 'create') !== false;
    }

    public static function getFieldValue($record, $field, $default = null)
    {
        $sessionKey = "form_{$field}";
        if (session()->has($sessionKey)) return session($sessionKey); // 1️⃣ Check session first

        // 2️⃣ Handle nested relationship syntax like "rw.id" or "rw.village.name"
        if ($record) {
            $segments = explode('.', $field);
            $value = $record;
            foreach ($segments as $segment) {
                if (is_null($value)) {
                    break;
                }
                $value = $value->{$segment} ?? null; // If it's a relation, dive in
            }
            if (!is_null($value)) return $value;
        }

        return $default; // 3️⃣ Default fallback
    }

    public static function getLayerColorValue(): string
    {
        $recordId = request()->route('record') ?? request()->route('id');
        $record = Layer::query()->find($recordId);
        $color = self::getFieldValue($record, 'color', '#3388ff');
        return $color;
    }

    public static function calculateArea($geojson)
    {
        try {
            $polygon = \geoPHP::load($geojson, 'geojson'); // Load geometry with geoPHP
            if (!$polygon) return null;
            $areaDegrees = $polygon->getArea(); // Calculate area in degrees²
            $areaMeters = $areaDegrees * pow(111320, 2); // Convert degrees² → m² (approx)
            $areaHectares = $areaMeters / 10000; // Convert hectares
            return $areaHectares;
        } catch (\Throwable $th) {
            return null;
        }
    }

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Radio::make('type')
                    ->label('Tipe')
                    ->options(function () {
                        $user = auth()->user();
                        if ($user->isSuperAdmin() || $user->isKelurahan()) return LayerType::class;
                        if ($user->isRW()) return [
                            LayerType::RW->value => LayerType::RW->getLabel(),
                            LayerType::RT->value => LayerType::RT->getLabel(),
                            LayerType::Lainnya->value => LayerType::Lainnya->getLabel(),
                        ];
                        if ($user->isRT()) return [
                            LayerType::RT->value => LayerType::RT->getLabel(),
                            LayerType::Lainnya->value => LayerType::Lainnya->getLabel(),
                        ];
                    })
                    ->inline()
                    ->columnSpanFull()
                    ->required()
                    ->live()
                    ->afterStateUpdated(function ($set) {
                        $set('kelurahan.id', null);
                        $set('rt.id', null);
                        $set('rw.id', null);
                    })
                    ->afterStateHydrated(fn($record, $set)
                    => $set('type', self::getFieldValue($record, 'type'))),

                Select::make('kelurahan.id')
                    ->label('Kelurahan')
                    ->visibleJs(<<<'JS'
                        $get('type') === 'kelurahan'
                    JS)
                    ->relationship('kelurahan', 'nama')
                    ->searchable()
                    ->preload()
                    ->columnSpanFull()
                    ->requiredIf('type', 'kelurahan')
                    ->afterStateHydrated(fn($record, $set)
                    => $set('kelurahan.id', self::getFieldValue($record, 'kelurahan.id')))
                    ->rule(function ($get, $livewire) {
                        return function (string $attribute, $value, \Closure $fail) use ($get, $livewire) {
                            $kelurahan = \App\Models\Kelurahan::find($value);
                            $currentLayerId = $livewire->record?->id;
                            if ($kelurahan && $kelurahan->layer_id && $kelurahan->layer_id !== $currentLayerId) {
                                $fail('Kelurahan ini sudah memiliki layer wilayah.');
                            }
                        };
                    }),

                Select::make('rw.id')
                    ->label('RW')
                    ->visibleJs(<<<'JS'
                        $get('type') === 'rw'
                    JS)
                    ->relationship('rw', 'nomor', modifyQueryUsing: function (Builder $query) {
                        $user = auth()->user();
                        if ($user->isRW()) {
                            $query->where('id', $user->rw_id);
                        }
                    })
                    ->getOptionLabelFromRecordUsing(fn($record) => "RW {$record->nomor}")
                    ->searchable()
                    ->preload()
                    ->columnSpanFull()
                    ->requiredIf('type', 'rw')
                    ->afterStateHydrated(fn($record, $set)
                    => $set('rw.id', self::getFieldValue($record, 'rw.id')))
                    ->rule(function ($get, $livewire) {
                        return function (string $attribute, $value, \Closure $fail) use ($get, $livewire) {
                            $rw = \App\Models\RW::find($value);
                            $currentLayerId = $livewire->record?->id;
                            if ($rw && $rw->layer_id && $rw->layer_id !== $currentLayerId) {
                                $fail('RW ini sudah memiliki layer wilayah.');
                            }
                        };
                    }),

                Select::make('rt.id')
                    ->label('RT')
                    ->visibleJs(<<<'JS'
                        $get('type') === 'rt'
                    JS)
                    ->relationship('rt', 'nomor', modifyQueryUsing: function (Builder $query) {
                        $user = auth()->user();
                        if ($user->isRT()) {
                            $query->where('id', $user->rt_id);
                        }
                        if ($user->isRW()) {
                            $query->where('rw_id', $user->rw_id);
                        }
                    })
                    ->getOptionLabelFromRecordUsing(fn($record) => "RT {$record->nomor} / RW {$record->rw->nomor}")
                    ->searchable()
                    ->preload()
                    ->columnSpanFull()
                    ->requiredIf('type', 'rt')
                    ->afterStateHydrated(fn($record, $set)
                    => $set('rt.id', self::getFieldValue($record, 'rt.id')))
                    ->rule(function ($get, $livewire) {
                        return function (string $attribute, $value, \Closure $fail) use ($get, $livewire) {
                            $rt = \App\Models\RT::find($value);
                            $currentLayerId = $livewire->record?->id;
                            if ($rt && $rt->layer_id && $rt->layer_id !== $currentLayerId) {
                                $fail('RT ini sudah memiliki layer wilayah.');
                            }
                        };
                    }),

                Fieldset::make('Layer Wilayah Lainnya')
                    ->columnSpan('full')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama')
                            ->columnSpan('full')
                            ->requiredIf('type', LayerType::Lainnya->value)
                            ->afterStateHydrated(fn($record, $set)
                            => $set('name', self::getFieldValue($record, 'name'))),

                        Textarea::make('description')
                            ->label('Deskripsi')
                            ->columnSpanFull()
                            ->requiredIf('type', LayerType::Lainnya->value)
                            ->afterStateHydrated(fn($record, $set)
                            => $set('description', self::getFieldValue($record, 'description'))),
                    ])
                    ->visibleJs(<<<'JS'
                    $get('type') === 'lainnya'
                    JS),

                Map::make('location')
                    ->label('Location')
                    ->columnSpanFull()
                    ->required()

                    // State Management
                    ->afterStateUpdated(function (Set $set, ?array $state): void {
                        $geojson = json_encode($state['geojson']); // Get the GeoJSON data
                        $set('geojson', $geojson); // Save to 'geojson' and fields
                        $set('area', round(self::calculateArea($geojson), 2)); // Save to 'area' fields
                    })
                    ->afterStateHydrated(function ($record, Set $set,): void {
                        $geojson = self::getFieldValue($record, 'geojson');
                        $set('location', ['geojson' => json_decode(strip_tags($geojson))]);
                    })

                    // Basic Configuration
                    ->defaultLocation(latitude: 0.7893, longitude: 113.9213)
                    ->draggable(true)
                    ->clickable(false)
                    ->zoom(5)
                    ->minZoom(0)
                    ->maxZoom(18)
                    ->tilesUrl('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png')
                    ->detectRetina(true)

                    // Marker Configuration
                    ->showMarker(false)

                    // Controls
                    ->showFullscreenControl(true)
                    ->showZoomControl(true)

                    // GeoMan Integration
                    ->geoMan(true)
                    ->geoManEditable(self::isEditable())
                    ->geoManPosition('topleft')
                    ->drawCircleMarker(false)
                    ->rotateMode(self::isEditable())
                    ->drawMarker(false)
                    ->drawPolygon(self::isEditable())
                    ->drawPolyline(false)
                    ->drawCircle(false)
                    ->drawRectangle(false)
                    ->drawText(self::isEditable())
                    ->dragMode(self::isEditable())
                    ->cutPolygon(self::isEditable())
                    ->editPolygon(self::isEditable())
                    ->deleteLayer(self::isEditable())
                    ->setColor(self::getLayerColorValue())
                    ->setFilledColor(self::getLayerColorValue())
                    ->snappable(true, 20)

                    // Extra Customization
                    ->extraTileControl([
                        'tileSize' => 256,
                        'zoomOffset' => 0,
                    ])
                    ->extraStyles([
                        'min-height: 500px',
                        'border-radius: 10px',
                        'z-index: 1',
                    ]),

                ColorPicker::make('color')
                    ->label('Warna Layer')
                    ->afterStateHydrated(fn($set)
                    => $set('color', self::getLayerColorValue()))
                    ->hintAction(
                        Action::make('save_temp')
                            ->icon('heroicon-m-bolt')
                            ->label('Update warna')
                            ->outlined()
                            ->tooltip('Simpan semua data sementara ke session dan refresh halaman untuk menampilkan warna layer baru')
                            ->action(function ($get, $livewire, $record) {
                                $state = $get(); // Grab the full form state
                                foreach ($state as $key => $value) { // Simpan semua input lain ke session
                                    Session::flash("form_{$key}", $value);
                                }

                                // Determine redirect URL
                                $record = $livewire->record ?? null;
                                $redirectUrl = $record
                                    ? route('filament.app.resources.layers.edit', ['record' => $record])
                                    : route('filament.app.resources.layers.create');

                                $livewire->redirect($redirectUrl, true);
                            })
                    ),

                TextInput::make('area')
                    ->label('Luas Wilayah (ha)')
                    ->afterStateHydrated(fn($record, $set)
                    => $set('area', self::getFieldValue($record, 'area')))
                    ->numeric()
                    ->suffixAction(
                        Action::make('calculate')
                            ->icon('heroicon-m-calculator')
                            ->tooltip('Hitung luas wilayah dari polygon')
                            ->action(function ($get, $set) {
                                $geojson = $get('geojson');
                                $set('area', round(self::calculateArea($geojson), 2)); // Save to 'area' fields
                            })
                    )
                    ->suffix('ha')
                    ->required(),

                Hidden::make('geojson')
                    ->afterStateHydrated(fn($record, $set)
                    => $set('geojson', self::getFieldValue($record, 'geojson', '{}'))),
            ]);
    }
}
