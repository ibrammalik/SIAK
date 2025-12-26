<?php

namespace App\Filament\Resources\RTS\Schemas;

use App\Filament\Resources\Penduduks\Schemas\PendudukForm;
use App\Models\RT;
use App\Models\RW;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Unique;

class RTForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                // === PILIH RW ===
                Select::make('rw_id')
                    ->label('Pilih RW')
                    ->relationship('rw', 'nomor', modifyQueryUsing: function (Builder $query) {
                        $user = Auth::user();
                        if ($user->isRW()) {
                            $query->where('id', $user->rw_id);
                        }
                    })
                    ->getOptionLabelFromRecordUsing(fn(RW $rw) => "RW {$rw->nomor}")
                    ->preload()
                    ->searchable()
                    ->required()
                    ->hintIcon('heroicon-m-map')
                    ->hintIconTooltip('Pilih RW tempat RT ini berada.')
                    ->helperText('RT harus berada di bawah satu RW tertentu.')
                    ->columnSpanFull()
                    ->live(),

                // === NOMOR RT ===
                TextInput::make('nomor')
                    ->label('Nomor RT')
                    ->placeholder('Contoh: 1')
                    ->required()
                    ->numeric()
                    ->unique(
                        ignoreRecord: true,
                        modifyRuleUsing: function (Unique $rule, $get) {
                            return $rule->where('rw_id', $get('rw_id'));
                        }
                    )
                    ->hintIcon('heroicon-m-numbered-list')
                    ->hintIconTooltip('Isi dengan angka urut RT di dalam RW yang dipilih.')
                    ->columnSpanFull(),

                // === KETUA RT ===
                Select::make('ketua_id')
                    ->label('Ketua RT')
                    ->relationship('ketua', 'nama')
                    ->getOptionLabelFromRecordUsing(fn($record) => "{$record->nama} — {$record->nik}")
                    ->hiddenOn('create')
                    ->preload()
                    ->searchable()
                    ->hintIcon('heroicon-m-user-circle')
                    ->hintIconTooltip('Pilih penduduk yang akan dijadikan Ketua RT.')
                    ->helperText('Pastikan penduduk sudah terdaftar di sistem penduduk.')
                    ->columnSpanFull()
                    ->rule(function (callable $get) {
                        return function (string $attribute, $value, \Closure $fail) use ($get) {
                            if (!$value) return;

                            // Cek apakah penduduk sudah menjadi ketua RT lain
                            $existsRt = RT::where('ketua_id', $value)
                                ->where('id', '!=', $get('id'))
                                ->exists();

                            if ($existsRt) {
                                $fail('Penduduk ini sudah terdaftar sebagai Ketua RT lain.');
                                return;
                            }

                            // Cek apakah penduduk sudah menjadi ketua RW
                            $existsRw = RW::where('ketua_id', $value)->exists();

                            if ($existsRw) {
                                $fail('Penduduk ini sudah terdaftar sebagai Ketua RW.');
                            }
                        };
                    })
                    ->createOptionModalHeading(function (?RT $record) {
                        return $record
                            ? "Buat Ketua RT {$record->nomor} / RW {$record->rw->nomor}"
                            : 'Buat Ketua RT';
                    })
                    ->createOptionForm(PendudukForm::getFormSchema())
                    ->createOptionUsing(function (array $data) {
                        $penduduk = \App\Models\Penduduk::create($data);

                        if (($data['shdk'] ?? null) === 'Kepala' && !empty($data['keluarga_id'])) {
                            \App\Models\Keluarga::where('id', $data['keluarga_id'])
                                ->update(['kepala_id' => $penduduk->id]);
                        }

                        return $penduduk->id;
                    }),
            ]);
    }
}
