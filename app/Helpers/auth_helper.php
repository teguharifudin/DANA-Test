<?php

function is_role($roles)
{
    if (!is_array($roles)) {
        $roles = [$roles];
    }
    return in_array(session()->get('role'), $roles);
}

function is_division($divisions)
{
    if (!is_array($divisions)) {
        $divisions = [$divisions];
    }
    return in_array(session()->get('division'), $divisions);
}