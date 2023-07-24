<?php

namespace App\Services;

use App\Contracts\SessionInterface;
use App\DataObjects\DataTableQueryParams;
use Psr\Http\Message\ServerRequestInterface;

class RequestService
{
    public function __construct(
        private readonly SessionInterface $session
    )
    {
    }

    public function getReferer(ServerRequestInterface $request): string
    {
        $referer = $request->getHeader('referer')[0] ?? '';

        if (!$referer) {
            return $this->session->get('previousUrl');
        }

        $refererHost = parse_url($referer, PHP_URL_HOST);

        if ($refererHost !== $request->getUri()->getHost()) {
            $referer = $this->session->get('previousUrl');
        }

        return $referer;
    }

    public function isXhr(ServerRequestInterface $request): bool
    {
        return $request->getHeaderLine('X-Requested-With') === 'XMLHttpRequest';
    }

    public function getDataTableQueryParams(ServerRequestInterface $request): DataTableQueryParams
    {
        $params = $request->getQueryParams();

//        var_dump($params);

        $orderBy = $params['columns'][$params['order'][0]['column']]['data'] ?? 'id';
        $orderDirection = $params['order'][0]['dir'] ?? 'DESC';

//        var_dump($orderBy);
//        var_dump($orderDirection);

        return new DataTableQueryParams(
            (int) $params['start'],
            (int) $params['length'],
            $orderBy,
            $orderDirection,
            $params['search']['value'] ?? '',
            (int) $params['draw'],
        );
    }
}