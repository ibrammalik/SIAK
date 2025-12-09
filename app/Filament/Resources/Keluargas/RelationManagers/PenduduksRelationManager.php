<?php

namespace App\Filament\Resources\Keluargas\RelationManagers;

use AlperenErsoy\FilamentExport\Actions\FilamentExportHeaderAction;
use App\Filament\Resources\Penduduks\PendudukResource;
use App\Models\Kelurahan;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PenduduksRelationManager extends RelationManager
{
    protected static string $relationship = 'penduduks';

    protected static ?string $relatedResource = PendudukResource::class;

    protected static bool $isLazy = false;

    public function isReadOnly(): bool
    {
        return false;
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nama')
            ->columns([
                TextColumn::make('nik')
                    ->label('NIK')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('nama')
                    ->label('Nama')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('tempat_lahir')
                    ->label('Tempat Lahir')
                    ->toggleable(),

                TextColumn::make('tanggal_lahir')
                    ->date()
                    ->label('Tanggal Lahir')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('jenis_kelamin')
                    ->label('JK')
                    ->toggleable(),

                TextColumn::make('agama')
                    ->label('Agama')
                    ->toggleable(),

                TextColumn::make('pendidikan')
                    ->label('Pendidikan')
                    ->toggleable(),

                TextColumn::make('status_perkawinan')
                    ->label('Status Perkawinan')
                    ->toggleable(),

                TextColumn::make('pekerjaan.name')
                    ->label('Pekerjaan')
                    ->toggleable(),

                TextColumn::make('status_kependudukan')
                    ->badge()
                    ->label('Status Kependudukan')
                    ->colors([
                        'success' => 'Aktif',
                        'danger' => 'Tidak Aktif',
                        'warning' => 'Pending',
                    ])
                    ->toggleable(),

                TextColumn::make('shdk')
                    ->badge()
                    ->label('SHDK')
                    ->colors([
                        'primary' => 'Kepala Keluarga',
                        'secondary' => 'Istri',
                        'success' => 'Anak',
                        'warning' => 'Lainnya',
                    ])
                    ->toggleable(),

                TextColumn::make('no_telp')
                    ->label('No. Telp')
                    ->toggleable(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Tambah Anggota Keluarga')
                    ->icon('heroicon-m-user-plus')
                    ->color('primary')
                    ->tooltip('Buka form untuk menambahkan anggota keluarga baru')
                    ->url(fn() => PendudukResource::getUrl('create', [
                        'keluarga_id' => $this->getOwnerRecord()->id,
                    ]))
                    ->openUrlInNewTab(false),

                FilamentExportHeaderAction::make('export')
                    ->fileName('Keluarga_' . $this->getOwnerRecord()->no_kk . '_' . now()->format('Y-m-d'))
                    ->disableFileNamePrefix()
                    ->disableCsv()
                    ->defaultFormat('pdf')
                    ->defaultPageOrientation('landscape')
                    ->disableAdditionalColumns()
                    ->modifyPdfWriter(function ($writer) {
                        // get the parent 'Keluarga' model from the relation manager
                        $keluarga = $this->getOwnerRecord();
                        $kelurahan = Kelurahan::query()->first();

                        $html = view('exports.keluarga', [
                            'keluarga' => $keluarga,
                            'kelurahan' => $kelurahan,
                        ])->render();

                        return $writer
                            ->loadHTML($html)
                            ->setPaper('A4')
                            ->setOption('isHtml5ParserEnabled', true)
                            ->setOption('dpi', 150);
                    }),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->emptyStateHeading('Belum ada anggota keluarga')
            ->emptyStateDescription('Tambahkan anggota keluarga melalui tombol di atas.')
            ->paginated([10, 25, 50]);
    }
}
