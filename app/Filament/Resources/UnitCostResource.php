<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UnitCostResource\Pages;
use App\Models\UnitCost;
use Carbon\Carbon;
use Coolsam\FilamentFlatpickr\Forms\Components\Flatpickr;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;

class UnitCostResource extends Resource
{
    protected static ?string $model = UnitCost::class;

    protected static ?string $navigationIcon = 'heroicon-o-calculator';
    protected static ?string $navigationGroup = 'Mess Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Flatpickr::make('month')->label('Month')->monthSelect()
                    ->unique()
                    ->altFormat('F Y')
                    ->altInput()
                    ->default(today()),
                TextInput::make('cost')->label('Unit Cost')->numeric()
                   ->default(0.00)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
               TextColumn::make('month')->label('Month')
                ->formatStateUsing(fn($state)=> Carbon::parse($state)->format('F Y')),
                TextColumn::make('cost')->label('Unit Cost')
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
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
            'index' => Pages\ListUnitCosts::route('/'),
            'create' => Pages\CreateUnitCost::route('/create'),
            'edit' => Pages\EditUnitCost::route('/{record}/edit'),
        ];
    }
}
