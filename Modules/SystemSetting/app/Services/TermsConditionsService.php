<?php

namespace Modules\SystemSetting\app\Services;

use Modules\SystemSetting\app\Repositories\TermsConditionsRepository;

class TermsConditionsService
{
    protected $repository;

    public function __construct(TermsConditionsRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getTermsConditions()
    {
        return $this->repository->getTermsConditions();
    }

    public function updateTermsConditions($data)
    {
        return $this->repository->update($data);
    }
}
