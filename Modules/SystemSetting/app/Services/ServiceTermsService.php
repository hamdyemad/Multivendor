<?php

namespace Modules\SystemSetting\app\Services;

use Modules\SystemSetting\app\Repositories\ServiceTermsRepository;

class ServiceTermsService
{
    protected $repository;

    public function __construct(ServiceTermsRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getServiceTerms()
    {
        return $this->repository->getServiceTerms();
    }

    public function updateServiceTerms($data)
    {
        return $this->repository->update($data);
    }
}
