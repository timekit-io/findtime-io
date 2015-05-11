<?php

namespace App\Findtime;

use App\Exceptions\ConfidenceTooLowException;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use SplPriorityQueue;

class PQtest extends SplPriorityQueue
{

    public function compare($priority1, $priority2)
    {
        if ($priority1 === $priority2) return 0;

        return $priority1 < $priority2 ? - 1 : 1;
    }
}

class WitResponse
{

    private $agenda = 'Meeting';
    private $datetime;
    private $contacts;
    private $emails;
    private $confidence;
    private $text;
    private $minConfidence = 0.5;
    private $durations;
    private $future = null;
    private $length = null;
    private $wit;

    public function __construct(Array $wit)
    {
        $this->wit = $wit;
        $this->text = $wit['_text'];
        Log::debug($this->text);
        $entities = $wit['outcomes'][0]['entities'];
        $this->confidence = $wit['outcomes'][0]['confidence'];

        if ($this->confidence < $this->minConfidence) {
            throw new ConfidenceTooLowException(sprintf('Confidence is to low, is: %s. Should be larger then %s', $this->confidence, $this->minConfidence));
        }

        if (isset($entities['email'])) {
            foreach ($entities['email'] as $email) {
                $this->emails[] = $email['value'];
            }
        }

        if (isset($entities['agenda_entry'])) {
            $this->agenda = $entities['agenda_entry'][0]['value'];
        }

        if (isset($entities['datetime'])) {
            $this->datetime = $entities['datetime'][0]['grain'];
        }

        if (isset($entities['duration'])) {
            foreach ($entities['duration'] as $duration) {
                $str = $duration['value'] . ' ' . $duration['unit'];
                if (strtotime($str) > strtotime('+1 day')) {
                    $this->future = $str;
                } else {
                    $this->length = $str;
                }
            }
        }

        if (isset($entities['contact'])) {
            foreach ($entities['contact'] as $contact) {
                $this->contacts[] = $contact['value'];
            }
        }
    }

    public function __toArray()
    {
        return [
            //'emails'    => $this->emails,
            'contacts'   => $this->contacts,
            'agenda'     => $this->agenda,
            'length'     => $this->length,
            'future'     => $this->future,
            'confidence' => $this->confidence,
            'raw'        => $this->wit
        ];
    }

    /**
     * @return mixed
     */
    public function getAgenda()
    {
        return $this->agenda;
    }

    /**
     * @return mixed
     */
    public function getDatetime()
    {
        return $this->datetime;
    }

    /**
     * @return mixed
     */
    public function getContacts()
    {
        return $this->contacts;
    }

    /**
     * @return float
     */
    public function getConfidence()
    {
        return $this->confidence;
    }

    /**
     * @return mixed
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @return mixed
     */
    public function getEmails()
    {
        return $this->emails;
    }

    /**
     * @return float
     */
    public function getMinConfidence()
    {
        return $this->minConfidence;
    }

    /**
     * @return mixed
     */
    public function getDurations()
    {
        return $this->durations;
    }

    /**
     * @return mixed
     */
    public function getFuture()
    {
        if ($this->future === null){
            return '2 weeks';
        }
        return $this->future;
    }

    /**
     * @return mixed
     */
    public function getLength()
    {
        if ($this->length === null){
            return '1 hour';
        }
        return $this->length;
    }


}