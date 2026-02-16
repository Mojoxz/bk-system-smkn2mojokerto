<?php

namespace App\Filament\Resources\ViolationTypes\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ViolationTypeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('violation_category_id')
                    ->required()
                    ->numeric(),
                TextInput::make('name')
                    ->required(),
                Textarea::make('description')
                    ->columnSpanFull(),
                TextInput::make('points')
                    ->required()
                    ->numeric(),
                Toggle::make('is_custom')
                    ->required(),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
