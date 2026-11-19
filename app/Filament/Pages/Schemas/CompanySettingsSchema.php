<?php

namespace App\Filament\Pages\Schemas;

use Awcodes\Curator\Components\Forms\CuratorPicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;

final class CompanySettingsSchema
{
    /** @return array<int, Section> */
    public static function components(): array
    {
        return [
            Section::make('Thông tin doanh nghiệp')
                ->icon(Heroicon::OutlinedBuildingOffice2)
                ->description('Thông tin pháp lý và hồ sơ năng lực; tên doanh nghiệp cũng được dùng ngoài frontend.')
                ->schema([
                    TextInput::make('company_name')->label('Tên doanh nghiệp')->required()->maxLength(255)->columnSpanFull(),
                    TextInput::make('tax_code')->label('Mã số thuế')->maxLength(100),
                    TextInput::make('representative')->label('Người đại diện')->maxLength(255),
                    TextInput::make('founded_year')->label('Năm thành lập')->numeric()->minValue(1900)->maxValue((int) now()->format('Y')),
                    TextInput::make('business_license')->label('Số đăng ký kinh doanh')->maxLength(100),
                    CuratorPicker::make('company_profile_media_id')->label('Hồ sơ năng lực')->disk('public')->constrained()->helperText('Có thể chọn PDF hoặc tài liệu hồ sơ năng lực.')->columnSpanFull(),
                ])
                ->columns(2),
        ];
    }
}
