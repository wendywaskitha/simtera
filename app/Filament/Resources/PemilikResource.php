<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Pemilik;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Illuminate\Database\Eloquent\Model;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\PemilikResource\Pages;

class PemilikResource extends Resource
{
    protected static ?string $model = Pemilik::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Data Pemilik';
    protected static ?string $modelLabel = 'Pemilik';
    protected static ?string $pluralModelLabel = 'Pemilik';
    protected static ?string $navigationGroup = 'Data UTTP';
    protected static ?int $navigationSort = 2;
    protected static ?string $recordTitleAttribute = 'nama';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Data Pemilik')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('nama')
                                    ->label('Nama Pemilik')
                                    ->required()
                                    ->maxLength(100)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (string $operation, $state, Forms\Set $set) {
                                        if ($operation !== 'create') {
                                            return;
                                        }
                                        $set('nama', ucwords(strtolower($state)));
                                    }),

                                Forms\Components\TextInput::make('nik')
                                    ->label('NIK')
                                    ->maxLength(20)
                                    ->unique(ignoreRecord: true)
                                    ->rules(['regex:/^\d{16}$/'])
                                    ->helperText('Format: 16 digit angka'),
                            ]),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('telepon')
                                    ->label('Nomor Telepon')
                                    ->tel()
                                    ->maxLength(15)
                                    ->placeholder('08123456789'),

                                Forms\Components\Toggle::make('is_active')
                                    ->label('Status Aktif')
                                    ->default(true)
                                    ->inline(false),
                            ]),

                        Forms\Components\Textarea::make('alamat')
                            ->label('Alamat')
                            ->required()
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama Pemilik')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Medium)
                    ->icon('heroicon-o-user')
                    ->limit(25)
                    ->tooltip(fn (Pemilik $record): ?string => $record->nama)
                    ->url(fn (Pemilik $record) => PemilikResource::getUrl('view', ['record' => $record])),


                Tables\Columns\TextColumn::make('nik')
                    ->label('NIK')
                    ->searchable()
                    ->toggleable()
                    ->copyable()
                    ->copyMessage('NIK disalin!'),

                Tables\Columns\TextColumn::make('telepon')
                    ->label('Telepon')
                    ->searchable()
                    ->toggleable()
                    ->icon('heroicon-o-phone'),

                Tables\Columns\TextColumn::make('jumlah_uttp_aktif')
                    ->label('Jumlah UTTP')
                    ->badge()
                    ->color('info')
                    ->icon('heroicon-o-scale'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status Aktif')
                    ->placeholder('Semua Status')
                    ->trueLabel('Aktif')
                    ->falseLabel('Tidak Aktif'),

                SelectFilter::make('has_uttp')
                    ->label('Memiliki UTTP')
                    ->options([
                        'yes' => 'Ya',
                        'no' => 'Tidak',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['value'] === 'yes',
                                fn (Builder $query): Builder => $query->has('uttps'),
                            )
                            ->when(
                                $data['value'] === 'no',
                                fn (Builder $query): Builder => $query->doesntHave('uttps'),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->icon('heroicon-o-eye'),
                    Tables\Actions\EditAction::make()
                        ->icon('heroicon-o-pencil-square'),
                    Tables\Actions\Action::make('view_uttps')
                        ->label('Lihat UTTP')
                        ->icon('heroicon-o-scale')
                        ->color('info')
                        ->url(fn (Pemilik $record) => route('filament.backend.resources.u-t-t-p-s.index', ['tableFilters[pemilik_id][value]' => $record->id]))
                        ->visible(fn (Pemilik $record) => $record->uttps()->count() > 0),
                    Tables\Actions\DeleteAction::make()
                        ->icon('heroicon-o-trash'),
                ])
                ->label('Aksi')
                ->icon('heroicon-m-ellipsis-vertical')
                ->size('sm')
                ->color('gray'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('activate')
                        ->label('Aktifkan')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(fn ($records) => $records->each->update(['is_active' => true]))
                        ->requiresConfirmation(),
                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('Nonaktifkan')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->action(fn ($records) => $records->each->update(['is_active' => false]))
                        ->requiresConfirmation(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->paginated([10, 25, 50, 100]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPemiliks::route('/'),
            'create' => Pages\CreatePemilik::route('/create'),
            'view' => Pages\ViewPemilik::route('/{record}'),
            'edit' => Pages\EditPemilik::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::aktif()->count();
    }

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return $record->nama;
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'NIK' => $record->nik ?? 'Tidak ada',
            'Telepon' => $record->telepon ?? 'Tidak ada',
            'Jumlah UTTP' => $record->jumlah_uttp_aktif . ' UTTP',
        ];
    }
}
