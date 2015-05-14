<?php

namespace EasyScrumREST\FrontendBundle\Util;

class StatisticSearchHelper {

    private $from;
    private $to;
    private $project;
    private $company;

    public function __construct($company)
    {
        $this->company = $company;
    }

    /**
     * @param mixed $dateFrom
     */
    public function setFrom($dateFrom)
    {
        $this->from = $dateFrom;
    }

    /**
     * @return mixed
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * @param mixed $dateTo
     */
    public function setTo($dateTo)
    {
        $this->to = $dateTo;
    }

    /**
     * @return mixed
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * @param mixed $project
     */
    public function setProject($project)
    {
        $this->project = $project;
    }

    /**
     * @return mixed
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * @param mixed $company
     */
    public function setCompany($company)
    {
        $this->company = $company;
    }

    /**
     * @return mixed
     */
    public function getCompany()
    {
        return $this->company;
    }

}