<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FileResource\Pages;
use App\Filament\Resources\FileResource\RelationManagers;
use App\Models\File;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Toggle;

class FileResource extends Resource
{
    protected static ?string $model = File::class;

    protected static ?string $navigationIcon = 'heroicon-o-document';

    protected static ?string $navigationLabel = 'Files';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('uploader_token')
                    ->label('Uploader Token')
                    ->required()
                    ->maxLength(255),
                TextInput::make('original_name')
                    ->label('Original Name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('compressed_path')
                    ->label('Compressed Path')
                    ->required()
                    ->maxLength(255),
                TextInput::make('pin_code')
                    ->label('PIN Code')
                    ->required()
                    ->maxLength(6),
                TextInput::make('downloads')
                    ->label('Downloads')
                    ->numeric()
                    ->default(0),
                TextInput::make('max_downloads')
                    ->label('Max Downloads')
                    ->numeric()
                    ->default(2),
                DateTimePicker::make('expires_at')
                    ->label('Expires At')
                    ->required(),
                Toggle::make('is_premium')
                    ->label('Premium')
                    ->default(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                TextColumn::make('original_name')
                    ->label('File Name')
                    ->searchable()
                    ->limit(30),
                TextColumn::make('pin_code')
                    ->label('PIN')
                    ->searchable()
                    ->copyable(),
                TextColumn::make('downloads')
                    ->label('Downloads')
                    ->sortable(),
                TextColumn::make('max_downloads')
                    ->label('Max Downloads')
                    ->sortable(),
                TextColumn::make('expires_at')
                    ->label('Expires At')
                    ->dateTime()
                    ->sortable(),
                BooleanColumn::make('is_premium')
                    ->label('Premium'),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Filter::make('expired')
                    ->label('Expired Files')
                    ->query(fn (Builder $query): Builder => $query->where('expires_at', '<', now())),
                Filter::make('premium')
                    ->label('Premium Files')
                    ->query(fn (Builder $query): Builder => $query->where('is_premium', true)),
                Filter::make('download_limit_reached')
                    ->label('Download Limit Reached')
                    ->query(fn (Builder $query): Builder => $query->whereRaw('downloads >= max_downloads')),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFiles::route('/'),
            'create' => Pages\CreateFile::route('/create'),
            'edit' => Pages\EditFile::route('/{record}/edit'),
        ];
    }
}
