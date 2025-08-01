<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentResource\Pages;
use App\Filament\Resources\PaymentResource\RelationManagers;
use App\Models\Payment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $navigationLabel = 'Payments';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('file_id')
                    ->label('File')
                    ->relationship('file', 'original_name')
                    ->required(),
                TextInput::make('amount')
                    ->label('Amount')
                    ->numeric()
                    ->prefix('$')
                    ->required(),
                TextInput::make('duration')
                    ->label('Duration (minutes)')
                    ->numeric()
                    ->required(),
                TextInput::make('payment_intent_id')
                    ->label('Payment Intent ID')
                    ->required()
                    ->maxLength(255),
                Select::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pending',
                        'completed' => 'Completed',
                        'failed' => 'Failed',
                    ])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                TextColumn::make('file.original_name')
                    ->label('File')
                    ->searchable()
                    ->limit(30),
                TextColumn::make('amount')
                    ->label('Amount')
                    ->money('USD')
                    ->sortable(),
                TextColumn::make('duration')
                    ->label('Duration')
                    ->formatStateUsing(fn (int $state): string => $state . ' min')
                    ->sortable(),
                TextColumn::make('payment_intent_id')
                    ->label('Payment Intent')
                    ->searchable()
                    ->limit(20),
                BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'completed',
                        'danger' => 'failed',
                    ]),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Filter::make('completed')
                    ->label('Completed Payments')
                    ->query(fn (Builder $query): Builder => $query->where('status', 'completed')),
                Filter::make('pending')
                    ->label('Pending Payments')
                    ->query(fn (Builder $query): Builder => $query->where('status', 'pending')),
                Filter::make('failed')
                    ->label('Failed Payments')
                    ->query(fn (Builder $query): Builder => $query->where('status', 'failed')),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayment::route('/create'),
            'edit' => Pages\EditPayment::route('/{record}/edit'),
        ];
    }
}
