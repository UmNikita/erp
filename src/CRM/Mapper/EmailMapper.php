<?php

namespace App\CRM\Mapper;

use App\CRM\DTO\Client\EmailRecordDTO;
use App\CRM\DTO\OpenAPI\Client\EmailListResponseDTO;
use App\CRM\Enums\EmailStatus;
use App\Entity\EmailLog;
use App\Home\Mapper\AbstractMapper;

class EmailMapper extends AbstractMapper {

    public function entityToListResponse(array $values) {
        $records = $this->mapList($values, function ($record) {
            return $this->entityToDTO($record);
        });
        return new EmailListResponseDTO($records);
    }

    public function entityToDTO(EmailLog $email) {
        $isSuccess = true;
        if($email->getStatus() == EmailStatus::FAIL)
            $isSuccess = false;
        return new EmailRecordDTO(
            $email->getTitle(),
            $email->getUser()->getName(),
            $email->getCreatedAt(),
            $isSuccess
        );
    }
}