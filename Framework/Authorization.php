<?php

namespace Framework;

use Framework\Session;

class Authorization {
    /**
     * Check if current logged in user owns a resource
     *
     * @param int $resourceId
     * @return bool
     */
    public static function isOwner($resourceId) {
        $sessionUser = Session::get('user');

        if ($sessionUser !== null && isset($sessionUser['id'])) {
            $sessionUserID = (int) $sessionUser['id'];
            return $sessionUserID === $resourceId;
        }

        return false;
    }
}
