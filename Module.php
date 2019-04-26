<?php

namespace kouosl\gii;

use Yii;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;
use yii\web\HttpException;

class Module extends \yii\base\Module{
    public $controllerNamespace = '';
    public $namespace;

    public function init(){
        parent::init();   
    }
}