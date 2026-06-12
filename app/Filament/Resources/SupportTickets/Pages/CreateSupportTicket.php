<?php
namespace App\Filament\Resources\SupportTickets\Pages;
use App\Filament\Resources\SupportTickets\SupportTicketResource;
use App\Services\Support\SupportTicketNumberGenerator;
use Filament\Resources\Pages\CreateRecord;
class CreateSupportTicket extends CreateRecord { protected static string $resource=SupportTicketResource::class; protected function mutateFormDataBeforeCreate(array $data):array{return [...$data,'ticket_number'=>app(SupportTicketNumberGenerator::class)->generate(),'created_by_id'=>auth()->id()];} }
