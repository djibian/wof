<?php

namespace Wof\WPModels;

class User
{

    /**
     * check if a user has a role
     * @param \WP_User $user
     * @param  string $role
     * @return boolean
     */
    static public function hasRole($user, $role)
    {
        if(in_array($role, $user->roles)) {
            return true;
        }
        else {
            return false;
        }
    }

    /**
     * check if user is connected
     * @return boolean
     */
    static public function isConnected()
    {
        $user = static::getCurrent();
        if($user->ID) {
            return true;
        }
        else {
            return false;
        }
    }

    /**
     * get current user
     * @return \WP_User
     */
    static public function getCurrent()
    {
        return wp_get_current_user();
    }

    /**
     * Rerieve users by id list
     *
     * @param array $userIds
     * @return \Wp_User[]
     */
    static public function getByIds(array $userIds)
    {
        return get_users([
            'include' => $userIds
        ]);
    }

    // DOC récupération liste  de users https://developer.wordpress.org/reference/functions/get_users/
    /**
     * Rerieve users by role
     *
     * @param string|array $role
     * @param string $orderBy
     * @param string $order
     * @return \Wp_User[]
     */
    static public function getByRole($role, $orderBy = 'user_nicename', $order = 'ASC')
    {
        if(is_string($role)) {
            $args = array(
                'role'    => $role,
                'orderby' => $orderBy,
                'order'   => $order
            );
        }
        elseif(is_array($role)) {
            $args = array(
                'role__in'    => $role,
                'orderby' => $orderBy,
                'order'   => $order
            );
        }
        else {
            throw new \Exception('$role parameter must be a string or an array. Passed value : ' . print_r($role, true));
        }
        return get_users( $args );
    }
}
