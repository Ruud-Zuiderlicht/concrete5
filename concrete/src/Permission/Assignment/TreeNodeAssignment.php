<?php
namespace Concrete\Core\Permission\Assignment;

use Concrete\Core\Permission\Access\Access;
use PermissionKeyCategory;
use Loader;

class TreeNodeAssignment extends Assignment
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Permission\Assignment\Assignment::getPermissionKeyTaskURL()
     */
    public function getPermissionKeyTaskURL(string $task = '', array $options = []): string
    {
        return parent::getPermissionKeyTaskURL($task, $options + ['treeNodeID' => $this->getPermissionObject()->getTreeNodeID()]);
    }

    public function getPermissionAccessObject()
    {
        $db = Loader::db();
        $r = $db->GetOne('select paID from TreeNodePermissionAssignments where treeNodeID = ? and pkID = ?', array(
            $this->permissionObject->getTreeNodePermissionsNodeID(), $this->pk->getPermissionKeyID(),
        ));

        return Access::getByID($r, $this->pk);
    }

    public function clearPermissionAssignment()
    {
        $db = Loader::db();
        $db->Execute('update TreeNodePermissionAssignments set paID = 0 where pkID = ? and treeNodeID = ?', array($this->pk->getPermissionKeyID(), $this->permissionObject->getTreeNodeID()));
    }

    public function assignPermissionAccess(Access $pa)
    {
        $db = Loader::db();
        $db->Replace('TreeNodePermissionAssignments', array('treeNodeID' => $this->permissionObject->getTreeNodeID(), 'paID' => $pa->getPermissionAccessID(), 'pkID' => $this->pk->getPermissionKeyID()), array('treeNodeID', 'pkID'), true);
        $pa->markAsInUse();
    }
}
