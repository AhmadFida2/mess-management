<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DailyUnitsResource\Pages;
use App\Filament\Resources\DailyUnitsResource\RelationManagers;
use App\Models\DailyUnits;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use function GuzzleHttp\default_ca_bundle;

class DailyUnitsResource extends Resource
{
    protected static ?string $model = DailyUnits::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Mess Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                DatePicker::make('date')->format('Y-m-d')->default(now())->required()->unique(),
                TextInput::make('lunch')->label('Lunch Units')->integer()->default(0),
                TextInput::make('dinner')->label('Dinner Units')->integer()->required()->autofocus(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('date')->label('Date')->formatStateUsing(fn($state)=> Carbon::parse($state)->format('d-m-Y')),
                TextColumn::make('lunch')->label('Lunch Units'),
                TextColumn::make('dinner')->label('Dinner Units'),
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
            'index' => Pages\ListDailyUnits::route('/'),
            'create' => Pages\CreateDailyUnits::route('/create'),
            'edit' => Pages\EditDailyUnits::route('/{record}/edit'),
        ];
    }
}
