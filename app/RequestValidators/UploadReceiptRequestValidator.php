<?php

namespace App\RequestValidators;

use App\Contracts\RequestValidatorInterface;
use App\Exception\ValidationException;
use finfo;
use InvalidArgumentException;
use League\MimeTypeDetection\FinfoMimeTypeDetector;

class UploadReceiptRequestValidator implements RequestValidatorInterface
{
    public function validate(array $data): array
    {
        $uploadedFile = $data['receipt'] ?? null;

        // Check if file was uploaded
        if (!$uploadedFile) {
            throw new ValidationException(['receipt' => ['No file was uploaded']]);
        }

        // Check if file upload was successful
        if ($uploadedFile->getError() !== UPLOAD_ERR_OK) {
            throw new ValidationException(['receipt' => ['File upload failed']]);
        }

        // Validate file size
        $maxFileSize = 5 * 1024 * 1024; // 5MB
        if ($uploadedFile->getSize() > $maxFileSize) {
            throw new ValidationException(['receipt' => ['File size exceeded']]);
        }

        // validate file name
        $filename = $uploadedFile->getClientFilename();
        if (! preg_match('/^[a-zA-Z0-9\s._-]+$/', $filename)) {
            throw new ValidationException(['receipt' => ['File name not allowed']]);
        }

        // Validate file type
        $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/jpg', 'application/pdf'];
        $tmpFilePath = $uploadedFile->getStream()->getMetadata('uri');

        if (!in_array($uploadedFile->getClientMediaType(), $allowedMimeTypes, false)) {
            throw new ValidationException(['receipt' => ['File type not allowed CHECK 1']]);
        }

        $detector = new FinfoMimeTypeDetector();
        $mimeType = $detector->detectMimeType($tmpFilePath, $uploadedFile->getStream()->getContents());

        if (!in_array($mimeType, $allowedMimeTypes, false)) {
            throw new ValidationException(['receipt' => ['File type not allowed CHECK 2']]);
        }

        return $data;
    }
}