<?php

namespace App\Controllers;

use App\Contracts\RequestValidatorFactoryInterface;
use App\RequestValidators\UploadReceiptRequestValidator;
use App\Services\ReceiptService;
use App\Services\TransactionService;
use Exception;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemException;
use Psr\Http\Message\UploadedFileInterface;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class ReceiptController
{

    public function __construct(
        private readonly Filesystem $filesystem,
        private readonly RequestValidatorFactoryInterface $requestValidatorFactory,
        private readonly TransactionService $transactionService,
        private readonly ReceiptService $receiptService
    )
    {
    }

    /**
     * @throws FilesystemException
     * @throws Exception
     */
    public function store(Request $request, Response $response, array $args): Response
    {

        /** @var UploadedFileInterface $file */
        $file = $this->requestValidatorFactory
            ->make(UploadReceiptRequestValidator::class)
            ->validate(
            $request->getUploadedFiles()
        )['receipt'];

        $filename = $file->getClientFilename();
        $randomFilename = bin2hex(random_bytes(16)) . '.' . pathinfo($filename, PATHINFO_EXTENSION);

        $id = $args['id'];

        if (! $id || ! ($transaction = $this->transactionService->getById($id))) {
            return $response->withStatus(404);
        }

        $this->filesystem->write('receipts/' . $randomFilename, $file->getStream()->getContents());

        $this->receiptService->create($transaction, $filename, $randomFilename);

        return $response;
    }
}