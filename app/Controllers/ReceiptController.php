<?php

namespace App\Controllers;

use App\Contracts\RequestValidatorFactoryInterface;
use App\RequestValidators\UploadReceiptRequestValidator;
use League\Flysystem\Filesystem;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class ReceiptController
{

    public function __construct(
        private readonly Filesystem $filesystem,
        private readonly RequestValidatorFactoryInterface $requestValidatorFactory
    )
    {
    }

    public function store(Request $request, Response $response, array $args): Response
    {
        $file = $this->requestValidatorFactory->make(UploadReceiptRequestValidator::class)->validate(
            $request->getUploadedFiles()
        )['receipt'];

        $filename = $file->getClientFilename();

        $this->filesystem->write('receipts/' . $filename, $file->getStream()->getContents());

        return $response;
    }
}