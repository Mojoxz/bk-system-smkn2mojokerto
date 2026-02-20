<?php

namespace App\Filament\Resources\Classrooms\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ClassroomForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('major_id')
                    ->relationship('major', 'name')
                    ->required(),
                TextInput::make('name')
                    ->required(),
                TextInput::make('grade')
                    ->required(),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
