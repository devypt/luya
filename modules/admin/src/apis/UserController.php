<?php

namespace luya\admin\apis;

use luya\admin\ngrest\base\Api;

/**
 * User API, provides ability to manager and list all administration users.
 *
 * @author Basil Suter <basil@nadar.io>
 */
class UserController extends Api
{
    /**
     * @var string Path to the user model class.
     */
    public $modelClass = 'luya\admin\models\User';
}
