<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BreakfastItemsResource\Pages;
use App\Models\BreakfastItems;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;

class BreakfastItemsResource extends Resource
{
    protected static ?string $model = BreakfastItems::class;

    protected static ?string $navigationGroup = 'Mess Management';
    protected static ?string $navigationIcon = 'lineawesome-bread-slice-solid';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
               Forms\Components\TextInput::make('name')->label('Name')->required(),
                Forms\Components\TextInput::make('units')->label('Units')->numeric()
                ->required()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('units')
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
            'index' => Pages\ListBreakfastItems::route('/'),
            'create' => Pages\CreateBreakfastItems::route('/create'),
            'edit' => Pages\EditBreakfastItems::route('/{record}/edit'),
        ];
    }
}
