<?php
namespace Kendo\Acl;

interface MultiRoleInterface 
{
    public function getRoles();
    public function hasRole($role);
}

