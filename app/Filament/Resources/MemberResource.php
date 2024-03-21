<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MemberResource\Pages;
use App\Filament\Resources\MemberResource\RelationManagers;
use App\Filament\Resources\MemberResource\RelationManagers\BillsRelationManager;
use App\Models\Member;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Hash;

class MemberResource extends Resource
{
    protected static ?string $model = Member::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';
    protected static ?string $navigationGroup = 'Mess Management';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')->label('Name'),
                TextInput::make('email')->email()->label('Member Email'),
                TextInput::make('mobile')->tel()->label('Member Mobile No'),
                Select::make('user_id')->relationship('user','name')
                ->reactive()->afterStateUpdated(function ($state,$set){
                        $user = User::find($state);
                        $set('name',$user->name);
                        $set('email', $user->email);
                        $set('join_date',$user->created_at);

                    }),
                DatePicker::make('join_date')->label('Join Date'),
                TextInput::make('security')->label('Security Deposit')->default(0),
                Toggle::make('is_active')->label('Active?')->default(true)->inline(false),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Name')->sortable()->searchable(),
                TextColumn::make('email')->label('Email'),
                TextColumn::make('mobile')->label('Mobile No'),
                TextColumn::make('security')->label('Security Deposit'),
                TextColumn::make('account_balance')->label('A/C Balance')->color(fn(TextColumn $column) => ($column->getState() <= 0 ) ? 'success':'danger'),
                ToggleColumn::make('is_active')->label('Active?'),
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
        return array(
          BillsRelationManager::class,
            RelationManagers\PaymentsRelationManager::class
        );
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMembers::route('/'),
            'create' => Pages\CreateMember::route('/create'),
            'edit' => Pages\EditMember::route('/{record}/edit'),
        ];
    }
}
