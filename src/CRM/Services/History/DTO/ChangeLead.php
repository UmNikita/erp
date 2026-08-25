<?php

namespace App\CRM\Services\History\DTO;

use App\Entity\Lead;

class ChangeLead {

    private ?string $name = null;
    private ?int $client_id = null;
    private ?int $budget = null;
    private ?string $product = null;
    private ?string $source = null;
    private ?string $next_action = null;
    private ?string $date_next_action = null;
    private ?string $comment = null;
    private ?int $responsible_id = null;
    private Lead $oldLead;
    
    public function setDTO(Lead $oldLead, Lead $newLead) {
        $this->oldLead = $oldLead;
        if($oldLead->getName() != $newLead->getName())
            $this->name = $newLead->getName();

        if($oldLead->getClient() != $newLead->getClient())
            $this->client_id = $newLead->getClient()->getId();

        if($oldLead->getBudget() != $newLead->getBudget())
            $this->budget = $newLead->getBudget();

        if($oldLead->getProduct() != $newLead->getProduct())
            $this->product = $newLead->getProduct();

        if($oldLead->getSource() != $newLead->getSource())
            $this->source = $newLead->getSource();

        if($oldLead->getNextAction() != $newLead->getNextAction())
            $this->next_action = $newLead->getNextAction();

        if($oldLead->getDateNextAction() != $newLead->getDateNextAction())
            $this->date_next_action = $newLead->getDateNextAction();

        if($oldLead->getComment() != $newLead->getComment())
            $this->comment = $newLead->getComment();

        
        if($oldLead->getResponsible() != $newLead->getResponsible())
            $this->responsible_id = $newLead->getResponsible()->getId();
    }

    public function toArray(): array
    {
        $obj = [];
        if($this->name) {
            $obj['name'] = [
                'to' => $this->name,
                'from' => $this->oldLead->getName()
            ];
        }
        if($this->client_id) {
            if($this->oldLead->getClient())
                $from = $this->oldLead->getClient()->getId();
            else
                $from = null;
            $obj['client_id'] = [
                'to' => $this->client_id,
                'from' => $from
            ];
        }
        if($this->budget) {
            $obj['budget'] = [
                'to' => $this->budget,
                'from' => $this->oldLead->getBudget()
            ];
        }
        if($this->product) {
            $obj['product'] = [
                'to' => $this->product,
                'from' => $this->oldLead->getProduct()
            ];
        }
        if($this->source) {
            $obj['source'] = [
                'to' => $this->source,
                'from' => $this->oldLead->getSource()
            ];
        }
        if($this->next_action) {
            $obj['next_action'] = [
                'to' => $this->next_action,
                'from' => $this->oldLead->getNextAction()
            ];
        }
        if($this->date_next_action) {
            $obj['date_next_action'] = [
                'to' => $this->date_next_action,
                'from' => $this->oldLead->getDateNextAction()
            ];
        }
        if($this->comment) {
            $obj['comment'] = [
                'to' => $this->comment,
                'from' => $this->oldLead->getComment()
            ];
        }
        if($this->responsible_id) {
            if($this->oldLead->getResponsible())
                $from = $this->oldLead->getResponsible()->getId();
            else
                $from = null;
            $obj['responsible_id'] = [
                'to' => $this->responsible_id,
                'from' => $from
            ];
        }
        return $obj;
    }

}