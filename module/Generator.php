<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace kouosl\gii\module;

use yii\gii\CodeFile;
use yii\helpers\Html;
use Yii;
use yii\helpers\StringHelper;

/**
 * This generator will generate the skeleton code needed by a module.
 *
 * @property string $controllerNamespace The controller namespace of the module. This property is read-only.
 * @property bool $modulePath The directory that contains the module class. This property is read-only.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class Generator extends \yii\gii\Generator
{
    public $moduleClass;
    public $moduleID;


    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'KOUOSL Module Generator';
    }

    /**
     * @inheritdoc
     */
    public function getDescription()
    {
        return 'This generator helps you to generate the skeleton code needed by a KOUOSL PORTAL module.';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['moduleID'], 'filter', 'filter' => 'trim'],
            [['moduleID'], 'required'],
            [['moduleID'], 'match', 'pattern' => '/^[\w\\-]+$/', 'message' => 'Only word characters and dashes are allowed.'],
            [['moduleClass'], 'match', 'pattern' => '/^[\w\\\\]*$/', 'message' => 'Only word characters and backslashes are allowed.'],
            [['moduleClass'], 'validateModuleClass'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'moduleID' => 'Module Name',
            'moduleClass' => 'Module Class',
        ];
    }

    /**
     * @inheritdoc
     */
    public function hints()
    {
        return [
            'moduleID' => 'This refers to the ID of the module, e.g., <code>admin</code>.',
            'moduleClass' => 'This is the fully qualified class name of the module, e.g., <code>app\modules\admin\Module</code>.',
        ];
    }

    /**
     * @inheritdoc
     */
    public function successMessage()
    {
        if (Yii::$app->hasModule($this->moduleID)) {
            $link = Html::a('try it now', Yii::$app->getUrlManager()->createUrl($this->moduleID), ['target' => '_blank']);

            return "The module has been generated successfully. You may $link.";
        }

        $output = <<<EOD
<p>The module has been generated successfully.</p>
<p>To access the module, you need to add this to your application configuration:</p>
EOD;
        $code = <<<EOD
<?php
    ......
    'modules' => [
        '{$this->moduleID}' => [
            'class' => '{'kouosl\\'.$this->moduleID.'\Module'}',
        ],
    ],
    ......
EOD;

        return $output . '<pre>' . highlight_string($code, true) . '</pre>';
    }

    /**
     * @inheritdoc
     */
    public function requiredTemplates()
    {
                return ['module.php', 'controller.php', 'index.php','_index.php','messages.php'];
    }

    /**
     * @inheritdoc
     */
    public function generate()
    {
        $files = [];
        $modulePath = $this->getModulePath();
        $files[] = new CodeFile(
            $modulePath . '/Module.php',
            $this->render("module.php")
        );
        $files[] = new CodeFile(
            $modulePath . '/controllers/api/DefaultController.php',
            $this->render("controller.php",['name' => 'api'])
        );
        $files[] = new CodeFile(
            $modulePath . '/controllers/backend/DefaultController.php',
            $this->render("controller.php",['name' => 'backend'])
        );
        $files[] = new CodeFile(
            $modulePath . '/controllers/frontend/DefaultController.php',
            $this->render("controller.php",['name' => 'frontend'])
        );
        $files[] = new CodeFile(
            $modulePath . '/controllers/console/DefaultController.php',
            $this->render("controller.php",['name' => 'console'])
        );
        $files[] = new CodeFile(
            $modulePath . '/views/backend/default/_index.php',
            $this->render("_index.php")
        );
        $files[] = new CodeFile(
            $modulePath . '/views/frontend/default/_index.php',
            $this->render("_index.php")
        );
        $files[] = new CodeFile(
            $modulePath . '/views/backend/default/index.php',
            $this->render("index.php")
        );
        $files[] = new CodeFile(
            $modulePath . '/views/frontend/default/index.php',
            $this->render("index.php")
        );
        $files[] = new CodeFile(
            $modulePath . '/messages/tr-TR/'. $this->moduleID .'.php',
            $this->render("messages.php",['moduleClass' =>  $this->moduleClass])
        );
        $files[] = new CodeFile(
            $modulePath . '/composer.json',
            $this->render("composer.php")
        );
        return $files;
    }

    /**
     * Validates [[moduleClass]] to make sure it is a fully qualified class name.
     */
    public function validateModuleClass()
    {
        $this->moduleClass = 'vendor\kouosl\\'.$this->moduleID.'\Module';
        if (strpos($this->moduleClass, '\\') === false || Yii::getAlias('@' . str_replace('\\', '/', $this->moduleClass), false) === false) {
            $this->addError('moduleClass', 'Module class must be properly namespaced.');
        }
        if (empty($this->moduleClass) || substr_compare($this->moduleClass, '\\', -1, 1) === 0) {
            $this->addError('moduleClass', 'Module class name must not be empty. Please enter a fully qualified class name. e.g. "app\\modules\\admin\\Module".');
        }
    }

    /**
     * @return bool the directory that contains the module class
     */
    public function getModulePath()
    {
        $this->moduleClass = 'vendor\kouosl\\'.$this->moduleID.'\Module';

        return Yii::getAlias('@' . str_replace('\\', '/', substr($this->moduleClass, 0, strrpos($this->moduleClass, '\\'))));
    }

    /**
     * @return string the controller namespace of the module.
     */
    public function getControllerNamespace()
    {
        $this->moduleClass = 'vendor\kouosl\\'.$this->moduleID.'\Module';

        return substr($this->moduleClass, 0, strrpos($this->moduleClass, '\\')) . '\controllers';
    }
}
