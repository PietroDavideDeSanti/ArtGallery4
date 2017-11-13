<?php

namespace CoreBundle\Libraries;

class K2ICSmailer {
    
    private $firstname;
    private $lastname;
    private $to;
    private $from;
    private $sender;
    private $location;
    private $subject;
    private $start;
    private $end;
    private $body;
    private $sequence = 0;
    private $uid;
    private $method;
        
    public function getFirstame() {
        return $this->firstname;
    }
    public function getLastame() {
        return $this->lastname;
    }
    public function getTo() {
        return $this->to;
    }
    public function getFrom() {
        return $this->from;
    }
    public function getSender() {
        return $this->sender;
    }
    public function getLocation() {
        return $this->location;
    }
    public function getSubject() {
        return $this->subject;
    }
    public function getStart() {
        return $this->start;
    }
    public function getEnd() {
        return $this->end;
    }
    public function getBody() {
        return $this->body;
    }
    public function getSequence() {
        return $this->sequence;
    }
    public function getUID() {
        if(empty($this->uid)) {
            $this->setUID();
        }
        return $this->uid;
    }
    public function setFirstame($firstname) {
        $this->firstname = $firstname;
    }
    public function setLastame($lastname) {
        $this->lastname = $lastname;
    }
    public function setTo(array $to) {
        $this->to = $to;
    }
    public function setFrom($from) {
        $this->from = $from;
    }
    public function setSender($sender) {
        $this->sender = $sender;
    }
    public function setLocation($location) {
        $this->location = $location;
    }
    public function setSubject($subject) {
        $this->subject = $subject;
    }
    public function setStart(\DateTime $start) {
        $this->start = $start;
    }
    public function setEnd(\DateTime $end) {
        $this->end = $end;
    }
    public function setBody($body) {
        $this->body = $body;
    }
    public function setSequence($sequence) {
        $this->sequence = (int)$sequence;
    }
    public function setUID($uid=null) {
        $this->uid = $uid ? $uid : $this->formatDateTime(new \DateTime()).rand()."@key2.it";
    }

    public function createMeeting($sender, $firstname, $lastname, $meeting_name, $meeting_start, $meeting_end, $meeting_location) {
        $format = <<<TPL
<p>Gentile %s</p>
<p>
La invitiamo a partecipare al seguente appuntamento "%s"
<br>
che si svolgerà in "%s" il %s alle ore %s     
<br>
<br>
Cordiali saluti
<br>
%s                
<br>
</p>
   
TPL;
        if(empty($this->body)) {
            $boyd_params = [
              trim($firstname." ".$lastname),
              $meeting_name,
              $meeting_location,
              $meeting_start->format("d/m/Y"),
              $meeting_start->format("H:i"),
              $sender
            ];
            $this->setBody(vsprintf($format, $boyd_params));
        }
        $this->setUID();
        $this->setSequence(0);
        $this->method = 'REQUEST';
        $this->setSender($sender);
        $this->setFirstame($firstname);
        $this->setLastame($lastname);
        $this->setSubject($meeting_name);
        $this->setStart($meeting_start);
        $this->setEnd($meeting_end);
        $this->setLocation($meeting_location);
        return $this;
    }

    public function updateMeeting($sender, $firstname, $lastname, $meeting_name, $meeting_start, $meeting_end, $meeting_location, $uid, $sequence) {
        $format = <<<TPL
<p>Gentile %s</p>
<p>
Il seguente appuntamento "%s" è stato aggiornato
<br>
si svolgerà in "%s" il %s alle ore %s     
<br>
<br>
Cordiali saluti
<br>
%s                
<br>
</p>
   
TPL;
        if(empty($this->body)) {
            $boyd_params = [
              trim($firstname." ".$lastname),
              $meeting_name,
              $meeting_location,
              $meeting_start->format("d/m/Y"),
              $meeting_start->format("H:i"),
              $sender
            ];
            $this->setBody(vsprintf($format, $boyd_params));
        }
        $this->setUID($uid);
        $this->setSequence($sequence);
        $this->method = 'UPDATE';
        $this->setSender($sender);
        $this->setFirstame($firstname);
        $this->setLastame($lastname);
        $this->setSubject($meeting_name);
        $this->setStart($meeting_start);
        $this->setEnd($meeting_end);
        $this->setLocation($meeting_location);
        return $this;
    }
      
    public function cancelMeeting($sender, $firstname, $lastname, $meeting_name, $meeting_start, $meeting_end, $meeting_location, $uid) {
        $format = <<<TPL
<p>Gentile %s</p>
<p>
L'appuntamento "%s" è stato cancellato
<br>
si sarebbe dovuto svolgere in "%s" il %s alle ore %s     
<br>
<br>
Cordiali saluti
<br>
%s                
<br>
</p>
   
TPL;
        if(empty($this->body)) {
            $boyd_params = [
              trim($firstname." ".$lastname),
              $meeting_name,
              $meeting_location,
              $meeting_start->format("d/m/Y"),
              $meeting_start->format("H:i"),
              $sender
            ];
            $this->setBody(vsprintf($format, $boyd_params));
        }
        $this->setUID($uid);
        $this->setSequence(0);
        $this->method = 'CANCEL';
        $this->setSender($sender);
        $this->setFirstame($firstname);
        $this->setLastame($lastname);
        $this->setSubject($meeting_name);
        $this->setStart($meeting_start);
        $this->setEnd($meeting_end);
        $this->setLocation($meeting_location);
        return $this;
    }
        
    public function sendMeeting(array $to) {
        $messageObject = \Swift_Message::newInstance();
        $messageObject->setContentType("multipart/alternative");
        $messageObject->addPart($this->body, "text/html");
        $messageObject->setSubject($this->subject)->setFrom($this->from, $this->sender);
        $messageObject->setTo($to);
        $ics_content = $this->generateICS();
        $ics_attachment = \Swift_Attachment::newInstance()->setBody(trim($ics_content))->setEncoder(\Swift_Encoding::get7BitEncoding());
        $headers = $ics_attachment->getHeaders();
        $content_type_header = $headers->get("Content-Type");
        $content_type_header->setValue("text/calendar");
        $content_type_header->setParameters([
          'charset' => 'UTF-8',
          'method' => 'REQUEST'
        ]);
        $headers->remove('Content-Disposition');
        $messageObject->attach($ics_attachment);
        $transport = \Swift_SmtpTransport::newInstance();
        $mailObject = \Swift_Mailer::newInstance($transport);
        $ICSsettings = new \stdClass();
        $ICSsettings->send = $mailObject->send($messageObject);
        $ICSsettings->sequence = $this->getSequence();
        $ICSsettings->uid = $this->getUid();
        return $ICSsettings;
    }
    
    private function generateICS() {
        $start = $this->formatDateTime($this->getStart());
        $end = $this->formatDateTime($this->getEnd());
        $from = $this->getFrom();
        $sender = $this->getSender();
        $location = $this->getLocation();
        $subject = $this->getSubject();
        $uid = $this->getUID();
        $sequence = $this->getSequence();
        $method = $this->method;
        $stamp = "\nDTSTAMP:".$this->formatDateTime(new \DateTime());
        $status = 'CONFIRMED';
        switch($method) {
            case 'REQUEST':
                break;
            case 'UPDATE':
                $method = 'REQUEST';
                $sequence++;
                $this->setSequence($sequence);
                break;
            case 'CANCEL':
                $status = 'CANCELLED';
                $stamp = "";
                break;
        }
        
        $format = <<<ICS
BEGIN:VCALENDAR
PRODID:-//%s//%s//EN
VERSION:2.0
CALSCALE:GREGORIAN
METHOD:REQUEST
BEGIN:VEVENT
DTSTART:%s
DTEND:%s%s
ORGANIZER;CN=%s:MAILTO:%s
UID:%s
CREATED:%s
LOCATION:%s
SEQUENCE:0
STATUS:CONFIRMED
SUMMARY:%s
TRANSP:OPAQUE
END:VEVENT
END:VCALENDAR            
     
ICS;
        $params = [$from, $from, $start, $end, $stamp, $sender, $from, $uid, $stamp, $location, $subject];
        return vsprintf($format, $params);
    }
    
    private function formatDateTime(\DateTime $dt) {
        $dt->setTimeZone(new \DateTimeZone('Europe/Rome'));
        return $dt->format("Ymd\THis");
    }
}

