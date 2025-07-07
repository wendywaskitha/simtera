<?php

namespace App\Filament\Resources;

use App\Filament\Resources\JenisUTTPResource\Pages;
use App\Models\JenisUTTP;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class JenisUTTPResource extends Resource
{
    protected static ?string $model = JenisUTTP::class;

    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';
    
    protected static ?string $navigationLabel = 'Jenis UTTP';
    
    protected static ?string $modelLabel = 'Jenis UTTP';
    
    protected static ?string $pluralModelLabel = 'Jenis UTTP';
    
    protected static ?string $navigationGroup = 'Master Data';
    
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Jenis UTTP')
                    ->description('Data jenis Ukuran, Takaran, Timbangan, dan Perlengkapannya')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('nama')
                                    ->label('Nama Jenis UTTP')
                                    ->required()
                                    ->maxLength(50)
                                    ->unique(ignoreRecord: true)
                                    ->placeholder('Contoh: Timbangan Digital'),
                                    
                                Forms\Components\TextInput::make('kode')
                                    ->label('Kode Jenis')
                                    ->maxLength(10)
                                    ->unique(ignoreRecord: true)
                                    ->placeholder('Contoh: TD01'),
                            ]),
                            
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('satuan')
                                    ->label('Satuan')
                                    ->maxLength(20)
                                    ->placeholder('Contoh: kg, liter, meter'),
                                    
                                Forms\Components\Toggle::make('is_active')
                                    ->label('Status Aktif')
                                    ->default(true),
                            ]),
                            
                        Forms\Components\Textarea::make('deskripsi')
                            ->label('Deskripsi')
                            ->rows(3)
                            ->placeholder('Deskripsi lengkap jenis UTTP'),
                    ])
                    ->columns(1)
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama Jenis UTTP')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                    
                Tables\Columns\TextColumn::make('kode')
                    ->label('Kode')
                    ->searchable()
                    ->badge()
                    ->color('gray'),
                    
                Tables\Columns\TextColumn::make('satuan')
                    ->label('Satuan')
                    ->badge()
                    ->color('info'),
                    
                Tables\Columns\TextColumn::make('jumlah_uttp')
                    ->label('Jumlah UTTP')
                    ->badge()
                    ->color('success'),
                    
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status Aktif')
                    ->placeholder('Semua Status')
                    ->trueLabel('Aktif')
                    ->falseLabel('Tidak Aktif'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('nama', 'asc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListJenisUTTPs::route('/'),
            'create' => Pages\CreateJenisUTTP::route('/create'),
            'view' => Pages\ViewJenisUTTP::route('/{record}'),
            'edit' => Pages\EditJenisUTTP::route('/{record}/edit'),
        ];
    }
}
