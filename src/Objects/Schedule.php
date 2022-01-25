<?php

namespace Omnipay\AuthorizeNetRecurring\Objects;

use Academe\AuthorizeNet\PaymentInterface;
use Academe\AuthorizeNet\AbstractModel;
use Omnipay\Common\Exception\InvalidRequestException;
use DateTime;

class Schedule extends AbstractModel
{

    const SCHEDULE_UNIT_DAYS = 'days';
    const SCHEDULE_UNIT_MONTHS = 'months';

    protected $intervalLength;
    protected $intervalUnit;
    protected $startDate;
    protected $totalOccurrences;
    protected $trialOccurrences;

    public function __construct($parameters = null) {
        parent::__construct();

        $this->setIntervalUnit($parameters['intervalUnit']);
        $this->setIntervalLength($parameters['intervalLength']);
        $this->setStartDate($parameters['startDate']);
        $this->setTotalOccurrences($parameters['totalOccurrences']);
        if (isset($parameters['trialOccurrences'])) {
            $this->setTrialOccurrences($parameters['trialOccurrences']);
        }
    }

    public function jsonSerialize() {
        $data = [];
        if ($this->hasIntervalLength()) {
            $data['interval']['length'] = $this->getIntervalLength();
        }
        if ($this->hasIntervalUnit()) {
            $data['interval']['unit'] = $this->getIntervalUnit();
        }
        if ($this->hasStartDate()) {
            $data['startDate'] = $this->getStartDate();
        }
        if ($this->hasTotalOccurrences()) {
            $data['totalOccurrences'] = $this->getTotalOccurrences();
        }
        if ($this->hasTrialOccurrences()) {
            $data['trialOccurrences'] = $this->getTrialOccurrences();
        }
        return $data;
    }
    
    protected function setIntervalLength(int $value) {

        switch ($this->intervalUnit) {
            case 'months':
                if ($value < 1 || $value > 12) {
                    throw new InvalidRequestException('Interval Length on a unit of months must be a string between 1 and 12, inclusive.');
                }
                break;
            case 'days':
                if ($value < 7 || $value > 365) {
                    throw new InvalidRequestException('Interval Length must be a string, between "7" and "365".');
                }
                break;
        }

        $this->intervalLength = (string)$value;
    }

    protected function setIntervalUnit(string $value) {
        $this->assertValueScheduleUnit($value);
        $this->intervalUnit = $value;
    }

    protected function setStartDate(string $value) {
        $this->startDate = $this->validateDate($value);
    }

    private function validateDate($date, $format = 'Y-m-d') {
        $d = DateTime::createFromFormat($format, $date);
        if ($d) {
            return $d->format($format);
        }
        else {
            throw new InvalidRequestException('Date must have the format "YYYY-MM-DD".');
        }
    }

    protected function setTotalOccurrences(int $value) {
        if ($value < 1 || $value > 9999) {
            throw new InvalidRequestException('Interval Unit must be a string, between "1" and "9999".');
        }
        $this->totalOccurrences = (string)$value;
    }

    protected function setTrialOccurrences(int $value) {
        if ($value < 1 || $value > 99) {
            throw new InvalidRequestException('Interval Unit must be a string, between "1" and "99".');
        }
        $this->trialOccurrences = (string)$value;
    }

}