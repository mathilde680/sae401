<?php

namespace App\Event;

use App\Entity\Groupe;

class GroupeEvent
{
    const ADDED = 'groupe.added';


    private Groupe $groupe;

    public function __construct(Groupe $groupe)
    {
        $this->groupe = $groupe;
    }

    public function getGroupe(): Groupe
    {
        return $this->groupe;
    }
}